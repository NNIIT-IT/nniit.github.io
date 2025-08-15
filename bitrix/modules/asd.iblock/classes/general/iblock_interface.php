<?php

IncludeModuleLangFile(__FILE__);

class CASDiblock {
	public static $error = '';
	public static $UF_IBLOCK = 'ASD_IBLOCK';
}

class CASDiblockInterface {

	/**
	 * @param CAdminList &$list
	 * @return void
	 */
	public static function OnAdminListDisplayHandler(&$list) {


		$strCurPage = $GLOBALS['APPLICATION']->GetCurPage();
		$bElemPage = ($strCurPage=='/bitrix/admin/iblock_element_admin.php' ||
						$strCurPage=='/bitrix/admin/cat_product_admin.php'
					);
		$bSectPage = ($strCurPage=='/bitrix/admin/iblock_section_admin.php' ||
						$strCurPage=='/bitrix/admin/cat_section_admin.php'
					);
		$bMixPage = ($strCurPage=='/bitrix/admin/iblock_list_admin.php');
		$bRightPage = ($bElemPage || $bSectPage || $bMixPage);

		if ($bRightPage && !empty($list->arActions)) {
			CJSCore::Init(array('asd_iblock'));
			$strSomeScripts  = '<script type="text/javascript">sListTable = \''.$list->table_id.'\';</script>';
			$list->arActions['asd_checkbox_manager'] = array('type' => 'html', 'value' => $strSomeScripts);
		}

		if ($bRightPage && !$list->bEditMode && !isset($_REQUEST['mode'])) {
			CJSCore::Init(array('asd_element_list'));
			$jsParams = array(
				'resultDiv' => $list->table_id.'_result_div'
			);
			$list->sPrologContent .= '<script type="text/javascript">
var asdElementList'.$list->table_id.' = new AsdIblockElementList('.CUtil::PhpToJSObject($jsParams, false, true, true).');
</script>';
		}

		if ($bMixPage || $strCurPage=='/bitrix/admin/iblock_element_admin.php' ||
			$strCurPage=='/bitrix/admin/iblock_section_admin.php'
		) {
			$list->context->additional_items[] = array(
				'TEXT' => GetMessage('ASD_IBLOCK_SETT_SECT_MODE'),
				'TITLE' => GetMessage('ASD_IBLOCK_SETT_SECT_MODE_TITLE'),
				'GLOBAL_ICON' => 'adm-menu-setting',
				'ONCLICK' => "location.href='".htmlspecialcharsbx($GLOBALS['APPLICATION']->GetCurPageParam('action=asd_reverse&'.bitrix_sessid_get(), array('action')))."'"
			);
		}

		if ($bRightPage && CModule::IncludeModule('iblock')) {
			if (!empty(CASDiblock::$error)) {
				$list->AddGroupError(CASDiblock::$error);
			}

			$IBLOCK_ID = intval($_REQUEST['IBLOCK_ID']);
			$find_section = intval($_REQUEST['find_section_section']);
			if ($find_section < 0)
				$find_section = 0;

			$boolSectionCopy = CASDIblockRights::IsSectionSectionCreate($IBLOCK_ID, $find_section);
			$boolElementCopy = CASDIblockRights::IsSectionElementCreate($IBLOCK_ID, $find_section);

			$copyMessageId = 'ASD_ACTION_POPUP_COPY';
			$copyContextMessageId = 'ASD_ACTION_COPY';
			$moveContextMessageId = 'ASD_ACTION_MOVE';
			if (CModule::IncludeModule('catalog')) {
				$productIBlock = CCatalog::GetList(array(), array('PRODUCT_IBLOCK_ID' => $IBLOCK_ID), false, false, array('IBLOCK_ID'))->Fetch();
				if (!empty($productIBlock)) {
					$copyMessageId = 'ASD_ACTION_POPUP_COPY_WITHOUT_SKU';
					$copyContextMessageId = 'ASD_ACTION_COPY_WITHOUT_SKU';
					$moveContextMessageId = 'ASD_ACTION_MOVE_WITHOUT_SKU';
				}
				unset($productIBlock);
			}

			if ($bElemPage) {
				if ($boolElementCopy) {
					foreach ($list->aRows as $id => $v) {
						$arnewActions = array();
						foreach ($v->aActions as $i => $act) {
							$arnewActions[] = $act;
							if ($act['ICON'] == 'copy') {
								$arnewActions[] = array('ICON' => 'copy',
													'TEXT' => GetMessage($copyMessageId),
													'ACTION' => $list->ActionDoGroup($v->id, 'asd_copy_in_list',
																'&type='.urlencode($_REQUEST['type']).'&lang='.LANGUAGE_ID.'&IBLOCK_ID='.$IBLOCK_ID.'&find_section_section='.$find_section),
													);
							}
						}
						$v->AddActions($arnewActions);
					}
				}
			} elseif ($bSectPage) {
				if ($boolSectionCopy) {
					foreach ($list->aRows as $id => $v) {
						$arnewActions = array();
						foreach ($v->aActions as $i => $act) {
							$arnewActions[] = $act;
							if ($act['ICON'] == 'edit') {
								$arnewActions[] = array('ICON' => 'copy',
														'TEXT' => GetMessage('ASD_ACTION_POPUP_COPY'),
														'ACTION' => $list->ActionDoGroup($v->id, 'asd_copy_in_list',
																	'&type='.urlencode($_REQUEST['type']).'&lang='.LANGUAGE_ID.'&IBLOCK_ID='.$IBLOCK_ID.'&find_section_section='.$find_section),
														);
							}
						}
						$v->AddActions($arnewActions);
					}
				}
			} else {
				foreach ($list->aRows as $id => $v) {
					$strPrefix = substr($v->id, 0, 1);
					if ($strPrefix == 'E') {
						if ($boolElementCopy) {
							$arnewActions = array();
							foreach ($v->aActions as $i => $act) {
								$arnewActions[] = $act;
								if ($act['ICON'] == 'copy') {
									$arnewActions[] = array('ICON' => 'copy',
														'TEXT' => GetMessage($copyMessageId),
														'ACTION' => $list->ActionDoGroup($v->id, 'asd_copy_in_list',
																	'&type='.urlencode($_REQUEST['type']).'&IBLOCK_ID='.$IBLOCK_ID.'&find_section_section='.$find_section),
														);
								}
							}
							$v->AddActions($arnewActions);
						}
					}
					elseif ($strPrefix == 'S')
					{
						if ($boolSectionCopy) {
							$arnewActions = array();
							foreach ($v->aActions as $i => $act) {
								$arnewActions[] = $act;
								if ($act['ICON'] == 'edit') {
									$arnewActions[] = array('ICON' => 'copy',
														'TEXT' => GetMessage('ASD_ACTION_POPUP_COPY'),
														'ACTION' => $list->ActionDoGroup($v->id, 'asd_copy_in_list',
																	'&type='.urlencode($_REQUEST['type']).'&lang='.LANGUAGE_ID.'&IBLOCK_ID='.$IBLOCK_ID.'&find_section_section='.$find_section),
														);
								}
							}
							$v->AddActions($arnewActions);
						}
					}
				}
			}

			$arIBtypes = array();
			$rsIBtype = CIBlockType::GetList();
			while($arIBtype = $rsIBtype->Fetch()) {
				if ($arIBTypeLang = CIBlockType::GetByIDLang($arIBtype['ID'], LANGUAGE_ID)) {
					$arIBtypes[$arIBTypeLang['IBLOCK_TYPE_ID']] = $arIBTypeLang['NAME'];
				}
			}

			$arIBblocks = array();
			$rsIB = CIBlock::GetList(array("IBLOCK_TYPE" => "ASC", "NAME" => "ASC"), array("MIN_PERMISSION" => "W"));
			while ($arIB = $rsIB->GetNext(true, false)) {
				if (!isset($arIBblocks[$arIB['IBLOCK_TYPE_ID']])) {
					$arIBblocks[$arIB['IBLOCK_TYPE_ID']] = array('NAME' => $arIBtypes[$arIB['IBLOCK_TYPE_ID']], 'ITEMS' => array());
				}
				$arIBblocks[$arIB['IBLOCK_TYPE_ID']]['ITEMS'][] = array('ID' => $arIB['ID'], 'NAME' => $arIB['NAME']);
			}

			if (CASDiblockVersion::isIblockNewGridv18()) {
				if (CASDIblockRights::IsSectionElementEdit($IBLOCK_ID, $find_section) && ($bElemPage || $bMixPage)) {
					$list->arActions['asd_remove'] = GetMessage('ASD_ACTION_REMOVE');
				}

				$multiControl = CASDModuleVersion::checkMinVersion('main', '18.1.8');

				$iblockListCopy = array();
				$iblockListMove = array();
				if (!empty($arIBblocks)) {
					foreach ($arIBblocks as $arType) {
						foreach ($arType['ITEMS'] as $arIB) {
							$row = array(
								'NAME' => '['.$arIB['ID'].'] '.$arIB['NAME'],
								'VALUE' => $arIB['ID'],

							);
							if ($multiControl) {
								$row['ONCHANGE'] = array(
									array(
										'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CALLBACK,
										'DATA' => array(
											array(
												'JS' => "ASDSetCurrentIblock('asd_sect_iblock_id_copy', ".$arIB['ID'].");"
											)
										)
									)
								);
							}
							$iblockListCopy[] = $row;
							if ($arIB['ID'] != $IBLOCK_ID) {
								$row = array(
									'NAME' => '['.$arIB['ID'].'] '.$arIB['NAME'],
									'VALUE' => $arIB['ID']
								);
								if ($multiControl) {
									$row['ONCHANGE'] = array(
										array(
											'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CALLBACK,
											'DATA' => array(
												array(
													'JS' => "ASDSetCurrentIblock('asd_sect_iblock_id_move', ".$arIB['ID'].");"
												)
											)
										)
									);
								}
								$iblockListMove[] = $row;
							}
						}
						unset($arIB);
					}
					unset($arType);
				}

				if ($multiControl) {
					$snippet = new \Bitrix\Main\Grid\Panel\Snippet();

					if (!empty($iblockListCopy)) {
						$list->arActions['asd_copy'] = array(
							'name' => GetMessage($copyContextMessageId),
							'type' => 'multicontrol',
							'action' => array(
								array(
									'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CREATE,
									'DATA' => array(
										array(
											'TYPE' => \Bitrix\Main\Grid\Panel\Types::DROPDOWN,
											'ID' => 'asd_ib_dest_copy',
											'NAME' => 'asd_ib_dest',
											'ITEMS' => $iblockListCopy
										),
										array(
											'TYPE' => \Bitrix\Main\Grid\Panel\Types::TEXT,
											'ID' => 'asd_sect_id_copy',
											'NAME' => 'asd_sect_dest',
											'VALUE' => '0'
										),
										array(
											'TYPE' => \Bitrix\Main\Grid\Panel\Types::HIDDEN,
											'ID' => 'asd_sect_iblock_id_copy',
											'NAME' => ''
										),
										array(
											'TYPE' => \Bitrix\Main\Grid\Panel\Types::CUSTOM,
											'ID' => 'asd_sect_name_copy',
											'VALUE' => '<span id="asd_sect_id_copy_control_link"></span>'
										),
										array(
											'TYPE' => \Bitrix\Main\Grid\Panel\Types::BUTTON,
											'ID' => 'asd_sect_id_btn_copy',
											'TEXT' => GetMessage('ASD_SELECT_BUTTON_ACTION'),
											'TITLE' => GetMessage('ASD_SELECT_BUTTON_DESCR'),
											'ONCHANGE' => array(
												array(
													'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CALLBACK,
													'DATA' => array(
														array(
															'JS' => "ASDShowSectionWindow('asd_sect_iblock_id_copy', 'asd_sect_id_copy', '".LANGUAGE_ID."');"
														)
													)
												)
											)
										),
										$snippet->getApplyButton(array(
											'ONCHANGE' => array(
												array(
													'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CALLBACK,
													'DATA' => array(
														array(
															'JS' => "BX.adminUiList.SendSelected('{$list->table_id}');"
														)
													)
												)
											)
										))
									)
								),
								array(
									'ACTION' => \Bitrix\Main\Grid\Panel\Actions::REMOVE,
									'DATA' => array(
										array('ID' => 'asd_sect_id_move'),
										array('ID' => 'asd_sect_iblock_id_move'),
										array('ID' => 'asd_sect_name_move'),
										array('ID' => 'asd_sect_id_btn_move')
									)
								)
							)
						);
					}
					if (!empty($iblockListMove)) {
						$list->arActions['asd_move'] = array(
							'name' => GetMessage($moveContextMessageId),
							'type' => 'multicontrol',
							'action' => array(
								array(
									'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CREATE,
									'DATA' => array(
										array(
											'TYPE' => \Bitrix\Main\Grid\Panel\Types::DROPDOWN,
											'ID' => 'asd_ib_dest_move',
											'NAME' => 'asd_ib_dest',
											'ITEMS' => $iblockListMove
										),
										array(
											'TYPE' => \Bitrix\Main\Grid\Panel\Types::TEXT,
											'ID' => 'asd_sect_id_move',
											'NAME' => 'asd_sect_dest',
											'VALUE' => '0'
										),
										array(
											'TYPE' => \Bitrix\Main\Grid\Panel\Types::HIDDEN,
											'ID' => 'asd_sect_iblock_id_move',
											'NAME' => ''
										),
										array(
											'TYPE' => \Bitrix\Main\Grid\Panel\Types::CUSTOM,
											'ID' => 'asd_sect_name_move',
											'VALUE' => '<span id="asd_sect_id_move_control_link"></span>'
										),
										array(
											'TYPE' => \Bitrix\Main\Grid\Panel\Types::BUTTON,
											'ID' => 'asd_sect_id_btn_move',
											'TEXT' => GetMessage('ASD_SELECT_BUTTON_ACTION'),
											'TITLE' => GetMessage('ASD_SELECT_BUTTON_DESCR'),
											'ONCHANGE' => array(
												array(
													'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CALLBACK,
													'DATA' => array(
														array(
															'JS' => "ASDShowSectionWindow('asd_sect_iblock_id_move', 'asd_sect_id_move', '".LANGUAGE_ID."');"
														)
													)
												)
											)
										),
										$snippet->getApplyButton(array(
											'ONCHANGE' => array(
												array(
													'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CALLBACK,
													'DATA' => array(
														array(
															'JS' => "BX.adminUiList.SendSelected('{$list->table_id}');"
														)
													)
												)
											)
										))
									)
								),
								array(
									'ACTION' => \Bitrix\Main\Grid\Panel\Actions::REMOVE,
									'DATA' => array(
										array('ID' => 'asd_sect_id_copy'),
										array('ID' => 'asd_sect_iblock_id_copy'),
										array('ID' => 'asd_sect_name_copy'),
										array('ID' => 'asd_sect_id_btn_copy')
									)
								)
							)
						);
					}
				} else
				{
					if (!empty($iblockListCopy)) {
						$list->arActions['asd_copy'] = array(
							'lable' => GetMessage($copyContextMessageId),
							'label' => GetMessage($copyContextMessageId),
							'type' => 'select',
							'name' => 'asd_ib_dest',
							'items' => $iblockListCopy
						);
					}
					if (!empty($iblockListMove)) {
						$list->arActions['asd_move'] = array(
							'lable' => GetMessage($moveContextMessageId),
							'label' => GetMessage($moveContextMessageId),
							'type' => 'select',
							'name' => 'asd_ib_dest',
							'items' => $iblockListMove
						);
					}
				}
				unset($iblockListMove);
				unset($iblockListCopy);
			} else {
				$boolAccess = false;
				$strIBlocksCp = '<div id="asd_ib_dest_cont" style="display:none; "><select class="typeselect" name="asd_ib_dest" id="asd_ib_dest">';
				foreach ($arIBblocks as &$arType) {
					$strIBlocksCpGr = '';
					foreach ($arType['ITEMS'] as &$arIB) {
						if (CASDIblockRights::IsIBlockDisplay($arIB['ID'])) {
							$boolAccess = true;
							$strIBlocksCpGr .= '<option value="' . $arIB['ID'] . '">' . $arIB['NAME'] . '</option>';
						}
					}
					if (isset($arIB)) {
						unset($arIB);
					}
					if ($strIBlocksCpGr != '') {
						$strIBlocksCp .= '<optgroup label="' . $arType['NAME'] . '">';
						$strIBlocksCp .= $strIBlocksCpGr;
						$strIBlocksCp .= '</optgroup>';
					}
				}
				if (isset($arType)) {
					unset($arType);
				}
				$strIBlocksCp .= '</select></div>';

				$strSectionSelect = '<div id="asd_ib_dest_sect" class="asd-sect-cont" style="display:none;" title="'.htmlspecialcharsbx(GetMessage('ASD_SELECT_SECTION_DESCR')).'">'.
								htmlspecialcharsex(GetMessage('ASD_SELECT_SECTION')).
								'&nbsp;<input class="asd-sect-input" type="text" id="asd_sect_id" value="" name="asd_sect_dest" size="4" title="">'.
								'<span id="sp_asd_sect_id" class="asd-sect-descr"></span>'.
								'<input type="button" onclick="ASDSelIBShow(\''.LANGUAGE_ID.'\');" value="'.
								htmlspecialcharsbx(GetMessage('ASD_SELECT_BUTTON')).
								'" title="'.htmlspecialcharsbx(GetMessage('ASD_SELECT_BUTTON_DESCR')).'"></div>';

				if (CASDIblockRights::IsSectionElementEdit($IBLOCK_ID, $find_section) && ($bElemPage || $bMixPage)) {
					$list->arActions['asd_remove'] = GetMessage('ASD_ACTION_REMOVE');
				}

				if ($boolAccess) {
					$list->arActions['asd_copy'] = GetMessage($copyContextMessageId);
					if ($bElemPage || $bMixPage) {
						$list->arActions['asd_move'] = GetMessage($moveContextMessageId);
					}
					$list->arActions['asd_copy_move'] = array('type' => 'html', 'value' => $strIBlocksCp);
					$list->arActions['asd_copy_move_sect'] = array('type' => 'html', 'value' => $strSectionSelect);

					$list->arActionsParams['select_onchange'] .= "ASDSelIBChange(this.value);";
				}
			}
		}
	}

