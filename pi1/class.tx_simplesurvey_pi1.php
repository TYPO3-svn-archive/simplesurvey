<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Michael Kuzmin <michael.kuzmin@stp-online.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Survey' for the 'simplesurvey' extension.
 *
 * @author	Michael Kuzmin <michael.kuzmin@stp-online.de>
 * @package	TYPO3
 * @subpackage	tx_simplesurvey
 */
class tx_simplesurvey_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_simplesurvey_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_simplesurvey_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'simplesurvey';	// The extension key.
	var $pi_checkCHash = true;
	var $uploaddir     = 'uploads/tx_simplesurvey/'; 	// Path to the upload dir
	var $content       = '';	// Content of the survey
	var $mandatoryArr  = array();
	var $dependencesArr= array();
	var $pointsArr     = array();
	
	/**
	 * Main method of your PlugIn
	 *
	 * @param	string		$content: The content of the PlugIn
	 * @param	array		$conf: The PlugIn Configuration
	 * @return	The content that should be displayed on the website
	 */
	function main($content,$conf)	{
		$this->pi_initPIflexForm();
		switch((string)$conf['CMD'])	{
			case 'singleView':
				list($t) = explode(':',$this->cObj->currentRecord);
				$this->internal['currentTable']=$t;
				$this->internal['currentRow']=$this->cObj->data;
				return $this->pi_wrapInBaseClass($this->singleView($content,$conf));
			break;
			default:
				if (strstr($this->cObj->currentRecord,'tt_content'))	{
					if($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'pages', 'prop') != ''){ // if no new path is choosen the old one will be used!
						$this->cObj->data['pages'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'pages', 'prop');
						$this->cObj->data['recursive'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'recursive', 'prop');
					}
					$conf['pidList'] = $this->cObj->data['pages'];
					$conf['recursive'] = $this->cObj->data['recursive'];
				}
				return $this->pi_wrapInBaseClass($this->listView($content,$conf));
			break;
		}
	}
	
	/**
	 * Shows a list of database entries
	 *
	 * @param	string		$content: content of the PlugIn
	 * @param	array		$conf: PlugIn Configuration
	 * @return	HTML list of table entries
	 */
	function listView($content,$conf)	{
		$this->conf=$conf;		// Setting the TypoScript passed to this function in $this->conf
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values
		$this->pi_initPIflexForm();
		
		$lConf = $this->conf['listView.'];	// Local settings for the listView function
	
		if($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singlesurvey', 'prop') != ''){ // Set single survey
			$this->piVars['showUid'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singlesurvey', 'prop');
		}

		if ($this->piVars['showUid'])	{	// If a single element should be displayed:
			$this->internal['currentTable'] = 'tx_simplesurvey_surveys';
			$this->internal['currentRow'] = $this->pi_getRecord('tx_simplesurvey_surveys',$this->piVars['showUid']);
	
			$content = $this->singleView($content,$conf);
			return $content;
		}else if ($this->piVars['showResult'])	{	// If a result should be displayed:
			$this->internal['currentTable'] = 'tx_simplesurvey_surveys';
			$this->internal['currentRow'] = $this->pi_getRecord('tx_simplesurvey_surveys',$this->piVars['showResult']);
			
			if($this->getFieldContent('showresults') == 1){
				$content = $this->resultView($content,$conf);
			}else{
				$content = '<div'.$this->pi_classParam('results-not-allowed').'>'.$this->pi_getLL('resultsNotAllowed','Die Ergebnisanzeige wurde für diese Umfrage deaktiviert!').'</div>';
			}
			return $content;
		} else {
			if (!isset($this->piVars['pointer']))	$this->piVars['pointer']=0;
	
				// Initializing the query parameters:
			list($this->internal['orderBy'],$this->internal['descFlag']) = explode(':',$this->piVars['sort']);
			$this->internal['results_at_a_time']=t3lib_div::intInRange($lConf['results_at_a_time'],0,1000,isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_simplesurvey_pi1.']['results_at_a_time'])?($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_simplesurvey_pi1.']['results_at_a_time']):20);		// Number of results to show in a listing.
			$this->internal['maxPages']=t3lib_div::intInRange($lConf['maxPages'],0,1000,isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_simplesurvey_pi1.']['maxPages'])?($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_simplesurvey_pi1.']['maxPages']):5);		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
			$this->internal['orderByList']='uid,title,questions';
	
				// Get number of records:
			$res = $this->pi_exec_query('tx_simplesurvey_surveys',1);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
	
				// Make listing query, pass query to SQL database:
			$res = $this->pi_exec_query('tx_simplesurvey_surveys');
			$this->internal['currentTable'] = 'tx_simplesurvey_surveys';
	
				// Put the whole list together:
			$fullTable='';	// Clear var;
		#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!
	
				// Adds the whole list table
			$fullTable.=$this->pi_list_makelist($res);
	
				// Adds the result browser:
			$fullTable.=$this->pi_list_browseresults();
	
				// Returns the content from the plugin.
			return $fullTable;
		}

	}
	/**
	 * Display a single item from the database
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	HTML of a single database entry
	 */
	function singleView($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->createPointsArr();
		$this->content = '<div'.$this->pi_classParam('notemplate').'>'.$this->pi_getLL('noTemplate','Template nicht gefunden... Bitte kontaktieren Sie den Administrator!').'</div>';
		
			// This sets the title of the page for use in indexed search results:
		if ($this->internal['currentRow']['title'])	$GLOBALS['TSFE']->indexedDocTitle=$this->internal['currentRow']['title'];
		
		if($this->piVars['finishedsurvey'] == 1){// if survey already sent
			if(isset($_FILES) && !$this->array_empty($_FILES)){ // file upload
				$uploaderror = array();
				foreach($_FILES['tx_simplesurvey_pi1']['name'] as $uid => $file){
					if($_FILES['tx_simplesurvey_pi1']['error'][$uid] == 1 || $_FILES['tx_simplesurvey_pi1']['error'][$uid] == 2){
						$uploaderror[$file] .= 'size+';
					}
					if($_FILES['tx_simplesurvey_pi1']['error'][$uid] == 3){
						$uploaderror[$file] .= 'error+';
					}
					if($_FILES['tx_simplesurvey_pi1']['error'][$uid] == 0){
						if(substr($uid, 0, 8) == 'question'){
							$questionuid = substr($uid, 8);
							$userdatauid = 0;
							$questionArr = $this->pi_getRecord('tx_simplesurvey_questions', $questionuid);
							$answerListArr = explode("\r\n", $questionArr['answers']);
							$length = count($answerListArr);
						}else if(substr($uid, 0, 8) == 'userdata'){
							$userdatauid = substr($uid, 8);
							$questionuid = 0;
							$questionArr = $this->pi_getRecord('tx_simplesurvey_userdata', $userdatauid);
							$answerListArr = explode("\r\n", $questionArr['options']);
							$length = count($answerListArr);
						}else{
							break;
						}
						for($i=0; $i<$length; $i++){
							if(strpos($answerListArr[$i], '@') === 0){
								$maxlength = substr($answerListArr[$i], 1);
							}else{
								$accept .= $answerListArr[$i].',';
							}
						}
						$accept = substr($accept, 0, -1);
						
						if(strpos($accept, $_FILES['tx_simplesurvey_pi1']['type'][$uid]) !== false){
							if($_FILES['tx_simplesurvey_pi1']['size'][$uid] < $maxlength || !isset($maxlength)){
								$file = utf8_decode($this->uploaddir.$this->getMD5($_FILES['tx_simplesurvey_pi1']['tmp_name'][$uid]).'/'.date("Y-m-d H-i-s",time()).' '.$file);
								if(!file_exists(t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/'.$this->uploaddir.$this->getMD5($_FILES['tx_simplesurvey_pi1']['tmp_name'][$uid]).'/index.php')){
									mkdir(t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/'.$this->uploaddir.$this->getMD5($_FILES['tx_simplesurvey_pi1']['tmp_name'][$uid]));
									$indexfile = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/'.$this->uploaddir.$this->getMD5($_FILES['tx_simplesurvey_pi1']['tmp_name'][$uid]).'/index.php';
									$fh = fopen($indexfile, 'w');
									$stringData =  '<?php
echo \'<html>
<head>
<meta http-equiv="Refresh" content="0; URL=http://\'.$_SERVER[\'SERVER_NAME\'].\'">
</head>
<body>
</body>
</html>\';
?>';
									fwrite($fh, $stringData);
									fclose($fh);
								}
								if($this->array_empty($uploaderror) && !copy($_FILES['tx_simplesurvey_pi1']['tmp_name'][$uid], t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/'.$file)){
									$uploaderror[$file] .= 'uploaderror+';
								}else{
									if(!isset($files)){
										$files = array();
										$files[0] = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/'.$file;
									}else{
										$files[count($files)] = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/'.$file;
									}
									if($this->getFieldContent('todb') == 1){
										$crdate = time();
										$surveyuid = $this->getFieldContent('uid');
										
										$insertArr = array( "crdate" => $crdate,
															"surveyuid" => $surveyuid,
															"questionuid" => $questionuid,
															"userdatauid" => $userdatauid,
															"answer" => $file );
										$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_simplesurvey_answers', $insertArr);
									}
								}
							}else{
								$uploaderror[$file] .= 'size+';
							}
						}else{
							$uploaderror[$file] .= 'invalid+';
						}
					}
				}
				if(!$this->array_empty($uploaderror)){
					foreach($uploaderror as $f => $error){
						$temperror .= $f.'+'.$error;
					}
					$errors = substr($temperror, 0, -1);
					
					if($this->setTemplate()){
						$this->createDependencesArr();
						$this->extractSubpart('questions');
						$this->extractSubpart('answers', 'questions');
						$this->replaceMarker($errors);
						$this->replaceUserdata();
						$this->createForm();
						$this->content .= $this->createMandatoryScript();
						$this->content .= $this->createDependencesScript();
						return $this->content;
					}else{
						header('Location: '.t3lib_div::linkThisScript(array('uploaderror'=>$errors)));
					}
				}
			}
			
			if($this->getFieldContent('todb') == 1){
				$crdate = time();
				$surveyuid = $this->getFieldContent('uid');
				
				foreach($this->piVars as $name => $content){
						if(substr($name, 0, 8) == 'question'){
							$points = 0;
							$questionuid = substr($name, 8);

							if(is_array($content)){
								$checkbox = '';
								foreach($content as $value){
									if($this->getFieldContent('pointssystem') == 1){
										$points += $this->getPoints($questionuid, $value);
									}
									$checkbox .= $value.',';
								}
								$content = substr($checkbox, 0, -1);
							}else if($this->getFieldContent('pointssystem') == 1){
								$points = $this->getPoints($questionuid, $content);
							}
							
							$insertArr = array( "crdate" => $crdate,
												"surveyuid" => $surveyuid,
												"questionuid" => intval($questionuid),
												"points" => $points,
												"answer" => $GLOBALS['TYPO3_DB']->fullQuoteStr($content, 'tx_simplesurvey_answers') );
							$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_simplesurvey_answers', $insertArr);
						}else if(substr($name, 0, 8) == 'userdata'){
							if(is_array($content)){
								$checkbox = '';
								foreach($content as $value){
									$checkbox .= $value.',';
								}
								$content = substr($checkbox, 0, -1);
							}
							$userdatauid = substr($name, 8);
							$insertArr = array( "crdate" => $crdate,
												"surveyuid" => $surveyuid,
												"userdatauid" => intval($userdatauid),
												"answer" => $GLOBALS['TYPO3_DB']->fullQuoteStr($content, 'tx_simplesurvey_answers') );
							$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_simplesurvey_answers', $insertArr);
						}
				}
			}
			
			$appendfiles = '';
			if(isset($_FILES) && !$this->array_empty($_FILES) && isset($files) && !$this->array_empty($files)){
				$appendfiles = '<b>'.$this->pi_getLL('receivedfiles','Empfangene Dateien:').'</b>';
				foreach($_FILES['tx_simplesurvey_pi1']['name'] as $uid => $curFile){
					$appendfiles .= '<br/>'.utf8_decode($curFile);
				}
			}
			$survey = $appendfiles.$this->getResult();
			$surveywithpoints = $appendfiles.$this->getResult(true);
			
			if($this->getFieldContent('receiptmails') != ''){
				if($this->getFieldContent('receiptmailtemplate') != ''){
					$receiptmailtemplate = $this->uploaddir.$this->getFieldContent('receiptmailtemplate');
					if(file_exists($receiptmailtemplate)){
						$fh = fopen($receiptmailtemplate, 'r');
						$mailcontent = fread($fh, filesize($receiptmailtemplate));
						fclose($fh);
					}else{
						$html_start='<html><head><title>'.$this->pi_getLL('survey','Umfrage').'</title></head><body>';
						$html_end='</body></html>';
						$mailcontent = $html_start.$this->pi_RTEcssText($this->getFieldContent('receiptmailcontent')).$html_end;
					}
				}else{
					$html_start='<html><head><title>'.$this->pi_getLL('survey','Umfrage').'</title></head><body>';
					$html_end='</body></html>';
					$mailcontent = $html_start.$this->pi_RTEcssText($this->getFieldContent('receiptmailcontent')).$html_end;
				}
				$mailcontent = str_replace('###CONTENT###', ($this->getFieldContent('pointssystem')?$surveywithpoints:$survey), $mailcontent);
				$subject = $this->getFieldContent('receiptmailsubject');
				$frommail = $this->getFieldContent('receiptsendersmail');
				$mailfrom = $this->getFieldContent('receiptsendersname');
				$recipients = explode("\r\n",$this->getFieldContent('receiptmails'));
				foreach($recipients as $mailaddress){
					if(t3lib_div::validEmail($mailaddress)){
						$this->sendMail($mailaddress, $mailcontent, $subject, $frommail, $mailfrom, $files);
					}
				}
			}
			
			if($this->getFieldContent('affirmationmail') == 1){
				$mailaddress = $this->piVars['userdata'.$this->getFieldContent('mailfield')];
				if(t3lib_div::validEmail($mailaddress)){
					if($this->getFieldContent('mailtemplate') != ''){
						$mailtemplate = $this->uploaddir.$this->getFieldContent('mailtemplate');
						if(file_exists($mailtemplate)){
							$fh = fopen($mailtemplate, 'r');
							$mailcontent = fread($fh, filesize($mailtemplate));
							fclose($fh);
						}else{
							$html_start='<html><head><title>'.$this->pi_getLL('survey','Umfrage').'</title></head><body>';
							$html_end='</body></html>';
							$mailcontent = $html_start.$this->pi_RTEcssText($this->getFieldContent('mailcontent')).$html_end;
						}
					}else{
						$html_start='<html><head><title>'.$this->pi_getLL('survey','Umfrage').'</title></head><body>';
						$html_end='</body></html>';
						$mailcontent = $html_start.$this->pi_RTEcssText($this->getFieldContent('mailcontent')).$html_end;
					}
					$mailcontent = str_replace('###CONTENT###', ($this->getFieldContent('pointssystem') && $this->getFieldContent('showpointsforuser') ? $surveywithpoints : $survey), $mailcontent);
					$subject = $this->getFieldContent('mailsubject');
					$frommail = $this->getFieldContent('sendersmail');
					$mailfrom = $this->getFieldContent('sendersname');
					$this->sendMail($mailaddress, $mailcontent, $subject, $frommail, $mailfrom, $files);
				}
			}
			
			if(is_numeric($this->getFieldContent('targetpage'))){
				header('Location: http://'. $_SERVER['SERVER_NAME'] .'/?id='.$this->getFieldContent('targetpage'));
			}else if($this->getFieldContent('targetpage') != ''){
				if(substr($this->getFieldContent('targetpage'), 0, 7) == 'http://'){
					header('Location: '.$this->getFieldContent('targetpage'));
				}else{
					header('Location: http://'.$this->getFieldContent('targetpage'));
				}
			}
			
			return str_replace('###CONTENT###', ($this->getFieldContent('pointssystem') && $this->getFieldContent('showpointsforuser') ? $surveywithpoints : $survey), $this->pi_RTEcssText($this->getFieldContent('goodbyetext')));
			
		}else{
			if($this->setTemplate()){ // if valid template found
				$this->createDependencesArr();
				$this->extractSubpart('questions');
				$this->extractSubpart('answers', 'questions');
				$this->replaceMarker();
				$this->replaceUserdata();
				$this->createForm();
				$this->content .= $this->createMandatoryScript();
				$this->content .= $this->createDependencesScript();
			}
			return $this->content;
		}
	}
	/**
	 * Displays a result page
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	HTML of a result
	 */
	function resultView($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->createPointsArr();
		
			// This sets the title of the page for use in indexed search results:
		if ($this->internal['currentRow']['title'])	$GLOBALS['TSFE']->indexedDocTitle=$this->internal['currentRow']['title'];
		
		$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('questions','tx_simplesurvey_surveys','uid='.intval($this->piVars['showResult']),'','','');
		while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)){
			if($row2['questions'] != ''){
				$questNums = explode(',',$row2['questions']);
				$idlist = $row2['questions'];
			}
		}
		
		if($this->getFieldContent('showdeletedresults') == 0){
			$where = ' AND questionuid IN ('.$idlist.')';
		}
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_simplesurvey_answers','surveyuid='.intval($this->piVars['showResult']).$where,'','','');
		$this->internal['res_count'] = mysql_num_rows($res);
		$this->internal['currentTable'] = 'tx_simplesurvey_answers';
		
		$contentArr = array();
		if(is_array($questNums)){
			foreach($questNums as $id => $value){
				$contentArr[$value] = array();
			}
		}
		$qi = 0;
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			if($row['questionuid'] != 0){
				$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('question,showinresult','tx_simplesurvey_questions','uid='.intval($row['questionuid']),'','','');
				while($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)){
					$question = $row1['question'];
					$showinresult = $row1['showinresult'];
				}
				if($showinresult){
					$contentArr[$row['questionuid']][$question][$qi++] = $row['answer'];
				}
			}
		}
		
		$result = $this->pi_getLL('noAnswer','Noch keine Antwort eingetragen!');
		if(!$this->array_empty($contentArr)){
			$resultArr = array();
			$totalanswersArr = array();
			$result = '<div'.$this->pi_classParam('result-header-questions').'>'.$this->pi_getLL('listFieldHeader_questions','Fragen').'</div>';
			foreach($contentArr as $uid => $subArr1){
				foreach($subArr1 as $question => $subArr2){
					$totalanswers = 0;
					$isempty = true;
					foreach($subArr2 as $uid2 => $answer){
						if($answer != ''){
							$resultArr[$question][$answer]++;
							$totalanswers++;
							$isempty = false;
						}
					}
					if($isempty){
						$resultArr[$question][$this->pi_getLL('noAnswer','Noch keine Antwort eingetragen!')] = 0;
					}
					$totalanswersArr[$question] = $totalanswers;
				}
			}
			foreach($resultArr as $question => $subArr){
				$result .= '<p><div'.$this->pi_classParam('result-question').'>'.$question.' ('.$totalanswersArr[$question].')</div>';
				foreach($subArr as $answer => $count){
					if($count > 0){
						$result .= '<br/><div'.$this->pi_classParam('result-percent').' style="display:inline;">'.round(($count/$totalanswersArr[$question])*100, 1).'% </div><div'.$this->pi_classParam('result-answer').' style="display:inline;">"'.$answer.'" ('.$count.')</div>';
					}else{
						$result .= '<br/><div'.$this->pi_classParam('result-noanswer').'>'.$answer.'</div>';
					}
				}
				$result .= '</p>';
			}
		}
		
		return $result;
	}
	/**
	 * Returns a single table row for list view
	 *
	 * @param	integer		$c: Counter for odd / even behavior
	 * @return	A HTML table row
	 */
	function pi_list_row($c)	{
		$editPanel = $this->pi_getEditPanel();
		if ($editPanel)	$editPanel='<TD>'.$editPanel.'</TD>';
	
		$list_row = '<tr'.($c%2 ? $this->pi_classParam('listrow-odd') : '').'>
				<td valign="top"><p>'.$this->getFieldContent('title').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('caption').'</p></td>
				<td valign="top"><p>'.$this->getFieldContent('questions').'</p></td>';
				if($this->getFieldContent('showresults') == 1){ $list_row .='<td valign="top"><p>'.$this->getFieldContent('results').'</p></td>'; }
				else{ $list_row .='<td valign="top"><p>'.$this->pi_getLL('noResult','Nicht verfügbar!').'</p></td>'; }
			$list_row .= '</tr>';
		return $list_row;
	}
	/**
	 * Returns a table row with column names of the table
	 *
	 * @return	A HTML table row
	 */
	function pi_list_header()	{
		return '<tr'.$this->pi_classParam('listrow-header').'>
				<td><p>'.$this->getFieldHeader_sortLink('title').'</p></td>
				<td><p>'.$this->getFieldHeader('caption').'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink('questions').'</p></td>
				<td><p>'.$this->getFieldHeader('results').'</p></td>
			</tr>';
	}
	/**
	 * Returns the content of a given field
	 *
	 * @param	string		$fN: name of table field
	 * @return	Value of the field
	 */
	function getFieldContent($fN)	{
		switch($fN) {
			//case 'uid':
			//	return $this->pi_list_linkSingle($this->internal['currentRow'][$fN],$this->internal['currentRow']['uid'],1);	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
			//break;
			case "title":
					// This will wrap the title in a link.
				if(!isset($this->piVars['showUid'])){
					return $this->pi_list_linkSingle($this->internal['currentRow']['title'],$this->internal['currentRow']['uid'],1);
				}else{
					return $this->internal['currentRow'][$fN];
				}
			break;
			case "questions":
				if(!isset($this->piVars['showUid'])){
					return count(explode(',',$this->internal['currentRow'][$fN]));
				}else{
					return $this->internal['currentRow'][$fN];
				}
			break;
			case "results":
				if(!isset($this->piVars['showUid'])){
					$arguments = array($this->prefixId.'[showResult]'=>$this->internal['currentRow']['uid']);
					return $this->pi_linkTP($this->pi_getLL('showResult','Ergebnis anzeigen'), $arguments);
				}
			break;
			default:
				return $this->internal['currentRow'][$fN];
			break;
		}
	}
	/**
	 * Returns the label for a fieldname from local language array
	 *
	 * @param	[type]		$fN: ...
	 * @return	[type]		...
	 */
	function getFieldHeader($fN)	{
		switch($fN) {
			case "title":
				return $this->pi_getLL('listFieldHeader_title','<em>title</em>');
			break;
			case "results":
				return $this->pi_getLL('listFieldHeader_results','<em>results</em>');
			break;
			default:
				return $this->pi_getLL('listFieldHeader_'.$fN,'['.$fN.']');
			break;
		}
	}
	
	/**
	 * Returns a sorting link for a column header
	 *
	 * @param	string		$fN: Fieldname
	 * @return	The fieldlabel wrapped in link that contains sorting vars
	 */
	function getFieldHeader_sortLink($fN)	{
		return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fN),array('sort'=>$fN.':'.($this->internal['descFlag']?0:1)));
	}
	
	/**
	  * Sets the array which holds the informations about the dependences
	  */
	function createDependencesArr(){
		$listArr = explode("\r\n", $this->getFieldContent('dependences'));
		$length = count($listArr);
		for($i=0; $i<$length; $i++){
			$pos1 = strpos($listArr[$i], '>');
			$pos2 = strpos($listArr[$i], '=');
			if($pos1 !== false && $pos2 !== false && $pos1 < $pos2){
				$question1 = trim(substr($listArr[$i], $pos1+1, ($pos2-$pos1-1)));
				$question2 = trim(substr($listArr[$i], 0, $pos1));
				$dependence = trim(substr($listArr[$i], $pos2+1));
				if($this->dependencesArr[$question1][$question2] != ''){
					$this->dependencesArr[$question1][$question2] .= ';'.$dependence;
				}else{
					$this->dependencesArr[$question1][$question2] = $dependence;
				}
			}
		}
	}
	
	/**
	  * Sets the array which holds the informations about the points
	  */
	function createPointsArr(){
		if($this->getFieldContent('pointssystem')){
			$listArr = explode("\r\n", $this->getFieldContent('points'));
			$length = count($listArr);
			for($i=0; $i<$length; $i++){
				$pos1 = strpos($listArr[$i], '=');
				$pos2 = strpos($listArr[$i], '>');
				if($pos1 !== false && $pos2 !== false && $pos1 < $pos2){
					$question = substr(trim(substr($listArr[$i], 0, $pos1)), 8);
					$questionArr = $this->pi_getRecord('tx_simplesurvey_questions', $question);
					$question = $questionArr['question'];
					$answers = trim(substr($listArr[$i], $pos1+1, ($pos2-$pos1-1)));
					$points = trim(substr($listArr[$i], $pos2+1));
					$answerArr = explode(';', $answers);
					if(!isset($this->pointsArr[$question]['min'])){
						$this->pointsArr[$question]['min'] = $points;
					}else if($points < $this->pointsArr[$question]['min']){
						$this->pointsArr[$question]['min'] = $points;
					}
					if(!isset($this->pointsArr[$question]['max'])){
						$this->pointsArr[$question]['max'] = $points;
					}else if($points > $this->pointsArr[$question]['max']){
						$this->pointsArr[$question]['max'] = $points;
					}
					foreach($answerArr as $answer){
						$this->pointsArr[$question][$answer] = $points;
					}
				}
			}
		}
	}
	
	/**
	  * Returns the number of points for a given answer of a question
	  * @params: Int uid, String answer
	  * @return: the number of points
	  */
	function getPoints($uid, $answer){
		$questionArr = $this->pi_getRecord('tx_simplesurvey_questions', $uid);
		$question = $questionArr['question'];
		if(is_numeric($this->pointsArr[$question][$answer])){
			return $this->pointsArr[$question][$answer];
		}
		return 0;
	}
	
	/**
	  * Saves the template into $this->content
	  * @return: Returns true if a valid template was found
	  */
	function setTemplate(){
		if($this->getFieldContent('template') != ''){
			$templatefile = $this->uploaddir.$this->getFieldContent('template');
		}
		if(isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_simplesurvey_pi1.']['template'])){
			$tstemplate = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_simplesurvey_pi1.']['template'];
		}
		$default = t3lib_extMgm::siteRelPath($this->extKey).'default_template.html';
		
		if(file_exists($tstemplate)){
			$fh = fopen($tstemplate, 'r');
			$this->content = fread($fh, filesize($tstemplate));
			fclose($fh);
			return true;
		}else if(file_exists($templatefile)){
			$fh = fopen($templatefile, 'r');
			$this->content = fread($fh, filesize($templatefile));
			fclose($fh);
			return true;
		}else if(file_exists($default)){
			$fh = fopen($default, 'r');
			$this->content = fread($fh, filesize($default));
			fclose($fh);
			return true;
		}
		return false;
	}
	
	/**
	  * Extracts the subart and safes it
	  * @params: String fieldname, opt String tablename
	  */
	function extractSubpart($name, $table=''){
		if($table){
			$list = $this->getFieldContent($table);
			$listArr = explode(",", $list);
			$length = count($listArr);
			$stack = array();
			for($i=0; $i<$length; $i++){
				$curRow = $this->pi_getRecord('tx_simplesurvey_'.$table, $listArr[$i]);
				array_push($stack, $curRow[$name]);
			}
			$runAmount = $length;
		}else{
			$list = $this->getFieldContent($name);
			$listArr = explode(",", $list);
			$length = count($listArr);
			$runAmount = 1;
			$this->content = str_replace('<!--###'.strtoupper($name).'### start-->', '<!--###'.strtoupper($name).'### start--><div id="tx-simplesurvey-pi1-hide-###">', $this->content);
			$this->content = str_replace('<!--###'.strtoupper($name).'### end-->', '</div><!--###'.strtoupper($name).'### end-->', $this->content);
		}
		for($i=0; $i<$runAmount; $i++){
			$startpos = strpos($this->content, '<!--###'.strtoupper($name).'### start-->');
			$endpos = strpos($this->content, '<!--###'.strtoupper($name).'### end-->');
			if($startpos !== false && $endpos !== false){
				$markerLength = strlen('<!--###'.strtoupper($name).'### start-->');
				$partLength = $endpos-strlen($this->content);
				$innerPart = substr($this->content, $startpos+$markerLength, $partLength);
				$this->content = $this->str_replace_count('<!--###'.strtoupper($name).'### start-->'.$innerPart.'<!--###'.strtoupper($name).'### end-->', '', $this->content, 1);
				if($table){
					$curRow = $this->pi_getRecord('tx_simplesurvey_'.$table, $listArr[$i]);
					if($curRow['type'] == 2 || $curRow['type'] == 3 || $curRow['type'] == 4){
						$answers = explode("\r\n", $stack[$i]);
						$answerLength = count($answers);
					}else{
						$answers = $stack[$i];
						$answerLength = 1;
					}
					for($j=0; $j<$answerLength; $j++){
						$this->content = $this->str_insert($innerPart, $this->content, $startpos);
					}
				}else{
					for($j=0; $j<$length; $j++){
						$this->content = $this->str_insert($innerPart, $this->content, $startpos);
					}
				}
			}
		}
	}
	
	/**
	  * Replaces all markers with its content
	  */
	function replaceMarker($err = false){
		$questiontext = $this->getFieldContent('questiontext');
		if($questiontext != 'false' && strpos($questiontext, '#') === false){
			$questiontext .= '#';
		}
		$list = $this->getFieldContent('questions');
		$listArr = explode(",", $list);
		$length = count($listArr);
		for($i=0; $i<$length; $i++){
			$listArr[$i] = $this->pi_getRecord('tx_simplesurvey_questions', $listArr[$i]);
		}
		if($err){
			$file = '';
			$errors = explode('+', $err);
			foreach($errors as $id => $value){
				switch($value){
					case 'size':
						$uploaderror .= str_replace('###FILE###', $file, $this->pi_getLL('uploadfile_size','Die Datei ###FILE### ist zu groß!')).'<br/>';
					break;
					case 'invalid':
						$uploaderror .= str_replace('###FILE###', $file, $this->pi_getLL('uploadfile_invalid','Das Format der Datei ###FILE### ist nicht erlaubt!')).'<br/>';
					break;
					case 'error':
						$uploaderror .= str_replace('###FILE###', $file, $this->pi_getLL('uploadfile_error','Es ist ein Fehler beim Hochladen der Datei ###FILE### aufgetreten!')).'<br/>';
					break;
					case 'uploaderror':
						$uploaderror .= str_replace('###FILE###', $file, $this->pi_getLL('uploadfile_uploaderror','Fehler beim hochladen der Datei ###FILE###!')).'<br/>';
					break;
					default:
						$file = $value;
					break;
				}
			}
		}
		if($this->getFieldContent('mandatoryfielderror') != ''){
			$error = '<div id="tx-simplesurvey-pi1-error" style="display:none;">'.$this->getFieldContent('mandatoryfielderror').'</div>';
		}
		if(isset($uploaderror)){
			$error .= '<div'.$this->pi_classParam('uploaderror').'>'.$uploaderror.'</div>';
		}
		
		$this->content = str_replace('###TITLE###', '<div'.$this->pi_classParam('title').'>'.$this->getFieldContent('title').'</div>', $this->content);
		
		$this->content = str_replace('###CAPTION###', '<div'.$this->pi_classParam('caption').'>'.$this->getFieldContent('caption').'</div>', $this->content);
		
		$this->content = str_replace('###DESCRIPTION###', '<div'.$this->pi_classParam('description').'>'.$this->pi_RTEcssText($this->getFieldContent('description')).'</div>', $this->content);
		
		$this->content = str_replace('###ERROR###', $error, $this->content);
		
		if(strlen($list) < 1){
			$this->content = str_replace('###QUESTIONTEXT###', '', $this->content);
			$this->content = str_replace('###QUESTION###', '', $this->content);
			$this->content = str_replace('###ANSWER###', '', $this->content);
			$this->content = str_replace('###SUBMIT###', $this->getFormfield($this->getFieldContent('submittext'), 7, $this->getFieldContent('affirmationmail')), $this->content);
			return;
		}
		
		$tempi = 1;
		for($i = 0; $i<=$length; $i++){
			if($questiontext != 'false'){
				$temptext = str_replace('#', $tempi, $questiontext);
			}
			if($listArr[$i]['mandatory'] && ($listArr[$i]['type'] == 0 || $listArr[$i]['type'] == 1 || $listArr[$i]['type'] == 2 || $listArr[$i]['type'] == 3 || $listArr[$i]['type'] == 4)){
				if($questiontext != 'false'){
					$temptext .= '*';
				}
				//array_push($this->mandatoryArr, 'question'.$listArr[$i]['uid']);
				$this->mandatoryArr['question'.$listArr[$i]['uid']] = array();
				$this->mandatoryArr['question'.$listArr[$i]['uid']]['type'] = $listArr[$i]['type'];
				$this->mandatoryArr['question'.$listArr[$i]['uid']]['rules'] = explode("\r\n", $listArr[$i]['mandatoryrules']);
			}
			if($listArr[$i]['fieldinformation'] != '' && $listArr[$i]['type'] != 5){
				$help = $this->getHelpfield($listArr[$i]['fieldinformation']);
			}else{
				$help = '';
			}
			if($listArr[$i]['type'] != 5 && $questiontext != 'false'){
				$this->content = $this->str_replace_count('###QUESTIONTEXT###', '<div'.$this->pi_classParam('questiontext').'>'.$temptext.'</div>'.$help, $this->content, 1);
				$tempi++;
			}else{
				$this->content = $this->str_replace_count('###QUESTIONTEXT###', '', $this->content, 1);
			}
		}
		
		for($i=0; $i<$length; $i++){
			if($listArr[$i]['type'] != 5){
				$quest = $listArr[$i]['question'];
				if(isset($this->pointsArr[$listArr[$i]['question']]) && $this->getFieldContent('showquestionpoints')){
					if($this->pointsArr[$listArr[$i]['question']]['min'] == $this->pointsArr[$listArr[$i]['question']]['max']){
						$quest .= ' (0 - '.$this->pointsArr[$listArr[$i]['question']]['max'].' '.$this->pi_getLL('points','Punkte').')';
					}else{
						$quest .= ' ('.$this->pointsArr[$listArr[$i]['question']]['min'].' - '.$this->pointsArr[$listArr[$i]['question']]['max'].' '.$this->pi_getLL('points','Punkte').')';
					}
				}
				if($listArr[$i]['mandatory'] && ($listArr[$i]['type'] == 0 || $listArr[$i]['type'] == 1 || $listArr[$i]['type'] == 2 || $listArr[$i]['type'] == 3 || $listArr[$i]['type'] == 4)){
					if($questiontext == 'false'){
						$quest .= '*';
					}
				}
				$this->content = $this->str_replace_count('###QUESTION###', '<div'.$this->pi_classParam('question').'>'.$quest.'</div>', $this->content, 1);
			}else{
				$this->content = $this->str_replace_count('###QUESTION###', '', $this->content, 1);
			}
			
			$this->content = $this->str_replace_count('<div id="tx-simplesurvey-pi1-hide-###">', '<div id="tx-simplesurvey-pi1-hide-question'.$listArr[$i]['uid'].'">', $this->content, 1);
			if($listArr[$i]['type'] == 0 || $listArr[$i]['type'] == 1){
				$answerListArr = explode("\r\n", $listArr[$i]['answers']);
				$answerLength = count($answerListArr);
				$textArr = array();
				$textArr['stdtxt'] = '';
				$textArr['params'] = '';
				for($j=0; $j<$answerLength; $j++){
					if(strpos($answerListArr[$j], '@') === 0){
						if($listArr[$i]['type'] == 0){
							$textArr['stdtxt'] .= substr($answerListArr[$j], 1);
						}else if($listArr[$i]['type'] == 1){
							if($textArr['stdtxt'] == ''){
								$textArr['stdtxt'] .= substr($answerListArr[$j], 1);
							}else{
								$textArr['stdtxt'] .= "\r\n".substr($answerListArr[$j], 1);
							}
						}
					}else{
						$textArr['params'] .= $answerListArr[$j].' ';
					}
				}
				$answer = $this->getFormfield($textArr, $listArr[$i]['type'], 'question'.$listArr[$i]['uid']);
				$this->content = $this->str_replace_count('###ANSWER###', '<div'.$this->pi_classParam('answer').'>'.$answer.'</div>', $this->content, 1);
			}else if($listArr[$i]['type'] == 5){
				$answer = $this->getFormfield($listArr[$i]['answers'], $listArr[$i]['type'], 'question'.$listArr[$i]['uid']);
				$this->content = $this->str_replace_count('###ANSWER###', '<div'.$this->pi_classParam('answer').'>'.$answer.'</div>', $this->content, 1);
			}else if($listArr[$i]['type'] == 2 || $listArr[$i]['type'] == 3 || $listArr[$i]['type'] == 4){
				$answerListArr = explode("\r\n", $listArr[$i]['answers']);
				$answerLength = count($answerListArr);
				for($j=0; $j<$answerLength; $j++){
					$answer = $this->getFormfield($answerListArr[$j], $listArr[$i]['type'], 'question'.$listArr[$i]['uid']);
					if($listArr[$i]['type'] == 2){
						if($j == 0){
							$this->content = $this->str_replace_count('###ANSWER###', '<div'.$this->pi_classParam('answer').'><select name="'.$this->prefixId.'[question'.$listArr[$i]['uid'].']" size="'.$this->inRange($listArr[$i]['size'], 1, 9).'" onChange="checkChanges(\'question'.$listArr[$i]['uid'].'\', 1)">###ANSWER###', $this->content, 1);
						}else if($j == ($answerLength-1)){
							$this->content = $this->str_replace_count('###ANSWER###', '###ANSWER###</select></div>', $this->content, 1);
						}
						$this->content = $this->str_replace_count('###ANSWER###', $answer, $this->content, 1);
					}else{
						$this->content = $this->str_replace_count('###ANSWER###', '<div'.$this->pi_classParam('answer').'>'.$answer.'</div>', $this->content, 1);
					}
				}
			}else if($listArr[$i]['type'] == 6){
				$answerListArr = explode("\r\n", $listArr[$i]['answers']);
				$answer = $this->getFormfield($answerListArr, $listArr[$i]['type'], 'question'.$listArr[$i]['uid']);
				$this->content = $this->str_replace_count('###ANSWER###', '<div'.$this->pi_classParam('answer').'>'.$answer.'</div>', $this->content, 1);
			}
		}
		
		$this->content = str_replace('###SUBMIT###', $this->getFormfield($this->getFieldContent('submittext'), 7, $this->getFieldContent('affirmationmail')), $this->content);
	}
	
	/**
	  * Replaces the Userdata-Marker with the user specific fields
	  */
	function replaceUserdata(){
		$list = $this->getFieldContent('userdata');
		if(strlen($list) < 1){
			$this->content = str_replace('###USERDATA###', '', $this->content);
			return;
		}
		
		$listArr = explode(",", $list);
		$length = count($listArr);
		for($i=0; $i<$length; $i++){
			$listArr[$i] = $this->pi_getRecord('tx_simplesurvey_userdata', $listArr[$i]);
		}
		$userdata = $this->pi_getLL('userdata','Benutzerdaten').'<br/>';
		
		for($i=0; $i<$length; $i++){
			if($listArr[$i]['type'] != 5){
				$userdata .= '<div'.$this->pi_classParam('userdata-title').'>'.$listArr[$i]['title'];
				if($listArr[$i]['mandatory'] && ($listArr[$i]['type'] == 0 || $listArr[$i]['type'] == 1 || $listArr[$i]['type'] == 2 || $listArr[$i]['type'] == 3 || $listArr[$i]['type'] == 4)){
					$userdata .= '*';
					//array_push($this->mandatoryArr, 'userdata'.$listArr[$i]['uid']);
					$this->mandatoryArr['userdata'.$listArr[$i]['uid']] = array();
					$this->mandatoryArr['userdata'.$listArr[$i]['uid']]['type'] = $listArr[$i]['type'];
					$this->mandatoryArr['userdata'.$listArr[$i]['uid']]['rules'] = explode("\r\n", $listArr[$i]['mandatoryrules']);
				}
				if($listArr[$i]['fieldinformation'] != '' && $listArr[$i]['type'] != 5){
					$help = $this->getHelpfield($listArr[$i]['fieldinformation']);
				}else{
					$help = '';
				}
				$userdata .= '</div>'.$help;
			}
			$answerListArr = explode("\r\n", $listArr[$i]['options']);
			$answerLength = count($answerListArr);
			if($listArr[$i]['type'] == 0 || $listArr[$i]['type'] == 1){
				$textArr = array();
				$textArr['stdtxt'] = '';
				$textArr['params'] = '';
				for($j=0; $j<$answerLength; $j++){
					if(strpos($answerListArr[$j], '@') === 0){
						if($listArr[$i]['type'] == 0){
							$textArr['stdtxt'] .= substr($answerListArr[$j], 1);
						}else if($listArr[$i]['type'] == 1){
							$textArr['stdtxt'] .= substr($answerListArr[$j], 1)."\r\n";
						}
					}else{
						$textArr['params'] .= $answerListArr[$j].' ';
					}
				}
				$answer = $this->getFormfield($textArr, $listArr[$i]['type'], 'userdata'.$listArr[$i]['uid']);
				$userdata .= '<div'.$this->pi_classParam('userdata-answer').'>'.$answer.'</div>';
			}else if($listArr[$i]['type'] == 5){
				$answer = $this->getFormfield($listArr[$i]['options'], $listArr[$i]['type'], 'userdata'.$listArr[$i]['uid']);
				$userdata .= '<div'.$this->pi_classParam('userdata-answer').'>'.$answer.'</div>';
			}else if($listArr[$i]['type'] == 2 || $listArr[$i]['type'] == 3 || $listArr[$i]['type'] == 4){
				for($j=0; $j<$answerLength; $j++){
					$answer = $this->getFormfield($answerListArr[$j], $listArr[$i]['type'], 'userdata'.$listArr[$i]['uid']);
					if($listArr[$i]['type'] == 2){
						if($j == 0){
							$userdata .= '<select name="'.$this->prefixId.'[userdata'.$listArr[$i]['uid'].']" size="'.$this->inRange($listArr[$i]['size'], 1, 9).'" onChange="checkChanges(\'userdata'.$listArr[$i]['uid'].'\', 1)">'.$answer;
						}else if($j == ($answerLength-1)){
							$userdata .= $answer.'</select>';
						}
					}else{
						$userdata .= '<div'.$this->pi_classParam('userdata-answer').'>'.$answer.'</div>';
					}
				}
			}else if($listArr[$i]['type'] == 6){
				$answer = $this->getFormfield($answerListArr, $listArr[$i]['type'], 'userdata'.$listArr[$i]['uid']);
				$userdata .= '<div'.$this->pi_classParam('userdata-answer').'>'.$answer.'</div>';
			}
		}
		$this->content = str_replace('###USERDATA###', '<div'.$this->pi_classParam('userdata').'>'.$userdata.'</div>', $this->content);
	}
	
	/**
	  * Creates the form-tags and places the content inside it
	  */
	function createForm(){
		$this->content = '<form enctype="multipart/form-data" action="'.htmlspecialchars(t3lib_div::getIndpEnv('REQUEST_URI')).'" method="post" name="surveyform" onsubmit="checkSurvey(); return false;">'.$this->content.'</form>';
	}
	
	/**
	  *  Returns an input type
	  * @param: String text, Int type, Int question-id
	  * @return: String input type
	  */
	function getFormfield($text, $type, $id, $mails=''){
		switch($type){
			case 0://Text
				if($this->piVars['finishedsurvey'] == 1){
					$text['stdtxt'] = $this->piVars[$id];
				}
				return '<input type="text" name="'.$this->prefixId.'['.$id.']" value="'.htmlspecialchars($text['stdtxt']).'" onKeyup="checkChanges(\''.$id.'\', 1)" '.str_replace(array('<', '>'), '', $text['params']).'/>';
			break;
			case 1://Textbox
				if($this->piVars['finishedsurvey'] == 1){
					$text['stdtxt'] = $this->piVars[$id];
				}
				return '<textarea name="'.$this->prefixId.'['.$id.']" onKeyup="checkChanges(\''.$id.'\', 1)" '.str_replace(array('<', '>'), '', $text['params']).'>'.htmlspecialchars($text['stdtxt']).'</textarea>';
			break;
			case 2://Selector
				if(strpos($text, '@') === 0){
					$text = substr($text, 1);
					if($text != ''){
						if(isset($this->piVars[$id]) && $this->piVars[$id] == $text || !isset($this->piVars[$id])){
							return '<option value="'.htmlspecialchars($text).'" selected>'.htmlspecialchars($text).'</option>';
						}else{
							return '<option value="'.htmlspecialchars($text).'">'.htmlspecialchars($text).'</option>';
						}
					}
				}else{
					if($text != ''){
						if(isset($this->piVars[$id]) && $this->piVars[$id] == $text){
							return '<option value="'.htmlspecialchars($text).'" selected>'.htmlspecialchars($text).'</option>';
						}else{
							return '<option value="'.htmlspecialchars($text).'">'.htmlspecialchars($text).'</option>';
						}
					}
				}
			break;
			case 3://Checkbox
				if(isset($this->piVars[$id]) || $this->piVars['finishedsurvey'] == 1){
					if(isset($this->piVars[$id]) && !$this->array_empty($this->piVars[$id])){
						if(strpos($text, '@') === 0){
							$text = substr($text, 1);
						}
						if($text != ''){
							return '<input type="checkbox" name="'.$this->prefixId.'['.$id.'][]" value="'.htmlspecialchars($text).'" onClick="checkChanges(\''.$id.'\', 1)"'. (t3lib_div::inArray($this->piVars[$id], $text) ? ' checked':'') .'/>'.htmlspecialchars($text);
						}
					}else{
						if(strpos($text, '@') === 0){
							$text = substr($text, 1);
						}
						if($text != ''){
							return '<input type="checkbox" name="'.$this->prefixId.'['.$id.'][]" value="'.htmlspecialchars($text).'" onClick="checkChanges(\''.$id.'\', 1)" />'.htmlspecialchars($text);
						}
					}
				}else{
					if(strpos($text, '@') === 0){
						$text = substr($text, 1);
						if($text != ''){
							return '<input type="checkbox" name="'.$this->prefixId.'['.$id.'][]" value="'.htmlspecialchars($text).'" onClick="checkChanges(\''.$id.'\', 1)" checked />'.htmlspecialchars($text);
						}
					}else{
						if($text != ''){
							return '<input type="checkbox" name="'.$this->prefixId.'['.$id.'][]" value="'.htmlspecialchars($text).'" onClick="checkChanges(\''.$id.'\', 1)" />'.htmlspecialchars($text);
						}
					}
				}
			break;
			case 4://Radio
				if(isset($this->piVars[$id])){
					if(strpos($text, '@') === 0){
						$text = substr($text, 1);
					}
					if($text != ''){
						return '<input type="radio" name="'.$this->prefixId.'['.$id.']" value="'.htmlspecialchars($text).'" onClick="checkChanges(\''.$id.'\', 1)"'. ($this->piVars[$id] == $text ? ' checked':'') .'/>'.htmlspecialchars($text);
					}
				}else{
					if(strpos($text, '@') === 0){
						$text = substr($text, 1);
						if($text != ''){
							return '<input type="radio" name="'.$this->prefixId.'['.$id.']" value="'.htmlspecialchars($text).'" onClick="checkChanges(\''.$id.'\', 1)" checked />'.htmlspecialchars($text);
						}
					}else{
						if($text != ''){
							return '<input type="radio" name="'.$this->prefixId.'['.$id.']" value="'.htmlspecialchars($text).'" onClick="checkChanges(\''.$id.'\', 1)" />'.htmlspecialchars($text);
						}
					}
				}
			break;
			case 5://Static
				return str_replace("\r\n", '<br/>', $text);
			break;
			case 6://Upload
				$length = count($text);
				for($i=0; $i<$length; $i++){
					if(strpos($text[$i], '@') === 0){
						$maxlength = substr($text[$i], 1);
					}else{
						$accept .= $text[$i].',';
					}
				}
				$accept = substr($accept, 0, -1);
				return '<input type="hidden" name="MAX_FILE_SIZE" value="'.$maxlength.'">
						<input name="'.$this->prefixId.'['.$id.']" type="file" accept="'.$accept.'">';
			break;
			case 7://Submit
				if($text == ''){
					$text = $this->pi_getLL('submit','Absenden');
				}
				return '<input type="hidden" name="'.$this->prefixId.'[finishedsurvey]" value="1">
						<input type="hidden" name="no_cache" value="1">
						<input type="submit" class="tx-simplesurvey-pi1-submit" value="'.$text.'" />';
			break;
			default://none
				return $text;
		}
		return '';
	}
	
	/**
	  * Creates a JS Script which checks the mandatory formfields
	  */
	function createMandatoryScript(){
		$content = '<script type="text/javascript">
						function checkSurvey(){
							noerror = true;';
						foreach($this->mandatoryArr as $name => $infos){
							$content .= 'var formObj = getFormObj(getType("'.$name.'"), "'.$name.'");
							if("'.substr($name, 0, 8).'" == "userdata" || ("'.substr($name, 0, 8).'" == "question" && (typeof hiddenValueArr["'.$name.'"] == "undefined" || !hiddenValueArr["'.$name.'"]["hidden"]))){';
			if($infos['type'] == 0 || $infos['type'] == 1){// textfield,  textarea
					$content .= 'if(formObj.value == ""){
									if(noerror){
										formObj.focus();';
										if($this->getFieldContent('mandatoryfielderror') != ''){
								$content .= 'document.getElementById(\'tx-simplesurvey-pi1-error\').style.display = \'block\';';
										}
						$content .= '}
									formObj.style.background="#FF4626";
									noerror = false;
								}else{
									formObj.style.background="white";
								}';
			}else if($infos['type'] == 2){// dropdown
					$content .= 'if(';
									foreach($infos['rules'] as $val){
										$content .= 'formObj.value != "'.$val.'" && ';
									}
									$content = substr($content, 0, -4);
						$content .= '){
									if(noerror){
										formObj.focus();';
										if($this->getFieldContent('mandatoryfielderror') != ''){
								$content .= 'document.getElementById(\'tx-simplesurvey-pi1-error\').style.display = \'block\';';
										}
						$content .= '}
									formObj.style.background="#FF4626";
									noerror = false;
								}else{
									formObj.style.background="white";
								}';
			}else if($infos['type'] == 3){// checkbox
					$content .= 'var allchecked = true;
								var notchecked = new Array();
								for(var i=0; i<formObj.length; i++){
									if(!formObj[i].checked){
										allchecked = false;
										notchecked.push(i);
									}else{
										formObj[i].style.background="none";
									}
								}
								if(!allchecked){
									if(noerror){
										formObj[0].focus();';
										if($this->getFieldContent('mandatoryfielderror') != ''){
								$content .= 'document.getElementById(\'tx-simplesurvey-pi1-error\').style.display = \'block\';';
										}
						$content .= '}
									for(var i = 0; i<notchecked.length; i++){
										formObj[notchecked[i]].style.background="#FF4626";
									}
									noerror = false;
								}';
			}else if($infos['type'] == 4){// radio
					$content .= 'var val = "";
								var firstMatch = 0;
								for(var i=0; i<formObj.length; i++){
									if(formObj[i].value == "'.$infos['rules'][0].'"){
										firstMatch = i;
									}
									if(formObj[i].checked){
										val = formObj[i].value;
									}
								}
								if(val == "" || (';
									foreach($infos['rules'] as $val){
						$content .= 'val != '.$val.' && ';
									}
									$content = substr($content, 0, -4);
						$content .= ')){
									if(noerror){
										formObj[firstMatch].focus();';
										if($this->getFieldContent('mandatoryfielderror') != ''){
								$content .= 'document.getElementById(\'tx-simplesurvey-pi1-error\').style.display = \'block\';';
										}
						$content .= '}
									formObj[firstMatch].style.background="#FF4626";
									noerror = false;
								}else{
									formObj[firstMatch].style.background="none";
								}';
			}
				$content .= '}';
						}
				$content .= 'if(noerror){';
								if($this->getFieldContent('mandatoryfielderror') != ''){
						$content .= 'document.getElementById(\'tx-simplesurvey-pi1-error\').style.display = \'none\';';
								}
				$content .= 'document.surveyform.submit();
							}
						}
					</script>';
		return $content;
	}
	
	/**
	  * Creates a JS Script which hides questions based on the input
	  */
	function createDependencesScript(){
		$this->pi_loadLL();
		$content = '<script type="text/javascript">
						startCheck();
						function startCheck(){
							hiddenValueArr = new Array();';
							foreach($this->dependencesArr as $q1 => $secondArr){
								foreach($secondArr as $q2 => $val){
									$content .= 'if(getType("'.$q2.'") != "NA"){
													switch(getType("'.$q1.'")){
														case "checkbox":
														case "radio":
															var formObj = getFormObj(getType("'.$q1.'"), "'.$q1.'");
															var noneselected = true;
															for(var i=0; i<formObj.length; i++){
																if(formObj[i].checked){
																	if(\';'.$val.';\'.search(";"+formObj[i].value+";") != -1){
																		hiddenValueArr["'.$q2.'"] = new Array();
																		hiddenValueArr["'.$q2.'"]["num"] = new Array();
																		noneselected = false;
																		break;
																	}
																}
															}
															if(noneselected){
																saveInput("'.$q2.'");
																document.getElementById(\'tx-simplesurvey-pi1-hide-'.$q2.'\').style.display="none";
															}
														break;
														case "text":
															var formObj = getFormObj(getType("'.$q1.'"), "'.$q1.'");
															if(\';'.$val.';\'.search(";"+formObj.value+";") != -1){
																hiddenValueArr["'.$q2.'"] = new Array();
															}else{
																saveInput("'.$q2.'");
																document.getElementById(\'tx-simplesurvey-pi1-hide-'.$q2.'\').style.display="none";
															}
														break;
													}
												}';
								}
							}
		$content .= '	}
						
						function checkChanges(id, run){
							if(run == 100){
								alert("'.$this->pi_getLL('recursion','Achtung!\nUm eine Endlosschleife durch Rekursion zu verhindern, wird jetzt abgebrochen.\nBitte überprüfen Sie ihre Abfragebedingungen im Backend!').'");
								return;
							}';
							foreach($this->dependencesArr as $q1 => $secondArr){
								$content .= 'if(id == \''.$q1.'\'){';
								foreach($secondArr as $q2 => $val){
									$content .= 'if(getType("'.$q2.'") != "NA"){
													switch(getType("'.$q1.'")){
														case "checkbox":
														case "radio":
															var formObj = getFormObj(getType("'.$q1.'"), "'.$q1.'");
															var noneselected = true;
															for(var i=0; i<formObj.length; i++){
																if(formObj[i].checked){
																	if(\';'.$val.';\'.search(";"+formObj[i].value+";") != -1){
																		if(hiddenValueArr["'.$q2.'"]["hidden"]){
																			document.getElementById(\'tx-simplesurvey-pi1-hide-'.$q2.'\').style.display="block";
																			loadPreValues("'.$q2.'");
																		}
																		noneselected = false;
																		break;
																	}
																}
															}
															if(noneselected && !hiddenValueArr["'.$q2.'"]["hidden"]){
																saveInput("'.$q2.'");
																document.getElementById(\'tx-simplesurvey-pi1-hide-'.$q2.'\').style.display="none";
															}
															checkChanges("'.$q2.'", run+1);
														break;
														case "text":
															var formObj = getFormObj(getType("'.$q1.'"), "'.$q1.'");
															if(\';'.$val.';\'.search(";"+formObj.value+";") != -1){
																if(hiddenValueArr["'.$q2.'"]["hidden"]){
																	document.getElementById(\'tx-simplesurvey-pi1-hide-'.$q2.'\').style.display="block";
																	loadPreValues("'.$q2.'");
																}
															}else if(!hiddenValueArr["'.$q2.'"]["hidden"]){
																saveInput("'.$q2.'");
																document.getElementById(\'tx-simplesurvey-pi1-hide-'.$q2.'\').style.display="none";
															}
															checkChanges("'.$q2.'", run+1);
														break;
													}
												}';
								}
								$content .= '}';
							}
		$content .= '	}
						
						function saveInput(input){
							switch(getType(input)){
								case "checkbox":
								case "radio":
									if(typeof(hiddenValueArr[input]) != "object"){
										hiddenValueArr[input] = new Array();
										hiddenValueArr[input]["num"] = new Array();
									}
									var formObj = getFormObj(getType(input), input);
									
									for(var i=0; i<formObj.length; i++){
										if(formObj[i].checked){
											hiddenValueArr[input]["num"][i] = true;
											formObj[i].checked = false;
										}else{
											hiddenValueArr[input]["num"][i] = false;
										}
									}
									hiddenValueArr[input]["hidden"] = true;
								break;
								case "text":
									if(typeof(hiddenValueArr[input]) != "object"){
										hiddenValueArr[input] = new Array();
									}
									var formObj = getFormObj(getType(input), input);
									
									hiddenValueArr[input]["value"] = formObj.value;
									formObj.value = "";
									hiddenValueArr[input]["hidden"] = true;
								break;
							}
						}
						
						function loadPreValues(input){
							switch(getType(input)){
								case "checkbox":
								case "radio":
									var formObj = getFormObj(getType(input), input);
									for(var i=0; i<formObj.length; i++){
										if(hiddenValueArr[input]["num"][i]){
											formObj[i].checked = true;
										}else{
											formObj[i].checked = false;
										}
									}
									hiddenValueArr[input]["hidden"] = false;
								break;
								case "text":
									var formObj = getFormObj(getType(input), input);
									formObj.value = hiddenValueArr[input]["value"];
									hiddenValueArr[input]["hidden"] = false;
								break;
							}
						}
						
						function getType(fieldname){
							if(typeof(document.surveyform.elements["tx_simplesurvey_pi1["+fieldname+"][]"]) == "object"){
								return "checkbox";
							}else if(document.surveyform.elements["tx_simplesurvey_pi1["+fieldname+"]"] == null){
								return "NA";
							}else if(document.surveyform.elements["tx_simplesurvey_pi1["+fieldname+"]"].value == undefined){
								return "radio";
							}else{
								return "text";
							}
						}
						
						function getFormObj(type, fieldname){
							switch(type){
								case "checkbox":
									return document.surveyform.elements["tx_simplesurvey_pi1["+fieldname+"][]"];
								break;
								case "radio":
								case "text":
									return document.surveyform.elements["tx_simplesurvey_pi1["+fieldname+"]"];
								break;
							}
						}
					</script>';
		return $content;
	}
	
	/**
	  * Returns the questions+answers of the previously filled survey
	  * @return: String survey
	  */
	function getResult($showPoints = false){
		$points = 0;
		$survey = '<div'.$this->pi_classParam('title').'>'.$this->getFieldContent('title').'</div><br/>';
		foreach($this->piVars as $name => $content){
			if($content != ''){
				if(substr($name, 0, 8) == 'question'){
					$question = $this->pi_getRecord('tx_simplesurvey_questions', substr($name, 8));
					if($question['type'] == 1){
						if($showPoints && isset($this->pointsArr[$question['question']])){
							if(!isset($this->pointsArr[$question['question']][$content])){
								$this->pointsArr[$question['question']][$content] = 0;
							}
							$points += $this->pointsArr[$question['question']][$content];
						}
						$survey .= '<div'.$this->pi_classParam('result-question').'>'.(isset($this->pointsArr[$question['question']]) && $this->getFieldContent('showquestionpoints') ? $question['question'].' ('.$this->pointsArr[$question['question']]['min'].' - '.$this->pointsArr[$question['question']]['max'].' '.$this->pi_getLL('points','Punkte').')' : $question['question']).'</div><br/><div'.$this->pi_classParam('result-answer').'>'.$this->pi_RTEcssText($content).'</div>'.($showPoints && isset($this->pointsArr[$question['question']]) ? ' ('.$this->pointsArr[$question['question']][$content].' '.$this->pi_getLL('points','Punkte').')' : '').'<br/><br/>';
					}else if($question['type'] == 3){
						$survey .= '<div'.$this->pi_classParam('result-question').'>'.(isset($this->pointsArr[$question['question']]) && $this->getFieldContent('showquestionpoints') ? $question['question'].' ('.$this->pointsArr[$question['question']]['min'].' - '.$this->pointsArr[$question['question']]['max'].' '.$this->pi_getLL('points','Punkte').')' : $question['question']).'</div><br/>';
						foreach($content as $value){
							if($showPoints && isset($this->pointsArr[$question['question']])){
								if(!isset($this->pointsArr[$question['question']][$value])){
									$this->pointsArr[$question['question']][$value] = 0;
								}
								$points += $this->pointsArr[$question['question']][$value];
							}
							$survey .= '<div'.$this->pi_classParam('result-answer').'>'.$value.($showPoints && isset($this->pointsArr[$question['question']]) ? ' ('.$this->pointsArr[$question['question']][$value].' '.$this->pi_getLL('points','Punkte').')' : '').'</div><br/>';
						}
						$survey .= '<br/>';
					}else{
						if($showPoints && isset($this->pointsArr[$question['question']])){
							if(!isset($this->pointsArr[$question['question']][$content])){
								$this->pointsArr[$question['question']][$content] = 0;
							}
							$points += $this->pointsArr[$question['question']][$content];
						}
						$survey .= '<div'.$this->pi_classParam('result-question').'>'.(isset($this->pointsArr[$question['question']]) && $this->getFieldContent('showquestionpoints') ? $question['question'].' ('.$this->pointsArr[$question['question']]['min'].' - '.$this->pointsArr[$question['question']]['max'].' '.$this->pi_getLL('points','Punkte').')' : $question['question']).'</div><br/><div'.$this->pi_classParam('result-answer').'>'.$content.($showPoints && isset($this->pointsArr[$question['question']]) ? ' ('.$this->pointsArr[$question['question']][$content].' '.$this->pi_getLL('points','Punkte').')' : '').'</div><br/><br/>';
					}
				}else if(substr($name, 0, 8) == 'userdata'){
					$userdata = $this->pi_getRecord('tx_simplesurvey_userdata', substr($name, 8));
					if($userdata['type'] == 1){
						$survey .= '<div'.$this->pi_classParam('userdata-title').'>'.$userdata['title'].'</div><br/><div'.$this->pi_classParam('result-userdata').'>'.$this->pi_RTEcssText($content).'</div><br/><br/>';
					}else if($userdata['type'] == 3){
						$survey .= '<div'.$this->pi_classParam('userdata-title').'>'.$userdata['title'].'</div><br/>';
						foreach($content as $value){
							$survey .= '<div'.$this->pi_classParam('result-userdata').'>'.$value.'</div><br/>';
						}
						$survey .= '<br/>';
					}else{
						$survey .= '<div'.$this->pi_classParam('userdata-title').'>'.$userdata['title'].'</div><br/><div'.$this->pi_classParam('result-userdata').'>'.$content.'</div><br/><br/>';
					}
				}
			}
		}
		if($showPoints){
			$survey .= '<br/><div'.$this->pi_classParam('overall-points').'>'.$this->pi_getLL('overallpoints','Gesamtpunktzahl:').' '.$points.' '.$this->pi_getLL('points','Punkte').'</div>';
		}
		return $survey;
	}
	
	/**
	  * Creates a help icon and text
	  * @params: String text
	  * @return: ready to use help icon
	  */
	function getHelpfield($text){
		$text = str_replace("\\", "\\\\", $text);
		$text = str_replace("\r\n", '\n', $text);
		$text = str_replace("'", '\\\'', $text);
		$text = str_replace('"', '&quot;', $text);
		$text = str_replace('<', '&lt;', $text);
		$text = str_replace('>', '&gt;', $text);

		return '<div'.$this->pi_classParam('help').'><a href="javascript:window.alert(\''.$text.'\');">?</a></div>';
	}
	
	/**
	  * Sends a html-mail
	  * @params: Mail empfänger, String nachricht, String betreff, Mail sender, String absendername
	  */
	function sendMail($receiptmail, $message, $subject, $frommail, $fromname, $files){
		require_once(PATH_t3lib.'class.t3lib_htmlmail.php');
		$this->htmlMail = t3lib_div::makeInstance('t3lib_htmlmail');
		$this->htmlMail->start();
		$this->htmlMail->recipient = $receiptmail;
		$this->htmlMail->subject = $subject;
		$this->htmlMail->from_email = $frommail;
		$this->htmlMail->from_name = $fromname;
		$this->htmlMail->returnPath = $frommail;
		//$this->htmlMail->addPlain($message);
		$this->htmlMail->setHTML($this->htmlMail->encodeMsg($message));
		if(count($files) > 0){
			foreach($files as $nr => $file){
				$this->htmlMail->addAttachment($file);
			}
		}
		$this->htmlMail->send($receiptmail);
	}
	
	/**
	  * Replaces a string optional times
	  * @params: Any search, Any replace, Sting/Var source, Int amount
	  * @returns: replaced String
	  */
	function str_replace_count($search, $replace, $subject, $times){
		$subject_original = $subject;
		$len = strlen($search);
		$pos = 0;
		for($i=1; $i<=$times; $i++){
			$pos = strpos($subject, $search, $pos);
			if($pos!==false){
				$subject = substr($subject_original, 0, $pos);
				$subject .= $replace;
				$subject .= substr($subject_original, $pos+$len);
				$subject_original = $subject;
			}else{
				break;
			}
		}
		return $subject;
	}
	
	/**
	  * Inserts a string into another
	  * @params: String insert string, String target string, Int offset
	  * @return: full string
	  */
	function str_insert($insertstring, $intostring, $offset){
		$part1 = substr($intostring, 0, $offset);
		$part2 = substr($intostring, $offset);
		$part1 = $part1 . $insertstring;
		$whole = $part1 . $part2;
		return $whole;
	}
	
	/**
	  * Tests if a array is empty
	  * @params: array
	  * @return: true if array is empty, else false
	  */
	function array_empty($arr){
		foreach($arr as $val){
			if($val != ''){
				if(!is_array($val)){
					return FALSE;
				}else{
					if(!$this->array_empty($val)){
						return FALSE;
					}
				}
			}
		}
		return TRUE;
	}
	
	/**
	  * Creates a md5-Hash for a file
	  * @params: file
	  * @return: the md5-value of the file
	  */
	function getMD5($file){
	    if(file_exists($file)){
	        return md5_file($file);
	    }
	    return false;
	}
	
	/**
	  * Returns a value in the range, if the given value is not in range
	  * @params: value, start, end
	  */
	function inRange($value, $start, $end){
	    if(!is_numeric($value)){
	        $value = intval($value);
	    }
		
	    if($value > $end){
			return $end;
		}
		if($value < $start){
			return $start;
		}
		return $value;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simplesurvey/pi1/class.tx_simplesurvey_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simplesurvey/pi1/class.tx_simplesurvey_pi1.php']);
}

?>