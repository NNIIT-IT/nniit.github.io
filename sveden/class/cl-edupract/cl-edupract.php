<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class edupract extends  iasmuinfo{
	const cashTime=6000;
	const arBlocks=array(59,61);
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
		$this->cashGUID="edupract_".($this->ovz)."_".$this->edulevelID;
	}
	private function generateItems2($log){

$sql=<<<ssql
SELECT 
pract.id as practId,
pract.DETAIL_TEXT as disclist,
opp.id as oppid,
opp.IBLOCK_SECTION_ID as sectionID,
FLOOR(prPract.#GOD#) as oppYear,
if(prPract.#OCH1# is not null,FLOOR(prPract.#OCH1#),0) as och1,
if(prPract.#OCH2# is not null,FLOOR(prPract.#OCH2#),0) as och2,
if(prPract.#OCH3# is not null,FLOOR(prPract.#OCH3#),0) as och3,
if(prPract.#OCH4# is not null,FLOOR(prPract.#OCH4#),0) as och4,
if(prPract.#OCH5# is not null,FLOOR(prPract.#OCH5#),0) as och5,
if(prPract.#PROIZVOCH# is not null,FLOOR(prPract.#PROIZVOCH#),0) as och6,
if(prPract.#OCHZ1# is not null,FLOOR(prPract.#OCHZ1#),0) as ochz1,
if(prPract.#OCHZ2# is not null,FLOOR(prPract.#OCHZ2#),0) as ochz2,
if(prPract.#OCHZ3# is not null,FLOOR(prPract.#OCHZ3#),0) as ochz3,
if(prPract.#OCHZ4# is not null,FLOOR(prPract.#OCHZ4#),0) as ochz4,
if(prPract.#OCHZ5# is not null,FLOOR(prPract.#OCHZ5#),0) as ochz5,
if(prPract.#DIPLOMOCH# is not null,FLOOR(prPract.#DIPLOMOCH#),0) as ochz6,
if(prPract.#ZOCH1# is not null,FLOOR(prPract.#ZOCH1#),0) as zoch1,
if(prPract.#ZOCH2# is not null,FLOOR(prPract.#ZOCH2#),0) as zoch2,
if(prPract.#ZOCH3# is not null,FLOOR(prPract.#ZOCH3#),0) as zoch3,
if(prPract.#ZOCH4# is not null,FLOOR(prPract.#ZOCH4#),0) as zoch4,
if(prPract.#ZOCH5# is not null,FLOOR(prPract.#ZOCH5#),0) as zoch5,
if(prPract.#UCHEBOCHZ# is not null,FLOOR(prPract.#UCHEBOCHZ#),0) as zoch6,
if(prOpp.#ADEDU#>0,1,0) as ovz,
if(prPract.#OCH#>0,1,0) as och,
if(prPract.#ZOCH#>0,1,0) as zoch,
if(prPract.#OZCH#>0,1,0) as ozch,
if(prOpp.#BEEDU#>0,1,0) as beEdu,
if(prOpp.#HIDE#>0,1,0) as hide,
prPract.#UCHEBOCH# as uchebOch,
prPract.#PROIZVOCH# as proizvOch,
prPract.#DIPLOMOCH# as diplomOch,
prPract.#UCHEBOCHZ# as uchebOchz,
prPract.#PROIZVOCHZ# as proizvOchz,
prPract.#DIPLOMOCHZ# as diplomOchz,
prPract.#UCHEBZOCH# as uchebZoch,
prPract.#PROIZVZOCH# as proizvZoch,
prPract.#DIPLOMZOCH# as diplomZoch,
prOpp.#SROKOBUCH# as arSrokObuch,
0 as fgospp,
bs.id as section, 
bs.name as level, 
prOpp.#EDUCODE# as educode, 
prOpp.#SROKOBUCH# as eduFormRec,
prOpp.#EDUEL# as uduel,
opp.name as oppname, 
if(prOpp.#HIDE#>0,1,0) as eduHide,
if(prOpp.#PROFILE# is null,"",prOpp.#PROFILE#) as profile, 

concat(f.SUBDIR,"/", f.FILE_NAME,";", trim(f.DESCRIPTION),";",f.id)as splan
from  b_iblock_element opp 
left join b_iblock_section bs on bs.id=opp.IBLOCK_SECTION_ID
left join b_iblock_element_prop_s#iblock0# prOpp on prOpp.IBLOCK_ELEMENT_ID=opp.id 
left join b_iblock_element_prop_s#iblock1# prPract on prPract.#OPP# =opp.id 
left join b_iblock_element pract on pract.id=prPract.IBLOCK_ELEMENT_ID
left join b_iblock_element_prop_m#iblock0# prOppm on prOppm.IBLOCK_ELEMENT_ID=opp.id and prOppm.IBLOCK_PROPERTY_ID=350 
left join b_file f on f.id=prPract.#UCHPLAN# and not f.FILE_NAME is NULL  
where opp.IBLOCK_ID=#iblock0# 
and (prOpp.#HIDE# is null)
and (((((now()>opp.ACTIVE_FROM) or (opp.ACTIVE_FROM is NULL)) and ((now()<=opp.ACTIVE_TO)or (opp.ACTIVE_TO is NULL))) and opp.ACTIVE="Y")  or opp.id is NULL) 
and (((((now()>pract.ACTIVE_FROM) or (pract.ACTIVE_FROM is NULL)) and ((now()<=pract.ACTIVE_TO)or (pract.ACTIVE_TO is NULL))) and pract.ACTIVE="Y")  or pract.id is NULL) and (prOpp.#ADEDU# != 22955 or prOpp.#ADEDU# is null)  
 #where#
group by opp.id,prPract.IBLOCK_ELEMENT_ID,prOpp.#BEEDU#
order by prOpp.#ADEDU#,bs.id, prOpp.#EDUCODE#, prPract.#GOD#
ssql;

$where="";
		if($this->ovz==0) $where="and (prOpp.#ADEDU#=0  or prOpp.#ADEDU# is null) ";
		if($this->ovz==1) $where="and prOpp.#ADEDU#>0 ";
		if($this->edulevelID>0) $where=" and opp.IBLOCK_SECTION_ID=".$this->edulevelID;
		if(!$this->isEditMode())$where.=" and (not(prOpp.#HIDE#>0) or prOpp.#HIDE# is NULL or prOpp.#HIDE#=0) ";
		
		$sql=str_replace("#where#", $where,$sql);
$sql=$this->sqltoiblock($sql,$this::arBlocks);
		$rez=$this->BD->query($sql);
		
		while ($rec=$rez->fetch()){
		//var_dump($rec);
			$this->listitems[$rec["section"]]["name"]=$rec["level"];
			$this->listitems[$rec["section"]]["id"]=intval($rec["sectionID"]);

			$row=array();
			$row["eduCode"]=$rec["educode"];
			$row["oppName"]=$rec["oppname"];
			$row["hide"]=$rec["eduHide"];
			$row["oppYear"]=$rec["oppYear"];
			$profile=preg_replace('/^(\d+\.\d+\.\d+).(\-|\–|\s)/',"",$rec["profile"]);
			//$profile=trim(preg_replace('/^(\-|\–)/',"",$profile));
			$row["oppProfile"]=$profile;
			$row["eduLevel"]=$rec["level"];
			$row["fgospp"]=$rec["fgospp"];
			$row["beEdu"]=intval($rec["beEdu"]);
			$row["oppid"]=$rec["oppid"];
			$row["disclist"]="";
			$aruduel=unserialize($rec["uduel"]);
			$row["uduel"]="";
			if(in_array(10881,$aruduel["VALUE"]) || count($aruduel["VALUE"])==0) $row["uduel"]="нет";
			else{
				if (in_array(10880,$aruduel["VALUE"]))	 $row["uduel"].="дистанционные технологии<br>";
				if (in_array(10879,$aruduel["VALUE"]))	 $row["uduel"].="электронное обучение<br>";
			}
			$shtml="";
			$formOK=false;
			$xrow=array();
			$eduFormAr=unserialize($rec["eduFormRec"]);
			if(strlen($rec["disclist"])>3) {
				$shtml.="<span title=\"Список дисциплин, модулей\" class=\"linkicon oppfiles\" onclick=\" showcell('#id#','{$row["oppYear"]}',{$row["beEdu"]})\" >{$row["oppYear"]} г.</span>";
				$shtml.="<div style=\"display:none\"><div id=\"#id#\">{$rec["disclist"]}</div></div>";
			}else {
				$arplan=explode(";",$rec["splan"]);
				$arplan[1]=$rec["oppYear"];
				if($arplan[0]!=""){
					$p=urlencode($arplan[0]);
					$shtml.="<a class=\"linkicon\" href=\"/upload/{$p}\" download target=\"blank\">{$arplan[1]}</a><br>";
				}
			}
			if(isset($eduFormAr["s1"])) if ($eduFormAr["s1"]!="") {
				$xrow[0]["eduForm"]=($rec["beEdu"])?"Full time / Очная форма":"Очная форма";
				$xrow[0]["ucheb"]= intval($rec["uchebOch"]);
				$xrow[0]["proizv"]= intval($rec["proizvOch"]);
				$xrow[0]["diplom"]= intval($rec["diplomOch"]);
				$formOK=$formOK || (($xrow[0]["ucheb"]+$xrow[0]["ucheb"]+$xrow[0]["diplom"])>0);
				
			}
			if(isset($eduFormAr["s2"])) if ($eduFormAr["s2"]!="") {
				$xrow[1]["eduForm"]=(($rec["beEdu"])?"Part-time / Заочная форма":"Заочная форма");
				$xrow[1]["ucheb"]= intval($rec["uchebZoch"]);
				$xrow[1]["proizv"]= intval($rec["proizvZoch"]);
				$xrow[1]["diplom"]= intval($rec["diplomZoch"]);
				$formOK=$formOK || (($xrow[1]["ucheb"]+$xrow[1]["ucheb"]+$xrow[1]["diplom"])>0);
			}
			if(isset($eduFormAr["s3"])) if ($eduFormAr["s3"]!="") {

				$xrow[2]["eduForm"]=($rec["beEdu"])?"Part-time / Очно-заочная форма":"Очно-заочная форма";
				$xrow[2]["ucheb"]= intval($rec["uchebOchz"]);
				$xrow[2]["proizv"]= intval($rec["proizvOchz"]);
				$xrow[2]["diplom"]= intval($rec["diplomOchz"]);
				$formOK=$formOK || (($xrow[2]["ucheb"]+$xrow[2]["ucheb"]+$xrow[2]["diplom"])>0);
			}
			//if($formOK)
			foreach ($xrow as $formId=>$arform){
				$ikey=$rec["educode"]."_".$profile."_".$row["oppYear"]."_".$formId."_".$row["beEdu"].$row["oppid"];
				
				$newAr=$row+$arform;
				$newAr["disclist"]=str_replace(array("#id#","\r\n"),array($ikey,"<br>"),$shtml);
				if($rec["ovz"]) $this->listitems[$rec["section"]]["itemsovz"][$ikey]=$newAr;
				//if(!$rec["ovz"]&&!$rec["beEdu"]) 
					if(!$rec["ovz"]) 
					$this->listitems[$rec["section"]]["items"][$ikey]=$newAr;
			}
		}
	

}


public function showHtml($buffer=false){
$tblH1="<table class=\"practtable\"><thead>";
$tblH2="</thead><tbody>";
$ZET=<<<htmlh
<tr class="captrow">
<th rowspan="2">Код</th>
<th rowspan="2">Наименование профессии, специальности,направления подготовки, наименование группы научных специальностей</th>
<th rowspan="2">Образовательная программа, направленность,профиль, шифр и наименование научной специальности</th>
<th rowspan="2" class="hide">Уровень образования</th>
<th rowspan="2">Год начала подго&shy;товки</th>
<th rowspan="2">Реализуемые формы обучения</th>
<th rowspan="2">Учебные предметы, курсы, дисциплины (модули)</th>
<th rowspan="2">Использование при реализации образовательных программ электронного обучения и дистанционных образовательных технологий</th>
<th colspan="3">Наличие практики (з.е.)</th>
</tr><tr class="captrow">
<th>учеб&shy;ная</th>
<th>производ&shy;ственная</th>
<th>пред&shy;диплом&shy;ная прак&shy;тика для выпол&shy;нения выпус&shy;кной квалифи&shy;кацион&shy;ной работы</th>
</tr>
htmlh;
$HORS=<<<htmlhspo
<tr class="captrow">
<th rowspan="2">Код</th>
<th rowspan="2">Наименование профессии, специальности,направления подготовки, наименование группы научных специальностей</th>
<th rowspan="2">Образовательная программа, направленность,профиль, шифр и наименование научной специальности</th>
<th rowspan="2" class="hide">Уровень образования</th>
<th rowspan="2">Год начала подго&shy;товки</th>
<th rowspan="2">Реализуемые формы обучения</th>
<th rowspan="2">Учебные предметы, курсы, дисциплины (модули)</th>
<th rowspan="2">Использование при реализации образовательных программ электронного обучения и дистанционных образовательных технологий</th>
<th colspan="3">Наличие практики (час)</th>
</tr><tr class="captrow">
<th>учеб&shy;ная</th>
<th>производ&shy;ственная</th>
<th>пред&shy;диплом&shy;ная прак&shy;тика для выпол&shy;нения выпус&shy;кной квалифи&shy;кацион&shy;ной работы</th>
</tr>
htmlhspo;
$memcache = new Memcache;
$memcache->addServer('unix:///tmp/memcached.sock', 0);
if(intval($this->ovz)==1){
	$cashGUID="edupract_1_".$this->edulevelID;
}ELSE{
	$cashGUID="edupract_2_".$this->edulevelID;
}
$headerprint=false;
	$html="";//trim($memcache->get($cashGUID));
if (strlen($html)<12110 || $this->isEditmode()){//2110

	if($this->ovz==1){ 	
		$html="<h2>Адаптированные образовательные программы, наличие практики</h2>";
	}
	else {	
		$html="<h2>Неадаптированные образовательные программы, наличие практики</h2>";
	}
	$html.=$this->generateItems2(false);

	//$html.=count($this->listitems);
	$html.=$tblH1;

	foreach($this->listitems as $sectionID=>$section){

		

		if(count($section["items"])>0) {
			$html.="<tr class=\"captrow1\"><th colspan=\"11\">".$section["name"]."<!--".$section["id"]."--></th></tr>";
		if($section["id"]!=3743) $html.=$ZET; else $html.=$HORS;
		if(!$headerprint) $html.=$tblH2;
		$headerprint=true;
		}
		//не адаптированные программы
		if($this->ovz==0 || $this->ovz==2){
		if(count($section["items"])>0){
			$cnt=count($section["items"]);
			$spCodOld=0;
			ksort($section["items"]);
			foreach($section["items"] as $item){
				$oo=$item["uduel"]+$item["ucheb"]+$item["proizv"]+$item["diplom"]+1;
				if($oo>0){

					$html.="<tr itemprop=\"eduPr\"";
					if($item["hide"]) {$html.=" #hide# ";$cnt--;}
					if ($spCodOld!=$item["eduCode"]) {$html.=" style=\"border-top: solid #0000003d 2pt;\"";$spCodOld=$item["eduCode"];}
					$html.="><td itemprop=\"eduCode\">{$item["eduCode"]}</td>";
					$html.="<td><span itemprop=\"eduName\">{$item["oppName"]}</span>";
					if($item["fgospp"]==1) $html.=" (ФГОС 3++)";
					if($item["beEdu"]==1) $html.=" (билингвальное обучение)";
					
	
					$html.="</td>";
					if($item["oppProfile"]==""){$item["oppProfile"]=$this::emptyCell;}
					$html.="<td itemprop=\"eduProf\">{$item["oppProfile"]}</td>";
	
					$html.="<td itemprop=\"eduLevel\" class=\"hide\">{$item["level"]}</td>";
					if(trim($item["oppYear"])===""){$item["oppYear"]="набор не проводится";	}
					$html.="<td itemprop=\"eduYear\">{$item["oppYear"]}</td>";
	
					$html.="<td itemprop=\"eduForm\">{$item["eduForm"]}</td>";
					if($item["disclist"]==""){$item["disclist"]=$this::emptyCell;}
					$html.="<td ><div itemprop=\"eduDisc\" class=\"link\">{$item["disclist"]}</div></td>";
					$item["ucheb"]=($item["ucheb"]==0)?"нет":$item["ucheb"];
					$item["proizv"]=($item["proizv"]==0)?"нет":$item["proizv"];
					$item["diplom"]=($item["diplom"]==0)?"нет":$item["diplom"];

					$html.="<td itemprop=\"eduEl\">{$item["uduel"]}</td>";
					$html.="<td itemprop=\"eduPr\">{$item["ucheb"]}</td>";
					$html.="<td itemprop=\"eduPr\">{$item["proizv"]}</td>";
					$html.="<td itemprop=\"eduPr\">{$item["diplom"]}</td>";
					$html.="</tr>";
				}
			}
		} 
		if(count($section["items"])==0 || $cnt==0){

				$html.="<tr itemprop=\"eduPr\" class=\"hide\" >";
				$html.="<td itemprop=\"eduCode\" >".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduName\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduProf\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduLevel\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduYear\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduForm\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduDisc\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduEl\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduPr\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduPr\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduPr\">".$this::emptyCell."</td>";
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
					if($item["oppProfile"]==""){$item["oppProfile"]=$this::emptyCell;}
					$html.="<td itemprop=\"eduProf\">{$item["oppProfile"]}</td>";

					$html.="<td itemprop=\"eduLevel\" class=\"hide\">{$item["level"]}</td>";
					if(trim($item["oppYear"])===""){$item["oppYear"]="набор не проводится";	}
					$html.="<td itemprop=\"eduYear\">{$item["oppYear"]}</td>";
					
					$html.="<td itemprop=\"eduForm\">{$item["eduForm"]}</td>";
					if($item["disclist"]==""){$item["disclist"]="-";}
					$html.="<td itemprop=\"eduDisc\">{$item["disclist"]}</td>";

					$item["ucheb"]=($item["ucheb"]==0)?"-":$item["ucheb"];
					$item["proizv"]=($item["proizv"]==0)?"-":$item["proizv"];
					$item["diplom"]=($item["diplom"]==0)?"-":$item["diplom"];

					$html.="<td itemprop=\"eduEl\">{$item["uduel"]}</td>";
					$html.="<td itemprop=\"eduPr\">{$item["ucheb"]}</td>";
					$html.="<td itemprop=\"eduPr\">{$item["proizv"]}</td>";
					$html.="<td itemprop=\"eduPr\">{$item["diplom"]}</td>";
					$html.="</tr>";
				}
			} 
			
			if(count($section["itemsovz"])==0 || $cnt==0){
				$html.="<tr itemprop=\"adeduPr\" class=\"hide\" >";
				$html.="<td itemprop=\"eduCode\" >".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduName\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduProf\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduLevel\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduYear\">".$this::emptyCell."</td>";

				$html.="<td itemprop=\"eduForm\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduDisc\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduEl\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduPr\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduPr\">".$this::emptyCell."</td>";
				$html.="<td itemprop=\"eduPr\">".$this::emptyCell."</td>";
				$html.="</tr>";
			}
		}
	}
	if (count($this->listitems)==0){
		$html0="<td itemprop=\"eduCode\">".$this::emptyCell."</td>";
		$html0.="<td itemprop=\"eduName\">".$this::emptyCell."</td>";
		$html0.="<td itemprop=\"eduProf\">".$this::emptyCell."</td>";
		$html0.="<td itemprop=\"eduLevel\">".$this::emptyCell."</td>";
		$html0.="<td itemprop=\"eduYear\">".$this::emptyCell."</td>";
		$html0.="<td itemprop=\"eduForm\">".$this::emptyCell."</td>";
		$html0.="<td itemprop=\"eduEl\">".$this::emptyCell."</td>";
		$html0.="<td itemprop=\"eduDisc\">".$this::emptyCell."</td>";
		$html0.="<td itemprop=\"eduPr\">".$this::emptyCell."</td>";
		$html0.="<td itemprop=\"eduPr\">".$this::emptyCell."</td>";
		$html0.="<td itemprop=\"eduPr\">".$this::emptyCell."</td>";
		$html0.="</tr>";


		if($this->ovz==0 || $this->ovz==2){
			$html.="<tr class=\"hide\" itemprop=\"eduPr\" >".$html0;
			$html.="<tr><td colspan=\"11\"><span style=\"font-size: 1.5em;\">Образовательные программы не реализуются</span></td></tr>";
		}
		if($this->ovz==1 || $this->ovz==2){
			$html.="<tr class=\"hide\" itemprop=\"adeduPr\" >".$html0;
			$html.="<tr><td colspan=\"11\"><span style=\"font-size: 1.5em;\">Адаптированные образовательные программы не реализуются</span></td></tr>";
		}
		

	}
	$html.="</tbody></table>";
	$memcache->set($cashGUID, $html, false, $this->cashTime);
	}else{
		$html.=strlen($html);
	}
	$memcache->close();	
	if($this->isEditMode())	
		$html=str_replace(array("#hide#","#noitems#"),array("class=\"gray\"","class=\"hide\""),$html);
	else
		$html=str_replace(array("#hide#","#noitems#"),array("class=\"hide\"","class=\"gray\""),$html);
		
	if($buffer) return $html; else echo $html;
	}//showHtml
}//class
