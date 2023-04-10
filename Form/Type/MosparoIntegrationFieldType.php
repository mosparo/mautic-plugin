<?php

namespace MauticPlugin\MosparoIntegrationBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\YesNoButtonGroupType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MosparoIntegrationFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'useDefaultConnection',
            YesNoButtonGroupType::class,
            [
                'label' => 'mautic.mosparoIntegration.formField.field.useDefaultConnection',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'tooltip' => 'mautic.mosparoIntegration.formField.tooltip.useDefaultConnection',
                ],
                'data' => isset($options['data']['useDefaultConnection']) ? $options['data']['useDefaultConnection'] : true,
            ]
        );

        $builder->add(
            'connection',
            MosparoIntegrationConnectionType::class,
            [
                'label' => false,
                'attr' => [
                    'data-show-on' => '{"formfield_properties_useDefaultConnection_1":""}'
                ],
                'data' => isset($options['data']['connection']) ? $options['data']['connection'] : [],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'globalConnection' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'mosparo-integration';
    }
}