<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$str=htmlspecialchars($_POST["txt"]);
	$str=trim(preg_replace('/[^a-zA-ZР°-СЏРђ-РЇ0-9 \-]/ui', '',$str));
	$str=str_replace("  "," ",$str);
	$ansver=$str;
	$DB=Bitrix\Main\Application::getConnection();
	$sql="select UF_NUMBER_ZAYAV as z from abit_list_by_fio_test where UF_USER_FIO=\"{$str}\" or UF_SNILS=\"{$str}\" or UF_NUMBER_ZAYAV=\"{$str}\" limit 1";
	if ($rez=$DB->query($sql)){
		if ($ob=$rez->fetch()){
			$ansver=$ob["z"];
		}
	}
	echo $ansver;
?>