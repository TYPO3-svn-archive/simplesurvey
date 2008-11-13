<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_simplesurvey_userdata"] = array (
	"ctrl" => $TCA["tx_simplesurvey_userdata"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,mandatory,title,type,fieldinformation,options"
	),
	"feInterface" => $TCA["tx_simplesurvey_userdata"]["feInterface"],
	"columns" => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_simplesurvey_userdata',
				'foreign_table_where' => 'AND tx_simplesurvey_userdata.pid=###CURRENT_PID### AND tx_simplesurvey_userdata.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		"type" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.type",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.type.I.0", "0"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.type.I.1", "1"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.type.I.2", "2"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.type.I.3", "3"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.type.I.4", "4"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.type.I.5", "5"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.type.I.6", "6"),
				),
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"mandatory" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.mandatory",		
			"config" => Array (
				"type" => "check",
			)
		),
		"size" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.size",		
			"config" => Array (
				"type" => "input",	
				"size" => "2",	
				"eval" => "num",
			)
		),
		"options" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.options",		
			"config" => Array (
				"type" => "text",
				"wrap" => "OFF",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"mandatoryrules" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.mandatoryrules",		
			"config" => Array (
				"type" => "text",
				"wrap" => "OFF",
				"cols" => "30",	
				"rows" => "3",
			)
		),
		"fieldinformation" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_userdata.fieldinformation",		
			"config" => Array (
				"type" => "text",
				"wrap" => "OFF",
				"cols" => "30",	
				"rows" => "5",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, type;;;;2-2-2, title, mandatory, options, fieldinformation"),
		"1" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, type;;;;2-2-2, title, mandatory, options, fieldinformation"),
		"2" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, type;;;;2-2-2, title, mandatory, size, options, mandatoryrules, fieldinformation"),
		"3" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, type;;;;2-2-2, title, mandatory, options, fieldinformation"),
		"4" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, type;;;;2-2-2, title, mandatory, options, mandatoryrules, fieldinformation"),
		"5" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, type;;;;2-2-2, title, options"),
		"6" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, type;;;;2-2-2, title, options, fieldinformation")
	),
	"palettes" => array (
	)
);



$TCA["tx_simplesurvey_questions"] = array (
	"ctrl" => $TCA["tx_simplesurvey_questions"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,mandatory,question,type,fieldinformation,answers,showinresult"
	),
	"feInterface" => $TCA["tx_simplesurvey_questions"]["feInterface"],
	"columns" => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_simplesurvey_questions',
				'foreign_table_where' => 'AND tx_simplesurvey_questions.pid=###CURRENT_PID### AND tx_simplesurvey_questions.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"type" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.type",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.type.I.0", "0"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.type.I.1", "1"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.type.I.2", "2"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.type.I.3", "3"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.type.I.4", "4"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.type.I.5", "5"),
					Array("LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.type.I.6", "6"),
				),
			)
		),
		"question" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.question",		
			"config" => Array (
				"type" => "input",	
				"size" => "48",	
				"eval" => "required",
			)
		),
		"mandatory" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.mandatory",		
			"config" => Array (
				"type" => "check",
			)
		),
		"size" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.size",		
			"config" => Array (
				"type" => "input",	
				"size" => "2",	
				"eval" => "num",
			)
		),
		"answers" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.answers",		
			"config" => Array (
				"type" => "text",
				"wrap" => "OFF",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"mandatoryrules" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.mandatoryrules",		
			"config" => Array (
				"type" => "text",
				"wrap" => "OFF",
				"cols" => "30",	
				"rows" => "3",
			)
		),
		"fieldinformation" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.fieldinformation",		
			"config" => Array (
				"type" => "text",
				"wrap" => "OFF",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"showinresult" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_questions.showinresult",		
			"config" => Array (
				"type" => "check",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden, type;;;;2-2-2, question, mandatory, answers, fieldinformation, showinresult"),
		"1" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden, type;;;;2-2-2, question, mandatory, answers, fieldinformation, showinresult"),
		"2" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden, type;;;;2-2-2, question, mandatory, size, answers, mandatoryrules, fieldinformation, showinresult"),
		"3" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden, type;;;;2-2-2, question, mandatory, answers, fieldinformation, showinresult"),
		"4" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden, type;;;;2-2-2, question, mandatory, answers, mandatoryrules, fieldinformation, showinresult"),
		"5" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden, type;;;;2-2-2, question, answers"),
		"6" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden, type;;;;2-2-2, question, answers, fieldinformation")
	),
	"palettes" => array (
	)
);



