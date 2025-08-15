<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
/*
	require_once($_SERVER['DOCUMENT_ROOT'].'/dompdf8/autoload.inc.php');
		require_once($_SERVER["DOCUMENT_ROOT"]."/local/common/class/pdfsign/pdfsign.php");
		use Dompdf\Dompdf;
		use Dompdf\Options;
*/
class studperevod extends iasmuinfo{
	private $perevodtable;
	private $levels;
	private $eduLevelId;
	private $section;
	const arBlocks=array(59);
	public function setparams($params){
		$this->eduLevelId=0;$this->section=0;
		$this->levels=array(182=>"Аспирантура", 181=>"Ординатура",179=>"Бакалавриат",180=>"Специалитет",184=>"Магистратура",186=>"СПО",185=>"НМО");
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		if(isset($params["section"]) && intval($params["section"])>0) $this->section=intval($params["section"]);
		$this->cashGUID="studperevod_".$this->eduLevelId;
	}
	private function parse1C($sData){
		$arDataObjects=json_decode($sData);
		$this->perevodtable=array();
		if (is_array($arDataObjects))
			if (count($arDataObjects)>0){

				foreach ($arDataObjects as $person){
					$prKod=intval(trim($person->prKod));
					$prVid=$person->prVid;
					$prNum=$person->prNum;
					$prDate=date("d.m.Y",strtotime($person->prDate));
					$specCode=$person->specCode;
					$specAdName=$person->specAdName;
					$budLevel=$person->budLevel;
					$osnova=$person->osnova;
					$formaObuch=$person->formaObuch;
					$specLevel=$person->specLevel;
					$specName=$person->specName;
					$key=md5($specCode.$formaObuch.$specAdName);
					if(is_set($this->perevodtable[$key])){
						$row=$this->perevodtable[$key];
					}else {
						$row=array(
							"UF_PRIKAZ_EXP"=>"",
							"UF_PRIKAZ_RES"=>"",
							"UF_PRIKAZ_OUT"=>"",
							"UF_PRIKAZ_TO"=>"",
							"UF_PRIKAZ_EXT"=>"",
							"UF_OPP_CODE"=>$specCode,
							"UF_OPP_ID"=>0,
							"UF_OPP_LEVEL"=>$specLevel,
							"UF_OPP_NAME_1C"=>$specName,
							"UF_FORMOBUCH"=>$formaObuch,
							"UF_NUMBEREXTPEREVOD"=>0,
							"UF_NUMBERRESPEREVOD"=>0,
							"UF_NUMBERTOPEREVOD"=>0,
							"UF_NUMBEROUTPEREVOD"=>0
							);
					}
					if ($this->debug){echo $prKod;}
					if ($prKod==10){//отчисление
						$row["UF_NUMBEREXTPEREVOD"]++;
						$row["UF_PRIKAZ_EXT"].=$prNum." от ".$prDate."; ";
					}elseif($prKod==6){//Перевод в другой вуз
						$row["UF_NUMBEROUTPEREVOD"]++;
						$row["UF_PRIKAZ_OUT"].=$prNum." от ".$prDate."; ";
					}elseif($prKod==1 || $prKod==2 || $prKod==48){//Восстановление
						$row["UF_NUMBERRESPEREVOD"]++;
						$row["UF_PRIKAZ_RES"].=$prNum." от ".$prDate."; ";
					}elseif($prKod==7){//Перевод из другого вуза
						$row["UF_NUMBERTOPEREVOD"]++;
						$row["UF_PRIKAZ_TO"].=$prNum." от ".$prDate."; ";
					}
					$this->perevodtable[$key]=$row;
				}

		}
		if ($this->debug){
				echo '<pre>';
				print_r($this->perevodtable);
				echo '</pre>';
		}
		$this->saveToBD();
	}
	function saveToBD(){
		$values=array();
		foreach($this->perevodtable as $rec){
			$values[]='("'.implode('","',$rec).'")';	
		}
		$sql="INSERT INTO `stud_perevod` (UF_PRIKAZ_EXP,UF_PRIKAZ_RES,UF_PRIKAZ_OUT,UF_PRIKAZ_TO,UF_PRIKAZ_EXT,UF_OPP_CODE,UF_OPP_ID,UF_OPP_LEVEL,UF_OPP_NAME_1C,UF_FORMOBUCH,
						UF_NUMBEREXTPEREVOD,UF_NUMBERRESPEREVOD,UF_NUMBERTOPEREVOD,UF_NUMBEROUTPEREVOD) VALUES ".implode(",",$values);
		//echo $sql;
		$this->BD->query("TRUNCATE TABLE `stud_perevod`");
		$this->BD->query($sql);

	}
	public function getfrom1C(){
		try{
			$wsdl = new SoapClient("http://",
                        	array('login'          => "",
                                	'password'       => "",
					'trace'          => 1,
					'soap_version'   => SOAP_1_2,
					'cache_wsdl'     => WSDL_CACHE_NONE, //WSDL_CACHE_MEMORY, //, WSDL_CACHE_NONE, WSDL_CACHE_DISK or WSDL_CACHE_BOTH
					'encoding'=>'UTF-8',
					'exceptions'=>0));//Dtl0vjcnM
		}catch(SoapFault $e){
			if ($this->debug){
				echo '<hr>Сервис временно недоступен<br>';
				var_dump($e);
			}
		}
		if (is_soap_fault($wsdl)){
			if ($this->debug){
				echo '<hr>Сервис временно недоступен<br>';
			}
		}else{
			$god=intval(date("Y"));
			$mes=intval(date("m"));
			if ($mes<7) $god--;
			if ($mes>8 || $mes<7){
				$dat=$god."-09-01:00:00:00";
				$resultF =  $wsdl->__soapCall("GetStStatus",array("parameters"=>array("Period"=>$dat)));
				if (!is_soap_fault($resultF)){
					$this->parse1C($resultF->return);
				}else{
					if ($this->debug){
						echo '<pre>';
						echo 'ERROR SOAP ';
						var_dump($resultF);
						trigger_error("SOAP Fault: (faultcode: {$resultF->faultcode}, faultstring: {$resultF->faultstring})", E_ERROR);
						echo '</pre>';
					}
				}
			}
			
		}

	}//getfrom1C
	//public function showHtml($buffer=false){
	//}
	public function showHtml($buffer=false){
		$this->cashGUID="studperevod_".$this->eduLevelId;
		$html="";
		$memcache = new Memcache;
		$memcache->addServer('unix:///tmp/memcached.sock', 0);
		$html=$memcache->get($this->cashGUID);
		if(strlen($html)<255 || $html==false || $this->isEditmode()) {
			$html=$this->genHtml();
			$result = $memcache->replace( $this->cashGUID, $html);
			if(!$result) $memcache->set($this->cashGUID, $html, false, $this->cashTime); 
		}//html=
		$memcache->close();
		if($buffer) return $html; else echo $html;
}//showHtml

	function genHtml(){

		$slevel="";$wh='';$html="";
		
		if (in_array($this->section,array_keys($this->levels))){
			$wh=' and  be.IBLOCK_SECTION_ID='.$section;
		}		
		
			//$this->getfrom1C();

			$needNew=array();
			$rows=array();

			$sql="select distinct bip.#SROKOBUCH# as FORM,be.ID as oppID,  be.NAME as SPECNAME,be.IBLOCK_SECTION_ID as SECTION, bip.#EDUCODE# as SPECCODE, bip.#PROFILE# as PROFILE, ";
			$sql.="sp.ID as spID, sp.UF_NUMBEROUTPEREVOD, sp.UF_NUMBERTOPEREVOD,sp.UF_NUMBERRESPEREVOD,sp.UF_NUMBEREXTPEREVOD,sp.UF_FORMOBUCH, sp.UF_OPP_CODE, ";
			$sql.="sp.UF_PRIKAZ_EXT,sp.UF_PRIKAZ_OUT,sp.UF_PRIKAZ_RES,sp.UF_PRIKAZ_EXP,sp.UF_PRIKAZ_TO,sp.UF_OPP_LEVEL,sp.UF_OPP_NAME_1C ";
			$sql.=" from `b_iblock_element` be ";
			$sql.=" left join `b_iblock_element_prop_s#iblock0#`  bip ON bip.IBLOCK_ELEMENT_ID=be.ID ";
			$sql.=" left join `stud_perevod` sp ON sp.UF_OPP_ID=be.ID  ";
			$sql.=' where be.IBLOCK_ID=#iblock0# and be.ACTIVE="Y" '.$wh;
			$sql.=" ORDER BY SECTION,UF_FORMOBUCH, UF_OPP_CODE ";
			$sql=$this->sqltoiblock($sql,$this::arBlocks);
			$speccode=array();
			$DBRecords=$this->BD->query($sql.$wh);
			$html='<table class="studperevod" ><thead>';
			$html.='<tr><th style="text-align: center;" rowspan="2">Код</th>';
			$html.='<th rowspan="2">Наименование специальности, направления подготовки, научной специальности</th>';
			$html.='<th rowspan="2">Образовательная программа, направленность, профиль, шифр и наименование научной специальности</th>';
			$html.='<th rowspan="2">Уровень обучения</th>';
			$html.='<th rowspan="2" itemprop="eduForm">Форма обучения</th>';
			$html.='<th colspan="4">Численность обучающихся, чел.</th>';
			$html.='<th rowspan="2" class="hide"><span class="hide">Приказы, содержащие информацию о результатах перевода, отчисления, восстановления</span></th>';
			$html.='</tr>';
			$html.='<tr>';
			$html.='<th>переведено в другие образовательные организации</th>';
			$html.='<th>переведено из других образовательных организации</th>';
			$html.='<th>восстановлено</th>';
			$html.='<th>отчислено</th>';
			$html.='</tr></thead><tbody>';
			
			while ($row=$DBRecords->fetch()){
				$obform=@unserialize($row["FORM"]);
				if ($obform["s1"]!="") $form="Очная";
				if ($obform["s2"]!="") $form="Заочная";
				if ($obform["s3"]!="") $form="Очно-заочная";
				$profile="отсутствует";
				$sprofile="";
				$scode=$row["SPECCODE"];
				if ($row["PROFILE"]!=NULL) 
					$profile=$row["PROFILE"];
				
				$key=$scode.$form.$profile;
				$sectId=$row["SECTION"];
				if (in_array($sectId,array_keys($this->levels)))
					$level=$this->levels[$sectId];
				else 
					$level="";
				if(!isset($rows[$key])){
					$rows[$key]=$row;			
					$rows[$key]["prezentRow"]=$form==$row["UF_FORMOBUCH"];
					$rows[$key]["form"]=$form;
					$rows[$key]["level"]=$level;
				}
			}
			foreach($rows as $key=>$row){
				if(!$row["prezentRow"]){
					$values=array(
						"UF_NUMBEROUTPEREVOD"=>0,
						"UF_NUMBERTOPEREVOD"=>0,
						"UF_NUMBERRESPEREVOD"=>0,
						"UF_NUMBEREXTPEREVOD"=>0,
						"UF_FORMOBUCH"=>$row["form"],
						"UF_OPP_ID"=>$row["oppID"],
						"UF_OPP_CODE"=>$row["SPECCODE"],
						
						"UF_PRIKAZ_EXT"=>"",
						"UF_PRIKAZ_OUT"=>"",
						"UF_PRIKAZ_RES"=>"",
						"UF_PRIKAZ_EXP"=>"",
						"UF_PRIKAZ_TO"=>"",
						"UF_OPP_LEVEL"=>$row["level"],
						"UF_OPP_NAME_1C"=>$row["SPECNAME"],
					);
					
					$rows[$key]["spID"]=$this->BD->add("stud_perevod", $values);
					if($rows[$key]["spID"]) $row["prezentRow"]=true;
				}
			}
			foreach($rows as $key=>$row){
				if ($row["prezentRow"]){

					$profile="";
					if ($row["PROFILE"]!=NULL && trim($row["PROFILE"])!="") $profile=trim($row["PROFILE"]); else $profile="отсутствует";
					$html.="<tr itemprop=\"eduPerevod\" class=\"maindocselementHB\"  id=\"row{$row['oppID']}\" data-id=\"{$row['spID']}\" data-iblock=\"10\" title=\"{$row["SPECNAME"]}\">\r\n";
					$html.='<td itemprop="eduCode">'.$row["SPECCODE"].'</td>'."\r\n";
					$html.='<td itemprop="eduName">'.$row["SPECNAME"].'</td>'."\r\n";
					$html.='<td style="text-align:left" itemprop="eduProf">'.$profile.'</td>'."\r\n";
					$html.='<td itemprop="eduLevel">'.$row["level"].'</td>'."\r\n";
					$html.='<td itemprop="eduForm">'.$row["form"].'</td>'."\r\n";
					$html.='<td itemprop="numberOut">'.(($row["UF_NUMBEROUTPEREVOD"]!=NULL)?$row["UF_NUMBEROUTPEREVOD"]:'0').'</td>'."\r\n";
					$html.='<td itemprop="numberTo">'.(($row["UF_NUMBERTOPEREVOD"]!=NULL)?$row["UF_NUMBERTOPEREVOD"]:'0').'</td>'."\r\n";
					$html.='<td itemprop="numberRes">'.(($row["UF_NUMBERRESPEREVOD"]!=NULL)?$row["UF_NUMBERRESPEREVOD"]:'0').'</td>'."\r\n";
					$html.='<td itemprop="numberExp">'.(($row["UF_NUMBEREXTPEREVOD"]!=NULL)?$row["UF_NUMBEREXTPEREVOD"]:'0').'</td>'."\r\n";
					$html.='<td class="hide">'."\r\n";
					$html.='<div class="hide" itemprop="OrderAdPerevod">'.(($row["UF_PRIKAZ_TO"]!="")?$row["UF_PRIKAZ_TO"]:'Приказы не издавались').'</div>'."\r\n";
					$html.='<div class="hide" itemprop="OrderOut">'.(($row["UF_PRIKAZ_OUT"]!="")?$row["UF_PRIKAZ_OUT"]:'Приказы не издавались').'</div>'."\r\n";
					$html.='<div class="hide" itemprop="OrderRes">'.(($row["UF_PRIKAZ_RES"]!="")?$row["UF_PRIKAZ_RES"]:'Приказы не издавались').'</div>'."\r\n";
					$html.='<div class="hide" itemprop="OrderExp">'.(($row["UF_PRIKAZ_EXP"]!="")?$row["UF_PRIKAZ_EXP"]:'Приказы не издавались').'</div>'."\r\n";
					$html.="</td>\r\n</tr>\r\n";
					$speccode[]=$key;
				}else{
					$profile="";
					if ($row["PROFILE"]!=NULL && trim($row["PROFILE"])!="") $profile=trim($row["PROFILE"]); else $profile="отсутствует";
					$html.="<tr itemprop=\"eduPerevod\" class=\"maindocselementHB\" id=\"row{$row['oppID']}\" data-opp=\"{$row['oppID']}\" data-id=\"0\" data-iblock=\"10\" title=\"{$item["SPECNAME"]}\">\r\n";
					$html.='<td itemprop="eduCode">'.$row["SPECCODE"].'</td>'."\r\n";
					$html.='<td itemprop="eduName">'.$row["SPECNAME"].'</td>'."\r\n";
					$html.='<td style="text-align:left" itemprop="eduProf">'.$profile.'</td>'."\r\n";
					$html.='<td itemprop="eduLevel">'.$row["level"].'</td>'."\r\n";
					$html.='<td itemprop="eduForm">'.$row["form"].'</td>'."\r\n";
					$html.='<td itemprop="numberOut">0</td>'."\r\n";
					$html.='<td itemprop="numberTo">0</td>'."\r\n";
					$html.='<td itemprop="numberRes">0</td>'."\r\n";
					$html.='<td itemprop="numberExp">0</td>'."\r\n";
					$html.='<td class="hide">'."\r\n";
					$html.='<div class="hide" itemprop="OrderAdPerevod">Приказы не издавались</div>'."\r\n";
					$html.='<div class="hide" itemprop="OrderOut">Приказы не издавались</div>'."\r\n";
					$html.='<div class="hide" itemprop="OrderRes">Приказы не издавались</div>'."\r\n";
					$html.='<div class="hide" itemprop="OrderExp">Приказы не издавались</div>'."\r\n";
					$html.='</td>'."\r\n";
					$html.='</tr>'."\r\n";
					$speccode[]=$key;
					
				}
			}
			$html.='</tbody></table>';
			$god=intval(date("Y"));
			$mes=intval(date("m"));
			if ($mes<7) $god--;
			$dat="01.09.".$god;
			$html.='<br><small>Данные за период с '.$dat.' по '.date("d.m.Y").'</small>';
			return $html;
		}
	}