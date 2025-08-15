<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class resultVI extends  iasmuinfo{
	const cashTime=600;
	
		
	private $listitems=array();
	private $eduLevelId=877;
	private $modyfyData=0;
	private $cashGUID;
	private $open;
	private $god;
	function setparams($params){
		$this->open=0;
		if(isset($params["open"])) $this->open=1;
		$this->listitems=array();
		$this->examsList=array();
		$this->eduLevelId=0;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		$this->cashGUID="resultVI_".$this->eduLevelId;
		if(isset($params["god"])) $this->god=intval($params["god"]); else $this->god=date("Y");
	
	}

	function generateItems(){
		$this->listitems=array();
		$this->examsList=array();
		$sql="";
		$asEduLevelStr=array(876=>"Р‘Р°РєР°Р»Р°РІСЂРёР°С‚",877=>"РЎРїРµС†РёР°Р»РёС‚РµС‚",879=>"РћСЂРґРёРЅР°С‚СѓСЂР°",878=>"РђСЃРїРёСЂР°РЅС‚СѓСЂР°");
		$god=$this->god;

		if(intval($this->eduLevelId)>0 && in_array($this->eduLevelId,array_keys($asEduLevelStr))){
			$eduLevel=$asEduLevelStr[$this->eduLevelId];
		}elseif(intval($this->eduLevelId)==0 && in_array($this->eduLevelId,$asEduLevelStr)){
			$eduLevel=$this->eduLevelId;
		}
		$arSqlWhere=array(); 
		$sql="SELECT  distinct UF_USER_FIO,UF_EXAMS, UF_NUMBER_ZAYAV,UF_SNILS, UF_LDELO, ";
		$sql.="	if((UF_OSNOVA='Р‘СЋРґР¶РµС‚РЅС‹Рµ РјРµСЃС‚Р°') and (UF_GROUP like '%СЃРїРµС†РёР°Р»СЊРЅС‹Рµ РїСЂР°РІР°'),1,0) as specpravo FROM `abit_list_by_fio` ";


		$sql.=" WHERE UPPER(UF_EXAMS) like \"%Р­РљР—РђРњР•Рќ%\" and UF_KATEGOR !='Р‘РµР· РІСЃС‚СѓРїРёС‚РµР»СЊРЅС‹С… РёСЃРїС‹С‚Р°РЅРёР№' and UF_GOD='{$god}' ";
		$sql.=" and NOT UF_STATUS LIKE 'РћС‚РѕР·РІР°РЅРѕ' and UF_LEVEL ='{$eduLevel}' order by UF_NUMBER_ZAYAV,UF_SNILS";
		echo "<!--".$this->eduLevelId."-->";
		if($rez=$this->BD->query($sql)){
			while ($rec=$rez->fetch()){
				$exams=json_decode($rec["UF_EXAMS"],true);
				$examsValues=array();
				foreach($exams as $exam){
					if($exam[2]=="Р­РєР·Р°РјРµРЅ"){
						if(!in_array($exam[0],$this->examsList)) $this->examsList[]=$exam[0];
					}
					$examIndex=array_search($exam[0],$this->examsList);
					if($examIndex>-1) {
						if($exam[1]>$examsValues[$examIndex])
							$examsValues[$examIndex]=$exam[1];
					}	
				}
				
				$dataRow=array("UF_USER_FIO"=>$rec["UF_USER_FIO"],"UF_EXAMS"=>$examsValues, "UF_NUMBER_ZAYAV"=>$rec["UF_NUMBER_ZAYAV"],"UF_SNILS"=>$rec["UF_SNILS"],"UF_LDELO"=>$rec["UF_LDELO"]);
				$index=$rec["UF_NUMBER_ZAYAV"]."-".$rec["UF_SNILS "];
				if(count($examsValues)>0) $this->listitems[$index]=$dataRow;
			}
		}
		//print_r($this->listitems);
	}	
	
	public function printTables(){
		
		
		if(count($this->listitems)>0){
			$html="<div class=\"resultVITable\" itemprop=\"resultVI\">";
				$html.="<div class=\"resultVIRow\">";
				$html.="<div class=\"resultVICell\">РќРѕРјРµСЂ Р·Р°СЏРІР»РµРЅРёСЏ,<br>РЅРѕРјРµСЂ Р»РёС‡РЅРѕРіРѕ РґРµР»Р°,<br>РЎРќР