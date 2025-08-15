<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class priemKonkurs extends  iasmuinfo{
	private $cashTime=600;
	private $listSnils=array();
	private $listitems=array();
	private $listitemsTemp=array();
	private $eduLevelId=877;
	private $modyfyData=0;
	private $cashGUID;
	private $open;
	private $god;
	private $orgLevel;
	private $orgName;
	private $countZayav=0;
	private $showTables=array();
	private $tableItempropsNames;
	private $tableItempropsKey;
	function setparams($params){
		$this->open=0;
		if(isset($params["open"])) $this->open=1;
		$this->listitems=array();
		$this->eduLevelId=877;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		if(isset($params["god"])) $this->god=intval($params["god"]); else $this->god=date("Y");
		$this->showTables=array(4);
		
		$this->tableItempropsNames=array(
			"Сведения о лицах, поступающих на места в пределах особой квоты",
			"Сведения о лицах, поступающих на места в пределах специальной квоты",
			"Сведения о лицах, поступающих на места в пределах целевой квоты",
			"Сведения о лицах, поступающих на основные места в рамках контрольных цифр",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг ",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг (для поступающих на основании внутренних испытаний АГМУ)",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг ФИС",
			"Сведения о лицах, поступающих на места без вступительных испытаний",

		);
		$this->tableItempropsKey=array('priemListQuota','priemSpecQuota','priemListTarget','priemListCommon','priemListContract','priemListContract','priemListContractIn','priemListNoExam');
	}
	private function getlevelName($levelID){
		if($levelID==877) return "Специалитет";
		elseif ($levelID==878) return "Аспирантура";
		elseif ($levelID==879) return "Ординатура";
                elseif($levelID===3743)return "СПО"; 
		else return "Специалитет";
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
		"000012671"=>"Министерство здравоохранения Республики Тыва",
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
SELECT distinct ID,UF_VIP,UF_VIP_PRAVO, UF_STATUS,UF_SPEC_KOD,UF_FORM,UF_MODIFY,UF_AUTO,UF_ITOG,UF_OTKAZ,UF_PRIKAZ,UF_PRKOM_BEG,UF_PRKOM_END,
UF_PRIKAZ_GUID,UF_NUMBER_ZAYAV,UF_EXAMS,UF_BALL,
if(UF_SNILS="","-",UF_SNILS) as UF_SNILS,UF_LDELO,
if(UF_OUTDOCS="","-",UF_OUTDOCS) as UF_OUTDOCS,
if(UF_INDOCS="","-",UF_INDOCS) as UF_INDOCS,UF_K_GROUP,
if(UF_K_GROUP_KOD is null,"000000000",UF_K_GROUP_KOD) as UF_K_GROUP_KOD,
case 
when UF_OSNOVA="Бюджетные места" and (UF_KATEGOR like "%особое право%" OR UF_GROUP like "%особые права%") and not (UF_GROUP like "%специальные%") then 0 
when UF_OSNOVA="Бюджетные места" and (UF_GROUP like "%специальн%") then 1 
when UF_OSNOVA="Бюджетные места" and UF_KATEGOR="На общих основаниях" then 3 
when UF_OSNOVA="С оплатой обучения" and not( UF_FACULTY ="ФИС" ) and  (UF_EXAMS like "%Экзамен%") then 5 
when UF_OSNOVA="С оплатой обучения" and not( UF_FACULTY ="ФИС" ) then 4 
when UF_OSNOVA="С оплатой обучения" and UF_FACULTY ="ФИС"  then 6 
when UF_OSNOVA="Бюджетные места" and UF_KATEGOR="Без вступительных испытаний" then 7 
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
		$sql.=" FROM `abit_list_by_fio` WHERE UF_GOD='{$this->god}'  and UF_LEVEL=\"$level\" ";
		$sql.=" having tbl>-1 ";
		$this->countZayav=0;
global $USER;
//if($USER->isAdmin() ) echo $sql;

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

					$sortOrgIndex=$this->orgLevel[$ob["UF_K_GROUP_KOD"]]."_".$ob["UF_K_GROUP_KOD"];
					if(!isset($this->orgName[$ob["UF_K_GROUP_KOD"]]))
                                               $this->orgName[$ob["UF_K_GROUP_KOD"]]= $ob["UF_K_GROUP"];
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
$sql=<<<sql
SELECT 
el.id as id,
el.name as eduName,
bs.NAME as eduLevel,
bs.id as eduLevelID,
bie.PROPERTY_84 as eduCode, 
if(bie.PROPERTY_578 >0,1,0) as adEdu,
if(bie.PROPERTY_577 >0,1,0) as beEdu,
bie.PROPERTY_90 as beForm,
bie.PROPERTY_119 as dateEnd,
if(bie.PROPERTY_628 is NULL,0,1) as hide,
if(bie.PROPERTY_121 is NULL,"",bie.PROPERTY_121) as eduProfile,
if(bie.PROPERTY_510 is null,"Русский",bie.PROPERTY_510) as language,
case 
when bie.PROPERTY_603=25863 then "ФГОС ВО 3+"
when bie.PROPERTY_603=25864 then "ФГОС ВО 3++"
when bie.PROPERTY_603=47256 then "ФГОС ВО 2021"
else ""
end as beFgos,
ex1.UF_NAME as exam1,
ex2.UF_NAME as exam2,
ex3.UF_NAME as exam3,
ex4.UF_NAME as exam4,
if(bie.PROPERTY_285 is NULL,0,bie.PROPERTY_285) as ExamBall1,
if(bie.PROPERTY_287 is NULL,0,bie.PROPERTY_287 ) as ExamBall2,
if(bie.PROPERTY_289 is NULL,0,bie.PROPERTY_289 ) as ExamBall3,
if(bie.PROPERTY_342 is NULL,0,bie.PROPERTY_343 ) as ExamBall4,

if(bie.PROPERTY_268>0 or bie.PROPERTY_581>0 or bie.PROPERTY_269>0 or bie.PROPERTY_270>0 or bie.PROPERTY_271>0 or bie.PROPERTY_583>0 or bie.PROPERTY_584>0,1,0) as actual

FROM `b_iblock_element` el
left join `b_iblock_element_prop_s104` bie on bie.IBLOCK_ELEMENT_ID=el.id
left join `b_hlbd_exam` ex1 on ex1.UF_XML_ID=bie.PROPERTY_284 
left join `b_hlbd_exam` ex2 on ex2.UF_XML_ID=bie.PROPERTY_286 
left join `b_hlbd_exam` ex3 on ex3.UF_XML_ID=bie.PROPERTY_288 
left join `b_hlbd_exam` ex4 on ex4.UF_XML_ID=bie.PROPERTY_342
LEFT JOIN `b_iblock_section_element` se on se.IBLOCK_ELEMENT_ID=el.id
LEFT JOIN `b_iblock_section` bs on bs.id=se.IBLOCK_SECTION_ID
WHERE 
el.IBLOCK_ID=104 and 
((((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y" ) 
#addwhere# 
having actual=1
order by beEdu, adEdu, bs.SORT,bie.PROPERTY_84 
sql;
//#and bie.PROPERTY_577 is NULL and bie.PROPERTY_628 is NULL 
	$addwhere="";
	if($this->eduLevelId>0){
	 $addwhere=" and bs.id=".$this->eduLevelId;
	} 
	$sql=str_replace("#addwhere#",$addwhere,$sql);
	$this->listitems=array();
	$this->modyfyData=date("d.m.Y H:i");

	if($rez=$this->BD->query($sql)){
		while ($rec=$rez->fetch()){
			$oppHeader=array();
			
			$eduLevelID=intval($rec["eduLevelID"]);	
			if($rec["eduLevel"]!="" && $eduLevelID>0) {
	
				$this->listitems[$eduLevelID]["name"]=$rec["eduLevel"];
				$exams=(($rec["exam1"].$rec["exam2"].$rec["exam3"].$rec["exam4"])!="");
				$arforms=unserialize($rec["beForm"]);
				$beEdu=$oppHeader["beEdu"];
				$index=md5($rec["eduCode"]."_".$beEdu." ".$rec["eduProfile"]);
				if($eduLevelID==878){$index=md5($rec["eduCode"]."_".$beEdu);}
				if(isset($this->listitems[$eduLevelID]["edu"][$index]["oppHeader"])){
					$oppHeader=$this->listitems[$eduLevelID]["edu"][$index]["oppHeader"];
					if($rec["exam1"]!="" && $oppHeader["exam1"]=="") {
						$oppHeader["exam1"]=$rec["exam1"];
						$oppHeader["examBall1"]=intval($rec["examBall1"]);
					}
					if($rec["exam2"]!="" && $oppHeader["exam2"]==""){
 $oppHeader["exam2"]=$rec["exam2"];
$oppHeader["examBall2"]=intval($rec["examBall2"]);
}
					if($rec["exam3"]!="" && $oppHeader["exam3"]=="") {
$oppHeader["exam3"]=$rec["exam3"];
$oppHeader["examBall3"]=intval($rec["examBall3"]);
}
					if($rec["exam4"]!="" && $oppHeader["exam4"]=="") {
$oppHeader["exam4"]=$rec["exam4"];
$oppHeader["examBall4"]=intval($rec["examBall4"]);
}
				} else{
					if($rec["exam1"]!="") {
$oppHeader["exam1"]=$rec["exam1"];$oppHeader["examBall1"]=intval($rec["ExamBall1"]);
} else  $oppHeader["exam1"]="";
					if($rec["exam2"]!="") {
$oppHeader["exam2"]=$rec["exam2"]; $oppHeader["examBall2"]=$rec["ExamBall2"];}
else  $oppHeader["exam2"]="";
					if($rec["exam3"]!=""){
 $oppHeader["exam3"]=$rec["exam3"]; $oppHeader["examBall3"]=$rec["ExamBall3"];}
else  $oppHeader["exam3"]="";
					if($rec["exam4"]!="") {
$oppHeader["exam4"]=$rec["exam4"]; $oppHeader["examBall4"]=$rec["ExamBall4"];}
else  $oppHeader["exam4"]="";
					$oppHeader["eduCode"]=trim($rec["eduCode"]);
					$oppHeader["eduName"]=trim($rec["eduName"]);
					$oppHeader["beEdu"]=intval($rec["beEdu"]);
					$oppHeader["eduProfile"]=$rec["eduProfile"];
					$oppHeader["eduForms"]=array();	
				}
					if ($arforms["s1"]!=""){$oppHeader["eduForms"][1]="Очная";}	
					if ($arforms["s2"]!=""){$oppHeader["eduForms"][2]="Заочная";}
					if ($arforms["s3"]!=""){$oppHeader["eduForms"][3]="Очно-заочная";}
		
				$this->listitems[$eduLevelID]["edu"][$index]["oppHeader"]=$oppHeader;
			}
		}
		//asort($this->listitems);
	}

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
		$osob=$rec["UF_VIP_PRAVO"]?2:1;
		$vibor=max($examsw[0],$examsw[1]);
		$arExamWeight[2]=trim(sprintf("%03d\n",$vibor));
		$allball=$rec["UF_BALL"]-$examsw[0]*100-$examsw[1]*100+$vibor*100;
		return "1-".trim(sprintf("%06d\n",$allball))."-".implode("",$arExamWeight).$osob.sprintf("%06d\n",$rec["ID"]);
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
				$htm.="<p> <a class=\"link\" target=\"blank\" href=\"/abitur/prikazLoad/?id={$p2}\">приказ {$row["UF_PRIKAZ"]}</a></p>";
				//str_replace(array("%1%","%2%"),array($p1,$p2),$prikaz);					
			};
		}elseif($row["UF_STATUS"]=="Принято"){
			$htm='Заявление принято';
		}
		return $htm;
		
	}
	private function getExamRec2($sExams,$oppRec,$oppRecExams){
		$exams=json_decode($sExams,true);

		$html="";$addBall="";$addBallNum=0;$examBall=0;$examsel=array(0,0);
		if(is_array($exams)) {
			$examscnt=count($exams);
				$examscntValid=0;
				if($oppRec["examBall1"]>0) $examscntValid++;
				if($oppRec["examBall2"]>0) $examscntValid++;
				if($oppRec["examBall3"]>0) $examscntValid++;
				if($oppRec["examBall4"]>0) $examscntValid++;
				$konkursOk=0;
				$examselected=0;
			for($key=0;$key<$examscnt;$key++){
				$exam0=trim($exams[$key][0]);
	
				if($this->eduLevelId==3743) 
					$exam1=round($exams[$key][1],6);
				else 
					$exam1=intval($exams[$key][1]);

				$fdn=true;	
				if($exam0==$oppRec["exam1"] && $oppRec["exam1"]!="") {
					$examBall+=$exam1;
					$oppRecExams[1]="<div class=\"priemListCell numberCell\">{$exam1}</div>";
					if($exam1>=$oppRec["examBall1"]) {$konkursOk++;};
				}
				elseif($exam0==$oppRec["exam2"] && $oppRec["exam2"]!="") {
					$examBall+=$exam1;
					$oppRecExams[2]="<div class=\"priemListCell numberCell\">{$exam1}</div>";
					if($exam1>=$oppRec["examBall2"]) {$konkursOk++;};

				}
				elseif($exam0==$oppRec["exam3"] && $oppRec["exam3"]!="") {
					$examsel[0]=$exam1;
					$oppRecExams[3]="<div class=\"%c% priemListCell numberCell\">{$exam1}</div>";
					if($exam1>=$oppRec["examBall3"]) {$konkursOk++;};
				}
				elseif($exam0==$oppRec["exam4"] && $oppRec["exam4"]!="") {
					$examsel[1]=$exam1;
					$oppRecExams[4]="<div class=\" %c%  priemListCell numberCell\">{$exam1}</div>";
					if($exam1>=$oppRec["examBall4"]) {$konkursOk++;};

				}
				else {
					$addBall.=$exam0." + ".$exam1."\r\n";
					$addBallNum+=$exam1;
				}
			}
		}
		$examBall+=max($examsel[0],$examsel[1]);

		if($examsel[0]>0 && $examsel[1]>0 && $examsel[0]>$examsel[1]){
			$oppRecExams[3]=str_replace("%c%","",$oppRecExams[3]);
			$oppRecExams[4]=str_replace("%c%","gray",$oppRecExams[4]);
		}
		if($examsel[0]>0 && $examsel[1]>0 && $examsel[0]<$examsel[1]){
			$oppRecExams[3]=str_replace("%c%","gray",$oppRecExams[3]);
			$oppRecExams[4]=str_replace("%c%","",$oppRecExams[4]);
		}

			$oppRecExams[3]=str_replace("%c%","",$oppRecExams[3]);
			$oppRecExams[4]=str_replace("%c%","",$oppRecExams[4]);


		if($this->eduLevelId!=3743) $oppRecExams[9]="<div class=\"priemListCell examball center\">{$examBall} </div>";
		if($addBall!="") $oppRecExams[10]="<div class=\"priemListCell center mark\" title=\"{$addBall}\">{$addBallNum}</div>";
		$oppRecExams[11]="<div class=\"priemListCell itogball center\" >".($addBallNum+$examBall)."</div>";
		if($konkursOk>=$examscntValid || $konkursOk>=3) {return 	$oppRecExams;
} else {return false;}
	}	
	
	
	private function generateItemsStep2(){
		$mdata="";
		
//$tableItempropsKey=array(4=>'priemListContract');
		



		

$c=microtime(true);
//echo "<br>-----generateItemsStep2-----<br>";
		foreach($this->listitems as $eduLevelID=>$beEduLevelData){
			$beEduLevelName=$this->getlevelName($eduLevelID);
			$listitemsTemp=$this->getZayavData($beEduLevelName);
			

//echo $beEduLevelName." getZayavData :".(microtime(true)-$c)."<br>";$c=microtime(true);

			foreach($beEduLevelData["edu"] as $eduIndex=>$beEduData){

				for($kprop=0;$kprop<=7;$kprop++){

					$this->listitems[$eduLevelID]["edu"][$eduIndex]["tables"][$kprop]=array();
					
					if(!is_array($listitemsTemp[$kprop])) $listitemsTemp[$kprop]=array();

					foreach($listitemsTemp[$kprop] as $orgkey=>$orgTabl){
						$this->listitems[$eduLevelID]["edu"][$eduIndex]["tables"][$kprop][$orgkey]["orgName"]=$orgTabl["orgName"];
				

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
echo "<!-- $mdata -->";
//echo "<br>-----end  generateItemsStep2-----<br>";
	}



	function generateItems(){
		$this->listitems=array();
		$this->generateItemsStep1();

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

//$tableItempropsKey=array('priemListQuota','priemListTarget','priemListCommon','priemListContract','priemListContract','priemListContractIn','priemListNoExam');
//$tableItempropsKey=array(3=>'priemListContract');
		

		
		$check="&#10003;";
		$html="<div itemprop=\"priemKonkurs\">";
		$htmlhRowBegin="<div class=\"priemKonkursRow\">";
		$htmlhRowBegin.="<div class=\"priemKonkursCell\">№</div>";
		$htmlhRowBegin.="<div class=\"priemKonkursCell\">Уникальный код</div>";
		//$htmlhRowBegin.="<div class=\"priemKonkursCell\">Баллов всего</div>";

		$htmlhRowEnd="<div class=\"priemKonkursCell\">Пре&shy;иму&shy;щест&shy;вен&shy;ное пра&shy;во</div>";
		//$htmlhRowEnd.="<div class=\"priemKonkursCell\">Вид вступи&shy;тель&shy;ного испы&shy;тания</div>";
		$htmlhRowEnd.="<div class=\"priemKonkursCell\">Согла&shy;сие на зачис&shy;ление</div>";
		//$htmlhRowEnd.="<div class=\"priemKonkursCell\">Решение комиссии</div>";
		//$htmlhRowEnd.="<div class=\"priemKonkursCell\">Подача докумен&shy;тов / Возврат докумен&shy;тов</div>";
		$htmlhRowEnd.="</div>";
		
			foreach($this->listitems as $eduLevelID=>$beEduLevelData){
				//$html.="<div class=\"priemKonkursLevelName \">".$beEduLevelData["name"]."</div>";
				$emptyList=true;
				foreach($beEduLevelData["edu"] as $eduIndex=>$beEduData){
					$oppRecExams=array();
					

					$htmlSpec="<div class=\"priemKonkursEdu\" id=\"edu_{$beEduData["oppHeader"]["eduCode"]}\"><div class=\"priemKonkursEduName\">".$beEduData["oppHeader"]["eduCode"]." ".$beEduData["oppHeader"]["eduName"];
					if($beEduData["oppHeader"]["eduProfile"]!="") $htmlSpec.="<br>    <span class=\"gray\">(направленность (профиль): ".$beEduData["oppHeader"]["eduProfile"].")</span>";
					if($beEduData["oppHeader"]["beEdu"]==1) $htmlSpec.=" для иностранных студентов (билингвальное обучение)";
					$htmlSpec.="</div><div>";
					
					
					
					$htmlh=$htmlhRowBegin;
					if($beEduData["oppHeader"]["exam1"]!="") 	{
						$htmlh.="<div class=\"priemKonkursCell\">".$beEduData["oppHeader"]["exam1"]."</div>";
						$oppRecExams[1]="<div class=\"priemKonkursCell numberCell\">-</div>";
					}
					if($beEduData["oppHeader"]["exam2"]!=""){
					 	$htmlh.="<div class=\"priemKonkursCell\">".$beEduData["oppHeader"]["exam2"]."</div>";
						$oppRecExams[2]="<div class=\"priemKonkursCell numberCell\">-</div>";
					}
					if($beEduData["oppHeader"]["exam3"]!="") 	{
						$htmlh.="<div class=\"priemKonkursCell\">".$beEduData["oppHeader"]["exam3"]."</div>";
						$oppRecExams[3]="<div class=\"priemKonkursCell numberCell\">-</div>";
					}
					if($beEduData["oppHeader"]["exam4"]!=""){
					 	$htmlh.="<div class=\"priemKonkursCell\">".$beEduData["oppHeader"]["exam4"]."</div>";
						$oppRecExams[4]="<div class=\"priemKonkursCell numberCell\">-</div>";
					}
					if($this->eduLevelId!=3743) {
						$htmlh.="<div class=\"priemKonkursCell\">Итого за экза&shy;мены</div>";
						$oppRecExams[9]="<div class=\"priemKonkursCell numberCell\">-</div>";
					}
					$htmlh.="<div class=\"priemKonkursCell\">Баллы за индиви&shy;дуаль&shy;ные дости&shy;жения</div>";
					$oppRecExams[10]="<div class=\"priemKonkursCell numberCell\">-</div>";
					$htmlh.="<div class=\"priemKonkursCell\">Всего баллов</div>";
					$oppRecExams[11]="<div class=\"priemKonkursCell numberCell\">-</div>";
					$htmlh.=$htmlhRowEnd;
						
					
	
					
					foreach ($beEduData["tables"] as $prop=>$propTable){
						
						$prop0=($prop!=6)?$prop:4;
						if($beEduData["beEdu"]==1 && $prop!=5) continue;
						$propKey=$this->tableItempropsKey[$prop0];
						$propName=$this->tableItempropsNames[$prop];	
						$htmlTable="";
						$cntItemsTable=0;
						$cntItemsTable2=0;
						foreach ($propTable as $orgKey=>$orgTable){
							if(is_array($orgTable["items"]))
								$cntItemsTable+=count($orgTable["items"]);
						}
						
						if($cntItemsTable>0){
							$htmlTable.="<div class=\"priemKonkursTable\"><span class=\"priemKonkursTableName link hidedivlink\">{$propName}</span>";
							$htmlTable.="<div class=\"priemKonkursTableBody\" style=\"display:none;\"  itemprop=\"{$propKey}\">";
							$htmlTable.=$htmlh;

							foreach ($propTable as $orgKey=>$orgTable){
								$propData=array_filter($orgTable["items"]);

								
								
	
								if(!empty($propData)){
									$cntItems=count($propData);$htmlTableHH="";
									if($orgKey!="000000000" && $orgTable["orgName"]!="" && ($cntItems>0)){
										
										$htmlTableHH="<div class=\"priemKonkursRowCaption\" data-orgKey=\"{$orgKey} {$cntItems}\"><div><div>{$orgTable["orgName"]}</div></div></div>";
										
									}
									$cntItemsTable2++;
									krsort($propData);										
									
									$nnum=0;
									$htmlTableB="";
									foreach ($propData as $row=>$rowData){
										$arExam=$this->getExamRec2($rowData["UF_EXAMS"],$beEduData["oppHeader"],$oppRecExams);	

										if($arExam!==false){
											$nnum++;
											$emptyList=false;
											$otziv=$rowData["UF_STATUS"]=='Отозвано';
											$strikeout=($otziv)?"strikeout":"";
											$htmlTableB.="<div class=\"priemKonkursRow $strikeout\" id=\"{$row}\">";
											$htmlTableB.="<div class=\"priemKonkursCell \">{$nnum}</div>";
											$specList=$this->listSnils[$rowData["UF_SNILS"]];
											if($specList==1){ //специальная квота

												$htmlTableB.="<div class=\"priemKonkursCell\">{$rowData["UF_NUMBER_ZAYAV"]}<br>{$rowData["UF_LDELO"]}</div>";
											}
											else{			
												$htmlTableB.="<div class=\"priemKonkursCell\">{$rowData["UF_NUMBER_ZAYAV"]}<br>{$rowData["UF_LDELO"]}<br>{$rowData["UF_SNILS"]}</div>";
											}		
											//$htmlTableB.="<div class=\"priemKonkursCell numberCell\">{$rowData["UF_BALL"]}</div>";
	
											
											$htmlTableB.=implode("\r\n",$arExam);
	
											$htmlTableB.="<div class=\"priemKonkursCell center\">";
												if ($rowData["UF_VIP_PRAVO"]) {$htmlTableB.=($otziv)?" ":$check;}
											$htmlTableB.="</div>";
	
											//$htmlTable.="<div class=\"priemKonkursCell\">{$rowData["xtypexam"]}</div>";							
	
											$htmlTableB.="<div class=\"priemKonkursCell center\">";
											 if($otziv){
													$htmlTableB.="Заявление отозвано";
											}else{
												if ($rowData["UF_AUTO"])  {$htmlTableB.=$check;}ELSE {$htmlTableB.="<span class=\"red\">нет</span>";}
											}			
											$htmlTableB.="</div>";
	
											//$htmlTableB.="<div class=\"priemKonkursCell \">".($this->getZayavStatus($rowData))."</div>";
											//$htmlTableB.="<div class=\"priemKonkursCell\">{$rowData["UF_INDOCS"]}/&shy;{$rowData["UF_OUTDOCS"]}</div>";
											$htmlTableB.="</div>";
										}
									}//foreach
									if($htmlTableB!="") 
										$htmlTable.=$htmlTableHH.$htmlTableB; 
									else 
										$htmlTable.="<div class=\"priemKonkursRowCaption\"><div><div></div></div></div>";
									//$htmlTable.="<br></div></div>";	
								
									
									
										
								} //empty propData
								
								
									
							}
							$htmlTable.="</div></div>";			
							
						}//cntItemsTable>0
						/*
						else{//$cntItemsTable>0
								
							$arExam=$this->getExamRec2($rowData["UF_EXAMS"],$beEduData["oppHeader"],$oppRecExams);
							if($arExam!==false){
							$htmlTable.="<div class=\"\">";
									//$htmlTable.="<div class=\"\" itemprop=\"{$propKey}\">";
											//$htmlTable.="<div class=\"priemKonkursTableName\">{$propName} - заявлений нет</div>";
										//$htmlTable.=$htmlh;	
										
											$htmlTable.="<div class=\"priemKonkursCell\"></div>";

												$htmlTable.="<div class=\"hideKonkursRow\" style=\"display: none\">";
												$htmlTable.=implode("\r\n",$arExam);
												$htmlTable.=implode("\r\n",$this->getExamRec2($rowData["UF_EXAMS"],$beEduData["oppHeader"],$oppRecExams));
												$htmlTable.="<div class=\"priemKonkursCell\"></div>";
												$htmlTable.="<div class=\"priemKonkursCell\"></div>";
												$htmlTable.="<div class=\"priemKonkursCell\"></div>";							
												$htmlTable.="<div class=\"priemKonkursCell\"></div>";
												$htmlTable.="<div class=\"priemKonkursCell\"></div>";
												$htmlTable.="<div class=\"priemKonkursCell\"></div>";
												$htmlTable.="</div>";
									//$htmlTable.="</div>";
								//$htmlTable.="<br></div>";
							}
						}
						*/
						
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
		$html="<div id=\"priemKonkursfind\" class=\"priemKonkursfind\">";
		$html.="<div><input placeholder=\"Введите: Фамилия Имя Отчество или номер СНИЛС или номер личного дела\" autocomplete=\"off\" title=\"Поисковая фраза\" type=\"text\" value=\"\"></div>";
		$html.="<div><img class=\"\" title=\"Очистить\" src=\"/local/common/icons/clear1.png\"></div>";
		$html.="<div><img title=\"Поиск\" src=\"/local/common/icons/find.png\"></div>";
		$html.="</div>";
		$html.="<div class=\"priemKonkursfindInfo\"><br><p><i>Примеры поисковых запросов</i>:</p><p>Иванов Иван Иванович</p><p>123-456-789 12</p><p>123456</p></div>";
		return $html;
	}
	public function showHtml($buffer=false){
		$cashGUID=md5("priemKonkurs_".$this->god."_".$this->eduLevelId);
		$html="<!-- ".$this->modyfyData." -->";
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
				$html.="<br><br><span> По состоянию на ".date("d.m.Y H:i",$this->modyfyData)."</span><!-- {$this->modyfyData} -->";
			$result = $memcache->replace($cashGUID, $html);
			if( $result == false ){

				$memcache->set($cashGUID, $html, false, 600);
			} 
		}
		$memcache->close();
		//global $APPLICATION;
		$html=str_replace("</div>","</div>\r\n",$html);
		//$APPLICATION->AddHeadScript("/sveden/class/cl-priemList/cl-priemList.js");
		if($buffer) return $html; else echo $html;
			

	}//showHtml
	
}//end class