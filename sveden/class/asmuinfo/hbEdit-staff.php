<?php
// admin initialization
define('ADMIN_MODULE_NAME', 'highloadblock');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile('/bitrix/admin/highloadblock_rows_list.php');


if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
{
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}


use Bitrix\Highloadblock as HL;

$hlblock = null;
if (isset($_GET['return_url'])){
	$return_url=htmlspecialcharsbx($_REQUEST['return_url']);
	$return_url=str_replace("https://www.dentmaster.ru","",$return_url);
	//echo $return_url;
}
// get entity info
if (isset($_REQUEST['ENTITY_ID']))
{
	$hlblock = HL\HighloadBlockTable::getById($_REQUEST['ENTITY_ID'])->fetch();

	if (!empty($hlblock))
	{
		//localization
		$lang = HL\HighloadBlockLangTable::getList(array(
					'filter' => array('ID' => $hlblock['ID'], '=LID' => LANG))
				)->fetch();
		if ($lang)
		{
			$hlblock['NAME_LANG'] = $lang['NAME'];
		}
		else
		{
			$hlblock['NAME_LANG'] = $hlblock['NAME'];
		}
		//check rights
		if ($USER->isAdmin())
		{
			$canEdit = $canDelete = true;
		}
		else
		{
			$operations = HL\HighloadBlockRightsTable::getOperationsName($ENTITY_ID);
			if (empty($operations))
			{
				$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
			}
			else
			{
				$canEdit = in_array('hl_element_write', $operations);
				$canDelete = in_array('hl_element_delete', $operations);
			}
		}
	}
}

if (empty($hlblock))
{
	// 404
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_js.php');
	echo GetMessage('HLBLOCK_ADMIN_ROW_EDIT_NOT_FOUND');
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin_js.php');
	die();
}

$is_create_form = true;
$is_update_form = false;
$action = isset($_REQUEST['action']) ? htmlspecialcharsbx($_REQUEST['action']) : 'add';

$isEditMode = $canEdit;

$errors = array();

// get entity
$entity = HL\HighloadBlockTable::compileEntity($hlblock);

/** @var HL\DataManager $entity_data_class */
$entity_data_class = $entity->getDataClass();

// get row
$row = null;
if (isset($_REQUEST['ID']) && $_REQUEST['ID'] > 0)
{
	$row = $entity_data_class::getById($_REQUEST['ID'])->fetch();

	if (empty($row))
	{
		$row = null;
	}

	if (!empty($row))
	{
		if ($action != 'copy')
		{
			if ($action != 'delete')
			{
				$action = 'update';
			}
			$is_update_form = true;
			$is_create_form = false;
		}
	}
}

if ($is_create_form)
{
	$APPLICATION->SetTitle(GetMessage('HLBLOCK_ADMIN_ENTITY_ROW_EDIT_PAGE_TITLE_NEW', array('#NAME#' => $hlblock['NAME_LANG'])));
}
else
{
	$APPLICATION->SetTitle(GetMessage('HLBLOCK_ADMIN_ENTITY_ROW_EDIT_PAGE_TITLE_EDIT',
		array('#NAME#' => $hlblock['NAME_LANG'], '#NUM#' => $row['ID']))
	);
}

// form
$aTabs = array(
	array('DIV' => 'edit1', 'TAB' => $hlblock['NAME_LANG'], 'ICON'=>'ad_contract_edit', 'TITLE'=> htmlspecialcharsbx($hlblock['NAME_LANG']))
);

$tabControl = new CAdminForm('hlrow_edit_'.$hlblock['ID'], $aTabs);
//echo " is_update_form=".$is_update_form." action=". $action." canDelete=".$canDelete." check_bitrix_sessid=".check_bitrix_sessid();
//print_r($row);
//echo"@1";
// delete action
if ($is_update_form && $action === 'delete' && $canDelete && check_bitrix_sessid())
{
	$entity_data_class::delete($row['ID']);
	//echo "deleted ".$row['ID'];
	//LocalRedirect('highloadblock_rows_list.php?ENTITY_ID='.$hlblock['ID'].'&lang='.LANGUAGE_ID);
	LocalRedirect($return_url);
	//header('Location: https://asmu.ru'.$return_url);
	echo "<script>location.reload();</script>";
	
}

