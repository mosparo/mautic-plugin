<?php

namespace MauticPlugin\MosparoIntegrationBundle\Helper;

use Mosparo\ApiClient\Client;

class ClientHelper
{
    public function getClient($host, $publicKey, $privateKey, $verifySsl)
    {
        return new Client($host, $publicKey, $privateKey, ['verify' => $verifySsl]);
    }
}