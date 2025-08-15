<?
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//$APPLICATION->SetTitle("Материально-техническое обеспечение и оснащённость образовательного процесса");
?>
 <br>
 <?
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$addstyle="display:block;";
$z=new asmuinfo();
$itemAr=array("classname"=>"maindocs","params"=>array("onlyValue"=>1,"sectionsList"=>array(),"mainsections"=>array(395),"hideCaption"=>1));
$itemAr1=array("classname"=>"maindocs","params"=>array("sectionsList"=>array(),"mainsections"=>array(395),"hideCaption"=>1));
echo "<h2>О материально-техническом обеспечении образовательной деятельности, в том числе в отношении инвалидов и лиц с ограниченными возможностями здоровья</h2>";
$z->setСlassList(array(array("classname"=>"purposeCab","params"=>array("objTypes"=>array("purposeCab","purposePrac"),"ovz"=>0,))));
$z->setAdminGroups(array(1,8));
$z->getHtml(false,true);
$z->setСlassList(array(array("classname"=>"xobjects","params"=>array("tables"=>array("purposeLibr","purposeSport")))));
$z->getHtml(false,false);

$itemAr1["params"]["propList"]=array("purposeFacil","tech"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);
?><br>
<div>
	<span class="texticon hidedivlink link">Cобственные электронные образовательные и информационные ресурсы</span>
<div style="<?=$addstyle?>" itemprop="eoisOwn">
	<?$APPLICATION->IncludeFile($APPLICATION->GetCurDir()."eoisOwn.html",Array(),Array("MODE"=>"html"));?>
</div>
</div>

<div>
	<span class="texticon hidedivlink link">Сторонние электронные образовательные и информационные ресурсы</span>
<div style="<?=$addstyle?>" itemprop="eoisSide">
<?$APPLICATION->IncludeFile($APPLICATION->GetCurDir()."eoisSide.html",Array(),Array("MODE"=>"html"));?>
</div>
</div>
<span class="texticon hidedivlink link">Количество электронных образовательных ресурсов</span>
<div style="<?=$addstyle?>">
	<table class="simpletable" style="max-width:600px;font-size:0.9em;" >
	<tbody>
	<tr>
		<th>
			Наименование
		</th>
		<th>
			Количество
		</th>
	</tr>
	<tr>
	</tr>
	<tr>
		<td>
			 Количество собственных электронных образовательных и информационных ресурсов
		</td>
		<td>
			<?$itemAr["params"]["propList"]=array("eoisOwn"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?>
		</td>
	</tr>
	<tr>
		<td>
			Количество сторонних электронных образовательных и информационных ресурсов
		</td>
		<td>
			<?$itemAr["params"]["propList"]=array("eoisSide"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?>
		</td>
	</tr>
	<tr>
		<td>
			Количество баз данных электронного каталога
		</td>
		<td>
			<?$itemAr["params"]["propList"]=array("bdec"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?>
		</td>
	</tr>
	</tbody>
	</table>
</div>
<br>
 <?$itemAr1["params"]["propList"]=array("comNet"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?> 
<?$itemAr1["params"]["propList"]=array("comNetOvz"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?> 
<?$itemAr1["params"]["propList"]=array("purposeEios"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?> 
<?$itemAr1["params"]["propList"]=array("erList"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?>
<?$itemAr1["params"]["propList"]=array("erListOvz"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?> <br>
 <b>О количестве жилых помещений в общежитии, интернате, формировании платы за проживание в общежитии</b><br>
 <span class="texticon hidedivlink link">О количестве жилых помещений в общежитии, интернате</span>
<div style="<?=$addstyle?>">
	<table class="simpletable" style="max-width:600px;font-size:0.9em;">
	<tbody>
	<tr>
		<th>
			Наименование показателя
		</th>
		<th>
			Общежития
		</th>
		<th>
			Интернаты
		</th>
	</tr>
	<tr>
	</tr>
	<tr>
		<td>
			 Количество общежитий/интернатов
		</td>
		<td>
			<?$itemAr["params"]["propList"]=array("hostelInfo"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?>
		</td>
		<td>
			<?$itemAr["params"]["propList"]=array("interInfo"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?>
		</td>
	</tr>
	<tr>
		<td>
			 Количество мест
		</td>
		<td>
			<?$itemAr["params"]["propList"]=array("hostelNum"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?>
		</td>
		<td>
			<?$itemAr["params"]["propList"]=array("interNum"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?>
		</td>
	</tr>
	<tr>
		<td>
			 Количество жилых помещений в общежитии, приспособленных для использования инвалидами и лицами с ограниченными возможностями здоровья
		</td>
		<td>
			<?$itemAr["params"]["propList"]=array("hostelNumOvz"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?>
		</td>
		<td>
			<?$itemAr["params"]["propList"]=array("interNumOvz"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?>
		</td>
	</tr>
	</tbody>
	</table>
</div>
<br>
 <?$itemAr["params"]["propList"]=array("localActObSt"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?> <br>
<h2>О специальных условиях для получения образования инвалидами и лицами с ограниченными возможностями здоровья</h2>
 <b>Об обеспечении доступа в здания образовательной организации, в том числе в общежитие, интернат, приспособленных для использования инвалидами и лицами с ограниченными возможностями здоровья</b>
<?$itemAr1["params"]["propList"]=array("ovz"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?> <?$itemAr1["params"]["propList"]=array("hostelInterOvz"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?> <b>О наличии специальных технических средств обучения коллективного и индивидуального пользования инвалидов и лиц с ограниченными возможностями здоровья</b><br>
<?$itemAr1["params"]["propList"]=array("purposeFacilOvz"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?>
<?$itemAr1["params"]["propList"]=array("techOvz"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?><?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>