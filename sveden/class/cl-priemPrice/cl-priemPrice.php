<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class priemPrice extends  iasmuinfo{
	const cashTime=6000;
	private $cashGUID;
	private $listitems=array();
	private $eduLevelId=877;
	private $specGroup=1;
	public function setparams($params){
		$this->eduLevelId=0;
		if(isset($params["eduLevel"])) $this->eduLevelId=intval($params["eduLevel"]);
		
		if(isset($params["specGroup"])) $this->specGroup=intval($params["specGroup"]);
		$this->cashGUID="priemPrice_".$this->eduLevelId;
	}
	private function setRowDataX($eduLevel,$index1,$index2,$tmprow){
			$PRICE_RF=intval(preg_replace('/[^0-9]/', '', $tmprow["PRICE_RF"]));
			$PRICE_SNG=intval(preg_replace('/[^0-9]/', '', $tmprow["PRICE_SNG"]));				
			$PRICE_IG=intval(preg_replace('/[^0-9]/', '', $tmprow["PRICE_IG"]));				

			$PRICE_RFZ=intval(preg_replace('/[^0-9]/', '', $tmprow["PRICE_RFZ"]));
			$PRICE_SNGZ=intval(preg_replace('/[^0-9]/', '', $tmprow["PRICE_SNGZ"]));				
			$PRICE_IGZ=intval(preg_replace('/[^0-9]/', '', $tmprow["PRICE_IGZ"]));				

			$PRICE_RFV=intval(preg_replace('/[^0-9]/', '', $tmprow["PRICE_RFV"]));
			$PRICE_SNGV=intval(preg_replace('/[^0-9]/', '', $tmprow["PRICE_SNGV"]));				
			$PRICE_IGV=intval(preg_replace('/[^0-9]/', '', $tmprow["PRICE_IGV"]));				

			
			if(!isset($this->listitems[$eduLevel][$index1][$index2])){
				$item=$tmprow;
				$item["PRICE_RF"]=$PRICE_RF;
				$item["PRICE_SNG"]=$PRICE_SNG;
				$item["PRICE_IG"]=$PRICE_IG;
				$item["PRICE_RFZ"]=$PRICE_RFZ;
				$item["PRICE_SNGZ"]=$PRICE_SNGZ;
				$item["PRICE_IGZ"]=$PRICE_IGZ;
				$item["PRICE_RFV"]=$PRICE_RFV;
				$item["PRICE_SNGV"]=$PRICE_SNGV;
				$item["PRICE_IGV"]=$PRICE_IGV;
			} else {
				$item=$this->listitems[$eduLevel][$index1][$index2];
					$item["PRICE_RF"]=max($item["PRICE_RF"],$PRICE_RF);
					$item["PRICE_SNG"]=max($item["PRICE_SNG"],$PRICE_SNG);
					$item["PRICE_IG"]=max($item["PRICE_IG"],$PRICE_IG);
					$item["PRICE_RFZ"]=max($item["PRICE_RFZ"],$PRICE_RFZ);
					$item["PRICE_SNGZ"]=max($item["PRICE_SNGZ"],$PRICE_SNGZ);
					$item["PRICE_IGZ"]=max($item["PRICE_IZ"],$PRICE_IGZ);
					$item["PRICE_RFV"]=max($item["PRICE_RFV"],$PRICE_RFV);
					$item["PRICE_SNGV"]=max($item["PRICE_SNGV"],$PRICE_SNGV);
					$item["PRICE_IGV"]=max($item["PRICE_IGV"],$PRICE_IGV);
		
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
when bie.PROPERTY_377=217 then "ФГТ 2021"
else ""
end as beFgos,
bie.PROPERTY_404 as PRICE_RF,
bie.PROPERTY_405 as PRICE_SNG,
bie.PROPERTY_406 as PRICE_IG,
bie.PROPERTY_407 as PRICE_RFZ,
bie.PROPERTY_408 as PRICE_SNGZ,
bie.PROPERTY_409 as PRICE_IGZ,
bie.PROPERTY_410 as PRICE_RFV,
bie.PROPERTY_411 as PRICE_SNGV,
bie.PROPERTY_412 as PRICE_IGV 
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

			$tmprow=(array)$rec;
			$tmprow["eduProfile"]="";
			if(($rec["eduProfile"]!="") && ($this->eduLevelId!=878)) $tmprow["eduProfile"]=$rec["eduProfile"];

			//$arforms=unserialize($rec["beForm"]);
			$arforms=$this->unserform($rec["beForm"]);



			if (strlen($arforms[1])>4){
				$tmprow["learningTerm"]=$arforms["s1"];
				$tmprow["eduForm"]="Очная";
				if($this->specGroup==1) {
					$index=($eduCode."_1");
					$this->setRowDataX($rec["eduLevel"],1,$index,$tmprow);
				}
				else {
					$index=($tmprow["id"]."_1");
					$this->listitems[$rec["eduLevel"]][1][$index]=$tmprow;
				}
			}
			if (strlen($arforms[2])>4){
				$tmprow["learningTerm"]=$arforms["s2"];
				$tmprow["eduForm"]="Заочная";
				if($this->specGroup==1) {
					$index=($eduCode."_2");
					$this->setRowDataX($rec["eduLevel"],2,$index,$tmprow);
				}
				else {
					$index=($tmprow["id"]."_2");
					$this->listitems[$rec["eduLevel"]][2][$index]=$tmprow;
				}
			}
			if (strlen($arforms[3])>4){
				$tmprow["learningTerm"]=$arforms["s3"];
				$tmprow["eduForm"]="Очно-заочная";

				if($this->specGroup==1) {
					$index=($eduCode."_3");
					$this->setRowDataX($rec["eduLevel"],3,$index,$tmprow);
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

private function printTableX(){
	$emptyTable=true;
	$thead="<thead>";
	$thead.="<tr><th>Код специальности</th><th>Образовательная программа (направленность, профиль, шифр и наименование научной специальности)</th>";
	$thead.="<th>Для граждан РФ, Казахстана, Таджикистана, Кыргызстана, Белоруссии</th>";
	$thead.="<th>Для граждан других государств</th>";
	$thead.="<th>Для иностранных граждан с использованием языка посредника</th></tr>";
	$thead.="</thead>";
	$tbody="<tbody>";
	$formsName=array(1=>"Очная",2=>"Заочная",3=>"Очно-заочная");
	//echo "<pre>"; print_r($this->listitems);echo "</pre>";
	foreach($this->listitems as $levelName=>$forms){

		$tbody.="<tr><th colspan=\"5\">{$levelName}</th></tr>";
		for($f=1;$f<=3;$f++){
			$items=$forms[$f];
			$trows="";
			if(!is_array($items)) $items=array();
			foreach($items as $itemrow){
				$trow="<tr><td>".$itemrow["eduCode"]."</td>";
				
				if($f==1) {
					$PRICE_RF=$itemrow["PRICE_RF"];
					$PRICE_SNG=$itemrow["PRICE_SNG"];
					$PRICE_IG=$itemrow["PRICE_IG"];
	
				}
				if($f==2) {
					$PRICE_RF=$itemrow["PRICE_RFZ"];
					$PRICE_SNG=$itemrow["PRICE_SNGZ"];
					$PRICE_IG=$itemrow["PRICE_IGZ"];
				}
				if($f==3) {
					$PRICE_RF=$itemrow["PRICE_RFV"];
					$PRICE_SNG=$itemrow["PRICE_SNGV"];
					$PRICE_IG=$itemrow["PRICE_IGV"];
				}
				if($itemrow["eduProfile"]!="")$itemrow["eduProfile"]="(".$itemrow["eduProfile"].")";
				$trow.="<td >".$itemrow["eduName"]." <br><span class=\"blue\" title=\"направленность, профиль, шифр и наименование научной специальности\" >".$itemrow["eduProfile"]."</span></td>";	
				$PR=($itemrow["PRICE_RF"]>0)?(number_format($PRICE_RF, 0, ',', ' '))." &#8381;":"-";
				$PS=($itemrow["PRICE_SNG"]>0)?(number_format($PRICE_SNG, 0, ',', ' ')." &#8381;"):"-";
				$PG=($itemrow["PRICE_IG"]>0)?(number_format($PRICE_IG, 0, ',', ' ')." &#8381;"):"-";

				$trow.="<td>{$PR}</td><td>{$PS}</td><td>{$PG}</td>";	
				$trow.="</tr>";
				if($PRICE_RF>0 || $PRICE_SNG>0 || $PRICE_IG>0) $trows.=$trow;
			} //foreach items
			if($trows!=""){
				$tbody.="<tr ><th colspan=\"5\"><i>{$formsName[$f]} форма обучения</i></th></tr>".$trows;
				$emptyTable=false;
			}

		}//for
		if($emptyTable){
		$tbody.="<tr ><td colspan=\"5\">Стоимость платного обучения не утверждена или не предусмотрена</td></tr>";
		}
		$tbody.="</tbody>";
	}//foreach  listitems
	$html="<br><p><span style=\"color: black;
  font-size: 1.5em;
  font-weight: 600;
  border: solid thin #0000005c;
  border-radius: 1em;
  width: 1em;
  display: inline-block;
  text-align: center;
  background-color: #ffff9a;
  margin-right: 0.2em;
  height: 1em;\">! </span>Стоимость указана за один год обучения.</p><table class=\"priemPriceTable\"> {$thead} {$tbody} </table>";	

	return $html;
}

	public function showHtml($buffer=false){
		$html="";
		$memcache = new Memcache;
		$memcache->addServer('unix:///tmp/memcached.sock', 0);
		$html="";//$memcache->get($this->cashGUID);
		if(strlen($html)<255 || $html==false || $this->isEditmode()) {
			$this->generateItems();
			$html=$this->printTableX();
			$result = $memcache->replace( $this->cashGUID, $html);
			if( $result == false ){
				
				$memcache->set($this->cashGUID, $html, false, $this->cashTime);
			} 
		}
		$memcache->close();
		if($buffer) return $html; else echo $html;
	}//showHtml

}//class 