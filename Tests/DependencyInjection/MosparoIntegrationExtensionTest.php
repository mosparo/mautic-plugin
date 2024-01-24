<?php

namespace DependencyInjection;

use MauticPlugin\MosparoIntegrationBundle\DependencyInjection\MosparoIntegrationExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class MosparoIntegrationExtensionTest extends TestCase
{
    public function testPrepend()
    {
        $builder = $this->createMock(ContainerBuilder::class);
        $builder
            ->expects($this->exactly(1))
            ->method('hasExtension')
            ->with('twig')
            ->willReturn(true);

        $builder
            ->expects($this->exactly(1))
            ->method('prependExtensionConfig')
            ->with('twig', ['form_themes' => ['@MosparoIntegration/Form/mosparohelper.html.twig']]);

        $extension = new MosparoIntegrationExtension();
        $extension->prepend($builder);
    }

    public function testGetNamespace()
    {
        $extension = new MosparoIntegrationExtension();

        $this->assertEquals('MauticPlugin\\MosparoIntegrationBundle', $extension->getNamespace());
        $this->assertEquals('mosparo_integration', $extension->getAlias());
    }
}