<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if($_REQUEST["lang"]=="ru") {
	define("LANGUAGE_ID","ru");
	$_SESSION["SESS_LANG_UI"]="ru";
}
if($_REQUEST["lang"]=="en") {
		define("LANGUAGE_ID","en");
		$_SESSION["SESS_LANG_UI"]="en";
}

?>