<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);?>
<ul>
<?if (!empty($arResult)) { ?>

    <?
    $previousLevel = 0;
    foreach($arResult as $arItem) { ?>
        <? if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel) { ?>
		<?=str_repeat("</ul></div></div></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>			
		<? }?>
        <? if ($arItem["IS_PARENT"]) { ?>
            <? if ($arItem["DEPTH_LEVEL"] == 1) { ?>

                <li class="parent dl-<?=$arItem["DEPTH_LEVEL"];?> <?if ($arItem["SELECTED"]):?>item-selected<?endif?>">
					<a tabindex="1" href="<?=($arItem["PARAMS"]['NO']=="Y" ? "javascript:void(0);":$arItem["LINK"])?>" class='dl-<?=$arItem["DEPTH_LEVEL"];?> <?=($arItem["PARAMS"]['NO']=="Y" ? "btn-no h4":"")?>'><?=$arItem["TEXT"]?></a>
                    <div class="lvl-2 container-fluid">
						<div class="content-menu">
                                    <ul class="clearfix">
			<? }else { ?>
				<li class="parent dl-<?=$arItem["DEPTH_LEVEL"];?>" <?=$arItem["PARAMS"]['STYLE']?>>
	            <a tabindex="1" href="<?=($arItem["PARAMS"]['NO']=="Y" ? "javascript:void(0);":$arItem["LINK"])?>" <?=($arItem["PARAMS"]['NO']=="Y" ? "class='btn-no h4'":"")?>><?=$arItem["TEXT"]?></a>
				    <div class="container-fluid">
						<div class="content-menu">
                                    <ul class="clearfix">
			<? }?>	
        <? } else { ?>
            <? if ($arItem["DEPTH_LEVEL"] == 1) { ?>
				<li class="dl-<?=$arItem["DEPTH_LEVEL"];?>"><a href="<?=($arItem["PARAMS"]['NO']=="Y" ? "javascript:void(0);":$arItem["LINK"])?>" <?=($arItem["PARAMS"]['NO']=="Y" ? "class='btn-no h4'":"")?>><?=$arItem["TEXT"]?></a></li>    
			<? }else { ?>
			 <li class="dl-<?=$arItem["DEPTH_LEVEL"];?> <?if ($arItem["SELECTED"]):?>item-selected<?endif?>" <?=$arItem["PARAMS"]['STYLE']?>>
	            <a href="<?=($arItem["PARAMS"]['NO']=="Y" ? "javascript:void(0);":$arItem["LINK"])?>" <?=($arItem["PARAMS"]['NO']=="Y" ? "class='btn-no h4'":"")?>><?=$arItem["TEXT"]?></a>
			</li>
			<? }?>	
			<? }?>	
        <?$previousLevel = $arItem["DEPTH_LEVEL"];?>
		<? } ?>	
    <? } ?>
    <? if ($previousLevel > 1) { ?>
	<?=str_repeat("</ul></div></div></li>", ($previousLevel-1) );?>	
    <? } ?>
							<!-- li class="d-lg-none"><span class="spec-lk"><a href="#"><i class="user-icon icon"></i> <span>Личный кабинет</span></a></span></li>
							<li class="d-lg-none"><span class="spec-vision aa-hide"><a href="/?set-aa=special" class="spec-vision-link" data-aa-on><i class="glases-icon icon"></i> <span>Для слабовидящих</span></a></span></li>
							<li>
							<div class="additional-info d-lg-none">	
								<div class="row no-gutters">
									<div class="col-12 header-info-block">
										<div class="place place-icon icon">г. Новосибирск, ул Николаева, д 12/3, этаж 2</div>
										<div class="align-self-md-end time time-icon icon header-info-block">С 9:00 до 18:00, пн-пт</div>		
									</div>
								</div>
							</div>
							</li -->

<?/*global $USER;if($USER->IsAuthorized()){	echo "<a href=\"/ob-institute\">Об институте</a>";}*/?>

</ul>
<??>