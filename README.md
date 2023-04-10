&nbsp;
<p align="center">
    <img src="https://github.com/mosparo/mosparo/blob/master/assets/images/mosparo-logo.svg?raw=true" alt="mosparo logo contains a bird with the name Mo and the mosparo text"/>
</p>

<h1 align="center">
    Integration for Mautic
</h1>
<p align="center">
    This Mautic plugin adds the functionality to use mosparo in your Mautic form.
</p>

-----

## Description
The Mautic plugin adds the functionality to protect your forms in Mautic with mosparo. mosparo can filter spam submissions before they get processed by Mautic.

## Requirements
To use the plugin, you must meet the following requirements:
- A mosparo installation (v0.3.14 or newer)
- A Mautic installation (v4.4 or newer)
- Access to the files of your Mautic installation

## Installation
To use the plugin, please follow these installation instructions:

### With the .zip package

1. Download the package from the releases page
2. Extract the package
3. Upload the directory `MosparoIntegrationBundle` into the `plugins` directory of your Mautic installation
4. Clear the cache by executing `php app/console cache:clear --env=prod` in the root of the Mautic installation **or** delete the directory `var/cache/prod`
5. Log into Mautic and go to the plugin management page
6. Click the button `Install/Upgrade Plugins` in the top right corner
7. Click on the `mosparo Integration` icon in the list of available plugins
8. Set the flag `Plugin must be enabled and authorized for this field to work` to `Yes`
9. Enter the mosparo connection details to your mosparo project below
10. Click `Save`

### With composer

1. Open a terminal and change into the root of the Mautic installation
2. Execute `composer require mosparo/mautic-integration-bundle:^1.0`
3. Log into Mautic and go to the plugin management page
4. Click the button `Install/Upgrade Plugins` in the top right corner
5. Click on the `mosparo Integration` icon in the list of available plugins
6. Set the flag `Plugin must be enabled and authorized for this field to work` to `Yes`
7. Enter the mosparo connection details to your mosparo project below
8. Click `Save`


## Usage

To use the mosparo integration, please do the following steps:

1. Edit your form
2. Choose the field type `mosparo` from the dropdown and configure the field according to your needs.
3. If you want to use a different connection for one of your forms, you can choose not to use the default connection when you edit the field. You will then see additional connection fields to set up the mosparo connection for this form.
4. Save your form

### Important

**Please do not use other spam protection methods like captcha, honeypot, or other 3rd party services.**