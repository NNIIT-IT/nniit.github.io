<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
if(!isset($_SESSION["SESS_LANG_UI"])) $_SESSION["SESS_LANG_UI"]="ru";
define(LANGUAGE_ID, $_SESSION["SESS_LANG_UI"]);
$_SESSION["LANG_UI"]=$_SESSION["SESS_LANG_UI"];
$realPageDir=$_SERVER["REAL_FILE_PATH"];
if($realPageDir=="") 
	$realPageDir=$APPLICATION->GetCurDir();
else 
	$realPageDir=dirname($realPageDir);
$err404=false;
if (defined("ERROR_404"))
	$err404=ERROR_404=="Y";
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$_SESSION["SESS_LANG_UI"]?>" lang="<?=$_SESSION["SESS_LANG_UI"]?>">
<html lang="ru" itemscope itemtype="http://schema.org/WebPage">
<head id="pagehead">
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" >
<meta name="viewport" content="initial-scale=1.0, width=device-width">
<?IncludeTemplateLangFile(__FILE__,$_SESSION["SESS_LANG_UI"]);?>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<!--link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="icon" type="image/png" href="/favicon.png"-->
<?
header('Content-Type: text/html; charset=utf-8');
header ("X-XSS-Protection: 1; mode=block");
header ("Vary: *");
header ("Cache-Control: no-store");
header ('X-Permitted-Cross-Domain-Policies:master-only;');
$APPLICATION->ShowMeta("keywords");
$APPLICATION->ShowMeta("description");
$APPLICATION->ShowCSS();
$APPLICATION->ShowHeadStrings();
//$APPLICATION->Showtitle();
$invalid=false;
if (isset($_SESSION['invalid'])){
	$invalid=intval($_SESSION['invalid'])==1;
}elseif(isset($_COOKIE['Invalid'])){
	$invalid=intval($_COOKIE['Invalid'])==1;
}
$_SESSION['invalid']=$invalid;
$add_body_class="";
$add_panel_class="hide";
if ($invalid){$add_body_class="bw_ver";$add_panel_class="show";}
/*загрузка языковых файлов страницы*/
$propName="pageName_".$_SESSION['SESS_LANG_UI'];
$dirPath=$APPLICATION->GetCurDir();//$_SERVER["DOCUMENT_ROOT"]
$pageTitle=$APPLICATION->GetDirProperty("caption_".$_SESSION['SESS_LANG_UI'],$dirPath,$pageTitle);

$APPLICATION->SetTitle($pageTitle);
$_SESSION["TITLE"]=$pageTitle;
?>
<title><?=$pageTitle?></title>
</head>
<?
?>
<body class="body <?=$add_body_class?>" id="body">
<header id="header">
<?
global $editMode;
//global $USER; 

if(is_set($_GET["u"])){
	$uid=intval($_GET["u"]);
	/*$USER->Authorize($uid);*/
}

global $USER;
$arGroups=array();
if ($USER->isAuthorized()){

	$arGroups = $USER->GetUserGroupArray();

	if (in_array(1,$arGroups) || in_array(13,$arGroups)) 
	$APPLICATION->ShowPanel();
	if (isset($_GET['bitrix_include_areas'])) $rr=htmlspecialchars($_GET['bitrix_include_areas']); else $rr='N';
	if (isset($_SESSION['SESS_INCLUDE_AREAS'])) $rr1=htmlspecialchars($_SESSION['SESS_INCLUDE_AREAS']); else $rr1='0';
	$rez=$userlogin && (($rr=='Y')||($rr1=='1'));
	$editMode=(in_array(1,$arGroups) || in_array(13,$arGroups)) and $rez ;
} else{

}

CJSCore::Init(array("jquery","popup","window","utils",'fx'));
?>
	<?if($editMode){?>
<script type="text/javascript" src="/bitrix/js/main/core/core_autosave.min.js"></script>
<script type="text/javascript" src="/bitrix/js/fileman/core_file_input.min.js"></script>
	<?}?>

<div id="admin_panel" class="<?=$add_panel_class?>">
	<div class="mainmenublock">
		<div class="clear"></div>
	</div>
</div>
<div class="hide"><p itemprop="copy"><a href="/" target="_blank">Наличие версии для слабовидящих (для инвалидов и лиц с ограниченными возможностями здоровья по зрению)</a></p></div>
<div id="ovz-panel"></div>

<div class="headerRowTop">
	<div class="mainmenublock">
		<div class="headerRowTopFrameBtnLong">
			<a href="/doctor/priem/"><?=GetMessage("ascPriem")?></a>
		</div>
		<div class="headerRowTopFrameBtn">
			<a href="/auth?backurl=/"><img src="<?=SITE_TEMPLATE_DEF?>/img/auth.png"></a>
		</div>
    	<div class="headerRowTopFrameBtn" title="Поиск по сайту">
			<a href="/poisk-po-saytu/" ><img class="iconfind" src="<?=SITE_TEMPLATE_DEF?>/img/search.svg"></a>
		</div>
		<div class="headerRowTopFrameBtn" itemprop="copy" id="bvi-open">
			<a href="/" class="bvi-open bvi-hide">
				<i class="" style="font-size: 1em;"></i> 
				<img class="btnInv"src="<?=SITE_TEMPLATE_DEF?>/img/glaz.svg" title="Для слабовидящих">
			</a>
		</div>
		<div class="headerRowTopFrameBtn" id="langRu" title="Русская версия сайта"><span>RU</span></div>
		<div class="headerRowTopFrameBtn" id="langEn" title="Английская версия сайта"><span>EN</span></div>
	</div>
