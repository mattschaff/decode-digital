<?php

namespace Drupal\decode\theme\preprocessors;

use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\distributed_preprocess\Base\PreprocessorBaseParagraph;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

class BlogListPreprocessor extends PreprocessorBaseParagraph {

  /**
   * @inheritDoc
   */
  public function preprocessParagraph(array &$variables): void {
    $blog_posts = [];
    $variables['blog_list'] = &$blog_posts;
    /** @var Paragraph $paragraph */
    $paragraph = $variables['paragraph'];
    /** @var EntityReferenceFieldItemList $list_field */
    $list_field = $paragraph->get('field_blog_posts');
     /** @var Node[] $nodes */
    $nodes = [];
    // Automatically display latest 5 blog posts, unless overridded by manual
    // field.
    if ($list_field->isEmpty()) {
      $nids = $this->entityTypeManager
        ->getStorage('node')
        ->getQuery()
        ->accessCheck(FALSE)
        ->condition('type', 'blog')
        ->condition('status', 1)
        ->sort('created', 'DESC')
        ->range(0, 5)
        ->execute();
      $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    }
    else {
      $nodes = $list_field->referencedEntities();
    }
    foreach ($nodes as $node) {
      $post = [
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
        $post['image'] = [
          'src_2x' => file_create_url($uri),
          'src_1x' => ImageStyle::load('50_percent')->buildUrl($uri),
        ];
      };
      $blog_posts[] = $post;
    }
  }

  /**
   * @inheritDoc
   */
  public function paragraphBundle(): string {
    return 'case_study_list';
  }
}