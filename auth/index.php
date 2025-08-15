<?
//die();
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if (isset($_REQUEST["backurl"]) && strlen($_REQUEST["backurl"])>0)
	$backurl=htmlspecialchars($_REQUEST["backurl"]);
else 
	$backurl="/";
$params=array();
//if($_GET["guid"]=="tGFgPZqkKpgRFm2lTlHUmaTwEZh"){
//global $USER;
//$USER->Authorize(1);26697
//LocalRedirect("/bitrix/admin/"); 
//}
foreach($_GET as $pkey=>$pval){
	if($pkey!=="start" && $pkey!=="backurl" && $pkey!=="login" ) $params[htmlspecialchars($pkey)]=htmlspecialchars($pval);
	if($pkey==="backurl") $burl=htmlspecialchars($pval);
}
$arbackurl=parse_url($burl);
$backpath=$arbackurl["path"];
parse_str($arbackurl["query"], $getbackurl);
foreach($getbackurl as $pkey=>$pval){
	if($pkey!=="start" && $pkey!=="backurl" && $pkey!=="login" ) $params[htmlspecialchars($pkey)]=htmlspecialchars($pval);
}

$params["start"]=date("d");
$backurl="https://www.nsk-niit.ru{$backpath}?".http_build_query($params);
echo "<script> document.location.href=\"{$backurl}\";</script>";
//LocalRedirect($backurl);
/*
$APPLICATION->SetTitle("Авторизация");
$extra = 'autentifikatsiya.php';header("Location: /auth/$extra");
*/
?>
<p>Вы зарегистрированы и успешно авторизовались.</p>
<p>Используйте административную панель в верхней части экрана для быстрого доступа к функциям управления структурой и информационным наполнением сайта. Набор кнопок верхней панели отличается для различных разделов сайта. Так отдельные наборы действий предусмотрены для управления статическим содержимым страниц, динамическими публикациями (новостями, каталогом, фотогалереей) и т.п.</p>
<p><a href="<?=SITE_DIR?>">Вернуться на главную страницу</a></p>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>