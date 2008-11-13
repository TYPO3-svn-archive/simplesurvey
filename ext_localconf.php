<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_simplesurvey_pi1 = < plugin.tx_simplesurvey_pi1.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_simplesurvey_pi1.php','_pi1','list_type',1);


t3lib_extMgm::addTypoScript($_EXTKEY,'setup','
	tt_content.shortcut.20.0.conf.tx_simplesurvey_surveys = < plugin.'.t3lib_extMgm::getCN($_EXTKEY).'_pi1
	tt_content.shortcut.20.0.conf.tx_simplesurvey_surveys.CMD = singleView
',43);

t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_simplesurvey_userdata=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_simplesurvey_questions=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_simplesurvey_surveys=1
');
?>