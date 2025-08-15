<?
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetTitle("Title");
global $USER;
if ($USER->isAuthorized()){
echo "<pre>";
print_r($_SERVER);
print_r($_SESSION);
foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}
	echo "</pre>";
}

//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>