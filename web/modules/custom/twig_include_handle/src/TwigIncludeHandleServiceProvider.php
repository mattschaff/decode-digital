<?php

namespace Drupal\twig_include_handle;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Symfony\Component\DependencyInjection\Reference;

class TwigIncludeHandleServiceProvider extends ServiceProviderBase implements ServiceProviderInterface {

  /**
   * @inheritDoc
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('twig');
    $definition->setClass('Drupal\twig_include_handle\TwigEnvironmentIncludeHandle');
    $definition->addArgument(new Reference('theme.manager'));
    $definition->addArgument(new Reference('theme_handler'));
  }
}