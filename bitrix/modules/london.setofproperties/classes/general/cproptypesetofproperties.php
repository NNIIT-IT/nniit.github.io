<?php
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
?>
<?class CPropertySetOfProperties{
 
//описываем поведение пользовательского свойства
function GetIBlockPropertyDescription() {
       return array(
           'PROPERTY_TYPE'           => 'S',
           'USER_TYPE'             	=> 'SET',
           'DESCRIPTION'           	=> GetMessage("LONDON_SETOFPROPERTIES_NABOR_SVOYSTV"),
	            //указываем необходимые функции, используемые в создаваемом типе
           'GetPropertyFieldHtml'  	=> array('CPropertySetOfProperties', 'GetPropertyFieldHtml'),
           'ConvertToDB'           	=> array('CPropertySetOfProperties', 'ConvertToDB'),
           'ConvertFromDB'         	=> array('CPropertySetOfProperties', 'ConvertFromDB'),
           'GetSettingsHTML'         	=> array('CPropertySetOfProperties', 'GetSettingsHTML'),
           'GetPublicEditHTML'         	=> array('CPropertySetOfProperties', 'GetPublicEditHTML'),
           'PrepareSettings'         	=> array('CPropertySetOfProperties', 'PrepareSettings'),
           'GetAdminListViewHTML'         	=> array('CPropertySetOfProperties', 'GetAdminListViewHTML')
                   );
       }
//формируем пару полей для создаваемого свойства

function GetUserTypeDescription()
 {
	  return array(
	   "USER_TYPE_ID" => "SET",
	   "CLASS_NAME" => "CPropertySetOfProperties",
	   "DESCRIPTION" => GetMessage("LONDON_SETOFPROPERTIES_NABOR_SVOYSTV"),
	   "BASE_TYPE" => "string",
	  );
 }


function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {
			
			if ($arProperty["USER_TYPE_SETTINGS"]){
				preg_match("/PROP\[[0-9]+\]\[([0-9:]+)\].*/ui",$strHTMLControlName["VALUE"], $match);
				$valID =  $match[1]? $match[1]:"n0";
				//$html=json_encode($arProperty["USER_TYPE_SETTINGS"], JSON_UNESCAPED_UNICODE);
				foreach($arProperty["USER_TYPE_SETTINGS"] as $property){
					
					$arType = explode(":",$property["TYPE"]);
					$property["PROPERTY_TYPE"]=$arType[0];
					$property["USER_TYPE"]=$arType[1];
					$property["ID"]=$arProperty["ID"];
					
					$property["VALUE"]=array($valID =>$value["VALUE"][$property["CODE"]]);
					$property["~VALUE"]=$property["VALUE"];
					$html.= "<div class='London_SetLine'><div class='London_title'>".$property["NAME"]."</div>";
			
					if ($property["TYPE"]!="F"){
						ob_start();
						_ShowPropertyField($strHTMLControlName["VALUE"]."[0][".$property["CODE"]."]",$property,$property["VALUE"], false,false,50000,$strHTMLControlName["FORM_NAME"],$strHTMLControlName["COPY"]);
						$input = ob_get_contents();
						ob_end_clean();
					}
					else{

						
						ob_start();
						_ShowPropertyField($strHTMLControlName["VALUE"]."[0][".$property["CODE"]."]",$property,$property["VALUE"], false,false,50000,$strHTMLControlName["FORM_NAME"],$strHTMLControlName["COPY"]);
						$input = ob_get_contents();
						ob_end_clean();
						
					}
					
					 
					$input = str_replace("[VALUE][VALUE]","[VALUE]",$input);
					$input = str_replace("[VALUE][0][$property[CODE]][$valID]","[VALUE][0][$property[CODE]]",$input);
					$input = str_replace("[$property[CODE]][]","[$property[CODE]]",$input);
					if (substr_count($input,"[".$property["CODE"]."]")==0){
						$input = str_replace("[VALUE]","[VALUE][0][$property[CODE]]",$input);
					} 
					
					$html.= "<div class='London_table'>".$s.$input."</div>";
					$html.= "</div>";
				}
			}
			$html.= "<style>
					#tr_PROPERTY_$arProperty[ID] #London_SetLine{padding: 0px 15px 9px 0px; overflow:hidden;}
					#tr_PROPERTY_$arProperty[ID] #London_table { float: left; width:300px}
					#tr_PROPERTY_$arProperty[ID] #London_title {
						float: left;
						width: 180px;
						padding: 0px 0px 4px;
					}
			</style><br/>";
			return $html;
   }
  
   function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
   {
		$arViewHTML = array();
		foreach($arProperty["USER_TYPE_SETTINGS"] as $aProp){

			$arEditHTML = array();
			$arType = explode(":",$aProp["TYPE"]);
			$aProp["PROPERTY_TYPE"]=$arType[0];
			$aProp["USER_TYPE"]=$arType[1];
			$aProp["ID"]=$arProperty["ID"];
			$aProp["VALUE"]=$value["VALUE"][$aProp["CODE"]];
			$aProp["~VALUE"]=$aProp["VALUE"];
			
			preg_match("/PROP\[[0-9]+\]\[([0-9]+)\].*/ui",$strHTMLControlName["VALUE"], $match);
			$valID =  $match[1]? $match[1]:"VALUE";
			
			if(strlen($aProp["USER_TYPE"])>0)
				$arUserType = CIBlockProperty::GetUserType($aProp["USER_TYPE"]);
			else
				$arUserType = array();
			$max_file_size_show=100000;

			$last_property_id = false;
			$prop_id = $arProperty["ID"];
			
			$prop = $aProp;
				$prop['PROPERTY_VALUE_ID'] = intval($valID);
				$VALUE_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].']['.$prop['PROPERTY_VALUE_ID'].'][VALUE]';
				$DESCR_NAME = 'FIELDS['.$f_ID.'][PROPERTY_'.$prop['ID'].']['.$prop['PROPERTY_VALUE_ID'].'][DESCRIPTION]';
				//View part
				if(array_key_exists("GetAdminListViewHTML", $arUserType))
				{
					$arViewHTML[] = call_user_func_array($arUserType["GetAdminListViewHTML"],
						array(
							$prop,
							array(
								"VALUE" => $prop["~VALUE"],
								"DESCRIPTION" => $prop["~DESCRIPTION"]
							),
							array(
								"VALUE" => $VALUE_NAME,
								"DESCRIPTION" => $DESCR_NAME,
								"MODE"=>"iblock_element_admin",
								"FORM_NAME"=>"form_".$sTableID,
							),
						));
				}
				elseif($prop['PROPERTY_TYPE']=='N')
					$arViewHTML[] = $prop["VALUE"];
				elseif($prop['PROPERTY_TYPE']=='S')
					$arViewHTML[] = $prop["VALUE"];
				elseif($prop['PROPERTY_TYPE']=='L')
					$arViewHTML[] = $prop["VALUE_ENUM"];
				elseif($prop['PROPERTY_TYPE']=='F')
				{
					
					$arViewHTML[] = CFileInput::Show('NO_FIELDS['.$prop['PROPERTY_VALUE_ID'].']', $prop["VALUE"], array(
						"IMAGE" => "Y",
						"PATH" => "Y",
						"FILE_SIZE" => "Y",
						"DIMENSIONS" => "Y",
						"IMAGE_POPUP" => "Y",
						"MAX_SIZE" => $maxImageSize,
						"MIN_SIZE" => $minImageSize,
						), array(
							'upload' => false,
							'medialib' => false,
							'file_dialog' => false,
							'cloud' => false,
							'del' => false,
							'description' => false,
						)
					);
				}
				elseif($prop['PROPERTY_TYPE']=='G')
				{
					if(intval($prop["VALUE"])>0)
					{
						$rsSection = CIBlockSection::GetList(Array(), Array("ID" => $prop["VALUE"]));
						if($arSection = $rsSection->GetNext())
						{
							$arViewHTML[] = $arSection['NAME'].
							' [<a href="'.
							htmlspecialcharsbx(CIBlock::GetAdminSectionEditLink($arSection['IBLOCK_ID'], $arSection['ID'])).
							'" title="'.GetMessage("IBEL_A_SEC_EDIT").'">'.$arSection['ID'].'</a>]';
						}
					}
				}
				elseif($prop['PROPERTY_TYPE']=='E')
				{
					if($t = GetElementName($prop["VALUE"]))
					{
						$arViewHTML[] = $t['NAME'].
						' [<a href="'.htmlspecialcharsbx(CIBlock::GetAdminElementEditLink($t['IBLOCK_ID'], $t['ID'], array(
							"find_section_section" => $find_section_section,
							'WF' => 'Y',
						))).'" title="'.GetMessage("IBEL_A_EL_EDIT").'">'.$t['ID'].'</a>]';
					}
				}
		}
	    return implode(", ",$arViewHTML);
   }
   
   function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/admin/iblock_edit.php");
		
		$arPropertyFields = array(
			"HIDE" => array("ROW_COUNT", "COL_COUNT", "DEFAULT_VALUE","WITH_DESCRIPTION"), 
			"SET" => array("FILTRABLE" => "N"),
			"USER_TYPE_SETTINGS_TITLE" => GetMessage("LONDON_SETOFPROPERTIES_SPISOK_SVOYSTV")
		);
		$arTypesRes = CIBlockProperty::GetUserType();
		$arTypes = array(
			array("NAME" =>GetMessage("IB_E_PROP_TYPE_S"), "VALUE"=>"S"),
			array("NAME" =>GetMessage("IB_E_PROP_TYPE_N"), "VALUE"=>"N"),
			array("NAME" =>GetMessage("IB_E_PROP_TYPE_L"), "VALUE"=>"L"),
			array("NAME" =>GetMessage("IB_E_PROP_TYPE_F"), "VALUE"=>"F"),
			array("NAME" =>GetMessage("IB_E_PROP_TYPE_G"), "VALUE"=>"G"),
			array("NAME" =>GetMessage("IB_E_PROP_TYPE_E"), "VALUE"=>"E"),
		);
		$arNotSupportTypes = array("L","S:video","S:HTML");//F?
		
		foreach ($arTypesRes as $type){
			$userType = $type["PROPERTY_TYPE"].":".$type["USER_TYPE"];
			if ($type["USER_TYPE"] !="SET")
				$arTypes[] = array("NAME"=>$type["DESCRIPTION"],"VALUE"=>$userType);
		}
		
		foreach($arTypes as $key=>$type)
			if (in_array($type["VALUE"],$arNotSupportTypes)) unset($arTypes[$key]);
		CUtil::InitJSCore(array('jquery'));
		$path = "/bitrix/admin/london.setofproperties_london_setofproperties.php?lang=ru&bxpublic=Y&receiver=obIBProps&bxsender=core_window_cadmindialog&IBLOCK_ID=$arProperty[IBLOCK_ID]&PARAMS[ID]=n0&propedit=n0"; //str_replace($_SERVER["DOCUMENT_ROOT"],"",__FILE__);
		$html = '
		<script>
			function London_SetOfProperty(){
				this.AddLine = function(){
					var setLine = $("#London_setofpropertyline .London_propertysetline").last();
					newLine = setLine.clone();
					newLine.find("input[type=text], select").each(function(){
						var name = $(this).attr("name");
						name = name.replace(/\[(.*)\]\[(.*)\]/g,"[_COUNTER][$2]");
						$(this).val("");
						$(this).attr("name",name);
					});
					newLine.insertAfter(setLine);
					this.ReinitLines();
				}	
				this.DelLine = function(oThis){
					var setLines = $("#London_setofpropertyline .London_propertysetline");
					if (setLines.length>1)
						$(oThis).parents("tr").first().remove();
					else setLines.find("input[type=text], select").val("");
					this.ReinitLines();
				}	
				
				this.ReinitLines = function(){
					var setLine = $("#London_setofpropertyline .London_propertysetline");
					var counter = 1;
					setLine.each(function(){
						$(this).find("#setnum").text(counter);
							$(this).find("input[type=text], select").each(function(){
									var name = $(this).attr("name");
									name = name.replace("_COUNTER",counter);
									$(this).attr("name",name);
							})
						counter++;
					});
				}
				
				this.ShowTypeSettings = function(btn){
					var type = $(btn).parents(".London_propertysetline").find("select").val();
					var id =  $(btn).parents(".London_propertysetline").attr("id");
					var setline =   $(btn).parents(".London_propertysetline");
						var Dialog = new BX.CDialog({
							title: "'.GetMessage("LONDON_SETOFPROPERTIES_NASTROYKI_SVOYSTVA").'",
							content_url:"'.$path.'&type="+type,
							icon: "head-block",
							resizable: true,
							draggable: true,
							height: 100,
							width: 550,
						});
						
						Dialog.SetButtons([
						{
							"title": "'.GetMessage("LONDON_SETOFPROPERTIES_SOHRANITQ").'",
							"id": "action_send",
							"className": "adm-btn-save",
							"name": "action_send",
							"action": function(){
								var form  =$(this.parentWindow.__form);
								var res = "";
								form.find("select, input").each(function(){	
									var name=$(this).attr("name");
									var propID="";
									if (name!=undefined)
									{
										if (name.split("PROPERTY_USER_TYPE_SETTINGS").length>1){
											propID =  name.replace(/PROPERTY_USER_TYPE_SETTINGS\[(.*)\]/g, "$1");
											name = name.replace("PROPERTY_USER_TYPE_SETTINGS", id);
										}
										else {
											propID =  name.replace(/PROPERTY_(.*)/g, "$1");
											name = name.replace(/PROPERTY_(.*)/g, id+"[$1]");
										}
										var value=$(this).val();
										res+="<input id=\'"+propID+"\' type=\'hidden\' name=\'"+name+"\' value=\'"+value+"\'>";
									}
								});
								setline.find("#setparams").html(res);
								this.parentWindow.Close();
								
							}
						},
						{
							"title": "'.GetMessage("LONDON_SETOFPROPERTIES_ZAKRYTQ").'",
							"id": "cancel",
							"name": "cancel",
							"action": function(){
							this.parentWindow.Close();
							}
						}
						]);
						Dialog.Show();
						var timer = setInterval(function(){
							var form = $(Dialog.__form);
							if (form.length>0){
								setline.find("#setparams input").each(function(){
									var name = $(this).attr("id");
									var val = $(this).val();
									form.find("[name*=\'"+name+"\']").each(function(){
										$(this).val(val);
										$(this).change();
									});
								})
								clearInterval(timer);
							}
						},200)
			
					
									};
				}
			
			L_SetOfProperty = new  London_SetOfProperty();
		</script>
		<tr id="London_setofpropertyline">
			<td colspan=2>
				<table>
					<tr>
						<td>'.GetMessage("LONDON_SETOFPROPERTIES_").'</td><td>'.GetMessage("LONDON_SETOFPROPERTIES_NAZVANIE").'</td><td>'.GetMessage("LONDON_SETOFPROPERTIES_TIP").'</td><td>'.GetMessage("LONDON_SETOFPROPERTIES_SIMVOLQNYY_KOD").'</td><td>'.GetMessage("LONDON_SETOFPROPERTIES_SORTIROVKA").'</td><td></td><td></td>
					</tr>';
		if (!$arProperty["USER_TYPE_SETTINGS"]) $arProperty["USER_TYPE_SETTINGS"] = array(0);
		$i=1;
		foreach ($arProperty["USER_TYPE_SETTINGS"] as $key=>$property){
			$typesOption="";
			foreach ($arTypes as $type){
				if ($type["VALUE"]==$property["TYPE"]) $selected="selected";
				else $selected =  "";
				$typesOption .= "<option $selected  value='$type[VALUE]'>[$type[VALUE]]$type[NAME]</option>";
			}
			$arNotParams = array("NAME","CODE","SORT","TYPE");
			$hiddenParams="";
			foreach ($property as $keyPropParam=>$propParam){
				if (in_array($keyPropParam,$arNotParams)) continue;
					if (!is_array($propParam)) $name = "$strHTMLControlName[NAME][$key][$keyPropParam]";
					else $name = "$strHTMLControlName[NAME][$key][USER_TYPE_SETTINGS][$keyPropParam]";
		
				$hiddenParams .= "<input id='$keyPropParam' type='hidden' name='$name' value='$propParam'>";
			}
			$html.='<tr id="'.$strHTMLControlName["NAME"].'['.$key.']" class="London_propertysetline">
						<td id="setnum">'.$i.'</td>
						<td><input value="'.$property["NAME"].'" type="text" size="10" name="'.$strHTMLControlName["NAME"].'['.$key.'][NAME]"></td>
						<td><select name="'.$strHTMLControlName["NAME"].'['.$key.'][TYPE]"><option>'.GetMessage("LONDON_SETOFPROPERTIES_VYBERITE_TIP").'</option>
						
						'.$typesOption.'</select></td>
						<td><input value="'.$property["CODE"].'" type="text" size="10" name="'.$strHTMLControlName["NAME"].'['.$key.'][CODE]"></td>
						<td><input value="'.$property["SORT"].'" type="text" size="4" name="'.$strHTMLControlName["NAME"].'['.$key.'][SORT]"></td>
						<td>
							<input type="button" onclick="L_SetOfProperty.ShowTypeSettings(this)" title="'.GetMessage("LONDON_SETOFPROPERTIES_NAJMITE_DLA_DETALQNO").'" value="...">
						</td>
						<td><div onclick="L_SetOfProperty.DelLine(this)" title="'.GetMessage("LONDON_SETOFPROPERTIES_UDALITQ_SVOYSTVO").'" id="btn_delete" style="width:20px;height:20px;cursor:pointer"></div>
							<div id="setparams">'.$hiddenParams.'</div>
						</td>
					</tr>';
			$i++;
		}
		$html.=
		'	</table>
			</td>
		</tr>
		<tr>
		<td colspan=2>
			<div style="width: 100%; text-align: center;">
					<input onclick="L_SetOfProperty.AddLine()" type="button" value="'.GetMessage("LONDON_SETOFPROPERTIES_ESE").'" title="'.GetMessage("LONDON_SETOFPROPERTIES_DOBAVITQ_ESE_ODNO_SV").'">
			</div>
		</td>
		</tr>
		<style>
			#London_setofpropertyline table td{ padding-right:10px;}
			#London_setofpropertyline table select {width:230px;}
		</style>
		'
		;
		return $html;
	}
	
	function L_myCmp($a, $b) {
		if ($a['SORT'] === $b['SORT']) return 0;
		return $a['SORT'] > $b['SORT'] ? 1 : -1;
	}
	
	function PrepareSettings($arFields)
	{
		uasort($arFields["USER_TYPE_SETTINGS"] , array("CPropertySetOfProperties","L_myCmp"));
		
		foreach ($arFields["USER_TYPE_SETTINGS"] as $prop){
			if ($prop["CODE"] && $prop["TYPE"] )
				foreach ($prop as $key => $value){
					$arResult[$prop["CODE"]][$key] = $value;
				}
		}
		return $arResult;
	}
	
	/* редактирование в публичной части */
	
	function GetPublicEditHTML( $arProperty, $value, $strHTMLControlName)
	{
		if ($arProperty["USER_TYPE_SETTINGS"])
		{

				$html = '<table>';
				foreach($arProperty['USER_TYPE_SETTINGS'] as $property)
				{
					$arType = explode(':', $property['TYPE']);
					$property['PROPERTY_TYPE'] = $arType[0];
					$property['USER_TYPE'] = $arType[1];
					$propTypeInfo = CIBlockProperty::GetUserType($property['USER_TYPE']);
					$property['ID'] = $arProperty["ID"];
					
					$property['VALUE'] = array($valID => $value['VALUE'][$property['CODE']]);
					$property['~VALUE'] = $property['VALUE'];
					
					$html.= '<tr><td>'.$property['NAME'].'</td>';
					
					
					if ($propTypeInfo['GetPublicEditHTML'])
					{
						ob_start();
						echo call_user_func_array($propTypeInfo['GetPublicEditHTML'],
											array(
												$property,
												array(
													'VALUE' => $value['VALUE'][$property['CODE']],
													'DESCRIPTION' => $value['DESCRIPTION'][$property['CODE']],
												),
												array(
													'VALUE' => $strHTMLControlName['VALUE'] . '[' . $property['CODE'] . ']',
													'DESCRIPTION' => 'PROPERTY[' . $property['ID'] . '][0][DESCRIPTION]',
													'FORM_NAME' => 'iblock_add',
												),
											));
						$input = ob_get_contents();
						ob_end_clean();
					}
					else
					{
						switch($property['PROPERTY_TYPE']):
							case 'F':{
								$input=implode("; ",$property);
	
							}
							break;
							case 'S':
							case 'G':
							case 'N':
								if ($property['ROW_COUNT'] > 1)
								{
									$input = '<textarea rows="' . $property['ROW_COUNT'] . '" cols="' . $property['COL_COUNT'] . '" type="text" name="' . $strHTMLControlName['VALUE'] . '[' . $property['CODE'] . ']">' .
										$value['VALUE'][$property['CODE']] . '</textarea>';
								}
								else
								{
									$input = '<input type="text" name="' . $strHTMLControlName['VALUE'] . '[' . $property['CODE'] . ']" value="' .
										$value['VALUE'][$property['CODE']] . '">';
								}
							break;
							default:
								$input = '<input type="text" name="' . $strHTMLControlName['VALUE'] . '[' . $property['CODE'] . ']" value="' .
									$value['VALUE'][$property['CODE']] . '">';
							break;
						endswitch;
					}
					

					
					$html.= '<td>'.$input.'</td></tr>';
				}
			}
			$html.= '</table>';
			return $html;
	}
	
   //сохраняем в базу
   function ConvertToDB($arProperty, $value)
   {	
		$vals = $value['VALUE'][0];
		if (!$vals) $vals = $value['VALUE'];
		$flag = false;
			foreach ($vals as $key=>$val)
			{
				if ($arProperty['USER_TYPE_SETTINGS'][$key] && !empty($vals[$key])) 
				{
					$flag = true;
				}
			}
			if (is_array($vals) && count($vals)>0 && $flag) $value['VALUE'] = serialize($vals);
       return $value;
   }
   
   //читаем из базы
   function ConvertFromDB($arProperty, $value)
   {
		$value['VALUE'] = unserialize($value['VALUE']);
       return $value;
   }
};

//AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CPropertySetOfProperties', 'GetIBlockPropertyDescription')); 
//AddEventHandler("main", "OnUserTypeBuildList", array("CPropertySetOfProperties", "GetUserTypeDescription"));

?>