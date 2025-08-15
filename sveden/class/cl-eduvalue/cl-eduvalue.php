<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class eduvalue extends  iasmuinfo{
	const cashTime=6000;
	private $edulevelID;
	private $ovz;
	private $listitems=array();
	private $cashGUID;
	public function setparams($params){
		$this->edulevelID=0;
		$this->ovz=0;
		if(isset($params["edulevelID"])) {
			$this->edulevelID=intval($params["edulevelID"]);

		}
		if(isset($params["ovz"])) {
			$this->ovz=intval($params["ovz"]);
		}
		$this->cashGUID="eduvalue_".($this->ovz)."_".$this->edulevelID;
	}
	private function generateItems(){
$sql=<<<ssql
SELECT 
pract.id as practId,
pract.DETAIL_TEXT as disclist,
opp.id as oppid,
FLOOR(prPract.PROPERTY_466) as oppYear,
if(prPract.PROPERTY_451 is not null,FLOOR(prPract.PROPERTY_451),0) as och1,
if(prPract.PROPERTY_452 is not null,FLOOR(prPract.PROPERTY_452),0) as och2,
if(prPract.PROPERTY_453 is not null,FLOOR(prPract.PROPERTY_453),0) as och3,
if(prPract.PROPERTY_454 is not null,FLOOR(prPract.PROPERTY_454),0) as och4,
if(prPract.PROPERTY_455 is not null,FLOOR(prPract.PROPERTY_455),0) as och5,
if(prPract.PROPERTY_472 is not null,FLOOR(prPract.PROPERTY_472),0) as och6,
if(prPract.PROPERTY_456 is not null,FLOOR(prPract.PROPERTY_456),0) as ochz1,
if(prPract.PROPERTY_457 is not null,FLOOR(prPract.PROPERTY_457),0) as ochz2,
if(prPract.PROPERTY_458 is not null,FLOOR(prPract.PROPERTY_458),0) as ochz3,
if(prPract.PROPERTY_459 is not null,FLOOR(prPract.PROPERTY_459),0) as ochz4,
if(prPract.PROPERTY_460 is not null,FLOOR(prPract.PROPERTY_460),0) as ochz5,
if(prPract.PROPERTY_473 is not null,FLOOR(prPract.PROPERTY_473),0) as ochz6,
if(prPract.PROPERTY_461 is not null,FLOOR(prPract.PROPERTY_461),0) as zoch1,
if(prPract.PROPERTY_462 is not null,FLOOR(prPract.PROPERTY_462),0) as zoch2,
if(prPract.PROPERTY_463 is not null,FLOOR(prPract.PROPERTY_463),0) as zoch3,
if(prPract.PROPERTY_464 is not null,FLOOR(prPract.PROPERTY_464),0) as zoch4,
if(prPract.PROPERTY_465 is not null,FLOOR(prPract.PROPERTY_465),0) as zoch5,
if(prPract.PROPERTY_474 is not null,FLOOR(prPract.PROPERTY_474),0) as zoch6,
if(prOpp.PROPERTY_578=22955,1,0) as ovz,
if(prPract.PROPERTY_468=11799,1,0) as och,
if(prPract.PROPERTY_469=11800,1,0) as zoch,
if(prPract.PROPERTY_469=11802,1,0) as ozch,
if(prOpp.PROPERTY_577>0,1,0) as beEdu,
if(prOpp.PROPERTY_628>0,1,0) as hide,
prPract.PROPERTY_471 as uchebOch,
prPract.PROPERTY_472 as proizvOch,
prPract.PROPERTY_473 as diplomOch,
prPract.PROPERTY_474 as uchebOchz,
prPract.PROPERTY_475 as proizvOchz,
prPract.PROPERTY_476 as diplomOchz,
prPract.PROPERTY_477 as uchebZoch,
prPract.PROPERTY_478 as proizvZoch,
prPract.PROPERTY_479 as diplomZoch,
prOpp.PROPERTY_90 as arSrokObuch,
if(prOpp.PROPERTY_603=25864,1,0) as fgospp,
bs.id as section, 
bs.name as level, 
prOpp.PROPERTY_84 as educode, 
prOpp.PROPERTY_90 as eduFormRec,
prOpp.PROPERTY_423 as uduel,
opp.name as oppname, 
if(prOpp.PROPERTY_628=28805,1,0) as eduHide,
if(prOpp.PROPERTY_121 is null,"",prOpp.PROPERTY_121) as profile, 

concat(f.SUBDIR,"/", f.FILE_NAME,";", trim(f.DESCRIPTION),";",f.id)as splan
from  b_iblock_element opp 
LEFT JOIN `b_iblock_section_element` se on se.IBLOCK_ELEMENT_ID=opp.id
LEFT JOIN `b_iblock_section` bs on bs.id=se.IBLOCK_SECTION_ID
left join b_iblock_element_prop_s104 prOpp on prOpp.IBLOCK_ELEMENT_ID=opp.id 
left join b_iblock_element_prop_s161 prPract on prPract.PROPERTY_450 =opp.id 
left join b_iblock_element pract on pract.id=prPract.IBLOCK_ELEMENT_ID
left join b_iblock_element_prop_m104 prOppm on prOppm.IBLOCK_ELEMENT_ID=opp.id and prOppm.IBLOCK_PROPERTY_ID=350 
left join b_file f on f.id=prPract.PROPERTY_654 and not f.FILE_NAME is NULL  
where opp.IBLOCK_ID=104 
and prOpp.PROPERTY_628 is null
and (((((now()>opp.ACTIVE_FROM) or (opp.ACTIVE_FROM is NULL)) and ((now()<=opp.ACTIVE_TO)or (opp.ACTIVE_TO is NULL))) and opp.ACTIVE="Y")  or opp.id is NULL) 
and (((((now()>pract.ACTIVE_FROM) or (pract.ACTIVE_FROM is NULL)) and ((now()<=pract.ACTIVE_TO)or (pract.ACTIVE_TO is NULL))) and pract.ACTIVE="Y")  or pract.id is NULL) #where#

group by opp.id,prPract.IBLOCK_ELEMENT_ID
order by prOpp.PROPERTY_578,bs.id, prOpp.PROPERTY_84, prPract.PROPERTY_466
ssql;

$where="";
		if($this->ovz==0) $where="and (prOpp.PROPERTY_578 != 22955 or prOpp.PROPERTY_578 is null) ";
		if($this->ovz==1) $where="and prOpp.PROPERTY_578 = 22955";
		if($this->edulevelID>0) $where=" and opp.IBLOCK_SECTION_ID=".$this->edulevelID;
		if(!$this->isEditMode())$where.=" and not(prOpp.PROPERTY_628>0)";
		
		$sql=str_replace("#where#", $where,$sql);
		$rez=$this->BD->query($sql);
		//echo $sql;
		while ($rec=$rez->fetch()){
		//var_dump($rec);
			$this->listitems[$rec["section"]]["name"]=$rec["level"];
			$row=array();
			$row["eduCode"]=$rec["educode"];
			$row["oppName"]=$rec["oppname"];
			$row["hide"]=$rec["eduHide"];
			$row["oppYear"]=$rec["oppYear"];
			$profile=preg_replace('/^(\d+\.\d+\.\d+).(\-|\вЂ“|\s)/',"",$rec["profile"]);
			//$profile=trim(preg_replace('/^(\-|\вЂ“)/',"",$profile));
			$row["oppProfile"]=$profile;
			$row["eduLevel"]=$rec["level"];
			$row["fgospp"]=$rec["fgospp"];
			$row["beEdu"]=$rec["beEdu"];
			$row["oppid"]=$rec["oppid"];
			$row["disclist"]="";
			$row["god"][1]["och"]=$rec["och1"];
			$row["god"][2]["och"]=$rec["och2"];
			$row["god"][3]["och"]=$rec["och3"];
			$row["god"][4]["och"]=$rec["och4"];
			$row["god"][5]["och"]=$rec["och5"];
			$row["god"][6]["och"]=$rec["och6"];

			$row["god"][1]["ochz"]=$rec["ochz1"];
			$row["god"][2]["ochz"]=$rec["ochz2"];
			$row["god"][3]["ochz"]=$rec["ochz3"];
			$row["god"][4]["ochz"]=$rec["ochz4"];
			$row["god"][5]["ochz"]=$rec["ochz5"];
			$row["god"][6]["ochz"]=$rec["ochz6"];

			$row["god"][1]["zoch"]=$rec["zoch1"];
			$row["god"][2]["zoch"]=$rec["zoch2"];
			$row["god"][3]["zoch"]=$rec["zoch3"];
			$row["god"][4]["zoch"]=$rec["zoch4"];
			$row["god"][5]["zoch"]=$rec["zoch5"];
			$row["god"][6]["zoch"]=$rec["zoch6"];
			$ikey=$row["id"];
			$aruduel=unserialize($rec["uduel"]);
			$row["uduel"]="";
			if(in_array(10881,$aruduel["VALUE"]) || count($aruduel["VALUE"])==0) $row["uduel"]="РЅРµС‚";
			else{
				if (in_array(10880,$aruduel["VALUE"]))	 $row["uduel"].="дистанционные технологии<br>";
				if (in_array(10879,$aruduel["VALUE"]))	 $row["uduel"].="электронное обучение<br>";
			}
			$this->listitems[$rec["section"]]["items"][$ikey]=$row;
			
		}

}


public function showHtml($buffer=false){
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html=trim($memcache->get($this->cashGUID));
	if ($html=="" || $html==false || $this->isEditmode()){
	$this->generateItems();

	if($this->ovz==1){ $html="<h2>Образовательная программа (наличие практики), адаптированные образовательные программы</h2>";}
	else {	$html="<h2>Образовательная программа (наличие практики)</h2>";}
$html.=<<<htmlh
<table class="practtable">
<thead>
<tr>
<th>Код</th>
<th>Наименование специальности/направления подготовки</th>
<th class="hide">Уровень образования</th>
<th>Год начала подго&shy;товки</th>
<th>Профиль программы</th>
<th>Объем <br>программы</th>
<th>Очная форма обучения (зачетные единицы – з. е.)</th>
<th>Очно-заочная форма обучения (з. е.)</th>
<th>Заочная форма обучения (з. е.)</th>
</tr>
</thead>
<tbody>
htmlh;
//echo "<pre>";
//print_r($this->listitems);
//echo "</pre>";

	foreach($this->listitems as $sectionID=>$section){
		if(count($section["items"])>0) $html.="<tr><th colspan=\"11\">".$section["name"]."</th></tr>";
		//РЅРµ Р°РґР°РїС‚РёСЂРѕРІР°РЅРЅС‹Рµ РїСЂРѕРіСЂР°РјРјС‹
		if($this->ovz==0 || $this->ovz==2){
		if(count($section["items"])>0){
			$cnt=count($section["items"]);
			$spCodOld=0;
			foreach($section["items"] as $item){
				$html.="<tr itemprop=\"eduPr\"";
				if($item["hide"]) {$html.=" #hide# ";$cnt--;}
				if ($spCodOld!=$item["eduCode"]) {$html.=" style=\"border-top: solid #0000003d 2pt;\"";$spCodOld=$item["eduCode"];}
				$html.="><td itemprop=\"eduCode\">{$item["eduCode"]}</td>";
				$html.="<td><span itemprop=\"eduName\">{$item["oppName"]}</span>";
				if($item["fgospp"]==1) $html.=" (ФГОС 3++)";
				if($item["beEdu"]==1) $html.=" (билингвальное обучение)";
				$html.="</td>";
				$html.="<td itemprop=\"eduLevel\" class=\"hide\">{$item["level"]}</td>";
				$html.="<td itemprop=\"eduYear\">{$item["oppYear"]}</td>";
				$html.="<td itemprop=\"profile\">{$item["oppProfile"]}</td>";
				$html.="<td itemprop=\"eduForm\">{$item["eduForm"]}</td>";
				$html.="<td itemprop=\"eduDisc\">{$item["disclist"]}</td>";
				$html.="<td itemprop=\"eduEl\">{$item["uduel"]}</td>";
				$html.="<td itemprop=\"eduPr\">{$item["ucheb"]}</td>";
				$html.="<td itemprop=\"eduPr\">{$item["proizv"]}</td>";
				$html.="<td itemprop=\"eduPr\">{$item["diplom"]}</td>";
				$html.="</tr>";
			}
		} 
		if(count($section["items"])==0 || $cnt==0){

				$html.="<tr itemprop=\"eduPr\" >";
				$html.="<td itemprop=\"eduCode\">отсутствует</td>";
				$html.="<td itemprop=\"eduName\">отсутствует</td>";
				$html.="<td itemprop=\"eduLevel\" class=\"hide\">отсутствует</td>";
				$html.="<td itemprop=\"eduYear\">-</td>";
				$html.="<td itemprop=\"profile\">-</td>";
				$html.="<td itemprop=\"eduForm\">-</td>";
				$html.="<td itemprop=\"eduDisc\">-</td>";
				$html.="<td itemprop=\"eduEl\">-</td>";
				$html.="<td itemprop=\"eduPr\">-</td>";
				$html.="<td itemprop=\"eduPr\">-</td>";
				$html.="<td itemprop=\"eduPr\">-</td>";
				$html.="</tr>";
		}}
		if($this->ovz==1 || $this->ovz==2){
			if(count($section["itemsovz"])>0){
				$cnt=count($section["itemsovz"]);
				
				foreach($section["itemsovz"] as $item){
					$html.="<tr itemprop=\"adeduPr\"";
					if($item["hide"]) {$html.=" #hide# ";$cnt--;}
					$html.="><td itemprop=\"eduCode\">{$item["eduCode"]}</td>";
					$html.="<td><span itemprop=\"eduName\">{$item["oppName"]}</span>";
					if($item["fgospp"]==1) $html.=" (ФГОС 3++)";
				if($item["beEdu"]==1) $html.=" (билингвальное обучение)";
					$html.="</td>";
					$html.="<td itemprop=\"eduLevel\" class=\"hide\">{$item["level"]}</td>";
					$html.="<td itemprop=\"eduYear\">{$item["oppYear"]}</td>";
					$html.="<td itemprop=\"profile\">{$item["oppProfile"]}</td>";
					$html.="<td itemprop=\"eduForm\">{$item["eduForm"]}</td>";
					$html.="<td itemprop=\"eduDisc\">{$item["disclist"]}</td>";
					$html.="<td itemprop=\"eduEl\">{$item["uduel"]}</td>";
					$html.="<td itemprop=\"eduPr\">{$item["ucheb"]}</td>";
					$html.="<td itemprop=\"eduPr\">{$item["proizv"]}</td>";
					$html.="<td itemprop=\"eduPr\">{$item["diplom"]}</td>";
					$html.="</tr>";
				}
			} 
			
			if(count($section["itemsovz"])==0 || $cnt==0){
				$html.="<tr itemprop=\"eduPr\" >";
				$html.="<td itemprop=\"eduCode\">отсутствует</td>";
				$html.="<td itemprop=\"eduName\">отсутствует</td>";
				$html.="<td itemprop=\"eduLevel\" class=\"hide\">отсутствует</td>";
				$html.="<td itemprop=\"eduYear\">-</td>";
				$html.="<td itemprop=\"profile\">-</td>";
				$html.="<td itemprop=\"eduForm\">-</td>";
				$html.="<td itemprop=\"eduEl\">-</td>";
				$html.="<td itemprop=\"eduDisc\">-</td>";
				$html.="<td itemprop=\"eduPr\">-</td>";
				$html.="<td itemprop=\"eduPr\">-</td>";
				$html.="<td itemprop=\"eduPr\">-</td>";
				$html.="</tr>";
			}
		}
	}
	if (count($this->listitems)==0){
		$html0="<td itemprop=\"eduCode\">отсутствует</td>";
		$html0.="<td itemprop=\"eduName\">отсутствует</td>";
		$html0.="<td itemprop=\"eduLevel\" class=\"hide\">отсутствует</td>";
		$html0.="<td itemprop=\"eduYear\">-</td>";
		$html0.="<td itemprop=\"profile\">-</td>";
		$html0.="<td itemprop=\"eduForm\">-</td>";
		$html0.="<td itemprop=\"eduEl\">-</td>";
		$html0.="<td itemprop=\"eduDisc\">-</td>";
		$html0.="<td itemprop=\"eduPr\">-</td>";
		$html0.="<td itemprop=\"eduPr\">-</td>";
		$html0.="<td itemprop=\"eduPr\">-</td>";
		$html0.="</tr>";

		if($this->ovz==0 || $this->ovz==2){
			$html.="<tr itemprop=\"eduPr\" >".$html0;
		}
		if($this->ovz==1 || $this->ovz==2){
			$html.="<tr itemprop=\"adeduPr\" >".$html0;
		}
		

	}
	$html.="</tbody></table>";
	$memcache->set($this->cashGUID, $html, false, $this->cashTime);
	}
	$memcache->close();	
	if($this->isEditMode())	
		$html=str_replace(array("#hide#","#noitems#"),array("class=\"gray\"","class=\"hide\""),$html);
	else
		$html=str_replace(array("#hide#","#noitems#"),array("class=\"hide\"","class=\"gray\""),$html);
		
	if ($buffer) return $html; else echo $html;
	}//showHtml
}//class
