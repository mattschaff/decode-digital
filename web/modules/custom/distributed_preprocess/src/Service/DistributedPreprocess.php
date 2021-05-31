<?php

namespace Drupal\distributed_preprocess\Service;

use Drupal\block_content\Entity\BlockContent;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Theme\ActiveTheme;
use Drupal\Core\Theme\Registry;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Utility\ThemeRegistry;
use Drupal\distributed_preprocess\Base\BlockContentPreprocessorInterface;
use Drupal\distributed_preprocess\Base\ParagraphPreprocessorInterface;
use Drupal\distributed_preprocess\Base\PreprocessorBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DistributedPreprocess {

  /**
   * Service container
   *
   * @var ContainerInterface
   */
  protected $serviceContainer;

  /**
   * Preprocessor container
   *
   * @var array
   */
  protected $preprocessorContainer;

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
   * Entity type manager
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Theme registry
   *
   * @var Registry
   */
  protected $themeRegistry;

  /**
   * Constructs DistributedPreprocess
   *
   * @param ContainerInterface $Container
   * @param ThemeManagerInterface $ThemeManager
   * @param ThemeHandlerInterface $ThemeHandler
   * @param EntityTypeManagerInterface $EntityTypeManager
   * @param Registry $ThemeRegistry
   */
  public function __construct(ContainerInterface $Container, ThemeManagerInterface $ThemeManager, ThemeHandlerInterface $ThemeHandler, EntityTypeManagerInterface $EntityTypeManager, Registry $ThemeRegistry){
    $this->serviceContainer = $Container;
    $this->themeManager = $ThemeManager;
    $this->themeHandler = $ThemeHandler;
    $this->entityTypeManager = $EntityTypeManager;
    $this->themeRegistry = $ThemeRegistry;
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
      case 'block_content':
        /** @var BlockContent $block_content */
        $block = $variables['elements']['#block_content'];
        /** @var BlockContentPreprocessorInterface $handler */
        foreach ($container[$hook] as $handler) {
          if ($block->bundle() === $handler->blockContentBundle()) {
            $handler->preprocessBlockContent($variables);
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
    $dir = rtrim($dir, DIRECTORY_SEPARATOR); # Ensure no ending slash
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
    if (!isset($this->preprocessorContainer)) {
      $component_files = $this->loadPhpFilesInDirectory($this->directory);
      $container = [];
      $namespace = rtrim($this->namespace, '\\'); # Ensure no ending slash
      foreach ($component_files as $file) {
        $name = basename($file, '.php');
        /** @var PreprocessorBase $class */
        $class = call_user_func("$namespace\\$name::create", $this->serviceContainer);
        if ($class instanceof PreprocessorBase) {
          $class->setBaseServices($this, $this->entityTypeManager);
          $container[$class::ELEMENT_NAME][] = $class;
        }
      }
      $this->preprocessorContainer = $container;
    }
    return $this->preprocessorContainer;
  }

  /**
   * Get render array for entity, already preprocessed
   *
   * @param string $entity_type
   * @param EntityInterface $entity
   * @return array
   */
  public function getPreprocessedRenderArrayForEntity(string $entity_type, EntityInterface $entity): array {
    $info = $this->themeRegistry->getRuntime()->get($entity_type);
    $variables = $this->entityTypeManager->getViewBuilder('paragraph')->view($entity);
    // If a renderable array is passed as $variables, then set $variables to
    // the arguments expected by the theme function.
    if (isset($variables['#theme']) || isset($variables['#theme_wrappers'])) {
      $element = $variables;
      $variables = [];
      if (isset($info['variables'])) {
        foreach (array_keys($info['variables']) as $name) {
          if (isset($element["#$name"]) || array_key_exists("#$name", $element)) {
            $variables[$name] = $element["#$name"];
          }
        }
      }
      else {
        $variables[$info['render element']] = $element;
        // Give a hint to render engines to prevent infinite recursion.
        $variables[$info['render element']]['#render_children'] = TRUE;
      }
    }

    // Merge in argument defaults.
    if (!empty($info['variables'])) {
      $variables += $info['variables'];
    }
    elseif (!empty($info['render element'])) {
      $variables += [$info['render element'] => []];
    }
    foreach ($info['preprocess functions'] as $fxn) {
      $fxn($variables, $entity_type, $info);
    }
    return $variables;
  }

}