<?php

namespace Form\Type;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use MauticPlugin\MosparoIntegrationBundle\Form\Type\MosparoIntegrationConnectionType;
use MauticPlugin\MosparoIntegrationBundle\Form\Type\MosparoIntegrationFieldType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MosparoIntegrationFieldTypeTest extends TestCase
{
    public function testBuildFormWithoutIntegration()
    {
        $builder = $this->createMock(FormBuilder::class);
        $builder
            ->expects($this->exactly(2))
            ->method('add')
            ->willReturnCallback(function ($name, $type, $args) {
                match ([$name, $type]) {
                    ['useDefaultConnection', YesNoButtonGroupType::class] => 'useDefaultConnection',
                    ['connection', MosparoIntegrationConnectionType::class] => 'connection',
                };
            });

        $type = new MosparoIntegrationFieldType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions()
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with($this->identicalTo(['globalConnection' => null]));

        $type = new MosparoIntegrationFieldType();
        $type->configureOptions($resolver);
    }

    public function testBlockPrefix()
    {
        $type = new MosparoIntegrationFieldType();

        $this->assertEquals('mosparo-integration', $type->getBlockPrefix());
    }
}