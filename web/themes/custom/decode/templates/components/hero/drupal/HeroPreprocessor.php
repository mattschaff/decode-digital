<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\distributed_preprocess\Base\PreprocessorBaseParagraph;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\Entity\Media;
use Drupal\paragraphs\Entity\Paragraph;

class HeroPreprocessor extends PreprocessorBaseParagraph {

  /**
   * @inheritDoc
   */
  public function preprocessParagraph(array &$variables): void {
    /** @var Paragraph $paragraph */
    $paragraph = $variables['paragraph'];
    /** @var Media $media */
    if ($media = $paragraph->get('field_image')->entity) {
      /** @var File $file */
      $file = $media->get('field_media_image')->entity;
      $uri = $file->getFileUri();
      $variables['image'] = [
        'src_2x' => file_create_url($uri),
        'src_1x' => ImageStyle::load('50_percent')->buildUrl($uri),
      ];
    };
    if (!$paragraph->get('field_link')->isEmpty()) {
      $variables['cta'] = [
        'title' => $paragraph->get('field_link')->title,
        'url' => Url::fromUri($paragraph->get('field_link')->uri),
      ];
    }
    $variables['title'] = $paragraph->get('field_title')->value;
  }

  /**
   * @inheritDoc
   */
  public function paragraphBundle(): string {
    return 'card';
  }
}