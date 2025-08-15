<?
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//$APPLICATION->SetTitle("Организация питания в образовательной организации");
?>
<?
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$z=new asmuinfo();
$z->setAdminGroups(array(8));
$z->setСlassList(array(array("classname"=>"xobjects","params"=>array("open"=>1,"tables"=>array("meals","health")))));
$z->getHtml(false,true);
?>
<span class="texticon hidedivlink linkicon link">Документы</span>
<div style="padding: 1em; " class="">
<?$params=array(
	"mainPropList"=>array(),
	"propList"=>array("localAct"),
	"sectionsList"=>array(441),
	"mainsections"=>array(441),//1175
	"hideCaption"=>1
	);
$z->setAdminGroups(array(8));
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params),
));
$z->getHtml(false);

?>
</div><br>

<?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>