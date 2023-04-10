<?php

return [
    'name'        => 'mosparo Integration',
    'description' => 'Integrates mosparo in Mautic to protect your form with mosparo.',
    'version'     => '1.0.0',
    'author'      => 'mosparo',
    'routes'      => [
        'main'   => [],
        'public' => [],
        'api'    => [],
    ],
    'menu'        => [],
    'services' => [
        'other' => [
            'mosparointegration.helper.clienthelper' => [
                'class' => \MauticPlugin\MosparoIntegrationBundle\Helper\ClientHelper::class,
            ],
            'mosparointegration.helper.verificationhelper' => [
                'class' => \MauticPlugin\MosparoIntegrationBundle\Helper\VerificationHelper::class,
                'arguments' => [
                    'event_dispatcher',
                    'request_stack',
                    'translator',
                    'mosparointegration.helper.clienthelper',
                ]
            ],
        ],
        'events' => [
            'mautic.mosparointegration.event_listener.form_subscriber' => [
                'class'     => \MauticPlugin\MosparoIntegrationBundle\EventListener\FormSubscriber::class,
                'arguments' => [
                    'mautic.integrations.helper',
                    'mosparointegration.helper.verificationhelper',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.mosparointegration' => [
                'class' => \MauticPlugin\MosparoIntegrationBundle\Integration\MosparoIntegrationIntegration::class,
                'tags'  => [
                    'mautic.integration',
                    'mautic.basic_integration',
                ],
            ],
            'mosparointegration.integration.configuration' => [
                'class' => \MauticPlugin\MosparoIntegrationBundle\Integration\Support\ConfigSupport::class,
                'tags' => [
                    'mautic.config_integration',
                ],
            ],
        ],
    ],
];