// save action
if (($save <> '' || $apply <> '') && $_SERVER['REQUEST_METHOD'] =='POST' && $canEdit && check_bitrix_sessid())
{
	$data = array();

	$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$hlblock['ID'], $data);

	/** @param Bitrix\Main\Entity\AddResult $result */
	if ($is_update_form)
	{
		$ID = intval($_REQUEST['ID']);
		$result = $entity_data_class::update($ID, $data);
	}
	else
	{
		$result = $entity_data_class::add($data);
		$ID = $result->getId();
	}

	if($result->isSuccess())
	{
		
		if ($save <> '')
		{
			LocalRedirect($return_url);
			//header('Location: https://asmu.ru'.$return_url);
			//LocalRedirect('highloadblock_rows_list.php?ENTITY_ID='.$hlblock['ID'].'&lang='.LANGUAGE_ID."&return_url=".$return_url);
			//LocalRedirect('hbEdit.php?ENTITY_ID='.$hlblock['ID'].'&return_url='.$return_url.'&ID='.intval($ID).'&lang='.LANGUAGE_ID.'&'.$tabControl->ActiveTabParam());
		}
		else
		{
			LocalRedirect('hbEdit.php?ENTITY_ID='.$hlblock['ID'].'&return_url='.$return_url.'&ID='.intval($ID).'&lang='.LANGUAGE_ID.'&'.$tabControl->ActiveTabParam());
			//header('Location: https://asmu.ru'.$return_url);

		}
	}
	else
	{
		$errors = $result->getErrorMessages();

		// rewrite values
		foreach ($data as $k => $v)
		{
			if (isset($row[$k]))
			{
				$row[$k] = $v;
			}
		}
	}
}

// menu
$aMenu = array(
	array(
		'TEXT'	=> GetMessage('HLBLOCK_ADMIN_ROWS_RETURN_TO_LIST_BUTTON'),
		'TITLE'	=> GetMessage('HLBLOCK_ADMIN_ROWS_RETURN_TO_LIST_BUTTON'),
		'LINK'	=> 'highloadblock_rows_list.php?ENTITY_ID='.$hlblock['ID'].'&lang='.LANGUAGE_ID,
		'ICON'	=> 'btn_list',
	)
);
if (!$action != 'copy' && $is_update_form && $canEdit)
{
	$aMenu[] = array(
		'TEXT' => GetMessage('HLBLOCK_ADMIN_ROWS_COPY'),
		'TITLE' => GetMessage('HLBLOCK_ADMIN_ROWS_COPY'),
		'LINK' => $APPLICATION->getCurPageParam('action=copy', array('action')),
		'ICON' => 'btn_copy',
	);
}
if ($is_update_form && ($canEdit || $canDelete))
{
	$subMenu = array();
	if ($canEdit)
	{
		$subMenu[] = array(
			'TEXT' => GetMessage('HLBLOCK_ADMIN_ROWS_ADD'),
			'TITLE' => GetMessage('HLBLOCK_ADMIN_ROWS_ADD'),
			'LINK' => $APPLICATION->getCurPageParam('ID=0', array('action', 'ID')),
			'ICON' => 'edit',
		);
	}
	if ($canDelete)
	{
		$subMenu[] = array(
			'TEXT' => GetMessage('HLBLOCK_ADMIN_ROWS_DEL'),
			'TITLE' => GetMessage('HLBLOCK_ADMIN_ROWS_DEL'),
			'ACTION' => 'if(confirm(\''.GetMessageJS('HLBLOCK_ADMIN_ROWS_DEL_CONF').'\'))window.location=\''.
						CUtil::JSEscape($APPLICATION->getCurPageParam('action=delete&'.bitrix_sessid_get(), array('action'))).'\';',
			'ICON' => 'delete',
		);
	}
	$aMenu[] = array(
		'TEXT' => GetMessage('HLBLOCK_ADMIN_ROWS_ACTIONS'),
		'TITLE' => GetMessage('HLBLOCK_ADMIN_ROWS_ACTIONS'),
		'MENU' => $subMenu
	);
}
$context = new CAdminContextMenu($aMenu);


//view

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_js.php');

$context->Show();


if (!empty($errors))
{
	$bVarsFromForm = true;
	CAdminMessage::ShowMessage(join("\n", $errors));
}
else
{
	$bVarsFromForm = false;
}

$tabControl->BeginPrologContent();

echo $USER_FIELD_MANAGER->ShowScript();

CAdminCalendar::ShowScript();

$tabControl->EndPrologContent();
$tabControl->BeginEpilogContent();
?>

	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value="<?= htmlspecialcharsbx(!empty($row) ? $row['ID'] : '')?>">
	<input type="hidden" name="ENTITY_ID" value="<?= htmlspecialcharsbx($hlblock['ID'])?>">
	<input type="hidden" name="lang" value="<?= LANGUAGE_ID?>">
	<input type="hidden" name="action" value="<?= $action?>">
	<input type="hidden" name="return_url" value="<?=$return_url?>">


