<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\distributed_preprocess\Base\PreprocessorBaseParagraph;

class LiquidDropPreprocessor extends PreprocessorBaseParagraph {

  /**
   * @inheritDoc
   */
  public function preprocessParagraph(array &$variables): void {
    // @todo - Add logic when field structure is finalized.
  }

  /**
   * @inheritDoc
   */
  public function paragraphBundle(): string {
    return 'liquid_drop';
  }
}