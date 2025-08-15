<?
if(!isset($mode)) {
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Образовательная программа, наличие практики");
	echo "<div class=\"container\">";
}
require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
echo "<h1>Образовательные программы, наличие практики</h1>";
echo "<!-- version ".date("Y-m-d His")." -->";
echo "<div itemprop=\"eduObrProg\">";
$zx1=new asmuinfo();
$zx1->setСlassList(array(array("classname"=>"edupract","params"=>array("ovz"=>0))));
$zx1->getHtml(false,true);
unset($zx1);
$zx2=new asmuinfo();
$zx2->setСlassList(array(array("classname"=>"edupract","params"=>array("ovz"=>1))));
$zx2->getHtml(false,false);
echo "</div>";
unset($zx2);

if(!isset($mode)) {
	echo "</div>";
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}
?>