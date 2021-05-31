<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\block_content\Entity\BlockContent;
use Drupal\distributed_preprocess\Base\PreprocessorBaseBlockContent;

class CopyrightPreprocessor extends PreprocessorBaseBlockContent {

  /**
   * @inheritDoc
   */
  public function preprocessBlockContent(array &$variables): void {
    /** @var BlockContent $block */
    $block = $variables['elements']['#block_content'];
    $variables['text'] = $block->get('field_copyright_text')->value;
  }

  /**
   * @inheritDoc
   */
  public function blockContentBundle(): string {
    return 'copyright';
  }
}