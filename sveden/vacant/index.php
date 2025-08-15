<?
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//$APPLICATION->SetTitle("Вакантные места для приёма (перевода) обучающихся");?>

<?
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$zz=new asmuinfo();
$manualMest=array(
	/*"14.04.03"=>array(
			"2"=>array("Заочная"=>array("FB"=>0,"P"=>1),"Очная"=>array("FB"=>0,"P"=>0),
		),
	
	)*/
);
$params["manual"]=$manualMest;

//if($USER->isAdmin())$params=array("testmode"=>1);
$zz->setAdminGroups(array(8));
//if($USER->isAdmin())
//$z->setСlassList(array(	array("classname"=>"vacant1","params"=>$params),));
//else

$zz->setСlassList(array(	array("classname"=>"vacant","params"=>$params),));
$zz->getHtml(false);
?>
<br><?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>