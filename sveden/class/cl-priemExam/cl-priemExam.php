<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class priemExam extends  iasmuinfo{
	const cashTime=6000;
	private $cashGUID;
	private $listitems=array();
	private $eduLevelId=410;
	private $specGroup=1;
	public function setparams($params){
		$this->eduLevelId=0;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		$this->cashGUID="priemExam_".$this->eduLevelId;
		if(isset($params["specGroup"])) $this->specGroup=intval($params["specGroup"]);
	}
	
	public function generateItems(){
$sql=<<<sql
SELECT 
el.PREVIEW_TEXT as Conditions,
el.id as id,
el.name as eduName,
bs.NAME as eduLevel,
ex1.UF_NAME as Exam1,
ex2.UF_NAME as Exam2,
ex3.UF_NAME as Exam3,
ex4.UF_NAME as Exam4,
bie.PROPERTY_396 as ExamBall1,
bie.PROPERTY_398 as ExamBall2,
bie.PROPERTY_400 as ExamBall3,
bie.PROPERTY_375 as ExamBall4,
bie.PROPERTY_371 as FormExam,
bie.PROPERTY_351 as eduCode, 
if(bie.PROPERTY_350 >0,1,0) as adEdu,
if(bie.PROPERTY_352 >0,1,0) as beEdu,
bie.PROPERTY_376 as beForm,
if(bie.PROPERTY_382 is NULL,0,1) as hide,
if(bie.PROPERTY_353 is NULL,"",bie.PROPERTY_353) as eduProfile,
case 
when bie.PROPERTY_377=25863 then "ФГОС ВО 3+"
when bie.PROPERTY_377=25864 then "ФГОС ВО 3++"
else ""
end as beFgos
FROM `b_iblock_element` el
left join `b_iblock_element_prop_s59` bie on bie.IBLOCK_ELEMENT_ID=el.id
left join `b_hlbd_exam` ex1 on ex1.UF_XML_ID=bie.PROPERTY_395
left join `b_hlbd_exam` ex2 on ex2.UF_XML_ID=bie.PROPERTY_397
left join `b_hlbd_exam` ex3 on ex3.UF_XML_ID=bie.PROPERTY_399
left join `b_hlbd_exam` ex4 on ex4.UF_XML_ID=bie.PROPERTY_374
LEFT JOIN `b_iblock_section_element` se on se.IBLOCK_ELEMENT_ID=el.id
LEFT JOIN `b_iblock_section` bs on bs.id=se.IBLOCK_SECTION_ID

WHERE 
el.IBLOCK_ID=59 and 
((((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y") 
#addwhere# 
order by bs.id, beEdu, adEdu, el.PREVIEW_TEXT,bie.PROPERTY_351 
sql;

	$addwhere="";
	if($this->eduLevelId>0){
	 $addwhere=" and bs.id=".$this->eduLevelId;
	} 
	$sql=str_replace("#addwhere#",$addwhere,$sql);

	$this->listitems=array();
	//echo "<!-- $sql -->";
	$maxball=($this->eduLevelId==411)?5:100;
	if($rez=$this->BD->query($sql)){
		while ($rec=$rez->fetch()){
			$item=array();
			$item["eduName"]=$rec["eduName"];
			$item["eduCode"]=$rec["eduCode"];
			$item["eduProfile"]=$rec["eduProfile"];
			$conditionsMd5=md5(preg_replace("/[^a-zA-Zа-яА-Я0-9]/ui","",$rec["Conditions"]));
			

			
			$forms=array();
			//$arforms=unserialize($rec["beForm"]);
			$arforms=$this->unserform($rec["beForm"]);
			if (strlen($arforms[1])>4)

			if (strlen($arforms[1])>4) 
				$forms[1]="очная";
			if (strlen($arforms[2])>4)
				$forms[2]="заочная";
			if (strlen($arforms[3])>4)
				$forms[3]="очно-заочная";
			$item["eduNameExt"]=(implode(", ",$forms))." форм".((count($forms)==1)?"а":"ы")." обучения<br> ";
			if ($rec["beEdu"]==1) $item["eduName"].="<br>(билингвальное обучение)";

			if($this->specGroup==1){
				$index=$item["eduCode"]."_".$rec["beEdu"];
			}else{
				$index=$rec["id"];
			}

			$exams=array();	$examballs=array();
			for($k=1;$k<5;$k++){
				if($rec["Exam".$k]!="" && intval($rec["ExamBall".$k])>0){$exams[$k]=$rec["Exam".$k];$examballs[$k]=intval($rec["ExamBall".$k]);}
			}
			$item["priemExamList"]="<div>".implode("</div><div>",$exams)."</div>";
			$item["priemExamPoints"]="<div>".implode(" / $maxball </div><div>",$examballs)." / $maxball </div>";
			
			$item["priemExamForms"]=str_replace("\r\n","<br>",$rec["FormExam"]);

			if(count($exams)>0){ 
				$this->listitems[$rec["eduLevel"]][$conditionsMd5]["items"][$index]=$item;
				$this->listitems[$rec["eduLevel"]][$conditionsMd5]["condition"]=$rec["Conditions"];			
			}
			
		}
		//asort($this->listitems);
	}
} //generateItems

private function printTable(){

		$thead="<thead><tr>";
		$thead.="<th>Код</th>";
		$thead.="<th>Направление подготовки (специальности)</th>";
		$thead.="<th>Вступительные испытания (в порядке приоритета)</th>";
		$thead.="<th>Минимальное / максимальное количество баллов</th>";

		$thead.="<th>Форма проведения вступительных испытаний, проводимых организацией самостоятельно</th>";
		$thead.="</tr></thead>";
		$tbody="<tbody>";
		$formsName=array(1=>"Очная",2=>"Заочная",3=>"Очно-заочная");

	foreach($this->listitems as $eduLevel=>$levelRec){
			if(count($this->listitems)>1) $tbody.="<tr><th colspan=\"5\" class=\"levelname\">{$eduLevel}</th></tr>";		
		foreach($levelRec as $conditionRec){
			$ctxt="";
			if(trim($conditionRec["condition"])!=""){
				$ctxt="<div style=\"display: table-cell;text-align: left;white-space: nowrap;padding-right: 1em;\">Условия поступления:</div>";
			}
			$tbody.="<tr><th colspan=\"5\">{$ctxt}<div style=\"display: table-cell;text-align: left;\"><p itemprop=\"priemCond\"> {$conditionRec["condition"]}</p></div></th></tr>";

			foreach($conditionRec["items"] as $itemrow){
			
				$tbody.="<tr itemprop=\"priemExam\">";
				$tbody.="<td itemprop=\"eduCode\">".$itemrow["eduCode"]."</td>";
				$tbody.="<td itemprop=\"eduName\">".$itemrow["eduName"]." <span>".$itemrow["eduProfile"]."</span></td>";
				$tbody.="<td itemprop=\"priemExamList\" >{$itemrow["priemExamList"]}</td>";
				$tbody.="<td itemprop=\"priemExamPoints\" >{$itemrow["priemExamPoints"]} </td>";
				$tbody.="<td itemprop=\"priemExamForms\" >{$itemrow["priemExamForms"]}</td>";
				$tbody.="</tr>";
			} 
		}
		$tbody.="</tbody>";
	}//foreach  listitems
	$html="<table class=\"priemExam bvi_voice\"> {$thead} {$tbody} </table>";	
	return $html;
}

	public function showHtml($buffer=false){
		$html="";
		$memcache = new Memcache;
		$memcache->addServer('unix:///tmp/memcached.sock', 0);
		$html=$memcache->get($this->cashGUID);
		if(strlen($html)<255 || $html==false || $this->isEditmode()) {
			$this->generateItems();
			//echo "<pre>"; print_r($this->listitems);echo "</pre>";
			$html=$this->printTable();
			$result = $memcache->replace( $this->cashGUID, $html);
			if( $result == false ){
				
				$memcache->set($this->cashGUID, $html, false, $this->cashTime);
			} 
		}
		$memcache->close();
		if($buffer) return $html; else echo $html;
	}//showHtml

}//class
?>