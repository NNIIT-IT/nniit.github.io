<?
$rightMenu=true;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Образование");?>
<h1>Образование</h1>
<br>
 <?
$god=2025;
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$addstyle="display:block;";
$z=new asmuinfo();
/*
$params=array(
	"hideCaption"=>true,
	"propList"=>array("licenseDocLink"),
	"mainsection"=>array(393),
	);
$z->setAdminGroups(array(8));
$z->setСlassList(array(
	array("classname"=>"maindocs","params"=>$params),
));*/
?> 
 <?$XSERVER=json_encode($_SERVER);?> <?
$tableID=htmlspecialchars($_GET["table"]);
$tables=array();
$tables1=array();
$tables1["eduAccred"]="Информация о реализуемых уровнях образования, о формах обучения, нормативных сроках обучения, сроке действия государственной аккредитации образовательной программы (при наличии государственной аккредитации)";
$tables1["eduOp"]="Информация об образовательной программе";
$tables1["eduAdOp"]="Информация об адаптированной образовательной программе";


$tables2["eduNir"]="О направлениях и результатах научной (научно-исследовательской) деятельности и научно-исследовательской базе для ее осуществления (для образовательных организаций высшего образования и образовательных организаций дополнительного профессионального образования)";
/*
$tables["study"]="Информация о численности обучающихся по
реализуемым образовательным программам за счет
бюджетных ассигнований федерального бюджета,
бюджетов субъектов Российской Федерации, местных
бюджетов и по договорам об образовании за счет средств
физических и (или) юридических лиц, в том числе
информация о численности обучающихся, являющихся
иностранными гражданами, по каждой образовательной
программе и каждой профессии, специальности, в том
числе научной, направлению подготовки или
укрупненной группе профессий, специальностей и
направлений подготовки (для профессиональных
образовательных программ)";*/
$tables["poa"]="Информация о профессионально-общественной аккредитации образовательной программы";
$tables["oaoo"]="Информация об общественной аккредитации образовательной организации";
//$tables["maoo"]="Информация о международной аккредитации образовательной организации";

//$tables["priem"]="Информация о результатах приема";
//$tables["perevod"]="Информация о результатах перевода, восстановления и отчисления";

$tables["graduateJob"]="Информация о трудоустройстве
выпускников для каждой реализуемой образовательной
программы, по которой состоялся выпуск";
//$tables["eduPr"]="Образовательные программы, наличие практики";

?>
<span class="hidedivlink linkicon link">О реализуемых образовательных программах с указанием учебных предметов, курсов, дисциплин (модулей), 
практики, предусмотренных соответствующей образовательной программой (за исключением образовательных программ дошкольного образования), 
представляемую в виде образовательной программы в форме электронного документа или в виде активных ссылок, 
непосредственный переход по которым позволяет получить доступ к страницам Сайта, 
содержащим отдельные компоненты образовательной программы
</span>
<div style="padding: 1em;">
<?
foreach ($tables1 as $k=>$name){
			$k1++;
	echo "<div class=\"tablelink tableicon black\" >";//link
			$mode=true;
	include  $_SERVER["DOCUMENT_ROOT"]."/sveden/education/".$k."/index.php";
	//echo "<a itemprop=\"addRef\" href=\"https://dentmaster.ru/sveden/education/".$k."\" >".$name."</a><br><br>";
			echo "</div>\r\n";

		}
?>
</div>
<?
foreach ($tables2 as $k=>$name){
			$k1++;
	echo "<div class=\"tablelink tableicon black\" >";//link
			$mode=true;
			include  $_SERVER["DOCUMENT_ROOT"]."/sveden/education/".$k."/index.php";
			//echo "<a itemprop=\"addRef\" href=\"https://dentmaster.ru/sveden/education/".$k."\" >".$name."</a><br><br>";
			echo "</div>\r\n";

		}
echo "<span class='hidedivlink link'>Информация о численности обучающихся по
реализуемым образовательным программам за счет
бюджетных ассигнований федерального бюджета,
бюджетов субъектов Российской Федерации, местных
бюджетов и по договорам об образовании за счет средств
физических и (или) юридических лиц, в том числе
информация о численности обучающихся, являющихся
иностранными гражданами, по каждой образовательной
программе и каждой профессии, специальности, в том
числе научной, направлению подготовки или
укрупненной группе профессий, специальностей и
направлений подготовки (для профессиональных
образовательных программ)</span>";
echo "<div style=\"".$addstyle." margin-left: 2em;\">";
$params=array("hideCaption"=>true,"propList"=>array("eduChislenEl"),	"mainsection"=>array(393),);
$z->setAdminGroups(array(8));
$z->setСlassList(array(array("classname"=>"maindocs","params"=>$params),));
$z->getHtml(false);
echo "</div><br><br>";
$params=array("hideCaption"=>true,"propList"=>array("languageEl","eduPriemEl","eduPerevodEl"),	"mainsection"=>array(393));
$z->setAdminGroups(array(8));
$z->setСlassList(array(array("classname"=>"maindocs","params"=>$params),));
$z->getHtml(false);

?><br><?
if(in_array($tableID,array_keys($tables))){
	$mode="require";
	include(__DIR__."/".$tableID.".php");
}else{
		$k1=0;
		foreach ($tables as $k=>$name){
			$k1++;
			echo "<br><br><div class=\"tablelink tableicon black\" >";//link
			$mode=true;
			include  $_SERVER["DOCUMENT_ROOT"]."/sveden/education/".$k."/index.php";

			//echo "<a itemprop=\"addRef\" href=\"https://dentmaster.ru/sveden/education/".$k."\" >".$name."</a><br><br>";
			echo "</div>\r\n";

		}
}
?>
<!-- 
<div class="tablelink link">
	<a href="https://minobrnauki.gov.ru/" target="_blank">Официальный сайт Министерства науки и высшего образования Российской Федерации</a><br><br>
</div>
<div class="tablelink link">
	<a href="https://obrnadzor.gov.ru" target="_blank">Официальный сайт Федеральной службы по надзору в сфере образования</a><br><br>
</div>
<div class="tablelink link">
	<a href="https://edu.gov.ru" target="_blank">Минпросвещения России</a><br><br>
</div>
--> <?
if(!isset($nofooter) || !$nofooter)
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>