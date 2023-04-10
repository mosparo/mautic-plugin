<?php

namespace Helper;

use MauticPlugin\MosparoIntegrationBundle\Helper\ClientHelper;
use Mosparo\ApiClient\Client;
use PHPUnit\Framework\TestCase;

final class ClientHelperTest extends TestCase
{
    public function testDetermineConnectionWithoutGlobal()
    {
        $helper = new ClientHelper();

        $client = $helper->getClient('h', 'pu', 'pr', ['verify' => false]);

        $this->assertInstanceOf(Client::class, $client);
    }
}