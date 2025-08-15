<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Структура и органы управления образовательной организацией");
$APPLICATION->SetAdditionalCss("/sveden/struct/tree-menu.css");
$connection=Bitrix\Main\Application::getConnection();
$emailCollection=Array();
//информация о подразделении
function getPodrContent($arFields,$mainInfo=false){
global $emailCollection;
	$connection=Bitrix\Main\Application::getConnection();
	if ($arFields["PROPERTY_NOPODRAZD_VALUE"]=="Y") return "";

	//$keys=array("ГЛАВНЫЙ ВРАЧ","РЕКТОР", "ДЕКАН","ЗАВЕДУЮЩИЙ КАФЕДРОЙ","ЗАВЕДУЮЩИЙ", "НАЧАЛЬНИК","ДИРЕКТОР","ГЛАВНЫЙ ИНЖЕНЕР","СТАРШИЙ НАУЧНЫЙ СОТРУДНИК","КОМЕНДАНТ","МЛАДШИЙ НАУЧНЫЙ СОТРУДНИК","ПРЕДСЕДАТЕЛЬ");
	//require_once($_SERVER["DOCUMENT_ROOT"]."/local/common/class/kdr/getuserdata.php");
	$resultHtml="";
	$pologenie=array();
	$PolDep="";
	

		$itempropDisplay=$arFields["PROPERTY_ITEMPROP_VALUE"]!="Да";
		
		//способ вывода информации
		$departmentInfo=$arFields["PROPERTY_INFO_VALUE"]=="Y";	//Этот элемент описывает подразделение

		$noDepartment=$arFields["PROPERTY_NODEPARTMENT_VALUE"]=="1"; //Представлять как физлицо в подразделении

		$departmentIsKafedra=$arFields["PROPERTY_KAF_VALUE"]=="Y"; //Кафедра
		

		//наименование подразделения
		$namePodr=$arFields["NAME"];

		//руководитель

		$bossId=intval($arFields["PROPERTY_BOSS_VALUE"]);
		if($bossId==0) 	$bossId=intval($arFields["PROPERTY_BOSS1_VALUE"]);
		$userisActualBoss=false;

		if ($bossId!=0){
			$rsUser = CUser::GetByID($bossId);
			$arUser = $rsUser->Fetch();
			$uid="";
			if($rec=$connection->query("select UF_1C_ID from emploers where UF_BITRIX_ID=".$bossId)){
				if($ob=$rec->fetch()){
					$uid=$ob["UF_1C_ID"];
				}
			};
			$arUser["UID"]=$uid;
		} else {
			$arUser=array(
			'LAST_NAME'=>"",
			'NAME'=>"",
			'SECOND_NAME'=>"",
			'WORK_POSITION'=>'',
			'WORK_ZIP'=>'',
			'WORK_STATE'=>'',
			'WORK_STREET'=>'',
			'WORK_CITY'=>'',
			'WORK_PHONE'=>'',
			'UF_INSIDE_TEL'=>'',
			'UF_MAIL'=>'',
			'PERSONAL_WWW'=>'',
			'PERSONAL_PHOTO'=>'',
			"UID"=>'',
			);
		}

		//ФИО руководителя
		$bossFio=trim($arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME']);
		$bossFio2=trim($arUser['LAST_NAME'].';'.$arUser['NAME'].';'.$arUser['SECOND_NAME']);
			

		//должность руководителя		
		$bossPossition=$arFields["PROPERTY_DOLG_VALUE"];	//должность из описания каталога	
		$UpPost=mb_strtoupper($bossPossition);
		$rector=mb_strpos($UpPost,"РЕКТОР")!==false && mb_strpos($UpPost,"ЗАМ")===false && mb_strpos($UpPost,"ПРОРЕКТОР")===false && mb_strpos($UpPost,"РЕКТОРАТ")===false;
		if ($bossPossition=="") $bossPossition=$arUser['WORK_POSITION'];//должность из профиля пользователя
		if ($bossPossition=="" && $itempropDisplay) $bossPossition="Руководитель";
		if ($departmentIsKafedra) $bossPossition="Заведующий кафедрой";
		
		if (trim($bossFio)=="" && $itempropDisplay) {
				$bossFio="Должность вакантна";
				$bossFio2="$namePodr; $bossPossition; ";
		}	
		//адрес 
			$podrAddresWork=$arFields["PROPERTY_ADDRESS_VALUE"];
			if ($podrAddresWork=="") {
				$bossAddresIndexWork=$arUser['WORK_ZIP'];
				if ($bossAddresIndexWork==''){$bossAddresIndexWork='656038';}
				$bossAddresRegionWork=$arUser['WORK_STATE'];				
				if ($bossAddresRegionWork==''){$bossAddresRegionWork='Алтайский край';}
				$bossAddresStreetWork=$arUser['WORK_STREET'];
				if ($bossAddresStreetWork==''){$bossAddresStreetWork='пр. Ленина, дом 40';}
				$bossAddresSityWork=$arUser['WORK_CITY'];
				if ($bossAddresSityWork==''){$bossAddresSityWork='Барнаул';}
				$podrAddresWork=$bossAddresIndexWork.', '.$bossAddresRegionWork.', г. '.$bossAddresSityWork.', '.$bossAddresStreetWork;
			}

		//телефон босса
			$bossTel=$arFields["PROPERTY_TEL_VALUE"];
			if ($bossTel=="") {
				$bossTel=$arUser['WORK_PHONE'];
				if ($arUser['UF_INSIDE_TEL']!="") $bossTel.=(($bossTel!="")?", ":"").$arUser['UF_INSIDE_TEL'];
			}
		//мини фото босса
			$PHboss=intval($arUser['PERSONAL_PHOTO']);
			if ($PHboss>0)	$fotoboss = CFile::GetPath($PHboss);
			//$fotoboss = CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'],array('width' => 100, 'height' => 120),BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true,	true,false);

			$bossphoto=($fotoboss!="")?$fotoboss:COMMON_TEMPLATE.'/img/mmm.jpg';

		//email bossa
			$bossEmail=$arFields["PROPERTY_EMAIL_VALUE"];
			if ($bossEmail=="") {
				$bossEmail=$arUser['UF_MAIL'];
			}

		//url bose
			$bossUrl=$arUser['PERSONAL_WWW'];	
			$URL_RUK=$arFields["PROPERTY_URL_RUK_VALUE"];//?


		//site 
			$podrSite=$arFields["PROPERTY_URL_E_VALUE"];

		//положение о подразделении
								
		$file_doc_id=0;//intval($arFields["PROPERTY_FILE_VALUE"]);
		if ($file_doc_id!=0){

			$file_arr=CFILE::GetFileArray($file_doc_id);
			$pologenie[]=array("name"=>"Положение о структурном подразделении","src"=>str_replace(array(" ","+"),array("%20","%2B"),$file_arr['SRC']));
		}	
		$bossUid=$arUser["UID"];
		//получаем положение подразделения из СМК
		if (count($pologenie)==0){
			/*
			$resp=CIBlockElement::Getlist(
				Array("SORT"=>"ASC"),
				Array("IBLOCK_ID"=>135, 'ACTIVE'=>'Y','PROPERTY_394_VALUE'=>$arFields['ID'],'?PROPERTY_275_VALUE'=>"Положение о%"),//
				false,
				array('nTopCount'=>10),
				array('PROPERTY_275','PROPERTY_276','PROPERTY_272')
			);
				//echo "<!--pol"; var_dump($resp);echo"-->";
			while($pol_doc = $resp->GetNextElement()){
				$pol_doc_fld=$pol_doc->GetFields();
				//echo "<!--pol"; print_r($pol_doc_fld);echo"-->";
				$file_doc_id=$pol_doc_fld['PROPERTY_276_VALUE'];
				if ($file_doc_id!=0){
					$file_arr=CFILE::GetFileArray($file_doc_id);
					$pologenieName=str_replace(array("«","»",'"'),"",$pol_doc_fld['PROPERTY_275_VALUE']);
					$pologenie[]=array("name"=>$pologenieName,"src"=>str_replace(array(" ","+"),array("%20","%2B"),$file_arr['SRC']));
			
				}
			}
			*/
			/*
$sql=<<<SQL
SELECT PROPERTY_276 as fileID, concat('/upload/',f.subdir,"/",f.file_name) as fileSrc,be.name as docName
FROM `b_iblock_element_prop_s135` ps
left join `b_iblock_element` be on be.id=ps.IBLOCK_ELEMENT_ID
left join `b_file` f on f.id=ps.PROPERTY_276
where (ps.PROPERTY_394 is not null or ps.PROPERTY_275=587) and ps.PROPERTY_276  is not null and (f.CONTENT_TYPE="application/pdf" or f.CONTENT_TYPE="application/msword")
and ps.PROPERTY_272=#podr#  and be.active="Y" and PROPERTY_273 is NULL
LIMIT 10
SQL;
			$sql=str_replace("#podr#",$arFields['ID'],$sql);

			$rezp=$connection->query($sql);
			while($pol_doc = $rezp->fetch()){
				$pologenieName=str_replace(array("«","»",'"'),"",$pol_doc['docName']);
				$pologenieName=mb_substr($pologenieName,mb_strpos($pologenieName," Положение"))."<!--".$pol_doc["fileID"]."-->";
				$pologenie[]=array("name"=>$pologenieName,"src"=>str_replace(array(" ","+"),array("%20","%2B"),$pol_doc['fileSrc']));
			}

		}//if (count($pologenie)==0)

		if(count($pologenie)==0 && $arSection["UF_POL"]!=0)
			$pologenie[]=array("name"=>"Положение о структурном подразделении","src"=>str_replace(" ","%20",CFile::GetFileArray($arSection["UF_POL"])["SRC"]));

*/

$pologenie[]=array("name"=>"Положение о структурном подразделении","src"=>str_replace(" ","%20",CFile::GetFileArray($arSection["UF_POL"])["SRC"]));
	//формирование HTML
$resultHtml="";
$emailCollectionkey=$bossFio2;
	if (!$noDepartment){ //основное описание подразделения

		if (!$departmentInfo || !$mainInfo){ //подразделения в составе
			$resultHtml.='<li class="menu-close"><div class="page" ></div><div class="item-text">'.$namePodr;
			if ($podrSite!="")
				$resultHtml.='<a href="'.$podrSite.'" style="cursor:alias;" title="Страница подразделения">  <img src="/local/common/img/cc.png" style="height: 0.8em;opacity: 0.6;" alt="#"></a>';
			$resultHtml.='</div><div class="leftpodr">';

		}elseif($mainInfo){
			$resultHtml.='<li class="menu-close"><div class="folder" ></div><div onclick="OpenMenuNode(this)" style="cursor: pointer;" class="item-text"  >'.$namePodr;
			if ($podrSite!="")
				$resultHtml.='<a href="'.$podrSite.'" style="cursor:alias;" title="Страница подразделения">  <img src="/local/common/img/cc.png" style="height: 0.8em;opacity: 0.6;" alt="#"></a>';
			$resultHtml.='</div><ul><li >';
		}
		$resultHtml.='<div '.(($itempropDisplay)?' itemprop="structOrgUprav" ':'').'>';
		$resultHtml.='<span class="hide" '.(($itempropDisplay)?'itemprop="name"':'').'>'.$namePodr.'</span>';
		if (count($pologenie)>0){
			foreach($pologenie as $plg){
				$resultHtml.='<span '.(($itempropDisplay)?' itemprop="divisionClauseDocLink" ':'').' ><a '.(($itempropDisplay)?' itemprop="divisionClause_DocLink" ':'').' Href="'.$plg["src"].'">'.$plg["name"].'</a></span><br>';
			}
		}else $resultHtml.='<div style="display:none;"'.(($itempropDisplay)?' itemprop="divisionClauseDocLink" ':'').'><a href="javascript:void(0);" '.(($itempropDisplay)?' itemprop="divisionClause_DocLink" ':'').' >Положения нет</a></div>';	


		if ($podrAddresWork!='') 
			$resultHtml.=' <b> Место нахождения</b><br/><span '.(($itempropDisplay)?' itemprop="addressStr" ':'').'>'.$podrAddresWork.'</span><br><br>';
		elseif ($itempropDisplay)  
			$resultHtml.='<span class="hide" itemprop="addressStr">Адрес: 656038, Сибирский федеральный округ, Алтайский край, г. Барнаул, пр. Ленина, 40.</span>';	

		$resultHtml.='<b '.(($itempropDisplay)?' itemprop="post" ':'').'>'.$bossPossition.' </b><br>';
		if ($bossUrl!=''){
			$resultHtml.='<a Href="'.$bossUrl.'" '.(($itempropDisplay)?' itemprop="fioPost" ':'').'>'.$bossFio.'</a><br>';

		}else{
			$resultHtml.='<span '.(($itempropDisplay)?' itemprop="fioPost" ':'').'>'.$bossFio.'</span><br>';
		}
		$resultHtml.='<span class="hide" itemprop="fio">'.$bossFio.'</span>';
			
		if ($bossEmail!='') {
			$resultHtml.=' <b> Email: </b><span '.(($itempropDisplay)?' itemprop="email" ':'').'>'.$bossEmail.'</span><br>';


			$emailCollection[$emailCollectionkey]["fio"]=explode(";",trim(str_replace("  "," ",$bossFio2)));
			$emailCollection[$emailCollectionkey]["post"]=(isset($emailCollection[$emailCollectionkey]["post"]))?$emailCollection[$emailCollectionkey]["post"].=", ".$bossPossition:$bossPossition;
			$emailCollection[$emailCollectionkey]["email"]=$bossEmail;
			
			$emailCollection[$emailCollectionkey]["tel"]=$bossTel;
			$emailCollection[$emailCollectionkey]["uid"]=$bossUid;
			$emailCollection[$emailCollectionkey]["podr"]=$namePodr;



		}	
		elseif ($itempropDisplay)  
			$resultHtml.='<span class="hide" itemprop="email">noemail@agmu.ru</span>';	

		if ($bossTel!='') 
			$resultHtml.=' <b> Телефон: </b><span '.(($itempropDisplay)?' itemprop="tel" ':'').'>'.$bossTel.'</span><br>';
		elseif ($itempropDisplay)  
			$resultHtml.='<span class="hide" itemprop="tel">+7 (3852) 566-800</span>';	

		if ($podrSite!="")
			 $resultHtml.='<br><a '.(($itempropDisplay)?' itemprop="site" ':'').' href="'.$podrSite.'" >Страница подразделения</a><br><br>';
		elseif ($itempropDisplay)  
			$resultHtml.='<span class="hide" itemprop="site" >Интернет страницы нет</span>';	
		$resultHtml.='</div>';
		if (!$departmentInfo || !$mainInfo){
			$resultHtml.='</div>';	
		}elseif($mainInfo){
			$resultHtml.='<br></li>';
		}
		
		/*
			elseif($mainInfo){
			$resultHtml.='';	
		}	*/
	
	}elseif ($noDepartment){ //физ лицо 
		//$resultHtml.='<li><img class="'.(($rector)?'bossicon':'bossicon').'" src="'.$bossphoto.'" alt="ФОТО"> <div class="'.(($rector)?'leftboss':'leftboss').'" >';
		$resultHtml.='<li><div class="'.(($rector)?'bossicon':'bossicon').'" style="background-image: url('.$bossphoto.'?par=1); background-size: cover;" title="ФОТО '.$bossFio.'"></div> <div class="'.(($rector)?'leftbboss':'leftboss').'" >';
		$resultHtml.='<b>'.$bossPossition.': </b><br>';
		$emailCollection[$emailCollectionkey]["fio"]=explode(" ",trim(str_replace("  "," ",$bossFio)));
		$emailCollection[$emailCollectionkey]["post"]=(isset($emailCollection[$emailCollectionkey]["post"]))?$emailCollection[$emailCollectionkey]["post"].=", ".$bossPossition:$bossPossition;

		if ($bossUrl!=''){
			$resultHtml.='<a Href="'.$bossUrl.'">'.$bossFio.'</a><br>';
		}else{
			$resultHtml.='<span>'.$bossFio.'</span><br>';
		}
		if ($podrAddresWork!='') {
			$resultHtml.=' <b> Адрес: </b><br/><span>'.$podrAddresWork.'</span><br>';
			
		} 

		if ($bossEmail!=''){ 
			$resultHtml.=' <b> Email: </b><span>'.$bossEmail.'</span><br>';
			$emailCollection[$emailCollectionkey]["email"]=$bossEmail;
		}
		if ($bossTel!='') {
			$resultHtml.=' <b> Телефон: </b><span >'.$bossTel.'</span><br>';//'.(($itempropDisplay)?' itemprop="tel" ':'').'
			$emailCollection[$emailCollectionkey]["tel"]=$bossTel;
		}
		$resultHtml.='</div>';
		//$resultHtml.='</li>';
	}
	//$resultHtml.='</li>';	
	return $resultHtml;
}



function printChild($el){
$noclouseID=array(860,872,1117,1133,3233);
if(!is_array($el["ITEMS"])) $el["ITEMS"]=array();
			if ($el["MAINITEM"]!=""){
					$shtml.=$el["MAINITEM"];
				}else{
					$stl='menu-close';
					if($el["DEPTH_LEVEL"]<2 || in_array($el["ID"],$noclouseID)) $stl='';
					if ($el["DEPTH_LEVEL"]>0){
						$shtml.='<li class="'.$stl.'"><div class="folder"></div><div onclick="OpenMenuNode(this)" style="cursor: pointer;" class="item-text">'.$el["NAME"].'</div><ul>';
					}else {
						$shtml.='<li>'.$el["NAME"].'<ul>';
					}
						
				}


		if (count($el["ITEMS"])>0){			
			foreach($el["ITEMS"] as $secondelement) 
				$shtml.=$secondelement."</li>";
		}
	$shtml.="</ul>";

return $shtml;
}
function printUL($el){


	$rez=printChild($el);
if(!is_array($el["CHILD"])) $el["CHILD"]=array();
if (count($el["CHILD"])>0){
		$rez.="<ul>";
		foreach($el["CHILD"] as $child){
			$rez.=printUL($child);	
		}
		$rez.="</ul>";
	}
	$rez.="</li>";
	return $rez;
};
?>
<?

/******************************************************************/
GLOBAL $USER;
$cachetime = 1800;
// Обслуживается из кеша, если время запроса меньше $cachetime
$memcache = new Memcache;
$memcache->addServer('unix:///tmp/memcached.sock', 0);
$cacheKey ='sveden-struct';
$cacheData="";
$editmode=isEditMode();
if (!$editmode) {
	$cacheData=$memcache->get($cacheKey);
}else{
echo "edit mode<br>";
	$cacheData="";
}
if ($cacheData==""){
	$cacheData='<div class="container"><br><h1 id="voiceSveden" class="voicetext"> Структура и органы управления образовательной организацией</h1><div  class="menu-sitemap-tree"><ul>';
		$arFilter = array(		
		    'ACTIVE' => 'Y',
		    'IBLOCK_ID' => 103,
		    'GLOBAL_ACTIVE'=>'Y',
			'UF_PUBLIC'=>1
		);
		$arSelect = array('IBLOCK_ID','ID','NAME','DEPTH_LEVEL','IBLOCK_SECTION_ID','UF_PUBLIC');
		$arOrder = array('DEPTH_LEVEL'=>'ASC','SORT'=>'ASC');
		$rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);
		$sectionLinc = array();
		$arResult['ROOT'] = array();
		$sectionLinc[0] = &$arResult['ROOT'];
		while($arSection = $rsSections->GetNext()) {
			echo "<!-- "; print_r($arSection); echo "-->";
			$secondelements=array();
			$arFilter = Array("IBLOCK_ID"=>$arSection["IBLOCK_ID"],  "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y" , "SECTION_ID" => $arSection['ID'],'!PROPERTY_329_VALUE'=>'Y',"!NAME"=>$arSection["NAME"],'!PROPERTY_239_VALUE'=>'Y');
			//$rescnt = CIBlockElement::GetList(Array(), $arFilter, Array(), false, $arSelect);//число элементов у потомка	
			$resX = CIBlockElement::GetList(Array("sort"=>"Desc"), $arFilter, false, false, $arSelect);
			
			

			while($ob = $resX->GetNextElement()){
				$fields=$ob->GetFields();
				$secondelements[$fields["ID"]]=getPodrContent($fields,false);
					
					

			}
			$mainElement="";
			$arSelect = Array("ID","IBLOCK_ID","NAME", "PROPERTY_URL_E", "PROPERTY_OPIS_E", "PROPERTY_TEL", "PROPERTY_FILE","PROPERTY_LINK", "PROPERTY_BOSS", "PROPERTY_BOSS1", "PROPERTY_ADDRESS", "PROPERTY_EMAIL","PROPERTY_DOLG","PROPERTY_URL_RUK","PROPERTY_NODEPARTMENT","PROPERTY_INFO","PROPERTY_NOPODRAZD","PROPERTY_ITEMPROP","PROPERTY_KAF");
			$arFilter = Array("IBLOCK_ID"=>$arSection["IBLOCK_ID"],  "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y" , "SECTION_ID" => $arSection['ID'],'!PROPERTY_329_VALUE'=>'Y','PROPERTY_239_VALUE'=>'Y' );
			$res = CIBlockElement::GetList(Array("sort"=>"Desc"), $arFilter, false, false, $arSelect);
			if ($ob = $res->GetNextElement()){
				$fields=$ob->GetFields();
				$mainElement=getPodrContent($fields,true);
			} else $mainElement="";
			
			$arSection['ITEMS']=$secondelements;
			$arSection['MAINITEM']=$mainElement;
		
		    $sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['ID']] = $arSection;
		
		
		
		    $sectionLinc[$arSection['ID']] = &$sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['ID']];
		}
		
	$cacheData.=printUL($sectionLinc[0]);
	$cacheData.='</ul></div></div>';
