<?php
require_once(t3lib_extMgm::extPath('oelib').'class.tx_oelib_testingFramework.php');
require_once(t3lib_extMgm::extPath('simplesurvey').'pi1/class.tx_simplesurvey_pi1.php');

class tx_simplesurvey_pi1_testcase extends tx_phpunit_testcase{
	private $fixture;
	private $testingFramework;
	private $uuid;
	private $quid;
	private $suid;
	private $auid;
	
	public function setUp(){
		$this->testingFramework = new tx_oelib_testingFramework('tx_simplesurvey');
		
		$this->uuid = $this->testingFramework->createRecord('tx_simplesurvey_userdata', array(
														'mandatory' => 0,
														'title' => 'Name',
														'type' => 0,
														'options' => ''
														));
		
		$this->quid = $this->testingFramework->createRecord('tx_simplesurvey_questions', array(
														'mandatory' => 0,
														'question' => 'Testfrage?',
														'type' => 2,
														'answers' => 'Ja;Nein',
														'showinresult' => 1
														));
		
		$this->suid = $this->testingFramework->createRecord('tx_simplesurvey_surveys', array(
														'title' => 'Testumfrage',
														'caption' => 'test test test',
														'description' => 'desc desc desc',
														'questions' => $quid,
														'dependences' => '',
														'pointssystem' => 0,
														'points' => '',
														'showquestionpoints' => 0,
														'showpointsforuser' => 0,
														'questiontext' => 'Frage #',
														'submittext' => 'Absenden',
														'template' => '',
														'mandatoryfielderror' => 'Fehler',
														'showresults' => 1,
														'showdeletedresults' => 0,
														'receiptmails' => '',
														'todb' => 1,
														'targetpage' => '',
														'userdata' => '',
														'affirmationmail' => 0,
														'mailfield' => 0,
														'mailsubject' => 'Subject',
														'sendersmail' => 'info@stp.de',
														'sendersname' => 'STP AG',
														'mailcontent' => 'Danke! ###CONTENT###',
														'goodbyetext' => 'Bye!'
														));
		
		$this->auid = $this->testingFramework->createRecord('tx_simplesurvey_answers', array(
														'surveyuid' => $suid,
														'questionuid' => $quid,
														'userdatauid' => 0,
														'points' => 0,
														'answer' => 'Ja'
														));
		
		$this->fixture = new tx_simplesurvey_pi1();
	}
	
	public function tearDown(){
		$this->testingFramework->cleanUp();
		unset($this->testingFramework);
		unset($this->fixture);
	}
	
	public function test_setTemplate(){
		$this->assertTrue($this->fixture->setTemplate());
	}
	
	public function test_getPoints(){
		$this->assertEquals(0, $this->fixture->getPoints($this->quid, 'Ja'));
	}
	
	public function test_str_replace_count(){
		$this->assertEquals('Test', $this->fixture->str_replace_count('xx', 'es', 'Txxt', 1));
		$this->assertEquals('Frage 1', $this->fixture->str_replace_count('#', '1', 'Frage #', 1));
		$this->assertEquals('% =)(} \ #abc', $this->fixture->str_replace_count('#', ' ', '%#=)(}#\##abc', 3));
		$this->assertEquals('Frage', $this->fixture->str_replace_count('#', '1', 'Frage', 1));
		$this->assertEquals('Frage#', $this->fixture->str_replace_count('#', '1', 'Frage#', 0));
		$this->assertEquals('Frage#', $this->fixture->str_replace_count('#', '1', 'Frage#', -999));
	}
	
	public function test_str_insert(){
		$this->assertEquals('Test', $this->fixture->str_insert('es', 'Tt', 1));
		$this->assertEquals('esTt', $this->fixture->str_insert('es', 'Tt', 0));
		$this->assertEquals('es', $this->fixture->str_insert('es', '', 2));
		$this->assertEquals('abcdfghe', $this->fixture->str_insert('fgh', 'abcde', -1));
	}
}
?>