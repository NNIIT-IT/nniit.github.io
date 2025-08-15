<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");

use asmuinfoclasses;
class priemSchedule extends  iasmuinfo{
	private $adminGroups=array(1);
	private $cashGUID;
	private $open;
	const cashTime=6000;
	private $listitems=array();
	private $god; 
	private $examNames;
	private $eduLevel;
	
private function generateItems(){
	
	$this->listitems=array();
$sql=<<<sql
select  g.UF_NAME as UF_GROUP,g.ID as groupID,
s.id as id,
s.UF_FIS,s.UF_CONTRACT,s.UF_BUDJET,s.UF_PLACER,s.UF_TIME_R,s.UF_PLACE,s.UF_TIME,s.UF_DATER,ex.UF_NAME as exam, 
s.UF_CONS as cons, 
dt.value as UF_DATE,ex.id as examId, s.UF_NOTE as note
from b_hlbd_exam ex 
left join priem_schedule s on ex.id=s.UF_EXAM 
left join priem_schedules_group g on g.id=s.UF_GROUP 
left join priem_schedule_uf_dates dt on dt.id=s.id and YEAR(dt.value)=%GOD%  
where dt.value is not null and UF_PLACE is not null
sql;
	$sql=str_replace("%GOD%", $this->god,$sql);
	$sql.=" and s.UF_LEVEL=".$this->eduLevel;
//echo $sql;
	$this->examNames=array();
	$rez=$this->BD->query($sql);
	while ($ob=$rez->fetch()){
		$groupID=$ob["groupID"];
		if(is_object($ob["UF_DATE"])){
			$uf_date=(string)$ob["UF_DATE"]->format("Y-m-d");
		}else $uf_date="";

		if(is_object($ob["UF_DATER"])){
			$uf_dateR=(string)$ob["UF_DATER"]->format("Y-m-d");
		} else $uf_dateR="";

		$exam=$ob["exam"];
		$examId=intval($ob["examId"]);
		$id=intval($ob["id"]);
		$this->examNames[$examId]=$exam;
		$this->listitems[$groupID]["name"]=$ob["UF_GROUP"];
		if($uf_date!="") {
			if($ob["UF_PLACE"]!="") {
				$this->listitems[$groupID]["first"][$uf_date][$examId]=array($id,$ob["UF_PLACE"],$ob["note"],$ob["UF_TIME"],$ob["cons"]);
			}
		}
		if($uf_dateR!="") {
			if($ob["UF_PLACER"]!="") {
				$this->listitems[$groupID]["second"][$uf_dateR][$examId]=array($id,$ob["UF_PLACER"],$ob["note"],$ob["UF_TIME_R"],$ob["cons"]);
			}
		}
	}

	foreach ($this->listitems as $groupKey=>$groupList){
		$examlist=array();
		foreach ($this->listitems[$groupKey]["first"] as $dateList){
			$examlist=array_merge($examlist,array_keys($dateList));
		}
		if(!is_array($this->listitems[$groupKey]["second"])) $this->listitems[$groupKey]["second"]=array();
		foreach ($this->listitems[$groupKey]["second"] as $dateList){
			$examlist=array_merge($examlist,array_keys($dateList));
		}
		$this->listitems[$groupKey]["exams"]=array_unique($examlist);
	}

}

private function prntbl($tblids){
	$html="";
	$dataurl=urlencode($_SERVER['REQUEST_URI']);
	//РћСЃРЅРѕРІРЅРѕРµ СЂР°СЃРїРёСЃР°РЅРёРµ
	if(count($this->listitems[$tblids]["first"])>0){	
		ksort($this->listitems[$tblids]["first"]);	
		$html.="<div class=\"priemScheduleTable\">";
		//С€Р°РїРєР° С‚Р°Р±Р»РёС†С‹
		$html.="<div class=\"priemScheduleRow\">";
		$html.="<div class=\"priemScheduleCell\">Р”Р°С‚Р°</div>";
		foreach ($this->listitems[$tblids]["exams"] as $examId){
			$examsList=array();
			foreach ($this->listitems[$tblids]["first"] as $xday){
				if(count($examsList)==0){
					if(isset($xday[$examId])) $examsList=$xday[$examId];
				}
			}
			$title="";
			$id_elem="priemSchedule_".$examsList[0]."_".$examId;
			$note=($examsList[2]!="")?"<br><span style=\"font-weight:200\">({$examsList[2]})</span>":"";
			$html.="<div class=\"priemScheduleCell \" title=\"{$title}\" id=\"{$id_elem}\" data-iblock=\"64\" data-id=\"0\" data-url=\"{$dataurl}\" data-level=\"{$this->eduLevel}\">";
			$html.=$this->examNames[$examId].$note."</div>";
		}
		$html.="</div>";	
		//РєРѕРЅРµС† С€Р°РїРєРё С‚Р°Р±Р»РёС†С‹
		//С‚РµР»Рѕ С‚Р°Р±Р»РёС†С‹	
		foreach ($this->listitems[$tblids]["first"] as $dateExam=>$examsList){
				$ardateExam=explode("-",$dateExam);
				$sdateExam=$ardateExam[2].".".$ardateExam[1].".".$ardateExam[0];
				$html.="<div class=\"priemScheduleRow\"><div class=\"priemScheduleCell\" >{$sdateExam}</div>";

					foreach ($this->listitems[$tblids]["exams"] as $examId){

						$examPlace=($examsList[$examId][1])?$examsList[$examId][1]:"-";
						$examPlace=str_replace("\r","<br>",$examPlace);
						$examTime=($examsList[$examId][3])?$examsList[$examId][3]:"";
						$dataid=(intval($examsList[$examId][0])!=0)?$examsList[$examId][0]:0;

						$id_elem="priemSchedule_".$examId."_".$dataid;
						$title="";
						
						$html.="<div class=\"priemScheduleCell \"  >";
						$html.="<div class=\"priemScheduleTime maindocselementHB\" title=\"{$title}\" id=\"{$id_elem}\" data-iblock=\"64\" data-id=\"{$dataid}\" data-url=\"{$dataurl}\" data-level=\"{$this->eduLevel}\">{$examTime}</div>";
						$html.="<div class=\"priemScheduleAddr\">{$examPlace}</div><div class=\"priemScheduleTimeInd\"></div></div>";
					}
				$html.="</div>";							
		}
		//С‚РµР»Рѕ С‚Р°Р±Р»РёС†С‹	РєРѕРЅРµС†
		$html.="</div>";	
	}//РћСЃРЅРѕРІРЅРѕРµ СЂР°СЃРїРёСЃР°РЅРёРµ РєРѕРЅРµС†
	
	//Р”РѕРїРѕР»РЅРёС‚РµР»СЊРЅРѕРµ СЂР°СЃРїРёСЃР°РЅРёРµ
		if(count($this->listitems[$tblids]["second"])>0){	
			$html.="<p><b>СЂРµР·РµСЂРІРЅС‹Р№ РґРµРЅСЊ</b></p>";
			ksort($this->listitems[$tblids]["second"]);	
			$html.="<div class=\"priemScheduleTable\">";
			//С€Р°РїРєР° С‚Р°Р±Р»РёС†С‹
			$html.="<div class=\"priemScheduleRow\">";
			$html.="<div class=\"priemScheduleCell\">Р”Р°С‚Р°</div>";
			foreach ($this->listitems[$tblids]["exams"] as $examId){
				$examsList=array();
				foreach ($this->listitems[$tblids]["second"] as $xday){
					if(count($examsList)==0){
						if(isset($xday[$examId])) $examsList=$xday[$examId];
					}
				}
				$title="";
				$note=($examsList[2]!="")?"<br><span style=\"font-weight:200\">({$examsList[2]})</span>":"";
				$html.="<div class=\"priemScheduleCell maindocselementHB\" title=\"{$title}\" id=\"priemSchedule_{$examsList[0]}\" data-iblock=\"64\" data-id=\"{$examsList[0]}\" data-url=\"{$dataurl}\" data-level=\"{$this->eduLevel}\">";
				$html.=$this->examNames[$examId].$note."</div>";
			}
			$html.="</div>";	
			//РєРѕРЅРµС† С€Р°РїРєРё С‚Р°Р±Р»РёС†С‹
			//С‚РµР»Рѕ С‚Р°Р±Р»РёС†С‹	
			foreach ($this->listitems[$tblids]["second"] as $dateExam=>$examsList){
					$ardateExam=explode("-",$dateExam);
					$sdateExam=$ardateExam[2].".".$ardateExam[1].".".$ardateExam[0];
					$html.="<div class=\"priemScheduleRow\"><div class=\"priemScheduleCell rezerv\" >{$sdateExam}</div>";
						foreach ($this->listitems[$tblids]["exams"] as $examId){
							$examPlace=($examsList[$examId][1])?$examsList[$examId][1]:"-";
							$examPlace=str_replace("\r","<br>",$examPlace);
							$examTime=($examsList[$examId][3])?$examsList[$examId][3]:"";
							$html.="<div class=\"priemScheduleCell\" ><div class=\"priemScheduleTime\">{$examTime}</div><div class=\"priemScheduleAddr\">{$examPlace}</div><div class=\"priemScheduleTimeInd\"></div></div>";

						}
					$html.="</div>";							
			}
			//С‚РµР»Рѕ С‚Р°Р±Р»РёС†С‹	РєРѕРЅРµС†
			$html.="</div>";	
		}//Р”РѕРїРѕР»РЅРёС‚РµР»СЊРЅРѕРµ СЂР°СЃРїРёСЃР°РЅРёРµ РєРѕРЅРµС†


	
	return $html;	
}

public function setparams($params){
		
		$this->open=0;
		if(isset($params["open"])) $this->open=1;
		$this->eduLevel=877;//СЃРїРµС†РёР°Р»РёСЃС‚
		$this->god=intval(date("Y"));
		if(isset($params["god"])) $this->god=intval($params["god"]);
		if(isset($params["eduLevel"])) $this->eduLevel=intval($params["eduLevel"]);
		$this->cashGUID="priemSchedule".$this->god."_".$this->eduLevel;
		if(isset($params["adminGroups"])) {
			$this->adminGroups=$params["adminGroups"];
			
		}

}
private function getFindForm(){
		$html="<div id=\"priemSchedulefind\" class=\"priemSchedulefind\">";
		$html.="<div><input placeholder=\"Р”Р»СЏ РїРѕРёСЃРєР° РёРЅРґРёРІРёРґСѓР°Р»СЊРЅРѕРіРѕ СЂР°СЃРїРёСЃР°РЅРёСЏ РІРІРµРґРёС‚Рµ: Р¤Р°РјРёР»РёСЏ Р