<?$tabControl->EndEpilogContent();?>

	<? $tabControl->Begin(array(
		'FORM_ACTION' => $APPLICATION->GetCurPage().'?ENTITY_ID='.$hlblock['ID'].'&ID='.intval($ID).'&lang='.LANG.'&return_url='.$return_url
	));?>

	<? $tabControl->BeginNextFormTab(); ?>

	<?
	$ufields = $USER_FIELD_MANAGER->GetUserFields('HLBLOCK_'.$hlblock['ID']);
	$hasSomeFields = !empty($ufields);

	if ($action != 'copy')
	{
		$tabControl->AddViewField('ID', 'ID', !empty($row) ? $row['ID'] : '');
	}
	//remove files for copy action
	elseif ($hasSomeFields && !empty($row))
	{
		foreach ($ufields as $ufCode => $ufField)
		{
			print_r($ufCode); print_r($ufField);	

			if (
				isset($ufField['USER_TYPE_ID']) && $ufField['USER_TYPE_ID'] == 'file' ||
				(
					isset($ufField['USER_TYPE']) && is_array($ufField['USER_TYPE']) &&
					isset($ufField['USER_TYPE']['BASE_TYPE']) && $ufField['USER_TYPE']['BASE_TYPE'] == 'file'
				)
			)
			{
				$row[$ufCode] = null;
			}
			
		}
		
	}
	if($hasSomeFields && empty($row)){

		foreach ($ufields as $ufCode => $ufField){
			if(in_array($ufCode,array_keys($_REQUEST))){
				$value=htmlspecialchars($_REQUEST[$ufCode]);
				$row[$ufCode] = $value;
			};
		}
	}
		
	///echo $tabControl->ShowUserFieldsWithReadyData('HLBLOCK_'.$hlblock['ID'], $row, $bVarsFromForm, 'ID');
//----------------------------------------------------------------------------------
$PROPERTY_ID='HLBLOCK_'.$hlblock['ID'];
$readyData=$row;
//$bVarsFromForm=$bVarsFromForm;
$primaryIdName='ID';
$sectionsName=array(
			179=>"Бакалавриат",
			180=>"Специалитет",
			181=>"Ординатура",
			182=>"Аспирантура",
			184=>"Магистратура",
			185=>"НПО",
			186=>"CПО"
		);
