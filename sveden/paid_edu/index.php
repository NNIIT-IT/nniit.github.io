<?
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$APPLICATION->SetTitle("Платные образовательные услуги");
//echo "<h1>Платные образовательные услуги</h1>";
$z=new asmuinfo();
$itemAr=array("classname"=>"maindocs","params"=>array("onlyValue"=>1,"sectionsList"=>array(),"mainsections"=>array(400),"hideCaption"=>1));
$itemAr1=array("classname"=>"maindocs","params"=>array("sectionsList"=>array(),"mainsections"=>array(400),"hideCaption"=>1));
?><br>
<b>О порядке оказания платных образовательных услуг, в том числе образец договора об оказании платных образовательных услуг</b>
<?$itemAr1["params"]["propList"]=array("paidEdu","docLink"); $z->setСlassList(array($itemAr1));$z->getHtml(false,true);?>
<?$params=array("propList"=>array("paidDog",),
	"mainsections"=>array(),
	"sectionsList"=>array(402,403,404,405,406,407,408,409,410,411,422),
	"hideCaption"=>1,
	"scoupeList"=>array());
$z->setСlassList(array(	array("classname"=>"maindocs","params"=>$params),));
$z->setAdminGroups(array(8));
$z->getHtml();
?><br>
<b>Об утверждении стоимости обучения по каждой образовательной программе</b>
<?$itemAr1["params"]["propList"]=array("docLink"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?>

<?$params=array(
	"propList"=>array("paidSt",),
	"mainsections"=>array(),
	"sectionsList"=>array(402,403,404,405,406,407,408,409,410,411,422),
	"hideCaption"=>1,
	"scoupeList"=>array());
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params),
));
$z->setAdminGroups(array(8));
$z->getHtml();?>
<br>
<b>Об установлении размера платы, взимаемой с родителей (законных представителей) за присмотр и уход за детьми, осваивающими образовательные программы дошкольного образования в организациях, осуществляющих образовательную деятельность, за содержание детей в образовательной организации, реализующей образовательные программы начального общего, основного общего или среднего общего образования, если в такой образовательной организации созданы условия для проживания обучающихся в интернате, либо за осуществление присмотра и ухода за детьми в группах продленного дня в образовательной организации, реализующей образовательные программы начального общего, основного общего или среднего общего образования.
</b>
<?$itemAr1["params"]["propList"]=array("paidParents"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);

if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>