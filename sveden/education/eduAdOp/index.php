<?
if(!isset($mode)) {
$rightMenu=true;
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Информация об адаптированной образовательной программе");
	echo "<h1>Образование</h1>";
	echo "<div class=\"container\">";
}
require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
$z=new asmuinfo();
$z->setСlassList(array(array("classname"=>"opedu","params"=>array("ovz"=>"1"))));
$z->getHtml();

if(!isset($mode)){
	echo "</div>";
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}
?>