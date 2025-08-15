<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
//$this->setFrameMode(true);
$countitems=0;
GLOBAL $USER;
$lang_up=$arParams['lang_up'];
	$userlogin=$USER->IsAuthorized();
	if (isset($_GET['bitrix_include_areas'])) $rr=$_GET['bitrix_include_areas']; else $rr='N';
	if (isset($_SESSION['SESS_INCLUDE_AREAS'])) $rr1=$_SESSION['SESS_INCLUDE_AREAS']; else $rr1='0';
	$editmode=$userlogin && (($rr=='Y')||($rr1=='1'));
	$folderID=intval($_GET["folder"]);
?>

<? //формируем дерево элементов
$Itree=array();
if (intval($arParams['SECTION_START_ID'])>0) 
	$defsect=intval($arParams['SECTION_START_ID']);
else 
	$defsect=$arParams['IBLOCK_ID'];
$sectID=$defsect;
$page=$arParams["PAGE"];
if(!function_exists("getpathsection")){
		function getpathsection($id,$defsect)
		{
		$sid=$id;
		$Spath=array();
		$sectName=' ';	
		if ($id==0) $id=$defsect;
			$ii=100;
		 while (($id!=$defsect)&&($ii>0)&&($id!=''))
			{
		
		 	 $res=CIBlockSection::GetByID($id);$ii--;
			 if($ar_res = $res->GetNext()) 
				{
					if (($id!='')&&($id!=$defsect)&&($id!=0))
					{
						$Spath[$id]=$ar_res['NAME'];
						if($lang_up=="EN")
							$Spath[$id]=$ar_res['UF_NAME_EN'];
						$id=$ar_res['IBLOCK_SECTION_ID'];
						$active=$ar_res['ACTIVE']=="Y";
						//print_r($Spath);echo '<br>';
						if (!$active)$id="";
					}
					
		
				}
			
			}
		if (($id=='')||($id==$defsect)||($id==0)) { $Spath[$defsect]=$sectName;}
		GLOBAL $USER;
		if ($USER->isadmin()){
		//echo '<hr>'.$sid.'<pre>';
		//print_r($Spath);
		//echo '</pre>';
		}
		return array_reverse($Spath,true);
		}
}

GLOBAL $USER;
$countImems0=false;

