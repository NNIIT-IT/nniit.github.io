<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$str=htmlspecialchars($_POST["txt"]);
	$str=trim(preg_replace('/[^a-zA-ZР°-СЏРђ-РЇ0-9 \-]/ui', '',$str));
	$str=str_replace("  "," ",$str);
	$ansver=array("0"=>$str);
	$DBX=Bitrix\Main\Application::getConnection();
	$sql="select SQL_NO_CACHE UF_NUMBER_ZAYAV as z from abit_list_by_fio ";
	$sql.="where UF_GOD=".date("Y")." and (UF_USER_FIO=\"{$str}\" or UF_SNILS=\"{$str}\" or UF_NUMBER_ZAYAV=\"{$str}\" or UF_LDELO=\"{$str}\") limit 10";
	if ($rez=$DBX->query($sql)){
		while ($ob=$rez->fetch()){
			$ansver[]=$ob["z"];
		}
	}
	$ansver=array_unique($ansver);
	echo implode(",",$ansver);
?>