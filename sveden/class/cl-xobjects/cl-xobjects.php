<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class xobjects extends  iasmuinfo{
	private $cashGUID;
	const cashTime=6000,
	nameTbls=array(
		"purposeLibr"=>"Сведения о библиотеке(ах)",
		"purposeSport"=>"Сведения об объектах спорта",
		"meals"=>"Сведения об условиях питания обучающихся",
		"health"=>"Сведения об условиях охраны здоровья обучающихся"),
	nameAdd=array(
		"purposeLibr"=>"библиотекe",
		"purposeSport"=>"объект спорта",
		"meals"=>"столовую / буфет",
		"health"=>"условие охраны здоровья обучающихся");
	private $objTypes;
	private $activeTables=array("purposeLibr","purposeSport");
	private $objType;
	private $ovz;
	private $capt;
	private $listitems=array();
	private $open=false;
	
private function generateItems(){

if(CModule::IncludeModule("iblock")){ 
				$IBLOCK_ID=15;
				$arFilter=array("IBLOCK_ID"=>$IBLOCK_ID,"ACTIVE"=>"Y");
	//if($this->ovz) $arFilter["!PROPERTY_INV_VALUE"]="257";
	//if(!$this->ovz) $arFilter["PROPERTY_INV_VALUE"]="255|256";
				$arSelect=array("NAME","PROPERTY_OBJADDRESS","PROPERTY_OBJSQ","PROPERTY_OBJCNT","PROPERTY_TYPEDOC","PROPERTY_INV","ID","IBLOCK_SECTION_ID","DETAIL_TEXT");
				$res=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,$arSelect);
				while($ar_fields = $res->GetNext())
					{

						$property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "ID"=>$ar_fields["PROPERTY_TYPEDOC_ENUM_ID"]));
						if($enum_fields = $property_enums->GetNext())
						{
						 $ar_fields["PROPERTY_TYPEDOC_XML_ID"]=$enum_fields["XML_ID"];
						}
						
						$this->itemsTree[$ar_fields["PROPERTY_TYPEDOC_XML_ID"]][$ar_fields["ID"]]["objName"]=$ar_fields["NAME"];
						$this->itemsTree[$ar_fields["PROPERTY_TYPEDOC_XML_ID"]][$ar_fields["ID"]]["objAddress"]=$ar_fields["PROPERTY_OBJADDRESS_VALUE"];
						$this->itemsTree[$ar_fields["PROPERTY_TYPEDOC_XML_ID"]][$ar_fields["ID"]]["objSq"]=$ar_fields["PROPERTY_OBJSQ_VALUE"];
						$this->itemsTree[$ar_fields["PROPERTY_TYPEDOC_XML_ID"]][$ar_fields["ID"]]["objCnt"]=$ar_fields["PROPERTY_OBJCNT_VALUE"];
						$this->itemsTree[$ar_fields["PROPERTY_TYPEDOC_XML_ID"]][$ar_fields["ID"]]["xmlId"]=$ar_fields["PROPERTY_TYPEDOC_XML_ID"];
						$this->itemsTree[$ar_fields["PROPERTY_TYPEDOC_XML_ID"]][$ar_fields["ID"]]["xmlInv"]=$ar_fields["PROPERTY_PROPERTY_INV	_ID"];
						$this->itemsTree[$ar_fields["PROPERTY_TYPEDOC_XML_ID"]][$ar_fields["ID"]]["objOvz"]=$ar_fields["DETAIL_TEXT"];

					}
						//echo "<pre>";print_r($this->itemsTree);echo "</pre>";
}else{echo "error";}






}
public function setparams($params){
	$this->ovz=false;
	$this->open=false;
	if (isset($params["objTypes"])) $this->objTypes=$params["objTypes"];
	if (isset($params["ovz"])) $this->ovz=$params["ovz"]==1;
	if (isset($params["tables"])) $this->activeTables=$params["tables"];
	if (isset($params["open"])) $this->open=true;
	$this->cashGUID=md5("xobjects".implode(' ',$this->activeTables));
}

