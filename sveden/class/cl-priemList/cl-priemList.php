<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class priemList extends  iasmuinfo{
	private $cashTime=600;
	private $listitems=array();
	private $listSnils=array();
	private $listitemsTemp=array();
	private $eduLevelId=180;
	private $modyfyData=0;
	private $cashGUID;
	private $open;
	private $god;
	private $orgLevel;
	private $orgName;
	private $countZayav=0;
	function setparams($params){
		$this->open=0;
		if(isset($params["open"])) $this->open=1;
		$this->listitems=array();
		$this->eduLevelId=180;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		if(isset($params["god"])) $this->god=intval($params["god"]); else $this->god=date("Y");
	
	}
	private function getlevelName($levelID){
		//return $this::sectionName($levelID);
	}
	private function getZayavData($level){
$this->orgLevel=array(
		"000007144"=>1,
		"000000786"=>2,
		"000009490"=>3,
		"000006028"=>3,
		"000012671"=>3,
		"000002890"=>3,
		"000000248"=>3,
		"000014037"=>4,
		"000002009"=>4,
		"000000244"=>4,
		"000007760"=>4,
		"000000245"=>5,
		"000013826"=>6,
		"000000020"=>7,
		"000000001"=>8,
		"000000244"=>8,
		"000000000"=>0,
	);
$this->orgName=array(
		/*
		"000007144"=>"Алтайский край",
		"000009490"=>"Республика Алтай",
		"000006028"=>"Республика Алтай",
		"000012671"=>"Республика Тыва",
		"000002890"=>"Республика Хакасия",
		"000000248"=>"Республика Бурятия",
		"000014037"=>"ОАО РЖД",
		"000002009"=>"ОАО РЖД",
		"000000244"=>"ФМБА России",
		"000007760"=>"ФСИН России",
		"000000245"=>"Роспотребнадзор",
		"000013826"=>"Министерство здравоохранения Кемеровской области",
		"000000020"=>"Органы местного самоуправления (не распределенные заявления)",
		*/
		"000000000"=>"",
	);

		$listitemsTemp=array();
$sql=<<<sql
SELECT distinct ID,UF_VIP,UF_VIP_PRAVO,UF_STATUS,UF_SPEC_KOD,UF_FORM,UF_MODIFY,UF_AUTO,UF_ITOG,UF_OTKAZ,UF_PRIKAZ,UF_PRKOM_BEG,UF_PRKOM_END,UF_DOCUM,
UF_PRIKAZ_GUID,UF_NUMBER_ZAYAV,UF_EXAMS,UF_BALL,
if(UF_SNILS="","-",UF_SNILS) as UF_SNILS,UF_LDELO,
if(UF_OUTDOCS="","-",UF_OUTDOCS) as UF_OUTDOCS,
if(UF_INDOCS="","-",UF_INDOCS) as UF_INDOCS,UF_K_GROUP,
if(UF_K_GROUP_KOD is null,"000000000",UF_K_GROUP_KOD) as UF_K_GROUP_KOD,
case 
when ((UF_OSNOVA="Бюджетные места") and (UF_KATEGOR like "%особое право%" OR UF_GROUP like "%особые права%") and not (UF_GROUP like "%специальные%")) then 0 
when ((UF_OSNOVA="Бюджетные места") and (UF_GROUP like "%специальные права")) then 1 
when ((UF_OSNOVA="Бюджетные места") and (UF_KATEGOR="На общих основаниях")) then 3 
when ((UF_OSNOVA="Бюджетные места") and (UF_KATEGOR="Без вступительных испытаний")) then 6 
when ((UF_OSNOVA="С оплатой обучения") and (not( UF_FACULTY ="ФИС" ))) then 4 
when ((UF_OSNOVA="С оплатой обучения") and (UF_FACULTY ="ФИС"))  then 5 
when UF_OSNOVA="Целевой прием" then 2 
else -1
end as tbl,

case 
when UF_EXAMS like "%ЕГЭ%" and not(UF_EXAMS like "%Экзамен%") then "ЕГЭ"
when UF_EXAMS like "%ЕГЭ%" and UF_EXAMS like "%Экзамен%" then "ЕГЭ, Экзамен АГМУ "
when not(UF_EXAMS like "%ЕГЭ%") and UF_EXAMS like "%Экзамен%" then "Экзамен АГМУ "
when (UF_EXAMS like "%аттестат%") then "Аттестат "

else ""
end as xtypexam,
if ((UF_OSNOVA="Бюджетные места") and (UF_GROUP like "%специальные права"),1,0) as specList,

(CURDATE()>=UF_PRKOM_BEG) and (CURDATE()<= UF_PRKOM_END) as xprkomactiv

sql;
		$sql.=" FROM `abit_list_by_fio` WHERE UF_GOD='{$this->god}' and UF_STATUS <> 'Отозвано' and UF_LEVEL=\"$level\" having tbl>=0";
		$this->countZayav=0;
//echo "<!-- xxxx="; echo $sql; echo"-->";
$this->listSnils=array();
		if($rez=$this->BD->query($sql)){
			while ($ob=$rez->fetch()){
				if(isset($this->listSnils[$ob["UF_SNILS"]])){
					$this->listSnils[$ob["UF_SNILS"]]=max($ob["specList"],$this->listSnils[$ob["UF_SNILS"]]);
				}else{
					$this->listSnils[$ob["UF_SNILS"]]=$ob["specList"];
				}
				$this->countZayav++;				
				if($ob["tbl"]==2){
					if(!in_array($ob["UF_K_GROUP_KOD"],array_keys($this->orgLevel))){
						$this->orgLevel[$ob["UF_K_GROUP_KOD"]]=9;
						$this->orgName[$ob["UF_K_GROUP_KOD"]]=$ob["UF_K_GROUP"];
					}
					if(!isset($this->orgName[$ob["UF_K_GROUP_KOD"]]))
                                               $this->orgName[$ob["UF_K_GROUP_KOD"]]= $ob["UF_K_GROUP"];
					$sortOrgIndex=$this->orgLevel[$ob["UF_K_GROUP_KOD"]]."_".$ob["UF_K_GROUP_KOD"];

					$listitemsTemp[$ob["tbl"]][$sortOrgIndex]["orgName"]=$this->orgName[$ob["UF_K_GROUP_KOD"]];
					$listitemsTemp[$ob["tbl"]][$sortOrgIndex]["items"][$ob["ID"]]=$ob;

				}else{
					$listitemsTemp[$ob["tbl"]]["000000000"]["orgName"]="";
					$listitemsTemp[$ob["tbl"]]["000000000"]["items"][$ob["ID"]]=$ob;
				}
			}
		}
		ksort($listitemsTemp[2]);	
		return $listitemsTemp;
	}

	private function generateItemsStep1(){
		$sections=array(424=>"ДПО",179=>"Бакалавриат",180=>"Специалитет",181=>"Ординатура",182=>"Аспирантура",183=>"В разработке",184=>"Магистратура",185=>"НПО",186=>"СПО",);
		$arFilter=array("IBLOCK_ID"=>59,"ACTIVE"=>"Y");
		if($this->eduLevelId>0){
			$arFilter["IBLOCK_SECTION_ID"]=$this->eduLevelId;
		} 
		$connection = Bitrix\Main\Application::getConnection();
		$rez=$this->BD->query("select UF_NAME,UF_XML_ID from b_hlbd_exam");
		$arExams=array();
		
		while($ob=$rez->fetch()){$arExams[$ob["UF_XML_ID"]]=$ob["UF_NAME"];}
		$arSelectFields=array("ID","IBLOCK_ID","IBLOCK_SECTION_ID","NAME","PROPERTY_adEdu","PROPERTY_EduCode","PROPERTY_adEdu","PROPERTY_beEdu","PROPERTY_eduProfile",
		"PROPERTY_SrokObuch","PROPERTY_SrokOPP","PROPERTY_HIDE","PROPERTY_Profile","PROPERTY_language","PROPERTY_FGOS","PROPERTY_Exam1","PROPERTY_Exam2",
		"PROPERTY_Exam3","PROPERTY_Exam4","PROPERTY_Exam1ball","PROPERTY_Exam2ball","PROPERTY_Exam3ball","PROPERTY_Exam4ball",);
		$arTempList=array();
		if(CModule::IncludeModule("iblock")){
			$rez=CIBlockElement::GetList(Array("property_EDUCODE"=>"ASC"),$arFilter,false,false,$arSelectFields);
			
			$arTempRez=array();
			 while($ob = $rez->fetch())  
			   {  
					$arTmpItem=array();
					$arTmpItem["id"]=$ob["ID"];
					$arTmpItem["eduName"]=$ob["NAME"];
					$arTmpItem["eduLevel"]=$sections[$ob["IBLOCK_SECTION_ID"]];
					$arTmpItem["eduLevelID"]=$ob["IBLOCK_SECTION_ID"];
					$arTmpItem["eduCode"]=$ob["PROPERTY_EDUCODE_VALUE"];
					$arTmpItem["adEdu"]=(intval($ob["PROPERTY_BEEDU_ENUM_ID"])>0)?1:0;
					$arTmpItem["beEdu"]=(intval($ob["PROPERTY_BEEDU_ENUM_ID"])>0)?1:0;
					$arTmpItem["beForm"]=$ob["PROPERTY_SROKOBUCH_VALUE"];
					$arTmpItem["dateEnd"]=$ob["PROPERTY_SROKOPP_VALUE"];
					$arTmpItem["hide"]=(intval($ob["PROPERTY_HIDE_ENUM_ID"])>0)?1:0;
					$arTmpItem["eduProfile"]=$ob["PROPERTY_PROFILE_VALUE"];
					$arTmpItem["language"]=$ob["PROPERTY_LANGUAGE_VALUE"];
					$arTmpItem["beFgos"]=$ob["PROPERTY_FGOS_VALUE"];
					$arTmpItem["exam1"]=$arExams[$ob["PROPERTY_EXAM1_VALUE"]];
					$arTmpItem["exam2"]=$arExams[$ob["PROPERTY_EXAM2_VALUE"]];
					$arTmpItem["exam3"]=$arExams[$ob["PROPERTY_EXAM3_VALUE"]];
					$arTmpItem["exam4"]=$arExams[$ob["PROPERTY_EXAM4_VALUE"]];
					$arTmpItem["ExamBall1"]=$arExams[$ob["PROPERTY_EXAM1BALL_VALUE"]];
					$arTmpItem["ExamBall2"]=$arExams[$ob["PROPERTY_EXAM2BALL_VALUE"]];
					$arTmpItem["ExamBall3"]=$arExams[$ob["PROPERTY_EXAM3BALL_VALUE"]];
					$arTmpItem["ExamBall4"]=$arExams[$ob["PROPERTY_EXAM4BALL_VALUE"]];
					$arTempRez[$ob["ID"]]=$arTmpItem;
			    }
		
		
		}
		

	$this->listitems=array();
	//if($rez=$this->BD->query($sql)){
	
		//while ($rec=$rez->fetch()){
		foreach($arTempRez as $rec){
		$oppHeader=array();
			
			$eduLevelID=intval($rec["eduLevelID"]);	
			if($rec["eduLevel"]!="" && $eduLevelID>0) {
	
				$this->listitems[$eduLevelID]["name"]=$rec["eduLevel"];
				$exams=(($rec["exam1"].$rec["exam2"].$rec["exam3"].$rec["exam4"])!="");
				//$arforms=unserialize($rec["beForm"]);
				//$arforms=$rec["beForm"];
				$arforms=$this->unserform($rec["beForm"]);


				$beEdu=$oppHeader["beEdu"];
				$index=md5($rec["eduCode"]."_".$beEdu." ".$rec["eduProfile"]);
				if($eduLevelID==878){$index=md5($rec["eduCode"]."_".$beEdu);}
				if(isset($this->listitems[$eduLevelID]["edu"][$index]["oppHeader"])){
					$oppHeader=$this->listitems[$eduLevelID]["edu"][$index]["oppHeader"];
					if($rec["exam1"]!="" && $oppHeader["exam1"]=="") $oppHeader["exam1"]=$rec["exam1"];
					if($rec["exam2"]!="" && $oppHeader["exam2"]=="") $oppHeader["exam2"]=$rec["exam2"];
					if($rec["exam3"]!="" && $oppHeader["exam3"]=="") $oppHeader["exam3"]=$rec["exam3"];
					if($rec["exam4"]!="" && $oppHeader["exam4"]=="") $oppHeader["exam4"]=$rec["exam4"];
				} else{
					if($rec["exam1"]!="") $oppHeader["exam1"]=$rec["exam1"]; else  $oppHeader["exam1"]="";
					if($rec["exam2"]!="") $oppHeader["exam2"]=$rec["exam2"]; else  $oppHeader["exam2"]="";
					if($rec["exam3"]!="") $oppHeader["exam3"]=$rec["exam3"]; else  $oppHeader["exam3"]="";
					if($rec["exam4"]!="") $oppHeader["exam4"]=$rec["exam4"]; else  $oppHeader["exam4"]="";
					$oppHeader["eduCode"]=$rec["eduCode"];
					$oppHeader["eduName"]=$rec["eduName"];
					$oppHeader["beEdu"]=intval($rec["beEdu"]);
					$oppHeader["eduProfile"]=$rec["eduProfile"];
					$oppHeader["eduForms"]=array();	
				}
					if (strlen($arforms[1])>4){$oppHeader["eduForms"][1]="Очная";}	
					if (strlen($arforms[2])>4){$oppHeader["eduForms"][2]="Заочная";}
					if (strlen($arforms[3])>4){$oppHeader["eduForms"][3]="Очно-заочная";}
		
				$this->listitems[$eduLevelID]["edu"][$index]["oppHeader"]=$oppHeader;
			}
		}
		//asort($this->listitems);
	//}

} 

private function getIndexRec($rec,$oppRec){
		$arExams=json_decode($rec["UF_EXAMS"],true);
		$examList=array($oppRec["exam1"],$oppRec["exam2"],$oppRec["exam3"],$oppRec["exam4"]);
		
		$arExamWeight=array("000","000","000");
		$examsw=array(0,0);
		if($arExams && is_array($arExams))
		foreach($arExams as $exam){
			$c=array_search($exam[0],$examList);
			
			if($c!==false){
				if($c==2){
					$examsw[0]=$exam[1];
				}elseif($c==3){
					$examsw[1]=$exam[1];
				} else $arExamWeight[$c]=trim(sprintf("%03d\n",$exam[1]));
					
			}
		}

		$original2=false;
		$osob=$rec["UF_VIP_PRAVO"]?2:1;
		if($rec["UF_AUTO"]){
			$ufAuto=2;
			$original2=mb_strpos($rec["UF_INDOCS"],"Суперсервис")!==false;
		}else{
			$ufAuto=1;
		}
		$original1=trim($rec["UF_DOCUM"])=="Оригинал";

		$original=($original1 || $original2)?1:0;
		$vibor=max($examsw[0],$examsw[1]);
		$arExamWeight[2]=trim(sprintf("%03d\n",$vibor));
		$examBall=intval($arExamWeight[0])+intval($arExamWeight[1])+$vibor;
		$allball=$rec["UF_BALL"]-$examsw[0]*100-$examsw[1]*100+$vibor*100;
		$rec["UF_BALL"]=$allball;
		return $ufAuto."-".$original."-".trim(sprintf("%05d\n",$allball))."-".trim(sprintf("%03d\n",$examBall))."-".implode("",$arExamWeight).$osob.sprintf("%06d\n",$rec["ID"]);
	}	
	private function getZayavStatus($row){
			//обработка статусов заявления
		$dtbeg=strtotime($row["UF_PRKOM_BEG"]);
		$dtend=strtotime($row["UF_PRKOM_END"]);
		$dtnow=time();

		$PrKomEnamled=($dtbeg<$dtnow) && ($dtend>=$dtnow);
		if($row["UF_STATUS"]=="Подано"){
			if(!$PrKomEnamled){
				if($row["UF_ITOG"]!="") $htm="Зачислен на ".$row["UF_ITOG"];
				else $htm="Не зачислен";
			} else {
				$htm="Подано";
			}
		}elseif($row["UF_STATUS"]=="Отозвано"){
			$htm="Заявление отозвано";
			if($row["UF_OTKAZ"]!="")
				$htm.="<br><small> (причина: {$row["UF_OTKAZ"]})</small>";
			else
				$htm.="<br><small> (причина: заявление поступающего)</small>";
			
		}elseif($row["UF_STATUS"]=="Зачислен"){
			$htm='Зачислен';
			if ($row["UF_PRIKAZ"]!="") {
			
				if($UF_PRIKAZ_GUID!="") {
					$p2=$UF_PRIKAZ_GUID;
				}else {
					$prAr=explode(" ",$row["UF_PRIKAZ"]);
					$prNum=intval(preg_replace("/[^,.0-9]/", '', $prAr[0]));
					$p2=trim(sprintf("%09d\n",$prNum));
					$p2.="-".$prAr[2];
					
				}
				if($prNum!=0) $htm.="<p> <a class=\"link\" target=\"blank\" href=\"/abitur/prikazLoad/?id={$p2}\">приказ {$row["UF_PRIKAZ"]}</a></p>";
				
else $htm="Подано";
				

//str_replace(array("%1%","%2%"),array($p1,$p2),$prikaz);					
			};
		}elseif($row["UF_STATUS"]=="Принято"){
			$htm='Заявление принято';
		}
		return $htm;
		
	}
	private function getExamRec($sExams,$oppRec,$oppRecExams){
		$exams=json_decode($sExams,true);

		
		$html="";$addBall="";$addBallNum=0;$examBall=0;
		if(is_array($exams)) {
			$examscnt=count($exams);
			$examsel=array(0,0);
			for($key=0;$key<$examscnt;$key++){
				$exam0=trim($exams[$key][0]);
				if($this->eduLevelId==3743) 
					$exam1=round($exams[$key][1],6);
				else 
					$exam1=intval($exams[$key][1]);
				$fdn=true;	
				if($exam0==$oppRec["exam1"] && $oppRec["exam1"]!="") {
					$examBall+=$exam1;$oppRecExams[1]="<div class=\"priemListCell numberCell\">{$exam1}</div>";
				}
				elseif($exam0==$oppRec["exam2"] && $oppRec["exam2"]!="") {
					$examBall+=$exam1;$oppRecExams[2]="<div class=\" priemListCell numberCell\">{$exam1}</div>";
				}
				elseif($exam0==$oppRec["exam3"] && $oppRec["exam3"]!="") {
					$examsel[0]=$exam1;$oppRecExams[3]="<div class=\" %c1% priemListCell numberCell\">{$exam1}</div>";
				}
				elseif($exam0==$oppRec["exam4"] && $oppRec["exam4"]!="") {
					$examsel[1]=$exam1;$oppRecExams[4]="<div class=\" %c2% priemListCell numberCell\">{$exam1}</div>";
				}
				else {
					$addBall.=$exam0." - ".$exam1."\r\n";
					$addBallNum+=$exam1;
				}
			}
		}
		
		//Баллы за экзамены 
		
		$examBall+=max($examsel[0],$examsel[1]);
		if($this->eduLevelId!=3743) 
			$oppRecExams[9]="<div class=\"priemListCell examball center\">{$examBall}</div>";
		
		
			if($addBall!="") $oppRecExams[10]="<div class=\"priemListCell center mark\" title=\"{$addBall}\">{$addBallNum}</div>";
		//Всего баллов
		$oppRecExams[11]="<div class=\"priemListCell itogball center\" >".($addBallNum+$examBall)."</div>";
		if($examsel[0]>0 && $examsel[1]>0 && $examsel[0]>$examsel[1]){
			$oppRecExams=str_replace(array("%c1%","%c2%"),array("","gray"),$oppRecExams);
		}
		if($examsel[0]>0 && $examsel[1]>0 && $examsel[0]<$examsel[1]){
			$oppRecExams=str_replace(array("%c1%","%c2%"),array("gray",""),$oppRecExams);
		}

			$oppRecExams=str_replace(array("%c1%","%c2%"),array("",""),$oppRecExams);
		return 	$oppRecExams;
	}	
	
	
	private function generateItemsStep2(){
		$mdata="";
		$tableItempropsKey=array('priemListQuota','priemSpecialQuota','priemListTarget','priemListCommon','priemListContract','priemListContractIn','priemListNoExam');
		$tableItempropsNames=array(
			"Сведения о лицах, поступающих на места в пределах особой квоты",
			"Сведения о лицах, поступающих на места в пределах специальной квоты",
			"Сведения о лицах, поступающих на места в пределах целевой квоты",
			"Сведения о лицах, поступающих на основные места в рамках контрольных цифр",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг ФИС",
			"Сведения о лицах, поступающих на места без вступительных испытаний"
		);



		

$c=microtime(true);
//echo "<br>-----generateItemsStep2-----<br>";
		foreach($this->listitems as $eduLevelID=>$beEduLevelData){
			$beEduLevelName=$this->getlevelName($eduLevelID);
			$listitemsTemp=$this->getZayavData($beEduLevelName);
		

//echo $beEduLevelName." getZayavData :".(microtime(true)-$c)."<br>";$c=microtime(true);

			foreach($beEduLevelData["edu"] as $eduIndex=>$beEduData){

				for($kprop=0;$kprop<=4;$kprop++){

					$this->listitems[$eduLevelID]["edu"][$eduIndex]["tables"][$kprop]=array();
					
					if(!is_array($listitemsTemp[$kprop])) $listitemsTemp[$kprop]=array();

					foreach($listitemsTemp[$kprop] as $orgkey=>$orgTabl){
						$this->listitems[$eduLevelID]["edu"][$eduIndex]["tables"][$kprop][$orgkey]["orgName"]=$orgTabl["orgName"];
						$this->listitems[$eduLevelID]["edu"][$eduIndex]["tables"][$kprop][$orgkey]["items"]=array();
						foreach ($orgTabl["items"] as $key=>$Xrec){


							$cc=trim($beEduData["oppHeader"]["eduCode"])==trim($Xrec["UF_SPEC_KOD"]);
							if($eduLevelID==878 && !$cc ) {
								$cc=strncmp($Xrec["UF_SPEC_KOD"], $beEduData["oppHeader"]["eduProfile"],8)==0;
							}
					
							
							if($cc){//&& $cc3
								//echo "<pre>".$prop;print_r($beEduData["oppHeader"]["eduCode"]);echo"</pre><br>";
								$indexRec=$this->getIndexRec($Xrec,$beEduData["oppHeader"]);

								$this->listitems[$eduLevelID]["edu"][$eduIndex]["tables"][$kprop][$orgkey]["items"][$indexRec]=$Xrec;
								if($Xrec["UF_MODIFY"]>0) $mdata=$Xrec["UF_MODIFY"];
/*
								if(strncmp($mdata,$cdata,8)>0){
									$mdata=$cdata;
								}
*/
								
							}
						}	
					}
					
					
					
					
				}	
			}
//echo $beEduLevelName." foreach :".(microtime(true)-$c)."<br>";$c=microtime(true);
$this->listitemsTemp=array();
		}		
$this->modyfyData=strtotime($mdata);
//echo "<!-- $mdata -->";
//echo "<br>-----end  generateItemsStep2-----<br>";
	}



	function generateItems(){
		$this->listitems=array();
		$this->generateItemsStep1();
//echo "<!--";print_r($this->listitems);echo "-->";
		$this->generateItemsStep2();
	}	
	private function getWhiteExamStr($levelID){
		switch($levelID){
			case 0: 
			case 878:
			case 3743: 
			case 879: return "ожидаются результаты испытаний";
			case 876: 
			case 877: return "Идет поверка баллов в системе ФИС ЕГЭ \r или ожидаются результаты экзаменов АГМУ";
		}
	}

	public function printTables(){

		$tableItempropsKey=array('priemListQuota','priemListTarget','priemListCommon','priemListContract','priemListContractIn','priemListNoExam');
		$tableItempropsNames=array(
			"Сведения о лицах, поступающих на места в пределах особой квоты",
			"Сведения о лицах, поступающих на места в пределах специальной квоты",
			"Сведения о лицах, поступающих на места в пределах целевой квоты",
			"Сведения о лицах, поступающих на основные места в рамках контрольных цифр",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг ФИС",
			"Сведения о лицах, поступающих на места без вступительных испытаний"
		);

		
		$check="&#10003;";
		$html="<div itemprop=\"priemList\">";
		$htmlhRowBegin="<div class=\"priemListRow\">";
		$htmlhRowBegin.="<div class=\"priemListCell\">№</div>";
		$htmlhRowBegin.="<div class=\"priemListCell\">Уникальный код</div>";
		//$htmlhRowBegin.="<div class=\"priemListCell\">Баллов всего</div>";

		$htmlhRowEnd="<div class=\"priemListCell\">Осо&shy;бое пра&shy;во</div>";
		$htmlhRowEnd.="<div class=\"priemListCell\">Вид вступи&shy;тель&shy;ного испы&shy;тания</div>";
		$htmlhRowEnd.="<div class=\"priemListCell\">Согла&shy;сие на зачис&shy;ление</div>";
		$htmlhRowEnd.="<div class=\"priemListCell\">Решение комиссии</div>";
		$htmlhRowEnd.="<div class=\"priemListCell\">Подача докумен&shy;тов / Возврат докумен&shy;тов</div>";
		$htmlhRowEnd.="</div>";
		//echo "<!--";print_r($this->listitems);echo "-->";
			foreach($this->listitems as $eduLevelID=>$beEduLevelData){
				//$html.="<div class=\"priemListLevelName \">".$beEduLevelData["name"]."</div>";
				$emptyList=true;
				foreach($beEduLevelData["edu"] as $eduIndex=>$beEduData){
					$oppRecExams=array();
					

					$htmlSpec="<div class=\"priemListEdu\" id=\"edu_{$beEduData["oppHeader"]["eduCode"]}\"><div class=\"priemListEduName\">".$beEduData["oppHeader"]["eduCode"]." ".$beEduData["oppHeader"]["eduName"];
					if($beEduData["oppHeader"]["eduProfile"]!="") $htmlSpec.="<br><span class=\"gray\"> профиль (направленность): ".$beEduData["oppHeader"]["eduProfile"]."</span>";

					if($beEduData["oppHeader"]["beEdu"]==1) $htmlSpec.=" для иностранных студентов (билингвальное обучение)";
					$htmlSpec.="</div><div>";
					
					
					
					$htmlh=$htmlhRowBegin;
					if($beEduData["oppHeader"]["exam1"]!="") 	{
						$htmlh.="<div class=\"priemListCell\">".$beEduData["oppHeader"]["exam1"]."</div>";
						$oppRecExams[1]="<div class=\"priemListCell numberCell\">-</div>";
					}
					if($beEduData["oppHeader"]["exam2"]!=""){
					 	$htmlh.="<div class=\"priemListCell\">".$beEduData["oppHeader"]["exam2"]."</div>";
						$oppRecExams[2]="<div class=\"priemListCell numberCell\">-</div>";
					}
					if($beEduData["oppHeader"]["exam3"]!="") 	{
						$htmlh.="<div class=\"priemListCell\">".$beEduData["oppHeader"]["exam3"]."</div>";
						$oppRecExams[3]="<div class=\"priemListCell numberCell\">-</div>";
					}
					if($beEduData["oppHeader"]["exam4"]!=""){
					 	$htmlh.="<div class=\"priemListCell\">".$beEduData["oppHeader"]["exam4"]."</div>";
						$oppRecExams[4]="<div class=\"priemListCell numberCell\">-</div>";
					}
					if($this->eduLevelId!=3743){
						$htmlh.="<div class=\"priemListCell\">Итого за экза&shy;мены</div>";
						$oppRecExams[9]="<div class=\"priemListCell numberCell\">-</div>";
					}
					$htmlh.="<div class=\"priemListCell\">Дополни&shy;тель&shy;ные баллы</div>";
					$oppRecExams[10]="<div class=\"priemListCell numberCell\">-</div>";
					$htmlh.="<div class=\"priemListCell\">Всего баллов</div>";
					$oppRecExams[11]="<div class=\"priemListCell numberCell\">-</div>";
					$htmlh.=$htmlhRowEnd;
						
					
	
					
					foreach ($beEduData["tables"] as $prop=>$propTable){
						
						$prop0=($prop!=4)?$prop:3;
						if($beEduData["beEdu"]==1 && $prop!=4) continue;
						$propKey=$tableItempropsKey[$prop0];
						$propName=$tableItempropsNames[$prop];	
						$htmlTable="";
						$cntItemsTable=0;

						foreach ($propTable as $orgKey=>$orgTable){
							if(is_array($orgTable["items"]))
								$cntItemsTable+=count($orgTable["items"]);
						}

						if($cntItemsTable>0){
							$htmlTable.="<div class=\"priemListTable\"><span class=\"priemListTableName link hidedivlink\">{$propName}</span>";
							$htmlTable.="<div class=\"priemListTableBody\" style=\"display:none;\"  itemprop=\"{$propKey}\">";
							$htmlTable.=$htmlh;
							foreach ($propTable as $orgKey=>$orgTable){
								if($orgKey!="000000000" && $orgTable["orgName"]!="" && (count($orgTable["items"])>0)){
									$htmlTable.="<div class=\"priemListRowCaption\" data-org=\"{$orgKey}\" ><div ><div >{$orgTable["orgName"]}</div></div></div>";
									
								} else{
									
								}
								$propData=$orgTable["items"];
	
								if(!empty($propData)){
								
			
									krsort($propData);										
									
									$nnum=0;
									foreach ($propData as $row=>$rowData){
										$nnum++;
										$emptyList=false;
										$htmlTable.="<div class=\"priemListRow\" id=\"$row\">";
										$htmlTable.="<div class=\"priemListCell\">{$nnum}</div>";
										$specList=$this->listSnils[$rowData["UF_SNILS"]];
										if($specList==1){ //специальная квота
											$htmlTable.="<div class=\"priemListCell\">{$rowData["UF_NUMBER_ZAYAV"]}<br>{$rowData["UF_LDELO"]}<br>-</div>";
										} else {
											$htmlTable.="<div class=\"priemListCell\">{$rowData["UF_NUMBER_ZAYAV"]}<br>{$rowData["UF_LDELO"]}<br>{$rowData["UF_SNILS"]}  </div>";
										}

										//$htmlTable.="<div class=\"priemListCell numberCell\">{$rowData["UF_BALL"]}</div>";

										$arExam=$this->getExamRec($rowData["UF_EXAMS"],$beEduData["oppHeader"],$oppRecExams);
										$htmlTable.=implode("\r\n",$arExam);

										$htmlTable.="<div class=\"priemListCell center\">";
											if ($rowData["UF_VIP_PRAVO"] ) {$htmlTable.=$check;}
										$htmlTable.="</div>";

										$htmlTable.="<div class=\"priemListCell\">{$rowData["xtypexam"]}</div>";							

										$htmlTable.="<div class=\"priemListCell center\">";
											if ($rowData["UF_AUTO"]) {
													
													$original=(trim($rowData["UF_DOCUM"])=="Оригинал") || mb_strpos($rowData["UF_INDOCS"],"Суперсервис")!==false;
													if($original) {
														$htmlTable.="<span class=\"green\" title=\"Оригинал документа\">$check</span>";
													}else{
														$htmlTable.=$check;
													}
											}else{
													$htmlTable.="<span class=\"red\">нет</span>";
											}
										$htmlTable.="</div>";

										$htmlTable.="<div class=\"priemListCell \">".($this->getZayavStatus($rowData))."</div>";
										$htmlTable.="<div class=\"priemListCell\">{$rowData["UF_INDOCS"]}/&shy;{$rowData["UF_OUTDOCS"]}</div>";
										$htmlTable.="</div>";
										
									}
									
								} 
							}
							$htmlTable.="<br></div></div>";	
						}else{//$cntItemsTable>0
								$htmlTable.="<div class=\"\">";
									$htmlTable.="<div class=\"\" itemprop=\"{$propKey}\">";
										$htmlTable.="<div class=\"priemListTableName\">{$propName} - заявлений нет</div>";
										//$htmlTable.=$htmlh;	
										$htmlTable.="<div class=\"hideListRow\" style=\"display: none\">";
											$htmlTable.="<div class=\"priemListCell\"></div>";
											$arExam=$this->getExamRec($rowData["UF_EXAMS"],$beEduData["oppHeader"],$oppRecExams);
											$htmlTable.=implode("\r\n",$arExam);
											$htmlTable.=implode("\r\n",$this->getExamRec($rowData["UF_EXAMS"],$beEduData["oppHeader"],$oppRecExams));
											$htmlTable.="<div class=\"priemListCell\"></div>";
											$htmlTable.="<div class=\"priemListCell\"></div>";
											$htmlTable.="<div class=\"priemListCell\"></div>";							
											$htmlTable.="<div class=\"priemListCell\"></div>";
											$htmlTable.="<div class=\"priemListCell\"></div>";
											$htmlTable.="<div class=\"priemListCell\"></div>";
										$htmlTable.="</div>";
									$htmlTable.="</div>";
								$htmlTable.="<br></div>";
						}
						
						$htmlSpec.=$htmlTable;
					}
					
					$htmlSpec.="</div></div>";
					//if(!$emptyList) 
					$html.=$htmlSpec;
				}	
				
			}
		
		return $html.="</div>";
	}
	private function getFindForm(){
		$html="<div id=\"priemListfind\" class=\"priemListfind\">";
		$html.="<div><input placeholder=\"Введите: Фамилия Имя Отчество или номер СНИЛС или номер личного дела\" autocomplete=\"off\" title=\"Поисковая фраза\" type=\"text\" value=\"\"></div>";
		$html.="<div><img class=\"\" title=\"Очистить\" src=\"/local/common/icons/clear1.png\"></div>";
		$html.="<div><img title=\"Поиск\" src=\"/local/common/icons/find.png\"></div>";
		$html.="</div>";
		$html.="<div class=\"priemListfindInfo\"><br><p><i>Примеры поисковых запросов</i>:</p><p>Иванов Иван Иванович</p><p>123-456-789 12</p><p>123456</p></div>";
		return $html;
	}
	public function showHtml($buffer=false){
		$cashGUID=md5("priemList_".$this->god."_".$this->eduLevelId);
		$html="<!--".$this->god."_".$this->eduLevelId."-->";
		$memcache = new Memcache;
		$memcache->addServer('unix:///tmp/memcached.sock', 0);
		$html=$memcache->get($cashGUID);
		if($html==false) {
			//echo "ERR";
		}
		if(strlen($html)<255 || $html==false || $this->isEditmode() ) {//|| $this->modyfyData<(time()-1200)

			$html="";
			$this->generateItems();
			if($this->countZayav>0) $html=$this->getFindForm()."<br>";
			$html.=$this->printTables();
			if($this->modyfyData>0)
				$html.="<span> По состоянию на ".date("d.m.Y H:i",$this->modyfyData)."</span><!-- {$this->modyfyData} -->";
			$result = $memcache->replace($cashGUID, $html);
			if( $result == false ){

				$memcache->set($cashGUID, $html, false, 600);
			} 
		}
		$memcache->close();
		//global $APPLICATION;
		//$APPLICATION->AddHeadScript("/sveden/class/cl-priemList/cl-priemList.js");
		if($buffer) return $html; else echo $html;
			

	}//showHtml
	
}//end class