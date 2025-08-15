<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
use asmuinfoclasses;
class struct extends  iasmuinfo{
	private $cashGUID;
	const cashTime=6000;
	private $itemsTree=array();
private $sectionnames;	

public function getFileSignature($file){
		$result2=$file;
		$signature = "";
//		$file=$_SERVER["DOCUMENT_ROOT"].CFile::GetPath($fileID);
		if(file_exists($file)){
			$content = file_get_contents($file);
			if($content!==false){
				$regexp = '#ByteRange\[(\d+) (\d+) (\d+)#'; // subexpressions are used to extract b and c
				preg_match_all($regexp, $content, $result);
				 if (isset($result[2]) && isset($result[3]) && isset($result[2][0]) && isset($result[3][0]))
				 {
				     $start = $result[2][0];
				     $end = $result[3][0];
				     if ($stream = fopen($file, 'rb')) {
				         $signature = stream_get_contents($stream, $end - $start - 2, $start + 1); // because we need to exclude < and > from start and end
				       	 fclose($stream);
				     }else $signature = "??";
				}

				$result2 =$start." ".$end." ".strlen($signature);
				//$result2.= str_replace('"','*',json_encode(openssl_pkey_get_public($signature)));
				//$result2 .=", ".str_replace('"','*',$signature);
			}
			/*

			if($content!===false){
				$regexp = '#ByteRange\[(\d+) (\d+) (\d+)#'; // subexpressions are used to extract b and c
				preg_match_all($regexp, $content, $result);
				
				 if (isset($result[2]) && isset($result[3]) && isset($result[2][0]) && isset($result[3][0]))
				 {
				     $start = $result[2][0];
				     $end = $result[3][0];
					if ($stream = fopen($file, 'rb')) {
				         $signature = stream_get_contents($stream, $end - $start - 2, $start + 1); // because we need to exclude < and > from start and end
				       	 fclose($stream);
				     	}
				}
				$result2 =str_replace('"','*',json_encode(array($result, $start,$end,$signature) ));
				if($signature!=""){
					//$result2 = $signature;
					//$result2 =str_replace('"','*',json_encode(base64_decode($signature)));
					//$result2 =str_replace('"','*',json_encode($result).$file);
		
					//$result2 = strReplace('"','*';json_encode(openssl_pkey_get_public($signature)));
				} else {
					$result2="Файл $result2 подписан ЭЦП";
				}
				
				//$result2 =str_replace('"','*',json_encode($result));
			} else {	$result2="Файл  подписан ЭЦП";}
			
			}else{
			*/
		}

		return "";
	}	

private function generateItems(){
if(CModule::IncludeModule("iblock")){ 
			$IBLOCK_ID=16;
$this->itemsTree=array();
$arFilterx = array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y'); 
$arSelectx = array('ID', 'NAME','SORT');
$rsSection = CIBlockSection::GetTreeList($arFilterx, $arSelectx); 
while($arSection = $rsSection->Fetch()) {
   $this->sectionnames[$arSection['ID']]["name"]=$arSection['NAME'];
   $this->sectionnames[$arSection['ID']]["sort"]=$arSection['SORT'];
}
//print_r($this->sectionnames);
				$arFilter=array("IBLOCK_ID"=>$IBLOCK_ID,"ACTIVE"=>"Y");
				$arSelect=array(
					"NAME",
				"PROPERTY_ADDRESSSTR",
					"PROPERTY_FIO",
					"PROPERTY_GRAFIK",
					"PROPERTY_PHONE",
					"PROPERTY_EMAIL",
					"PROPERTY_DIVISIONCLAUSEDOCLINK",
					"PROPERTY_SITE",
					"PROPERTY_POST",
					"PROPERTY_DIVISIONCLAUSEDOCLINKSIG",
					"PROPERTY_FILES2",


					"ID","IBLOCK_SECTION_ID");
					$res=CIBlockElement::GetList(Array("SORT"=>"ASC"),$arFilter,false,false,$arSelect);

global $USER;

//while($ar_fields = $res->GetNextElement()){
//echo "<!--";
//print_r($ar_fields);
//echo "-->";

//}
				while($ar_fields = $res->GetNext())
					{

//echo "<!--";
//print_r($ar_fields);
//echo "-->";
						$item=array();
						$skey=$this->sectionnames[$ar_fields["IBLOCK_SECTION_ID"]]["sort"]."_".$ar_fields["IBLOCK_SECTION_ID"];
						$this->itemsTree[$skey]["name"]=$this->sectionnames[$ar_fields["IBLOCK_SECTION_ID"]]["name"];
						$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["name"]=$ar_fields["NAME"];
						$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["addressStr"]=$ar_fields["PROPERTY_ADDRESSSTR_VALUE"];
						$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["fioPost"]=$ar_fields["PROPERTY_FIO_VALUE"];
						$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["post"]=$ar_fields["PROPERTY_POST_VALUE"];
						$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["site"]=$ar_fields["PROPERTY_SITE_VALUE"];
						$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["email"]=$ar_fields["PROPERTY_EMAIL_VALUE"];
						$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["divisionClauseDocLink"]="";
						if($ar_fields["PROPERTY_DIVISIONCLAUSEDOCLINK_VALUE"]>0){
							$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["divisionClauseDocLink"]=CFILE::getPath($ar_fields["PROPERTY_DIVISIONCLAUSEDOCLINK_VALUE"]);
							$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["signInfo"]=$this->getFileSignature($_SERVER["DOCUMENT_ROOT"].$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["divisionClauseDocLink"]);
						}
						if(is_array($ar_fields["PROPERTY_FILES2_VALUE"]) && count($ar_fields["PROPERTY_FILES2_VALUE"])>0){
							$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["arSignInfo"]=array();
							foreach($ar_fields["PROPERTY_FILES2_VALUE"] as $key=>$fileid){
								$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["arSignInfo"][]=array(
									"url"=>CFILE::getPath($fileid),
									"name"=>$ar_fields["PROPERTY_FILES2_DESCRIPTION"][$key]
								);
							}
						}
						$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["divisionClauseDocLinkSig"]="";	
						if($ar_fields["PROPERTY_DIVISIONCLAUSEDOCLINKSIG_VALUE"]>0)
							$this->itemsTree[$skey]["items"][$ar_fields["ID"]]["divisionClauseDocLinkSig"]=CFILE::getPath($ar_fields["PROPERTY_DIVISIONCLAUSEDOCLINKSIG_VALUE"]);
						
					}
					//	echo "<pre>";print_r($this->itemsTree);echo "</pre>";
}else{echo "error";}
ksort($this->itemsTree);




}
public function setparams($params){
	$this->ovz=2;
	if (isset($params["objTypes"])) $this->objTypes=$params["objTypes"];
	if(isset($params["ovz"])) $this->ovz=intval($params["ovz"]);//0 нет 1 да 2 все
	$this->cashGUID="struct".$this->ovz;
}

public function showHtml($buffer=true){
	$this->css=__DIR__."/style.css";
	$IBLOCK_ID=16;
$memcache = new Memcache;
$this->cashGUID="struct";
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
	$html=trim($memcache->get($this->cashGUID));
if ($html=="" || $html==false || self::isEditmode()){
	$this->generateItems();
	$fields=array(
			
			"name"=>"Наименование органа управления / структурного подразделения",
			"fioPost"=>"ФИО руководителя структурного подразделения",
			"post"=>"Должность руководителя структурного подразделения",
			"addressStr"=>"Адрес местонахождения структурного подразделения",
			"site"=>"Адрес официального сайта структурного подразделения",
			"email"=>"Адреса электронной почты структурного подразделения",
			"divisionClauseDocLink"=>"Положение об органе управления/ о структурном подразделении" 
	);		
		$html="";
		$htmlh="<div class=\"container-thead\"><table class=\"structtable \"><thead class=\"table-header\"><tr><th style=\"min-width:2em!important;\">№</th>";
		foreach($fields as $field){
			$htmlh.="<th><div>{$field}</div></th>";
		}
		$htmlh.="</tr><tbody>";
		foreach ($this->itemsTree as $sid=>$itemsOb){
			$html.="<h2>".$itemsOb["name"]."</h2>".$htmlh;
			$idn=0;
			foreach ($itemsOb["items"] as $id=>$item){
						
				$html.="<tr  title=\"{$title}\" class=\"maindocselement\" id=\"{$id}\" data-id=\"{$id}\" data-iblock=\"{$IBLOCK_ID}\" itemprop=\"structOrgUprav\">";
				$idn++;$html.='<td style="text-align:center;vertical-align:middle">'.$idn.'</td>'; 
				foreach($fields as $itemprop=>$field){
					
					if($itemprop!="fioPost") 
						$html.='<td itemprop="'.$itemprop.'" class="" data-id="'.$id.'">';
					else	
						$html.='<td class="" data-id="'.$id.'">';
					if(!is_array($item["arSignInfo"])) $item["arSignInfo"]=array();
					if($itemprop=="divisionClauseDocLink" ){
						if(($item["divisionClauseDocLink"]!="") || (count($item["arSignInfo"])==0)) 
						$html.='<span class="signature" title="['.$item["signInfo"].']"></span><a href="'.$item["divisionClauseDocLink"].'">Положение</a><br>';
						if($item["divisionClauseDocLinkSig"]!=""){
							$html.='<sub><a class="blue" href="'.$item[$itemprop].'">.sig</a></sub>';
						}
						if(is_array($item["arSignInfo"]) && count($item["arSignInfo"])>0){
							foreach($item["arSignInfo"] as $finfo){
								$html.='<span class="signature" title=""></span><a href="'.$finfo["url"].'">'.$finfo["name"].'</a><br>';
							}
						}
					}elseif($itemprop=="fioPost"){
						$html.="<span itemprop=\"fioPost\">".(($item[$itemprop]!="")?$item[$itemprop]:$this::emptyCell)."</span>";
						$html.="<span itemprop=\"fio\" class=\"hide\">".(($item[$itemprop]!="")?$item[$itemprop]:$this::emptyCell)."</span>";
					}else{
						$html.=($item[$itemprop]!="")?$item[$itemprop]:$this::emptyCell;
					}
					$html.='</td>';
				}
				$html.="</tr>";
		}	
		$html.="</tbody></table></div><br>";};
	$memcache->set($this->cashGUID, $html, false, $this->cashTime);
}
$memcache->close();	
if($buffer) return $html; else echo $html;
}
}//end class xobjects  