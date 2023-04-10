<?php

namespace MauticPlugin\MosparoIntegrationBundle\Helper;

use Doctrine\Common\Collections\Collection;
use Mautic\FormBundle\Event\ValidationEvent;
use MauticPlugin\MosparoIntegrationBundle\Event\FilterFieldTypesEvent;
use Mosparo\ApiClient\Client;
use Mosparo\ApiClient\Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class VerificationHelper
{
    protected EventDispatcherInterface $eventDispatcher;

    protected RequestStack $requestStack;

    protected TranslatorInterface $translator;

    protected ClientHelper $clientHelper;

    public function __construct(EventDispatcherInterface $eventDispatcher, RequestStack $requestStack, TranslatorInterface $translator, ClientHelper $clientHelper)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->clientHelper = $clientHelper;
    }

    public function verifyForm(ValidationEvent $event): array
    {
        $formHelper = new FormHelper();

        $request = $this->requestStack->getMasterRequest();
        $data = $request->request->all();
        $field = $event->getField();
        $form = $field->getForm();

        [ $formData, $requiredFields, $verifiableFields ] = $this->prepareFormData($data, $form->getFields());
        $mosparoSubmitToken = $data['_mosparo_submitToken'] ?? '';
        $mosparoValidationToken = $data['_mosparo_validationToken'] ?? '';

        // If the tokens are not available, the submission cannot be valid.
        if (empty($mosparoSubmitToken) || empty($mosparoValidationToken)) {
            return [ false, $this->translator->trans('mautic.mosparointegration.error.tokensInvalid') ];
        }

        // Prepare the connection
        $connection = $formHelper->determineConnectionParameters($field->getCustomParameters(), $field->getProperties());
        if ($connection === null) {
            return [ false, $this->translator->trans('mautic.mosparointegration.error.noConnectionAvailable') ];
        }

        // Verify the submission
        $client = $this->clientHelper->getClient(
            $connection['host'],
            $connection['publicKey'],
            $connection['privateKey'],
            $connection['verifySsl']
        );
        try {
            $result = $client->verifySubmission($formData, $mosparoSubmitToken, $mosparoValidationToken);
        } catch (Exception $e) {
            return [ false, sprintf($this->translator->trans('mautic.mosparointegration.error.generalErrorOccurred'), $e->getMessage()) ];
        }

        if (!$result->isSubmittable()) {
            return [ false, $this->translator->trans('mautic.mosparointegration.error.submissionInvalid') ];
        }

        $verifiedFields = array_keys($result->getVerifiedFields());
        $requiredFieldDifference = array_diff($requiredFields, $verifiedFields);
        $verifiableFieldDifference = array_diff($verifiableFields, $verifiedFields);
        if (!empty($requiredFieldDifference)) {
            return [ false, $this->translator->trans('mautic.mosparointegration.error.submissionInvalidRequiredFieldsMismatch') ];
        }

        if (!empty($verifiableFieldDifference)) {
            return [ false, $this->translator->trans('mautic.mosparointegration.error.submissionInvalidVerifiableFieldsMismatch') ];
        }

        return [ true, '' ];
    }

    protected function prepareFormData(array $data, Collection $fields): array
    {
        $preparedData = [];
        $requiredFields = [];
        $verifiableFields = [];

        $ignoredFieldTypes = [
            'checkboxgrp',
            'file',
            'freehtml',
            'hidden',
            'pagebreak',
            'password',
            'radiogrp',
            'plugin.loginSocial',
            'plugin.recaptcha',
            'plugin.mosparointegration',
            'button',
        ];
        $verifiableFieldTypes = [
            'text',
            'textarea',
            'email',
            'url',
            'number',
            'tel',
        ];

        // Let other plugins filter the lists of ignored and verifiable field types
        if ($this->eventDispatcher->hasListeners(FilterFieldTypesEvent::class)) {
            $filterFieldTypesEvent = new FilterFieldTypesEvent($ignoredFieldTypes, $verifiableFieldTypes);
            $filterFieldTypesEvent = $this->eventDispatcher->dispatch($filterFieldTypesEvent);

            $ignoredFieldTypes = $filterFieldTypesEvent->getIgnoredFieldTypes();
            $verifiableFieldTypes = $filterFieldTypesEvent->getVerifiableFieldTypes();
        }

        foreach ($fields as $field) {
            $type = $field->getType();
            if (in_array($type, $ignoredFieldTypes)) {
                continue;
            }

            $key = 'mauticform[' . $field->getAlias() . ']';
            $value = $data['mauticform'][$field->getAlias()] ?? '';

            $preparedData[$key] = $value;

            if ($field->isRequired()) {
                $requiredFields[] = $key;
            }

            if (in_array($type, $verifiableFieldTypes)) {
                $verifiableFields[] = $key;
            }
        }

        return [ $preparedData, $requiredFields, $verifiableFields ];
    }
}