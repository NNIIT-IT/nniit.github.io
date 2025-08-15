<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?function generateSalt(){$salt = '';$saltLength = 8; for($i=0; $i<$saltLength; $i++) {	$salt .= mt_rand(1,9); }return $salt;}?>

<?
//$GLOBALS['APPLICATION']->RestartBuffer();
//подключаем модули
CModule::IncludeModule("CUser");
CModule::IncludeModule("IBlock");
if (!is_object($USER)) $USER = new CUser;
$User_ID=0; //id в таблице 10 Highloadblock
CModule::IncludeModule('highloadblock');

$hlblock_id = 10;//не перепутать!
$hlblock10 = Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id)->fetch();
$entity10 = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock10);
$entity_data_class10 = $entity10->getDataClass();
$Xkeys=$entity10->getFields();
$keys=array_keys($Xkeys);
?>

<style>
	.dualformbtn{
	padding: 0;
	margin: 0; 
	display: inline-block;
	width: 45%;
	text-align: center;
	}
	.reg_message{
	font-size: 2em;
	display: inline-block;
	width: 100%;
	text-align: center;
	color: darkgreen;
	font-weight: 600;
	}
	.overlay_reg{
	    background-color: rgba(101, 5, 5,0.8);
	    bottom: 0;
	    cursor: default;
	    left: 0;
	    opacity: 1;
	    position: fixed;
	    right: 0;
	    top: 0;
	    visibility: hidden;
	    z-index: 99999;
			-webkit-transition: opacity .5s;
			-moz-transition: opacity .5s;
			-ms-transition: opacity .5s;
			-o-transition: opacity .5s;
			transition: opacity .5s;
	}
	.popup_reg {
		background-color: #fff;
		border: 3px solid #fff;
		display: inline-block;
		left: 50%;
		opacity: 1;
		padding: 15px;
		width: 450px;
		height: 450px;
		position: fixed;
		text-align: justify;
		top: 40%;
		visibility: show;
		z-index: 999999;
		-webkit-transform: translate(-50%, -50%);
		-moz-transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
		-o-transform: translate(-50%, -50%);
		transform: translate(-50%, -50%);
		-webkit-transition: opacity .5s, top .5s;
		-moz-transition: opacity .5s, top .5s;
		-ms-transition: opacity .5s, top .5s;
		-o-transition: opacity .5s, top .5s;
		transition: opacity .5s, top .5s;
		border-radius: 11px;
	}
	.popup_reg .close_window_reg{
		width: 10px;height: 20px;
		position: absolute;
		padding: 1px 9px 4px 9px;
		top: -15px;
		right: -15px;
		cursor: pointer;
		color: #fff;
		font-family: 'tahoma', sans-serif;
		background: -webkit-gradient(linear, left top, right top, from(#3d51c8), to(#051fb8));
		background: -webkit-linear-gradient(top, #3d51c8, #051fb8);
		background: -moz-linear-gradient(top, #ae0f31, #f59a9a);
		background: -o-linear-gradient(top, #ae0f31, #f59a9a);
		background: -ms-linear-gradient(top, #ae0f31, #f59a9a);
		background: linear-gradient(top, #ae0f31, #f59a9a);

		background-color: #3d51c8;
		border: 1px solid #b80606;
		-webkit-border-radius: 50%;
		-moz-border-radius: 50%;
		-o-border-radius: 50%;
		-ms-border-radius: 50%;
		border-radius: 50%;
		text-align: center;
		box-shadow: -1px 1px 3px rgba(0, 0, 0, 0.5);
	}
	.popup_reg .close_window_reg:hover {
		background: -webkit-gradient(linear, left top, right top, from(#3d51c8), to(#051fb8));
		background: -webkit-linear-gradient(top, #3d51c8, #051fb8);
		background: -moz-linear-gradient(top, #ae0f31, #f59a9a);
		background: -o-linear-gradient(top, #ae0f31, #f59a9a);
		background: -ms-linear-gradient(top, #ae0f31, #f59a9a);
		background: linear-gradient(top, #ae0f31, #f59a9a);
		background-color: #3d51c8;
		border: 2px solid #b80606;
	}
	.popup_reg .close_window_reg:active {
		background: #8f9be0;
	}
	.popup_reg input{width:50%;}
	.input_title{width:150px;display:inline-block}
.accordiona
	{
	    display: inline-block;
	    background-color: lightgrey;
	    width: 100%;
	    padding: 5px;
	    text-align: center;
	    border: gray thin solid;
	    border-radius: 5px;
		text-decoration: unset;
		color: brown;
		font-size: 23px;
		cursor:pointer;
	}
.popup_reg form {
	padding: 10px;
 }
.submit{
	padding: .3em;
	margin-top: 1em;
	margin-left: 100px;
	}
.submit2{
	padding: .3em;
	margin-top: 1em;
	margin-left: unset;
	font-size: 1.5em;
	}


</style>
<?
if ($_SERVER['REQUEST_METHOD']=='POST'):
	$USER_ID=0;
	$ERROR="";
	$TYPEAUT=0;
	/*для почтовой регистрации параметры передаются через GET*/
	
	if (isset($_GET['TYPEAUT'])&&($TYPEAUT=="")) $TYPEAUT=intval(htmlspecialchars($_GET['TYPEAUT']));
	if (isset($_GET['B_URL'])&&($B_URL=="")) $B_URL=htmlspecialchars($_GET['B_URL']);
	if (isset($_GET['PHP_URL'])&&($PHP_URL=="")) $PHP_URL=htmlspecialchars($_GET['PHP_URL']);
	if (isset($_GET['PODPISKA_TYPE'])&&($PODPISKA_TYPE=="")) $PODPISKA_TYPE=intval(htmlspecialchars($_GET['PODPISKA_TYPE']));
	if (isset($_GET['PODPISKA_ID'])&&($PODPISKA_ID=="")) $PODPISKA_ID=intval(htmlspecialchars($_GET['PODPISKA_ID']));
	
	if (isset($_POST['B_URL'])) $B_URL=htmlspecialchars($_POST['B_URL']);
	if (isset($_POST['PHP_URL'])) $PHP_URL=htmlspecialchars($_POST['PHP_URL']);
	if (isset($_POST['PODPISKA_TYPE'])) $PODPISKA_TYPE=intval(htmlspecialchars($_POST['PODPISKA_TYPE']));
	if (isset($_POST['PODPISKA_ID'])) $PODPISKA_ID=intval(htmlspecialchars($_POST['PODPISKA_ID']));
	if (isset($_POST['USER_ID'])) $USER_ID=htmlspecialchars($_POST['USER_ID']);
	if (isset($_POST['UF_LOGIN'])) $UF_LOGIN=htmlspecialchars($_POST['UF_LOGIN']);
	if (isset($_POST['UF_PASSWORD'])) $PasswordBitrix=htmlspecialchars($_POST['UF_PASSWORD']);
	if (isset($_POST['UF_EMAIL'])) $UF_EMAIL=htmlspecialchars($_POST['UF_EMAIL']);
	if (isset($_POST['UF_NAME'])) $UF_NAME=htmlspecialchars($_POST['UF_NAME']);
	if (isset($_POST['UF_SECONDNAME'])) $UF_SECONDNAME=htmlspecialchars($_POST['UF_SECONDNAME']);
	if (isset($_POST['UF_PATRONYMIC'])) $UF_PATRONYMIC=htmlspecialchars($_POST['UF_PATRONYMIC']);
	if (isset($_POST['UF_TEL'])) $UF_TEL=htmlspecialchars($_POST['UF_TEL']);
	if (isset($_POST['TYPEAUT'])) $TYPEAUT=intval(htmlspecialchars($_POST['TYPEAUT']));
	if (isset($_POST['UF_COOKIE'])) $UF_COOKIE=intval(htmlspecialchars($_POST['UF_COOKIE']));
	if (isset($_POST['ERROR'])) $ERROR=htmlspecialchars($_POST['ERROR']);
	$MESSAGE=$ERROR;	
	//$MESSAGE=implode('|',$_POST);
	if (($TYPEAUT==999)&&($B_URL!="")){
		$location="Location: ".$B_URL;
		Header($location); 
		exit;
		$ERROR="";
	}
	if ($PODPISKA_TYPE==9999)// logout
	{
		setcookie("key", ""); //удаляем куки	
		if ($USER->IsAuthorized()) $USER->Logout();
		$location="Location: /";
		Header($location); 
		exit; 
	?><script>location.replace("http://asmu.ru/");</script><?
	}

	if ($USER_ID==0)://первый запуск php
	$ERROR="";	
	/*=====================bitrix авторизация ====================*/	
		/*авторизация посредством учетной записи битрикса*/

		/*	if (!$USER->IsAuthorized())//если пользователь не авторизировался
			{
				//пробуем авторизовать используя куки
				$cookie_login = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};
				$cookie_md5pass = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_UIDH"};
				$USER->LoginByHash($cookie_login, $cookie_md5pass);
			}
		*/
		/*
		//авторизация по сессии
		$UF_SESSION=session_id();
		$resA = $entity_data_class10::getList(array('filter' => array("UF_SESSION"=>$UF_SESSION), 'select' => array('ID','UF_SESSION'), 'limit'=>2,));
						if ($row = $resA->fetch()) {
							$USER_ID=$row['ID'];
							}
	
		*/
		if (!function_exists(userAuthorized)):	
			function userAuthorized(){
				$xUSER_ID=0;
				
				GLOBAL $USER;
				GLOBAL $entity_data_class10;
				GLOBAL $keys;
				GLOBAL $ERROR;
				GLOBAL $UF_COOKIE;
				if ($USER->IsAuthorized())//если пользователь авторизировался
				{
				
					//получаем данные авторизации
					$xUF_LOGIN=trim($USER->GetLogin());//login
					$xUF_NAME=$USER->GetParam('FIRST_NAME');//имя
					$xUF_SECONDNAME=$USER->GetParam('LAST_NAME');//фамилия
					$xUF_PATRONYMIC=$USER->GetParam('SECOND_NAME');//отчество
					$xUF_EMAIL=$USER->GetEmail();//email
					$UxF_TEL="";
					
					//ищем в таблице пользователей-заявителей услуг
					
					if (($xUF_LOGIN!="")){//поиск по логин
			
						unset($FValsOld);
						$xUSER_ID=0;
						
						$resA = $entity_data_class10::getList(array('filter' => array("UF_LOGIN"=>$xUF_LOGIN), 'select' => array('*'), 'limit'=>2,));
						if ($row = $resA->fetch()) {
							$xUSER_ID=$row['ID'];
							foreach($keys as $keyname) {
								if ($keyname!='ID') $FValsOld[$keyname]=$row[$keyname];
							}
						}
						
						if ($xUSER_ID>0){
							$UF_COOKIE=generateSalt();
							setcookie('key', $UF_COOKIE, time()+60*60*24*3); //обновляем куки
							$FValsOld['UF_COOKIE']=$UF_COOKIE;
							$FValsOld['UF_SESSION']=session_id();
							$entity_data_class10::update($xUSER_ID,$FValsOld);
							
						} else $ERROR="логин не найден";	
					
					}
					
					if ($xUSER_ID==0)//нет в таблице - создадим :)
					{
						$UF_COOKIE=generateSalt();
						$UF_PASSWORD=substr(generateSalt(),0,6);
						$FVals=array();
						$FVals['UF_PATRONYMIC']=$xUF_PATRONYMIC;
						$FVals['UF_SECONDNAME']=$xUF_SECONDNAME;
						$FVals['UF_TEL']=$xUF_TEL;
						$FVals['UF_EMAIL']=$xUF_EMAIL;
						$FVals['UF_NAME']=$xUF_NAME;
						$FVals['UF_LOGIN']=$xUF_LOGIN;
						$FVals['UF_COOKIE']=$UF_COOKIE;
						$FVals['UF_PASSWORD']=$UF_PASSWORD;
						$FVals['UF_LASTFORM']=1;
						$FVals['UF_SESSION']=session_id();
						setcookie('key', $UF_COOKIE, time()+60*60*24*3); //обновляем куки		
						/*setcookie('login', $UF_PASSWORD, time()+60*60*24*3); //обновляем куки	*/
						$xx=$entity_data_class10::add($FVals);
						$xUSER_ID=$xx->getId();
						
					}
					
				}
			
			return 	$xUSER_ID;
			}
		endif;

		if (!function_exists(checkphone)):	
			function checkphone($phone) {
			        return preg_match("/^\+?([87](?!95[4-79]|99[^2457]|907|94[^0]|336)([348]\d|9[0-689]|7[027])\d{8}|[1246]\d{9,13}|68\d{7}|5[1-46-9]\d{8,12}|55[1-9]\d{9}|500[56]\d{4}|5016\d{6}|5068\d{7}|502[45]\d{7}|5037\d{7}|50[457]\d{8}|50855\d{4}|509[34]\d{7}|376\d{6}|855\d{8}|856\d{10}|85[0-4789]\d{8,10}|8[68]\d{10,11}|8[14]\d{10}|82\d{9,10}|852\d{8}|90\d{10}|96(0[79]|17[01]|13)\d{6}|96[23]\d{9}|964\d{10}|96(5[69]|89)\d{7}|96(65|77)\d{8}|92[023]\d{9}|91[1879]\d{9}|9[34]7\d{8}|959\d{7}|989\d{9}|97\d{8,12}|99[^456]\d{7,11}|994\d{9}|9955\d{8}|996[57]\d{8}|380[34569]\d{8}|381\d{9}|385\d{8,9}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}|37[6-9]\d{7,11}|30[69]\d{9}|34[67]\d{8}|3[12359]\d{8,12}|36\d{9}|38[1679]\d{8}|382\d{8,9})$/", preg_replace("/[^0-9]/i","", $phone));
			}	
		endif;		

		/* пробуем авторизироваться */	
		if ($USER_ID==0) $USER_ID=userAuthorized();	

		if ($USER_ID==0)//не смогли авторизовать через куки битрикса попробуем через куки пользователя 
		{
			unset($FValsOld);
			if (!empty($_COOKIE['key']) ) {
				//Пишем логин и ключ из КУК в переменные (для удобства работы):
				$key = $_COOKIE['key']; //ключ из кук (аналог пароля, в базе поле password)
				if ($key!=""){
					$resA = $entity_data_class10::getList(array('filter' => array('UF_COOKIE'=>$key), 'select' => array('*'), 'limit'=>2,));
					if ($row = $resA->fetch()) {foreach($keys as $keyname) $FValsOld[$keyname]=$row[$keyname];}
				}
			}
			if (isset($FValsOld)){
				$UF_COOKIE=generateSalt();
				$FValsOld['UF_COOKIE']=$UF_COOKIE;
				$FValsOld['UF_SESSION']=session_id();
				$entity_data_class10::update($FValsOld['ID'],$FValsOld);
				$USER_ID=$FValsOld['ID'];
				setcookie('key', $UF_COOKIE, time()+60*60*24*3); //обновляем куки	
				/*setcookie('login', $FValsOld['UF_PASSWORD'], time()+60*60*24*3); //обновляем куки	*/
			}
		}

	endif;//первый запуск php
	
	



		//$USER_ID=0;

	/*==================== анализ данных формы =========================*/
	if (($USER_ID==0)&&($TYPEAUT!=0)):
		/*=========form 1===============*/
		if ($TYPEAUT==1){
			if ($UF_PASSWORD=="") $ERROR="Не указан пароль";
			if ($UF_LOGIN=="") $ERROR="Не указан логин";		
	
			if (($UF_LOGIN!="")&&($UF_PASSWORD!=""))//авторизируем через битрикс
				{
					$USER_ID=0;
					$res=$USER->Login($UF_LOGIN, $UF_PASSWORD, "N", "Y");
					if ($res===true) { 
						$USER_ID=userAuthorized();	
					}
					else {$ERROR='Ошибка '.$res['MESSAGE'];}
				}
		}
		/*=========form 2===============*/
		if ($TYPEAUT==2){
			$ERROR="";
			if ($UF_EMAIL=="")  $ERROR=" Не заполнено поле Email 2";
			
				elseif (!filter_var($UF_EMAIL, FILTER_VALIDATE_EMAIL)) {
				$ERROR.=" Не верно введён Email";
				}

			if ($UF_PASSWORD=="") $ERROR.=" Введите ключ доступа";

			if ($ERROR==""){//вход через пару пароль/email
				$cookie_ok=false;
				$resA = $entity_data_class10::getList(
							array(
							'filter' => array('UF_PASSWORD'=>$UF_PASSWORD,"UF_EMAIL"=>$UF_EMAIL), 
							'select' => array('*')
							));
			
				if ($row = $resA->fetch()) {
					foreach($keys as $keyname) $FValsOld[$keyname]=$row[$keyname];
					}
				$cookie_ok=($FValsOld['UF_PASSWORD']==$UF_PASSWORD)&&($FValsOld['UF_EMAIL']==$UF_EMAIL);
	
				if ($cookie_ok){
					//Пишем куки (имя куки, значение, время жизни - сейчас+3 дня)
					$UF_COOKIE=generateSalt();
					setcookie('key', $UF_COOKIE, time()+60*60*24*3,'/auth/npo/'); //обновляем куки
					setcookie('key', $UF_COOKIE, time()+60*60*24*3); //обновляем куки
					$FValsOld['UF_COOKIE']=$UF_COOKIE;
					$FValsOld['UF_SESSION']=session_id();
					$entity_data_class10::update($FValsOld['ID'],$FValsOld);
					$USER_ID=$FValsOld['ID'];
				}else $ERROR='Введенный вами ключ не найден!';	
			
			}
		}

		/*=========form 3===============*/
		if ($TYPEAUT==3){
		$ERROR="";
			if ($UF_NAME=="")  $ERROR="Не заполнено поле ВАШЕ ИМЯ <br>";
				if ($UF_SECONDNAME=="")  $ERROR.=" Не заполнено поле ВАША ФАМИЛИЯ<br>";
				if ($UF_EMAIL=="")  $ERROR.=" Не заполнено поле ВАШ EMAIL<br>";
			
				elseif (!filter_var($UF_EMAIL, FILTER_VALIDATE_EMAIL)) {
				$ERROR.=" Не верно введён Email";
				}
				if (!checkphone($UF_TEL)){
				$ERROR.=" Не верно введён Телефон<br>";
				}
				//может пльзователь уже зарегистрирован
				if ($ERROR==""){
					$cookie_ok=false;
					$resA = $entity_data_class10::getList(
								array(
								'filter' => array("UF_EMAIL"=>$UF_EMAIL), 
								'select' => array('*')
								));
				
					if ($row = $resA->fetch()) {
						foreach($keys as $keyname) $FValsOld[$keyname]=$row[$keyname];
						}
					$cookie_ok=($FValsOld['UF_EMAIL']==$UF_EMAIL);
		
					if ($cookie_ok){
						$ERROR="";
						$TYPEAUT=2;
						$title="Восстановление регистрации на сайте Алтайского государственного медицинского университета";
						$mess ="Уважаемый(а) ".$FValsOld['UF_SECONDNAME']." ".$FValsOld['UF_NAME']."\n";
						$mess.="Вы получили это сообщение, так как ваш адрес был использован при попытке создания нового пользователя на сервере asmu.ru\n";
						$mess.="Ваш ключ доступа в кабинет Слушателя:".$FValsOld['UF_PASSWORD']."\n";
						$mess.='Сообщение создано автоматически.';
						$to = $FValsOld['UF_EMAIL'];
						$mymail='no-reply@asmu.ru';
        					$from = "From: =?utf-8?B?". base64_encode("asmu.ru"). "?= < $mymail >\n";
					        $from .= "X-Sender: < $mymail >\n";
						$from .= "Content-Type: text/plain; charset=utf-8\n";
						if (mail($to, $title, $mess,$from)){$ERROR="Пользователь уже был зарегистрирован. Ключ доступа выслан на EMAIL, указанный при регистрации";}
						else {$ERROR="Мы не смогли отправить Вам письмо с ключем доступа, попробуйте позднее ";}
					} 
				}	
				if ($ERROR==""){//запись в базу данных
				
						$UF_COOKIE=generateSalt();
						$UF_PASSWORD=substr(generateSalt(),0,6);
			
						$FVals['UF_PATRONYMIC']=$UF_PATRONYMIC;
						$FVals['UF_SECONDNAME']=$UF_SECONDNAME;
						$FVals['UF_TEL']=$UF_TEL;
						$FVals['UF_EMAIL']=$UF_EMAIL;
						$FVals['UF_NAME']=$UF_NAME;
						$FVals['UF_COOKIE']=$UF_COOKIE;
						$FVals['UF_PASSWORD']=$UF_PASSWORD;
						$FVals['UF_LASTFORM']=1;
						$FVals['UF_SESSION']=session_id();
						$xx=$entity_data_class10::add($FVals);

						$ERROR='На вашу почту отправлен ключ доступа';
						
						$TYPEAUT=2;
						setcookie('key', $UF_COOKIE, time()+60*60*24*3); //обновляем куки		
						setcookie('login', $UF_PASSWORD, time()+60*60*24*3); //обновляем куки	
						$xx=$entity_data_class10::add($FVals);
						$USER_ID=$xx->getId();
						$title="Регистрация на сайте Алтайского государственного медицинского университета";
						$mess ="Уважаемый(а) ".$UF_SECONDNAME." ".$UF_NAME."\n";
						$mess.="Вы получили это сообщение, так как ваш адрес был использован при регистрации нового пользователя на сервере asmu.ru\n";
						$mess.="Ваш ключ доступа в кабинет Слушателя:".$UF_PASSWORD."\n";
						$mess.='Сообщение создано автоматически.';
						$to = $UF_EMAIL;
						$mymail='no-reply@asmu.ru';
        					$from = "From: =?utf-8?B?". base64_encode("asmu.ru"). "?= < $mymail >\n";
					        $from .= "X-Sender: < $mymail >\n";
						$from .= "Content-Type: text/plain; charset=utf-8\n";
						if (mail($to, $title, $mess,$from)){$ERROR="Вам отправлено письмо с ключем доступа";}
						else {$ERROR="Мы не смогли отправить Вам письмо с ключем доступа";}
					}
		}//form 3
	endif;

	/*==================== пользователь определен вызываем скрипт работы с данными =========*/
?>
				<form action="http://asmu.ru/personal/lk/index.php" name="LK_FORM" method="POST">
			  		<input type="hidden" name="B_URL" value="<?=$B_URL?>">
					<input type="hidden" name="PHP_URL" value="<?=$PHP_URL?>">
					<input type="hidden" name="PODPISKA_TYPE" value="<?=$PODPISKA_TYPE?>">
					<input type="hidden" name="PODPISKA_ID" value="<?=$PODPISKA_ID?>">
					<input type="hidden" name="ERROR" value="<?=$ERROR?>">
					<input type="hidden" name="UF_LOGIN" value="<?=$UF_LOGIN?>">
					<input type="hidden" name="UF_COOKIE" value="<?=$UF_COOKIE?>">
					<input type="hidden" name="USER_ID" value="<?=$USER_ID?>">
					
				</form>

<?
	  if (($ERROR=="")&&($USER_ID>0)): 
		//записываем session_id в базу

		//	
		if ($PHP_URL!=""):
		?>
		<form action="http://asmu.ru<?=$PHP_URL?>" name="PHP_URL_FORM" method="POST">
	  		<input type="hidden" name="B_URL" value="<?=$B_URL?>">
			<input type="hidden" name="PHP_URL" value="<?=$PHP_URL?>">
			<input type="hidden" name="PODPISKA_TYPE" value="<?=$PODPISKA_TYPE?>">
			<input type="hidden" name="PODPISKA_ID" value="<?=$PODPISKA_ID?>">
			<input type="hidden" name="ERROR" value="<?=$ERROR?>">;
			<input type="hidden" name="UF_LOGIN" value="<?=$UF_LOGIN?>">
			<input type="hidden" name="USER_ID" value="<?=$USER_ID?>">
			<input type="hidden" name="UF_COOKIE" value="<?=$UF_COOKIE?>">

			<input type="submit" hidden value="">
		</form>
		<script>
		  document.forms["PHP_URL_FORM"].submit();
		</script>

		<?
		else:
			if ($PODPISKA_TYPE==0)://личный кабинет
				?><script>document.forms["LK_FORM"].submit();</script><?
			elseif (($PODPISKA_TYPE==1)&&($TYPEAUT!=1000))://вебинар
				$hlblock_id12 = 12;//не перепутать!
				$hlblock12 = Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id12)->fetch();
				$entity12 = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock12);
				$entity_data_class12 = $entity12->getDataClass();
				$Xkeys12=$entity12->getFields();
				$keys12=array_keys($Xkeys12);
				$ERROR=$TYPEAUT;
				//добавление подписки
				$resV=CIBlockElement::GetByID($PODPISKA_ID);
				if ($resV!==false){
					if($ar_res = $resV->GetNext())
					$resV1=$ar_res['IBLOCK_ID']==151;
				} else $resV1=false;
				if ($resV1!==false){
					$REC12=array('UF_VEBINAR'=>$PODPISKA_ID,'UF_USER_ID'=>$USER_ID);
					//проверка есть ли подписка
					$resA = $entity_data_class12::getList(array('filter' => $REC12, 'select' => array('*'), 'limit'=>1,));
					$c=0;
					if ($row = $resA->fetch()) {$c=1;}
					if ($c==0) $xx=$entity_data_class12::add($REC12);
					$ERROR=	'Ваша заявка на вебинар принита ';
				} else 		$ERROR=	'Вебинар не существует';
				$TYPEAUT=999;
			elseif ($PODPISKA_TYPE==1001)://вебинар
				$hlblock_id12 = 12;//не перепутать!
				$hlblock12 = Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id12)->fetch();
				$entity12 = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock12);
				$entity_data_class12 = $entity12->getDataClass();
				$Xkeys12=$entity12->getFields();
				$keys12=array_keys($Xkeys12);
				$res = $entity_data_class12::getList(array('filter' => array('UF_USER_ID'=>$USER_ID,'UF_VEBINAR'=>$PODPISKA_ID), 'select' => array('ID'), 'limit' => 0,'order' => array(),));
				while ($row = $res->fetch()) {	$entity_data_class12::delete($row['ID']);}
			
					?><script>document.forms["LK_FORM"].submit();</script><?
			elseif ($PODPISKA_TYPE==2)://запись на курсы
				$hlblock_id11 = 11;//не перепутать!
				$hlblock11 = Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id11)->fetch();
				$entity11 = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock11);
				$entity_data_class12 = $entity12->getDataClass();
				$Xkeys11=$entity11->getFields();
				$keys11=array_keys($Xkeys11);
				$resV=CIBlockElement::GetByID($PODPISKA_ID);
				if ($resV!==false){
					if($ar_res = $resV->GetNext())
					$resV1=$ar_res['IBLOCK_ID']==147;
				} else $resV1=false;
				if ($resV1!==false){
					$REC11=array('UF_OPP'=>$PODPISKA_ID,'UF_USER_NPO'=>$USER_ID);
					//проверка есть ли подписка
					$resA = $entity_data_class11::getList(array('filter' => $REC11, 'select' => array('*'), 'limit'=>1,));
					$c=0;
					if ($row = $resA->fetch()) {$c=1;}
					if ($c==0) $xx=$entity_data_class11::add($REC11);
					$ERROR=	'Ваша заявка принята, <br> не забудьте проверить данные анкеты в личном кабинете Слушателя. ';
				}else $ERROR=	'Курс не существует';
				$TYPEAUT=999;
			elseif ($PODPISKA_TYPE==1002)://вебинар
				$hlblock_id12 = 12;//не перепутать!
				$hlblock12 = Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id12)->fetch();
				$entity12 = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock12);
				$entity_data_class12 = $entity12->getDataClass();
				$Xkeys12=$entity12->getFields();
				$keys12=array_keys($Xkeys12);
				$res = $entity_data_class12::getList(array('filter' => array('UF_USER_ID'=>$USER_ID,'UF_VEBINAR'=>$PODPISKA_ID), 'select' => array('ID'), 'limit' => 0,'order' => array(),));
				while ($row = $res->fetch()){ $entity_data_class12::delete($row['ID']);}
				?><script>document.forms["LK_FORM"].submit();</script><?
			endif;//$PODPISKA_TYPE
		endif;//if ($PHP_URL!="")
	endif;//if (($ERROR=="")&&($USER_ID>0))
	
endif;//$_SERVER['REQUEST_METHOD']=='POST'


	/*==================== вывод формы =========================*/
?>
		
			<div class="overlay_reg" title="окно"></div>
			<div class="popup_reg" style="height:auto">
			<div class="close_window_reg" onclick="$('.popup_reg, .overlay_reg').css({'opacity': 0, 'visibility': 'hidden'});">x</div>
			<div <?=($TYPEAUT>=999)?'style="display:none;"':''?>>			
			<div style="color:red;"><?print_r($ERROR);?></div><br>
			<p class="reg_message"><?=$MESSAGE?></p>
			<div class="accordion">
			<div class="accord-header accordiona">Я зарегистрирован в сети АГМУ</div>
			<!--a class="hidedivlink" href="javascript:void(); ">Я зарегистрирован в сети АГМУ</a-->
			<!--div <?=($TYPEAUT==1)?'':'style="display:none;"'?>><br-->
			<div class="accord-content" <?=($TYPEAUT==1)?'':'style="display:none;"'?>>

				<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
					
					<span class="input_title">Логин  </span><input type="text" name="UF_LOGIN" value="<?=$UF_LOGIN?>"><br>
					<span class="input_title">Пароль  </span><input name="UF_PASSWORD" type="password" value="<?=$UF_PASSWORD?>"><br>
					<input class="submit" type="submit" value="ОК" onclick="return=false;"><br>
					<input type="hidden" name="TYPEAUT" value="1">
					<input type="hidden" name="B_URL" value="<?=$B_URL?>">
					<input type="hidden" name="PHP_URL" value="<?=$PHP_URL?>">
					<input type="hidden" name="PODPISKA_TYPE" value="<?=$PODPISKA_TYPE?>">
					<input type="hidden" name="PODPISKA_ID" value="<?=$PODPISKA_ID?>">
					<input type="hidden" name="ERROR" value="<?=$ERROR?>">


				</form>
			</div><br>
			<div class="accord-header accordiona">Я имею ключ доступа к кабинету Слушателя</div>
			<!--a class="hidedivlink" href="javascript:void(); ">Я имею ключ доступа к кабинету Слушателя</a-->

			<!--div <?=($TYPEAUT==2)?'':'style="display:none;"'?>-->

			<div class="accord-content" <?=($TYPEAUT==2)?'':'style="display:none;"'?>>
				<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
					<span class="input_title">Ваш Email  </span><input type="text" name="UF_EMAIL" value="<?=$UF_EMAIL?>"><br>
					<span class="input_title">Ваш ключ  </span><input name="UF_PASSWORD" type="text" value="<?=$UF_PASSWORD?>"><br>
					<input class="submit" type="submit" value="ОК"><br>
					<div style="height:0">
					<input type="hidden" name="TYPEAUT" value="2">
					<input type="hidden" name="B_URL" value="<?=$B_URL?>">
					<input type="hidden" name="PHP_URL" value="<?=$PHP_URL?>">
					<input type="hidden" name="PODPISKA_TYPE" value="<?=$PODPISKA_TYPE?>">
					<input type="hidden" name="PODPISKA_ID" value="<?=$PODPISKA_ID?>">
					<input type="hidden" name="ERROR" value="<?=$ERROR?>">
					<input type="hidden" name="UF_NAME" value="<?=$UF_NAME?>"><br>
					<input type="hidden" name="UF_SECONDNAME" value="<?=$UF_SECONDNAME?>"><br>
					<input type="hidden" name="UF_PATRONYMIC" value="<?=$UF_PATRONYMIC?>"><br>
					<input type="hidden" name="UF_TEL" value="<?=$UF_TEL?>"><br>
					</div>
			
			</form>
			</div>	<br>
			<div class="accord-header accordiona">Я первый раз на сайте</div>
			<!--a class="hidedivlink" href="javascript:void(); ">Я первый раз на сайте</a-->
			<!--div <?=($TYPEAUT==3)?'':'style="display:none;"'?>-->

				<div class="accord-content" <?=($TYPEAUT==3)?'':'style="display:none;"'?>>
				<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
					<span class="input_title">Ваше имя </span><input type="text" name="UF_NAME" value="<?=$UF_NAME?>"><br>
					<span class="input_title">Ваша фамилия </span><input type="text" name="UF_SECONDNAME" value="<?=$UF_SECONDNAME?>"><br>
					<span class="input_title">Ваше отчество </span><input type="text" name="UF_PATRONYMIC" value="<?=$UF_PATRONYMIC?>"><br>
					<span class="input_title">Ваш E-mail </span><input type="text" name="UF_EMAIL" value="<?=$UF_EMAIL?>"><br>
					<span class="input_title">Ваш телефон </span><input type="text" name="UF_TEL" value="<?=$UF_TEL?>"><br>
					<input class="submit" type="submit" value="ОК"><br>
					<input type="hidden" name="TYPEAUT" value="3">
					<input type="hidden" name="B_URL" value="<?=$B_URL?>">
					<input type="hidden" name="PHP_URL" value="<?=$PHP_URL?>">
					<input type="hidden" name="PODPISKA_TYPE" value="<?=$PODPISKA_TYPE?>">
					<input type="hidden" name="PODPISKA_ID" value="<?=$PODPISKA_ID?>">
					<input type="hidden" name="ERROR" value="<?=$ERROR?>">
					
				</form>
			</div>
					
			</div>
			</div>

			<div <?=($TYPEAUT!=999)?'style="display:none;"':''?>>
				<p class="reg_message"><?=$ERROR?></p>

				<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
					<input class="submit" type="submit" value="Хорошо"><br>
					<input type="hidden" name="TYPEAUT" value="<?=($TYPEAUT>0)?$TYPEAUT:'999'?>">
					<input type="hidden" name="B_URL" value="<?=$B_URL?>">
				</form>
			
				<form action="http://asmu.ru/personal/lk/index.php" method="POST">
					<input type="hidden" name="B_URL" value="<?=$B_URL?>">
					<input type="hidden" name="PHP_URL" value="<?=$PHP_URL?>">
					<input type="hidden" name="PODPISKA_TYPE" value="<?=$PODPISKA_TYPE?>">
					<input type="hidden" name="PODPISKA_ID" value="<?=$PODPISKA_ID?>">
					<input type="hidden" name="ERROR" value="<?=$ERROR?>">
					<input type="hidden" name="UF_LOGIN" value="<?=$UF_LOGIN?>">
					<input type="hidden" name="USER_ID" value="<?=$USER_ID?>">
					<input type="hidden" name="UF_COOKIE" value="<?=$UF_COOKIE?>">
					<input class="submit" type="submit" value="Личный кабинет Слушателя">

				</form>
				
			</div>
			<div <?=($TYPEAUT!=1000)?'style="display:none;"':''?>>
				<p class="reg_message"><?=$MESSAGE?></p>
				<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" class="dualformbtn">
					<input type="hidden" name="B_URL" value="<?=$B_URL?>">
					<input type="hidden" name="PHP_URL" value="">
					<input type="hidden" name="PODPISKA_TYPE" value="<?=1000+intval($PODPISKA_TYPE)?>">
					<input type="hidden" name="PODPISKA_ID" value="<?=$PODPISKA_ID?>">
					<input type="hidden" name="ERROR" value="">
					<input type="hidden" name="UF_LOGIN" value="<?=$UF_LOGIN?>">
					<input type="hidden" name="USER_ID" value="<?=$USER_ID?>">
					<input type="hidden" name="UF_COOKIE" value="<?=$UF_COOKIE?>">
					<input class="submit2" type="submit" value="Да">
				</form>
				<form action="http://asmu.ru/personal/lk/index.php" name="LK_FORM" method="POST" class="dualformbtn">
			  		<input type="hidden" name="B_URL" value="<?=$B_URL?>">
					<input type="hidden" name="PHP_URL" value="<?=$PHP_URL?>">
					<input type="hidden" name="PODPISKA_TYPE" value="<?=$PODPISKA_TYPE?>">
					<input type="hidden" name="PODPISKA_ID" value="<?=$PODPISKA_ID?>">
					<input type="hidden" name="ERROR" value="<?=$ERROR?>">
					<input type="hidden" name="UF_LOGIN" value="<?=$UF_LOGIN?>">
					<input type="hidden" name="UF_COOKIE" value="<?=$UF_COOKIE?>">
					<input type="hidden" name="USER_ID" value="<?=$USER_ID?>">
					<input class="submit2" type="submit" value="Нет"><br>
				</form>
				
			</div>
			<a href="http://asmu.ru">Перейти на главную страницу</a>
			</div>
			<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery-3.1.1.min.js"></script>			
			
			<script type="text/javascript">
			$('.popup_reg .close_window_reg, .overlay_reg').click(function (){
				$('.popup_reg, .overlay_reg').css({'opacity': 0, 'visibility': 'hidden'});
				location.replace("http://asmu.ru/<?=$B_URL?>");
			});

			$('a.open_window_reg').click(function (e){
				$('.popup_reg, .overlay_reg').css({'opacity': 1, 'visibility': 'visible'});
				e.preventDefault();
			});
			
				$('.popup_reg, .overlay_reg').css({'opacity': 1, 'visibility': 'visible'});
				/*accordion*/
				
				    $(document).on('click', '.accordion .accord-header', function(){
				      if($(this).next("div").is(":visible")){
				        $(".arrow").removeClass("drop");
				        $(this).find(".arrow").removeClass("drop");
				        $(this).next("div").slideUp("slow");
				      } else {
				        $(".arrow").removeClass("drop");
				        $(this).find(".arrow").addClass("drop");
				        $(".accordion .accord-content").slideUp("slow");
				        $(this).next("div").slideToggle("slow");
				
				      }
				    });
				
			</script>
<!--
<?//print_r($_POST);?>
<?//print_r($_GET);?>
-->
<?//=$USER_ID?>
<?//=$ERROR?>
<?//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
