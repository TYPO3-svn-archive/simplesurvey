#
# Table structure for table 'tx_simplesurvey_userdata'
#
CREATE TABLE tx_simplesurvey_userdata (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	is_dummy_record tinyint(1) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	title text NOT NULL,
	mandatory tinyint(3) DEFAULT '0' NOT NULL,
	size tinyint(3) DEFAULT '0' NOT NULL,
	options text NOT NULL,
	fieldinformation text NOT NULL,
	mandatoryrules text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_simplesurvey_questions'
#
CREATE TABLE tx_simplesurvey_questions (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	is_dummy_record tinyint(1) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	question text NOT NULL,
	mandatory tinyint(3) DEFAULT '0' NOT NULL,
	size tinyint(3) DEFAULT '0' NOT NULL,
	answers text NOT NULL,
	fieldinformation text NOT NULL,
	showinresult tinyint(3) DEFAULT '0' NOT NULL,
	mandatoryrules text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_simplesurvey_surveys'
#
CREATE TABLE tx_simplesurvey_surveys (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	is_dummy_record tinyint(1) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	caption text NOT NULL,
	description text NOT NULL,
	questions blob NOT NULL,
	dependences text NOT NULL,
	pointssystem tinyint(3) DEFAULT '0' NOT NULL,
	points text NOT NULL,
	showquestionpoints tinyint(3) DEFAULT '0' NOT NULL,
	showpointsforuser tinyint(3) DEFAULT '0' NOT NULL,
	questiontext tinytext NOT NULL,
	submittext tinytext NOT NULL,
	template blob NOT NULL,
	mandatoryfielderror text NOT NULL,
	showresults tinyint(3) DEFAULT '0' NOT NULL,
	showdeletedresults tinyint(3) DEFAULT '0' NOT NULL,
	receiptmails text NOT NULL,
	receiptmailsubject tinytext NOT NULL,
	receiptsendersmail tinytext NOT NULL,
	receiptsendersname tinytext NOT NULL,
	receiptmailcontent text NOT NULL,
	receiptmailtemplate blob NOT NULL,
	todb tinyint(3) DEFAULT '0' NOT NULL,
	targetpage tinytext NOT NULL,
	goodbyetext text NOT NULL,
	userdata blob NOT NULL,
	affirmationmail tinyint(3) DEFAULT '0' NOT NULL,
	mailfield int(11) DEFAULT '0' NOT NULL,
	mailsubject tinytext NOT NULL,
	sendersmail tinytext NOT NULL,
	sendersname tinytext NOT NULL,
	mailcontent text NOT NULL,
	mailtemplate blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_simplesurvey_answers'
#
CREATE TABLE tx_simplesurvey_answers (
	uid int(11) NOT NULL auto_increment,
	is_dummy_record tinyint(1) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	surveyuid int(11) DEFAULT '0' NOT NULL,
	questionuid int(11) DEFAULT '0' NOT NULL,
	userdatauid int(11) DEFAULT '0' NOT NULL,
	points tinyint(3) DEFAULT '0' NOT NULL,
	answer text NOT NULL,
	
	PRIMARY KEY (uid)
);