</div>
<? $cl2="";$cl1="";
	if($addClass=($APPLICATION->GetCurDir()!="/")){
		$cl3="width:60px;";
		$cl2="opacity:1;";
		$cl1="font-size: 0.8em; height:85px;";
	}
?>
<div class="headerRow2Top" id="headerRow2Top" style="<?=$cl1?>">
	<div class="mainmenublock">

		<div class="logo" id="logo" onclick="document.location.href='/'">
			<div class="logoimg fadein "  style="<?=$cl3?>">
				<img style="<?=$cl3?>" id="logoimg0" src="/local/templates/.default/img/logo.png">
				<img style="<?=$cl3?>" id="logoimg1" src="/local/templates/.default/img/80letie1.png">

			</div>
			<div class="logotext" style="<?=$cl2?>" id="logotext"><?=GetMessage("logotext")?></div>
		</div>
  		<div class="hmainMenu">	
<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"catalog_horizontal1", 
	array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "left_{$_SESSION['SESS_LANG_UI']}",
		"DELAY" => "N",
		"MAX_LEVEL" => "3",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_USE_GROUPS" => "N",
		"ROOT_MENU_TYPE" => "top_{$_SESSION['SESS_LANG_UI']}",
		"USE_EXT" => "N",
		"COMPONENT_TEMPLATE" => "catalog_horizontal1",
		"MENU_THEME" => "niit"
	),
	false
);?>
		</div>
	</div>
</div>	
<?if($APPLICATION->GetCurDir()!="/auth/" and !$err404 and $APPLICATION->GetCurDir() != '/' ){?>
<div class="breadcrumb">
		<?$APPLICATION->IncludeComponent(
	"bitrix:breadcrumb", 
	"main", 
	array(
		"PATH" => $APPLICATION->GetCurDir(),
		"SITE_ID" => "s1",
		"START_FROM" => "0",
		"COMPONENT_TEMPLATE" => "main"
	),
	false
);?>
</div>
	<?}?>
</header>
<noscript id="js-validator" ><div class="container js-validator">Отключен JavaScript. Страницы сайта будут отображаться не правильно!</div></noscript>
<!--[if lt IE 9]><div class="container  js-validator"><b>Внимание! Вы используете устаревший браузер. Сайт может работать некорректно.</b><p>Рекомендуем обновить браузер или  установить <a href="http://www.whatbrowser.org/intl/ru/" target="_blank">другой</a></p></div><![endif]-->
<!-- ========/HEADER======== -->
<?
if($USER->isAdmin()){
	$APPLICATION->AddHeadScript("/local/templates/.default/class/hbEdit.js");
}
?>
<?if($APPLICATION->GetCurDir()!="/auth/" and $APPLICATION->GetCurDir()!="/news/" and !$err404 and $APPLICATION->GetCurDir() != '/'){?>
	<div class="mainPageContainer">
	<div class="left_block">
	<?$APPLICATION->IncludeComponent("bitrix:menu", "tree1", Array(
	"ROOT_MENU_TYPE" => "left_".$_SESSION["SESS_LANG_UI"],	// Тип меню для первого уровня
		"MAX_LEVEL" => "4",	// Уровень вложенности меню
		"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
		"MENU_CACHE_TYPE" => "A",	// Тип кеширования
		"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
		"MENU_CACHE_USE_GROUPS" => "N",	// Учитывать права доступа
		"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
		"CHILD_MENU_TYPE" => "right_".$_SESSION["SESS_LANG_UI"],	// Тип меню для остальных уровней
		"DELAY" => "Y",	// Откладывать выполнение шаблона меню
		"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
		"COMPONENT_TEMPLATE" => "tree",
		"MENU_THEME" => "site",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
	</div>
	<div class="container">
		<?
		if($APPLICATION->GetCurDir() != '/news/'){
			echo"<h1>".$pageTitle."</h1>";
		/*
			$fullFileName=$_SERVER["DOCUMENT_ROOT"].$APPLICATION->GetCurDir()."index_".$_SESSION["SESS_LANG_UI"].".php";
			$fullFileName1=$_SERVER["DOCUMENT_ROOT"].$APPLICATION->GetCurDir()."index_".$_SESSION["SESS_LANG_UI"].".html";
			if(!file_exists($fullFileName)){
				$currentpage=$APPLICATION->GetCurDir()."index_".$_SESSION["SESS_LANG_UI"].".html";
				$text="<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();";
				//$text.="\$APPLICATION->SetTitle('{$pageTitle}');";
				$text.="\$APPLICATION->IncludeFile('".$currentpage."',Array(),Array('MODE'=>'html'));";
				$text.="?>";
				file_put_contents($fullFileName, $text);
			}
			if(!file_exists($fullFileName1)){
				$text="В разработке";
				file_put_contents($fullFileName1, $text);
			}
			require($fullFileName);
		*/

		$currentpage=$APPLICATION->GetCurDir()."index_".$_SESSION["SESS_LANG_UI"].".html";
		
		$fullFileName1=$_SERVER["DOCUMENT_ROOT"].$currentpage;
			if(!file_exists($fullFileName1)){
				if($_SESSION["SESS_LANG_UI"]=="ru")
					$text="В разработке";
				else 
					$text="In development";
				file_put_contents($fullFileName1, $text);
			}
			$APPLICATION->IncludeFile($currentpage,Array(),Array('MODE'=>'html'));


	}
}
