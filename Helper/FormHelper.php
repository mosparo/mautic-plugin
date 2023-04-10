<?php

namespace MauticPlugin\MosparoIntegrationBundle\Helper;

class FormHelper
{
    public function determineConnectionParameters(array $customProperties, array $fieldProperties = []): ?array
    {
        $globalConnection = $customProperties['globalConnection'] ?? null;
        if ($globalConnection === null) {
            return null;
        }

        $connection = [
            'host' => $globalConnection['host'] ?? '',
            'uuid' => $globalConnection['uuid'] ?? '',
            'publicKey' => $globalConnection['publicKey'] ?? '',
            'privateKey' => $globalConnection['privateKey'] ?? '',
            'verifySsl' => (bool) ($globalConnection['verifySsl'] ?? true),
        ];

        if (isset($fieldProperties['useDefaultConnection']) && !$fieldProperties['useDefaultConnection']) {
            $connection = [
                'host' => $fieldProperties['connection']['host'] ?? '',
                'uuid' => $fieldProperties['connection']['uuid'] ?? '',
                'publicKey' => $fieldProperties['connection']['publicKey'] ?? '',
                'privateKey' => $fieldProperties['connection']['privateKey'] ?? '',
                'verifySsl' => (bool) ($fieldProperties['connection']['verifySsl'] ?? true),
            ];
        }

        if (empty($connection['host'])) {
            return null;
        }

        return $connection;
    }
}