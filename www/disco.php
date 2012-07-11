<?php

/**
 * Builtin IdP discovery service.
 */

$discoHandler = new sspmod_accountLinking_IdPDisco(array('saml20-idp-remote', 'shib13-idp-remote'), 'saml');

$discoHandler->handleRequest();
