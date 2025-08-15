<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?CModule::IncludeModule("fileman");
$sql="SELECT * FROM `prepod` WHERE UF_LOGIN='".$Login."'";
$results = $connection->query($sql);
if($row = $results->Fetch()){?>
<div class="prepodform">
	<div id="tabpage1"><?//Общие сведения?>
		<div class="xrowuser">
			<div class="xheadrow">
					<table>
					<tr><td class="xcell3">
					<img style="width:120px; height:160px;" id="img-preview" src="<?=$renderImage['src']?>" >
					</td>
					<td class="xcell3">
						<input type="FILE" value="Изменить" id="img"  onchange="changeImg('<?=$renderImage['src']?>');" accept="image/*" name="PERSONALPHOTO"><br>
						<a style="display:none;" href="javascript:void(0);" id="reset-img-preview" onclick="resetImgPreview('<?=$renderImage['src']?>')">Вернуть исходное фото</a>
						<input type="hidden" value="" name="XPERSONALPHOTO" id="XPERSONALPHOTO">(только .jpg)
					
					</td>
					</tr>
					</table>
			</div>
			<div class="xcell2" >
				<br>
				<b>Фамилия, имя, отчество: </b><?=$row['UF_FIO']?><br>
				<?if ($EDIT_USER["SECOND_NAME"]=="") $EDIT_USER["SECOND_NAME"]=explode(" ",$row['UF_FIO'])[2];?>
				<input type="hidden" name="BX_SECOND_NAME" value="<?=$EDIT_USER["SECOND_NAME"]?>">
				<br>
				<b>Должность преподавателя в отделе кадров: </b><span><?=$row['UF_POST']?></span><br>
				<?
					$WORK_POST=$EDIT_USER["WORK_NOTES"];
					if($WORK_POST=="") $WORK_POST=$row['UF_POST'];
				 ?>
				<b>Должность преподавателя для сайта: </b><input name="UF_POST" value="<?=$WORK_POST?>"><br>
				<br>
				<b title="Изменение доступно только через отдел кадров">Уч&#1104;ная степень: </b><span name="UF_STEP"><?=$row['UF_STEP']?></span><br>
				<br>
				<b title="Изменение доступно только через отдел кадров">Уч&#1104;ное звание: </b><span name="UF_ZVAN"><?=$row['UF_ZVAN']?></span><br>
				<br>

				<br>
			</div>
		</div>
			<div class="xrowuser"><div class="xheadrow">Рабочий телефон (городской) </div><div class="xcell2"><input name="BX_WORK_PHONE" value="<?=$EDIT_USER['WORK_PHONE']?>"></div></div>
			<div class="xrowuser"><div class="xheadrow">Рабочий телефон (внутренний АГМУ)</div><div class="xcell2"><input name="BX_UF_INSIDE_TEL" value="<?=$EDIT_USER['UF_INSIDE_TEL']?>"></div></div>
			<div class="xrowuser"><div class="xheadrow">Рабочий E-mail</div><div class="xcell2"><input name="BX_UF_MAIL" value="<?=$EDIT_USER['UF_MAIL']?>"></div></div>
			<div class="xrowuser"><div class="xheadrow">Место работы (улица, дом, кабинет)</div><div class="xcell2"><input name="BX_WORK_STREET" value="<?=$EDIT_USER['WORK_STREET']?>"></div></div>

	</div>
	<div id="tabpage2"><?//Опыт?>
		<div class="xrowuser" title="Изменение доступно только через отдел кадров">
			<div class="xheadrow">Уровень образования</div>
			<div class="xcell2"><span name="UF_OB_LEVEL"> <?=$row['UF_OB_LEVEL']?></span></div>
		</div>
		<div class="xrowuser" title="Изменение доступно только через отдел кадров">
			<div class="xheadrow">Квалификация</div>
		<?$row['UF_OB_KVAL']=str_replace("|","",$row['UF_OB_KVAL']);?>	
			<div class="xcell2"><span name="UF_OB_KVAL"><?=$row['UF_OB_KVAL']?></span></div>
		</div>
		<div class="xrowuser" title=""><div class="xheadrow">Регалии</div>
			<?if ($EDIT_USER['UF_REGALII']=="") $EDIT_USER['UF_REGALII']=(($row['UF_ZVAN']!="")?($row['UF_ZVAN'].", "):"").$row['UF_STEP'];?>
			<div class="xcell2"><input type="text" name="BX_UF_REGALII" value="<?=$EDIT_USER['UF_REGALII']?>"> </div></div>
		<div class="xrowuser" title="Изменение доступно только через отдел кадров"><div class="xheadrow">Наименование направления подготовки и (или) специальности педагогического работника</div>
			<div class="xcell2"><span name="UF_OB_SPEC"> <?=$row['UF_OB_SPEC']?></span></div></div>
		<div class="xrowuser" title="Изменение доступно только через отдел кадров">
			<div class="xheadrow">Сведения о повышении квалификации и (или) профессиональной переподготовке педагогического работника</div>
			<?//$row['UF_ADD_KVAL']=str_replace("<br>","\n",$row['UF_ADD_KVAL']);?>
			<div class="xcell2"><span disabled name="UF_ADD_KVAL"><?=$row['UF_ADD_KVAL']?></span></div>
		</div>
		<div class="xrowuser" title="Изменение доступно только через отдел кадров"><div class="xheadrow">Общий стаж работы</div><div class="xcell2"><span disabled name="UF_ST_TOTAL"><?=allmounstostr($row['UF_ST_TOTAL'])?></span></div></div>
		<div class="xrowuser" title="Изменение доступно только через отдел кадров"><div class="xheadrow">Стаж работы педагогического работника по специальности</div><div class="xcell2"><span disabled name="UF_ST_PED"><?=allmounstostr($row['UF_ST_PED'])?></span></div></div>
	</div>

	<div id="tabpage3"><?//Перечень преподаваемых дисциплин?>
		<div class="xrowuser"><div class="xheadrow">Перечень преподаваемых дисциплин</div>
			<div   class="xcell2">
				<div id="disclist">
				<?
				$AR_DISC=mb_json_decode($row['UF_DISC'],true);
				$kdisc=0;
				//print_r($AR_DISC);
				if (is_array($AR_DISC))
					foreach ($AR_DISC as $sdisc):
						$kdisc++;
						if (is_array($sdisc)){$discName=$sdisc["name"];$discOPP=$sdisc["OPP"];} else {$discName=$sdisc;$discOPP=0;}
						?>
						<div id="disc_id_<?=$kdisc?>" class="discname" >
							<textarea class="ufdisc" name="UF_DISC_<?=$kdisc?>"  rows=2 style="display: block;width:calc(100% - 30px);"><?=$discName?></textarea>
							<button class="removedisc" onclick="$('#disc_id_<?=$kdisc?>').remove(); return false;" ><img src=COMMON_TEMPLATE."/img/delete.png" style="width:20px"></bunnon>
						</div>
				<?endforeach?>
				</div>
				<button onclick="return add_dst();" style="width: 90%;">Добавить</button>
			</div>
		</div>
	</div>
	<div id="tabpage4"><?//Биографическая справка?>
	
	<?
	$txt=htmlspecialchars_decode($EDIT_USER["UF_BSPRAVKA"]);
	$ptn= "!<script[^>]*>(.)*</script>!Uis";
	$txt = preg_replace($ptn,"",$txt); 

	$LHE = new CLightHTMLEditor;
    $LHE->Show(array(
        'id' => "LHE_BX_UF_BSPRAVKA",
        'width' => '100%',
        'height' => '400px',
        'inputName' => "BX_UF_BSPRAVKA",
        'content' => $txt,
	'RESIZABLE'=>false,
        'bUseFileDialogs' => false,
        'bFloatingToolbar' => false,
        'bArisingToolbar' => false,
        'toolbarConfig' => array(
            'Bold', 'Italic', 'Underline', 'RemoveFormat', 'Code', 'Source', 'Video', 'Html',
            'CreateLink', 'DeleteLink', 'Image', 'Video',
            'BackColor', 'ForeColor',
            'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull',
            'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent',
            'StyleList', 'HeaderList',
            'FontList', 'FontSizeList',
        ),
    ));

	

	?>
	</div>
	<div id="tabpage5" ><?//Публикации?>
		В разработке
	</div>

</div>
<?}?>
<input type="hidden" name="PID" value="<?=$row['ID']?>">