<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$str=htmlspecialchars($_POST["txt"]);
	$str=trim(preg_replace('/[^a-zA-ZР°-СЏРђ-РЇ0-9 \-]/ui', '',$str));
	$str=str_replace("  "," ",$str);
	$ansver=$str;
	if($str!=""){
		$DBX=Bitrix\Main\Application::getConnection();
		$arAnsver=array();
		$sql="SELECT distinct UF_SUBJECT,UF_DATE from  abit_list_by_fio alz join  rasp_exam_abit ra on alz.UF_USER_ID=ra.UF_USER_ID ";
		$sql.="where ra.UF_FIO=\"{$str}\" or alz.UF_SNILS=\"{$str}\" or alz.UF_NUMBER_ZAYAV=\"{$str}\" or alz.UF_LDELO=\"{$str}\" limit 5 ";

		//$sql="select UF_SUBJECT,UF_DATE from  rasp_exam_abit where UF_FIO=\"{$str}\" or UF_SNILS=\"{$str}\" or UF_NUMBER_ZAYAV=\"{$str}\" limit 5"; // or UF_LDELO=\"{$str}\"
		
		if ($rez=$DBX->query($sql)){
			while ($ob=$rez->fetch()){
				$dt=strtotime($ob["UF_DATE"]);
				$dt_date=date("d.m.Y",$dt);
				$dt_TIME=date("h:i",$dt);
				$subject=$ob["UF_SUBJECT"];
				$arAnsver[$dt_date]=array("s"=>$subject,"t"=>$dt_TIME);
			}
			$ansver=json_encode($arAnsver,JSON_UNESCAPED_UNICODE);
		}
	}
		echo trim($ansver);
?>