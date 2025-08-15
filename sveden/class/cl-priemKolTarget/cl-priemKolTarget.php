<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class priemKolTarget extends  iasmuinfo{
	const cashTime=6000;
	private $cashGUID;
	private $listitems=array();
	private $eduLevelId=180;
	private $specGroup=1;
	public function setparams($params){
		$this->eduLevelId=0;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		$this->cashGUID="priemKolTarget_".$this->eduLevelId;
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
		//echo "<div style=\"display:none\">";
		//echo "<pre>";print_r($sql);
		//echo "</pre></div>";
	$this->listitems=array();
	if($rez=$this->BD->query($sql)){

		while ($rec=$rez->fetch()){


			$eduName=$rec["eduName"];
			$eduCode=$rec["eduCode"];
			//if(($rec["eduProfile"]!="") && ($this->eduLevelId!=878)) $eduName.=",<br>".$rec["eduProfile"];
			$tmprow=(array)$rec;
			$tmprow["eduProfile"]="";
			if(($rec["eduProfile"]!="") && ($this->eduLevelId!=182)) $tmprow["eduProfile"]=$rec["eduProfile"];

			//$arforms=unserialize($rec["beForm"]);
			$arforms=$this->unserform($rec["beForm"]);
			if (strlen($arforms[1])>4){
				$tmprow["learningTerm"]=$arforms[1];
				$tmprow["eduForm"]="Очная";
				if($this->specGroup==1) {
					$index=($eduCode."_1");
					$this->setRowData($rec["eduLevel"],1,$index,$tmprow);
				}
				else {
					$index=($tmprow["id"]."_1");
					$this->listitems[$rec["eduLevel"]][1][$index]=$tmprow;
				}
			}
			if (strlen($arforms[2])>4){
				$tmprow["learningTerm"]=$arforms[2];
				$tmprow["eduForm"]="Заочная";
				if($this->specGroup==1) {
					$index=($eduCode."_2");
					$this->setRowData($rec["eduLevel"],2,$index,$tmprow);
				}
				else {
					$index=($tmprow["id"]."_2");
					$this->listitems[$rec["eduLevel"]][2][$index]=$tmprow;
				}
			}
			if (strlen($arforms[3])>4){
				$tmprow["learningTerm"]=$arforms[3];
				$tmprow["eduForm"]="Очно-заочная";
				
				if($this->specGroup==1) {
					$index=($eduCode."_3");
					$this->setRowData($rec["eduLevel"],3,$index,$tmprow);
				}
				else {
					$index=($tmprow["id"]."_3");
					$this->listitems[$rec["eduLevel"]][3][$index]=$tmprow;
				}
			}

		}
		//asort($this->listitems);
	}

} //generateItems

private function printTable(){
	$allcols=6;
	$thead="<thead>";
	if($this->eduLevelId==186) {
			$thead.="<tr><th rowspan=\"2\" >Код</th><th  rowspan=\"2\" >Направление подготовки (специальности)</th>";
			$thead.="<th  rowspan=\"2\" >Всего</th><th  rowspan=\"2\">Места в рамках контрольных цифр приема (по общему конкурсу)</th>";
			$thead.="<th rowspan=\"2\">Места в пределах особой квоты</th><th rowspan=\"2\">Места в пределах специальной квоты</th><th rowspan=\"2\">Места в пределах целевой квоты</th><th colspan=\"2\">По договорам об оказании платных образовательных услуг</th></tr>";
			$thead.="<tr><th>для граждан России и граждан СНГ</th><th>для иностранных граждан</th></tr></thead>";
		$allcols=9;
	}
	if($this->eduLevelId==180) {
			$thead.="<tr><th>Код</th><th>Направление подготовки (специальности)</th>";
			$thead.="<th>Всего</th><th>Места в рамках контрольных цифр приема (по общему конкурсу)</th>";
		$allcols=7;
			$thead.="<th>Места в пределах особой квоты</th><th>Места в пределах специальной квоты</th><th>Места в пределах целевой квоты</th></tr></thead>";
	}
	if($this->eduLevelId==182) {
			$thead.="<tr><th>Код</th><th>Направление подготовки (специальности)</th>";
			$thead.="<th>Всего</th><th>Места в рамках контрольных цифр приема (по общему конкурсу)</th>";

			$thead.="<th>Места в пределах целевой квоты</th><th>По договорам об оказании платных образовательных услуг</th></tr></thead>";
	}
	if($this->eduLevelId==181) {
			$thead.="<tr><th rowspan=\"2\" >Код</th><th  rowspan=\"2\" >Направление подготовки (специальности)</th>";
			$thead.="<th  rowspan=\"2\" >Всего</th><th  rowspan=\"2\">Места в рамках контрольных цифр приема (по общему конкурсу)</th>";

			$thead.="<th  rowspan=\"2\" >Места в пределах целевой квоты</th><th colspan=\"2\">По договорам об оказании платных образовательных услуг</th></tr>";
			$thead.="<tr><th>для граждан России и граждан СНГ</th><th>для иностранных граждан</th></tr></thead>";
		$allcols=7;
	}
	$tbody="<tbody>";
	$formsName=array(1=>"Очная",2=>"Заочная",3=>"Очно-заочная");
	//echo "<pre>"; print_r($this->listitems);echo "</pre>";
	if(count($this->listitems)==0){
				$trow="<tr itemprop=\"priemKolMest\">";
				$trow.="<td itemprop=\"eduCode\" >-</td>";
				$trow.="<td itemprop=\"eduName\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestSpecQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestCommon\">-</td>";
				$trow.="</tr>";
				$tbody.=$trow;
				//echo "<!--".$trow."-->";
			}

	foreach($this->listitems as $levelName=>$forms){

		$tbody.="<tr><th colspan=\"$allcols\">{$levelName}</th></tr>";
		for($f=1;$f<=3;$f++){
			$items=$forms[$f];
			$trows="";
			/*
			if(count($items)==0){
				$trow="<tr itemprop=\"priemKolMest\">";
				$trow.="<td itemprop=\"eduCode\" >-</td>";
				$trow.="<td itemprop=\"eduName\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestSpecQuota\" >-</td>";
				$trow.="<td itemprop=\"priemKolMestCommon\">-</td>";
				$trow="</tr>";
				$trows.=$trow;
			}*/
			if(!is_array($items)) $items=array();
			foreach($items as $itemrow){
				$trow="<tr itemprop=\"priemKolTarget\">";
				$trow.="<td itemprop=\"eduCode\" >".$itemrow["eduCode"]."</td>";
				if($itemrow["eduProfile"]!="") $itemrow["eduProfile"]=" (".$itemrow["eduProfile"].")";
				$trow.="<td itemprop=\"eduName\" >".$itemrow["eduName"]."<br><span title=\"направленность, профиль, шифр и наименование научной специальности\" class=\"blue\">".$itemrow["eduProfile"]."</span></td>";
				if($f==1) {
					$NumberVacantInv=$itemrow["NumberVacantInv"];	//особая квота
					$NumberBFVacantS=$itemrow["NumberBFVacantS"];	//специальная квота
 
					$NumberBVacantC=$itemrow["NumberBFVacantC"];	//цевилики
					$NumberBVacant=$itemrow["NumberBFVacant"];	//бюджет
					$NumberPVacant=$itemrow["NumberPVacant"];	//платные
					$NumberInVacant=$itemrow["NumberInVacant"];	//платные иностранцы
					$NumberVacantInv=$itemrow["NumberVacantInv"];
					
				}
				if($f==2) {
					$NumberVacantInv=$itemrow["NumberVacantInvZ"];
					$NumberBVacantC=$itemrow["NumberBFVacantCZ"];	//цевилики
					$NumberBVacant=$itemrow["NumberBFVacantZ"];
					$NumberPVacant=$itemrow["NumberPVacantZ"];	//платные
					$NumberInVacant=$itemrow["NumberInVacant"];	//платные иностранцы
					$NumberVacantInv=$itemrow["NumberVacantInv"];
					$NumberBFVacantS=0;	//специальная квота
				}
				if($f==3) {
					$NumberVacantInv=$itemrow["NumberVacantInvOZ"];
					$NumberBVacantC=$itemrow["NumberBFVacantCOZ"];	//цевилики
					$NumberBVacant=$itemrow["NumberBFVacantOZ"];
					$NumberPVacant=$itemrow["NumberPVacantOZ"];	//платные
					$NumberInVacant=$itemrow["NumberInVacant"];	//платные иностранцы
					$NumberVacantInv=$itemrow["NumberVacantInv"];
					$NumberBFVacantS=0;	//специальная квота
				}

				$NumberVacantInvF=intval($itemrow["NumberVacantInvF"]); //вакантные места за счет особого права
				$NumberBFVacantCF=intval($itemrow["NumberBFVacantCF"]);//вакантные места за счет цевиликов
				$NumberBFVacantSF=intval($itemrow["NumberBFVacantSF"]);//вакантные места за счет специальной квоты

				$title2="";
				if($NumberInVacant>0) {$title2=" title=\"в том числе места для иностранных граждан: ".$NumberInVacant."\"";}
				$NumberContractVacant=$NumberPVacant+$NumberInVacant;
				//$NumberAllVacant=$NumberContractVacant+$NumberBVacant;
				$NumberAllVacant=$NumberBVacant+$NumberPVacant;
				$trow.="<td> {$NumberAllVacant}</td>";

				if($NumberVacantInvF>0 || $NumberBFVacantCF>0 || $NumberBFVacantSF>0){
					$NumberFVacant0=$NumberBVacant-$NumberVacantInv-$NumberBVacantC-$NumberBFVacantS;
					$NumberFVacant0+=$NumberVacantInvF+$NumberBFVacantSF+$NumberBFVacantCF;
					$title=" в том числе мест: \r\n";
					if($NumberVacantInvF>0){$title.="из мест особой квоты - ".$NumberVacantInvF.";\r\n";}
					if($NumberBFVacantSF>0){$title.="из мест специальной квоты - ".$NumberBFVacantSF.";\r\n";}
					if($NumberBFVacantCF>0){$title.="из мест целевой квоты - ".$NumberBFVacantCF.";";}
					$trow.="<td title=\"{$title}\">{$NumberFVacant0}<span class=\"footnotes\" title=\"{$title}\">i</span></td>";									
				} else{
					$trow.="<td  >".($NumberBVacant-$NumberVacantInv-$NumberBVacantC-$NumberBFVacantS)."</td>";
				}

				
				if($this->eduLevelId==877){ 
					if(($NumberVacantInvF>0) && ($NumberVacantInvF<=$NumberVacantInv)){
							$itmp=$NumberVacantInv-$NumberVacantInvF;
							$title="{$NumberVacantInvF} мест по результатам набора перераспределены в общий конкурс";
							$trow.="<td title=\"{$title}\" >{$itmp} <span class=\"footnotes\" title=\"{$title}\">i</span></td>";
						}else{
							$trow.="<td  >{$NumberVacantInv}</td>";
						}
		
						if($NumberBFVacantSF>0 && ($NumberBFVacantSF<=$NumberVacantS)){
							$itmp=$NumberVacantS-$NumberBFVacantSF;
							$title="{$NumberBFVacantSF} мест по результатам набора перераспределены в общий конкурс";

							$trow.="<td title=\"{$title}\">{$itmp}<span class=\"footnotes\" title=\"{$title}\">i</span></td>";
						}else{
							$trow.="<td>{$NumberBFVacantS}</td>";
						}
						if($NumberBFVacantCF>0 && ($NumberBFVacantCF<=$NumberBVacantC)){
							$itmp=$NumberBVacantC-$NumberBFVacantCF;
							$title="{$NumberBFVacantCF} мест по результатам набора перераспределены в общий конкурс";
							$trow.="<td title=\"{$title}\">{$itmp}<span class=\"footnotes\" title=\"{$title}\">i</span></td>";
						}else{
							$trow.="<td>{$NumberBVacantC}</td>";
						}
					




				}
				if($this->eduLevelId==3743){ $trow.="<td  >{$NumberVacantInv}</td><td>{$NumberBFVacantS}</td><td >{$NumberBVacantC}</td>";}
				if($this->eduLevelId==182){ 
					//$trow.="<td  >{$NumberBVacantC}</td>";
					$trow.="<td  >{$NumberBVacantC}</td><td>{$NumberContractVacant}";
					if($title2!="") $trow.="<span class=\"footnotes\" $title2>i</span>";				
					$trow.="</td>";

				}
				if($this->eduLevelId==186){ $trow.="<td>{$NumberPVacant}</td><td>{$NumberInVacant}</td>";}
				if($this->eduLevelId==181){ 
					
					$trow.="<td  >{$NumberBVacantC}</td><td>{$NumberPVacant}</td><td>{$NumberInVacant}</td>";
				}

				$trow.="</tr>";
				if(($NumberBVacant+$NumberPVacant+$NumberInVacant) >0) $trows.=$trow;
			} //foreach items
			if($trows!=""){
				$tbody.="<tr ><th colspan=\"$allcols\"><i>{$formsName[$f]} форма обучения</i></th></tr>".$trows;
			}

		}//for
		
	}//foreach  listitems
$tbody.="</tbody>";
	$html="<table class=\"priemVacantTable\"> {$thead} {$tbody} </table>";	
	return $html;
}

	public function showHtml($buffer=false){
		$html="";
		$memcache = new Memcache;
		$memcache->addServer('unix:///tmp/memcached.sock', 0);
		$html="";$memcache->get($this->cashGUID);
		if(strlen($html)<255 || $html==false || $this->isEditmode()) {
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

}//class priemVacant