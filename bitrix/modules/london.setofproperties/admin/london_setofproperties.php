<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/admin/iblock_edit_property.php");

$bFullForm = isset($_REQUEST["IBLOCK_ID"]) && isset($_REQUEST["ID"]);
$bSectionPopup = isset($_REQUEST["return_url"]) && ($_REQUEST["return_url"] === "section_edit");
$bReload = isset($_REQUEST["action"]) && $_REQUEST["action"] === "reload";

if (
	('POST' == $_SERVER['REQUEST_METHOD'])
	&& (false == isset($_REQUEST['saveresult']))
	&& (false == isset($_REQUEST['IBLOCK_ID']))
)
	CUtil::JSPostUnescape();
elseif ($bSectionPopup)
	CUtil::JSPostUnescape();

global $DB;
global $APPLICATION;
global $USER;

define('DEF_LIST_VALUE_COUNT',5);

$arDisabledPropFields = array(
	'ID',
	'IBLOCK_ID',
	'TIMESTAMP_X',
	'TMP_ID',
	'VERSION',
);

$arDefPropInfo = array(
	'ID' => 'ntmp_xxx',
	'XML_ID' => '',
	'VALUE' => '',
	'SORT' => '500',
	'DEF' => 'N',
	'MULTIPLE' => 'N',
);

$arDefPropInfo = array(
	'ID' => 0,
	'IBLOCK_ID' => 0,
	'FILE_TYPE' => '',
	'LIST_TYPE' => 'L',
	'ROW_COUNT' => '1',
	'COL_COUNT' => '30',
	'LINK_IBLOCK_ID' => '0',
	'DEFAULT_VALUE' => '',
	'USER_TYPE_SETTINGS' => false,
	'WITH_DESCRIPTION' => '',
	'SEARCHABLE' => '',
	'FILTRABLE' => '',
	'ACTIVE' => 'Y',
	'MULTIPLE_CNT' => '5',
	'XML_ID' => '',
	'PROPERTY_TYPE' => 'S',
	'NAME' => '',
	'HINT' => '',
	'USER_TYPE' => '',
	'MULTIPLE' => 'N',
	'IS_REQUIRED' => 'N',
	'SORT' => '500',
	'CODE' => '',
	'SHOW_DEL' => 'N',
	'VALUES' => false,
	'SECTION_PROPERTY' => $bSectionPopup? "N": "Y",
	'SMART_FILTER' => 'N',
);

$arHiddenPropFields = array(
	'IBLOCK_ID',
	'FILE_TYPE',
	'LIST_TYPE',
	'ROW_COUNT',
	'COL_COUNT',
	'LINK_IBLOCK_ID',
	'DEFAULT_VALUE',
	'USER_TYPE_SETTINGS',
	'WITH_DESCRIPTION',
	'SEARCHABLE',
	'FILTRABLE',
	'MULTIPLE_CNT',
	'HINT',
	'XML_ID',
	'VALUES',
	'SECTION_PROPERTY',
	'SMART_FILTER',
);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !check_bitrix_sessid())
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

if(isset($_REQUEST["PARAMS"]['IBLOCK_ID']))
	$intIBlockID = intval($_REQUEST["PARAMS"]['IBLOCK_ID']);
elseif(isset($_REQUEST["IBLOCK_ID"]))
	$intIBlockID = intval($_REQUEST["IBLOCK_ID"]);
else
	$intIBlockID = false;

