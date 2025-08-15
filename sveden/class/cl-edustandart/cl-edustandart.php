<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class edustandart extends  iasmuinfo{
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
		$this->cashGUID="edustandart";
	}
	private function generateItemsEdustandart(){
$sql=<<<ssql
SELECT distinct
opp.id as oppid,
opp.name as oppname,
opps.PROPERTY_84 as oppcode, 
if(opps.PROPERTY_121 is null, "", opps.PROPERTY_121) as profile, 
s.name as level,
if(doc.PREVIEW_TEXT is null,"",doc.PREVIEW_TEXT) as htxt,
if(f.id is NULL,"", concat("/upload/",f.SUBDIR,"/",f.FILE_NAME)) as filename,
if(docs.PROPERTY_649 is NULL,0,docs.PROPERTY_649) as doctype,
if(docs.IBLOCK_ELEMENT_ID is NULL,0,docs.IBLOCK_ELEMENT_ID) as docid
from  b_iblock_element opp 
left join b_iblock_section s on s.id=opp.IBLOCK_SECTION_ID
left join b_iblock_element_prop_s104 opps on opps.IBLOCK_ELEMENT_ID=opp.id
left join b_iblock_element_prop_s175 docs on docs.property_648=opp.id and docs.PROPERTY_649 in(43419,43420,43421,43422)
left join b_iblock_element_prop_m175 docm on docm.IBLOCK_ELEMENT_ID=docs.IBLOCK_ELEMENT_ID and docm.IBLOCK_PROPERTY_ID = 650  
left join b_iblock_element doc on doc.id=docs.IBLOCK_ELEMENT_ID
left join b_file f on f.id=docm.value
where 
opp.active = "Y" 
and opp.IBLOCK_ID=104
and (opps.PROPERTY_628 is NULL  or opps.PROPERTY_628="")
and (opps.PROPERTY_578 is NULL  or opps.PROPERTY_578 ="")  
and (opps.PROPERTY_577 is NULL  or opps.PROPERTY_577 ="")
group by oppcode,oppname, profile
order by level desc, oppcode,oppname
ssql;
//echo $sql;
$this->listitems=array();
	$rez=$this->BD->query($sql);
	while ($rec=$rez->fetch()){
		$opp=array();	
		$opp["level"]=$rec["level"];
		$opp["oppcode"]=$rec["oppcode"];
		$opp["oppname"]=$rec["oppname"];
		$opp["profile"]=$rec["profile"];
		$idd=md5($rec["oppcode"].$rec["profile"].$rec["docid"]);
		//echo 	$idd,$rec["oppcode"],$rec["profile"],$rec["docid"];
		if(!array_key_exists($idd,$this->listitems)){
			$opp["oppid"]=$rec["oppid"];
			$opp["docid"]=$rec["docid"];
			$opp["oppcode"]=$rec["oppcode"];
			$opp["oppname"]=$rec["oppname"];
			$opp["profile"]=$rec["profile"];
			$opp["eduFedDoc"]=array("",0,43419);
			$opp["eduStandartDoc"]=array("",0,43420);
			$opp["eduFedTreb"]=array("",0,43421);
			$opp["eduStandartTreb"]=array("",0,43422);
			$this->listitems[$idd]=$opp;
		}
		$doc_content="";
		if($rec["htxt"]!=""){
			$doc_content=strip_tags($rec["htxt"],"<a></a><br><br/>");
		} 
		if($doc_content==""){
			if($rec["filesrc"]!="") {
				if($rec["filename"]=="") $rec["filename"]=basename($rec["filesrc"]);
				$doc_content="<a href=\"{$rec["filesrc"]}\">{$rec["filename"]}</a>";
			}
		}
		if($doc_content!=""){
			if($rec["doctype"]==43419) $this->listitems[$idd]["eduFedDoc"]=array($doc_content,$rec["docid"],43419);
			if($rec["doctype"]==43420) $this->listitems[$idd]["eduStandartDoc"]=array($doc_content,$rec["docid"],43420);
			if($rec["doctype"]==43421) $this->listitems[$idd]["eduFedTreb"]=array($doc_content,$rec["docid"],43421);
			if($rec["doctype"]==43422) $this->listitems[$idd]["eduStandartTreb"]=array($doc_content,$rec["docid"],43422);
		}


	}
	//print_r($this->listitems);
}


