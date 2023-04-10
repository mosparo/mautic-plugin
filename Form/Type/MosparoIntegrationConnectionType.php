<?php

namespace MauticPlugin\MosparoIntegrationBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MosparoIntegrationConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['integration']) && $options['integration']) {
            $configProvider = $options['integration'];
            if ($configProvider->getIntegrationConfiguration() && $configProvider->getIntegrationConfiguration()->getApiKeys()) {
                $data = $configProvider->getIntegrationConfiguration()->getApiKeys();
                $options['data'] = $data;
            }
        }

        $builder->add(
            'host',
            UrlType::class,
            [
                'label' => 'mautic.mosparointegration.connection.field.host',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control',
                    'tooltip' => 'mautic.mosparointegration.connection.tooltip.host',
                ],
                'data' => isset($options['data']['host']) ? $options['data']['host'] : '',
            ]
        );

        $builder->add(
            'uuid',
            TextType::class,
            [
                'label' => 'mautic.mosparointegration.connection.field.uuid',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control',
                    'tooltip' => 'mautic.mosparointegration.connection.tooltip.uuid',
                ],
                'data' => isset($options['data']['uuid']) ? $options['data']['uuid'] : '',
            ]
        );

        $builder->add(
            'publicKey',
            TextType::class,
            [
                'label' => 'mautic.mosparointegration.connection.field.publicKey',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control',
                    'tooltip' => 'mautic.mosparointegration.connection.tooltip.publicKey',
                ],
                'data' => isset($options['data']['publicKey']) ? $options['data']['publicKey'] : '',
            ]
        );

        $hasPrivateKey = isset($options['data']['privateKey']);
        $builder->add(
            'privateKey',
            PasswordType::class,
            [
                'label' => 'mautic.mosparointegration.connection.field.privateKey',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control',
                    'tooltip' => 'mautic.mosparointegration.connection.tooltip.privateKey',
                ],
                'required' => !$hasPrivateKey,
                'empty_data' => $hasPrivateKey ? $options['data']['privateKey'] : '',
            ]
        );

        $builder->add(
            'verifySsl',
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.mosparointegration.connection.field.verifySsl',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'tooltip' => 'mautic.mosparointegration.connection.tooltip.verifySsl',
                ],
                'data' => isset($options['data']['verifySsl']) ? $options['data']['verifySsl'] : true,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'integration' => '',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'mosparo-integration';
    }
}