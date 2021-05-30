<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\distributed_preprocess\Base\PreprocessorBaseParagraph;

class ContactFormPreprocessor extends PreprocessorBaseParagraph {

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
    return 'contact_form';
  }
}