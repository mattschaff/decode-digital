<?php

namespace Drupal\distributed_preprocess\Service;

use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Theme\ActiveTheme;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\distributed_preprocess\Base\ParagraphPreprocessorInterface;
use Drupal\distributed_preprocess\Base\PreprocessorBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DistributedPreprocess {

  /**
   * Service container
   *
   * @var ContainerInterface
   */
  protected $container;

  /**
   * Theme handler
   *
   * @var ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * Theme manager
   *
   * @var ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * Whether the active theme calls for distributed preprocessors
   *
   * @var boolean
   */
  protected $enabled;

  /**
   * Active theme
   *
   * @var ActiveTheme
   */
  protected $activeTheme;

  /**
   * Namespace of theme preprocessor class
   *
   * @var string
   */
  protected $namespace;

  /**
   * Directory of prepreprocessors in theme
   *
   * @var string
   */
  protected $directory;

  /**
   * Constructs DistributedPreprocess
   *
   * @param ContainerInterface $Container
   * @param ThemeManagerInterface $ThemeManager
   * @param ThemeHandlerInterface $ThemeHandler
   */
  public function __construct(ContainerInterface $Container, ThemeManagerInterface $ThemeManager, ThemeHandlerInterface $ThemeHandler){
    $this->container = $Container;
    $this->themeManager = $ThemeManager;
    $this->themeHandler = $ThemeHandler;
  }

  /**
   * Preprocess element
   *
   * - Called in hook_preprocess()
   */
  public function preprocess(array &$variables, $hook): void {
    if (!$this->isEnabled()) {
      return;
    }
    $container = $this->getThemePreprocessorContainer();
    if (!isset($container[$hook])) {
      return;
    }
    switch ($hook) {
      case 'paragraph':
        /** @var Paragraph $paragraph */
        $paragraph = $variables['paragraph'];
        /** @var ParagraphPreprocessorInterface $handler */
        foreach ($container[$hook] as $handler) {
          if ($paragraph->getType() === $handler->paragraphBundle()) {
            $handler->preprocessParagraph($variables);
          }
        }
      break;
    }
  }

  /**
   * Returns whether the active theme calls for distributed preprocessors
   *
   * @return boolean
   */
  protected function isEnabled(): bool {
    if (isset($this->enabled)) {
      return $this->enabled;
    }
    $this->enabled = FALSE;
    $active_theme = $this->themeManager->getActiveTheme();
    $info = $this->themeHandler->listInfo()[$active_theme->getName()]->info;
    if (isset($info['preprocessor_directory']) && isset($info['preprocessor_namespace'])) {
      $this->enabled = TRUE;
      $this->namespace = $info['preprocessor_namespace'];
      $this->directory = $info['preprocessor_directory'];
      $this->activeTheme = $active_theme;
    }
    return $this->enabled;
  }

  /**
   * Get PHP file paths in directory, recursively
   *
   * @param string $dir
   * @param string[] $results
   * @return array
   */
  protected function getRecursivePhpFiles($dir, &$results = []) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
      $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
      $path_parts = pathinfo($path);
      if (!is_dir($path) && $path_parts['extension'] === 'php') {
        $results[] = $path;
      } else if (is_dir($path) && $value != "." && $value != "..") {
        $this->getRecursivePhpFiles($path, $results);
      }
    }
    return $results;
  }

  /**
   * Load PHP files in directory
   *
   * @param string $dir
   * @return array
   */
  protected function loadPhpFilesInDirectory(string $dir) {
    $src_path = drupal_get_path('theme', $this->activeTheme->getName()) . DIRECTORY_SEPARATOR . $dir;
    $src_files = $this->getRecursivePhpFiles($src_path);
    foreach ($src_files as $file) {
      require_once $file;
    }
    return $src_files;
  }

  /**
   * Autoload the theme processor container
   *
   * @return array
   */
  protected function getThemePreprocessorContainer(): array {
    $container = &drupal_static(__METHOD__);
    if (is_null($container)) {
      $component_files = $this->loadPhpFilesInDirectory($this->directory);
      $container = [];
      foreach ($component_files as $file) {
        $name = basename($file, '.php');
        /** @var PreprocessorBase $class */
        $class = call_user_func("{$this->namespace}\\$name::create", $this->container);
        $container[$class::ELEMENT_NAME][] = $class;
      }
    }
    return $container;
  }

}