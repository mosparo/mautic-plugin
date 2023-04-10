<?php

namespace Integration;

use MauticPlugin\MosparoIntegrationBundle\Integration\MosparoIntegrationIntegration;
use PHPUnit\Framework\TestCase;

final class MosparoIntegrationIntegrationTest extends TestCase
{
    public function testInitialize()
    {
        $integration = new MosparoIntegrationIntegration();

        $this->assertEquals('mosparointegration', $integration->getName());
        $this->assertEquals('mosparo Integration', $integration->getDisplayName());
        $this->assertEquals('plugins/MosparoIntegrationBundle/Assets/img/mosparo.png', $integration->getIcon());
    }
}