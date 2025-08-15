<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
 <?$APPLICATION->IncludeComponent(
	"bitrix:highloadblock.list",
	"docList",
	Array(
		"BLOCK_ID" => "2",
		"CHECK_PERMISSIONS" => "N",
		"COMPONENT_TEMPLATE" => ".default",
		"DETAIL_URL" => "",
		"FILTER_NAME" => "",
		"PAGEN_ID" => "page",
		"ROWS_PER_PAGE" => "",
		"SORT_FIELD" => "ID",
		"SORT_ORDER" => "DESC"
	)
);?><br>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>