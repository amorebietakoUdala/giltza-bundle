<?php

namespace AMREU\GiltzaBundle\DependencyInjection;

use AMREU\GiltzaBundle\Controller\GiltzaController;
use AMREU\GiltzaBundle\Service\GiltzaProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

class GiltzaExtension extends Extension
{
   public function load(array $configs, ContainerBuilder $container): void
   {
      $configuration = new Configuration();
      $config = $this->processConfiguration($configuration, $configs);

      $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
      $loader->load('services.yaml');

      $definition = $container->getDefinition(GiltzaProvider::class);
      $definition->setArgument('$options', $config['provider']);

      $definition2 = $container->getDefinition(GiltzaController::class);
      $definition2->setArgument('$options', $config['controller']);
   }
}
