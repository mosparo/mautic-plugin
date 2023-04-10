<?php

namespace Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\FormBundle\Entity\Field;
use Mautic\FormBundle\Entity\Form;
use Mautic\FormBundle\Event\ValidationEvent;
use MauticPlugin\MosparoIntegrationBundle\Event\FilterFieldTypesEvent;
use MauticPlugin\MosparoIntegrationBundle\Helper\ClientHelper;
use MauticPlugin\MosparoIntegrationBundle\Helper\VerificationHelper;
use Mosparo\ApiClient\Client;
use Mosparo\ApiClient\VerificationResult;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class VerificationHelperTest extends TestCase
{
    protected $eventDispatcher;

    protected $requestStack;

    protected $translator;

    protected $clientHelper;

    protected $client;

    protected $verificationHelper;

    protected $request;

    protected $field;

    protected $fields;

    protected $form;

    protected $globalConnection = [
        'globalConnection' => [
            'host' => 'https://mosparo.test',
            'uuid' => '12345678-test-test-test-test12345678',
            'publicKey' => 'publicKey',
            'privateKey' => 'privateKey',
            'verifySsl' => false
        ]
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->translator = $this->createMock(Translator::class);
        $this->clientHelper = $this->createMock(ClientHelper::class);
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->willReturnCallback(function ($key) {
                return $key;
            });

        $this->client = $this->createMock(Client::class);

        $this->verificationHelper = new VerificationHelper($this->eventDispatcher, $this->requestStack, $this->translator, $this->clientHelper);

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

    public function testVerifyFormClientThrowsException()
    {
        $this->request->request->add([
            'mauticform' => [
                'first_name' => 'first-name',
                'last_name' => 'last-name',
            ],
            '_mosparo_submitToken' => 'submitToken',
            '_mosparo_validationToken' => 'validationToken'
        ]);

        $this->field
            ->expects($this->once())
            ->method('getCustomParameters')
            ->willReturn($this->globalConnection);

        $this->field
            ->expects($this->once())
            ->method('getProperties')
            ->willReturn([]);

        $this->clientHelper
            ->expects($this->any())
            ->method('getClient')
            ->willReturn($this->client);

        $this->client
            ->expects($this->once())
            ->method('verifySubmission')
            ->willThrowException(new \Mosparo\ApiClient\Exception('General exception'));

        [ $result, $message ] = $this->verificationHelper->verifyForm($this->event);

        $this->assertFalse($result);
        $this->assertEquals('mautic.mosparointegration.error.generalErrorOccurred', $message);
    }

    public function testVerifyFormNotSubmittable()
    {
        $this->request->request->add([
            'mauticform' => [
                'first_name' => 'first-name',
                'last_name' => 'last-name',
            ],
            '_mosparo_submitToken' => 'submitToken',
            '_mosparo_validationToken' => 'validationToken'
        ]);

        $this->field
            ->expects($this->once())
            ->method('getCustomParameters')
            ->willReturn($this->globalConnection);

        $this->field
            ->expects($this->once())
            ->method('getProperties')
            ->willReturn([]);

        $this->clientHelper
            ->expects($this->any())
            ->method('getClient')
            ->willReturn($this->client);

        $verificationResult = new VerificationResult(false, false, ['firstName' => 'invalid', 'lastName' => 'invalid'], []);

        $this->client
            ->expects($this->once())
            ->method('verifySubmission')
            ->willReturn($verificationResult);

        [ $result, $message ] = $this->verificationHelper->verifyForm($this->event);

        $this->assertFalse($result);
        $this->assertEquals('mautic.mosparointegration.error.submissionInvalid', $message);
    }

    public function testVerifyFormRequiredFieldsMismatch()
    {
        $this->request->request->add([
            'mauticform' => [
                'first_name' => 'first-name',
                'last_name' => 'last-name',
            ],
            '_mosparo_submitToken' => 'submitToken',
            '_mosparo_validationToken' => 'validationToken'
        ]);

        $this->addFields();

        $this->field
            ->expects($this->once())
            ->method('getCustomParameters')
            ->willReturn($this->globalConnection);

        $this->field
            ->expects($this->once())
            ->method('getProperties')
            ->willReturn([]);

        $this->clientHelper
            ->expects($this->any())
            ->method('getClient')
            ->willReturn($this->client);

        $verificationResult = new VerificationResult(true, true, ['mauticform[last_name]' => 'not-verified'], []);

        $this->client
            ->expects($this->once())
            ->method('verifySubmission')
            ->willReturn($verificationResult);

        [ $result, $message ] = $this->verificationHelper->verifyForm($this->event);

        $this->assertFalse($result);
        $this->assertEquals('mautic.mosparointegration.error.submissionInvalidRequiredFieldsMismatch', $message);
    }

    public function testVerifyFormVerifiableFieldsMismatch()
    {
        $this->request->request->add([
            'mauticform' => [
                'first_name' => 'first-name',
                'last_name' => 'last-name',
            ],
            '_mosparo_submitToken' => 'submitToken',
            '_mosparo_validationToken' => 'validationToken'
        ]);

        $this->addFields();

        $this->field
            ->expects($this->once())
            ->method('getCustomParameters')
            ->willReturn($this->globalConnection);

        $this->field
            ->expects($this->once())
            ->method('getProperties')
            ->willReturn([]);

        $this->clientHelper
            ->expects($this->any())
            ->method('getClient')
            ->willReturn($this->client);

        $verificationResult = new VerificationResult(true, true, ['mauticform[first_name]' => 'valid'], []);

        $this->client
            ->expects($this->once())
            ->method('verifySubmission')
            ->willReturn($verificationResult);

        [ $result, $message ] = $this->verificationHelper->verifyForm($this->event);

        $this->assertFalse($result);
        $this->assertEquals('mautic.mosparointegration.error.submissionInvalidVerifiableFieldsMismatch', $message);
    }

    public function testVerifyFormVerifiableFieldsMismatchWithPasswordType()
    {
        $this->request->request->add([
            'mauticform' => [
                'first_name' => 'first-name',
                'last_name' => 'last-name',
            ],
            '_mosparo_submitToken' => 'submitToken',
            '_mosparo_validationToken' => 'validationToken'
        ]);

        $this->addFields();

        $this->field
            ->expects($this->once())
            ->method('getCustomParameters')
            ->willReturn($this->globalConnection);

        $this->field
            ->expects($this->once())
            ->method('getProperties')
            ->willReturn([]);

        $this->clientHelper
            ->expects($this->any())
            ->method('getClient')
            ->willReturn($this->client);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('hasListeners')
            ->willReturn(true);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function (FilterFieldTypesEvent $event) {
                $event->setIgnoredFieldTypes([]);
                $event->setVerifiableFieldTypes(array_merge($event->getVerifiableFieldTypes(), ['password']));
            });

        $verificationResult = new VerificationResult(true, true, ['mauticform[first_name]' => 'valid', 'mauticform[last_name]' => 'valid'], []);

        $this->client
            ->expects($this->once())
            ->method('verifySubmission')
            ->willReturn($verificationResult);

        [ $result, $message ] = $this->verificationHelper->verifyForm($this->event);

        $this->assertFalse($result);
        $this->assertEquals('mautic.mosparointegration.error.submissionInvalidVerifiableFieldsMismatch', $message);
    }

    public function testVerifyFormIsValid()
    {
        $this->request->request->add([
            'mauticform' => [
                'first_name' => 'first-name',
                'last_name' => 'last-name',
            ],
            '_mosparo_submitToken' => 'submitToken',
            '_mosparo_validationToken' => 'validationToken'
        ]);

        $this->addFields();

        $this->field
            ->expects($this->once())
            ->method('getCustomParameters')
            ->willReturn($this->globalConnection);

        $this->field
            ->expects($this->once())
            ->method('getProperties')
            ->willReturn([]);

        $this->clientHelper
            ->expects($this->any())
            ->method('getClient')
            ->willReturn($this->client);

        $verificationResult = new VerificationResult(true, true, ['mauticform[first_name]' => 'valid', 'mauticform[last_name]' => 'valid'], []);

        $this->client
            ->expects($this->once())
            ->method('verifySubmission')
            ->willReturn($verificationResult);

        [ $result, $message ] = $this->verificationHelper->verifyForm($this->event);

        $this->assertTrue($result);
        $this->assertEmpty($message);
    }

    protected function addFields()
    {
        $firstNameField = $this->createMock(Field::class);
        $firstNameField
            ->expects($this->once())
            ->method('getType')
            ->willReturn('text');
        $firstNameField
            ->expects($this->exactly(2))
            ->method('getAlias')
            ->willReturn('first_name');
        $firstNameField
            ->expects($this->once())
            ->method('isRequired')
            ->willReturn(true);

        $this->fields->add($firstNameField);

        $lastNameField = $this->createMock(Field::class);
        $lastNameField
            ->expects($this->once())
            ->method('getType')
            ->willReturn('text');
        $lastNameField
            ->expects($this->exactly(2))
            ->method('getAlias')
            ->willReturn('last_name');
        $lastNameField
            ->expects($this->once())
            ->method('isRequired')
            ->willReturn(false);

        $this->fields->add($lastNameField);

        $passwordField = $this->createMock(Field::class);
        $passwordField
            ->expects($this->once())
            ->method('getType')
            ->willReturn('password');
        $passwordField
            ->expects($this->any())
            ->method('isRequired')
            ->willReturn(false);

        $this->fields->add($passwordField);
    }
}