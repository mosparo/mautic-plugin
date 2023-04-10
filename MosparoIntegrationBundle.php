<?php

namespace MauticPlugin\MosparoIntegrationBundle;

use Mautic\IntegrationsBundle\Bundle\AbstractPluginBundle;

class MosparoIntegrationBundle extends AbstractPluginBundle
{
}

if (!class_exists('Mosparo\ApiClient\Client') && file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once(__DIR__ . '/vendor/autoload.php');
}