<?php

namespace MauticPlugin\MosparoIntegrationBundle\Tests\Helper;

use PHPUnit\Framework\TestCase;

final class FormHelperTest extends TestCase
{
    public function testDetermineConnectionWithoutGlobal()
    {
        $helper = new \MauticPlugin\MosparoIntegrationBundle\Helper\FormHelper();

        $connection = $helper->determineConnectionParameters([]);

        $this->assertNull($connection);
    }

    public function testDetermineConnectionWithGlobalOnly()
    {
        $helper = new \MauticPlugin\MosparoIntegrationBundle\Helper\FormHelper();

        $connection = $helper->determineConnectionParameters([
            'globalConnection' => [
                'host' => 'h',
                'uuid' => 'u',
                'publicKey' => 'pu',
                'privateKey' => 'pr',
                'verifySsl' => true,
            ],
        ]);

        $this->assertEquals('h', $connection['host']);
        $this->assertEquals('u', $connection['uuid']);
        $this->assertEquals('pu', $connection['publicKey']);
        $this->assertEquals('pr', $connection['privateKey']);
        $this->assertTrue($connection['verifySsl']);
    }

    public function testDetermineConnectionWithGlobalAndField()
    {
        $helper = new \MauticPlugin\MosparoIntegrationBundle\Helper\FormHelper();

        $connection = $helper->determineConnectionParameters([
            'globalConnection' => [
                'host' => 'h',
                'uuid' => 'u',
                'publicKey' => 'pu',
                'privateKey' => 'pr',
                'verifySsl' => true,
            ],
        ], [
            'useDefaultConnection' => false,
            'connection' => [
                'host' => 'fH',
                'uuid' => 'fU',
                'publicKey' => 'fPu',
                'privateKey' => 'fPr',
                'verifySsl' => false,
            ]
        ]);

        $this->assertEquals('fH', $connection['host']);
        $this->assertEquals('fU', $connection['uuid']);
        $this->assertEquals('fPu', $connection['publicKey']);
        $this->assertEquals('fPr', $connection['privateKey']);
        $this->assertFalse($connection['verifySsl']);
    }

    public function testDetermineConnectionWithEmptyGlobal()
    {
        $helper = new \MauticPlugin\MosparoIntegrationBundle\Helper\FormHelper();

        $connection = $helper->determineConnectionParameters([
            'globalConnection' => [
                'host' => '',
                'uuid' => '',
                'publicKey' => '',
                'privateKey' => '',
            ],
        ]);

        $this->assertNull($connection);
    }
}