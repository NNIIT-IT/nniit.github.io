<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (CModule::IncludeModule("iblock")):
	$filter = Array("ACTIVE"=> "Y", "GROUPS_ID"=> Array(9));//высшее руководство
	$rsUsers = CUser::GetList(($by="WORK_PAGER"), ($order="asc"), $filter); // выбираем пользователей
	$users=array();
	while($rec=$rsUsers->Fetch()){
	//echo "<!--";print_r($rec);echo"-->";
		$rsUser = CUser::GetByID($rec["ID"]); 
		$arUser = $rsUser->Fetch(); 
		$FIO=($rec["PERSONAL_WWW"]!="")?('<a href="'.$rec["PERSONAL_WWW"].'">'):'';
		$FIO.=$rec["LAST_NAME"]." ".$rec["NAME"]." ".$rec["SECOND_NAME"];
		$FIO.=($rec["PERSONAL_WWW"]!="")?'</a>':'';
		$POST=$arUser["UF_HIPOST"];
		$HIPOST=$arUser["UF_HIPOST"];
		$PHONE=($rec["WORK_PHONE"]!="")?$rec["WORK_PHONE"]:"-";
		$EMAIL=($arUser["UF_MAIL"]!="")?$arUser["UF_MAIL"]:$rec["EMAIL"];
		if ($EMAIL=="") $EMAIL="-";
		$index=intval($rec["WORK_PAGER"]);
		if($index==0)$index=9;
		$users[$index."_".$rec["ID"]]=array("FIO"=>$FIO, "POST"=>$POST,"PHONE"=>$PHONE,"EMAIL"=>$EMAIL,"HIPOST"=>$HIPOST);
	}

	ksort($users);
	?>
	<table  class="simpletable">
	<tbody>
	<tr style="text-align: center;vertical-align: middle;"><th>№ п/п</th><th width="25%">Фамилия, имя, отчество</th><th>Должность</th><th>Контактный телефон</th><th>Электронная почта</th></tr>
	<?
	$i=0;
	foreach($users as $user):
if ($user["POST"]!=""){
	$i++;$teg="rucovodstvoZam";
	$pst=trim(mb_strtoupper(str_replace(array("."," "),"",$user["HIPOST"])));
	if ($pst=="РЕКТОР"  || $pst=="ИОРЕКТОРА") $teg="rucovodstvo";
	if ($pst=="ПРОРЕКТОР"   || $pst=="ИОПРОРЕКТОРА") $teg="rucovodstvoZam";
?>
	<tr itemprop="<?=$teg?>">
	<td style="text-align: center;vertical-align: middle;"><?=$i?></td>
	<td itemprop="fio"><?=$user["FIO"]?></td>
	<td itemprop="post"><?=$user["POST"]?></td>
	<td itemprop="telephone"><?=$user["PHONE"]?></td>
	<td itemprop="email"><?=$user["EMAIL"]?></td>
	</tr>
	<?
}
endforeach;?>
<tr style="display:none" itemprop="rucovodstvoFil">
	<td itemprop="nameFil">филиалы отсутствуют</td>
	<td itemprop="fio"> филиалы отсутствуют</td>
	<td itemprop="post"> филиалы отсутствуют</td>
	<td itemprop="telephone"> филиалы отсутствуют</td>
	<td itemprop="email"> филиалы отсутствуют</td>
	</tr>
	</tbody>
	</table>
<?endif;?>