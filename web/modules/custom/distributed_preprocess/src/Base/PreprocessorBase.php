<?php

namespace Drupal\distributed_preprocess\Base;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\distributed_preprocess\Service\DistributedPreprocess;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class PreprocessorBase implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * Distributed preprocess service
   *
   * @var DistributedPreprocess
   */
  protected $service;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static();
  }

  /**
   * Set base services
   *
   * @param DistributedPreprocess $service
   * @return PreprocessorBase
   */
  public function setBaseServices(DistributedPreprocess $service): PreprocessorBase {
    $this->service = $service;
    return $this;
  }

  /**
   * Get render array for entity, already preprocessed
   *
   * @param string $entity_type
   * @param EntityInterface $entity
   * @return array
   */
  public function getPreprocessedRenderArrayForEntity(string $entity_type, EntityInterface $entity): array {
    return $this->service->getPreprocessedRenderArrayForEntity($entity_type, $entity);
  }

}