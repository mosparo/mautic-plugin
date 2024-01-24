<?php

namespace Form\Type;

use MauticPlugin\MosparoIntegrationBundle\Form\Type\MosparoHelperType;
use MauticPlugin\MosparoIntegrationBundle\Form\Type\MosparoIntegrationConnectionType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MosparoHelperTypeTest extends TestCase
{
    public function testBuildView()
    {
        $view = $this->createMock(FormView::class);

        $form = $this->createMock(Form::class);

        $type = new MosparoHelperType();
        $type->buildView($view, $form, ['html' => 'Test HTML code']);

        $this->assertEquals('Test HTML code', $view->vars['html']);
    }

    public function testConfigureOptions()
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with($this->identicalTo(['html' => '']));

        $type = new MosparoHelperType();
        $type->configureOptions($resolver);
    }

    public function testBlockPrefix()
    {
        $type = new MosparoHelperType();

        $this->assertEquals('mosparo_integration_helper', $type->getBlockPrefix());
    }
}