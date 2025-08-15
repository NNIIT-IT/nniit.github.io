<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class maindocs extends  iasmuinfo{
	private $cashGUID;
	const cashTime=6000;
	private $emptyLink="Отсутствует";
	const emptyFile="/sveden/emptyFile.pdf";
	const arBlocks=array(7);
	private $hideCaption=false;
	private $mainPropList=array();//список групп для вывода
	private $propList=array();//список элементов для вывода
	private $propListSections=array();//список секций для поиска элементов
	private $propListNoSections=array();//список не секций для поиска элементов
	private $propListOpen=array();//список открытых элементов
	private $sectionsList=array();//список секций с элементами для вывода
	private $scoupeList=array();//список групп
	private $itemsTree=array(); //дерево элементов для вывода 
	private $logicFilter="or"; //дерево элементов для вывода 
	private $siteUID;
	private $podrazd;
	private $god;
	private $mainsections=array();
	private $expandPropetyItem="";//развернуть элементы с этим тегом;
	private $onlyValue;
	public function setMainPropList($list){$this->mainPropList=$list;}
	public function setPropList($list){$this->propList=$list;}
	public function setSectionsList($list){$this->sectionsList=$list;}
	public function setScoupeList($list){
		if(is_array($list)) $this->scoupeList=$list;
	}
	
	
	
private function generateItems(){
$sql=<<<SQL
SELECT DISTINCT el.ACTIVE,
	case 
	when el.id is null then 0 else el.id end as id,
	el.DETAIL_TEXT as itemText,
	case when be.#HIDE# is null then 0 else 1 end as itemHide,
	be.#DATEDOC# as datadoc,
	be.#VERSION# as versiondoc,
	be.#LANG# as lang,
	be.#UNIT# as unit,
	be.#HREF# as href,
	mt.UF_FULL_DESCRIPTION as groupNameL0,
	mt.UF_ORG_UNIT as groupNameL1,
	mt.UF_ITEMPROP as itemProp, 
	mt.UF_ITEMSCOPE as itemScope,
	mt.UF_TYPE as itemType,
	mt.UF_SORT2 as sort,
	mt.UF_MAIN_ITEM_PROP as mainItemProp,
	if (mt.UF_ADD_DATE=1 and (be.#NOSHOWDATE#!=263 or be.#NOSHOWDATE# is NULL),1,0) as addDate,
	f.SUBDIR as fdir,
	f.FILE_NAME as fname,f.ID as fileID,
	fsig.SUBDIR as fsigdir,
	fsig.FILE_NAME as fsigname,fsig.ID as filesigID,
	mt.UF_SCOUP_NAME,
	mt.UF_SORT1,
	mt.UF_XML_ID as xmlId,
	group_concat(bs.name separator ",") as sectionName,
	group_concat(bs.id separator ",") as sectionId,
	bs.name as sectionName,
	bs.id as sectionId,
	case 
	when mt.UF_USE_NAME=1 then el.name 
	when mt.UF_USE_NAME=0 and mt.UF_ADDNAME!="" then mt.UF_ADDNAME 
	else mt.UF_NAME 
	end as name,
	case 
	when mt.UF_GROUP and not mt.UF_USE_NAME and mt.UF_ADDNAME!='' then mt.UF_ADDNAME 
	when mt.UF_GROUP and not mt.UF_USE_NAME and mt.UF_ADDNAME='' then mt.UF_NAME 
	else "NaN"
	end as itempropgroupname,
		concat(
(if (mt.UF_SORT is null,0, mt.UF_SORT)),
(if(el.SORT is null,0,el.SORT)),
(if(be.#DATEDOC# is null,7001,DATE_FORMAT(be.#DATEDOC#,'%y%m'))), '-',el.id)  as globalsort,
	1 as sigreg	
	FROM `b_hlbd_mikrotegi` mt 
	LEFT JOIN `b_iblock_element_prop_s#iblock0#` be on be.#TYPEDOC#=mt.UF_XML_ID
	LEFT JOIN `b_iblock_element` el on el.ID=be.IBLOCK_ELEMENT_ID
	LEFT JOIN `b_iblock_section_element` se on se.IBLOCK_ELEMENT_ID=el.id
	LEFT JOIN `b_iblock_section` bs on bs.id=se.IBLOCK_SECTION_ID

	
	LEFT JOIN `b_file` f on be.#FILEDOC#=f.ID
	LEFT JOIN `b_file` fsig on be.#SIG#=fsig.ID
	
	WHERE ((el.ACTIVE="Y")  or (el.id is not NULL))
	and (mt.UF_DISABLED is NULL OR mt.UF_DISABLED=0)


	
SQL;
//	WHERE((( ((now()>=el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and (el.ACTIVE="Y"))  or el.id is NULL) 
//
//+el.SORT*10000+mt.UF_SORT1+mt.UF_SORT2/100000
// mt.UF_XML_ID
	$swhere="true ";
		//ограничение по секциям
		$sectOR=array();
		
		if(count($this->propListSections)>1){
			$sectOR[]=" bs.id in(".implode(",",$this->propListSections).") ";
		}elseif(count($this->propListSections)==1){
			$sectOR[]=" bs.id = ".intval($this->propListSections[0])." ";
		}
		if(count($this->sectionsList)>1){
			$sectOR[]=" bs.id in(".implode(",",$this->sectionsList).") ";
		}elseif(count($this->sectionsList)==1){
			$sectOR[]=" bs.id = ".intval($this->sectionsList[0])." ";
		}
		if(!is_array($this->scoupeList)) $this->scoupeList=array();
		if(count($this->scoupeList)>1){
			$sectOR[]=" mt.UF_ITEMSCOPE in('".implode("','",$this->scoupeList)."') ";
		}elseif(count($this->scoupeList)==1){
			$sectOR[]=" mt.UF_ITEMSCOPE ='".$this->scoupeList[0]."' ";
		}
		if(count($this->propListNoSections)>1){
			$sectOR[]=" not(bs.id in(".implode(",",$this->propListNoSections).") )";
		}elseif(count($this->propListNoSections)==1){
			$sectOR[]=" not(bs.id =".intval($this->propListNoSections[0]).") ";
		}
		if(count($sectOR)==1){
			$swhere.=" and (".$sectOR[0].") ";
		}
		if(count($sectOR)>1){
			$swhere.=" and (".implode(" or ",$sectOR).") ";
		}	
		// ограничение по элементам
		
		if(count($this->propList)>0 && count($this->mainPropList)>0) {
			if(count($this->propList)>1){
				$swhere.=" and ( mt.UF_ITEMPROP in('".implode("','",$this->propList)."')) ";
			}elseif(count($this->propList)==1){
				$swhere.=" and  (mt.UF_ITEMPROP ='".$this->propList[0]."') ";
			}
			if(count($this->mainPropList)>1){
				$swhere.=" or  mt.UF_MAIN_ITEM_PROP in('".implode("','",$this->mainPropList)."') ";
			}elseif(count($this->mainPropList)==1){
				$swhere.=" or  mt.UF_MAIN_ITEM_PROP='".$this->mainPropList[0]."' ";
			}

		}elseif(count($this->propList)>0){

			if(count($this->propList)>1){
				$swhere.=" and ( mt.UF_ITEMPROP in('".implode("','",$this->propList)."')) ";
			}elseif(count($this->propList)==1){
				$swhere.=" and ( mt.UF_ITEMPROP ='".$this->propList[0]."') ";
			}
		}elseif(count($this->mainPropList)>0) {
			if(count($this->mainPropList)>1){
				$swhere.=" and  (mt.UF_MAIN_ITEM_PROP in('".implode("','",$this->mainPropList)."')) ";
			}elseif(count($this->mainPropList)==1){
				$swhere.=" and  mt.UF_MAIN_ITEM_PROP='".$this->mainPropList[0]."' ";
			}
		}
		
		
		
		if($this->god>0) {
			$swhere.=" and be.#DATEDOC# like \"".$this->god."%\"";
		}
		
	
		if($swhere!="") $sql.="AND {$swhere}";
/*
		if($this->siteUID==262) 
			$sql.=" and be.#SITE#=262";
		else 
			$sql.=" and (be.#SITE#=261 or be.#SITE# is NULL)";

		$JOINADD="";
		if(intval($this->podrazd)>0) {
			$JOINADD=" left join `b_iblock_element_prop_m67` bm on bm.IBLOCK_ELEMENT_ID=be.IBLOCK_ELEMENT_ID ";
			$sql.=" and bm.value=".$this->podrazd;
		}
		$sql=str_replace("#JOINADD#",$JOINADD,$sql);
*/		



		$sql.=" group by el.id order by bs.id, mt.UF_SORT,  mt.UF_SORT1,mt.UF_SORT2,el.SORT ";
		$sql=$this->sqltoiblock($sql,$this::arBlocks);
	//echo "<div class='hide'>".$sql."</div>";
		 $rez=$this->BD->query($sql);
		$itemProp="";$nn=0;
$tmpItems=array();
while ($rec=$rez->fetch()){
	//echo "<div class='hide'>";
	//	print_r($rec);
	//echo "</div>";

	if($rec["ACTIVE"]=="Y"){
	$key=intval($rec["id"]);	
	if($key==0) $key=$rec["xmlId"];
		

	$tmpItems[$key]=$rec;
		$sectionsNames=explode(",",$rec["sectionName"].",");
		$sectionsIds=explode(",",$rec["sectionId"].",");
		$tmpItems[$rec["id"]]["sections"]=array_unique(array_combine($sectionsIds,$sectionsNames));
		$tmpItems[$rec["id"]]["sectionId"]=array_unique($sectionsIds);
		$tmpItems[$rec["id"]]["sectionName"]=array_unique($sectionsNames);

        if($tmpItems[$key]["itemProp"]=="priemLocalAct") {
	 	$tmpItems[$key]["itemProp"]="localAct";
		$this->propList[]="localAct";
	}
        if($tmpItems[$key]["itemProp"]=="paidEduDogDocLink") {
	 	$tmpItems[$key]["itemProp"]="paidEduDogDocLink";
		$this->propList[]="paidDog";
	}


	}
}

//echo "<!--";print_r($tmpItems);echo "-->";
//проверка обязательных документов
if(count($this->propList)>0){
	$arPropListData=array();
	$sql="select * from `b_hlbd_mikrotegi` where UF_ITEMPROP in (\"".implode("\",\"",$this->propList)."\")"; 
	$rez=$this->BD->query($sql);
	while ($rec=$rez->fetch()){
		$arPropListData[trim($rec["UF_ITEMPROP"])]=$rec;
	}
	foreach($this->propList as $itemprop){
		$isPrezent=false;
		//echo "<b>\$itemprop=".$itemprop."</b><br>"; 

		foreach ($tmpItems as $key=>$rec) {
			if(($rec["itemProp"]==$itemprop && $rec["id"]>0) || ($key==$itemprop)){
				$isPrezent=true;
			//	echo "<span style=\"color:red;\">\$rec[\"itemProp\"]=".$rec["itemProp"].",  \$rec[\"id\"]=".$rec["id"]." key=".$key."</span><br>";
			} else {
			//	echo "\$rec[\"itemProp\"]=".$rec["itemProp"].",  \$rec[\"id\"]=".$rec["id"]." key=".$key."<br>";
			}
		}
		//$arPropListData[$itemprop][],
		if(!$isPrezent && $itemprop!="localAct" && $itemprop!="priemLocalAct"){
			$tmpItems[$itemprop]=array(
				"id"=>0,
				"itemText"=>$this->emptyLink,
				"itemHide"=>0,
				"datadoc"=>"",
				"versiondoc"=>"0",
				"siteID"=>"S1",
				"unit"=>"",
				"groupNameL0"=>$arPropListData[$itemprop]["UF_FULL_DESCRIPTION"],
				"groupNameL1"=>(($_SESSION["SESS_LANG_UI"]=="en")?$arPropListData[$itemprop]["UF_ORG_UNIT_EN"]:$arPropListData[$itemprop]["UF_ORG_UNIT"]),
				"itemProp"=>$arPropListData[$itemprop]["UF_ITEMPROP"],
				"itemScope"=>$arPropListData[$itemprop]["UF_ITEMSCOPE"],
				"itemType"=>$arPropListData[$itemprop]["UF_TYPE"],
				"sort"=>$arPropListData[$itemprop]["UF_SORT2"],
				"mainItemProp"=>$arPropListData[$itemprop]["UF_MAIN_ITEM_PROP"],
				"addDate"=>0,
				"fdir"=>"",
				"fname"=>"",
				"fileID"=>0,
				"fsigdir"=>"",
				"fsigname"=>"",
				"filesigID"=>0,
				"filesigID"=>0,
				"UF_SCOUP_NAME"=>$arPropListData[$itemprop]["UF_SCOUP_NAME"],
				"UF_SORT1"=>$arPropListData[$itemprop]["UF_SORT1"],
				"xmlId"=>$arPropListData[$itemprop]["UF_XML_ID"],
				"sectionName"=>"",
				"sectionId"=>((isset($this->mainsections[0]))?$this->mainsections[0]:0),
				"name"=>(($_SESSION["SESS_LANG_UI"]=="en")?$arPropListData[$itemprop]["UF_NAME_EN"]:$arPropListData[$itemprop]["UF_NAME"]),

				"itempropgroupname"=>"NaN",
				"globalsort"=>"900000000-0",
				"sigreg"=>0,
				"href"=>"#",
			
			);
		}elseif($tmpItems[$itemprop]["name"]=="" && $itemprop!="localAct" && $itemprop!="priemLocalAct"){
/*
			$tmpItems[$itemprop]["groupNameL0"]=$arPropListData[$itemprop]["UF_FULL_DESCRIPTION"];
			$tmpItems[$itemprop]["groupNameL1"]=$arPropListData[$itemprop]["UF_ORG_UNIT"];
			$tmpItems[$itemprop]["itemProp"]=$arPropListData[$itemprop]["UF_ITEMPROP"];
			$tmpItems[$itemprop]["itemScope"]=$arPropListData[$itemprop]["UF_ITEMSCOPE"];
			$tmpItems[$itemprop]["itemType"]=$arPropListData[$itemprop]["UF_TYPE"];
			$tmpItems[$itemprop]["sort"]=$arPropListData[$itemprop]["UF_SORT2"];
			$tmpItems[$itemprop]["mainItemProp"]=$arPropListData[$itemprop]["UF_MAIN_ITEM_PROP"];
			$tmpItems[$itemprop]["UF_SCOUP_NAME"]=$arPropListData[$itemprop]["UF_SCOUP_NAME"];
			$tmpItems[$itemprop]["UF_SORT1"]=$arPropListData[$itemprop]["UF_SORT1"];
			$tmpItems[$itemprop]["xmlId"]=$arPropListData[$itemprop]["UF_XML_ID"];
			$tmpItems[$itemprop]["name"]=$arPropListData[$itemprop]["UF_NAME"];
			$tmpItems[$itemprop]["itempropgroupname"]="NaN";
			$tmpItems[$itemprop]["globalsort"]="900000000-0";
			$tmpItems[$itemprop]["sigreg"]=0;	
*/			
			

		}
	}
	

}
//echo "<pre>";
//print_r($arPropListData);
//echo "<hr>";
//print_r($tmpItems);
//echo "</pre>";

$nn=0;
		foreach ($tmpItems as $rec){
			$nn++;
			$item=$rec;
			$item["uid"]=$nn;



			if ($rec["fname"]!=""){
				if ($rec["fdir"]!="") $item["file"]="/upload/{$rec["fdir"]}/{$rec["fname"]}"; else $item["file"]="/upload/{$rec["fname"]}";
				$item["fileId"]=$rec["fileID"];
				$item["sigreg"]=$rec["sigreg"];

				$item["fname"]=$rec["fname"];
			}
			if ($rec["fsigname"]!=""){
				if ($rec["fsigdir"]!="") $item["sigfile"]="/upload/{$rec["fsigdir"]}/{$rec["fsigname"]}"; else $item["sigfile"]="/upload/{$rec["fsigname"]}";
				$item["fsigname"]=$rec["fsigname"];
			}	

				$item["itemProp"]=$rec["itemProp"];

			if ($rec["itempropgroupname"]!="NaN" && $rec["itemProp"]!="") {$itemPropGroup=" itemprop=\"{$rec["itemProp"]}\" ";} else{$itemPropGroup="";}
			$rec["itemScope"]=trim($rec["itemScope"]);
			$rec["mainItemProp"]=trim($rec["mainItemProp"]);
			$item["rec"]=$rec;

			//список разделов для которых можно отображать расположение документа
			$noNameSections=array(403,404,406,407,408,409,410,411,422);
			$sectScreenList=array();
			//echo "<!-- ";print_r($item["sections"]);echo "-->";

			$arLeves=array(
			411=>"Среднее профессиональное образование - подготовка специалистов среднего звена",
			404=>"Высшее образование - бакалавриат",
			410=>"Высшее образование - специалитет",
			407=>"Высшее образование - магистратура",
			403=>"Высшее образование - подготовка научных и научно-педагогических кадров в аспирантуре (адъюнктуре)",
			408=>"Высшее образование - ординатура",
			424=>"Дополнительное профессиональное образование",
			);




			foreach($item["sections"] as $sectionId=>$sectionName){
				if(in_array($sectionId,$noNameSections)){
					//отображаем только разделы не отмеченные как основные
					if(!in_array($sectionId,$this->mainsections)){
						if(isset($arLeves[intval($sectionId)]))
							$sectionName=$arLeves[intval($sectionId)];
						$sectScreenList[]=$sectionName;
					}
				}
			}

			if(count($sectScreenList)>0){
				$item["name"].=" (".implode(", ",$sectScreenList).") ";
			}

			
			if(strlen($rec["itemScope"])>2 && $rec["itemScope"]!="NaN" && $rec["itemScope"]!=NULL) {
				//$itemScope=" itemscope itemtype=\"{$rec["itemScope"]}\" ";
				$itemScope="";
			} else $itemScope="";
			if($rec["mainItemProp"]!="NaN" && $rec["mainItemProp"]!="" && $rec["mainItemProp"]!=NULL) {$mainItemProp=" itemprop=\"{$rec["mainItemProp"]}\" ";} else $mainItemProp="";
			
			if (in_array($rec["itemProp"],$this->propList)&&$this->hideCaption){//сортировка в указанном порядке списка, без группировок
				
				$groupLevel_0_Id="A".sprintf("%04d", array_search($rec["itemProp"],$this->propList));
				$groupLevel_s_Id="B";
				$groupLevel_1_Id="C";
				$groupLevel_2_Id="D";
				$globalsort=$groupLevel_0_Id."0".$nn;
			} else {
				$groupLevel_0_Id="A".trim($rec["groupNameL0"]);
				$groupLevel_s_Id="B".trim($rec["itemScope"]);
				$groupLevel_1_Id="C".(trim($mainItemProp).trim($rec["groupNameL1"]));
				$groupLevel_2_Id="D".$rec["UF_SORT1"].(trim($itemPropGroup).trim($rec["groupNameL2"]));
				$globalsort=$rec["globalsort"].$nn;
			}

			
			$item["globalsort"]=$globalsort;
			$this->itemsTree[$groupLevel_0_Id]["name"]=$rec["groupNameL0"];
			$this->itemsTree[$groupLevel_0_Id][$groupLevel_s_Id]["div"]=$itemScope;

			$this->itemsTree[$groupLevel_0_Id][$groupLevel_s_Id]["items"][$groupLevel_1_Id]["name"]=$rec["groupNameL1"];
			$this->itemsTree[$groupLevel_0_Id][$groupLevel_s_Id]["items"][$groupLevel_1_Id]["div"]=$mainItemProp;

			$this->itemsTree[$groupLevel_0_Id][$groupLevel_s_Id]["items"][$groupLevel_1_Id]["items"][$groupLevel_2_Id]["name"]=$rec["groupNameL2"];
			$this->itemsTree[$groupLevel_0_Id][$groupLevel_s_Id]["items"][$groupLevel_1_Id]["items"][$groupLevel_2_Id]["div"]=$itemPropGroup;

			$this->itemsTree[$groupLevel_0_Id][$groupLevel_s_Id]["items"][$groupLevel_1_Id]["items"][$groupLevel_2_Id]["items"][$globalsort]=$item;

		}
}
public function setparams($params){
	$podrazd=0;
	$this->mainPropList=array();//список групп для вывода
	$this->propList=array();//список элементов для вывода
	$this->propListSections=array();//список секций для поиска элементов
	$this->propListNoSections=array();//список секций для поиска элементов
	$this->propListOpen=array();//список открытых элементов
	$this->sectionsList=array();//список секций с элементами для вывода
	$this->scoupeList=array();//список групп
	$this->itemsTree=array(); //дерево элементов для вывода 
	$this->siteUID;
	$this->podrazd;
	$this->mainsections=array();
	$this->god=0;
	$this->expandPropetyItem="";
	$this->onlyValue=false;
	$this->emptyLink=($_SESSION["SESS_LANG_UI"]=="en")?"is missing":"отсутствует";
	if(is_array($params)){
		if (is_set($params["onlyValue"])) $this->onlyValue=true;
		if (is_set($params["mainPropList"])) $this->mainPropList=$params["mainPropList"];
		if (is_set($params["expandPropetyItem"])) $this->expandPropetyItem=$params["expandPropetyItem"];
	
		if (is_set($params["sectionsList"])) $this->sectionsList=$params["sectionsList"];
		if (is_set($params["propList"])) $this->propList=$params["propList"];
		if (is_set($params["propListOpen"])) $this->propListOpen=$params["propListOpen"];
		if (is_set($params["scoupeList"])) $this->scoupeList=$params["scoupeList"];
		if (is_set($params["god"])) $this->god=intval($params["god"]);
		if (is_set($params["logicFilter"])) {
			if(in_array($params["logicFilter"],array("or","and"))) $this->logicFilter=$params["logicFilter"];
		}
		if (is_set($params["siteID"])) {
			if($params["siteID"]=="ru") $this->siteUID=261;
			if($params["siteID"]=="en") $this->siteUID=262;
		} else $this->siteUID=261;
		$this->scoupeList=$params["scoupeList"];
	
		if (is_array($params["mainsections"])) $this->mainsections=$params["mainsections"];
		if (isset($params["hideCaption"])) $this->hideCaption=true;
		if(isset($params["podrazd"])) $this->podrazd=$params["podrazd"];
		if(isset($params["propListSections"])) {
			$this->propListSections=array();
			foreach($params["propListSections"] as $propListSection) $this->propListSections[]=$propListSection;
		}
		if(isset($params["propListNoSections"])) {
			$this->propListNoSections=array();
			foreach($params["propListNoSections"] as $propListNoSection) $this->propListNoSections[]=$propListNoSection;
		}
	}
	$this->cashGUID=md5(serialize($params));
}

private function printElement($element,$mainItemPropDiv){

//	$item=array("name"=>$rec["itemName"],"fname"=>"","id"=>$rec["id"],"itemType"=>$rec["itemType"],"itemText"=>$rec["itemText"],"itemHide"=>$rec["itemHide"]);

			if(isset($arLeves[$sectionId])) $sectionName=$arLeves[$sectionId];

			$result="";
			$sprops=" data-prop=\"".implode(",",$this->propList)."\" ";
			$adClass="maindocselement link ";
			$adStyle="";
			$defaultSection="";
			if(count($this->mainsections)>0){
				$defaultSection=$this->mainsections[0];
			}
			if ($element["name"]!=""){
			$elementname=htmlspecialcharsEx($element["name"]);
			if ($element["addDate"]) $elementname.=" от ".date("d.m.Y",strtotime($element["datadoc"]));
			$rez="";
			$br="";
			$idlink="maindocs_".$element["uid"]."_".$element["id"].rand(1,65000);
			$itemHide=$element["itemHide"] || ($element["id"]==0);
			if ($element["itemProp"]!="" && !$mainItemPropDiv) $itemprop=" itemprop=\"{$element["itemProp"]}\"";else  $itemprop="";
			if ($element["itemProp"]!="" && !$mainItemPropDiv) $xmlID=$element["itemProp"]; else  $xmlID="";
			if(!is_array($this->mainsections)) $this->mainsections=array();
			if(!is_string($element["sectionName"]))$element["sectionName"]="";

			if($element["sectionName"]!="")
			if (isset($elementname) && !in_array($sectionId,$this->mainsections) && count($this->mainsections)>0 && mb_strpos($elementname,$element["sectionName"])===false ){
				 $elementname.=" ({$element["sectionName"]})";
			}

			$open=in_array($element["itemprop"],$this->propListOpen)||in_array($element["xmlId"],$this->propListOpen);
			switch ($element["itemType"]){
			case 4://файл
				$rez="";
				if ($element["file"]!=""){
					$ext=pathinfo($element["file"], PATHINFO_EXTENSION);
					$ver=str_replace(array('.'," "),array('',''),microtime());
					$fileSRC=$element["file"];
				} else {$fileSRC=''; $br="<br>";}
				if ($element["sigfile"]!=""){
					$sigfile="<a href=\"{$element['sigfile']}\" title=\"Открепленная цифровая подпись\" class=\"blue link\"><sup>.sig</sup></a>";
				} else {$sigfile='';}
				$sigreg="";
				if($element["sigreg"]==1){$sigreg="<span class=\"epinfo\"></span>";}
				if($element["itemHide"]) $adClass.=" #addClassHide#";
				if($fileSRC!=""){
					$rez.="<a {$itemprop} title=\"{$elementname}\" target=\"blank\" download class=\"linkicon {$adClass}\" id=\"{$idlink}\"  data-section=\"{$defaultSection}\" href=\"{$fileSRC}\" data-fileid=\"{$element["fileId"]}\" data-id=\"{$element["id"]}\" data-iblock=\"7\" data-xmlId=\"{$element["xmlId"]}\" {$sprops}>{$elementname}</a>{$sigreg}{$sigfile}<br>\r\n";	
					}	
				else {
					$fileSRC=$this::emptyFile;
					$rez.="<a {$itemprop}  data-section=\"{$defaultSection}\" title=\"{$elementname}\" class=\"iconEmpty {$adClass}  \" id=\"{$idlink}\" data-id=\"{$element["id"]}\" href=\"{$fileSRC}\" data-iblock=\"7\" data-xmlId=\"{$element["xmlId"]}\" {$sprops} >{$elementname} - ".$this->emptyLink."</a><br>\r\n";	
				}
				$rez.="\r\n";
				break;
				/*case 9://ссылка на ресурс
				$hrefLink=$element["href"];
				$rez.="<a {$itemprop}  data-section=\"{$defaultSection}\" title=\"{$elementname}\" class=\"iconEmpty {$adClass}  \" id=\"{$idlink}\" data-id=\"{$element["id"]}\" href=\"{$hrefLink}\" data-iblock=\"67\" data-xmlId=\"{$element["xmlId"]}\" {$sprops} >{$elementname}</a><br>\r\n";	
break;*/
			case 17://php object


				$adStyle=" display:none;";
				if ($open){$adStyle=" display:block;";}
				
				$clName=trim($xmlID);
				//$clName="";
				$rez="";
				if($clName!=""){
					if(file_exists($_SERVER["DOCUMENT_ROOT"]."/sveden/class/cl-".$clName."/cl-".$clName.".php")){
						$z=new asmuinfo();
						$z->setСlassList(array(array("classname"=>$clName,)));
						$rez="<span title=\"{$elementname}\" class=\"texticon hidedivlink linkicon {$adClass}\" id=\"{$idlink}\" data-id=\"{$element["id"]}\" data-iblock=\"7\" data-xmlId=\"{$element["xmlId"]}\">{$elementname}</span>\r\n";
						$rez.="<div style=\"{$adStyle} padding:1em;\" class=\"\" {$itemprop}>";
						
						$rez.=$z->getHtml(true,false);
						$rez.="</div><br>\r\n";

					}
				}


				if($rez==""){
					if($element["itemText"]!=""){
						$rez.="<span title=\"{$elementname}\" class=\"texticon hidedivlink linkicon {$adClass}\" id=\"{$idlink}\" data-id=\"{$element["id"]}\" data-iblock=\"7\" data-xmlId=\"{$element["xmlId"]}\" {$sprops}>{$elementname}</span>\r\n";
						$rez.="<div style=\"{$adStyle} padding:1em;\" class=\"\" {$itemprop}>{$element["itemText"]}</div><br>\r\n";
					}else{
						$rez.="<span {$itemprop} title=\"{$elementname}\" class=\"linkicontxt {$adClass} \" id=\"{$idlink}\" data-id=\"{$element["id"]}\" data-iblock=\"7\" data-xmlId=\"{$element["xmlId"]}\" {$sprops}>{$elementname} - ".$this::emptyLink."</span>{$br}<br>\r\n";
					}
				}
				break;

			default://текст
				$itemText=($element["itemText"]!="")?$element["itemText"]:$this::emptyLink;

				$adStyle=" display:block;";
				if ($open){
					$adStyle=" display:block;";
				}
				if($element["itemHide"]) $adClass.=" #addClassHide#";
				if(!$this->onlyValue)
				{
					if($element["itemText"]!=""){
						$rez.="<span title=\"{$elementname}\" class=\"linkicontxt hidedivlink linkicon {$adClass}\" id=\"{$idlink}\" data-id=\"{$element["id"]}\" data-iblock=\"7\"  data-section=\"{$defaultSection}\" data-xmlId=\"{$element["xmlId"]}\" {$sprops}>{$elementname}</span>\r\n";
						$rez.="<div style=\"{$adStyle} padding:1em;\" class=\"\" {$itemprop}>{$element["itemText"]}</div><br>\r\n";
					}else{
						$rez.="<span title=\"{$elementname}\" class=\"linkicontxt hidedivlink linkicon {$adClass}\" id=\"{$idlink}\" data-id=\"{$element["id"]}\" data-iblock=\"7\"  data-section=\"{$defaultSection}\" data-xmlId=\"{$element["xmlId"]}\" {$sprops}>{$elementname}</span>\r\n";
						$rez.="<div style=\"{$adStyle} padding:1em;\" class=\"iconEmpty\" {$itemprop}>".$this::emptyLink."</div><br>\r\n";

					}
				}
				else
				{
					if($element["itemText"]!=""){
						$rez.="<div {$itemprop} title=\"{$elementname}\" class=\"maindocselement\" id=\"{$idlink}\" data-id=\"{$element["id"]}\" data-iblock=\"7\"  data-section=\"{$defaultSection}\" data-xmlId=\"{$element["xmlId"]}\" {$sprops}>";
						$rez.=$element["itemText"];
						$rez.="</div>\r\n";
					}else{
						$rez.="<div {$itemprop} title=\"{$elementname}\" class=\"maindocselement\" id=\"{$idlink}\" data-id=\"{$element["id"]}\" data-iblock=\"7\"  data-section=\"{$defaultSection}\" data-xmlId=\"{$element["xmlId"]}\" {$sprops}>";
						$rez.=$this::emptyLink;
						$rez.="</div>\r\n";
					}
				}

			}
				if($element["itemType"]==9) {
					$rez=str_replace($itemprop,"", $rez);
					$rez=str_replace("<a ","<a ".$itemprop, $rez);
				}
			return $rez;
			}
}	
public function showHtml($buffer=false,$nocache=false){
$html="";
$obCache = new CPHPCache();
$cacheLifetime = 86400*7; 
$cacheID = 'maindocs'.$this->cashGUID; 
$cachePath = '/'.$cacheID;

if( $obCache->InitCache($cacheLifetime, $cacheID, $cachePath) )
{$vars = $obCache->GetVars();if(is_array($vars) && isset($vars["html"]))$html=$vars["html"];}
if ($obCache->StartDataCache() || $html=="" || $html==false || $this->isEditmode() || $nocache){


	//if ($html=="" || $html==false || $this->isEditmode() || $nocache){
	$this->generateItems();
	$html="";
	if($this->hideCaption) ksort($this->itemsTree);
global $USER;

	$this->css=__DIR__."/style.css";
	if(!$this->onlyValue) $html="<div class=\"divlevels\">";else $html="";
	$showL1=false;

	foreach($this->itemsTree as $level_1_key=>$level_s_Prop){
		$level_s_name=trim($level_s_Prop["name"]);
		if($level_s_name!="" && !$this->hideCaption){
			if($this->podrazd>0)
				$html.="<h2> {$level_s_name}</h2>";
			else 
				$html.="<b> {$level_s_name}</b>";
		}

	 foreach($level_s_Prop as $level_1_key=>$level_1_Prop){

		$level_1_div="";
		if(isset($level_1_Prop["div"]) && $level_1_Prop["div"]!="NaN") $level_1_div=trim($level_1_Prop["div"]);


		if(!$this->onlyValue)
			  if ($level_1_div!=""){$html.="<div class=\"divlevel1 #addClassL1#\" {$level_1_div}>";}

		$showL2=false;
		if(is_array($level_1_Prop))
		if(is_array($level_1_Prop["items"]))

		foreach($level_1_Prop["items"] as $level_2_key=>$level_2_Prop){

			$level_2_name=trim($level_2_Prop["name"]);
			$level_2_div="";
			if($level_2_Prop["div"]!="NaN") $level_2_div=$level_2_Prop["div"];

			$level_2_div=$level_2_Prop["div"];
			$h3show=($level_2_name!="") && ($level_1_name!=$level_2_name)&& !$this->hideCaption;




			if($h3show){
				if($this->podrazd>0)
					$html.="<i class=\"#addClassL2#\"> {$level_2_name}</i>";
				else
					$html.="<h3 class=\"#addClassL2#\"> {$level_2_name}</h3>";
			}
			if(!$this->onlyValue) if ($level_2_div!=""  && $level_2_div!="NaN"){$html.="<div class=\"#addClassL2#\" {$level_2_div}>";}
			$showL3=false;
			$itms2=$level_2_Prop["items"];



			//		$html.="<!--"; 
			//		$html.=json_encode($itms2,JSON_UNESCAPED_UNICODE); 
			//		$html.=ksort($itms2); 
			//		$html.=json_encode($itms2,JSON_UNESCAPED_UNICODE); 
			//		$html.="-->";

			foreach($itms2 as $level_3_key=>$level_3_Prop){
				//$html.="<!-- ".$level_3_key." -->";
				$showL4=false;
				$level_3_name=$level_3_Prop["name"];$level_3_div="";
				if($level_3_Prop["div"]!="NaN") $level_3_div=$level_3_Prop["div"];


					if($level_3_name!=""  && count($level_3_Prop["items"])>1 && ($level_3_name!=$level_2_name) && !$this->hideCaption) {
						if($this->podrazd>0)
							$html.="<u class=\"#addClassL3#\"> {$level_3_name}</u>";
						else
							$html.="<h4 class=\"#addClassL3#\"> {$level_3_name}</h4>";
					}
					if(!$this->onlyValue)
						 if ($level_3_div!="" && $level_3_div!="NaN"){$html.="<div class=\"#addClassL3#\" {$level_3_div}>";}

					$itms3=$level_3_Prop["items"];

					foreach($itms3 as $srt=>$item){
						$parentItemProp=false;
						if(trim($item["itemProp"])!="" && trim($level_3_div)!="") $parentItemProp=mb_strpos($level_3_div,$item["itemProp"])>0;

						$html.=$this->printElement($item,$parentItemProp);
						$showL4=$showL4 || !($item["itemHide"] || ($item["id"]==0));
					}
					if(!$this->onlyValue) if ($level_3_div!="" && $level_3_div!="NaN"){$html.="</div>";}

				if($showL4) $html=str_replace("#addClassL3#","",$html); else $html=str_replace("#addClassL3#","#addClassHide#",$html); 
				$showL3=$showL3 || $showL4;
			}

			if(!$this->onlyValue) 
					if ($level_2_div!=""  && $level_2_div!="NaN"){$html.="</div>";}


			if($showL3) $html=str_replace("#addClassL2#","",$html); else $html=str_replace("#addClassL2#","#addClassHide#",$html); 
			$showL2=$showL2 || $showL3;
		}
		if(!$this->onlyValue) 
			if ($level_1_div!=""  && $level_1_div!="NaN"){$html.="</div>";}
		if($showL2) $html=str_replace("#addClassL1#","",$html); else $html=str_replace("#addClassL1#","#addClassHide#",$html); 
	}}
		if(!$this->onlyValue) 
			$html.="</div>";
	$obCache->EndDataCache(array("html"=>$html));// Сохраняем переменные в кэш.
//	$obCache->EndDataCache(array("html"=>$html));// Сохраняем переменные в кэш.
	//$result = $memcache->replace( $this->cashGUID, $html);
		if( $result == false )
		{
			//$memcache->set($this->cashGUID, $html, false, $this->cashTime);
		} 
} 
	
	//$memcache->close();	

/* не кешируемая часть */
			
			$addClassHide="";
		if($this->isEditmode()) {$addClassHide=" gray ";}else{$addClassHide="  ";}
		$html=str_replace("#addClassHide#",$addClassHide,$html);

		$html.="<input type=\"hidden\" id=\"expandPropetyItem\" value=\"{$this->expandPropetyItem}\">";

		if($this->podrazd>0 || $buffer){
			 return $html;
		}else {
			echo  $html;
		}	
/*--------------------*/		
	}
	
}