$TCA["tx_simplesurvey_surveys"] = array (
	"ctrl" => $TCA["tx_simplesurvey_surveys"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,title,caption,description,questions,dependences,pointssystem,points,showquestionpoints,showpointsforuser,questiontext,submittext,template,mandatoryfielderror,showresults,showdeletedresults,receiptmails,todb,targetpage,receiptmailsubject,receiptsendersmail,receiptsendersname,receiptmailcontent,userdata,affirmationmail,mailfield,mailsubject,sendersmail,sendersname,mailcontent,goodbyetext"
	),
	"feInterface" => $TCA["tx_simplesurvey_surveys"]["feInterface"],
	"columns" => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_simplesurvey_surveys',
				'foreign_table_where' => 'AND tx_simplesurvey_surveys.pid=###CURRENT_PID### AND tx_simplesurvey_surveys.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"caption" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.caption",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"description" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"questions" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.questions",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_simplesurvey_questions",	
				"foreign_table_where" => "AND tx_simplesurvey_questions.pid=###CURRENT_PID### ORDER BY tx_simplesurvey_questions.question",	
				"size" => 10,	
				"minitems" => 0,
				"maxitems" => 100,
			)
		),
		"dependences" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.dependences",		
			"config" => Array (
				"type" => "text",
				"wrap" => "OFF",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"pointssystem" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.pointssystem",		
			"config" => Array (
				"type" => "check",
			)
		),
		"points" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.points",		
			"config" => Array (
				"type" => "text",
				"wrap" => "OFF",
				"cols" => "30",	
				"rows" => "10",
			)
		),
		"showquestionpoints" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.showquestionpoints",		
			"config" => Array (
				"type" => "check",
				"default" => 1,
			)
		),
		"showpointsforuser" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.showpointsforuser",		
			"config" => Array (
				"type" => "check",
				"default" => 1,
			)
		),
		"questiontext" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.questiontext",		
			"config"  => array (
				"type"     => "input",
				"size"     => "30",
				"checkbox" => "false"
			)
		),
		"submittext" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.submittext",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"template" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.template",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "",	
				"disallowed" => "php,php3",	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_simplesurvey",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"mandatoryfielderror" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.mandatoryfielderror",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"showresults" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.showresults",		
			"config" => Array (
				"type" => "check",
				"default" => 1,
			)
		),
		"showdeletedresults" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.showdeletedresults",		
			"config" => Array (
				"type" => "check",
				"default" => 1,
			)
		),
		"receiptmails" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.receiptmails",		
			"config" => Array (
				"type" => "text",
				"wrap" => "OFF",
				"cols" => "30",	
				"rows" => "3",
			)
		),
		"todb" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.todb",		
			"config" => Array (
				"type" => "check",
				"default" => 1,
			)
		),
		"targetpage" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.targetpage",		
			"config" => Array (
				"type"     => "input",
				"size"     => "15",
				"max"      => "255",
				"checkbox" => "",
				"eval"     => "trim",
				"wizards"  => array(
					"_PADDING" => 2,
					"link"     => array(
						"type"         => "popup",
						"title"        => "Link",
						"icon"         => "link_popup.gif",
						"script"       => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					)
				)
			)
		),
		"receiptmailsubject" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.receiptmailsubject",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"receiptsendersmail" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.receiptsendersmail",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"receiptsendersname" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.receiptsendersname",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"receiptmailcontent" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.receiptmailcontent",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"receiptmailtemplate" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.receiptmailtemplate",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "htm,html",	
				"disallowed" => "",	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_simplesurvey",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"userdata" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.userdata",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_simplesurvey_userdata",	
				"foreign_table_where" => "ORDER BY tx_simplesurvey_userdata.title",	
				"size" => 5,	
				"minitems" => 0,
				"maxitems" => 10,
			)
		),
		"affirmationmail" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.affirmationmail",		
			"config" => Array (
				"type" => "check",
			)
		),
		"mailfield" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.mailfield",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_simplesurvey_userdata",	
				"foreign_table_where" => "ORDER BY tx_simplesurvey_userdata.uid",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"mailsubject" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.mailsubject",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"sendersmail" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.sendersmail",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"sendersname" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.sendersname",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"mailcontent" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.mailcontent",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"mailtemplate" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.mailtemplate",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "htm,html",	
				"disallowed" => "",	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_simplesurvey",
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"goodbyetext" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:simplesurvey/locallang_db.xml:tx_simplesurvey_surveys.goodbyetext",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden, starttime, endtime, title;;;;2-2-2, caption, description;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], questions;;;;3-3-3, dependences, pointssystem, questiontext;;;;4-4-4, submittext, template, mandatoryfielderror, showresults, showdeletedresults, receiptmails;;;;5-5-5, receiptmailsubject, receiptsendersmail, receiptsendersname, receiptmailtemplate, receiptmailcontent;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts];;;;6-6-6, todb, targetpage, goodbyetext;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], userdata;;;;7-7-7, affirmationmail, mailfield, mailsubject, sendersmail, sendersname, mailtemplate, mailcontent;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]"),
		"1" => array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden, starttime, endtime, title;;;;2-2-2, caption, description;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], questions;;;;3-3-3, dependences, pointssystem, points, showquestionpoints, showpointsforuser, questiontext;;;;4-4-4, submittext, template, mandatoryfielderror, showresults, showdeletedresults, receiptmails;;;;5-5-5, receiptmailsubject, receiptsendersmail, receiptsendersname, receiptmailtemplate, receiptmailcontent;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts];;;;6-6-6, todb, targetpage, goodbyetext;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], userdata;;;;7-7-7, affirmationmail, mailfield, mailsubject, sendersmail, sendersname, mailtemplate, mailcontent;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]")
	),
	"palettes" => array (
	)
);
?>