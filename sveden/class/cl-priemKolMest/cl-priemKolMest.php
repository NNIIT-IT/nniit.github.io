<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class priemKolMest extends  iasmuinfo{
	const cashTime=6000;
	private $cashGUID;
	private $listitems=array();
	private $eduLevelId=410;
	private $specGroup=1;
	public function setparams($params){
		$this->eduLevelId=0;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		$this->cashGUID="priemVacantT1_".$this->eduLevelId;
		if(isset($params["specGroup"])) $this->specGroup=intval($params["specGroup"]);
	}
	private function setRowData($eduLevel,$index1,$index2,$tmprow){

			if(!isset($this->listitems[$eduLevel][$index1][$index2])){
				$item=$tmprow;
				$item["NumberBFVacant"]=intval($tmprow["NumberBFVacant"]);
				$item["NumberBFVacantZ"]=intval($tmprow["NumberBFVacantZ"]);
				$item["NumberBFVacantOZ"]=intval($tmprow["NumberBFVacantOZ"]);
				$item["NumberBRVacant"]=intval($tmprow["NumberBRVacant"]);
				$item["NumberBMVacant"]=intval($tmprow["NumberBMVacant"]);
				$item["NumberPVacant"]=intval($tmprow["NumberPVacant"]);
				$item["NumberPVacantZ"]=intval($tmprow["NumberPVacantZ"]);
				$item["NumberPVacantOZ"]=intval($tmprow["NumberPVacantOZ"]);
				$item["NumberFVacant"]=intval($tmprow["NumberFVacant"]);
				$item["NumberVacantInv"]=intval($tmprow["NumberVacantInv"]);
				$item["NumberVacantInvZ"]=intval($tmprow["NumberVacantInvZ"]);
				$item["NumberVacantInvOZ"]=intval($tmprow["NumberVacantInvOZ"]);
				$item["NumberInVacant"]=intval($tmprow["NumberInVacant"]);
				$item["NumberBFVacantC"]=intval($tmprow["NumberBFVacantC"]);
				$item["NumberBFVacantCZ"]=intval($tmprow["NumberBFVacantCZ"]);
				$item["NumberBFVacantCOZ"]=intval($tmprow["NumberBFVacantCOZ"]);
				$item["NumberBRVacantC"]=intval($tmprow["NumberBRVacantC"]);
				$item["NumberBMVacantC"]=intval($tmprow["NumberBMVacantC"]);
				$item["NumberPVacantC"]=intval($tmprow["NumberPVacantC"]);
				$item["NumberBFVacantS"]=intval($tmprow["NumberBFVacantS"]);
				$item["NumberVacantInvF"]=intval($tmprow["NumberVacantInvF"]);
				$item["NumberBFVacantCF"]=intval($tmprow["NumberBFVacantCF"]);
				$item["NumberBFVacantSF"]=intval($tmprow["NumberBFVacantSF"]);

			} else {
				$item=$this->listitems[$eduLevel][$index1][$index2];
				$item["NumberBFVacant"]+=intval($tmprow["NumberBFVacant"]);
				$item["NumberBFVacantZ"]+=intval($tmprow["NumberBFVacantZ"]);
				$item["NumberBFVacantOZ"]+=intval($tmprow["NumberBFVacantOZ"]);
				$item["NumberBRVacant"]+=intval($tmprow["NumberBRVacant"]);
				$item["NumberBMVacant"]+=intval($tmprow["NumberBMVacant"]);
				$item["NumberPVacant"]+=intval($tmprow["NumberPVacant"]);
				$item["NumberPVacantZ"]+=intval($tmprow["NumberPVacantZ"]);
				$item["NumberPVacantOZ"]+=intval($tmprow["NumberPVacantOZ"]);
				$item["NumberFVacant"]+=intval($tmprow["NumberFVacant"]);
				$item["NumberVacantInv"]+=intval($tmprow["NumberVacantInv"]);
				$item["NumberVacantInvZ"]+=intval($tmprow["NumberVacantInvZ"]);
				$item["NumberVacantInvOZ"]+=intval($tmprow["NumberVacantInvOZ"]);
				$item["NumberInVacant"]+=intval($tmprow["NumberInVacant"]);
				$item["NumberBFVacantC"]+=intval($tmprow["NumberBFVacantC"]);
				$item["NumberBFVacantCZ"]+=intval($tmprow["NumberBFVacantCZ"]);
				$item["NumberBFVacantCOZ"]+=intval($tmprow["NumberBFVacantCOZ"]);
				$item["NumberBRVacantC"]+=intval($tmprow["NumberBRVacantC"]);
				$item["NumberBMVacantC"]+=intval($tmprow["NumberBMVacantC"]);
				$item["NumberPVacantC"]+=intval($tmprow["NumberPVacantC"]);
				$item["NumberBFVacantS"]+=intval($tmprow["NumberBFVacantS"]);
				$item["NumberVacantInvF"]+=intval($tmprow["NumberVacantInvF"]);
				$item["NumberBFVacantCF"]+=intval($tmprow["NumberBFVacantCF"]);
				$item["NumberBFVacantSF"]+=intval($tmprow["NumberBFVacantSF"]);

			}
		$this->listitems[$eduLevel][$index1][$index2]=$item;
		//echo "<pre>";print_r($item); echo "</pre>";
	}
	private function numpadeg($n){
		if(in_array($n,array(2,3,4,22,23,24,32,33,34,42,43,44,52,53,54,62,63,64,72,73,74,82,83,84,92,93,94)) ) $okon="а";
		if(in_array($n,array(1,21,31,41,51,61,71,81,91))) $okon="о";
		return $okon;
	}
	public function generateItems(){
$sql=<<<sql
SELECT 
el.id as id,
el.name as eduName,
bs.NAME as eduLevel,
bie.PROPERTY_351 as eduCode, 
if(bie.PROPERTY_350 >0,1,0) as adEdu,
if(bie.PROPERTY_352 >0,1,0) as beEdu,
bie.PROPERTY_376 as beForm,
bie.PROPERTY_383 as dateEnd,
if(bie.PROPERTY_382 is NULL,0,1) as hide,
if(bie.PROPERTY_353 is NULL,"",bie.PROPERTY_353) as eduProfile,
if(bie.PROPERTY_387 is null,"Русский",bie.PROPERTY_387) as language,
case 
when bie.PROPERTY_377=215 then "ФГОС ВО 3+"
when bie.PROPERTY_377=216 then "ФГОС ВО 3++"
when bie.PROPERTY_377=217 then "ФГОС ВО 2021"
else ""
end as beFgos,
bie.PROPERTY_354 as NumberBFVacant,
bie.PROPERTY_355 as NumberBFVacantZ,
bie.PROPERTY_356 as NumberBFVacantOZ,
bie.PROPERTY_357 as NumberBRVacant,
bie.PROPERTY_358 as NumberBMVacant,
bie.PROPERTY_359 as NumberPVacant,
bie.PROPERTY_360 as NumberPVacantZ,
bie.PROPERTY_361 as NumberPVacantOZ,
bie.PROPERTY_362 as NumberFVacant,
bie.PROPERTY_367 as NumberVacantInv,
bie.PROPERTY_368 as NumberVacantInvZ,
bie.PROPERTY_369 as NumberVacantInvOZ,
bie.PROPERTY_385 as NumberInVacant,
bie.PROPERTY_389 as NumberBFVacantC,
bie.PROPERTY_390 as NumberBFVacantCZ,
bie.PROPERTY_391 as NumberBFVacantCOZ,
bie.PROPERTY_392 as NumberBRVacantC,
bie.PROPERTY_393 as NumberBMVacantC,
bie.PROPERTY_394 as NumberPVacantC,
bie.PROPERTY_370 as NumberBFVacantS,
bie.PROPERTY_413 as NumberVacantInvF,
bie.PROPERTY_414 as NumberBFVacantCF,
bie.PROPERTY_415 as NumberBFVacantSF 
FROM `b_iblock_element` el
left join `b_iblock_element_prop_s59` bie on bie.IBLOCK_ELEMENT_ID=el.id
LEFT JOIN `b_iblock_section_element` se on se.IBLOCK_ELEMENT_ID=el.id
LEFT JOIN `b_iblock_section` bs on bs.id=se.IBLOCK_SECTION_ID

WHERE 
el.IBLOCK_ID=59 and 
((((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y") 

#addwhere# 
order by beEdu, adEdu, bs.SORT,bie.PROPERTY_351 
sql;

	$addwhere="";
	if($this->eduLevelId>0){
	 $addwhere=" and bs.id=".$this->eduLevelId;
	} 
	$sql=str_replace("#addwhere#",$addwhere,$sql);

	$this->listitems=array();
	if($rez=$this->BD->query($sql)){
		while ($rec=$rez->fetch()){
			$eduName=$rec["eduName"];
			$eduCode=$rec["eduCode"];
			if($rec["eduProfile"]!="") $eduName.=",<br>".$rec["eduProfile"];
			$tmprow=(array)$rec;
			//$arforms=unserialize($rec["beForm"]);
			$arforms=$this->unserform($rec["beForm"]);
			if (strlen($arforms[1])>4){
				$tmprow["learningTerm"]=$arforms[1];
				$tmprow["eduForm"]="Очная";
				if($this->specGroup==1) {
					$index=($eduCode);
					//$this->setRowData($rec["eduLevel"],1,$index,$tmprow);
					if ($this->eduLevelId==180) $this->setRowData($rec["eduLevel"],1,$index,$tmprow);
					if ($this->eduLevelId==181) $this->setRowData($rec["eduLevel"],$index,1,$tmprow);
					if ($this->eduLevelId==182) $this->setRowData($rec["eduLevel"],$index,1,$tmprow);

				}
				else {
					$index=($tmprow["id"]."_1");

					if ($this->eduLevelId==180) $this->listitems[$rec["eduLevel"]][1][$index]=$tmprow;
					if ($this->eduLevelId==181) $this->listitems[$rec["eduLevel"]][$index][1]=$tmprow;
					if ($this->eduLevelId==182) $this->listitems[$rec["eduLevel"]][$index][1]=$tmprow;

					//$this->listitems[$rec["eduLevel"]][1][$index]=$tmprow;
				}
			}
			if (strlen($arforms[2])>4){
				$tmprow["learningTerm"]=$arforms[2];
				$tmprow["eduForm"]="Заочная";
				if($this->specGroup==1) {
					$index=($eduCode);
					//$this->setRowData($rec["eduLevel"],2,$index,$tmprow);
					if ($this->eduLevelId==181) $this->setRowData($rec["eduLevel"],2,$index,$tmprow);
					if ($this->eduLevelId==181) $this->setRowData($rec["eduLevel"],$index,2,$tmprow);
					if ($this->eduLevelId==182) $this->setRowData($rec["eduLevel"],$index,2,$tmprow);
				}
				else {
					$index=($tmprow["id"]."_2");
					if ($this->eduLevelId==180) $this->listitems[$rec["eduLevel"]][2][$index]=$tmprow;
					if ($this->eduLevelId==181) $this->listitems[$rec["eduLevel"]][$index][2]=$tmprow;
					if ($this->eduLevelId==182) $this->listitems[$rec["eduLevel"]][$index][2]=$tmprow;
					//$this->listitems[$rec["eduLevel"]][2][$index]=$tmprow;
				}
			}
			if (strlen($arforms[3])>4){
				$tmprow["learningTerm"]=$arforms[3];
				$tmprow["eduForm"]="Очно-заочная";
				
				if($this->specGroup==1) {
					$index=($eduCode);
					//$this->setRowData($rec["eduLevel"],3,$index,$tmprow);
					if ($this->eduLevelId==180) $this->setRowData($rec["eduLevel"],3,$index,$tmprow);
					if ($this->eduLevelId==181) $this->setRowData($rec["eduLevel"],$index,3,$tmprow);
					if ($this->eduLevelId==182) $this->setRowData($rec["eduLevel"],$index,3,$tmprow);
				}
				else {
					$index=($tmprow["id"]."_3");
					if ($this->eduLevelId==180) $this->listitems[$rec["eduLevel"]][3][$index]=$tmprow;
					if ($this->eduLevelId==181) $this->listitems[$rec["eduLevel"]][$index][3]=$tmprow;
					if ($this->eduLevelId==182) $this->listitems[$rec["eduLevel"]][$index][3]=$tmprow;
					//$this->listitems[$rec["eduLevel"]][3][$index]=$tmprow;

				}
			}


			

		}
		//asort($this->listitems);
	}
} //generateItems

private function printTable180(){//специалитет

		$thead="<thead data-tbl=\"180\">";
		$thead.="<tr><th rowspan=\"2\">Код</th><th rowspan=\"2\">Направление подготовки (специальности)</th>";
		$thead.="<th rowspan=\"2\">Всего</th>";
		$thead.="<th colspan=\"3\">Контрольные цифры приема</th>";
		$thead.="<th rowspan=\"2\">По договорам об оказании платных образовательных услуг</th>";
		$thead.="</tr>";
		$thead.="<tr>";
		$thead.="<th>Особая квота</th>";
		$thead.="<th>Специальная квота</th>";
		//$thead.="<th>Целевые места</th>";
		$thead.="<th>Общие условия</th></tr>";


	$tbody="</thead><tbody>";
	$formsName=array(1=>"Очная",2=>"Заочная",3=>"Очно-заочная");
	//echo "<pre>"; print_r($this->listitems);echo "</pre>";
	if(count($this->listitems)==0){
				$trow="<tr itemprop=\"priemKolMest\">";
				$trow.="<td itemprop=\"eduCode\" >-</td>";
				$trow.="<td itemprop=\"eduName\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestSpecQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestCommon\">-</td>";
				$trow="</tr>";
				$tbody.=$trow;
			}
	foreach($this->listitems as $levelName=>$forms){
		//$tbody.="<tr><th colspan=\"7\">{$levelName}</th></tr>";
		for($f=1;$f<=3;$f++){
			$items=$forms[$f];
			$trows="";
			if(is_array($items) && count($items)==0){
				$trow="<tr itemprop=\"priemKolMest\">";
				$trow.="<td itemprop=\"eduCode\" >-</td>";
				$trow.="<td itemprop=\"eduName\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestSpecQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestCommon\">-</td>";
				$trow="</tr>";
				$trows.=$trow;
			}
			foreach($items as $itemrow){
				$trow="<tr itemprop=\"priemKolMest\">";
				$trow.="<td itemprop=\"eduCode\" >".$itemrow["eduCode"]."</td>";
				if($itemrow["eduProfile"]!="") $itemrow["eduProfile"]="(".$itemrow["eduProfile"].")";
				$trow.="<td itemprop=\"eduName\" >".$itemrow["eduName"]." <br><span title =\"направленность, профиль, шифр и наименование научной специальности\"class=\"blue\">".$itemrow["eduProfile"]."</span></td>";

				if($f==1) {
					$NumberVacantInv=$itemrow["NumberVacantInv"];	//особая квота
					$NumberBFVacantS=$itemrow["NumberBFVacantS"];	//специальная квота
 
					$NumberBVacantC=$itemrow["NumberBFVacantC"];	//цевилики
					$NumberBVacant=$itemrow["NumberBFVacant"];	//бюджет
					$NumberPVacant=$itemrow["NumberPVacant"];	//платные
					$NumberInVacant=$itemrow["NumberInVacant"];	//платные иностранцы

					
				}
				if($f==2) {
					$NumberBFVacantS=0;	//специальная квота
					$NumberVacantInv=$itemrow["NumberVacantInvZ"];
					$NumberBVacantC=$itemrow["NumberBFVacantCZ"];	//цевилики
					$NumberBVacant=$itemrow["NumberBFVacantZ"];
					$NumberPVacant=$itemrow["NumberPVacantZ"];	//платные
					$NumberInVacant=$itemrow["NumberInVacant"];	//платные иностранцы
				}
				if($f==3) {
					$NumberBFVacantS=0;	//специальная квота
					$NumberVacantInv=$itemrow["NumberVacantInvOZ"];
					$NumberBVacantC=$itemrow["NumberBFVacantCOZ"];	//цевилики
					$NumberBVacant=$itemrow["NumberBFVacantOZ"];
					$NumberPVacant=$itemrow["NumberPVacantOZ"];	//платные
					$NumberInVacant=$itemrow["NumberInVacant"];	//платные иностранцы
				}
			
				$NumberVacantInvF=intval($itemrow["NumberVacantInvF"]); //вакантные места за счет особого права
				$NumberBFVacantCF=intval($itemrow["NumberBFVacantCF"]);//вакантные места за счет цевиликов
				$NumberBFVacantSF=intval($itemrow["NumberBFVacantSF"]);//вакантные места за счет специальной квоты

				$NumberFVacant=$NumberBVacant-$NumberVacantInv-$NumberBFVacantS;//общий конкурс
				$title1="";$title2="";
				if($NumberBVacantC>0) {$title1=" title=\"в том числе целевые места: ".$NumberBVacantC."\"";}
				if($NumberInVacant>0) {$title2=" title=\"в том числе места для иностранных граждан: ".$NumberInVacant."\"";}

				$trow.="<td $title2>".($NumberBVacant+$NumberPVacant+$NumberInVacant);
				if($title2!="") $trow.="<span class=\"footnotes\" $title2>i</span>";
				$trow.="</td>";

				if($NumberVacantInvF>0 && $NumberVacantInvF<=$NumberVacantInv){
					$itmp=$NumberVacantInv-$NumberVacantInvF;
					$title="{$NumberVacantInvF} мест по результатам набора перераспределены в общий конкурс";
					$trow.="<td title=\"{$title}\" itemprop=\"priemKolMestQuota\">{$itmp} <span class=\"footnotes\" title=\"{$title}\">i</span></td>";
				}else{
					$trow.="<td itemprop=\"priemKolMestQuota\" >".$NumberVacantInv."</td>";
				}

				if($NumberBFVacantSF>0 && $NumberBFVacantSF<=$NumberBFVacantS){
					$itmp=$NumberVacantS-$NumberBFVacantSF;
					$title="{$NumberBFVacantSF} мест по результатам набора перераспределены в общий конкурс";
					$trow.="<td title=\"{$title}\" itemprop=\"priemKolMestSpecQuota\">{$itmp} <span class=\"footnotes\" title=\"{$title}\">i</span></td>";
				}else{
					$trow.="<td itemprop=\"priemKolMestSpecQuota\" >".$NumberBFVacantS."</td>";
				}

				

				if(($NumberVacantInvF>0) || ($NumberBFVacantCF>0) || ($NumberBFVacantSF>0)){
					$NumberFVacant0=$NumberFVacant+$NumberVacantInvF+$NumberBFVacantSF+$NumberBFVacantCF;
					$title=" в том числе: \r\n";
					if($NumberVacantInvF>0){$title.="из мест особой квоты - ".$NumberVacantInvF.";\r\n";}
					if($NumberBFVacantSF>0){$title.="из мест специальной квоты - ".$NumberBFVacantSF.";\r\n";}else{$title.="из мест специальной квоты будет определено после 09.08.2022 ;\r\n";}
					$NumberVacantCF0=$NumberBFVacantCF+$NumberBVacantC;
					if($NumberVacantCF0>0){$title.="мест целевой квоты - ".$NumberVacantCF0.";";}
					$trow.="<td title=\"{$title}\">{$NumberFVacant0}<span class=\"footnotes\" title=\"{$title}\">i</span></td>";									
				} else{
					$trow.="<td itemprop=\"priemKolMestCommon\" $title1>".$NumberFVacant."</td>";
				}



				



				$trow.="<td itemprop=\"priemKolMestContract\" >".($NumberPVacant+$NumberInVacant);
				if($title2!="") $trow.="<span class=\"footnotes\" $title2>i</span>";
				$trow.="</td>";
				
				$trow.="</tr>";
				if(($NumberBVacant+$NumberPVacant)>0) $trows.=$trow;
			} //foreach items
			if($trows!=""){
				$tbody.="<tr ><th colspan=\"7\"><i>{$formsName[$f]} форма обучения</i></th></tr>".$trows;
				
			}

		}//for
	}//foreach  listitems
		$tbody.="</tbody>";
	$html="<table class=\"priemKolMestTable\"> {$thead} {$tbody} </table>";	
	return $html;
}
private function printTable181(){

	$thead="<thead>";
		$thead.="<tr><th  rowspan=\"2\">Код</th><th  rowspan=\"2\">Cпециальность</th><th  rowspan=\"2\">Всего</th>";
		$thead.="<th colspan=\"3\">Контрольные цифры приема</th>";
		$thead.="</tr>";
		$thead.="<tr>";
		$thead.="<th>Очное</th><th>Заочное</th><th>Очно-заочное</th>";
		$thead.="</tr>";
	$tbody="</thead><tbody>";
	$formsName=array(1=>"Очная",2=>"Заочная",3=>"Очно-заочная");
//	echo "<pre>"; print_r($this->listitems);echo "</pre>";
if(count($this->listitems)==0){
				$trow="<tr itemprop=\"priemKolMest\">";
				$trow.="<td itemprop=\"eduCode\" >-</td>";
				$trow.="<td itemprop=\"eduName\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestSpecQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestCommon\">-</td>";
				$trow="</tr>";
				$tbody.=$trow;
			}
	foreach($this->listitems as $levelName=>$levelsItems){
		//$tbody.="<tr><th colspan=\"7\">{$levelName}</th></tr>";
			//$items=$forms[$f];
			$trows="";

			foreach($levelsItems as $itemrow){
				//echo "<pre>"; print_r($itemrow);echo "</pre>";

				$trow="<tr itemprop=\"priemKolMest\">";
				$trow.="<td itemprop=\"eduCode\" >".$itemrow[1]["eduCode"]."</td>";
				$trow.="<td itemprop=\"eduName\" >".$itemrow[1]["eduName"]." <span>";
				if($this->eduLevelId!=878) $trow.="<br>".$itemrow[1]["eduProfile"];
				$trow.="</span></td>";
					
					$NumberInVacant=$itemrow[1]["NumberInVacant"];	//платные иностранцы
					$NumberPVacant=$itemrow[1]["NumberPVacant"];	//платные
					$NumberBVacantC=$itemrow[1]["NumberBFVacantC"];	//цевилики

					$NumberInVacantZ=0; //$itemrow[1]["NumberInVacantZ"];	платные иностранцы
					$NumberPVacantZ=0;  //$itemrow[1]["NumberPVacantZ"];	платные

					$NumberVacant=$itemrow[1]["NumberBFVacant"]+$NumberInVacant+$NumberPVacant;	//
					$NumberVacantZ=0;//$itemrow[1]["NumberBFVacantZ"]+$NumberInVacantZ+$NumberPVacantZ;	
					$NumberVacantOZ=0; //$itemrow[1]["NumberBFVacantOZ"];	

					$NumberVacantAll=$NumberVacant+$NumberVacantZ+$NumberVacantOZ;
					$title1="";
					//if($NumberBVacantC>0) {$title1=" в том числе целевые места: ".$NumberBVacantC;}
					if($NumberInVacant>0) {$title1="в том числе места для иностранных граждан: ".$NumberInVacant;}

					$trow.="<td  itemprop=\"numberVacantAll\" >".$NumberVacantAll;
					if($title1!="") $trow.="<span class=\"footnotes\" title=\"{$title1}\">i</span>";
					$trow.="</td>";

					$trow.="<td itemprop=\"numberVacant\" >".$NumberVacant;
					if($title1!="") $trow.="<span class=\"footnotes\" title=\"{$title1}\">i</span>";
					$trow.="</td>";

					$trow.="<td itemprop=\"numberVacantZ\" $title1>".$NumberVacantZ."</td>";
					$trow.="<td itemprop=\"numberVacantOZ\" $title1>".$NumberVacantOZ."</td>";
					
	
				
				$trow.="</tr>";
				if($NumberVacantAll>0) $trows.=$trow;
			} //foreach items
			if($trows!=""){
				
					$tbody.=$trows;
				
			}

		
		$tbody.="</tbody>";
	}//foreach  listitems
	$html="<table class=\"priemKolMestTable\"> {$thead} {$tbody} </table>";	
	return $html;
}
private function printTable182(){

	$thead="<thead>";
		$thead.="<tr><th  rowspan=\"2\">Код</th><th  rowspan=\"2\">Cпециальность</th><th  rowspan=\"2\">Всего</th>";
		$thead.="<th colspan=\"3\">Контрольные цифры приема</th><th colspan=\"3\">По договорам об оказании платных<br>образовательных услуг</th>";
		$thead.="</tr>";
		$thead.="<tr>";
		$thead.="<th>Очное</th><th>Заочное</th><th>Очно-заочное</th>";
		$thead.="<th>Очное</th><th>Заочное</th><th>Очно-заочное</th>";
		$thead.="</tr>";
	$tbody="</thead><tbody> ";
	$formsName=array(1=>"Очная",2=>"Заочная",3=>"Очно-заочная");
	//echo "<!-- "; print_r($this->listitems);echo " -->";
if(count($this->listitems)==0){
				$trow="<tr itemprop=\"priemKolMest\">";
				$trow.="<td itemprop=\"eduCode\" >-</td>";
				$trow.="<td itemprop=\"eduName\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestSpecQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestCommon\">-</td>";
				$trow="</tr>";
				$tbody.=$trow;
			}
	foreach($this->listitems as $levelName=>$levelsItems){
		//$tbody.="<tr><th colspan=\"9\">{$levelName}</th></tr>";
			//$items=$forms[$f];
			$trows="";

			foreach($levelsItems as $itemrow){
				//echo "<pre>"; print_r($itemrow);echo "</pre>";

				$trow="<tr itemprop=\"priemKolMest\">";
				if($itemrow[1]["eduCode"]!=""){
					$eduCode=$itemrow[1]["eduCode"];
					$eduName=$itemrow[1]["eduName"];
				}elseif($itemrow[2]["eduCode"]!=""){
					$eduName=$itemrow[2]["eduName"];
					$eduCode=$itemrow[2]["eduCode"];
				}elseif($itemrow[3]["eduCode"]!=""){
					$eduName=$itemrow[3]["eduName"];
					$eduCode=$itemrow[3]["eduCode"];
				}

				$trow.="<td itemprop=\"eduCode\" >{$eduCode}</td>";
				$trow.="<td itemprop=\"eduName\" >{$eduName}</td>";
					
					$NumberVacant=$itemrow[1]["NumberBFVacant"];	//бюджет очники
					$NumberVacantZ=$itemrow[2]["NumberBFVacantZ"];	//бюджет заочники
					$NumberVacantOZ=$itemrow[3]["NumberBFVacantOZ"];	//бюджет

					$NumberPVacant=$itemrow[1]["NumberPVacant"];	//коммерческие очники
					$NumberPVacantZ=$itemrow[2]["NumberPVacantZ"];	//коммерческие заочники
					$NumberPVacantOZ=$itemrow[3]["NumberPVacantOZ"];	

					$NumberVacantAll=$NumberVacant+$NumberVacantZ+$NumberVacantOZ;
					$NumberPVacantAll=$NumberPVacant+$NumberPVacantZ+$NumberPVacantOZ;

					$trow.="<td itemprop=\"numberVacantAll\" >".($NumberVacantAll+$NumberPVacantAll)."</td>";
					$trow.="<td itemprop=\"numberVacant\" >".$NumberVacant."</td>";
					$trow.="<td itemprop=\"numberVacantZ\" >".$NumberVacantZ."</td>";
					$trow.="<td itemprop=\"numberVacantOZ\" >".$NumberVacantOZ."</td>";
					$trow.="<td itemprop=\"numberPVacant\" >".$NumberPVacant."</td>";
					$trow.="<td itemprop=\"numberPVacantZ\" >".$NumberPVacantZ."</td>";
					$trow.="<td itemprop=\"numberPVacantOZ\" >".$NumberPVacantOZ."</td>";
	
				
				$trow.="</tr>";
				if(($NumberVacantAll+$NumberPVacantAll)>0) $trows.=$trow;
			} //foreach items
			if($trows!=""){
				
					$tbody.=$trows;
				
			}

		
		
	}//foreach  listitems
	$tbody.="</tbody>";
	$html="<table class=\"priemKolMestTable\"> {$thead} {$tbody} </table>";	
	return $html;
}
private function printTable878a(){

	$thead="<thead>";
		$thead.="<tr><th  rowspan=\"2\">Код</th><th  rowspan=\"2\">Cпециальность</th><th  rowspan=\"2\">Всего</th>";
		$thead.="<th colspan=\"3\">Контрольные цифры приема</th><th colspan=\"3\">По договорам об оказании платных<br>образовательных услуг</th>";
		$thead.="</tr>";
		$thead.="<tr>";
		$thead.="<th>Очное</th><th>Заочное</th><th>Очно-заочное</th>";
		$thead.="<th>Очное</th><th>Заочное</th><th>Очно-заочное</th>";
		$thead.="</tr>";
	$tbody="</thead><tbody>";
	$formsName=array(1=>"Очная",2=>"Заочная",3=>"Очно-заочная");
	//echo "<!-- "; print_r($this->listitems);echo " -->";
	foreach($this->listitems as $levelName=>$levelsItems){
		//$tbody.="<tr><th colspan=\"9\">{$levelName}</th></tr>";
			//$items=$forms[$f];
			$trows="";

			foreach($levelsItems as $itemrow){
				//echo "<pre>"; print_r($itemrow);echo "</pre>";

				$trow="<tr itemprop=\"priemKolMest\">";
				if($itemrow[1]["eduCode"]!=""){
					$eduCode=$itemrow[1]["eduCode"];
					$eduName=$itemrow[1]["eduName"];
					$eduProfile=$itemrow[1]["eduProfile"];
				}elseif($itemrow[2]["eduCode"]!=""){
					$eduName=$itemrow[2]["eduName"];
					$eduCode=$itemrow[2]["eduCode"];
					$eduProfile=$itemrow[2]["eduProfile"];
				}elseif($itemrow[3]["eduCode"]!=""){
					$eduName=$itemrow[3]["eduName"];
					$eduCode=$itemrow[3]["eduCode"];
					$eduProfile=$itemrow[3]["eduProfile"];
				}

				$trow.="<td itemprop=\"eduCode\" >{$eduCode}</td>";
				$trow.="<td  ><span itemprop=\"eduName\">{$eduName}</span><br><span class=\"blue small\" itemprop=\"eduProfile\">{$eduProfile}</span></td>";
					
					$NumberVacant=$itemrow[1]["NumberBFVacant"];	//бюджет очники
					$NumberVacantZ=$itemrow[2]["NumberBFVacantZ"];	//бюджет заочники
					$NumberVacantOZ=$itemrow[3]["NumberBFVacantOZ"];	//бюджет

					$NumberPVacant=$itemrow[1]["NumberPVacant"];	//коммерческие очники
					$NumberPVacantZ=$itemrow[2]["NumberPVacantZ"];	//коммерческие заочники
					$NumberPVacantOZ=$itemrow[3]["NumberPVacantOZ"];	

					$NumberVacantAll=$NumberVacant+$NumberVacantZ+$NumberVacantOZ;
					$NumberPVacantAll=$NumberPVacant+$NumberPVacantZ+$NumberPVacantOZ;
					$trow.="<td itemprop=\"numberVacantAll\" >".($NumberVacantAll+$NumberPVacantAll)."</td>";
					$trow.="<td itemprop=\"numberVacant\" >".$NumberVacant."</td>";
					$trow.="<td itemprop=\"numberVacantZ\" >".$NumberVacantZ."</td>";
					$trow.="<td itemprop=\"numberVacantOZ\" >".$NumberVacantOZ."</td>";
					$trow.="<td itemprop=\"numberPVacant\" >".$NumberPVacant."</td>";
					$trow.="<td itemprop=\"numberPVacantZ\" >".$NumberPVacantZ."</td>";
					$trow.="<td itemprop=\"numberPVacantOZ\" >".$NumberPVacantOZ."</td>";
				
				$trow.="</tr>";
				if(($NumberVacantAll+$NumberPVacantAll)>0) $trows.=$trow;
			} //foreach items
			if($trows!=""){
				
					$tbody.=$trows;
				
			}

		
		
	}//foreach  listitems
$tbody.="</tbody>";
	$html="<table class=\"priemKolMestTable\"> {$thead} {$tbody} </table>";	
	return $html;
}
	public function showHtml($buffer=false){
		$html="";
		$memcache = new Memcache;
		$memcache->addServer('unix:///tmp/memcached.sock', 0);
		$html="";//$memcache->get($this->cashGUID);
		if(strlen($html)<255 || $html==false || $this->isEditmode()) {
			$this->generateItems();
			if ($this->eduLevelId==180) $html=$this->printTable180();//специалитет
			elseif ($this->eduLevelId==181) $html=$this->printTable181();//ординатура 879
			elseif ($this->eduLevelId==182) $html=$this->printTable182();//аспирантура 878
			$result = $memcache->replace( $this->cashGUID, $html);
			if( $result == false ){
				
				$memcache->set($this->cashGUID, $html, false, $this->cashTime);
			} 
		}
		$memcache->close();
		if($buffer) return $html; else echo $html;
	}//showHtml

}//class priemVacant