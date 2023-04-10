&nbsp;
<p align="center">
    <img src="https://github.com/mosparo/mosparo/blob/master/assets/images/mosparo-logo.svg?raw=true" alt="mosparo logo contains a bird with the name Mo and the mosparo text"/>
</p>

<h1 align="center">
    Integration for Mautic
</h1>
<p align="center">
    This Mautic plugin adds the required functionality to use mosparo in your Mautic form.
</p>

-----

## Description
The Mautic plugin adds the functionality to protect your forms in Mautic with mosparo. mosparo can filter spam submissions before they get processed by Mautic.

## Requirements
To use the plugin, you must meet the following requirements:
- A mosparo project
- A Mautic installation (v4.4 or newer)
- Access to the files of your Mautic installation

## Installation
To use the plugin, please follow this installation instruction:

1. Download the package from the releases page
2. Extract the package
3. Upload the directory `MosparoIntegrationBundle` into the directory `plugins` of your Mautic instasllation
4. Log into Mautic and go to the plugin management page
5. Click the button `Install/Upgrade Plugins` in the top right corner
6. Click on the `mosparo Integration` icon in the list of available plugins
7. Set the flag `Plugin must be enabled and authorized for this field to work` to `Yes`
8. Enter the mosparo connection details to your mosparo project below
9. Click `Save`
10. Edit your form
11. Choose the field type `mosparo` from the dropdown
12. Save your form
