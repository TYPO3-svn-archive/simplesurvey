<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array('LLL:EXT:simplesurvey/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','Survey');


if(TYPO3_MODE=="BE"){
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_simplesurvey_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_simplesurvey_pi1_wizicon.php';
	t3lib_extMgm::insertModuleFunction(
        "web_info",        
        "tx_simplesurvey_modfunc1",
        t3lib_extMgm::extPath($_EXTKEY)."modfunc1/class.tx_simplesurvey_modfunc1.php",
        "LLL:EXT:simplesurvey/locallang_db.xml:moduleFunction.tx_simplesurvey_modfunc1"
    );
}

$TCA["tx_simplesurvey_userdata"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'type',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l18n_parent',	
		'transOrigDiffSourceField' => 'l18n_diffsource',	
		'default_sortby' => "ORDER BY title",	
		'delete' => 'deleted',	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_simplesurvey_userdata.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, type, title, mandatory, fieldinformation, options",
	)
);

$TCA["tx_simplesurvey_questions"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions',		
		'label'     => 'question',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'type',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l18n_parent',	
		'transOrigDiffSourceField' => 'l18n_diffsource',	
		'default_sortby' => "ORDER BY question",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_simplesurvey_questions.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, type, question, mandatory, fieldinformation, answers, showinresult",
	)
);

$TCA["tx_simplesurvey_surveys"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'pointssystem',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l18n_parent',	
		'transOrigDiffSourceField' => 'l18n_diffsource',	
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_simplesurvey_surveys.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, title, caption, description, questions, dependences, pointssystem, points, showquestionpoints, showpointsforuser, questiontext, submittext, template, mandatoryfielderror, showresults, showdeletedresults, receiptmails, receiptmailsubject, receiptsendersmail, receiptsendersname, receiptmailcontent, todb, targetpage, goodbyetext, userdata, affirmationmail, mailfield, mailsubject, sendersmail, sendersname, mailcontent",
	)
);

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');

t3lib_extMgm::addLLrefForTCAdescr('tx_simplesurvey_questions','EXT:simplesurvey/csh/locallang_csh_questions.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_simplesurvey_userdata','EXT:simplesurvey/csh/locallang_csh_userdata.xml');
t3lib_extMgm::addLLrefForTCAdescr('tx_simplesurvey_surveys','EXT:simplesurvey/csh/locallang_csh_surveys.xml');
?>