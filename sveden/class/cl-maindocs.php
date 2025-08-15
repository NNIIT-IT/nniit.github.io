<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/cl-iasmuinfo.php");
use asmuinfoclasses;
class maindocs extends  iasmuinfo{
	private $mainPropList=array();//СЃРїРёСЃРѕРє РіСЂСѓРїРї РґР»СЏ РІС‹РІРѕРґР°
	private $propList=array();//СЃРїРёСЃРѕРє СЌР»РµРјРµРЅС‚РѕРІ РґР»СЏ РІС‹РІРѕРґР°
	private $propListOpen=array();//СЃРїРёСЃРѕРє РѕС‚РєСЂС‹С‚С‹С… СЌР»РµРјРµРЅС‚РѕРІ
	private $sectionsList=array();//СЃРїРёСЃРѕРє СЃРµРєС†РёР№ СЃ СЌР»РµРјРµРЅС‚Р°РјРё РґР»СЏ РІС‹РІРѕРґР°
	private $itemsTree=array(); //РґРµСЂРµРІРѕ СЌР»РµРјРµРЅС‚РѕРІ РґР»СЏ РІС‹РІРѕРґР° 
								/*
								if itemScope!="" {div itemScope=itemScope}
									if mainItemProp!="" {head 1 mainItemPropName  div itemprop= mainItemProp}
										if itempropgroupname!="" {head 2 itempropgroupname div item-prop}
												if itempropgroupname=="" {div item-prop items /div} else {div item /div}
										if itempropgroupname!="" {/div}		
									if mainItemProp!="" {/div}
								if itemScope!="" {/div}
								*/
	function setMainPropList($list){$this->mainPropList=$list;}
	function setPropList($list){$this->propList=$list;}
	function setSectionsList($list){$this->sectionsList=$list;}
	
	
	
private function generateItems(){
$sql=<<<SQL
SELECT 
	el.id as id,
	el.DETAIL_TEXT as itemText,
	case when be.PROPERTY_533 is null then 0 else 1 end as itemHide,
	be.PROPERTY_242 as datadoc,
	be.PROPERTY_566 as versiondoc,
	be.PROPERTY_592 as siteID,
	be.PROPERTY_561 as groupL0,
	mt.UF_ORG_UNIT as mainItemPropName,
	mt.UF_ITEMPROP as itemProp, 
	mt.UF_ITEMSCOPE as itemScope,
	mt.UF_TYPE as itemType,
	
	mt.UF_MAIN_ITEM_PROP as mainItemProp,
	f.SUBDIR as fdir,
	f.FILE_NAME as fname,
	mt.UF_SCOUP_NAME,
	mt.UF_SORT1,
	bs.name as sectionName,
	case 
	when mt.UF_USE_NAME=1 then el.name 
	when mt.UF_USE_NAME=0 and mt.UF_ADDNAME!="" then mt.UF_ADDNAME 
	else mt.UF_NAME 
	end as itemName,
	case 
	when mt.UF_GROUP and not mt.UF_USE_NAME and mt.UF_ADDNAME!="" then mt.UF_ADDNAME 
	when mt.UF_GROUP and not mt.UF_USE_NAME and mt.UF_ADDNAME="" then mt.UF_NAME 
	else ""
	end as itempropgroupname 
	FROM `b_iblock_element_prop_s130` be
	LEFT JOIN `b_iblock_element` el on el.ID=be.IBLOCK_ELEMENT_ID
	LEFT JOIN `b_iblock_section` bs on bs.id=el.IBLOCK_SECTION_ID
	LEFT JOIN `b_hlbd_mikrotegi` mt on be.PROPERTY_241=mt.UF_XML_ID
	LEFT JOIN `b_file` f on be.PROPERTY_240=f.ID
	WHERE el.ACTIVE="Y" 
SQL;
	$swhere="";
		if(count($this->sectionsList)>0) {
			$swhere.=" bs.id in(".implode(",",$this->sectionsList).") ";
		}
		if(count($this->propList)>0) {
			$swhere.=(($swhere!="")?" or ":"")." mt.UF_ITEMPROP in('".implode("','",$this->propList)."') ";
		}
		if(count($this->mainPropList)>0) {
			$swhere.=(($swhere!="")?" or ":"")." mt.UF_MAIN_ITEM_PROP in('".implode("','",$this->mainPropList)."') ";
		}
		if($swhere!="") $sql.=" and ({$swhere})";
		 $rez=$this->BD->query($sql);
		$itemProp="";
		while ($rec=$rez->fetch()){
			$item=array("name"=>$rec["itemName"],"fname"=>"","id"=>$rec["id"],"itemType"=>$rec["itemType"],"itemText"=>$rec["itemText"],"itemHide"=>$rec["itemHide"]);
			if ($rec["fname"]!=""){
				if ($rec["SUBDIR"]!="") $item["file"]="/upload/{$rec["SUBDIR"]}/{$rec["fname"]}"; else $item["file"]="/upload/{$rec["fname"]}";
				$item["fname"]=$rec["fname"];
			}		
			
			if ($rec["itempropgroupname"]!="") {
				$itemPropGroup=" itemprop=\"{$rec["itemProp"]}\" ";
				$item["itemProp"]="";
			} else{
				$itemPropGroup="";
				$item["itemProp"]=$rec["itemProp"];
			}
			
			if ($rec["itemScope"]=="" && $rec["mainItemProp"]!="") $mainItemProp=" itemprop=\"{$rec["mainItemProp"]}\" ";
			elseif($rec["itemScope"]!="" && $rec["mainItemProp"]=="") $mainItemProp=" itemscope itemtype=\"{$rec["mainItemProp"]}\" ";
			elseif($rec["itemScope"]!="" && $rec["mainItemProp"]!="") $mainItemProp=" itemscope itemtype=\"{$rec["mainItemProp"]}\" itemprop=\"{$rec["mainItemProp"]}\" ";
			else $mainItemProp="";


			$this->itemsTree[$mainItemProp]["name"]=$rec["mainItemPropName"];
			
			$this->itemsTree[$mainItemProp]["items"][$itemPropGroup]["name"]=$rec["itempropgroupname"];
			$this->itemsTree[$mainItemProp]["items"][$itemPropGroup]["items"][$rec["id"]]=$item;

		}
}
public function setparams($params){
	if (is_set($params["mainPropList"])) $this->mainPropList=$params["mainPropList"];
	if (is_set($params["sectionsList"])) $this->sectionsList=$params["sectionsList"];
	if (is_set($params["propList"])) $this->propList=$params["propList"];
	if (is_set($params["propListOpen"])) $this->propListOpen=$params["propListOpen"];
	$this->generateItems();
}
private function printElement($element){
//	$item=array("name"=>$rec["itemName"],"fname"=>"","id"=>$rec["id"],"itemType"=>$rec["itemType"],"itemText"=>$rec["itemText"],"itemHide"=>$rec["itemHide"]);
			$result="";
			$aClass="maindocselement";
			$adStyle="";
			if ($element["name"]!=""){
			$rez="";
			$idlink="maindocs_".$element["id"];
			//echo "<!--";print_r($element);echo "-->";
			if ($element["itemprop"]!="") $itemprop=" itemprop=\"{$element["itemprop"]}\"";
			switch ($element["itemType"]){
			case 4://С„Р°Р№Р»
				if ($element["itemHide"]) {$adStyle="color:gray; ";$aClass.=" gray ";}
				if ($element["itemHide"] && !$this->isEditmode()){$aClass.=" hide"; $adStyle=" display:none;";}
			
				if ($element["file"]!=""){
					$ext=pathinfo($element["file"], PATHINFO_EXTENSION);
					$ver=str_replace(array('.'," "),array('',''),microtime());
					$fileSRC=$element["file"]."?ver={$ver}.{$ext}";
				} else $fileSRC='#'; 
				$rez="<a title=\"{$element["name"]}\" target=\"blank\" download class=\"linkicon {$aClass}\" id=\"{$idlink}\" {$itemprop} href=\"{$fileSRC}\" data-id=\"{$element["id"]}\">{$element["name"]}</a>\r\n";	
				break;
			default://С‚РµРєСЃС‚
				$open=in_array($element["itemprop"],$this->propListOpen);
				$adStyle=" display:none;";
				if ($element["itemHide"]) {$adStyle="color:gray; ";$aClass.=" gray ";}
				if ($element["itemHide"] && !$this->isEditmode()){$aClass.=" hide"; $adStyle=" display:none;";}
				if (!$element["itemHide"] && $open){$adStyle=" display:block;";}
				$rez.="<span class=\"hidedivlink linkicon {$adClass}\" id=\"{$idlink}\" data-id=\"{$element["id"]}>{$element["name"]}</a>\r\n";
				$rez.="<div style=\"{$adStyle} padding:1em;\" {$itemprop}>{$element["itemText"]}</div>\r\n";
			}
			return $rez;
			}
}	
public function showHtml(){
echo "<pre>";print_r($this->itemsTree);echo "</pre>";
	$html="";
	foreach($this->itemsTree as $itemScope=>$arMainItemProp){
		$nameScoupeName=$arMainItemProp["name"];
		if($nameScoupeName!="") {$html.="<h2> {$nameScoupeName}</h2>";}
		if ($itemScope!=""){$html.="<div {$itemScope}>";}
		foreach($arMainItemProp["items"] as $mainItemProp=>$itemProps){
			$mainItemPropDiv=$mainItemProp!="";
			if($itemProps["name"]!=""){$html.="<h3>{$itemProps["name"]}</h3>";}
			if($mainItemProp!=""){$html.="<div {$mainItemProp}>";}
				foreach($itemProps["items"] as $itemProp=>$item){
					if(!$mainItemPropDiv){$html.="<div itemprop=\"$itemProp\">";}
						$html.=$this->printElement($item);
					if(!$mainItemPropDiv){$html.="</div>";}
				}
				if($mainItemProp!=""){$html.="</div>";}
			}
			if ($itemScope!=""){$html.="</div>";}
		}
		return $html;
		
	}
	
}
$z=new maindocs();
$z->setparams(array("mainPropList"=>array("fillnfo","volume")));
$z->getHtml();
