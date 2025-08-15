<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
if($arResult["TITLE"]!=""){
	//$APPLICATION->AddChainItem($arResult["TITLE"], "");
	$APPLICATION->SetTitle($arResult["TITLE"]);
	$APPLICATION->AddChainItem($arResult["TITLE"],"");
	$APPLICATION->SetPageProperty("OG_TITLE",$arResult["TITLE"]);
	$APPLICATION->SetDirProperty("OG_TITLE",$arResult["TITLE"]);
}
if($arResult["OG_IMAGE"]!=""){
	$APPLICATION->SetDirProperty("OG_IMAGE",$arResult["OG_IMAGE"]);
	$APPLICATION->SetPageProperty("OG_IMAGE",$arResult["OG_IMAGE"]);
	$_SESSION["OG_IMAGE"]=$arResult["OG_IMAGE"];
}?>