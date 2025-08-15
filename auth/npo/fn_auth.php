<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
//$GLOBALS['APPLICATION']->RestartBuffer();
//подключаем модули
$User_ID=0; //id в таблице 10 Highloadblock
CModule::IncludeModule('highloadblock');
$hlblock_id = 10;//не перепутать!
$hlblock10 = Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id)->fetch();
$entity10 = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock10);
$entity_data_class10 = $entity10->getDataClass();
if (!function_exists(npoAuthorized)){	
	function npoAuthorized($USER_ID){
		//авторизация по сессии
		$UF_SESSION=session_id();$r=false;
		$resA = $entity_data_class10::getList(array('filter' => array("ID"=>$USER_ID), 'select' => array('ID','UF_SESSION'), 'limit'=>2,));
		if ($row = $resA->fetch()) {$r=($UF_SESSION==$row['UF_SESSION'];)}
	return $r;}
}
?>

	