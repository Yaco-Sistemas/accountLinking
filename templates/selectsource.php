<?php
$this->data['header'] = $this->t('{accountLinking:multiauth:select_source_header}');

$this->includeAtTemplateBase('includes/header.php');
?>

<h2><?php echo $this->t('{accountLinking:multiauth:select_source_header}'); ?></h2>

<p><?php echo $this->t('{accountLinking:multiauth:select_source_text}'); ?></p>

<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="get">
<input type="hidden" name="AuthState" value="<?php echo htmlspecialchars($this->data['authstate']); ?>" />
<ul>
<?php

if(!empty($this->data['sources'])) {

	foreach($this->data['sources'] as $source) {
		echo '<li class="' . htmlspecialchars($source['css_class']) . ' authsource">';
		if ($source['source'] === $this->data['preferred']) {
			$autofocus = ' autofocus="autofocus"';
		} else {
			$autofocus = '';
		}
		echo '<button type="submit" name="source"' . $autofocus . ' ' .
			'id="button-' . htmlspecialchars($source['source']) . '" ' .
			'value="' . htmlspecialchars($source['source']) . '">';
		echo htmlspecialchars($this->t($source['text']));
		echo '</button>';
		if (isset($this->data['displayLoas'])) {
			if (isset($source['loa'])) {
				echo ' (Loa: '.$source['loa'].')';
			}
		}
		echo '</li>';
	}
}
else {
	echo $this->t('{accountLinking:multiauth:no_source_available}');
}

?>
</ul>
</form>

<?php $this->includeAtTemplateBase('includes/footer.php'); ?>
