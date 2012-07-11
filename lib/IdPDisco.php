<?php

/**
 * This class implements a generic IdP discovery service, that will filter IdPs
 * that no complains the required LoA.
 *
 * This module extends the basic IdP disco getIdPList and the handler (to show LoAs)
 *
 * @author Sixto Martin, Yaco Sistemas S.L.
 * @package simpleSAMLphp
 * @version $Id$
 */
class sspmod_accountLinking_IdPDisco extends SimpleSAML_XHTML_IdPDisco {

	/**
	 * Retrieve the list of IdPs which are stored in the metadata and complains the LoA.
	 *
	 * @return array  Array with entityid=>metadata mappings.
	 */
	protected function getIdPList() {

		$loaConfiguration = SimpleSAML_Configuration::getConfig('module_accountLinking.php');
		$loas = $loaConfiguration->getArray('LoAs');
		$defaultLoas = $loaConfiguration->getArray('default-LoAs');		

		$idpList = array();
		foreach ($this->metadataSets AS $metadataSet) {
			$newList = $this->metadata->getList($metadataSet);
			/*
			 * Note that we merge the entities in reverse order. This ensuers
			 * that it is the entity in the first metadata set that "wins" if
			 * two metadata sets have the same entity.
			 */
			$idpList = array_merge($newList, $idpList);
		}


		// Calculate the original spEntityid
		
		$url = urldecode($_REQUEST['return']);
		$data_url = parse_url($url);
		$query = $data_url['query'];

		$split1 = explode('spentityid=', $query);
		$spUrl = urldecode($split1[1]);
		$split2 = explode('&cookieTime', $spUrl);
		$spEntityid = $split2[0];

		$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

		$spMetadataList = $metadata->getList('saml20-sp-remote');

		if (!array_key_exists($spEntityid, $spMetadataList)) {
			// If a valid spEntityid was not found,  I use the local SP
			$spEntityid = $this->spEntityId;
		}

		if (isset($loas['sps']) && array_key_exists($spEntityid , $loas['sps'])) {
			$requiredLoa = $loas['sps'][$spEntityid];
		}
		else if (isset($defaultLoas['sp'])) {
			$requiredLoa = $defaultLoas['sp'];
		}

		foreach ($idpList as $key => &$idp) {
			$loa = NULL;
			if (isset($loas['idps']) && array_key_exists($key, $loas['idps'])) {
				$loa = $loas['idps'][$key];
			}
			else if (isset($defaultLoas['idp'])) {
				$loa = $defaultLoas['idp'];
			}
			else {
				break;
			}
			$idp['loa'] = $loa;
		}

		if (isset($requiredLoa)) {
			foreach ($idpList as $key => &$idp) {
				if (!isset($idp['loa']) || ($idp['loa'] < $requiredLoa)) {
					unset($idpList[$key]);
				}
			}
		}

		return $idpList;
	}

	public function handleRequest() {

		$idp = $this->getTargetIdp();
		if($idp !== NULL) {
		
			$extDiscoveryStorage = $this->config->getString('idpdisco.extDiscoveryStorage', NULL);
			if ($extDiscoveryStorage !== NULL) {
				$this->log('Choice made [' . $idp . '] (Forwarding to external discovery storage)');
				SimpleSAML_Utilities::redirect($extDiscoveryStorage, array(
//					$this->returnIdParam => $idp,
					'entityID' => $this->spEntityId,
					'IdPentityID' => $idp,
					'returnIDParam' => $this->returnIdParam,
					'isPassive' => 'true',
					'return' => $this->returnURL
				));
				
			} else {
				$this->log('Choice made [' . $idp . '] (Redirecting the user back. returnIDParam=' . $this->returnIdParam . ')');
				SimpleSAML_Utilities::redirect($this->returnURL, array($this->returnIdParam => $idp));
			}
			
			return;
		}
		
		if ($this->isPassive) {
			$this->log('Choice not made. (Redirecting the user back without answer)');
			SimpleSAML_Utilities::redirect($this->returnURL);
			return;
		}

		/* No choice made. Show discovery service page. */

		$idpList = $this->getIdPList();
		$preferredIdP = $this->getRecommendedIdP();

		$idpintersection = array_intersect(array_keys($idpList), $this->getScopedIDPList());
		if (sizeof($idpintersection) > 0) {
			$idpList = array_intersect_key($idpList, array_fill_keys($idpintersection, NULL));
		}

        $idpintersection = array_values($idpintersection); 
        
        if(sizeof($idpintersection)  == 1) {
            $this->log('Choice made [' . $idpintersection[0] . '] (Redirecting the user back. returnIDParam=' . $this->returnIdParam . ')');
            SimpleSAML_Utilities::redirect($this->returnURL, array($this->returnIdParam => $idpintersection[0]));
        }

        $templateFile = 'accountLinking:';

		/*
		 * Make use of an XHTML template to present the select IdP choice to the user.
		 * Currently the supported options is either a drop down menu or a list view.
		 */
		switch($this->config->getString('idpdisco.layout', 'links')) {
		case 'dropdown':
			$templateFile .= 'selectidp-dropdown.php';
			break;
		case 'links':
			$templateFile .= 'selectidp-links.php';
			break;
		default:
			throw new Exception('Invalid value for the \'idpdisco.layout\' option.');
		}

		$t = new SimpleSAML_XHTML_Template($this->config, $templateFile, 'disco');
		$t->data['idplist'] = $idpList;
		$t->data['preferredidp'] = $preferredIdP;
		$t->data['return'] = $this->returnURL;
		$t->data['returnIDParam'] = $this->returnIdParam;
		$t->data['entityID'] = $this->spEntityId;
		$t->data['urlpattern'] = htmlspecialchars(SimpleSAML_Utilities::selfURLNoQuery());
		$t->data['rememberenabled'] = $this->config->getBoolean('idpdisco.enableremember', FALSE);

		$loaConfiguration = SimpleSAML_Configuration::getConfig('module_accountLinking.php');
		$displayLoas =  $loaConfiguration->getBoolean('displayLoas', false);

		if ($displayLoas) {
			$t->data['displayLoas'] = true;
		}

		$t->show();
	}

}
