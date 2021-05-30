<?php

namespace Drupal\twig_include_handle;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Template\TwigEnvironment;
use Drupal\Core\Theme\ThemeManagerInterface;
use Twig\Loader\LoaderInterface;

class TwigEnvironmentIncludeHandle extends TwigEnvironment{

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
   * Is include handle enabled
   *
   * @var bool
   */
  protected $includeHandleEnabled;

  /**
   * Map of component handles to paths
   *
   * @var array
   */
  protected $componentMap;

  /**
   * Active theme path
   *
   * @var string
   */
  protected $activeThemePath;

  /**
   * Constructs TwigEnvironmentIncludeHandle
   *
   * @param string $root
   * @param CacheBackendInterface $cache
   * @param string $twig_extension_hash
   * @param StateInterface $state
   * @param LoaderInterface $loader
   * @param array $options
   * @param ThemeManagerInterface $ThemeManager
   * @param ThemeHandlerInterface $ThemeHandler
   */
  public function __construct($root, CacheBackendInterface $cache, $twig_extension_hash, StateInterface $state, LoaderInterface $loader = NULL, array $options = [], ThemeManagerInterface $ThemeManager, ThemeHandlerInterface $ThemeHandler) {
    parent::__construct($root, $cache, $twig_extension_hash, $state, $loader, $options);
    $this->themeHandler = $ThemeHandler;
    $this->themeManager = $ThemeManager;
  }

  /**
   * @inheritDoc
   */
  public function getTemplateClass($name, $index = NULL) {
    $this->transformName($name);
    // We override this method to add caching because it gets called multiple
    // times when the same template is used more than once. For example, a page
    // rendering 50 nodes without any node template overrides will use the same
    // node.html.twig for the output of each node and the same compiled class.
    $cache_index = $name . (NULL === $index ? '' : '_' . $index);
    if (!isset($this->templateClasses[$cache_index])) {
      $this->templateClasses[$cache_index] = parent::getTemplateClass($name, $index);
    }
    return $this->templateClasses[$cache_index];
  }

  /**
   * @inheritDoc
   */
  public function loadTemplate($name, $index = null) {
    $this->transformName($name);
    return $this->loadClass($this->getTemplateClass($name), $name, $index);
  }

  /**
   * Transform name by component map, if the key exists
   *
   * @param string $name
   * @return string
   */
  protected function transformName(string &$name): string {
    if ($this->isIncludeHandleEnabled()) {
      $name = (strpos($name, '@') === 0 && isset($this->componentMap[$name]))
        ? $this->activeThemePath . DIRECTORY_SEPARATOR . $this->componentMap[$name]
        : $name;
    }
    return $name;
  }

  /**
   * Is the include handle functionality enabled
   *
   * - Requires component_map.json file placed at root of active theme.
   *
   * @return boolean
   */
  protected function isIncludeHandleEnabled() {
    if (isset($this->includeHandleEnabled)) {
      return $this->includeHandleEnabled;
    }
    $this->includeHandleEnabled = FALSE;
    $path = DRUPAL_ROOT
      . DIRECTORY_SEPARATOR
      . $this->themeManager->getActiveTheme()->getPath()
      . DIRECTORY_SEPARATOR
      . 'component_map.json';
    if (file_exists($path)) {
      $this->activeThemePath = $this->themeManager->getActiveTheme()->getPath();
      $this->componentMap = Json::decode(file_get_contents($path));
      $this->includeHandleEnabled = TRUE;
    }
    return $this->includeHandleEnabled;
  }


}