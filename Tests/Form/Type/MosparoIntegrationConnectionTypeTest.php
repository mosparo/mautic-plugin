<?php

namespace Form\Type;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Mautic\PluginBundle\Entity\Integration;
use MauticPlugin\MosparoIntegrationBundle\Form\Type\MosparoIntegrationConnectionType;
use MauticPlugin\MosparoIntegrationBundle\Integration\MosparoIntegrationIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MosparoIntegrationConnectionTypeTest extends TestCase
{
    public function testBuildFormWithoutIntegration()
    {
        $builder = $this->createMock(FormBuilder::class);
        $builder
            ->expects($this->exactly(5))
            ->method('add')
            ->willReturnCallback(function ($name, $type, $args) {
                match ([$name, $type]) {
                    ['host', UrlType::class] => 'host',
                    ['uuid', TextType::class] => 'uuid',
                    ['publicKey', TextType::class] => 'publicKey',
                    ['privateKey', PasswordType::class] => 'privateKey',
                    ['verifySsl', YesNoButtonGroupType::class] => 'verifySsl'
                };
            });

        $type = new MosparoIntegrationConnectionType();
        $type->buildForm($builder, []);
    }

    public function testBuildFormWithIntegration()
    {
        $builder = $this->createMock(FormBuilder::class);
        $builder
            ->expects($this->exactly(5))
            ->method('add')
            ->willReturnCallback(function ($name, $type, $args) {
                match ([$name, $type]) {
                    ['host', UrlType::class] => 'host',
                    ['uuid', TextType::class] => 'uuid',
                    ['publicKey', TextType::class] => 'publicKey',
                    ['privateKey', PasswordType::class] => 'privateKey',
                    ['verifySsl', YesNoButtonGroupType::class] => 'verifySsl'
                };
            });

        $configuration = $this->createMock(Integration::class);
        $configuration
            ->expects($this->exactly(2))
            ->method('getApiKeys')
            ->willReturn(['host' => 'a', 'uuid' => 'b', 'publicKey' => 'pu', 'privateKey' => 'pr', 'verifySsl' => true]);

        $integration = $this->createMock(MosparoIntegrationIntegration::class);
        $integration
            ->expects($this->exactly(3))
            ->method('getIntegrationConfiguration')
            ->willReturn($configuration);

        $type = new MosparoIntegrationConnectionType();
        $type->buildForm($builder, ['integration' => $integration]);
    }

    public function testConfigureOptions()
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with($this->identicalTo(['integration' => '']));

        $type = new MosparoIntegrationConnectionType();
        $type->configureOptions($resolver);
    }

    public function testBlockPrefix()
    {
        $type = new MosparoIntegrationConnectionType();

        $this->assertEquals('mosparo-integration', $type->getBlockPrefix());
    }
}