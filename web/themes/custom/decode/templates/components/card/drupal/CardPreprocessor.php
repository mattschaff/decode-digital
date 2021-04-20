<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\distributed_preprocess\Base\PreprocessorBaseParagraph;

class CardPreprocessor extends PreprocessorBaseParagraph {

  /**
   * @inheritDoc
   */
  public function preprocessParagraph(array &$variables): void {
    /** @todo extract Drupal variables */
  }

  /**
   * @inheritDoc
   */
  public function paragraphBundle(): string {
    return 'card';
  }
}