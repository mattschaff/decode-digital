<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\distributed_preprocess\Base\PreprocessorBaseParagraph;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

class CaseStudyListPreprocessor extends PreprocessorBaseParagraph {

  /**
   * @inheritDoc
   */
  public function preprocessParagraph(array &$variables): void {
    $studies = [];
    $variables['case_studies'] = &$studies;
    /** @var Paragraph $list_object */
    $list_object = $variables['paragraph'];
    /** @var EntityReferenceFieldItemList $list_field */
    $list_field = $list_object->get('field_case_studies');
    /** @var Node $node */
    foreach ($list_field->referencedEntities() as $node) {
      $study = [
        'title' => $node->getTitle(),
        'text' => $node->get('field_summary')->value,
        'link' => [
          'text' => $this->t('Read More'),
          'url' => $node->toUrl(),
        ],
      ];
      /** @var Media $media */
      if ($media = $node->get('field_card_image')->entity) {
        /** @var File $file */
        $file = $media->get('field_media_image')->entity;
        $uri = $file->getFileUri();
        $study['image'] = [
          'src_2x' => file_create_url($uri),
          'src_1x' => ImageStyle::load('50_percent')->buildUrl($uri),
        ];
      };
      $studies[] = $study;
    }
  }

  /**
   * @inheritDoc
   */
  public function paragraphBundle(): string {
    return 'case_study_list';
  }
}