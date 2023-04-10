<?php

namespace Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\FormBundle\Entity\Field;
use Mautic\FormBundle\Entity\Form;
use Mautic\FormBundle\Event\ValidationEvent;
use MauticPlugin\MosparoIntegrationBundle\Helper\VerificationHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class VerificationHelperTest extends TestCase
{
    protected $eventDispatcher;

    protected $requestStack;

    protected $translator;

    protected $verificationHelper;

    protected $request;

    protected $field;

    protected $fields;

    protected $form;

    public function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->translator = $this->createMock(Translator::class);
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->willReturnCallback(function ($key) {
                return $key;
            });

        $this->verificationHelper = new VerificationHelper($this->eventDispatcher, $this->requestStack, $this->translator);

        $this->request = new Request();

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->willReturn($this->request);

        $this->fields = new ArrayCollection();

        $this->form = $this->createMock(Form::class);
        $this->form
            ->expects($this->once())
            ->method('getFields')
            ->willReturn($this->fields);

        $this->field = $this->createMock(Field::class);
        $this->field
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->event = new ValidationEvent($this->field, '');
    }

    public function testVerifyFormWithoutTokens()
    {
        [ $result, $message ] = $this->verificationHelper->verifyForm($this->event);

        $this->assertFalse($result);
        $this->assertEquals('mautic.mosparointegration.error.tokensInvalid', $message);
    }

    public function testVerifyFormWithoutValidationToken()
    {
        $this->request->request->set('_mosparo_submitToken', 'submitToken');

        [ $result, $message ] = $this->verificationHelper->verifyForm($this->event);

        $this->assertFalse($result);
        $this->assertEquals('mautic.mosparointegration.error.tokensInvalid', $message);
    }

    public function testVerifyFormWithoutConnection()
    {
        $this->request->request->add([
            '_mosparo_submitToken' => 'submitToken',
            '_mosparo_validationToken' => 'validationToken'
        ]);

        $this->field
            ->expects($this->once())
            ->method('getCustomParameters')
            ->willReturn([]);

        $this->field
            ->expects($this->once())
            ->method('getProperties')
            ->willReturn([]);

        [ $result, $message ] = $this->verificationHelper->verifyForm($this->event);

        $this->assertFalse($result);
        $this->assertEquals('mautic.mosparointegration.error.noConnectionAvailable', $message);
    }
}