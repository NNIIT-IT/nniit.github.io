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

//echo "<pre>"; echo print_r($arParams); echo "</pre>";
//echo "<pre>"; echo print_r($arResult); echo "</pre>";
//$NTAB="NEWS";
//$NEWSGROUP=intval($arParams["NEWSGROUP"]);
	$APPLICATION->AddHeadString('<meta itemprop="name" content="'.$arResult["TITLE"].'">');
	$APPLICATION->SetPageProperty("OG_TITLE",$arResult["TITLE"]);
	$APPLICATION->AddHeadString('<link itemprop="url" href="https://www.nsk-niit.ru/news/?id='.$arResult["ID"].'/">');
if(is_array($arResult["PREVIEW_PICTURE"])){
	$APPLICATION->SetPageProperty("OG_IMAGE",$arResult["PREVIEW_PICTURE"]["SRC"]);
	$APPLICATION->AddHeadString('<met a property="og:image" content="'.$arResult["PREVIEW_PICTURE"]["SRC"].'"/>');
	$APPLICATION->AddHeadString('<met a name="twitter:image" content="'.$arResult["PREVIEW_PICTURE"]["SRC"].'"/>');
	$APPLICATION->AddHeadString('<met a property="og:image:width" content="110">');
	$APPLICATION->AddHeadString('<met a property="og:image:height" content="55">');
	$APPLICATION->AddHeadString('<link itemprop="thumbnailUrl" href="https://www.nsk-niit.ru/'.$arResult["PREVIEW_PICTURE"]["SRC"].'">');
}
$APPLICATION->SetTitle($arResult["TITLE"]);
$this->setFrameMode(true);
?>

<div class="bx-newsdetail bvi-voice" >
	<div class="bx-newsdetail-block"  id="<?echo $this->GetEditAreaId($arResult['ID'])?>">
<?
$PODRAZD=$arResult['PROPERTIES']['PODR']['VALUE'];
//echo  $PODRAZD;
/*
$BUCK_URL="";
if ($PODRAZD!=''){
 $res = CIBlockElement::GetProperty(103, $PODRAZD, "sort", "asc", array("CODE" => "URL_E"));
	while ($ob = $res->GetNext())
	{
        	$BUCK_URL = $ob['VALUE'];
	}
}
*/
//echo "<pre>";print_r($arResult);echo "</pre>";




	$sdate=$arResult["FIELDS"]["DATE_ACTIVE_FROM"];
	$addons=$arResult["PROPERTIES"]["addons"]["VALUE_XML_ID"];

