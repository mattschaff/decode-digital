<?php

namespace Drupal\distributed_preprocess\Base;

interface BlockContentPreprocessorInterface {

  /**
   * Preprocess block content render array
   *
   * @param array  $variables
   *
   * @return void
   */
  public function preprocessBlockContent(array &$variables): void;

  /**
   * Get bundle of block content the preprocessor targets
   *
   * @return string
   */
  public function blockContentBundle(): string;
}