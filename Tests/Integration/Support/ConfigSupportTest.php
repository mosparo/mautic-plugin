<?php

namespace Integration\Support;

use MauticPlugin\MosparoIntegrationBundle\Integration\Support\ConfigSupport;
use PHPUnit\Framework\TestCase;

final class ConfigSupportTest extends TestCase
{
    public function testInitialize()
    {
        $integration = new ConfigSupport();

        $this->assertEquals('MauticPlugin\MosparoIntegrationBundle\Form\Type\MosparoIntegrationConnectionType', $integration->getAuthConfigFormName());
    }
}