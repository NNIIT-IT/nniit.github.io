<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Материально-техническое обеспечение и оснащённость образовательного процесса");
?><h1>Материально-техническое обеспечение и оснащенность образовательного процесса. Доступная среда</h1>
 <br>
 <?
require_once(__DIR__."/class/asmuinfo/asmuinfo.php");
$addstyle="display:none;";
$z=new asmuinfo();
$itemAr=array("classname"=>"maindocsA","params"=>array("onlyValue"=>1,"sectionsList"=>array(),"mainsections"=>array(395),"hideCaption"=>1));
$itemAr1=array("classname"=>"maindocsA","params"=>array("sectionsList"=>array(),"mainsections"=>array(395),"hideCaption"=>1));
$itemAr1["params"]["propList"]=array("comNet"); $z->setСlassList(array($itemAr1));$z->getHtml(false,false);?> 
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
 <?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>