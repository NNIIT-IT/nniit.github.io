<?
if(!isset($mode)){
$rightMenu=true;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

echo "<div class=\"container\">";
}
require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
if(!isset($mode)) {
	echo "<h1>Образование</h1>";

}
echo "<h2>Информация об образовательной программе</h2>";
	echo "(для каждого года набора)";
if(!isset($mode)){
	echo "</div>";
}
$z=new asmuinfo();
$z->setСlassList(array(array("classname"=>"opedu","params"=>array("ovz"=>0,"hideTitle"=>1))));
$z->setAdminGroups(array(8));
if(!isset($mode))
	 echo $z->getHtml(false,true);
else
	 echo $z->getHtml(false,false);

?>
<h3 class="texticon hidedivlink link">Образовательные программы дополнительного профессионального образования</h3>
<?
	echo "<div style=\"display:none\">";
$APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"dpo", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "N",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array(
			0 => "ID",
			1 => "NAME",
			2 => "SORT",
			3 => "",
		),
		"FILTER_NAME" => "",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "76",
		"IBLOCK_TYPE" => "sveden",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "Y",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "1000",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Новости",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "secondSpec",
			1 => "info",
			2 => "volume",
			3 => "mainSpec",
			4 => "form",
			5 => "lang",
			6 => "level",
			7 => "",
		),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"COMPONENT_TEMPLATE" => "dpo"
	),
	false
);
echo "</div>";
?>
<?if(!isset($mode)){
//	echo "</div>";
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}?>