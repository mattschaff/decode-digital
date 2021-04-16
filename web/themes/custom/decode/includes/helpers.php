<?php

use Drupal\decode\theme\PreprocessorBase;

/**
 * Get PHP file paths in directory, recursively
 *
 * @param string $dir
 * @param string[] $results
 * @return array
 */
function get_recursive_php_files($dir, &$results = []) {
  $files = scandir($dir);
  foreach ($files as $key => $value) {
    $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
    $path_parts = pathinfo($path);
    if (!is_dir($path) && $path_parts['extension'] === 'php') {
      $results[] = $path;
    } else if (is_dir($path) && $value != "." && $value != "..") {
      get_recursive_php_files($path, $results);
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
function load_php_files_in_directory(string $dir) {
  $src_path = drupal_get_path('theme', 'decode') . DIRECTORY_SEPARATOR . $dir;
  $src_files = get_recursive_php_files($src_path);
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
function get_theme_preprocessor_container(): array {
  $container = &drupal_static(__FUNCTION__);
  if (is_null($container)) {
    load_php_files_in_directory('src');
    $component_files = load_php_files_in_directory('templates/components');
    $container = [];
    foreach ($component_files as $file) {
      $name = basename($file, '.php');
      /** @var PreprocessorBase $class */
      $class = call_user_func("Drupal\\decode\\theme\\preprocessors\\$name::create", \Drupal::getContainer());
      $container[$class::ELEMENT_NAME][] = $class;
    }
  }
  return $container;
}