// function ShowUserFieldsWithReadyData($PROPERTY_ID, $readyData, $bVarsFromForm, $primaryIdName = 'VALUE_ID')
	/**
         * @global CMain $APPLICATION
         * @global CUserTypeManager $USER_FIELD_MANAGER
         */
        global $USER_FIELD_MANAGER, $APPLICATION;

        if($USER_FIELD_MANAGER->GetRights($PROPERTY_ID) >= "W")
        {
            $tabControl->BeginCustomField("USER_FIELDS_ADD", GetMessage("admin_lib_add_user_field"));
            ?>
            <tr>
                <td colspan="2" align="left">
                    <a href="/bitrix/admin/userfield_edit.php?lang=<?echo LANGUAGE_ID?>&amp;ENTITY_ID=<?echo urlencode($PROPERTY_ID)?>&amp;back_url=<?echo urlencode($APPLICATION->GetCurPageParam($tabControl->name.'_active_tab=user_fields_tab', array($tabControl->name.'_active_tab')))?>"><?echo $tabControl->GetCustomLabelHTML()?></a>
                </td>
            </tr>
            <?
            $tabControl->EndCustomField("USER_FIELDS_ADD", '');
        }

        $arUserFields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData($PROPERTY_ID, $readyData, LANGUAGE_ID, false, $primaryIdName);

        foreach($arUserFields as $FIELD_NAME => $arUserField)
        {
	    if($FIELD_NAME!="UF_TEACHINGDISCIPLIN"){
            $arUserField["VALUE_ID"] = intval($readyData[$primaryIdName]);
            if(array_key_exists($FIELD_NAME, $tabControl->arCustomLabels))
                $strLabel = $tabControl->arCustomLabels[$FIELD_NAME];
            else
                $strLabel = $arUserField["EDIT_FORM_LABEL"]? $arUserField["EDIT_FORM_LABEL"]: $arUserField["FIELD_NAME"];
            $arUserField["EDIT_FORM_LABEL"] = $strLabel;

            $tabControl->BeginCustomField($FIELD_NAME, $strLabel, $arUserField["MANDATORY"]=="Y");

            if(isset($_REQUEST['def_'.$FIELD_NAME]))
                $arUserField['SETTINGS']['DEFAULT_VALUE'] = $_REQUEST['def_'.$FIELD_NAME];

            echo $USER_FIELD_MANAGER->GetEditFormHTML($bVarsFromForm, $GLOBALS[$FIELD_NAME], $arUserField);

            $form_value = $GLOBALS[$FIELD_NAME];
            if(!$bVarsFromForm)
                $form_value = $arUserField["VALUE"];
            elseif($arUserField["USER_TYPE"]["BASE_TYPE"]=="file")
                $form_value = $GLOBALS[$arUserField["FIELD_NAME"]."_old_id"];

            $hidden = "";
            if(is_array($form_value))
            {
                foreach($form_value as $value)
                    $hidden .= '<input type="hidden" name="'.$FIELD_NAME.'[]" value="'.htmlspecialcharsbx($value).'">';
            }
            else
            {
                $hidden .= '<input type="hidden" name="'.$FIELD_NAME.'" value="'.htmlspecialcharsbx($form_value).'">';
            }
            $tabControl->EndCustomField($FIELD_NAME, $hidden);

        }else{
			   $arUserField["VALUE_ID"] = intval($readyData[$primaryIdName]);
            if(array_key_exists($FIELD_NAME, $tabControl->arCustomLabels))
                $strLabel = $tabControl->arCustomLabels[$FIELD_NAME];
            else
                $strLabel = $arUserField["EDIT_FORM_LABEL"]? $arUserField["EDIT_FORM_LABEL"]: $arUserField["FIELD_NAME"];
            $arUserField["EDIT_FORM_LABEL"] = $strLabel;

            $tabControl->BeginCustomField($FIELD_NAME, $strLabel, $arUserField["MANDATORY"]=="Y");
			//echo "<pre>";
		//	 print_r($bVarsFromForm); 
		//	echo "<br>2";
		//	print_r($GLOBALS[$FIELD_NAME]); 
		//	echo print_r($arUserField);
		//	echo "</pre>";
			?>
			<tr>
			<td class="adm-detail-content-cell-l" width="40%"><?=$arUserField["EDIT_FORM_LABEL"]?></td>
			<td class="adm-detail-content-cell-r" width="60%">
			<div class="disclist" id="disclist">
				<div class="discnameh">
					<div class="discCollh">
						Специальность
						
					</div>
					<div class="discCollh">
						Дисциплина
						
					</div>
					<div class="discbtnColl">
						<b> </b>
					</div>
				</div>
			<?
				if($arUserField["VALUE"]!=""){
					$jsonValue=str_replace("&quot;",'"',$arUserField["VALUE"]);
					
					$listRows=\Bitrix\Main\Web\Json::decode(mb_convert_encoding($jsonValue, 'UTF-8', 'windows-1251'));
				
					if(count($listRows)>0){
						$rowId=0;
						foreach($listRows as $rowData){
							$rowId++;
							$opp=$rowData["opp"];
							$disc=$rowData["disc"];
							echo "<div class=\"discname\" id=\"discname_{$rowId}\">";
							echo "<div class=\"discColl\">";
							include $_SERVER['DOCUMENT_ROOT']."/sveden/class/asmuinfo/get_spec_js.php";
							echo "</div><div class=\"discColl\">";
							include $_SERVER['DOCUMENT_ROOT']."/sveden/class/asmuinfo/get_disc_js.php";
							echo "</div><div class=\"discbtnColl\">";
							echo "<button onclick=\"$('#discname_{$rowId}').remove();savechange(); return false;\" class=\"btndel\">";
							echo "<img src=\"/sveden/icons/delete.png\" style=\"width:20px\"></button></div>";
							echo "</div>";									
						}
					}
				}
			?>
			</div>
			</div>
			<div><input type="button"  onclick="add_dst('',''); return false;" value="Добавить"></div>
			<input type="hidden" id="input_<?=$FIELD_NAME ?>" name="<?=$FIELD_NAME?>" value="<?=htmlspecialcharsBX($jsonValue)?>">
			
			</td>
			</tr>
<?
			//$form_value = $GLOBALS[$FIELD_NAME];
//
	//		 $hidden = "";
      //      
        //        $hidden .= '<input type="hidden" id="input_'.$FIELD_NAME.'" name="'.$FIELD_NAME.'" value="'.htmlspecialcharsbx($form_value).'">';
            
            $tabControl->EndCustomField($FIELD_NAME, $hidden);
			
		}
	}

//----------------------------------------------------------------------------------
	?>

	<?
	$disable = true;
	if($isEditMode)
		$disable = false;

	if ($hasSomeFields)
	{
		//$tabControl->Buttons(array('disabled' => $disable, 'back_url'=>'highloadblock_rows_list.php?ENTITY_ID='.intval($hlblock['ID']).'&lang='.LANGUAGE_ID));
		//$tabControl->Buttons(array('disabled' => $disable, 'back_url'=>'highloadblock_rows_list.php?ENTITY_ID='.intval($hlblock['ID']).'&lang='.LANGUAGE_ID));
		$tabControl->Buttons(array('disabled' => $disable, 'back_url'=>$return_url));
	}
	else
	{
		$tabControl->Buttons(false);
	}


	$tabControl->Show();
	?>
</form>

<?include ($_SERVER['DOCUMENT_ROOT'].'/sveden/class/asmuinfo/hbEdit-js.php');?>
<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin_js.php');