public function showHtml($buffer=true){
	$this->css=__DIR__."/style.css";
$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html="";//trim($memcache->get($this->cashGUID));
if ($html=="" || $html==false || self::isEditmode()){
	$this->generateItems();
	if(!$this->open){
		$html="<span class=\"texticon hidedivlink link\">";
		foreach($this->activeTables as $itable){ 
			$html.=self::nameTbls[$itable].". ";
		}
		$html.="</span><div style=\"display:block;\"><br>";
	} else{
		$html="<h3>";
		foreach($this->activeTables as $itable){ 
			$html.=self::nameTbls[$itable].". ";
		}
		$html.="</h3><div><br>";
	}
	$html.="<table class=\"simpletable\"><thead><tr >";
	$html.="<th>Наименование объекта</th><th>Адрес места нахождения объекта</th>";
	$html.="<th>Площадь объекта</th><th>Количество мест</th>";
	$html.="<th>Приспособленность для использования инвалидами и лицами с ограниченными возможностями здоровья</th>";
	$colspan=5;

	$html.="</tr></thead><tbody>";

	foreach ($this->activeTables as $xmlId){
		$items=$this->itemsTree[$xmlId];
		$tblName=self::nameTbls[$xmlId];
		$html.="<tr><th colspan=\"$colspan\">$tblName</th></tr>";
		if (is_array($items) && count($items)>0){
			foreach ($items as $id=>$item){
				$idlink=($this->ovz?"ovz":"").$this->objType."_".$id;
				$title=htmlspecialcharsEx($item["objName"]);
				$html.="<tr title=\"{$title}\" class=\"maindocselement\" id=\"{$idlink}\" data-id=\"{$id}\" data-iblock=\"15\" itemprop=\"{$xmlId}\">";
				$html.="<td itemprop=\"objName\">{$title}</td>";
				$item["objAddress"]=($item["objAddress"]=="")?$this::emptyCell:$item["objAddress"];
				$html.="<td itemprop=\"objAddress\">{$item["objAddress"]}</td>";
				$item["objSq"]=($item["objSq"]=="")?"-":$item["objSq"];
				$html.="<td itemprop=\"objSq\">{$item["objSq"]}</td>";
				$mst=intval($item["objCnt"])>0?$item["objCnt"]:"-<span class=\"hide\">не предусмотрено</span>";
				$html.="<td itemprop=\"objCnt\">{$mst}</td>";
				if($item["objOvz"]=="")$item["objOvz"]="Не приспособлено";
				$html.="<td itemprop=\"objOvz\">{$item["objOvz"]}</td>";
				$html.="</tr>";
			}	
		}else{
			$idlink=($this->ovz?"ovz":"").$xmlId."_0";
			$xtitle=xobjects::nameAdd[$xmlId];
			$html.="<tr title='{$xtitle}' class=\"maindocselement \" id=\"{$idlink}\" data-id=\"0\" data-iblock=\"15\" data-xmlid=\"{$xmlId}\" itemprop=\"{$xmlId}\">";
			$html.="<td itemprop=\"objName\">отсутствует</td>";
			$html.="<td itemprop=\"objAddress\">отсутствует</td>";
			$html.="<td itemprop=\"objSq\">отсутствует</td>";
			$html.="<td itemprop=\"objCnt\">отсутствует</td>";
			$html.="<td itemprop=\"objOvz\">отсутствует</td>";
			$html.="</tr>";
		}
		
	}
	$html.="</tbody></table></div><br>";
	$memcache->set($this->cashGUID, $html, false, $this->cashTime);
}
$memcache->close();	
$addClassHide="";
if($this->isEditmode()) {$addClassHide=" gray ";}else{$addClassHide=" hide ";}
		$html=str_replace("#addClassHide#",$addClassHide,$html);
if($buffer) return $html; else echo $html;
}
}//end class xobjects  