public function showHtml($buffer=false){
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$cashGUID="edustandart";
	$html=trim($memcache->get($cashGUID));
	if (strlen($html)<200 || $html==false || $this->isEditmode()){
	$this->generateItemsEdustandart();
	
$html=<<<htmlh
<table class="simpletable">
<thead>
<tr>
<th></th>
<th>РќР°Р·РІР°РЅРёРµ СЃРїРµС†РёР°Р»СЊРЅРѕСЃС‚Рё РёР»Рё РЅР°РїСЂР°РІР»РµРЅРёСЏ РїРѕРґРіРѕС‚РѕРІРєРё, РЅР°СѓС‡РЅРѕР№ СЃРїРµС†РёР°Р»СЊРЅРѕСЃС‚Рё</th>
<th>РЈСЂРѕРІРµРЅСЊ РѕР±СЂР°Р·РѕРІР°РЅРёСЏ</th>
<th>
Рћ РїСЂРёРјРµРЅСЏРµРјС‹С… С„РµРґРµСЂР°Р»СЊРЅС‹С… РіРѕСЃСѓРґР°СЂСЃС‚РІРµРЅРЅС‹С… РѕР±СЂР°Р·РѕРІР°С‚РµР»СЊРЅС‹С… СЃС‚Р°РЅРґР°СЂС‚Р°С… СЃ СЂР°Р·РјРµС‰РµРЅРёРµРј РёС… РєРѕРїРёР№ 
Рё (РёР»Рё) РіРёРїРµСЂСЃСЃС‹Р»РєРё РЅР° СЃРѕРѕС‚РІРµС‚СЃС‚РІСѓСЋС‰РёРµ РґРѕРєСѓРјРµРЅС‚С‹
</th>
<th>
РћР± СѓС‚РІРµСЂР¶РґРµРЅРЅС‹С… РѕР±СЂР°Р·РѕРІР°С‚РµР»СЊРЅС‹С… СЃС‚Р°РЅРґР°СЂС‚Р°С…, СЃ СЂР°Р·РјРµС‰РµРЅРёРµРј РёС… РІ С„РѕСЂРјРµ СЌР»РµРєС‚СЂРѕРЅРЅРѕРіРѕ РґРѕРєСѓРјРµРЅС‚Р°, РїРѕРґРїРёСЃР°РЅРЅРѕРіРѕ СЌР»РµРєС‚СЂРѕРЅРЅРѕР№ РїРѕРґРїРёСЃСЊСЋ
Рё (РёР»Рё) РіРёРїРµСЂСЃСЃС‹Р»РєРё РЅР° СЃРѕРѕС‚РІРµС‚СЃС‚РІСѓСЋС‰РёР№ СЌР»РµРєС‚СЂРѕРЅРЅС‹Р№ РґРѕРєСѓРјРµРЅС‚, РїРѕРґРїРёСЃР°РЅРЅРѕРіРѕ СЌР»РµРєС‚СЂРѕРЅРЅРѕР№ РїРѕРґРїРёСЃСЊСЋ
</th>
<th>
Рћ РїСЂРёРјРµРЅСЏРµРјС‹С… С„РµРґРµСЂР°Р»СЊРЅС‹С… РіРѕСЃСѓРґР°СЂСЃС‚РІРµРЅРЅС‹С… С‚СЂРµР±РѕРІР°РЅРёСЏС… СЃ СЂР°Р·РјРµС‰РµРЅРёРµРј РёС… РєРѕРїРёР№
Рё (РёР»Рё) РіРёРїРµСЂСЃСЃС‹Р»РєРё РЅР° СЃРѕРѕС‚РІРµС‚СЃС‚РІСѓСЋС‰РёРµ РґРѕРєСѓРјРµРЅС‚С‹
</th>
<th>
Рћ СЃР°РјРѕСЃС‚РѕСЏС‚РµР»СЊРЅРѕ СѓСЃС‚Р°РЅР°РІР»РёРІР°РµРјС‹С… С‚СЂРµР±РѕРІР°РЅРёСЏС…, СЃ СЂР°Р·РјРµС‰РµРЅРёРµРј РёС… РІ С„РѕСЂРјРµ СЌР»РµРєС‚СЂРѕРЅРЅРѕРіРѕ РґРѕРєСѓРјРµРЅС‚Р°, РїРѕРґРїРёСЃР°РЅРЅРѕРіРѕ СЌР»РµРєС‚СЂРѕРЅРЅРѕР№ РїРѕРґРїРёСЃСЊСЋ,
Рё (РёР»Рё)  РіРёРїРµСЂСЃСЃС‹Р»РєРё РЅР° СЃРѕРѕС‚РІРµС‚СЃС‚РІСѓСЋС‰РёР№ СЌР»РµРєС‚СЂРѕРЅРЅС‹Р№ РґРѕРєСѓРјРµРЅС‚, РїРѕРґРїРёСЃР°РЅРЅРѕРіРѕ СЌР»РµРєС‚СЂРѕРЅРЅРѕР№ РїРѕРґРїРёСЃСЊСЋ
</th>
</tr>
</thead>
<tbody>
htmlh;
//echo "<pre>";
//print_r($this->listitems);
//echo "</pre>";
	$idx=0;
	foreach($this->listitems as $opprec){
		$idx++;
		$html.="<tr>";
		$html.="<td>{$idx}</td>";
		$html.="<td>{$opprec["oppcode"]} {$opprec["oppname"]}<br>{$opprec["profile"]}</td>";
		$html.="<td>РІС‹СЃС€РµРµ РѕР±СЂР°Р·РѕРІР°РЅРёРµ - {$opprec["level"]}</td>";

		$id=$opprec["oppid"]."_1";
		$x="title=\"СЃСЃС‹Р»РєР°\" class=\"maindocselement sublink\" id=\"{$id}\" data-xmlid=\"{$opprec["eduFedDoc"][2]}\"  data-id=\"{$opprec["eduFedDoc"][1]}\" data-iblock=\"175\" data-opp=\"{$opprec["oppid"]}\" itemprop=\"eduFedDoc\"";
		$y=$opprec["eduFedDoc"][0];
		if($y==""){$y="РЅРµ РїСЂРµРґСѓСЃРјРѕС‚СЂРµРЅ";}
		$html.="<td><div {$x}>{$y}</div></td>";

		$id=$opprec["oppid"]."_2";
		$y=$opprec["eduStandartDoc"][0];
		if($y==""){$y="РЅРµ РїСЂРµРґСѓСЃРјРѕС‚СЂРµРЅ";}

		$x="title=\"СЃСЃС‹Р»РєР°\" class=\"maindocselement sublink\" id=\"{$id}\" data-xmlid=\"{$opprec["eduStandartDoc"][2]}\" data-id=\"{$opprec["eduStandartDoc"][1]}\" data-iblock=\"175\" data-opp=\"{$opprec["oppid"]}\" itemprop=\"eduStandartDoc\"";
		$html.="<td><div {$x}>{$y}</div></td>";

		$id=$opprec["oppid"]."_3";
		$y=$opprec["eduFedTreb"][0];
		if($y==""){$y="РЅРµ РїСЂРµРґСѓСЃРјРѕС‚СЂРµРЅ";}

		$x="title=\"СЃСЃС‹Р»РєР°\" class=\"maindocselement sublink\" id=\"{$id}\" data-xmlid=\"{$opprec["eduFedTreb"][2]}\" data-id=\"{$opprec["eduFedTreb"][1]}\" data-iblock=\"175\" data-opp=\"{$opprec["oppid"]}\" itemprop=\"eduFedTreb\"";
		$html.="<td ><div {$x}>{$y}</div></td>";

		$id=$opprec["oppid"]."_4";
		$y=$opprec["eduStandartTreb"][0];
		if($y==""){$y="РЅРµ РїСЂРµРґСѓСЃРјРѕС‚СЂРµРЅ";}

		$x="title=\"СЃСЃС‹Р»РєР°\" class=\"maindocselement sublink\" id=\"{$id}\" data-xmlid=\"{$opprec["eduStandartTreb"][2]}\" data-id=\"{$opprec["eduStandartTreb"][1]}\" data-iblock=\"175\" data-opp=\"{$opprec["oppid"]}\" itemprop=\"eduStandartTreb\"";
		$html.="<td><div {$x}>{$y}</div></td>";
		$html.="</tr>";
	}
		$html.="</tbody></table>";
	$memcache->set($cashGUID, $html, false, $this->cashTime);
	}
	$memcache->close();	
	if($this->isEditMode())	
		$html=str_replace(array("#hide#","#noitems#"),array("class=\"gray\"","class=\"hide\""),$html);
	else
		$html=str_replace(array("#hide#","#noitems#"),array("class=\"hide\"","class=\"gray\""),$html);
		
	if($buffer) return $html; else echo $html;
	}//showHtml
}//class
