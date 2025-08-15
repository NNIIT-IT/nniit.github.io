<?
if(!isset($mode)) {
$rightMenu=true;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Информация о трудоустройстве выпускников для каждой реализуемой образовательной программы, по которой состоялся выпуск");
?>
<h1>Образование</h1>
<?}?>
<h2>Информация о трудоустройстве выпускников для каждой реализуемой образовательной программы, по которой состоялся выпуск</h2>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
$z=new asmuinfo();
$z->setAdminGroups(array(8));
$z->setСlassList(array(array("classname"=>"graduateJob","params"=>array("god"=>2024,"ver"=>2))));
//$table2=$z->getHtml(false,true);
if(!isset($mode))
	 $table2=$z->getHtml(false,true);
else
	 $table2=$z->getHtml(false,false);
?>
<br><br>
<div><?=$table2?></div>
<?
if(!isset($mode)) {
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}
?>
