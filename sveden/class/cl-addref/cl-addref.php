<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class addref extends  iasmuinfo{
	private $cashGUID;
	const cashTime=6000;
	private $propList=array();//СЃРїРёСЃРѕРє СЌР»РµРјРµРЅС‚РѕРІ РґР»СЏ РІС‹РІРѕРґР°
	private $scoupeList=array();//СЃРїРёСЃРѕРє РіСЂСѓРїРї
	private $mainPropList=array();//СЃРїРёСЃРѕРє РіСЂСѓРїРї РґР»СЏ РІС‹РІРѕРґР°
	private $itemsTree=array(); //РґРµСЂРµРІРѕ СЌР»РµРјРµРЅС‚РѕРІ РґР»СЏ РІС‹РІРѕРґР° 
	private $siteUID;
private function generateItems(){
$sql=<<<SQL
select 
mt.id as id,
mt.UF_FULL_DESCRIPTION as groupNameL0,
mt.UF_ORG_UNIT as groupNameL1,
mt.UF_ITEMPROP as itemProp, 
mt.UF_ITEMSCOPE as itemScope,
mt.UF_TYPE as itemType,
mt.UF_SORT2 as sort,
mt.UF_MAIN_ITEM_PROP as mainItemProp,
mt.UF_ADD_DATE  as addDate,
mt.UF_SCOUP_NAME,
mt.UF_SORT1,
mt.UF_XML_ID as xmlId,
mt.UF_LINK as href,
case 
when mt.UF_USE_NAME=0 and mt.UF_ADDNAME!="" then mt.UF_ADDNAME 
else mt.UF_NAME 
end as name,
case 
when mt.UF_GROUP and not mt.UF_USE_NAME and mt.UF_ADDNAME!="" then mt.UF_ADDNAME 
when mt.UF_GROUP and not mt.UF_USE_NAME and mt.UF_ADDNAME="" then mt.UF_NAME 
else "NaN"
end as itempropgroupname,
concat((if (mt.UF_SORT is null,0, mt.UF_SORT*100000000)+if(mt.UF_SORT1 is null,0,mt.UF_SORT1*10000) + if(mt.UF_SORT2 is null,0,mt.UF_SORT2)), mt.UF_XML_ID)  as globalsort
FROM `b_hlbd_mikrotegi` mt 
where (mt.UF_DISABLED is NULL OR mt.UF_DISABLED=0) and (mt.UF_LINK is not null OR mt.UF_LINK!="")
SQL;
		if(count($this->propList)>0) {
			$swhere.=(($swhere!="")?" or ":"")." mt.UF_ITEMPROP in('".implode("','",$this->propList)."') ";
		}
		if(count($this->mainPropList)>0) {
			$swhere.=(($swhere!="")?" or ":"")." mt.UF_MAIN_ITEM_PROP in('".implode("','",$this->mainPropList)."') ";
		}
		if(count($this->scoupeList)>0) {
			$swhere.=(($swhere!="")?" or ":"")." mt.UF_ITEMSCOPE in('".implode("','",$this->scoupeList)."') ";
		}

		if($swhere!="") $sql.=" and ({$swhere})";
		$sql.=" order by mt.UF_SORT,  mt.UF_SORT1,mt.UF_SORT2 ";
//echo "<!--";print_r($sql);echo "-->";
		$rez=$this->BD->query($sql);
		$itemProp="";$nn=0;
	while ($rec=$rez->fetch()){
		if(intval($rec["id"])!=0)  $this->itemsTree[$rec["xmlId"]]=$rec;
	}

}
public function setparams($params){
	if (is_set($params["mainPropList"])) $this->mainPropList=$params["mainPropList"];
	if (is_set($params["propList"])) $this->propList=$params["propList"];
	if (is_set($params["scoupeList"])) $this->scoupeList=$params["scoupeList"];
	$this->cashGUID=md5(serialize($params));

}
public function showHtml($buffer=false){
//Р±РµР· РєРµС€РёСЂРѕРІР°РЅРёСЏ РґРѕР»Р¶РЅРѕ СЂР°Р±РѕС‚Р°С‚СЊ Р±С‹СЃС‚СЂРѕ
	$this->generateItems();
	$this->css=__DIR__."/style.css";
	$html="";

	foreach($this->itemsTree as $itemXml=>$item){
		$href=$item["href"];
		$html.=$rez="<a target=\"blank\" class=\"linkicon link\" itemprop=\"addRef\" href=\"{$href}\" >{$item["name"]}</a><br>";
	}
	if($buffer) return $html; else echo $html;
}
}