foreach($arResult["ITEMS"] as $arElement){

	$sres=CIBlockElement::GetElementGroups($arElement['ID'], false, array());
	if ($Xres=$sres->GetNext()){
		$Asect=$Xres['ID'];
		$AsectName=$Xres['NAME'];
		$active=$Xres['ACTIVE'];//$ar_res
	}
	$sectID=$Asect;	
	
	$nameitem=getpathsection($sectID,$defsect);
	$key='';
	foreach ($nameitem as $keyp=>$pathitem) $key.=$keyp.'/';
	$Itree[$key]['path']=$nameitem;
	$Itree[$key]['SECTION_ID']=$sectID;
	$file=CFILE::GetFileArray($arElement['PROPERTIES']['FILE_'.$lang_up]['VALUE']);
	$filesrc=$file['SRC'];
	$filesize=$file['FILE_SIZE'];
	$filesizen='байт';
	if ($filesize>1024) {$filesize=$filesize/1024;$filesizen='Kб';}
	if ($filesize>1024) {$filesize=$filesize/1024;$filesizen='Mб';}
	if ($filesize>1024) {$filesize=$filesize/1024;$filesizen='Gб';}
	$filesize=round($filesize,2);
	$item=array(
		'ID'=>$arElement['ID'],
		'NAME'=>$arElement['NAME_'.$lang_up],
		'SRC'=>$filesrc,
		'SIZE'=>$filesize.'&nbsp;'.$filesizen,
		'EDIT_LINK'=>$arElement['EDIT_LINK'],
		'sectionID'=>$sectID,
		'DELETE_LINK'=>$arElement['DELETE_LINK']
	);
	if ($arElement['NAME']!=""){
		$countitems++; $Itree[$key]['ITEMS'][]=$item;
		
	}
}
ksort($Itree);
$x0=array(); 
$sectName="";
$html="";
$html0='<div class="menu-sitemap-tree" style="margin-left:-18px;">';
$html1='<ul>';
$pathKeys=array_keys($Itree);
$adminscript="";
$x1=array();
if(count($Itree)==0){
	if ($editmode){
						
		$ereaId="doc_new_podr_doc";//$this->GetEditAreaId($docs['ID']);
		$elelement_id=0;
		$sectionID=0;
		$adminscript.='<script>';
		$adminscript.='if(window.BX&&BX.admin){BX.admin.setComponentBorder("'.$ereaId.'");}';
		$adminscript.="if(window.BX)BX.ready(function() {";
		$adminscript.="(new BX.CMenuOpener({";
		$adminscript.="'parent':'".$ereaId."', 'component_id':'page_edit_control', ";
		$adminscript.="'menu':[";

		$adminscript.="{'ICONCLASS':'bx-context-toolbar-create-icon','TITLE':'Редактировать данные ', 'TEXT':'Добавить запись', ";
		$adminscript.="'ONCLICK':'";
		$adminscript.="(new BX.CAdminDialog({\'content_url\':\'/bitrix/admin/iblock_element_edit.php?";
		$adminscript.="IBLOCK_ID=117&ID=&type=News&lang=ru&force_catalog=&filter_section=".$sectionID."&IBLOCK_SECTION_ID=".$sectionID."&bxpublic=Y&from_module=iblock&PODRAZD=".$idpodrazd;
		$adminscript.="&return_url=".$bbukurl."\',\'width\':\'1014\',\'height\':\'657\'})).Show()";
		$adminscript.="','DEFAULT':true}";	
		$adminscript.="]})).Show()})";
		$adminscript.="</script>";
		$html.='<div id="'.$ereaId.'" class="item-text" style="left:9px">';
		$html.='Добавить документ';
		$html.='</div>';						


	}
}
foreach ($Itree as $Xitem){
	$id=$Xitem['SECTION_ID'];
	$oldx1=$x1;
	$x1=array_keys($Xitem['path']);
	//закрываем уровни при необходимости
	$k=0;$k1=0;
	if ($x1[0]!='' && !empty($oldx1))	while ($oldx1[$k1]==$x1[$k1]){  $k1++;}// число одинаковых уровней
	$cnt=count($oldx1)-$k1-1;
	if (($cnt>0)&&(count($oldx1)!=0))
		for ($k=0;$k<$cnt;$k++) { 
			if (strlen($x1[$k])>1) $html.="</ul></li></ul></li>";
		} 
		$k=0;
		while ($x1[$k]!=''){
			if ($x1[$k]!=$x0[$k]){
				$x0[$k]=$x1[$k];
				
				$sulname=$Xitem['NAME'];
				if (($sulname=="")&&($Xitem["SECTION_ID"]==$defsect)){
					 $res=CIBlock::GetByID($Xitem["SECTION_ID"]);
					 if($ar_res = $res->Fetch()) {
						$sulname=$ar_res['NAME'];
					}
				}
				if ((trim($sulname)=="")&&(trim($Xitem['path'][$x0[$k]])=="")) $sulname=$sectName;
				if ((trim($sulname)==".")&&(trim($Xitem['path'][$x0[$k]])=="")) $sulname=$sectName;
				
				
				if (strlen($Xitem['path'][$x0[$k]])>1){
			
					$html.="<li id=\"{$Xitem['SECTION_ID']}\"><ul>";
					$_st=((!in_array($folderID,array_keys($Xitem['path'])))&&($countitems>7))?'menu-close':'';
							$html.='<li class="'.$_st.'">';
							$html.='<div class="folder" onclick="OpenMenuNode(this)"></div>';
							$html.='<div onclick="OpenMenuNode(this)" style="cursor: pointer;" class="item-text">';
							$html.=$Xitem['path'][$x0[$k]].$sulname;
							$html.='</div>';
								$html.='<ul>';
				} 
				
				if ($x1[$k+1]=='') 
					foreach ($Xitem['ITEMS'] as $docs){
						$bbukurl=str_replace("/","%2F",$arParams['BACKURL']);
						//echo "<!--BUCKURL=".$bbukurl."-->";
						if($docs['NAME']!==""){
							if ($editmode){
								//$this->AddEditAction($docs['ID'], $docs['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
								//$this->AddDeleteAction($docs['ID'], $docs['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
								$ereaId="doc_".$elelement_id;//$this->GetEditAreaId($docs['ID']);
								$elelement_id=$docs['ID'];
								$sectionID=$docs['sectionID'];
	
								$adminscript.='<script>';
								$adminscript.='if(window.BX&&BX.admin){BX.admin.setComponentBorder("'.$ereaId.'");}';
								$adminscript.="if(window.BX)BX.ready(function() {";
								$adminscript.="(new BX.CMenuOpener({";
								$adminscript.="'parent':'".$ereaId."', 'component_id':'page_edit_control', ";
								$adminscript.="'menu':[";
	
								$adminscript.="{'ICONCLASS':'bx-context-toolbar-edit-icon','TITLE':'Редактировать данные ', 'TEXT':'Изменить запись', ";
								$adminscript.="'ONCLICK':'";
								$adminscript.="(new BX.CAdminDialog({\'content_url\':\'/bitrix/admin/iblock_element_edit.php?";
								$adminscript.="IBLOCK_ID=117&ID=".$elelement_id."&type=News&lang=ru&force_catalog=&filter_section=".$sectionID."&IBLOCK_SECTION_ID=".$sectionID."&bxpublic=Y&from_module=iblock";
								$adminscript.="&return_url=".$bbukurl."\',\'width\':\'1014\',\'height\':\'657\'})).Show()";
								//$adminscript.="; $('.adm-detail-valign-top').Attr('width','20%');}";
								$adminscript.="','DEFAULT':true},";
	
	
	
	
								$adminscript.="{'ICONCLASS':'bx-context-toolbar-delete-icon','TITLE':'Удалить данные', 'TEXT':'Удалить запись', ";
								$adminscript.="'ONCLICK':";
								$adminscript.="'if(confirm(\'Будет удалена вся информация, связанная с этой записью. Продолжить?\')) ";
								$adminscript.=" jsUtils.Redirect([], \'/bitrix/admin/iblock_list_admin.php?";
								$adminscript.="IBLOCK_ID=117&type=Struktura&lang=ru&action=delete&ID=E".$elelement_id;
								$adminscript.="&sessid=".bitrix_sessid()."&return_url=".$bbukurl."\');'";
								$adminscript.="},";
	
								$adminscript.="{'ICONCLASS':'bx-context-toolbar-create-icon','TITLE':'Редактировать данные ', 'TEXT':'Добавить запись', ";
								$adminscript.="'ONCLICK':'";
								$adminscript.="(new BX.CAdminDialog({\'content_url\':\'/bitrix/admin/iblock_element_edit.php?";
								$adminscript.="IBLOCK_ID=117&ID=&type=News&lang=ru&force_catalog=&filter_section=".$sectionID."&IBLOCK_SECTION_ID=".$sectionID."&bxpublic=Y&from_module=iblock&PODRAZD=".$idpodrazd;
								$adminscript.="&return_url=".$bbukurl."\',\'width\':\'1014\',\'height\':\'657\'})).Show()";
								$adminscript.="','DEFAULT':true}";	
			
								$adminscript.="]})).Show()})";
								
								$adminscript.="</script>";
								//$adminscript.='<script type="text/javascript">if(window.BX&&BX.admin)BX.admin.setComponentBorder(\''.$ereaId.'\')</script>';
	
							}
							$html.='<li class="menu-close" style="list-style-type: none;">';
							$html.='<div class="page" style="background-image:none;"></div><div id="'.$ereaId.'" class="item-text" style="left:9px">';
							if ($docs['SRC']!="") {
								$src=str_replace(" ","%20",$docs['SRC']);
								$html.='<a class="linkicon" target="blank" href="'.$src.'">'.$docs['NAME'].'</a><small>('.$docs['SIZE'].')</small>';
							} else {
								$html.=$docs['NAME'];
							}
							$html.='</div></li>';
						}
					}
				}
			$k++;
		}
}
$html2='</ul>';
$html3='</div>';
if(count($Itree)>0){
	echo $html0.$html1.$html.$html2.$html3;
} else{
	if ($editmode) echo $html;
}
if ($editmode) echo "<!--ADMINMODE -->\n".$adminscript;?>
