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
$this->setFrameMode(true);
?>
<div class="news-list" id="mainNews">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	//echo "<pre>";print_r($arItem);echo "</pre>";

	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	$lang=$_SESSION["SESS_LANG_UI"];
	$newscaption=$arItem["PROPERTIES"]["title_".$lang]["VALUE"];
	if($newscaption=="")$newscaption=$arItem["FIELDS"]["NAME"];
	$sdate=$arItem["FIELDS"]["DATE_ACTIVE_FROM"];
	$addons=$arItem["PROPERTIES"]["addons"]["VALUE_XML_ID"];
	$fonColor=trim($arItem["PROPERTIES"]["fonColor"]["VALUE"]);
	$itemBkColor=$fonColor!=""?"#".$fonColor:"#fff";
	?>
	<div class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>" style="background-color:<?=$itemBkColor?>!important;">
	<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
		<div class="news_picture" style="background-color:<?=$itemBkColor?>!important;">
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
				<img  style="background-color:<?=$itemBkColor?>!important;"

						border="0"
						src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
						width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
						height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
						alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
						title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
				/><?else:?><img	
						style="background-color:<?=$itemBkColor?>!important;"		
						border="0"
						src="<?=$templateFolder?>/default.png"
						width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
						height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
						alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
						title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"

				/>
		<?endif?>
		</div>
		<div class="news-title">
		<div><?=$newscaption?></div>
		<div class="news-date"><?=$sdate?></div>
		</div>
	</a>
</div>	
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
