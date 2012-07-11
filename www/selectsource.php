<?php

/**
 * This page shows a list of authentication sources. When the user selects
 * one of them if pass this information to the
 * sspmod_multiauth_Auth_Source_MultiAuth class and call the
 * delegateAuthentication method on it.
 *
 * @author Sixto Martin, Yaco Sistemas S.L.
 * @author Lorenzo Gil, Yaco Sistemas S.L.
 * @package simpleSAMLphp
 * @version $Id$
 */

if (!array_key_exists('AuthState', $_REQUEST)) {
	throw new SimpleSAML_Error_BadRequest('Missing AuthState parameter.');
}
$authStateId = $_REQUEST['AuthState'];


$loaConfiguration = SimpleSAML_Configuration::getConfig('module_accountLinking.php');
$displayLoas =  $loaConfiguration->getBoolean('display-Loas', false);

/* Retrieve the authentication state. */
$state = SimpleSAML_Auth_State::loadState($authStateId, sspmod_accountLinking_Auth_Source_MultiAuth::STAGEID);

if (array_key_exists("SimpleSAML_Auth_Default.id", $state)) {
	$authId = $state["SimpleSAML_Auth_Default.id"];
	$as = SimpleSAML_Auth_Source::getById($authId);
} else {
	$as = NULL;
}

if (array_key_exists('source', $_REQUEST)) {
	$source = $_REQUEST['source'];
	if ($as !== NULL) {
		$as->setPreviousSource($source);
	}
	sspmod_accountLinking_Auth_Source_MultiAuth::delegateAuthentication($source, $state);
} elseif (array_key_exists('multiauth:preselect', $state)) {
	$source = $state['multiauth:preselect'];
	sspmod_accountLinking_Auth_Source_MultiAuth::delegateAuthentication($source, $state);
}

$globalConfig = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($globalConfig, 'accountLinking:selectsource.php');
$t->data['authstate'] = $authStateId;
$t->data['sources'] = $state[sspmod_accountLinking_Auth_Source_MultiAuth::SOURCESID];
if ($as !== NULL) {
	$t->data['preferred'] = $as->getPreviousSource();
} else {
	$t->data['preferred'] = NULL;
}

if ($displayLoas) {
	$t->data['displayLoas'] = true;
}

$t->show();
exit();

?>