?>

	<?if($arParams["DISPLAY_PICTURE"]!="N"):?>


		<?if ($arResult["VIDEO"]):?>
			<div class="bx-newsdetail-youtube embed-responsive embed-responsive-16by9" style="display: block;">
				<iframe src="<?echo $arResult["VIDEO"]?>" frameborder="0" allowfullscreen=""></iframe>
			</div>
		<?elseif ($arResult["SOUND_CLOUD"]):?>
			<div class="bx-newsdetail-audio">
				<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?echo urlencode($arResult["SOUND_CLOUD"])?>&amp;color=ff5500&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>
			</div>
		<?elseif ($arResult["SLIDER"] && count($arResult["SLIDER"]) > 1):?>
			<div class="bx-newsdetail-slider">
				<div class="bx-newsdetail-slider-container" style="width: <?echo count($arResult["SLIDER"])*100?>%;left: 0%;">
					<?foreach ($arResult["SLIDER"] as $file):?>
					<div style="width: <?echo 100/count($arResult["SLIDER"])?>%;" class="bx-newsdetail-slider-slide">
						<img src="<?=$file["SRC"]?>" alt="<?=$file["DESCRIPTION"]?>">
					</div>
					<?endforeach?>
					<div style="clear: both;"></div>
				</div>
				<div class="bx-newsdetail-slider-arrow-container-left"><div class="bx-newsdetail-slider-arrow"><i class="fa fa-angle-left" ></i></div></div>
				<div class="bx-newsdetail-slider-arrow-container-right"><div class="bx-newsdetail-slider-arrow"><i class="fa fa-angle-right"></i></div></div>
				<ul class="bx-newsdetail-slider-control">
					<?foreach ($arResult["SLIDER"] as $i => $file):?>
						<li rel="<?=($i+1)?>" <?if (!$i) echo 'class="current"'?>><span></span></li>
					<?endforeach?>
				</ul>
			</div>
		<?elseif ($arResult["SLIDER"])://SLIDER?>
			<div class="bx-newsdetail-img">
				<img
					src="<?=$arResult["SLIDER"][0]["SRC"]?>"
					width="<?=$arResult["SLIDER"][0]["WIDTH"]?>"
					height="<?=$arResult["SLIDER"][0]["HEIGHT"]?>"
					alt="<?=$arResult["SLIDER"][0]["ALT"]?>"
					title="<?=$arResult["SLIDER"][0]["TITLE"]?>"
					/>
			</div>
		<?elseif (is_array($arResult["DETAIL_PICTURE"])):?>
			<div class="bx-newsdetail-img">
				<img
					src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
					width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
					height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
					alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
					title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
					/>
			</div>
		<?endif;?>
	<?endif?>



	<?if(is_array($arResult["PREVIEW_PICTURE"])):


	?>
	<div style="display:inline-block;min-width:240px;text-align:center;margin-left:10px;">
	<picture style="width:100%;min-height:383px;">
	<source style="width:100%;min-height:383px;" srcset="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" type="img/jpeg">
	<img class="preview_picture" style="float:left;padding-right: 1em;" border="0" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$title?>"  title="<?=$title?>"/>
	</picture>
	</div>
	<?endif;?>
	
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["TITLE"]):?>
	<?$dvst=""; if(is_array($arResult["PREVIEW_PICTURE"])): $dvst = " style='display: inline-block; min-width: 360px; vertical-align: top; width: calc(100% - 360px);'"; endif;?>
	<div <?=$dvst?>>
	<h1 class="bx-newsdetail-title"><?=$arResult["TITLE"]?></h1>
	</div>
	<?endif;?>

	<div class="bx-newsdetail-content" style="overflow: hidden;">
	<?
if($arResult["NAV_RESULT"]){
		if($arParams["DISPLAY_TOP_PAGER"]){
			echo $arResult["NAV_STRING"]."<br>";
		}
		echo $arResult["NAV_TEXT"];
		if($arParams["DISPLAY_BOTTOM_PAGER"]){
			echo "<br />".$arResult["NAV_STRING"];
		}
}
if(strlen($arResult["TEXT"])>0) echo $arResult["TEXT"];


//imbedded pdf

$pdf_file_id=intval($arResult["PROPERTIES"]["pdf"]["VALUE"]);
if($pdf_file_id>0){
	echo "<div class=\"center\">";
	echo "<ob ject data=\"/files/?id={$pdf_file_id}&json=1\" type=\"application/pdf\" width=\"80%\" height=\"600px\">";
	echo "<a href=\"/files/?id={$pdf_file_id}&load=1\">Скачать файл</a>";
	echo "</object></div>";
}

?>
	</div>

	<?foreach($arResult["FIELDS"] as $code=>$value):?>
		<?if($code == "SHOW_COUNTER"):?>
			<div class="bx-newsdetail-view"><i class="fa fa-eye"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
				<?=intval($value);?>
			</div>
		<?elseif($code == "SHOW_COUNTER_START" && $value):?>
			<?
			$value = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($value, CSite::GetDateFormat()));
			?>
			<div class="bx-newsdetail-date"><i class="fa fa-calendar-o"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
				<?=$value;?>
			</div>
		<?elseif($code == "TAGS" && $value):?>
			<div class="bx-newsdetail-tags"><i class="fa fa-tag"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
				<?=$value;?>
			</div>
		<?elseif($code == "CREATED_USER_NAME"):?>
			<div class="bx-newsdetail-author"><i class="fa fa-user"></i> <?=GetMessage("IBLOCK_FIELD_".$code)?>:
				<?=$value;?>
			</div>
		<?elseif ($value != ""):?>
		<!--div class="bx-newsdetail-other"><i class="fa"></i--> <?//=GetMessage("IBLOCK_FIELD_".$code)?>