	/**
	 * @param CAdminSubList &$list
	 * @return void
	 */
	public static function OnAdminSubListDisplayHandler(&$list) {
		$strCurPage = $GLOBALS['APPLICATION']->GetCurPage();
		$bElemPage = ($strCurPage=='/bitrix/admin/iblock_element_edit.php' ||
			$strCurPage=='/bitrix/admin/cat_product_edit.php'
		);

		if ($bElemPage && !$list->bEditMode && !isset($_REQUEST['mode'])) {
			CJSCore::Init(array('asd_element_list'));
			$jsParams = array(
				'resultDiv' => $list->table_id.'_result_div'
			);
			$list->sPrologContent .= '<script type="text/javascript">
var asdElementList'.$list->table_id.' = new AsdIblockElementList('.CUtil::PhpToJSObject($jsParams, false, true, true).');
</script>';
			}
	}

	public static function OnAdminContextMenuShowHandler(&$items) {
		if ($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_edit.php' && $_REQUEST['ID']>0) {
			CJSCore::Init(array('asd_iblock'));
			$BID = intval($_REQUEST['ID']);
			$importAction = "javascript:(new BX.CDialog({
							width: 310,
							height: 110,
							resizable: false,
							title: '".GetMessage('ASD_ACTION_IMPORT_FORM')."',
							content: '<form action=\"".CUtil::JSEscape($GLOBALS['APPLICATION']->GetCurPageParam('', array('action')))."\" method=\"post\" enctype=\"multipart/form-data\">"
										.bitrix_sessid_post()
										."<input type=\"hidden\" name=\"action\" value=\"asd_prop_import\" />"
										."<input type=\"hidden\" name=\"ID\" value=\"".$BID."\" />"
										."<input type=\"hidden\" name=\"type\" value=\"".htmlspecialcharsbx($_REQUEST['type'])."\" />"
										."<input type=\"file\" name=\"xml_file\" /><br/><br/>"
										."<center><input type=\"submit\" value=\"".GetMessage('ASD_ACTION_IMPORT_SUBMIT')."\" /></center>"
									."</form>'
						})).Show()";
			$exportAction = "javascript:(new BX.CDialog({
							width: 310,
							height: 200,
							resizable: false,
							title: '".GetMessage('ASD_ACTION_EXPORT_FORM')."',
							buttons: [BX.CAdminDialog.btnSave, BX.CAdminDialog.btnCancel],
							content: '<form action=\"".CUtil::JSEscape($GLOBALS['APPLICATION']->GetCurPageParam('', array('action')))."\" method=\"post\" enctype=\"multipart/form-data\">"
										.bitrix_sessid_post()
										."<input type=\"hidden\" name=\"action\" value=\"asd_prop_export\" />"
										."<input type=\"hidden\" name=\"ID\" value=\"".$BID."\" />";

			$exportAction .= '<input type="checkbox" name="forms" id="forms" value="Y" />'.
							'<label for="forms">'.GetMessage('ASD_ACTION_EXPORT_FORMS').'</label><br/><br/>';
			$exportAction .= '<input type="checkbox" id="asd_export_prop_all" checked="checked" />'.
							'<label for="asd_export_prop_all"><i>'.GetMessage('ASD_ACTION_EXPORT_ALL').'</i></label><br/>';
			$rsProp = CIBlockProperty::GetList(array(), array('IBLOCK_ID' => $BID));
			while ($arProp = $rsProp->GetNext()) {
				$exportAction .= '<input type="checkbox" class="asd_export_prop" name="p['.$arProp['ID'].']" id="p'.$arProp['ID'].'" value="Y" checked="checked" />'.
								'<label for="p'.$arProp['ID'].'" title="'.$arProp['CODE'].'">'.$arProp['NAME'].'</label><br/>';
			}

			$exportAction .=	"</form>'
							})).Show()";
			$items[] = array(
				'TEXT' => GetMessage('ASD_ACTION_EXPORT_IMPORT'),
				'TITLE' => GetMessage('ASD_ACTION_EXPORT_IMPORT_TITLE'),
				'LINK' => '#',
				'ICON' => 'btn_settings',
				'MENU' => array(
					array(
						'TEXT' => GetMessage('ASD_ACTION_EXPORT_PROP'),
						'ACTION' => version_compare(SM_VERSION, '11.5.5')>=0 ?  $exportAction : htmlspecialcharsbx($exportAction),
					),
					array(
						'TEXT' => GetMessage('ASD_ACTION_IMPORT_PROP'),
						'ACTION' => version_compare(SM_VERSION, '11.5.5')>=0 ?  $importAction : htmlspecialcharsbx($importAction),
					),
				),
			);
		}
		if (($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_element_edit.php' ||
			$GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/cat_product_edit.php') && $_REQUEST['ID']>0 &&
			(!isset($_REQUEST['action']) && $_REQUEST['action']!='copy')
		) {
			if ($arElement = CIBlockElement::GetByID($_REQUEST['ID'])->GetNext()) {
				if (strlen($arElement['CANONICAL_PAGE_URL'])) {
					$items[] = array('ICON' => 'asd_iblock_show_element',
									'TEXT' => GetMessage('ASD_ACTION_VIEW_DETAIL'),
									'LINK' => str_replace('%2F', '/', $arElement['CANONICAL_PAGE_URL']),
									);
				}
				else if (strlen($arElement['DETAIL_PAGE_URL'])) {
					$items[] = array('ICON' => 'asd_iblock_show_element',
									'TEXT' => GetMessage('ASD_ACTION_VIEW_DETAIL'),
									'LINK' => str_replace('%2F', '/', $arElement['DETAIL_PAGE_URL']),
									);
				}
			}
		}
	}

	public static function OnAdminTabControlBeginHandler(&$form) {
		static $bPublicLinkShow = false;
		if (!$bPublicLinkShow && array_key_exists('ID', $_REQUEST) && intval($_REQUEST['ID'])>0 && $_REQUEST['bxpublic']=='Y' &&
			($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_element_edit.php' ||
			$GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/cat_product_edit.php')
			&& !CASDiblockVersion::checkMinVersion('15.5.8')
		) {
			$bPublicLinkShow = true;
			?>
			<div style="float: right">
				<a style="text-decoration: none;" href="/bitrix/admin/iblock_element_edit.php?ID=<?= intval($_REQUEST['ID'])?>&amp;<?
						?>type=<?= htmlspecialcharsback($_REQUEST['type'])?>&amp;<?
						?>lang=<?= LANGUAGE_ID?>&amp;<?
						?>IBLOCK_ID=<?= $_REQUEST['IBLOCK_ID']?>"><?= GetMessage('ASD_IBLOCK_IN_ADMIN')?></a>
			</div>
			<?
		} elseif ($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_edit.php' &&
			array_key_exists('ID', $_REQUEST) && intval($_REQUEST['ID'])>0
		) {
			global $USER_FIELD_MANAGER, $APPLICATION;
			$ID = intval($_REQUEST['ID']);
			$PROPERTY_ID = CASDiblock::$UF_IBLOCK;
			$bVarsFromForm = $_SERVER['REQUEST_METHOD']=='POST';
			if ($USER_FIELD_MANAGER->GetRights($PROPERTY_ID) >= 'W') {
				ob_start();
				if(method_exists($USER_FIELD_MANAGER, 'showscript')) {
					echo $USER_FIELD_MANAGER->ShowScript();
				}
				?>
				<tr>
					<td colspan="2" align="left">
						<a href="/bitrix/admin/userfield_edit.php?lang=<?= LANGUAGE_ID?><?
						?>&amp;ENTITY_ID=<?= urlencode($PROPERTY_ID)?>&amp;back_url=<?= urlencode($APPLICATION->GetCurPageParam().'&tabControl_active_tab=user_fields_tab')?><?
						?>"><?= GetMessage('ASD_IBLOCK_ADD_UF')?></a>
					</td>
				</tr>
				<?
				$arUserFields = $USER_FIELD_MANAGER->GetUserFields($PROPERTY_ID, $ID, LANGUAGE_ID);
				foreach($arUserFields as $FIELD_NAME => $arUserField) {

					$arUserField['VALUE_ID'] = $ID;
					if (isset($_REQUEST['def_'.$FIELD_NAME])) {
						$arUserField['SETTINGS']['DEFAULT_VALUE'] = $_REQUEST['def_'.$FIELD_NAME];
					}

					echo $USER_FIELD_MANAGER->GetEditFormHTML($bVarsFromForm, $GLOBALS[$FIELD_NAME], $arUserField);
				}
				$strContent = ob_get_contents();
				ob_end_clean();

				$arTab = $GLOBALS['USER_FIELD_MANAGER']->EditFormTab($PROPERTY_ID);
				$arTab['CONTENT'] = $strContent;
				$form->tabs[] = $arTab;
			}
		}
	}
}