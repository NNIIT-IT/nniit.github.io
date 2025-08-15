<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class purposeCab extends  iasmuinfo{
	const cashTime=6000;
	public $cashGUID;
	public $activeTable;
	public $listitems=array();
	const  arTables=array(
		"purposeCab"=>array(
			"itemprop"=>"purposeCab",
			"name"=>"Cведения о наличии оборудованных учебных кабинетов",
			"td"=>array('addressCab','nameCab','osnCab'),
			"th"=>array("Адрес места нахождения","Наименование оборудованного учебного кабинета","Приспособленность для использования инвалидами и
лицами с ограниченными возможностями здоровья")
		),
		"purposeCabOvz"=>array(
		  	"itemprop"=>'purposeCab',
		 	"name"=>'Cведения о наличии оборудованных учебных кабинетов, приспособленных для использования инвалидами и лицами с ограниченными возможностями здоровья',
			"td"=>array('addressCab','nameCab','osnCab','ovzCab'),
			"th"=>array("Адрес места нахождения","Наименование оборудованного учебного кабинета","Оснащенность оборудованного учебного кабинета","Приспособленность для использования инвалидами и
лицами с ограниченными возможностями здоровья")
		),
		"purposePrac"=>array(
	   		"itemprop"=>'purposePrac',
		 	"name"=>'Cведения о наличии объектов для проведения практических занятий',
			"td"=>array('addressPrac','namePrac','osnPrac'),
			"th"=>array("Адрес места нахождения","Наименование объекта для проведения практических занятий","Оснащенность объекта для проведения практических занятий")
		),
		"purposePracOvz"=>array(
			"itemprop"=>'purposePrac',
			"name"=>'Cведения о наличии объектов для проведения практических занятий, приспособленных для использования инвалидами и лицами с ограниченными возможностями здоровья',
			"td"=>array('addressPrac','namePrac','osnPrac','ovzPrac'),
			"th"=>array("Адрес места нахождения","Наименование объекта для проведения практических занятий","Оснащенность объекта для проведения практических занятий", "Приспособленность для использования инвалидами и
лицами с ограниченными возможностями здоровья")
		)
	);


	
function generateItems(){
$listitemsTmp=array();
	if(! CModule::IncludeModule("iblock")) exit(); 
	$IBLOCK_CAB=68;
	$IBLOCK_SOBSTV=66;
	$objListId=array();
	$objOvzId=array();
	$thislistitems=array();
	
	$arFilter=array("IBLOCK_ID"=>$IBLOCK_CAB,"ACTIVE"=>"Y");
	$arSelect=array(
			"NAME",
			"DETAIL_TEXT",
			"PREVIEW_TEXT",
			"PROPERTY_TYPEAUDITOR",
			"PROPERTY_OBJ",
			"PROPERTY_OVZ",
			"ID","IBLOCK_SECTION_ID"
	);
	$res=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,$arSelect);
	while($ar_fields = $res->GetNext())
	{
		$listitemsTmp[$ar_fields["ID"]]=array(
			"id"=>$ar_fields["ID"],
			"name"=>$ar_fields["NAME"],
			"osn"=>$ar_fields["PREVIEW_TEXT"],
			"ovz"=>$ar_fields["DETAIL_TEXT"],
			"id"=>$ar_fields["ID"],
			"AUD_ID"=>$ar_fields["PROPERTY_TYPEAUDITOR_ENUM_ID"],
			"OBJ_ID"=>$ar_fields["PROPERTY_OBJ_VALUE"],
			"OVZ_ID"=>$ar_fields["PROPERTY_OVZ_ENUM_ID"],
		);	
		$objListId[$ar_fields["PROPERTY_OBJ_VALUE"]]=array();
		$objOvzId[$ar_fields["PROPERTY_OVZ_ENUM_ID"]]=array();
		$objAudId[$ar_fields["PROPERTY_TYPEAUDITOR_ENUM_ID"]]=array();
	}
	//object data
	$arFilter=array("IBLOCK_ID"=>$IBLOCK_SOBSTV,"ACTIVE"=>"Y","ID"=>explode("|",array_keys($objListId)));
	$arSelect=array("ID","NAME","PROPERTY_ADDRESS",	"IBLOCK_ID"	);
	$res=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,$arSelect);
	while($ar_fields = $res->GetNext())
	{
		$objListId[$ar_fields["ID"]]=array(
			"id"=>$ar_fields["ID"],
			"name"=>$ar_fields["NAME"],
			"address"=>$ar_fields["PROPERTY_ADDRESS_VALUE"],
		);	
	}


	//type ovz 
	$filter=array();
	if(count($objOvzId)>1) $filter=array("PROPERTY_ID"=>515,"IBLOCK_ID"=>$IBLOCK_CAB,"ID"=>explode("|",array_keys($objOvzId))); 
	if(count($objOvzId)==1) $filter=array("PROPERTY_ID"=>515,"IBLOCK_ID"=>$IBLOCK_CAB,"ID"=>array_keys($objOvzId)[0]);
	

	$property_enums = CIBlockPropertyEnum::GetList(
		Array("SORT"=>"ASC"), 
		$filter
	);

	while($enum_fields = $property_enums->GetNext()){

		$objOvzId[$enum_fields["ID"]]=array("xml"=>$enum_fields["XML_ID"], "name"=>$enum_fields["VALUE"]);
	}


	//type auditor	
	if(count($objAudId)==1) $filter=array("IBLOCK_ID"=>$IBLOCK_CAB,"ID"=>array_keys($objAudId)[0]); 
	if(count($objAudId)>1) $filter=array("IBLOCK_ID"=>$IBLOCK_CAB,"ID"=>explode("|",array_keys($objAudId)));

	$property_enums = CIBlockPropertyEnum::GetList(
		Array("SORT"=>"ASC"), 
		$filter
	);
	
	while($enum_fields = $property_enums->GetNext()){
		$objAudId[$enum_fields["ID"]]["xml"]=$enum_fields["XML_ID"];
		$objAudId[$enum_fields["ID"]]["name"]=$enum_fields["VALUE"];
	}	


	foreach($listitemsTmp as $k=>$Citem){
		$xitem=array();
		$addkey=$objAudId[$Citem["AUD_ID"]]["xml"];
		$xitem["id"]=$Citem["id"];
		$xitem["name".$addkey]=$Citem["name"];
		$xitem["osn".$addkey]=$Citem["osn"];
		$xitem["ovz".$addkey]=$Citem["ovz"];
		$xitem["address".$addkey]=$objListId[$Citem["OBJ_ID"]]["address"];
		$xitem["addressName"]=$objListId[$Citem["OBJ_ID"]]["name"];
		$xitem["ovz".$addkey]="<b>".$objOvzId[$Citem["OVZ_ID"]]["name"].".</b><br><br>".$Citem["ovz"];



		$ovz=$objOvzId[$Citem["OVZ_ID"]]["xml"];		
		if($ovz=="Inv1" || $ovz=="Inv0") {
			$mainKey="purpose".$addkey;
			$thislistitems[$mainKey][$k]=$xitem;
		}
		if($ovz=="Inv1" || $ovz=="Inv2") {
			$mainKey="purpose".$addkey."Ovz";
			$thislistitems[$mainKey][$k]=$xitem;
		}
		if($ovz=="Inv0"){
			$mainKey="purpose".$addkey;
			$xitem["ovz".$addkey]=$ovzName;
			$thislistitems[$mainKey][$k]=$xitem;
		}	
	}

	$this->listitems=$thislistitems;
					//var_dump($thislistitems);
}
public function setparams($params){
	$this->activeTableID=1;
	$this->listitems=array();
	$arPropIDs=array_keys($this::arTables);
	if (isset($params["objTypes"]) && is_array($params["objTypes"])){
		$this->activeTable=array_intersect($arPropIDs,$params["objTypes"]);
	} 
	if(count($this->activeTable)==0) $this->activeTable=$arPropIDs;
	
	$this->cashGUID=md5("purpose".implode(",",$this->activeTable));
	
}

public function showHtml($buffer=true){
	$IBLOCKID=68;//74;
	
	$this->css=__DIR__."/style.css";
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html=trim($memcache->get($this->cashGUID));
	if ($html=="" || $html==false || $this->isEditmode()){
		$this->generateItems();$html="";
		
		foreach($this->activeTable as $tableID){
			$tableData=$this::arTables[$tableID];
			$tableRows=$this->listitems[$tableID];
			
			if(!is_array($tableRows) || count($tableRows)==0){
				//заголовок таблицы
					$html.="<span class=\"texticon hidedivlink link\" >".$tableData["name"]."</span>";
					$html.="<div style=\"display:none;\"><table class=\"simpletable\"><thead><tr>";
					foreach($tableData["th"] as $th){
						$html.="<th>{$th}</th>";
					} 
					$html.="</tr></thead><tbody>";
						$html.='<tr itemprop="'.$tableData["itemprop"].'" id="'.$tableID.'0"  data-id="0" data-iblock="'.$IBLOCKID.'" class="maindocselement" >';
						foreach($tableData["td"] as $td){
							 $html.="<td itemprop=\"{$td}\">".$this::emptyCell."</th>";
						}
						$html.='</tr>' ;
					$html.="</tbody></table></div><br>";
			}else{
			
				//заголовок таблицы
					$emptyTable=array();

					$html.="<span class=\"texticon hidedivlink link\" >".$tableData["name"]."</span>";
					$html.="<div style=\"display:none;\">";
					$html.="<table class=\"simpletable\"><thead><tr>";
					foreach($tableData["th"] as $th){
						$html.="<th>{$th}</th>";
					} 
					$html.="</tr></thead><tbody>";

					foreach($tableRows as $tableRow){
				
						$html.="<tr itemprop=\"{$tableData['itemprop']}\" id=\"{$tableID}{$tableRow['id']}\" ";
						$html.=" data-id=\"{$tableRow['id']}\" data-iblock=\"{$IBLOCKID}\" class=\"maindocselement\" title=\"{$tableRow['nameCab']}\">";
						foreach($tableData["td"] as $td){
							$elHtm=$tableRow[$td];
							if($td=="osnPrac" || $td=="osnCab"){
							$html.="<td ><span class=\"texticon hidedivlink link\">Показать</span><div style=\"display:none;\" itemprop=\"{$td}\">{$elHtm}</div></td>";
							}else{
							$html.="<td itemprop=\"{$td}\">{$elHtm}</td>";
							}
						}
						$html.='</tr>';
					}	
					$html.="</tbody></table></div><br>";
			}
		}
		$memcache->set($this->cashGUID, $html, false, $this->cashTime);
	}
	$memcache->close();	
	if($buffer) return $html; else echo $html;
}
}//end class purposeCab   