if ($intIBlockID < 0 || $intIBlockID === false)
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	echo ShowError(GetMessage("BT_ADM_IEP_IBLOCK_ID_IS_INVALID"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}
elseif ($intIBlockID > 0)
{
	$rsIBlocks = CIBlock::GetList(array(), array(
		"ID" => $intIBlockID,
		"CHECK_PERMISSIONS" => "N",
	));
	$arIBlock = $rsIBlocks->Fetch();
	if ($arIBlock)
	{
		if (!CIBlockRights::UserHasRightTo($intIBlockID, $intIBlockID, "iblock_edit"))
		{
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
			$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
			die();
		}
	}
	else
	{
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
		echo ShowError(str_replace('#ID#',$intIBlockID,GetMessage("BT_ADM_IEP_IBLOCK_NOT_EXISTS")));
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
		die();
	}
}

if(isset($_REQUEST["PARAMS"]['ID']))
	$str_PROPERTY_ID = htmlspecialcharsbx($_REQUEST["PARAMS"]['ID']);
elseif(isset($_REQUEST['ID']))
	$str_PROPERTY_ID = htmlspecialcharsbx($_REQUEST['ID']);
else
	$str_PROPERTY_ID = "";

if (!strlen($str_PROPERTY_ID))
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	echo ShowError(GetMessage("BT_ADM_IEP_PROPERTY_ID_IS_ABSENT"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

if (1 != preg_match('/^n\d+$/',$str_PROPERTY_ID))
{
	$str_PROPERTY_IDCheck = intval($str_PROPERTY_ID);
	if (0 == $intIBlockID || ($str_PROPERTY_IDCheck.'|' != $str_PROPERTY_ID.'|') || 0 >= $str_PROPERTY_IDCheck)
	{
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
		echo ShowError(GetMessage("BT_ADM_IEP_PROPERTY_ID_IS_ABSENT"));
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
		die();
	}
	else
	{
		$str_PROPERTY_ID = $str_PROPERTY_IDCheck;
		unset($str_PROPERTY_IDCheck);
		$rsProps = CIBlockProperty::GetByID($str_PROPERTY_ID, $intIBlockID);
		if (!($arPropCheck = $rsProps->Fetch()))
		{
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
			echo ShowError(str_replace('#ID#',$str_PROPERTY_ID,GetMessage("BT_ADM_IEP_PROPERTY_IS_NOT_EXISTS")));
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
			die();
		}
	}
}

$bVarsFromForm = $bReload;
$message = false;
$strWarning = "";

$strReceiver = '';

if (isset($_REQUEST["PARAMS"]['RECEIVER']))
	$strReceiver = preg_replace("/[^a-zA-Z0-9_:]/", "", htmlspecialcharsbx(($_REQUEST["PARAMS"]['RECEIVER'])));

$aTabs = array();
$tabControl = null;
if(!$bFullForm)
{
	$arProperty = array();
	$PROPERTY = $_POST['PROP'];
	$PARAMS = $_POST['PARAMS'];

	if ((isset($PARAMS['TITLE'])) && ('' != $PARAMS['TITLE']))
	{
		$APPLICATION->SetTitle($PARAMS['TITLE']);
	}

	$arFieldsList = $DB->GetTableFieldsList("b_iblock_property");
	foreach ($arFieldsList as $strFieldName)
	{
		if (!in_array($strFieldName,$arDisabledPropFields))
			$arProperty[$strFieldName] = (isset($PROPERTY[$strFieldName]) ? htmlspecialcharsback($PROPERTY[$strFieldName]) : '');
	}
	$arProperty['PROPINFO'] = $PROPERTY['PROPINFO'];
	$arProperty['PROPINFO'] = base64_decode($arProperty['PROPINFO']);
	if (CheckSerializedData($arProperty['PROPINFO']))
	{
		$arTempo = unserialize($arProperty['PROPINFO']);
		if (is_array($arTempo))
		{
			foreach ($arTempo as $k => $v)
				$arProperty[$k] = $v;
		}
		unset($arTempo);
		unset($arProperty['PROPINFO']);
	}

	$arProperty['MULTIPLE'] = ('Y' == $arProperty['MULTIPLE'] ? 'Y' : 'N');
	$arProperty['IS_REQUIRED'] = ('Y' == $arProperty['IS_REQUIRED'] ? 'Y' : 'N');
	$arProperty['FILTRABLE'] = ('Y' == $arProperty['FILTRABLE'] ? 'Y' : 'N');
	$arProperty['SEARCHABLE'] = ('Y' == $arProperty['SEARCHABLE'] ? 'Y' : 'N');
	$arProperty['ACTIVE'] = ('Y' == $arProperty['ACTIVE'] ? 'Y' : 'N');
	$arProperty['SECTION_PROPERTY'] = ('N' == $arProperty['SECTION_PROPERTY'] ? 'N' : 'Y');
	$arProperty['SMART_FILTER'] = ('Y' == $arProperty['SMART_FILTER'] ? 'Y' : 'N');
	$arProperty['MULTIPLE_CNT'] = intval($arProperty['MULTIPLE_CNT']);
	if (0 >= $arProperty['MULTIPLE_CNT'])
		$arProperty['MULTIPLE_CNT'] = DEF_LIST_VALUE_COUNT;
	$arProperty['WITH_DESCRIPTION'] = ('Y' == $arProperty['WITH_DESCRIPTION'] ? 'Y' : 'N');

	$type=explode(":",$_REQUEST["type"]);
	$arProperty['USER_TYPE'] = $type[1];
	$arProperty['PROPERTY_TYPE'] = $type[0];

	$arProperty["ID"] = $PARAMS['ID'];
}


	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

	if ('L' == $arProperty['PROPERTY_TYPE'])
		$arDefPropInfo['MULTIPLE'] = $arProperty['MULTIPLE'];

	$arTypesList = array(
		"S" => GetMessage("BT_ADM_IEP_PROP_TYPE_S"),
		"N" => GetMessage("BT_ADM_IEP_PROP_TYPE_N"),
		"L" => GetMessage("BT_ADM_IEP_PROP_TYPE_L"),
		"F" => GetMessage("BT_ADM_IEP_PROP_TYPE_F"),
		"G" => GetMessage("BT_ADM_IEP_PROP_TYPE_G"),
		"E" => GetMessage("BT_ADM_IEP_PROP_TYPE_E"),
	);

	$aMenu = array(
		array(
			"TEXT" => GetMessage("BT_ADM_IEP_LIST") ,
			"LINK" => 'iblock_property_admin.php?lang='.LANGUAGE_ID.'&IBLOCK_ID='.$intIBlockID.($_REQUEST["admin"]=="Y"? "&admin=Y": "&admin=N"),
			"ICON" => "btn_list",
		),
	);

	if($str_PROPERTY_ID > 0)
	{
		$aMenu[] = array("SEPARATOR"=>"Y");
		$aMenu[] = array(
			"TEXT" => GetMessage("BT_ADM_IEP_DELETE") ,
			"LINK"=>"javascript:jsDelete('frm_prop', '".GetMessage("BT_ADM_IEP_CONFIRM_DEL_MESSAGE")."')",
			"ICON"=>"btn_delete",
		);
	}

	if(!$bReload)
	{
		$context = new CAdminContextMenu($aMenu);
		$context->Show();
	}

	if($strWarning)
		CAdminMessage::ShowOldStyleError($strWarning."<br>");
	elseif($message)
		echo $message->Show();

		$arProperty['USER_TYPE'] = trim($arProperty['USER_TYPE']);
		$arUserType = ('' != $arProperty['USER_TYPE'] ? CIBlockProperty::GetUserType($arProperty['USER_TYPE']) : array());

		$arPropertyFields = array();
		$USER_TYPE_SETTINGS_HTML = "";
		if(array_key_exists("GetSettingsHTML", $arUserType))
			$USER_TYPE_SETTINGS_HTML = call_user_func_array($arUserType["GetSettingsHTML"],
				array(
					$arProperty,
					array(
						"NAME"=>"PROPERTY_USER_TYPE_SETTINGS",
					),
					&$arPropertyFields,
				)
			);

		$PROPERTY_TYPE = $arProperty['PROPERTY_TYPE'].($arProperty['USER_TYPE']? ':'.$arProperty['USER_TYPE']: '');
	?>
	<form method="POST" name="frm_prop" id="frm_prop" action="<?echo $APPLICATION->GetCurPageParam(); ?>" enctype="multipart/form-data">
	<div id="form_content">
	<table>

<?// PROPERTY_TYPE specific properties
	if ('L' == $arProperty['PROPERTY_TYPE'])
	{?><tr>
	<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_APPEARANCE")?></td>
	<td>
		<select id="PROPERTY_LIST_TYPE" name="PROPERTY_LIST_TYPE">
			<option value="L"<?if($arProperty['LIST_TYPE']!="C")echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_APPEARANCE_LIST")?></option>
			<option value="C"<?if($arProperty['LIST_TYPE']=="C")echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_APPEARANCE_CHECKBOX")?></option>
		</select>
	</td>
</tr>
<?
		$bShow = true;
		if (is_array($arPropertyFields["SHOW"]) && in_array("ROW_COUNT", $arPropertyFields["SHOW"]))
			$bShow = true;
		elseif (is_array($arPropertyFields["HIDE"]) && in_array("ROW_COUNT", $arPropertyFields["HIDE"]))
			$bShow = false;

		if ($bShow)
		{?><tr>
			<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_ROW_CNT")?></td>
			<td><input type="text" size="2" maxlength="10" id="PROPERTY_ROW_COUNT" name="PROPERTY_ROW_COUNT" value="<?echo intval($arProperty['ROW_COUNT']); ?>"></td>
		</tr><?
		} elseif(
			is_array($arPropertyFields["SET"]) && array_key_exists("ROW_COUNT", $arPropertyFields["SET"])
		){?>
			<input type="hidden" id="PROPERTY_ROW_COUNT" name="PROPERTY_ROW_COUNT" value="<?echo htmlspecialcharsbx($arPropertyFields["SET"]["ROW_COUNT"])?>">
		<?}
?><tr class="heading"><td colspan="2"><?echo GetMessage("BT_ADM_IEP_PROP_LIST_VALUES")?></td></tr>
<tr>
	<td colspan="2" align="center">
	<table cellpadding="1" cellspacing="0" border="0" class="internal" id="list-tbl">
		<tr class="heading">
			<td><?echo GetMessage("BT_ADM_IEP_PROP_LIST_ID")?></td>
			<td><?echo GetMessage("BT_ADM_IEP_PROP_LIST_XML_ID")?></td>
			<td><?echo GetMessage("BT_ADM_IEP_PROP_LIST_VALUE")?></td>
			<td><?echo GetMessage("BT_ADM_IEP_PROP_LIST_SORT")?></td>
			<td><?echo GetMessage("BT_ADM_IEP_PROP_LIST_DEFAULT")?></td>
		</tr>
	<?
		if ('Y' != $arProperty['MULTIPLE'])
		{
			$boolDef = true;
			if (isset($arProperty['VALUES']) && is_array($arProperty['VALUES']))
			{
				foreach ($arProperty['VALUES'] as &$arListValue)
				{
					if ('Y' == $arListValue['DEF'])
					{
						$boolDef = false;
						break;
					}
				}
				unset($arListValue);
			}
		?><tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td colspan="2"><?echo GetMessage("BT_ADM_IEP_PROP_LIST_DEFAULT_NO")?></td>
		<td style="text-align:center"><input type="radio" name="PROPERTY_VALUES_DEF" value="0" <?if ($boolDef) echo " checked"; ?>> </td>
		</tr>
		<?
		}
		$MAX_NEW_ID = 0;
		if (isset($arProperty['VALUES']) && is_array($arProperty['VALUES']))
		{
			foreach ($arProperty['VALUES'] as $intKey => $arListValue)
			{
				$arPropInfo = array(
					'ID' => $intKey,
					'XML_ID' => $arListValue['XML_ID'],
					'VALUE' => $arListValue['VALUE'],
					'SORT' => (0 < intval($arListValue['SORT']) ? intval($arListValue['SORT']) : '500'),
					'DEF' => ('Y' == $arListValue['DEF'] ? 'Y' : 'N'),
					'MULTIPLE' => $arProperty['MULTIPLE'],
				);
				echo __AddListValueRow($intKey,$arPropInfo);
			}
			$MAX_NEW_ID = sizeof($arProperty['VALUES']);
		}

		for ($i = $MAX_NEW_ID; $i < $MAX_NEW_ID+DEF_LIST_VALUE_COUNT; $i++)
		{
			$intKey = 'n'.$i;
			$arPropInfo = array(
				'ID' => $intKey,
				'XML_ID' => '',
				'VALUE' => '',
				'SORT' => '500',
				'DEF' => 'N',
				'MULTIPLE' => $arProperty['MULTIPLE'],
			);
			echo __AddListValueRow($intKey,$arPropInfo);
		}
		?>
		</table><br>
		<input type="hidden" name="PROPERTY_CNT" id="PROPERTY_CNT" value="<?echo ($MAX_NEW_ID+DEF_LIST_VALUE_COUNT)?>">
		<input type="button" id="propedit_add_btn" name="propedit_add" value="<?echo GetMessage("BT_ADM_IEP_PROP_LIST_MORE")?>">
		</td>
</tr><?
	}
	elseif ("F" == $arProperty['PROPERTY_TYPE'])
	{
		$bShow = true;
		if (is_array($arPropertyFields["SHOW"]) && in_array("COL_COUNT", $arPropertyFields["SHOW"]))
			$bShow = true;
		elseif (is_array($arPropertyFields["HIDE"]) && in_array("COL_COUNT", $arPropertyFields["HIDE"]))
			$bShow = false;

		if ($bShow)
		{?><tr>
			<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_COL_CNT")?></td>
			<td><input type="text" size="2" maxlength="10" name="PROPERTY_COL_COUNT" value="<?echo intval($arProperty['COL_COUNT'])?>"></td>
		</tr><?
		} elseif(
			is_array($arPropertyFields["SET"]) && array_key_exists("COL_COUNT", $arPropertyFields["SET"])
		){?>
		<input type="hidden" name="PROPERTY_COL_COUNT" value="<?echo htmlspecialcharsbx($arPropertyFields["SET"]["COL_COUNT"])?>">
		<?}?>
<tr>
	<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES")?></td>
	<td>
		<input type="text"  size="30" maxlength="255" name="PROPERTY_FILE_TYPE" value="<?echo htmlspecialcharsbx($arProperty['FILE_TYPE']); ?>" id="CURRENT_PROPERTY_FILE_TYPE">
		<select  onchange="if(this.selectedIndex!=0) document.getElementById('CURRENT_PROPERTY_FILE_TYPE').value=this[this.selectedIndex].value">
			<option value="-"></option>
			<option value=""<?if('' == $arProperty['FILE_TYPE'])echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_ANY")?></option>
			<option value="jpg, gif, bmp, png, jpeg"<?if("jpg, gif, bmp, png, jpeg" == $arProperty['FILE_TYPE'])echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_PIC")?></option>
			<option value="mp3, wav, midi, snd, au, wma"<?if("mp3, wav, midi, snd, au, wma" == $arProperty['FILE_TYPE'])echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_SOUND")?></option>
			<option value="mpg, avi, wmv, mpeg, mpe, flv"<?if("mpg, avi, wmv, mpeg, mpe, flv" == $arProperty['FILE_TYPE'])echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_VIDEO")?></option>
			<option value="doc, txt, rtf"<?if("doc, txt, rtf" == $arProperty['FILE_TYPE'])echo " selected"?>><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_DOCS")?></option>
		</select>
	</td>
</tr>
<?
	}
	elseif ("G" == $arProperty['PROPERTY_TYPE'] || "E" == $arProperty['PROPERTY_TYPE'])
	{
		$bShow = false;
		if (is_array($arPropertyFields["SHOW"]) && in_array("COL_COUNT", $arPropertyFields["SHOW"]))
			$bShow = true;
		if ($bShow)
		{?><tr>
			<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_FILE_TYPES_COL_CNT")?></td>
			<td><input type="text" size="2" maxlength="10" name="PROPERTY_COL_COUNT" value="<?echo intval($arProperty['COL_COUNT']);?>"></td>
			</tr>
			<?
		} elseif(
			is_array($arPropertyFields["SET"]) && array_key_exists("COL_COUNT", $arPropertyFields["SET"])
		){?>
			<input type="hidden" name="PROPERTY_COL_COUNT" value="<?echo htmlspecialcharsbx($arPropertyFields["SET"]["COL_COUNT"])?>">
		<?}?>
	<tr>
		<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_LINK_IBLOCK")?></td>
		<td>
		<?
		$b_f = ($arProperty['PROPERTY_TYPE']=="G" || ($arProperty['PROPERTY_TYPE'] == 'E' && $arProperty['USER_TYPE'] == BT_UT_SKU_CODE) ? array("!ID"=>$intIBlockID) : array());
		echo GetIBlockDropDownList(
			$arProperty['LINK_IBLOCK_ID'],
			"PROPERTY_LINK_IBLOCK_TYPE_ID",
			"PROPERTY_LINK_IBLOCK_ID",
			$b_f,
			'class="adm-detail-iblock-types"',
			'class="adm-detail-iblock-list"'
		);
		?>
		</td>
	</tr>
	<?}
	else
	{
		$bShow = true;
		if (is_array($arPropertyFields["HIDE"]) && in_array("COL_COUNT", $arPropertyFields["HIDE"]))
			$bShow = false;
		elseif (is_array($arPropertyFields["HIDE"]) && in_array("ROW_COUNT", $arPropertyFields["HIDE"]))
			$bShow = false;

		if ($bShow)
		{?><tr>
			<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_SIZE")?></td>
			<td>
				<input type="text"  size="2" maxlength="10" name="PROPERTY_ROW_COUNT" value="<?echo intval($arProperty['ROW_COUNT']); ?>"> x <input type="text"  size="2" maxlength="10" name="PROPERTY_COL_COUNT" value="<?echo intval($arProperty['COL_COUNT']); ?>">
			</td>
		</tr>
		<?} else {
			if (is_array($arPropertyFields["SET"]) && array_key_exists("ROW_COUNT", $arPropertyFields["SET"]))
			{?><input type="hidden" name="PROPERTY_ROW_COUNT" value="<?echo htmlspecialcharsbx($arPropertyFields["SET"]["ROW_COUNT"])?>"><?}
			else
			{?><input type="hidden" name="PROPERTY_ROW_COUNT" value="<?echo intval($arProperty['ROW_COUNT'])?>"><?}

			if(is_array($arPropertyFields["SET"]) && array_key_exists("COL_COUNT", $arPropertyFields["SET"]))
			{?><input type="hidden" name="PROPERTY_COL_COUNT" value="<?echo htmlspecialcharsbx($arPropertyFields["SET"]["COL_COUNT"])?>"><? }
			else
			{ ?><input type="hidden" name="PROPERTY_COL_COUNT" value="<?echo intval($arProperty['COL_COUNT']); ?>"><? }
		}

		$bShow = true;
		if (is_array($arPropertyFields["HIDE"]) && in_array("DEFAULT_VALUE", $arPropertyFields["HIDE"]))
			$bShow = false;

		if ($bShow)
		{?><tr>
			<td width="40%"><?echo GetMessage("BT_ADM_IEP_PROP_DEFAULT")?></td>
			<td>
			<?if(array_key_exists("GetPropertyFieldHtml", $arUserType))
			{
				echo call_user_func_array($arUserType["GetPropertyFieldHtml"],
					array(
						$arProperty,
						array(
							"VALUE"=>$arProperty["DEFAULT_VALUE"],
							"DESCRIPTION"=>""
						),
						array(
							"VALUE"=>"PROPERTY_DEFAULT_VALUE",
							"DESCRIPTION"=>"",
							"MODE" => "EDIT_FORM",
							"FORM_NAME" => "frm_prop"
						),
					));
			} else {
				?><input type="text"  size="40" maxlength="2000" name="PROPERTY_DEFAULT_VALUE" value="<?echo htmlspecialcharsbx($arProperty['DEFAULT_VALUE']);?>"><?
			}
		?></td>
	</tr><?
		}
	}
	if ($USER_TYPE_SETTINGS_HTML)
	{?><tr class="heading"><td colspan="2"><?
		echo (isset($arPropertyFields["USER_TYPE_SETTINGS_TITLE"]) && '' != trim($arPropertyFields["USER_TYPE_SETTINGS_TITLE"]) ? $arPropertyFields["USER_TYPE_SETTINGS_TITLE"] : GetMessage("BT_ADM_IEP_PROP_USER_TYPE_SETTINGS"));
		?></td></tr><?
		echo $USER_TYPE_SETTINGS_HTML;
	}

	if(is_object($tabControl))
	{
		if (!defined('BX_PUBLIC_MODE') || BX_PUBLIC_MODE != 1):
			$tabControl->Buttons(array(
				"disabled"=>false,
				"back_url"=>'iblock_property_admin.php?lang='.LANGUAGE_ID.'&IBLOCK_ID='.$intIBlockID.($_REQUEST["admin"]=="Y"? "&admin=Y": "&admin=N"),
			));
		else:
			$tabControl->ButtonsPublic(array(
				'.btnSave',
				'.btnCancel'
			));
		endif;
		$tabControl->End();
	}
	else
	{
		?><?
	}
	?></table></div></form><?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>