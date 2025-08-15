<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class priemList1 extends  iasmuinfo{
	const cashTime=600;
	
	private $listitems=array();
	private $listitemsTemp=array();
	private $eduLevelId=877;
	private $modyfyData=0;
	private $cashGUID;
	private $open;
	private $god;
	
	function setparams($params){
		$this->open=0;
		if(isset($params["open"])) $this->open=1;
		$this->listitems=array();
		$this->eduLevelId=877;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		if(isset($params["god"])) $this->god=intval($params["god"]); else $this->god=date("Y");
	
	}
	
	private function getZayavData($level){
		$listitemsTemp=array();
$sql=<<<sql
SELECT ID,UF_VIP,UF_STATUS,UF_SPEC_KOD,UF_FORM,UF_MODIFY,UF_AUTO,UF_ITOG,UF_OTKAZ,UF_PRIKAZ,
UF_PRIKAZ_GUID,UF_NUMBER_ZAYAV,UF_EXAMS,UF_BALL,
if(UF_SNILS="","-",UF_SNILS) as UF_SNILS,
if(UF_OUTDOCS="","-",UF_OUTDOCS) as UF_OUTDOCS,
if(UF_INDOCS="","-",UF_INDOCS) as UF_INDOCS,
case 
when UF_OSNOVA="Бюджетные места" and UF_KATEGOR like"%особое право%" then 0 
when UF_OSNOVA="Бюджетные места" and UF_KATEGOR="На общих основаниях" then 2 
when UF_OSNOVA="Бюджетные места" and UF_KATEGOR="Без вступительных испытаний" then 5 
when UF_OSNOVA="С оплатой обучения" and not( UF_FACULTY ="ФИС" ) then 3 
when UF_OSNOVA="С оплатой обучения" and UF_FACULTY ="ФИС"  then 4 
when UF_OSNOVA="Целевой прием" then 1 
else -1
end as tbl,

case 
when UF_EXAMS like "%ЕГЭ%" and not(UF_EXAMS like "%Экзамен%") then "ЕГЭ"
when UF_EXAMS like "%ЕГЭ%" and UF_EXAMS like "%Экзамен%" then "ЕГЭ, Экзамен АГМУ "
when not(UF_EXAMS like "%ЕГЭ%") and UF_EXAMS like "%Экзамен%" then "Экзамен АГМУ "
else ""
end as xtypexam,
(CURDATE()>=UF_PRKOM_BEG) and (CURDATE()<= UF_PRKOM_END) as xprkomactiv

sql;
		$sql.=" FROM `abit_list_by_fio_test` WHERE UF_GOD='{$this->god}' and UF_STATUS <> 'Отозвано' and UF_LEVEL=\"$level\" having tbl>=0";
//echo $sql;
		if($rez=$this->BD->query($sql)){
			while ($ob=$rez->fetch()){
				$listitemsTemp[$ob["tbl"]][]=$ob;
			}
		}	
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
else ""
end as beFgos,
ex1.UF_NAME as exam1,
ex2.UF_NAME as exam2,
ex3.UF_NAME as exam3,
ex4.UF_NAME as exam4,
bie.PROPERTY_285 as ExamBall1,
bie.PROPERTY_287 as ExamBall2,
bie.PROPERTY_289 as ExamBall3,
bie.PROPERTY_342 as ExamBall4
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
((((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y") 
#and bie.PROPERTY_577 is NULL and bie.PROPERTY_628 is NULL 
#addwhere# 
order by beEdu, adEdu, bs.SORT,bie.PROPERTY_84 
sql;
	$addwhere="";
	if($this->eduLevelId>0){
	 $addwhere=" and bs.id=".$this->eduLevelId;
	} 
	$sql=str_replace("#addwhere#",$addwhere,$sql);
	$this->listitems=array();
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
		
		$arExamWeight=array("000","000","000","000");
		if($arExams && is_array($arExams))
		foreach($arExams as $exam){
			$c=array_search($exam[0],$examList);
			
			if($c!==false){
				$arExamWeight[$c]=trim(sprintf("%03d\n",$exam[1]));
			}
		}
		return "1".trim(sprintf("%03d\n",$rec["UF_BALL"])).implode("",$arExamWeight).sprintf("%06d\n",$rec["ID"]);
	}
	
	private function getZayavStatus($row){
			//обработка статусов заявления
		
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
					$p2=sprintf("%09d\n",$prNum)."-".$prAr[2];
					
				}
				$htm.="<p> <a class=\"link\" target=\"blank\" href=\"/abitur/prikazLoad/?id={$p2}\">приказ {$row["UF_PRIKAZ"]}</a></p>";
				//str_replace(array("%1%","%2%"),array($p1,$p2),$prikaz);					
			};
		}elseif($row["UF_STATUS"]=="Принято"){
			$htm='Заявление принято';
		}
		return $htm;
		
	}
	private function getExamRec($sExams,$oppRec,$oppRecExams){
		$exams=json_decode($sExams,true);

		
		
		$html="";$addBall="";$examscnt=count($exams);
		if(is_array($exams)) {
			for($key=0;$key<$examscnt;$key++){
				$exam0=trim($exams[$key][0]);
				$exam1=intval($exams[$key][1]);
				$fdn=true;	
				if($exam0==$oppRec["exam1"] && $oppRec["exam1"]!="") $oppRecExams[1]="<div class=\"priemListCell numberCell\">{$exam1}</div>";
				elseif($exam0==$oppRec["exam2"] && $oppRec["exam2"]!="") $oppRecExams[2]="<div class=\"priemListCell numberCell\">{$exam1}</div>";
				elseif($exam0==$oppRec["exam3"] && $oppRec["exam3"]!="") $oppRecExams[3]="<div class=\"priemListCell numberCell\">{$exam1}</div>";
				elseif($exam0==$oppRec["exam4"] && $oppRec["exam4"]!="") $oppRecExams[4]="<div class=\"priemListCell numberCell\">{$exam1}</div>";
				else $addBall.=$exam0." - ".$exam1."<br>";
			}
		}
		if($addBall!="") $oppRecExams[5]="<div class=\"priemListCell\">{$addBall}</div>";
		return 	$oppRecExams;
	}	
	
	
	private function generateItemsStep2(){
		$mdata="0";
		$tableItempropsKey=array('priemListQuota','priemListTarget','priemListCommon','priemListContract','priemListContractIn','priemListNoExam');
		$tableItempropsNames=array(
			"Сведения о лицах, поступающих на места в пределах особой квоты",
			"Сведения о лицах, поступающих на места в пределах целевой квоты",
			"Сведения о лицах, поступающих на основные места в рамках контрольных цифр",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг ФИС",
			"Сведения о лицах, поступающих на места без вступительных испытаний"
		);



		
$c=microtime(true);
//echo "<br>-----generateItemsStep2-----<br>";
		foreach($this->listitems as $eduLevelID=>$beEduLevelData){
			switch($eduLevelID){
				case 0: 
				case 878:$beEduLevelName="Аспирантура";
				case 879: $beEduLevelName="Ординатура";
				case 876: $beEduLevelName="Бакалавриат";
				case 877: $beEduLevelName="Специалитет";
			}
			$listitemsTemp=$this->getZayavData($beEduLevelName);
			
//echo $beEduLevelName." getZayavData :".(microtime(true)-$c)."<br>";$c=microtime(true);

			foreach($beEduLevelData["edu"] as $eduIndex=>$beEduData){

				for($kprop=0;$kprop<=4;$kprop++){

					$this->listitems[$eduLevelID]["edu"][$eduIndex]["tables"][$kprop]=array();
					foreach($listitemsTemp[$kprop] as $key=>$Xrec){
						//$cc3=in_array($Xrec["UF_FORM"],$beEduData["oppHeader"]["eduForms"]);

						$cc=$beEduData["oppHeader"]["eduCode"]==$Xrec["UF_SPEC_KOD"];
						if($eduLevelID==878 && !$cc ) {
							$cc=strncmp($Xrec["UF_SPEC_KOD"], $beEduData["oppHeader"]["eduProfile"],8)==0;
						}
				
						
						if($cc){//&& $cc3
							//echo "<pre>".$prop;print_r($beEduData["oppHeader"]["eduCode"]);echo"</pre><br>";
							$indexRec=$this->getIndexRec($Xrec,$beEduData["oppHeader"]);
							$this->listitems[$eduLevelID]["edu"][$eduIndex]["tables"][$kprop][$indexRec]=$Xrec;
					
							
							
							
						}

					}
					
					$cdata=current($listitemsTemp)["UF_MODIFY"];
					if(strncmp($mdata,$cdata,8)>0){
						$mdata=$cdata;
					}
				}	
			}
//echo $beEduLevelName." foreach :".(microtime(true)-$c)."<br>";$c=microtime(true);
$this->listitemsTemp=array();
		}		
$this->modyfyData=strtotime($mdata);
//echo "<br>-----end  generateItemsStep2-----<br>";
	}

	function generateItems(){
$c=microtime(true);

		$this->listitems=array();


		$this->generateItemsStep1();
//echo "generateItemsStep1 :".(microtime(true)-$c);$c=microtime(true);

		$this->generateItemsStep2();
//echo "generateItemsStep2 :".(microtime(true)-$c);$c=microtime(true);


	}	
	private function getWhiteExamStr($levelID){
		switch($levelID){
			case 0: 
			case 878:
			case 879: return "ожидаются результаты испытаний";
			case 876: 
			case 877: return "Идет поверка баллов в системе ФИС ЕГЭ \r или ожидаются результаты экзаменов АГМУ";
		}
	}
	public function printTables(){
		$tableItempropsKey=array('priemListQuota','priemListTarget','priemListCommon','priemListContract','priemListContractIn','priemListNoExam');
		$tableItempropsNames=array(
			"Сведения о лицах, поступающих на места в пределах особой квоты",
			"Сведения о лицах, поступающих на места в пределах целевой квоты",
			"Сведения о лицах, поступающих на основные места в рамках контрольных цифр",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг",
			"Сведения о лицах, поступающих на места по договорам об оказании платных образовательных услуг ФИС",
			"Сведения о лицах, поступающих на места без вступительных испытаний"
		);

		//echo "<pre>";
		//print_r($this->listitems);
		//echo "</pre>";
		$check="✓";
		$html="<div itemprop=\"priemList1\">";
		$htmlhRowBegin="<div class=\"priemListRow\">";
		$htmlhRowBegin.="<div class=\"priemListCell\">№</div>";
		$htmlhRowBegin.="<div class=\"priemListCell\">Номер заявления / СНИЛС <span class=\"priemListInfo\" title=\"Национальный индивидуальный идентификационный номер физического лица\">i</span></div>";
		$htmlhRowBegin.="<div class=\"priemListCell\">Баллов всего</div>";

		$htmlhRowEnd="<div class=\"priemListCell\">Решение комиссии</div>";
		$htmlhRowEnd.="<div class=\"priemListCell\">Вид вступительного испытания</div>";
		$htmlhRowEnd.="<div class=\"priemListCell\">Подача документов / Возврат документов</div>";

		$htmlhRowEnd.="</div>";
		
			foreach($this->listitems as $eduLevelID=>$beEduLevelData){
				//$html.="<div class=\"priemListLevelName \">".$beEduLevelData["name"]."</div>";
				$emptyList=true;
				foreach($beEduLevelData["edu"] as $eduIndex=>$beEduData){
					$oppRecExams=array();
					

					$htmlSpec="<div class=\"priemListEduName\">".$beEduData["oppHeader"]["eduName"];
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
					$htmlh.="<div class=\"priemListCell\">Дополнительные баллы</div>";
					$oppRecExams[5]="<div class=\"priemListCell numberCell\">-</div>";
					$htmlh.=$htmlhRowEnd;
						
					
	
					
					foreach ($beEduData["tables"] as $prop=>$propData){
						$prop0=($prop!=4)?$prop:3;
						if($beEduData["beEdu"]==1 && $prop!=4) continue;
						$propKey=$tableItempropsKey[$prop0];
						$propName=$tableItempropsNames[$prop];	
						$htmlTable="";
						if(!empty($propData)){
							
							$htmlTable="<div class=\"priemListTable hidedivlink\"><span class=\"priemListTableName link\">{$propName}</span></div>";
							$htmlTable.="<div class=\"priemListTableBody\" style=\"display:none;\"  itemprop=\"{$propKey}\">";
							$htmlTable.=$htmlh;
							krsort($propData);	
							$nnum=0;
							foreach ($propData as $row=>$rowData){
								$nnum++;
								$emptyList=false;
								$htmlTable.="<div class=\"priemListRow\">";
								$htmlTable.="<div class=\"priemListCell\">{$nnum}</div>";
								$htmlTable.="<div class=\"priemListCell\">{$rowData["UF_NUMBER_ZAYAV"]} / {$rowData["UF_SNILS"]}</div>";
								$htmlTable.="<div class=\"priemListCell numberCell\">{$rowData["UF_BALL"]}</div>";
								$arExam=$this->getExamRec($rowData["UF_EXAMS"],$beEduData["oppHeader"],$oppRecExams);
								$htmlTable.=implode("\r\n",$arExam);
								$htmlTable.="<div class=\"priemListCell \">".$this->getZayavStatus($rowData)."</div>";
								$htmlTable.="<div class=\"priemListCell\">{$rowData["xtypexam"]}</div>";							
								$htmlTable.="<div class=\"priemListCell\">{$rowData["UF_INDOCS"]}/{$rowData["UF_OUTDOCS"]}</div>";
								$htmlTable.="</div>";
							}
							$htmlTable.="<br></div>";	
						} else{
							//print_r($propData);
							
							$htmlTable.="<div class=\"\" itemprop=\"{$propKey}\"><div class=\"priemListTableName\">{$propName} - заявлений нет</div>";
							//$htmlTable.=$htmlh;	
							$htmlTable.="<div class=\"priemListRow \" style=\"visibility: hidden\">";
							$htmlTable.="<div class=\"priemListCell\"></div><div class=\"priemListCell\"></div>";
							$htmlTable.=$this->getExamRec($rowData["UF_EXAMS"],$beEduData["oppHeader"],$oppRecExams);
							$htmlTable.=implode("\r\n",$this->getExamRec($rowData["UF_EXAMS"],$beEduData["oppHeader"],$oppRecExams));
							$htmlTable.="<div class=\"priemListCell\"></div>";
							$htmlTable.="<div class=\"priemListCell\"></div>";
							$htmlTable.="<div class=\"priemListCell\"></div>";							
							$htmlTable.="<div class=\"priemListCell\"></div>";
							$htmlTable.="</div>";
							$htmlTable.="</div>";
						}
						$htmlSpec.=$htmlTable;
					}
					
					$htmlSpec.="</div>";
					//if(!$emptyList) 
						$html.=$htmlSpec;
				}	
				
			}
		
		return $html.="</div>";
	}
	private function getFindForm(){
		$html="<div class=\"priemListfind\">";
		$html.="<div><input placeholder=\"Для поиска заявления введите ФИО или СНИЛС \" autocomplete=\"off\" title=\"Поисковая фраза\" type=\"text\" value=\"\"></div>";
		$html.="<div><img class=\"\" title=\"Очистить\" src=\"/local/common/icons/clear1.png\"></div>";
		$html.="<div><img title=\"Поиск\" src=\"/local/common/icons/find.png\"></div>";
		$html.="</div>";
		return $html;
	}
	public function showHtml($buffer=false){
		$cashGUID=md5("priemList_test_".$this->eduLevelId);
		$html="";
		$memcache = new Memcache;
		$memcache->addServer('unix:///tmp/memcached.sock', 0);
		$html=false;//$memcache->get($cashGUID);
		if($html==false) {
			echo "ERR";
		}
		if(strlen($html)<255 || $html==false || $this->isEditmode() ) {//|| $this->modyfyData<(time()-1200)

			$html="";
$c=microtime(true);
			$this->generateItems();
echo "generateItems :".(microtime(true)-$c);
$c=microtime(true);

			$html=$this->getFindForm()."<br>";
			$html.=$this->printTables();
echo "printTables :".(microtime(true)-$c);$c=microtime(true);
			$result = $memcache->replace($cashGUID, $html);
//echo "memcache replace:".(microtime(true)-$c);$c=microtime(true);
			if( $result == false ){
			
				$html.="<span> По состоянию на ".date("Y.m.d H:i:s",$this->modyfyData)."</span>";		
				$memcache->set($cashGUID, $html, false, $this->cashTime);
			} 
		}
		$memcache->close();
		//$html.="<link rel=\"stylesheet\" href=\"https://asmu.ru/sveden/class/cl-priemList/cl-priemList.css\">";
		global $APPLICATION;
		$APPLICATION->AddHeadScript("/sveden/class/cl-priemList1/cl-priemList1.js");
		if($buffer) return $html; else echo $html;
			

	}//showHtml
	
}//end class