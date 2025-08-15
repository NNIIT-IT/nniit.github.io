<?if(!isset($mode)) {
$rightMenu=true;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("Информация о реализуемых уровнях образования, о формах обучения, нормативных сроках обучения, сроке действия государственной аккредитации образовательной программы (при наличии государственной аккредитации)");
}
if(!isset($mode)) {
	echo "<h1>Образование</h1>";
}
echo "<h2>Информация о реализуемых уровнях образования, о формах обучения, нормативных сроках обучения, сроке действия государственной аккредитации образовательной программы (при наличии государственной аккредитации)</h2>";
require_once($_SERVER["DOCUMENT_ROOT"]."/sveden/class/asmuinfo/asmuinfo.php");
	$z=new asmuinfo();
	$z->setAdminGroups(array(8));
	$z->setСlassList(array(array("classname"=>"eduAccred","params"=>array())));
	if(!isset($mode))
	 echo $z->getHtml(false,true);
else
	 echo $z->getHtml(false,false);
if(!isset($mode)) {

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}
?>