<?php

namespace MauticPlugin\MosparoIntegrationBundle\EventListener;

use Mautic\FormBundle\Event as Events;
use Mautic\FormBundle\FormEvents;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\IntegrationsBundle\Integration\Interfaces\IntegrationInterface;
use MauticPlugin\MosparoIntegrationBundle\Form\Type\MosparoIntegrationFieldType;
use MauticPlugin\MosparoIntegrationBundle\Helper\VerificationHelper;
use MauticPlugin\MosparoIntegrationBundle\Integration\MosparoIntegrationIntegration;
use MauticPlugin\MosparoIntegrationBundle\MosparoIntegrationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormSubscriber implements EventSubscriberInterface
{
    protected IntegrationInterface $integration;

    protected VerificationHelper $verificationHelper;

    public function __construct(IntegrationsHelper $integrationsHelper, VerificationHelper $verificationHelper)
    {
        $this->integration = $integrationsHelper->getIntegration(MosparoIntegrationIntegration::NAME);
        $this->verificationHelper = $verificationHelper;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD => ['onFormBuild', 0],
            MosparoIntegrationEvents::ON_FORM_VALIDATE => ['onFormValidate', 0],
        ];
    }

    public function onFormBuild(Events\FormBuilderEvent $event)
    {
        // Register the mosparo form field
        $event->addFormField(
            'plugin.mosparointegration',
            [
                'label'    => 'mautic.mosparointegration.formfield.mosparofield',
                'formType' => MosparoIntegrationFieldType::class,
                'template' => 'MosparoIntegrationBundle:Form:mosparofield.html.php',
                'builderOptions' => [
                    'addHelpMessage' => true,
                    'addShowLabel' => true,
                    'addDefaultValue' => false,
                    'addLabelAttributes' => false,
                    'addInputAttributes' => false,
                    'addIsRequired' => false,
                    'addSaveResult' => true,
                    'addLeadFieldList' => false,
                ],
                'globalConnection' => $this->integration->getIntegrationConfiguration()->getApiKeys(),
            ]
        );

        // Register the mosparo form field validator
        $event->addValidator('plugin.mosparointegration.validator', [
            'eventName' => MosparoIntegrationEvents::ON_FORM_VALIDATE,
            'fieldType' => 'plugin.mosparointegration',
        ]);
    }

    public function onFormValidate(Events\ValidationEvent $event)
    {
        [ $result, $message ] = $this->verificationHelper->verifyForm($event);

        if (!$result) {
            $event->failedValidation($message);
        }
    }
}