<?php

if(!array_key_exists('header', $this->data)) {
	$this->data['header'] = 'selectidp';
}
$this->data['header'] = $this->t($this->data['header']);

$this->data['autofocus'] = 'preferredidp';

$this->includeAtTemplateBase('includes/header.php');

foreach ($this->data['idplist'] AS $idpentry) {
	if (isset($idpentry['name'])) {
		$this->includeInlineTranslation('idpname_' . $idpentry['entityid'], $idpentry['name']);
	} elseif (isset($idpentry['OrganizationDisplayName'])) {
		$this->includeInlineTranslation('idpname_' . $idpentry['entityid'], $idpentry['OrganizationDisplayName']);
	}
	if (isset($idpentry['description']))
		$this->includeInlineTranslation('idpdesc_' . $idpentry['entityid'], $idpentry['description']);
}


?>


		<h2><?php echo $this->data['header']; ?></h2>

		<form method="get" action="<?php echo $this->data['urlpattern']; ?>">
		<input type="hidden" name="entityID" value="<?php echo htmlspecialchars($this->data['entityID']); ?>" />
		<input type="hidden" name="return" value="<?php echo htmlspecialchars($this->data['return']); ?>" />
		<input type="hidden" name="returnIDParam" value="<?php echo htmlspecialchars($this->data['returnIDParam']); ?>" />
		


		<p><?php echo $this->t('selectidp_full'); ?></p> 

		<?php

		if( !empty($this->data['idplist'])) {
		
			if (!empty($this->data['preferredidp']) && array_key_exists($this->data['preferredidp'], $this->data['idplist'])) {
				$idpentry = $this->data['idplist'][$this->data['preferredidp']];
				echo '<div class="preferredidp">';
				echo '	<img src="/' . $this->data['baseurlpath'] .'resources/icons/experience/gtk-about.64x64.png" class="float-r" alt="'.$this->t('icon_prefered_idp').'" />';

				if(array_key_exists('icon', $idpentry) && $idpentry['icon'] !== NULL) {
					$iconUrl = SimpleSAML_Utilities::resolveURL($idpentry['icon']);
					echo '<img class="float-l" style="margin: 1em; padding: 3px; border: 1px solid #999" src="' . htmlspecialchars($iconUrl) . '" />';
				}
				echo "\n" . '	<h3 style="margin-top: 8px">' . htmlspecialchars($this->t('idpname_' . $idpentry['entityid'])) . '</h3>';

				if (!empty($idpentry['description'])) {
					echo '	<p>' . htmlspecialchars($this->t('idpdesc_' . $idpentry['entityid'])) . '<br />';
				}
				echo('<input id="preferredidp" type="submit" name="idp_' .
					htmlspecialchars($idpentry['entityid']) . '" value="' .
					$this->t('select') . '" /></p>');
				echo '</div>';
			}
		
		
			foreach ($this->data['idplist'] AS $idpentry) {
				if ($idpentry['entityid'] != $this->data['preferredidp']) {

					if(array_key_exists('icon', $idpentry) && $idpentry['icon'] !== NULL) {
						$iconUrl = SimpleSAML_Utilities::resolveURL($idpentry['icon']);
						echo '<img class="float-l" style="clear: both; margin: 1em; padding: 3px; border: 1px solid #999" src="' . htmlspecialchars($iconUrl) . '" />';
					}
					echo "\n" . '	<h3 style="margin-top: 8px">' . htmlspecialchars($this->t('idpname_' . $idpentry['entityid'])) . '</h3>';

					if (!empty($idpentry['description'])) {

						echo '	<p>' . htmlspecialchars($this->t('idpdesc_' . $idpentry['entityid'])) . '<br />';
					}
			
					echo('<input type="submit" name="idp_' .
						htmlspecialchars($idpentry['entityid']) . '" value="' .
						$this->t('select') . '" /></p>');

					if (isset($this->data['displayLoas'])) {
						if (!empty($idpentry['loa'])) {

							echo ' (Loa: ' . $idpentry['loa'] . ')';
						}
					}
				}
			}
			if($this->data['rememberenabled']) {
				echo('<br/><br/><p><input type="checkbox" name="remember" value="1" title="'.$this->t('remember').'" /> ' . $this->t('remember')).'</p>';
			}
		
		}
		else {
			echo $this->t('{accountLinking:multiauth:no_source_available}');
		}

		?>
		</form>
		
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>
