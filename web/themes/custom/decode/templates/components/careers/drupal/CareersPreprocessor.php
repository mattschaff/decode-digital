<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\distributed_preprocess\Base\PreprocessorBaseParagraph;

class CareersPreprocessor extends PreprocessorBaseParagraph {

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
    return 'careers';
  }
}