<?//=$value;?>
			<!--/div-->
		<?endif;?>
	<?endforeach;?>

	<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
		<?
		if(is_array($arProperty["DISPLAY_VALUE"]))
			$value = implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
		else
			$value = $arProperty["DISPLAY_VALUE"];
		?>
		<?if($arProperty["CODE"] == "FORUM_MESSAGE_CNT"):?>
			<div class="bx-newsdetail-comments"><i class="fa fa-comments"></i> <?=$arProperty["NAME"]?>
				<?=$value;?>
			</div>
		<?elseif ($value != ""):?>
		<!--div class="bx-newsdetail-other"><i class="fa"></i--> <?//=$arProperty["NAME"]?>
				<?//=$value;?>
			<!--/div-->
		<?endif;?>
	<?endforeach;?>
<?

// additional photos
$LINE_ELEMENT_COUNT = 4; // number of elements in a row
if(is_array($arResult["MORE_PHOTO"]) && count($arResult["MORE_PHOTO"])>0):
$titlealbom=$arResult["MORE_PHOTO_TITLE"]["VALUE"];
if ($titlealbom=="")$titlealbom="Фотоальбом";
echo '<hr /><div>';
echo '<h2>'.$titlealbom.'</h2><div>';

	foreach($arResult["MORE_PHOTO"] as $PHOTO):
		$file = CFile::ResizeImageGet($PHOTO, array('width'=>150, 'height'=>112), BX_RESIZE_IMAGE_EXACT, true); 
		echo '<a data-fancybox="gallery" href="'.$PHOTO["SRC"].'" data-caption="'.$PHOTO['DESCRIPTION'].'" >';
		//echo ' <img border="0" src="'.$file["src"].'" width="'.$file["width"].'" height="'.$file["height"].'">';
		echo ' <img border="0" src="'.$file["src"].'" width="150" height="112">';
		echo '</a>';

		//print_r($PHOTO["ID"]);
//echo '-->';

	endforeach;
echo '<hr /></div>';
echo '</div>';
endif;

?> 



	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<div class="bx-newsdetail-date"><i class="fa fa-calendar-o"></i> <?echo $arResult["DISPLAY_ACTIVE_FROM"]?></div><br />
	<?endif?>

	<?if($arParams["USE_RATING"]=="Y"):?>
		<div class="bx-newsdetail-separator">|</div>
		<div class="bx-newsdetail-rating">
			<?$APPLICATION->IncludeComponent(
				"bitrix:iblock.vote",
				"flat",
				Array(
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"ELEMENT_ID" => $arResult["ID"],
					"MAX_VOTE" => $arParams["MAX_VOTE"],
					"VOTE_NAMES" => $arParams["VOTE_NAMES"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"DISPLAY_AS_RATING" => $arParams["DISPLAY_AS_RATING"],
					"SHOW_RATING" => "Y",
				),
				$component
			);?>
		</div>
	<?endif?>





	<div class="row">
		<div class="col-xs-5">
		</div>
	<?
	if ($arParams["USE_SHARE"] == "Y")
	{
		?>
		<div class="col-xs-7 text-right">
			<noindex>
			<?
			$APPLICATION->IncludeComponent("bitrix:main.share", $arParams["SHARE_TEMPLATE"], array(
					"HANDLERS" => $arParams["SHARE_HANDLERS"],
					"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
					"PAGE_TITLE" => $arResult["~NAME"],
					"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
					"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
					"HIDE" => $arParams["SHARE_HIDE"],
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);
			?>
			</noindex>
		</div>
		<?
	}
	?>
	</div>
	</div>
</div>
<?
$resElement = CIBlockElement::GetByID($arResult['ID'])->GetNext();
$cnt=$resElement["SHOW_COUNTER"];
$cnt1=$cnt;
if (($arResult['ID']==33865)&&($cnt<500)) {
	$cnt=374+intval($cnt/3);
	$n=intval($cnt1*$cnt1/200);
	$cnt3=intval(360/$n*($n-1)+$cnt1/$n);
} else $cnt3=$cnt1;
echo '<small>Количество просмотров: '.$cnt3.'</small>';
echo "<!-- $cnt1 $n $cnt3-->";
?><!-- end news -->