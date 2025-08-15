<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?

//echo implode(';',$_POST);
//echo implode(';',$_GET);


?>
<?//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?function generateSalt(){$salt = '';$saltLength = 8; for($i=0; $i<$saltLength; $i++) {	$salt .= mt_rand(1,9); }return $salt;}?>
<?
//$GLOBALS['APPLICATION']->RestartBuffer();
//подключаем модули
CModule::IncludeModule("CUser");
CModule::IncludeModule("IBlock");
if (!is_object($USER)) $USER = new CUser;
CModule::IncludeModule('highloadblock');

$hlblock_id = 10;//не перепутать!
$hlblock10 = Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id)->fetch();
$entity10 = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock10);
$entity_data_class10 = $entity10->getDataClass();
$Xkeys=$entity10->getFields();
$keys=array_keys($Xkeys);
?>

<?
$PUserRec=array();
$ULOGIN="";
$MASHINE_COOKIE=$_COOKIE['key'];
$BUserRec=array();
$USER_ID=intval(htmlspecialchars($_POST['USER_ID']));
if ($USER_ID>0):
	$resA = $entity_data_class10::getList(array('filter' => array('ID'=>$USER_ID), 'select' => array('*'), 'limit'=>2,));
	if ($row = $resA->fetch()){
		foreach($keys as $keyname){
			 if ($keyname!='ID') $BUserRec[$keyname]=$row[$keyname];
		}
			
	}	
$ULOGIN=$BUserRec['UF_LOGIN'];
$UF_COOKIE=$BUserRec['UF_COOKIE'];
else:
$UF_COOKIE=0;
endif;
if ((($MASHINE_COOKIE==$UF_COOKIE)&&($UF_COOKIE>0))||(($USER->GetLogin())==$ULOGIN)):
$postarr=array();
//пользователь активен. чтение пост запроса

foreach ($_POST as $postname=>$postvalue)
{
$postvalue=htmlspecialchars($postvalue);
if (strpos($postname,'UF_PASPORT')!==false) $postarr['UF_PASPORT'][]=$postvalue;
if (strpos($postname,'UF_ADDR_FACT')!==false) $postarr['UF_ADDR_FACT'][]=$postvalue;
if (strpos($postname,'UF_ADDR_REG')!==false) $postarr['UF_ADDR_REG'][]=$postvalue;
if (strpos($postname,'UF_DIPLOM')!==false) $postarr['UF_DIPLOM'][]=$postvalue;
if (strpos($postname,'UF_INTERNATURA')!==false) $postarr['UF_INTERNATURA'][]=$postvalue;
if (strpos($postname,'UF_ORDINATURA')!==false) $postarr['UF_ORDINATURA'][]=$postvalue;
if (strpos($postname,'UF_UF_LAST_SPEC')!==false) $postarr['UF_UF_LAST_SPEC'][]=$postvalue;
if (strpos($postname,'UF_CATEGORY')!==false) $postarr['UF_CATEGORY'][]=$postvalue;
}
foreach ($postarr as $kkk=>$vvv){
	$_POST[$kkk]=implode(';',$vvv);
}
foreach ($_POST as $postname=>$postvalue)
{
	if ((in_array($postname, $keys))&&($postname!='USER_ID'))
	$BUserRec[$postname]=trim(htmlspecialchars($postvalue));
}
//echo explode(';',$BUserRec);

if ($_POST['RESETPWD']!="")//смена пароля
{
	$BUserRec['UF_PASSWORD']=substr(generateSalt(),0,6);
	$title="Смена кода входа на сайте Алтайского государственного медицинского университета";
	$mess ="Уважаемый(а) ".$BUserRec['UF_SECONDNAME']." ".$BUserRec['UF_NAME']."\n";
	$mess.="Вы получили это сообщение, так как ваш адрес был указан в личном кабинете слушателя на сервере asmu.ru\n";
	$mess.="Ваш ключ доступа в кабинет слушателя:".$BUserRec['UF_PASSWORD']."\n";
	$mess.='Сообщение создано автоматически.';
	$to = $BUserRec['UF_EMAIL'];
	$mymail='no-reply@asmu.ru';
	$from = "From: =?utf-8?B?". base64_encode("asmu.ru"). "?= < $mymail >\n";
        $from .= "X-Sender: < $mymail >\n";
	$from .= "Content-Type: text/plain; charset=utf-8\n";
	if (mail($to, $title, $mess,$from)) echo $BUserRec['UF_PASSWORD'];
}
$entity_data_class10::update($USER_ID,$BUserRec);
else:
//echo "error";
//cookie просрочен или не действителен отправляем пользователя на авторизацию
	$e_html='<form action="/auth/npo/auth_npo.php" method="POST" name="LK_FORM_A">';
	$e_html.='<input type="submit" hidden value="  ">';
	$e_html.='<input hidden name="PODPISKA_TYPE" value="0">';
	$e_html.='<input hidden name="PODPISKA_ID" value="0">';
	$e_html.='<input hidden name="B_URL" value="/persons/npo/lk/index.php">';
	$e_html.='<input hidden name="ERROR" value="Для входа в личный кабинет необходима авторизация">';
	$e_html.='</form>';
	$e_html.='<script>document.forms["LK_FORM_A"].submit();</script>';
	//echo 	$e_html;

endif;
?>