/*
LDIF справочник 
*/

$LDIF="";$VCARD="";
foreach($emailCollection as $fio=>$fioData){
	$fio2=$fioData["fio"][0]." ".$fioData["fio"][1]." ".$fioData["fio"][2];
	
$LDIF.="dn:: ".base64_encode("cn=$fio,mail={$fioData["email"]}")."\r\n";
$LDIF.="objectclass: top\r\nobjectclass: person\r\nobjectclass: organizationalPerson\r\nobjectclass: inetOrgPerson\r\nobjectclass: mozillaAbPersonAlpha\r\n";
$LDIF.="givenName:: ".base64_encode($fioData["fio"][0])."\r\n";
$LDIF.="sn:: ".base64_encode($fioData["fio"][1])."\r\n";
$LDIF.="cn:: ".base64_encode($fio2)."\r\n";
$LDIF.="mail: ".$fioData["email"]."\r\n";
$LDIF.="modifytimestamp: ".time()."\r\n";
$LDIF.="telephoneNumber: ".$fioData["tel"]."\r\n\r\n";

$VCARD.="BEGIN:VCARD\r\n";
$VCARD.="VERSION:4.0\r\n";
$VCARD.="FN:$fio2\r\n";
$VCARD.="EMAIL;PREF=1:".$fioData["email"]."\r\n";
$VCARD.="N:".$fioData["fio"][1].";".$fioData["fio"][0].";;;\r\n";
$VCARD.="TEL;TYPE=work;VALUE=TEXT:".$fioData["tel"]."\r\n";
$VCARD.="UID:".$fioData["uid"]."\r\n";
$VCARD.="TITLE:".$fioData["post"]."\r\n";
$VCARD.="ORG:АГМУ;".$fioData["podr"]."\r\n";
$VCARD.="END:VCARD\r\n";




}
file_put_contents($_SERVER["DOCUMENT_ROOT"]."/sveden/struct/emails.ldif",$LDIF);
file_put_contents($_SERVER["DOCUMENT_ROOT"]."/sveden/struct/emails.vcard",$VCARD);
$cacheData.='<div ><a class="link" style="color: #d2d2d2 !important;" download href="/sveden/struct/emails.ldif">■</a><a class="link" style="color: #d2d2d2 !important;" download href="/sveden/struct/emails.vcard">■</a></div>';
	unset($sectionLinc);
	$memcache->set($cacheKey, $cacheData, false, $cachetime);
}

echo $cacheData;
?>
<?
//филиалы
require_once(__DIR__."/../class/asmuinfo/asmuinfo.php");
$z=new asmuinfo();
$params1=array(
	"propListSections"=>array(4610),
	"mainSection"=>array(4610),
	"mainPropList"=>array(
		"filInfo",
		"repInfo",
	),
	"propList"=>array(
		"nameFil","addressFil","workTimeFil","telephoneFil","emailFil","websiteFil","fiofil","postfil","divisionClauseDocLink",
		"nameRep","addressRep","workTimeRep","telephoneRep","emailRep","websiteRep","fioRep","postRep",
),
	"sectionsList"=>array(),);
$z->setAdminGroups(array(41728));
$z->setСlassList(array(array("classname"=>"maindocs","params"=>$params1)));
?>
<div class="container">
<? $z->getHtml(false,true);?>
</div>
<script>function OpenMenuNode(e){if(e.parentNode.className=="")e.parentNode.className="menu-close";else e.parentNode.className="";return false}</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>