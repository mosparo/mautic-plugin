<?php

namespace EventSubscriber;

use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use MauticPlugin\MosparoIntegrationBundle\EventListener\FormSubscriber;
use MauticPlugin\MosparoIntegrationBundle\Helper\VerificationHelper;
use MauticPlugin\MosparoIntegrationBundle\Integration\MosparoIntegrationIntegration;
use MauticPlugin\MosparoIntegrationBundle\MosparoIntegrationEvents;
use PHPUnit\Framework\TestCase;

final class FormSubscriberTest extends TestCase
{
    protected $integrationsHelper;
    protected $verificationHelper;

    public function prepareMocks()
    {
        $this->integration = $this->createMock(MosparoIntegrationIntegration::class);

        $this->integrationsHelper = $this->createMock(IntegrationsHelper::class);
        $this->integrationsHelper->expects($this->once())
                                 ->method('getIntegration')
                                 ->willReturn($this->integration);

        $this->verificationHelper = $this->createMock(VerificationHelper::class);
    }

    public function testGetSubscribedEvents()
    {
        $subscribedEvents = FormSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(FormEvents::FORM_ON_BUILD, $subscribedEvents);
        $this->assertArrayHasKey(MosparoIntegrationEvents::ON_FORM_VALIDATE, $subscribedEvents);
    }

    public function testOnFormBuild()
    {
        $this->prepareMocks();

        $event = $this->createMock(FormBuilderEvent::class);

        $event->expects($this->once())
              ->method('addFormField')
              ->with($this->identicalTo('plugin.mosparointegration'));

        $event->expects($this->once())
              ->method('addValidator')
              ->with($this->identicalTo('plugin.mosparointegration.validator'));

        $subscriber = new FormSubscriber($this->integrationsHelper, $this->verificationHelper);
        $subscriber->onFormBuild($event);
    }

    public function testOnFormValidate()
    {
        $this->prepareMocks();

        $event = $this->createMock(ValidationEvent::class);

        $this->verificationHelper->expects($this->once())
                                 ->method('verifyForm')
                                 ->with($event)
                                 ->willReturn([ false, 'General error' ]);

        $event->expects($this->once())
              ->method('failedValidation')
              ->with($this->identicalTo('General error'));

        $subscriber = new FormSubscriber($this->integrationsHelper, $this->verificationHelper);
        $subscriber->onFormValidate($event);
    }
}