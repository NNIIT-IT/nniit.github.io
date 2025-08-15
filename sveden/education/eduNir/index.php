<?
if(!isset($mode)) {
$rightMenu=true;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
}
$APPLICATION->SetTitle("Информация о направлениях и результатах
научной (научно-исследовательской) деятельности и
научно-исследовательской базе для ее осуществления
(для образовательных организаций высшего образования
и организаций дополнительного профессионального
образования)");
if(!isset($mode)) echo "<h1>Образование</h1>";
?>
<h2>Информация о направлениях и результатах
научной (научно-исследовательской) деятельности и
научно-исследовательской базе для ее осуществления
(для образовательных организаций высшего образования
и организаций дополнительного профессионального
образования)</h2>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
$z=new asmuinfo();
$z->setAdminGroups(array(8));
$z->setСlassList(array(array("classname"=>"edunir","params"=>array("ovz"=>0))));
if(!isset($mode))
	 echo $z->getHtml(false,true);
else
	 echo $z->getHtml(false,false);
if(!isset($mode)) {
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}
?>
