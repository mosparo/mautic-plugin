<?php

namespace MauticPlugin\MosparoIntegrationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class MosparoIntegrationExtension implements ExtensionInterface, PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('twig')) {
            $container->prependExtensionConfig('twig', ['form_themes' => ['@MosparoIntegration/Form/mosparohelper.html.twig']]);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Do nothing
    }

    public function getNamespace()
    {
        return 'MauticPlugin\\MosparoIntegrationBundle';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getXsdValidationBasePath()
    {
        // Do nothing
    }

    public function getAlias()
    {
        return 'mosparo_integration';
    }
}