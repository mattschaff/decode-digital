<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\block_content\Entity\BlockContent;
use Drupal\distributed_preprocess\Base\PreprocessorBaseBlockContent;

class FooterPreprocessor extends PreprocessorBaseBlockContent {

  /**
   * @inheritDoc
   */
  public function preprocessBlockContent(array &$variables): void {
    /** @var BlockContent $block */
    $block = $variables['elements']['#block_content'];
  }

  /**
   * @inheritDoc
   */
  public function blockContentBundle(): string {
    return 'footer';
  }
}