<?php

// admin initialization
define('ADMIN_MODULE_NAME', 'highloadblock');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

IncludeModuleLangFile(__FILE__);
//IncludeModuleLangFile('/bitrix/admin/highloadblock_rows_list.php');


if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
{
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}


use Bitrix\Highloadblock as HL;
//https://nsk-niit.local/bitrix/js/main/core/core_autosave.min.js
CJSCore::Init(array("jquery","popup","window","utils",'fx'));
///bitrix/js/main/core/core_admin_interface.min.js
?>

<?
$hlblock = null;
if (isset($_GET['return_url'])){
	$return_url=htmlspecialcharsbx($_REQUEST['return_url']);
	//$return_url=str_replace("https://asmu.ru","",$return_url);
	//echo $return_url;
}
	$arProps=array();
if (isset($_GET['prop'])){
	$prop=htmlspecialcharsbx($_REQUEST['prop']);
	$arProp=json_decode(base64_decode($prop));

	foreach($arProp as $prname=>$prvalue){
		$arProps[htmlspecialchars($prname)]=htmlspecialchars($prvalue);
	}
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
/*
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

*/
//view

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_js.php');

//$context->Show();


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
	if(count($arProps)>0){
		foreach($arProps as $ufCode=>$value){
			if($row[$ufCode]=="") $row[$ufCode] = $value;
		}
	}
				//print_r($hlblock);echo "<br>";
//$arUserFields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData('HLBLOCK_'.$hlblock['ID'], $row, LANGUAGE_ID, false, 'ID');

//	echo "<pre>["; print_r($bVarsFromForm);echo "]</pre>";
	echo $tabControl->ShowUserFieldsWithReadyData('HLBLOCK_'.$hlblock['ID'], $row, $bVarsFromForm, 'ID');
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
<!-- script>
			var topWindow = BX.PageObject.getRootWindow();
			(topWindow.BX.file_input) ? new topWindow.BX.file_input({'id':'bx_file_uf_file','fileExists':false,'files':[],'menuNew':[{'ID':'upload','GLOBAL_ICON':'adm-menu-upload-pc','TEXT':'Загрузить с компьютера','CLOSE_ON_CLICK':false}],'menuExist':[{'ID':'upload','GLOBAL_ICON':'adm-menu-upload-pc','TEXT':'Заменить файлом с компьютера','CLOSE_ON_CLICK':false}],'multiple':false,'useUpload':true,'useMedialib':false,'useFileDialog':false,'useCloud':false,'delName':'UF_FILE_del','descName':'','inputSize':'50','minPreviewHeight':'100','minPreviewWidth':'120','showDesc':false,'showDel':true,'maxCount':false,'viewMode':false,'inputs':{'upload':{'NAME':'UF_FILE'},'medialib':false,'file_dialog':false,'cloud':false}}) : new BX.file_input({'id':'bx_file_uf_file','fileExists':false,'files':[],'menuNew':[{'ID':'upload','GLOBAL_ICON':'adm-menu-upload-pc','TEXT':'Загрузить с компьютера','CLOSE_ON_CLICK':false}],'menuExist':[{'ID':'upload','GLOBAL_ICON':'adm-menu-upload-pc','TEXT':'Заменить файлом с компьютера','CLOSE_ON_CLICK':false}],'multiple':false,'useUpload':true,'useMedialib':false,'useFileDialog':false,'useCloud':false,'delName':'UF_FILE_del','descName':'','inputSize':'50','minPreviewHeight':'100','minPreviewWidth':'120','showDesc':false,'showDel':true,'maxCount':false,'viewMode':false,'inputs':{'upload':{'NAME':'UF_FILE'},'medialib':false,'file_dialog':false,'cloud':false}})
		</script -->
<?//require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin_js.php');?>