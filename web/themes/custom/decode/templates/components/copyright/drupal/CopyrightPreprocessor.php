<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\distributed_preprocess\Base\PreprocessorBaseBlockContent;

class CopyrightPreprocessor extends PreprocessorBaseBlockContent {

  /**
   * @inheritDoc
   */
  public function preprocessBlockContent(array &$variables): void {
    /** @todo extract Drupal variables */
    $variables['injected'] = 'Injected value';
  }

  /**
   * @inheritDoc
   */
  public function blockContentBundle(): string {
    return 'copyright';
  }
}