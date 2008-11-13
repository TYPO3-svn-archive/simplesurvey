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

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(PATH_t3lib.'class.t3lib_tsfebeuserauth.php');

/**
 * Module extension (addition to function menu) 'SimpleSurvey results' for the 'simplesurvey' extension.
 *
 * @author    Michael Kuzmin <michael.kuzmin@stp-online.de>
 * @package    TYPO3
 * @subpackage    tx_simplesurvey
 */
class tx_simplesurvey_modfunc1 extends t3lib_extobjbase {

	function menuConfig()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		
		$this->MOD_MENU = array(
			'tx_simplesurvey_modfunc1_survey' => array(
				-1 => $LANG->getLL('choose')
			),
			'tx_simplesurvey_modfunc1_order' => array(
				'c DESC,answer' => $LANG->getLL('mostanswers'),
				'c ASC,answer' => $LANG->getLL('fewestanswers'),
				'answer' => $LANG->getLL('alphabeticalanswers'),
				'uid' => $LANG->getLL('appearanceanswers')
			),
			'tx_simplesurvey_modfunc1_limit' => array(
				10 => '10',
				100 => '100',
				250 => '250',
				500 => '500',
				'' => $LANG->getLL('all')
			),
			'tx_simplesurvey_modfunc1_single' => array(
				-1 => $LANG->getLL('choosesingle')
			)
		);
		
