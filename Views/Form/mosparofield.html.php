<?php

$defaultInputClass = (isset($inputClass)) ? $inputClass : 'input';
$containerType     = 'div-wrapper';

include __DIR__.'/../../../../app/bundles/FormBundle/Views/Field/field_helper.php';

$action   = $app->getRequest()->get('objectAction');
$settings = $field['properties'];

$formName    = str_replace('_', '', $formName);
$formButtons = (!empty($inForm)) ? $view->render(
    'MauticFormBundle:Builder:actions.html.php',
    [
        'deleted'        => false,
        'id'             => $id,
        'formId'         => $formId,
        'formName'       => $formName,
        'disallowDelete' => false,
    ]
) : '';

$label = (!$field['showLabel']) ? '' : <<<HTML
<label $labelAttr>{$view->escape($field['label'])}</label>
HTML;

$formHelper = new \MauticPlugin\MosparoIntegrationBundle\Helper\FormHelper();
$connection = $formHelper->determineConnectionParameters($field['customParameters'], $field['properties']);

$formName    = str_replace('_', '', $formName);
$hashedFormName = md5($formName);

$designMode = 'false';
if ($inBuilder) {
    $designMode = 'true';
}

$jsCode = '';
if ($connection) {
    $host = $connection['host'];
    $uuid = $connection['uuid'];
    $publicKey = $connection['publicKey'];

    $jsCode = <<<JSELEMENT
        <script type="text/javascript">
            function initializeMosparo{$hashedFormName}()
            {
                new mosparo('mosparo-box-{$hashedFormName}', '{$host}', '{$uuid}', '{$publicKey}', {
                    loadCssResource: true, 
                    designMode: {$designMode}
                });
            }
            
            function loadMosparo{$hashedFormName}() {
                if (document.getElementById('mosparo-js-{$hashedFormName}') === null) {
                    let head = document.getElementsByTagName('head')[0];
                
                    let mosparoJs = document.createElement('script');
                    mosparoJs.id = 'mosparo-js-{$hashedFormName}';
                    mosparoJs.type = 'text/javascript';
                    mosparoJs.src = '{$host}/build/mosparo-frontend.js';
                    mosparoJs.onload = function() {
                        initializeMosparo{$hashedFormName}();
                    };
                    
                    head.appendChild(mosparoJs);
                } else {
                    initializeMosparo{$hashedFormName}();
                }
            }
            
            if (typeof mosparo !== 'undefined') {
                initializeMosparo{$hashedFormName}();
            } else {
                loadMosparo{$hashedFormName}();
            }
        </script>
JSELEMENT;

    echo <<<HTML
        <div $containerAttr>
            {$label}
            <div id="mosparo-box-{$hashedFormName}"></div>
        
            <input $inputAttr type="hidden">
            <span class="mauticform-errormsg" style="display: none;"></span>
        </div>
        {$jsCode}
HTML;
} else {
    echo <<<HTML
        No mosparo connection configured.
HTML;
}


