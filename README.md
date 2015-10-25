# zf-saml

SAML authentication module for ZF2.

[![Build Status](https://travis-ci.org/MehrAlsNix/zf-saml.svg)](https://travis-ci.org/MehrAlsNix/zf-saml)

This module uses `onelogin/php-saml` internally and wrap the function in services.

## Installation

Just require `mehr-asl-nix/zf-saml` in your `composer.json`.

Enable it in application.config.php
```
    'modules' => array (
        // ---8<---
        'ZF\ApiProblem',
        'ZF\ContentNegotiation',
        'MehrAlsNix\ZF\SAML',
        // --->8---
    ),
```

## Configuration

tbd

## Services

### MehrAlsNix\ZF\SAML\Service\SAML2Auth

Provides an instance of `OneLogin_Saml2_Auth`

### MehrAlsNix\ZF\SAML\Service\SAML2AuthnRequest

Provides an instance of `OneLogin_Saml2_AuthnRequest`

### MehrAlsNix\ZF\SAML\Service\SAML2Metadata

Provides an instance of `OneLogin_Saml2_Metadata`

### MehrAlsNix\ZF\SAML\Service\SAML2Response

Provides an instance of `OneLogin_Saml2_Response`

### MehrAlsNix\ZF\SAML\Service\SAML2Settings

Provides an instance of `OneLogin_Saml2_Settings`

## API protection

Assume `$this->samlAuth` contains an instance of `OneLogin_Saml2_Auth`, then
you would be able to protect your API. 

```
    if (!$this->samlAuth->isAuthenticated()) {
        $this->getResponse()->setStatusCode(401);
        return;
    }
```
