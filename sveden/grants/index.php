<?
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Стипендии и иные виды материальной поддержки");
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");

echo "<h1>Стипендии и меры поддержки обучающихся</h1>";
$z=new asmuinfo();
$z->setAdminGroups(array(8));
$itemAr=array("classname"=>"maindocs","params"=>array("onlyValue"=>1,"sectionsList"=>array(),"mainsections"=>array(395),"hideCaption"=>1));
$itemAr1=array("classname"=>"maindocs","params"=>array("sectionsList"=>array(),"mainsections"=>array(412),"hideCaption"=>1));
$itemAr2=array("classname"=>"maindocs","params"=>array("sectionsList"=>array(),"mainsections"=>array(395),"hideCaption"=>1));
$itemAr3=array("classname"=>"maindocs","params"=>array("sectionsList"=>array(412),"hideCaption"=>1));
?><br>
<b>О наличии и условиях предоставления обучающимся стипендий</b>
<?$itemAr1["params"]["propList"]=array("grant"); $z->setСlassList(array($itemAr1));$z->getHtml(false,true);?>
<?$itemAr3["params"]["propList"]=array("localAct"); $z->setСlassList(array($itemAr3));$z->getHtml(false,false);?>

<b>О наличии и условиях предоставления обучающимся мер социальной поддержки</b><br>
<?$itemAr1["params"]["propList"]=array("support"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?>
<!-- b>Локальный нормативный акт, которым регламентируется наличие и условия предоставления стипендий</b><br -->


<br>
<b>О наличии общежития, интерната</b><br>
<table class="simpletable" style="max-width:600px;font-size:0.9em;">
	<tr><th>Наименование показателя</th><th>Общежития</th><th>Интернаты</th><tr>
	<tr>
	<td> Количество общежитий/интернатов</td>
	<td><?$itemAr["params"]["propList"]=array("hostelInfo"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?></td>
	<td><?$itemAr["params"]["propList"]=array("interInfo"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?></td>
</tr>
<tr>
	<td> Количество мест </td>
	<td><?$itemAr["params"]["propList"]=array("hostelNum"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?></td>
	<td><?$itemAr["params"]["propList"]=array("interNum"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?></td>
</tr>
<tr>
	<td> Количество жилых помещений в общежитии, приспособленных для использования инвалидами и лицами с ограниченными возможностями здоровья</td>
	<td><?$itemAr["params"]["propList"]=array("hostelNumOvz"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?></td>
	<td><?$itemAr["params"]["propList"]=array("interNumOvz"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?></td>
</tr>
</table>
<br>
<b>О формировании платы за проживание в общежитии</b><br>
<?$itemAr1["params"]["propList"]=array("localActObSt"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?>

<?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>