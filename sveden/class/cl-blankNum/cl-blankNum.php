<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class blankNum extends  iasmuinfo{
	private $cashGUID;
	private $open;
	private $god;
	private $cashTime=600;
	private $listitems=array();
	private $eduLevelId=180;
	private $modyfyData=0;
	private function setRowData($eduLevel,$index1,$index2,$tmprow){
		$beEdu=intval($tmprow["beEdu"]);
		if(!isset($this->listitems[$eduLevel][$beEdu][$index1][$index2])){
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
			
		}
	
	$this->listitems[$eduLevel][$beEdu][$index1][$index2]=$item;
	//echo "<pre>";print_r($item); echo "</pre>";
	}
	private function numpadeg($n){
		if(in_array($n,array(2,3,4,22,23,24,32,33,34,42,43,44,52,53,54,62,63,64,72,73,74,82,83,84,92,93,94)) ) $okon="а";
		if(in_array($n,array(1,21,31,41,51,61,71,81,91))) $okon="о";
		return $okon;
	}
	private function generateItemsStep1(){
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
when bie.PROPERTY_377=217 then "ФГТ 2021 "
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
(bie.PROPERTY_354>0 or
bie.PROPERTY_355>0 or
bie.PROPERTY_356>0 or
bie.PROPERTY_357>0 or
bie.PROPERTY_358>0 or
bie.PROPERTY_359>0 or
bie.PROPERTY_360>0 or
bie.PROPERTY_361>0 or
cast(bie.PROPERTY_362 as UNSIGNED)>0 or
bie.PROPERTY_367>0 or
bie.PROPERTY_368>0 or
cast(bie.PROPERTY_369 as UNSIGNED)>0 or
bie.PROPERTY_385>0 or
cast(bie.PROPERTY_389 as UNSIGNED)>0 or
bie.PROPERTY_390>0 or
cast(bie.PROPERTY_391 as UNSIGNED)>0 or
cast(bie.PROPERTY_392 as UNSIGNED)>0 or
cast(bie.PROPERTY_393 as UNSIGNED)>0 or
cast(bie.PROPERTY_394 as UNSIGNED)>0
 ) and
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
			$beEdu=$rec["beEdu"];
			if($rec["eduProfile"]!="") $eduName.=",<br>".$rec["eduProfile"];
			$tmprow=(array)$rec;
			$tmprow["eduProfile"]="";
			



			if($rec["eduProfile"]!="" ) $tmprow["eduProfile"]=$rec["eduProfile"];

			//$arforms=unserialize($rec["beForm"]);
			$arforms=$this->unserform($rec["beForm"]);





			if (strlen($arforms[1])>4){
				$tmprow["learningTerm"]=$arforms["s1"];
				$tmprow["eduForm"]="Очная";
				if($this->specGroup==1) {
					$index=($eduCode."_1");
					$this->setRowData($rec["eduLevel"],1,$index,$tmprow);
				}
				else {
					$index=($tmprow["id"]."_1");
					$this->listitems[$rec["eduLevel"]][$beEdu][1][$index]=$tmprow;
				}
			}
			if (strlen($arforms[2])>4){
				$tmprow["learningTerm"]=$arforms["s2"];
				$tmprow["eduForm"]="Заочная";
				if($this->specGroup==1) {
					$index=($eduCode."_2");
					$this->setRowData($rec["eduLevel"],2,$index,$tmprow);
				}
				else {
					$index=($tmprow["id"]."_2");
					$this->listitems[$rec["eduLevel"]][$beEdu][2][$index]=$tmprow;
				}
			}
			if (strlen($arforms[3])>4){
				$tmprow["learningTerm"]=$arforms["s3"];
				$tmprow["eduForm"]="Очно-заочная";
				
				if($this->specGroup==1) {
					$index=($eduCode."_3");
					$this->setRowData($rec["eduLevel"],3,$index,$tmprow);
				}
				else {
					$index=($tmprow["id"]."_3");
					$this->listitems[$rec["eduLevel"]][$beEdu][3][$index]=$tmprow;
				}
			}

		}
		//asort($this->listitems);
	}

} 

