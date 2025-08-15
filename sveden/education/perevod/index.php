<?if(!isset($mode)) {
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Информация о результатах перевода, восстановления и отчисления");
echo "<h1>Образование</h1>";
}
require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
$section=intval($_GET["section"]);
echo "<h2>Информация о результатах перевода, восстановления и отчисления</h2>";
	$zz=new asmuinfo();
	$zz->setAdminGroups(array(8));
	$params=array("mainPropList"=>array(),"propList"=>array("eduPerevodEl"),);
	$zz->setСlassList(array(
		array("classname"=>"maindocs","params"=>$params),
	));
	$zz->getHtml(false);
	$params=array("section"=>$section);
	$zz->setСlassList(array(
		array("classname"=>"studperevod","params"=>$params),
	));

	$zz->getHtml(false,false);

if(!isset($mode)) {
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}

?>