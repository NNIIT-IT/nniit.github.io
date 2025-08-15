<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';
//$APPLICATION->AddChainItem($arResult["TITLE"],"");
//we can't use $APPLICATION->SetAdditionalCSS() here because we are inside the buffered function GetNavChain()
$css = $APPLICATION->GetCSSArray();
if(!is_array($css) || !in_array("/bitrix/css/main/font-awesome.css", $css))
{
	$strReturn .= '<link href="'.CUtil::GetAdditionalFileURL("/bitrix/css/main/font-awesome.css").'" type="text/css" rel="stylesheet" />'."\n";
}

$strReturn .= '<div class="bx-breadcrumb" itemprop="http://schema.org/breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';
$itemSize = count($arResult);
$itemsPath=array();
$arResultMod=array();
for($index = 0; $index < $itemSize; $index++){
	if(!in_array($arResult[$index]["LINK"],$itemsPath)){
		$itemsPath[]=$arResult[$index]["LINK"];
		$arResultMod[]=$arResult[$index];
	} else{

	}
}
$arResult=$arResultMod;
//echo "<pre>";print_r($arResult);echo "</pre>";
//$arResult=array_unique($arResult);
$itemSize = count($arResult);
//echo "<pre>";print_r($arResult);echo "</pre>".$itemSize;
if(trim($arResult[$itemSize-1]["LINK"])!=""){
		$arResult[$itemSize-1]["TITLE"]=$APPLICATION->GetDirProperty("caption_".$_SESSION['SESS_LANG_UI'],$arResult[$itemSize-1]["LINK"],$title);
	}

for($index = 0; $index < $itemSize-1; $index++){
	if(trim($arResult[$index]["LINK"])!=""){
		$arResult[$index]["TITLE"]=$APPLICATION->GetDirProperty("caption_".$_SESSION['SESS_LANG_UI'],$arResult[$index]["LINK"],$title);
	}
	$t1=$arResult[$index]["TITLE"];
	$t2=$arResult[$index+1]["TITLE"];
	if($t1==$t2) {
		$arResult[$index]["LINK"]="";
		unset($arResult[$index+1]);
		$itemSize = count($arResult);
	}
	if($arResult[$index+1]["LINK"]=="" && $arResult[$index+1]["TITLE"]=="") {
			unset($arResult[$index+1]);
			$itemSize = count($arResult);
	}
}
//echo "<pre>";print_r($arResult);echo "</pre>";
for($index = 0; $index < $itemSize; $index++)
{

	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);





		$arrow = ($index > 0? '<i class="bx-breadcrumb-item-angle fa fa-angle-right"></i>' : '');

		if(trim($arResult[$index]["LINK"]) <> "" && $index != $itemSize-1)
		{
			$strReturn .=  $arrow.'
				<div class="bx-breadcrumb-item" id="bx_breadcrumb_'.$index.'" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a class="bx-breadcrumb-item-link" href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="item">
						<span class="bx-breadcrumb-item-text" itemprop="name">'.$title.'</span>
					</a>
					<meta itemprop="position" content="'.($index + 1).'" />
				</div>';
		}else{
			$strReturn .= $arrow.'
				<div class="bx-breadcrumb-item">
					<span class="bx-breadcrumb-item-text">'.$title.'</span>
				</div>';
		}

}

$strReturn .= '</div>';

return $strReturn;
