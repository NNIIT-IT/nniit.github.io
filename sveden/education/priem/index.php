<?
if(!isset($mode)) {
$rightMenu=true;
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	require_once(__DIR__."/../../class/asmuinfo/asmuinfo.php");
	$APPLICATION->SetTitle("Информация о результатах приёма");
	echo '<h1>Образование</h1>';
}
echo "<h2>Информация о результатах приёма <span id=\"prkomreportgod\"></span></h2>";
	global $USER;
	$z=new asmuinfo();
	$params=array(	"mainPropList"=>array(),	"propList"=>array("eduPriemEl"),	);
	$z->setAdminGroups(array(8));
	$z->setСlassList(array(
		array("classname"=>"maindocs","params"=>$params),
	));
	echo $z->getHtml(false);

	$params=array(	);
	$z->setAdminGroups(array(41728));
	$z->setСlassList(
		array(
			array("classname"=>"priemRez","params"=>$params),
		)
	);
	$z->getHtml(false);
	
	
if(!isset($mode)) {
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}
?>