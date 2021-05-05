<?php

namespace Drupal\distributed_preprocess\Base;

abstract class PreprocessorBaseBlockContent extends PreprocessorBase implements BlockContentPreprocessorInterface {
  const ELEMENT_NAME = 'block_content';
}