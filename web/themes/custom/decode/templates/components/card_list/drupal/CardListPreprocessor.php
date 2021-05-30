<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\distributed_preprocess\Base\PreprocessorBaseParagraph;
use Drupal\paragraphs\Entity\Paragraph;

class CardListPreprocessor extends PreprocessorBaseParagraph {

  /**
   * @inheritDoc
   */
  public function preprocessParagraph(array &$variables): void {
    $cards = [];
    $variables['cards'] = &$cards;
    /** @var Paragraph $list_object */
    $list_object = $variables['paragraph'];
    /** @var EntityReferenceFieldItemList $list_field */
    $list_field = $list_object->get('field_cards');
    foreach ($list_field->referencedEntities() as $card_object) {
      $cards[] = [
        'image' => $image,
        'text' => $text,
        'cta' => $cta,
        'title' => $title,
      ] = $this->getPreprocessedRenderArrayForEntity('paragraph', $card_object)
        + array_fill_keys(['image', 'text', 'cta', 'title'], null);
    }
  }

  /**
   * @inheritDoc
   */
  public function paragraphBundle(): string {
    return 'card_list';
  }
}