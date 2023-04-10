<?php

namespace MauticPlugin\MosparoIntegrationBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormAuthInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\MosparoIntegrationBundle\Form\Type\MosparoIntegrationConnectionType;
use MauticPlugin\MosparoIntegrationBundle\Integration\MosparoIntegrationIntegration;

class ConfigSupport extends MosparoIntegrationIntegration implements ConfigFormInterface, ConfigFormAuthInterface
{
    use DefaultConfigFormTrait;

    public function getAuthConfigFormName(): string
    {
        return MosparoIntegrationConnectionType::class;
    }
}
