<?php

namespace MauticPlugin\MosparoIntegrationBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;

class FilterFieldTypesEvent extends CommonEvent
{
    protected $ignoredFieldTypes;

    protected $verifiableFieldTypes;

    public function __construct($ignoredFieldTypes = [], $verifiableFieldTypes = [])
    {
        $this->ignoredFieldTypes = $ignoredFieldTypes;
        $this->verifiableFieldTypes = $verifiableFieldTypes;
    }

    public function getIgnoredFieldTypes()
    {
        return $this->ignoredFieldTypes;
    }

    public function setIgnoredFieldTypes($ignoredFieldTypes)
    {
        $this->ignoredFieldTypes = $ignoredFieldTypes;
    }

    public function getVerifiableFieldTypes()
    {
        return $this->verifiableFieldTypes;
    }

    public function setVerifiableFieldTypes($verifiableFieldTypes)
    {
        $this->verifiableFieldTypes = $verifiableFieldTypes;
    }
}
