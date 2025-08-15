<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?global $docsFilter;
$docsFilter=array("UF_URL"=>$APPLICATION->GetCurDir());
$APPLICATION->IncludeComponent(
	"niit:highloadblock.docs", 
	"docList", 
	array(
		"BLOCK_ID" => "2",
		"CHECK_PERMISSIONS" => "Y",
		"DETAIL_URL" => "",
		"FILTER_NAME" => "docsFilter",
		"PAGEN_ID" => "page",
		"ROWS_PER_PAGE" => "",
		"SORT_FIELD" => "ID",//UF_SORT
		"SORT_ORDER" => "ASC",//DESC
		"COMPONENT_TEMPLATE" => "docList"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>