private function generateItemsStep2(){
$sqlx=<<<sqlx
select sum(x.osob) as osob,sum(x.celev) as celev,sum(x.plat) as plat,sum(x.budjet) as budjet, sum(x.fis) as fis,sum(x.specosob) as specosob, x.FORM as form,max(x.UF_MODIFY) as modify from (
SELECT distinct UF_NUMBER_ZAYAV,UF_OSNOVA, UF_FORM as FORM,UF_MODIFY,
if((UF_KATEGOR like "%особое право%" OR UF_GROUP like "%особые права%") and not (UF_GROUP like "%специальные%"),1,0)as osob,
if((UF_GROUP like "%специальные права") or (UF_KATEGOR="Имеющие специальное право") ,1,0)as specosob,
if(UF_OSNOVA="Целевой прием",1,0) as celev,
if(UF_OSNOVA="С оплатой обучения",1,0)as plat,
if(UF_OSNOVA="Бюджетные места",1,0)as budjet,
if(UF_FACULTY="ФИС",1,0)as fis
FROM `abit_list_by_fio`
where #where# and UF_STATUS<>"Отозвано") x
group by x.FORM
sqlx;

	foreach($this->listitems as $eduLevel=>$beEduData){
		$filter=array();
		$filter[0]="UF_GOD=\"".$this->god."\"";
		$filter[1]="UF_LEVEL=\"".$eduLevel."\"";
		foreach($beEduData as $beEdu=>$formData){
			if($beEdu==1) $filter[4]="UF_FACULTY=\"ФИС\""; else $filter[4]="UF_FACULTY!=\"ФИС\"";
			foreach($formData as $formID=>$oppList){
				if($formID==1) $filter[2]="UF_FORM=\"Очная\"";
				if($formID==2) $filter[2]="UF_FORM=\"Заочная\"";
				if($formID==3) $filter[2]="UF_FORM=\"Очно-заочная\"";
				foreach($oppList as $oppID=>$opp){
					$filter[3]="(UF_SPEC_KOD=\"{$opp["eduCode"]}\" or UF_SPEC like \"{$opp["eduCode"]}%\")";
	
					$sqlx2=str_replace("#where#",implode(" and ",$filter),$sqlx);
					
					if($rez=$this->BD->query($sqlx2)){
						if ($rec=$rez->fetch()){
							
							$this->listitems[$eduLevel][$beEdu][$formID][][$oppID]["fis"]=intval($rec["fis"]);
							$this->listitems[$eduLevel][$beEdu][$formID][$oppID]["osob"]=intval($rec["osob"]);
                                                        $this->listitems[$eduLevel][$beEdu][$formID][$oppID]["specosob"]=intval($rec["specosob"]);
							$this->listitems[$eduLevel][$beEdu][$formID][$oppID]["celev"]=intval($rec["celev"]);
							$this->listitems[$eduLevel][$beEdu][$formID][$oppID]["plat"]=intval($rec["plat"]);
							$this->listitems[$eduLevel][$beEdu][$formID][$oppID]["budjet"]=intval($rec["budjet"]);
							$this->modyfyData=max($this->modyfyData,strtotime($rec["modify"]));
						}
					}
				}
			}
		}
	}
}
function generateItems(){
$this->generateItemsStep1();
$this->generateItemsStep2();
}
function setparams($params){

		$this->open=0;
		$this->god=intval(date("Y"));
		if(isset($params["open"])) $this->open=1;
		$this->listitems=array();
		$this->eduLevelId=0;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		$this->cashGUID=md5("blankNum_".$this->god."_".$this->eduLevelId);
		if(isset($params["god"])){
			$god=intval($params["god"]);
			if($god>0) $this->god=$god; 
		}
		
}
	public function printTable(){
		$thead="<thead><tr>";
		$thead.="<th rowspan=\"2\">Код</th>";
		$thead.="<th rowspan=\"2\">Наименование профессии, специальности, направления подготовки</th>";
		$thead.="<th rowspan=\"2\">Всего заявлений</th>";
		$thead.="<th colspan=\"2\">Места в рамках контрольных цифр приема (по общему конкурсу)</th>";
		$thead.="<th colspan=\"2\">Места в пределах особой квоты</th>";
		$thead.="<th colspan=\"2\">Места в пределах специальной квоты</th>";
		$thead.="<th colspan=\"2\">Места в пределах целевой квоты</th>";
		$thead.="<th colspan=\"2\">По договорам об оказании платных образовательных услуг</th>";
		$thead.="</tr><tr>";
		$thead.="<th>План приёма</th><th>Фактически подано заявлений</th>";
		$thead.="<th>План приёма</th><th>Фактически подано заявлений</th>";
		$thead.="<th>План приёма</th><th>Фактически подано заявлений</th>";
		$thead.="<th>План приёма</th><th>Фактически подано заявлений</th>";
		$thead.="<th>План приёма</th><th>Фактически подано заявлений</th>";
		$thead.="</tr></thead>";
		$tbody="<tbody>";
		
		$formsName=array(1=>"Очная",2=>"Заочная",3=>"Очно-заочная");
		//echo "<pre>"; print_r($this->listitems);echo "</pre>";
		foreach($this->listitems as $levelName=>$beEduRec){
		$tbody.="<tr><th colspan=\"13\">{$levelName}</th></tr>";
		foreach($beEduRec as $biedu=>$forms){
			if($biedu==1) $tbody.="<tr><th colspan=\"13\">Иностранные студенты (билингвальное обучение)</th></tr>";

			for($f=1;$f<=3;$f++){
				$items=$forms[$f];
//echo "<!-- item $f: ";print_r($items);echo " -->";
				$trows="";
				if(is_array($items))
				foreach($items as $itemrow){
					$trow="<tr itemprop=\"priemKolMest\">";
					$trow.="<td itemprop=\"eduCode\" >".$itemrow["eduCode"]."</td>";
					$trow.="<td itemprop=\"eduName\" ><a href=\"#edu_{$itemrow["eduCode"]}\">".$itemrow["eduName"];
						if($itemrow["eduProfile"]!="") $trow.=" <br><span class=\"blue\"> профиль (направленность): ".$itemrow["eduProfile"]."</span>";
					$trow.="</a></td>";
						$osob=intval($itemrow["osob"]);
						$specosob=intval($itemrow["specosob"]);
						$celev=intval($itemrow["celev"]);
						$plat=intval($itemrow["plat"]);
						$budjet=intval($itemrow["budjet"]);
						$fis=intval($itemrow["fis"]);
					if($f==1) {
						$NumberBFVacantS=intval($itemrow["NumberBFVacantS"]);	//особая квота
						$NumberVacantInv=intval($itemrow["NumberVacantInv"]);	//специальнаяая квота
						$NumberBVacantC=intval($itemrow["NumberBFVacantC"]);	//цевилики
						$NumberBVacant=intval($itemrow["NumberBFVacant"]);	//бюджет
						$NumberPVacant=intval($itemrow["NumberPVacant"]);	//платные
						$NumberInVacant=intval($itemrow["NumberInVacant"]);	//платные иностранцы
						
	
						
					}
					if($f==2) {
						$NumberBFVacantS=0;
						$NumberVacantInv=intval($itemrow["NumberVacantInvZ"]);
						$NumberBVacantC=intval($itemrow["NumberBFVacantCZ"]);	//цевилики
						$NumberBVacant=intval($itemrow["NumberBFVacantZ"]);
						$NumberPVacant=intval($itemrow["NumberPVacantZ"]);	//платные
						$NumberInVacant=intval($itemrow["NumberInVacant"]);	//платные иностранцы
						
					}
					if($f==3) { //такой формы нет
						$NumberBFVacantS=0;
						$NumberVacantInv=0;//intval($itemrow["NumberVacantInvOZ"]);
						$NumberBVacantC=0;//intval($itemrow["NumberBFVacantCOZ"]);	цевилики
						$NumberBVacant=0;//intval($itemrow["NumberBFVacantOZ"]);
						$NumberPVacant=0;//intval($itemrow["NumberPVacantOZ"]);	платные
						$NumberInVacant=0;//intval($itemrow["NumberInVacant"]);	платные иностранцы
					}
						
						$NumberVacantInvF=intval($itemrow["NumberVacantInvF"]); //вакантные места за счет особого права
						$NumberBFVacantCF=intval($itemrow["NumberBFVacantCF"]);//вакантные места за счет цевиликов
						$NumberBFVacantSF=intval($itemrow["NumberBFVacantSF"]);//вакантные места за счет специальной квоты
						

					$priemAll=$plat+$budjet+$fis+$celev;

						$NumberFVacant=intval($NumberBVacant-$NumberVacantInv-$NumberBVacantC-$NumberBFVacantS);//общий конкурс
						$trow.="<td>{$priemAll}</td>";

					if(($NumberVacantInvF>0) || ($NumberBFVacantCF>0) || ($NumberBFVacantSF>0)){
						$NumberFVacant0=$NumberFVacant+$NumberVacantInvF+$NumberBFVacantSF+$NumberBFVacantCF;
						$title="в том числе мест: \r\n";
						if($NumberVacantInvF>0){$title.="из мест особой квоты - ".$NumberVacantInvF.";\r\n";}
						if($NumberBFVacantSF>0)
							{
								$title.="из мест специальной квоты - ".$NumberBFVacantSF.";\r\n";
							}else{
								$title.="из мест специальной квоты будет определено после 09.08.2022 ;\r\n";
							}
						if($NumberBFVacantCF>0){$title.="из мест целевой квоты - ".$NumberBFVacantCF.";";}
						$trow.="<td title=\"{$title}\">{$NumberFVacant0}<span class=\"footnotes\" title=\"{$title}\">i</span></td>";									
					} else{
						$trow.="<td>{$NumberFVacant}</td>";
					}
					$NumberFVacantX=intval($budjet)-intval($osob) -intval($specosob);//-intval($celev)-intval($osob);
					$trow.="<td>{$NumberFVacantX}</td>";

					if($NumberVacantInvF>0 && $NumberVacantInvF<=$NumberVacantInv){
						$itmp=$NumberVacantInv-$NumberVacantInvF;
						$okon="";
						$okon=$this->numpadeg($NumberVacantInvF);
						$title="{$NumberVacantInvF} мест{$okon} по результатам набора перераспределены в общий конкурс";
						$trow.="<td title=\"{$title}\">{$itmp}<span class=\"footnotes\" title=\"{$title}\">i</span></td>";
					} else{
						$trow.="<td>{$NumberVacantInv}</td>";
					}
					
						$trow.="<td>{$osob}</td>";

					if($NumberBFVacantSF>0 && ($NumberBFVacantSF<=$NumberBFVacantS)){
						$itmp=$NumberBFVacantS-$NumberBFVacantSF;
						$okon="";
						$okon=$this->numpadeg($NumberBFVacantSF);
						
						$title="{$NumberBFVacantS} мест{$okon} по результатам набора перераспределены в общий конкурс";
						$trow.="<td title=\"$title\">{$itmp}<span class=\"footnotes\" title=\"{$title}\">i</span></td>";
					} else{
						$trow.="<td>{$NumberBFVacantS}</td>";
					}
					
					$trow.="<td>{$specosob}</td>";
					if($NumberBFVacantCF>0 && ($NumberBFVacantCF<=$NumberBVacantC)){
						$itmp=$NumberBVacantC-$NumberBFVacantCF;
						$okon="";
						$okon=$this->numpadeg($NumberBFVacantCF);
						$title="{$NumberBFVacantCF} мест{$okon} по результатам набора перераспределены в общий конкурс";
						$trow.="<td title=\"$title\">{$itmp}<span class=\"footnotes\" title=\"{$title}\">i</span></td>";
					} else{
						$trow.="<td>{$NumberBVacantC}</td>";
					}

					$trow.="<td>{$celev}</td>";
					$trow.="<td>".intval($NumberPVacant+$NumberInVacant)."</td>";
					$trow.="<td>".intval($plat+$fis)."</td>";
					$trow.="</tr>";
					if(($NumberBVacant+$NumberPVacant+$NumberInVacant)>0) $trows.=$trow;
				} //foreach items
				if($trows!=""){
					$tbody.="<tr ><th colspan=\"13\"><i>{$formsName[$f]} форма обучения</i></th></tr>".$trows;
				}
	
			}//for
			
	}}//foreach  listitems
	$tbody.="</tbody>";
	$html="<div class=\"blankNumTable\"><table itemprop=\"blankNum\" > {$thead} {$tbody} </table></div>";	
	if($this->modyfyData>0) $html.="<span> По состоянию на ".date("d.m.Y H:i",$this->modyfyData)."</span>";
	return $html;
	}
	public function showHtml($buffer=false){
		$html="";
		$memcache = new Memcache;
		$memcache->addServer('unix:///tmp/memcached.sock', 0);
		$html="";//$memcache->get($this->cashGUID);
		if(strlen($html)<255 || $html==false || $this->isEditmode() || $this->modyfyData<(time()-1200)) {
			$this->generateItems();
			$html=$this->printTable();
			$result = $memcache->replace( $this->cashGUID, $html);
			if( $result == false ){
				
				$memcache->set($this->cashGUID, $html, false, $this->cashTime);
			} 
		}
		$memcache->close();
		if($buffer) return $html; else echo $html;
	}//showHtml

}//end class xobjects  