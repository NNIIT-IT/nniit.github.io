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
?>
<?
$cntslide=count($arResult["ITEMS"]);
$cnt=1;//slider_carousel
?><div class="slider">
<ul class="slider_main" id="slider_main">
<?foreach($arResult["ITEMS"] as $arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	echo "<li>";
//$pic_url = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"],	array( 'width' => 1920, 'height' => 412),	BX_RESIZE_IMAGE_PROPORTIONAL, true,true,false)['src'];//BX_RESIZE_IMAGE_EXACT 
$pic_url=$arItem["DETAIL_PICTURE"]["SRC"];
		/*$arImg = CFile::ResizeImageGet(					$arItem["DETAIL_PICTURE"],
										array('height' => 120),
										BX_RESIZE_IMAGE_PROPORTIONAL, 
										true,
										true,
										false);
		*/
		$id="mainslide_".$this->GetEditAreaId($arItem['ID']);
		?>
		<div class="slide backgroundSlide" id="<?=$id?>" style="background-image: url('<?=$pic_url?>');">
			<img src="<?=$pic_url?>"  style="width: 100vw; display:none;" >
			<div class="slide__plate">
				<?if (($arItem["PROPERTIES"]["SHOW_ALL_BOX"]["VALUE"]!="Да")){?>
						<?if (($arItem["NAME"]!='')&&($arItem["PROPERTIES"]['SHOW_NAME']['VALUE']=='Да')):?>
							<a class="slide__title slide-up" href="<?=$arItem["PROPERTIES"]["URL"]["VALUE"]?>"><?=$arItem["NAME"];?></a>
						<?endif?>
		
						<?if ($arItem["PREVIEW_TEXT"]!=''){?> <div class="slide__txt">	<?=$arItem["PREVIEW_TEXT"]?></div><?}?>
				<?} else{?>
		
					<?//if ($USER->isadmin()){?>
					<?if ($arItem["PREVIEW_TEXT"]!=''):?> <?=$arItem["PREVIEW_TEXT"]?><?endif?>
					<? //echo $arItem["PROPERTIES"]["SHOW_ALL_BOX"]["VALUE"];?>
				<?}?>

			</div>
		</div>
<?
	echo "</li>";
endforeach;
?>
</ul>
</div>