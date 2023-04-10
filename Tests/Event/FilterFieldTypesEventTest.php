<?php

namespace Event;

use MauticPlugin\MosparoIntegrationBundle\Event\FilterFieldTypesEvent;
use PHPUnit\Framework\TestCase;

final class FilterFieldTypesEventTest extends TestCase
{
    public function testEventGetTypes()
    {
        $ignoredFieldTypes = ['captcha', 'password'];
        $verifiableFieldTypes = ['text', 'textarea'];
        $event = new FilterFieldTypesEvent($ignoredFieldTypes, $verifiableFieldTypes);

        $this->assertSame($ignoredFieldTypes, $event->getIgnoredFieldTypes());
        $this->assertSame($verifiableFieldTypes, $event->getVerifiableFieldTypes());
    }

    public function testEventSetAndGetTypes()
    {
        $ignoredFieldTypes = ['captcha', 'password'];
        $verifiableFieldTypes = ['text', 'textarea'];
        $event = new FilterFieldTypesEvent($ignoredFieldTypes, $verifiableFieldTypes);

        $newIgnoredFieldTypes = ['captcha', 'password', 'mosparo'];
        $newVerifiableFieldTypes = ['text', 'textarea', 'email'];

        $event->setIgnoredFieldTypes($newIgnoredFieldTypes);
        $event->setVerifiableFieldTypes($newVerifiableFieldTypes);

        $this->assertSame($newIgnoredFieldTypes, $event->getIgnoredFieldTypes());
        $this->assertSame($newVerifiableFieldTypes, $event->getVerifiableFieldTypes());
    }
}