		// tx_simplesurvey_modfunc1_survey
		$queryParts['SELECT']= 'uid, pid, title';
		$queryParts['FROM']='tx_simplesurvey_surveys';
		$queryParts['WHERE']=sprintf('pid = %s ', $this->pObj->id);
		$queryParts['ORDERBY']='title';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$queryParts['SELECT'],
				$queryParts['FROM'],
				$queryParts['WHERE'],
				'',
				$queryParts['ORDERBY'],
				''
		);
		
		if($res){
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$this->MOD_MENU['tx_simplesurvey_modfunc1_survey'][$row['uid']] = $row['title'];
			}
		}
		
		// tx_simplesurvey_modfunc1_single
		$queryParts['SELECT']= 'crdate, surveyuid';
		$queryParts['FROM']='tx_simplesurvey_answers';
		$queryParts['GROUPBY']='crdate';
		$queryParts['ORDERBY']='crdate DESC';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$queryParts['SELECT'],
				$queryParts['FROM'],
				'',
				$queryParts['GROUPBY'],
				$queryParts['ORDERBY'],
				''
		);
		
		if($res){
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$this->MOD_MENU['tx_simplesurvey_modfunc1_single'][$row['crdate']] = date("Y-m-d H:i:s",$row['crdate']).' #uid:'.$row['surveyuid'];
			}
		}

		$this->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, t3lib_div::_GP('SET'), $this->MCONF['name']);
	}
	
	/**
	 * Calls showResults to generate output.
	 *
	 * @return	string		html table with results from showResults()
	 */
	function main()	{
		global $SOBE,$BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		$this->MCONF = $GLOBALS['MCONF'];
		$this->menuConfig();
		
		if(t3lib_div::_GET('export') == 'csv'){
			$csv = 'date;survey;question;userdata;points;answer';
			$this->makeUIDArr();
			
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'crdate, surveyuid, questionuid, userdatauid, points, answer',
				'tx_simplesurvey_answers',
				'',
				'',
				'',
				''
			);
			
			if($res){
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						$csv.="\r\n".date("Y-m-d H:i:s",$row['crdate']).';';
					if(strpos($this->UIDArr['surveyuid'][$row['surveyuid']], ';') !== false || strpos($this->UIDArr['surveyuid'][$row['surveyuid']], "\r\n") !== false){
						$csv.='"'.$this->UIDArr['surveyuid'][$row['surveyuid']].'";';
					}else{
						$csv.=$this->UIDArr['surveyuid'][$row['surveyuid']].';';
					}
					if(strpos($this->UIDArr['questionuid'][$row['questionuid']], ';') !== false || strpos($this->UIDArr['questionuid'][$row['questionuid']], "\r\n") !== false){
						$csv.='"'.$this->UIDArr['questionuid'][$row['questionuid']].'";';
					}else{
						$csv.=$this->UIDArr['questionuid'][$row['questionuid']].';';
					}
					if(strpos($this->UIDArr['userdatauid'][$row['userdatauid']], ';') !== false || strpos($this->UIDArr['userdatauid'][$row['userdatauid']], "\r\n") !== false){
						$csv.='"'.$this->UIDArr['userdatauid'][$row['userdatauid']].'";';
					}else{
						$csv.=$this->UIDArr['userdatauid'][$row['userdatauid']].';';
					}
						$csv.=$row['points'].';';
					if(strpos($row['answer'], ';') !== false || strpos($row['answer'], "\r\n") !== false){
						$csv.='"'.$row['answer'].'"';
					}else{
						$csv.=$row['answer'];
					}
				}
			}
			
			header("Content-type: application/download");
	        header('Pragma: public'); 
	        header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT'); 
	        header('Cache-Control: no-store, no-cache, must-revalidate'); 
	        header('Cache-Control: pre-check=0, post-check=0, max-age=0'); 
	        header('Content-Transfer-Encoding: none');
			header('Content-Type: application/csv; name="surveys.csv"'); 
	        header('Content-Disposition: inline; filename="surveys.csv"');
			print $csv;
			exit;
		}
		
		
		$menu=array();
		if($this->MOD_SETTINGS['tx_simplesurvey_modfunc1_single'] == -1){
			$menu[]=t3lib_BEfunc::getFuncMenu($this->pObj->id, 'SET[tx_simplesurvey_modfunc1_survey]', $this->MOD_SETTINGS['tx_simplesurvey_modfunc1_survey'], $this->MOD_MENU['tx_simplesurvey_modfunc1_survey']);
			$menu[]=t3lib_BEfunc::getFuncMenu($this->pObj->id, 'SET[tx_simplesurvey_modfunc1_order]', $this->MOD_SETTINGS['tx_simplesurvey_modfunc1_order'], $this->MOD_MENU['tx_simplesurvey_modfunc1_order']);
			$menu[]=t3lib_BEfunc::getFuncMenu($this->pObj->id, 'SET[tx_simplesurvey_modfunc1_limit]', $this->MOD_SETTINGS['tx_simplesurvey_modfunc1_limit'], $this->MOD_MENU['tx_simplesurvey_modfunc1_limit']);
		}
		$menu[]=t3lib_BEfunc::getFuncMenu($this->pObj->id, 'SET[tx_simplesurvey_modfunc1_single]', $this->MOD_SETTINGS['tx_simplesurvey_modfunc1_single'], $this->MOD_MENU['tx_simplesurvey_modfunc1_single']);
		$theOutput.=$this->pObj->doc->section("Menu",implode(" - ",$menu),0,1);
		if($this->MOD_SETTINGS['tx_simplesurvey_modfunc1_single'] != -1){
			$theOutput.=' - <input type="button" name="backtoresultview" value="'.$LANG->getLL('back').'" onclick="jumpToUrl(\'index.php?&amp;id='.$this->pObj->id.'&amp;SET[tx_simplesurvey_modfunc1_single]=-1\');">';
		}
		$theOutput.=' - <input type="button" name="csv" value="'.$LANG->getLL('csvexport').'" onclick="jumpToUrl(\'index.php?&amp;id='.$this->pObj->id.'&amp;export=csv\');">';
		
		$theOutput.=$this->pObj->doc->spacer(5);
		$theOutput.=$this->pObj->doc->section($LANG->getLL('title'),$this->showResults(),0,1);
		$theOutput.=$this->pObj->doc->spacer(5);
		
		return $theOutput;
	}


	/**
	 * Generates html table containing the results.
	 * @return	string		html table with results
	 */
	function showResults()	{
		global $LANG,$HTTP_GET_VARS,$TYPO3_CONF_VARS;

		if($this->MOD_SETTINGS['tx_simplesurvey_modfunc1_single'] == -1){
			$conf['bid']=$this->MOD_SETTINGS['tx_simplesurvey_modfunc1_survey'];
			$conf['order']=$this->MOD_SETTINGS['tx_simplesurvey_modfunc1_order'];
			$conf['limit']=$this->MOD_SETTINGS['tx_simplesurvey_modfunc1_limit'];

			$addwhere1=' AND questionuid <> 0';
			$addwhere2=' AND userdatauid <> 0';
			$addwhere3=' AND crdate = (SELECT max(crdate) FROM tx_simplesurvey_answers WHERE surveyuid = '.$conf['bid'].')';

			$content.= '<table cellpading="5" cellspacing="5" valign="top"><tr><td valign="top">'
				.$this->getTable($LANG->getLL('allquestions'),$addwhere1,$conf).'</td><td valign="top">'
				.$this->getTable($LANG->getLL('alluser'),$addwhere2,$conf).'</td><td valign="top">'
				.$this->getTable($LANG->getLL('last'),$addwhere3,$conf).'</td></tr></table>';
		}else{
			$conf['crdate']=$this->MOD_SETTINGS['tx_simplesurvey_modfunc1_single'];
			$conf['order']='uid';
			$addwhere=' AND crdate = '.$this->MOD_SETTINGS['tx_simplesurvey_modfunc1_single'];
			$content.= '<table cellpading="5" cellspacing="5" valign="top"><tr><td valign="top">'.$this->getTable($LANG->getLL('single'),$addwhere,$conf).'</td></tr></table>';
		}

		return $content;
	}

	/**
	 * Generates html table with title and results
	 *
	 * @param	string		title
	 * @param	string		add where for sql query
	 * @param	array			configuration: bid = pageid, order = order of the results, limit =max number of rows
	 * @return	string		html table with results
	 */
	function getTable($title,$addwhere,$conf){
		global $LANG;

		$queryParts['SELECT']= '*, COUNT(answer) AS c, MAX(crdate) AS max';
		/*if($addwhere == ' AND questionuid <> 0'){
			$queryParts['SELECT'].=', GROUP_CONCAT(questionuid) as uids';
		}else if($addwhere == ' AND userdatauid <> 0'){
			$queryParts['SELECT'].=', GROUP_CONCAT(userdatauid) as uids';
		}*/
		$queryParts['FROM']='tx_simplesurvey_answers';
		if($title == $LANG->getLL('single')){
			$queryParts['WHERE']=sprintf('crdate = %s '.$addwhere, $conf['crdate']);
		}else{
			$queryParts['WHERE']=sprintf('surveyuid = %s '.$addwhere, $conf['bid']);
		}
		$queryParts['GROUPBY']='questionuid,answer';
		if($conf['order'] == 'uid'){
			if($addwhere == ' AND questionuid <> 0'){
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('questions','tx_simplesurvey_surveys','uid = '.$conf['bid'],'','',1);
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$queryParts['ORDERBY']='field(questionuid, '.$row['questions'].')';
			}else if($addwhere == ' AND userdatauid <> 0'){
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('userdata','tx_simplesurvey_surveys','uid = '.$conf['bid'],'','',1);
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$queryParts['ORDERBY']='field(userdatauid, '.$row['userdata'].')';
			}else{
				$queryParts['ORDERBY']=$conf['order'];
			}
		}else{
			$queryParts['ORDERBY']=$conf['order'];
		}
		
		if($title == $LANG->getLL('last') || $title == $LANG->getLL('single')){
			$queryParts['LIMIT']='';
		}else{
			$queryParts['LIMIT']=$conf['limit'];
		}
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$queryParts['SELECT'],
				$queryParts['FROM'],
				$queryParts['WHERE'],
				$queryParts['GROUPBY'],
				$queryParts['ORDERBY'],
				$queryParts['LIMIT']
		);

		if($res){
			$count = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		}else{
			$count = 0;
		}

		if($count == 0 && $title != $LANG->getLL('single')){
			$secureaddwhere = ' AND surveyuid IN ('.($this->extGetTreeList($conf['bid'],100,0,'1')).$conf['bid'].') ';

	 		$queryParts['WHERE']= '1 '.$addwhere.$secureaddwhere;
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$queryParts['SELECT'],
				$queryParts['FROM'],
				$queryParts['WHERE'],
				$queryParts['GROUPBY'],
				$queryParts['ORDERBY'],
				$queryParts['LIMIT']
		);
		
		$table1='';
		$i=0;
		if($res){
			$this->makeUIDArr();
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				if($i == 0){
					if($title == $LANG->getLL('last')){
						$title.=' ('.date("d.m.Y H:i",$row['max']).')';
					}
				}
				$i++;
				
				if($row['questionuid'] != 0){
					$uid = 'questionuid';
				}else if($row['userdatauid'] != 0){
					$uid = 'userdatauid';
				}
				/*$uids = explode(',', $row['uids']);
				foreach($uids as $uidval){
					$table1.='<tr class="bgColor4"><td>'.$this->UIDArr[$uid][$uidval].'</td><td>'.$row['answer'].'</td><td>&nbsp;&nbsp;'.$row['c'].'</td></tr>';
				}*/
				$table1.='<tr class="bgColor4"><td>'.$this->UIDArr[$uid][$row[$uid]].'</td><td>'.$row['answer'].'</td><td>&nbsp;&nbsp;'.$row['c'].'</td></tr>';
			}
		}

		if($i==0){
			$table1='<tr class="bgColor4"><td callspan="3">'.$LANG->getLL("noresults").'</td></tr>';
		}

		$table1='<table class="bgColor5" cellpadding="2" cellspacing="1"><tr class="tableheader"><td colspan="3">'.$title.'</td></tr>'.$table1.'</table>';

		return $table1;
	}
	
	function makeUIDArr(){
		$queryParts['SELECT']= 'uid, question';
		$queryParts['FROM']='tx_simplesurvey_questions';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$queryParts['SELECT'],
				$queryParts['FROM'],
				'','','',''
		);

		if($res){
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$this->UIDArr['questionuid'][$row['uid']] = $row['question'];
			}
		}
		
		$queryParts['SELECT']= 'uid, title';
		$queryParts['FROM']='tx_simplesurvey_userdata';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$queryParts['SELECT'],
				$queryParts['FROM'],
				'','','',''
		);

		if($res){
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$this->UIDArr['userdatauid'][$row['uid']] = $row['title'];
			}
		}
		
		$queryParts['FROM']='tx_simplesurvey_surveys';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				$queryParts['SELECT'],
				$queryParts['FROM'],
				'','','',''
		);

		if($res){
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$this->UIDArr['surveyuid'][$row['uid']] = $row['title'];
			}
		}
	}

	/**
	 * Calls t3lib_tsfeBeUserAuth::extGetTreeList.
	 * Although this duplicates the function t3lib_tsfeBeUserAuth::extGetTreeList
	 * this is necessary to create the object that is used recursively by the original function.
	 *
	 * Generates a list of Page-uid's from $id. List does not include $id itself
	 * The only pages excluded from the list are deleted pages.
	 *
	 * @param	integer		Start page id
	 * @param	integer		Depth to traverse down the page tree.
	 * @param	integer		$begin is an optional integer that determines at which level in the tree to start collecting uid's. Zero means 'start right away', 1 = 'next level and out'
	 * @param	string		Perms clause
	 * @return	string		Returns the list with a comma in the end (if any pages selected!)
	 */
	function extGetTreeList($id,$depth,$begin = 0,$perms_clause)	{
		return t3lib_tsfeBeUserAuth::extGetTreeList($id,$depth,$begin,$perms_clause);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simplesurvey/modfunc1/class.tx_simplesurvey_modfunc1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simplesurvey/modfunc1/class.tx_simplesurvey_modfunc1.php']);
}

?>