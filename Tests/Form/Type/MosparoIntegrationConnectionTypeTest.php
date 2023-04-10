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
                $this->match($name, $type);
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
                $this->match($name, $type);
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

    protected function match($name, $type)
    {
        $options = [
            ['host', UrlType::class],
            ['uuid', TextType::class],
            ['publicKey', TextType::class],
            ['privateKey', PasswordType::class],
            ['verifySsl', YesNoButtonGroupType::class],
        ];

        foreach ($options as $option) {
            if ($name === $option[0] && $type === $option[1]) {
                return true;
            }
        }

        throw new \Exception('Arguments not matching');
    }
}