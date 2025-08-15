<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
	$z=new asmuinfo();

$addstyle="display:block";
$params1=array("mainPropList"=>array(),
	"propList"=>array("fullName","shortName","regDate"),
	"hideCaption"=>1,
	"sectionsList"=>array(),
);
$params11=array("mainPropList"=>array(),
	"propList"=>array("address","workTime","telephone","email"),
	"hideCaption"=>1,
	"sectionsList"=>array(),
);
$params111=array("mainPropList"=>array(),
	"propList"=>array("accreditationDocLink"),
	"hideCaption"=>1,
	"sectionsList"=>array(),
);

//addressPlaceList
$addressPlaceSet=array("itemprop"=>"addressPlaceSet");
$addressPlacePrac=array("itemprop"=>"addressPlacePrac");
$addressPlacePodg=array("itemprop"=>"addressPlacePodg");
$addressPlaceGia=array("itemprop"=>"addressPlaceGia");
$addressPlaceDop=array("itemprop"=>"addressPlaceDop");
$addressPlaceOppo=array("itemprop"=>"addressPlaceOppo");

$params2=array(
	"mainPropList"=>array("filInfo","repInfo",),
	"propList"=>array("nameFil","addressFil","workTimeFil","telephoneFil","emailFil","websiteFil","nameRep","addressRep","workTimeRep","telephoneRep","emailRep","websiteRep"),
	"sectionsList"=>array(),
);
$paramsuchredLaw=array(
	"mainPropList"=>array("uchredLaw",),
	"propList"=>array(),
	"hideCaption"=>1,
	"sectionsList"=>array(),
);
$itemAr=array("classname"=>"maindocs","params"=>array("sectionsList"=>array(),"mainsections"=>array(395),"hideCaption"=>1));//"onlyValue"=>1,
$z->setAdminGroups(array(8));
$z->setСlassList(array(array("classname"=>"maindocs","params"=>$params1)));
$z->getHtml(false,true);
$z->setСlassList(array(array("classname"=>"maindocs","params"=>$paramsuchredLaw)));
?>
<span class="texticon hidedivlink linkicon link">Об учредителе, учредителях образовательной организации</span>
<div style="<?=$addstyle?>; padding:1em;" class=""><?$z->getHtml(false,true);?></div><br>
<?
$z->setAdminGroups(array(8));
$z->setСlassList(array(array("classname"=>"maindocs","params"=>$params11)));
$z->getHtml(false,true);?>
<div class="divlevels"><br>
<h3>О местах осуществления образовательной деятельности, сведения о которых в соответствии с Федеральным законом N 273-ФЗ не включаются в соответствующую запись в реестре лицензий на осуществление образовательной деятельности, перечисленных в Правилах размещения на официальном сайте образовательной организации в информационно-телекоммуникационной сети "Интернет" и обновления информации об образовательной организации, утвержденных постановлением Правительства Российской Федерации от 20 октября 2021 г. N 1802, в виде адреса места нахождения</h3>
<?$itemAr["params"]["propList"]=array("addressPlaceSet"); $z->setСlassList(array($itemAr));$z->getHtml(false,false);?>
<?
// <div class="divlevels2">
// $z->setСlassList(array(array("classname"=>"maindocs","params"=>$addressPlaceSet)));
// $z->getHtml(false,true);
// </div>
?>
<br>
<span class="texticon hidedivlink linkicon link">О местах проведения практики</span>
<div style=" <?=$addstyle?>; padding:1em;" class="">
<?
$z->setСlassList(array(array("classname"=>"addressPlaceList","params"=>$addressPlacePrac)));
$z->getHtml(false,true);
?>
</div>
<br>
<span class="texticon hidedivlink linkicon link">О местах проведения практической подготовки обучающихся</span>
<div style=" <?=$addstyle?>; padding:1em;" class="">
<?
$z->setСlassList(array(array("classname"=>"addressPlaceList","params"=>$addressPlacePodg)));
$z->getHtml(false,true);
?>
</div>
<br>
<span class="texticon hidedivlink linkicon link">О местах проведения итоговой (государственной итоговой) аттестации</span>
<div style="<?=$addstyle?>; padding:1em;" class="">
<?
$z->setСlassList(array(array("classname"=>"addressPlaceList","params"=>$addressPlaceGia)));
$z->getHtml(false,true);
?>
</div>
<br>
<span class="texticon hidedivlink linkicon link">О местах осуществления образовательной деятельности по дополнительным образовательным программам</span>
<div style=" <?=$addstyle?>; padding:1em;" class="">
<?
$z->setСlassList(array(array("classname"=>"addressPlaceList","params"=>$addressPlaceDop)));
$z->getHtml(false,true);
?>
</div>
<br>
<span class="texticon hidedivlink linkicon link">О местах осуществления образовательной деятельности по основным программам профессионального обучения</span>
<div style=" <?=$addstyle?>; padding:1em;" class="">
<?
$z->setСlassList(array(array("classname"=>"addressPlaceList","params"=>$addressPlaceOppo)));
$z->getHtml(false,true);
?>
</div></div><br><br>
<span class="texticon hidedivlink linkicon link">О лицензии на осуществление образовательной деятельности (выписке из реестра лицензий на осуществление образовательной деятельности)</span>
<div style="<?=$addstyle?>; padding:1em;" class="">
<a itemprop="licenseDocLink" title="Лицензия" class="link texticon" href="https://islod.obrnadzor.gov.ru/rlic/details/0B10110D-0C11-0F0D-0E0E-0F0A0B0B0E100F0F120A/">
Выписка из государственной информационной системы "Реестр организаций, осуществляющих образовательную деятельность по имеющим государственную аккредитацию образовательным программам".
</a>
</div><br>


<?$z->setAdminGroups(array(8));
$z->setСlassList(array(array("classname"=>"maindocs","params"=>$params111)));
$z->getHtml(false,true);?>

<?
echo '<div class="popupInfo" id="wininfo"><p></p></div>';
echo "</div>";
if(!isset($nofooter) || !$nofooter)

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>