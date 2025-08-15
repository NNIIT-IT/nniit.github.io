<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use asmuinfoclasses;
/*class memcache{
	function addServer(){}
	function get(){
		return "";
	}
	function set(){
		return "";
	}
	function close(){
	}
	function replace(){
	}

}*/
abstract class iasmuinfo{
	const emptyCell="Отсутствует";
	const sectionsName=array(
			179=>"Бакалавриат",
			180=>"Специалитет",
			181=>"Ординатура",
			182=>"Аспирантура",
			184=>"Магистратура",
			185=>"НПО",
			186=>"CПО"
		);

	public $BD;
	public $css;
	public $adminGroup=array(1,8);		
	abstract function showHtml();
	abstract function setparams($params);
	public function allmounstostr($mouns){
		$mouns_Y=0+floor($mouns/12);
		$mouns_M=$mouns-$mouns_Y*12;
		if ($mouns_Y>20)
			$mouns_Y1=$mouns_Y-floor($mouns_Y/10)*10; 
		else 
			$mouns_Y1=$mouns_Y;
		$S_mouns_Y=" лет";
		$S_mouns_M=" месяцев";
		if ($mouns_Y1==1) 
			$S_mouns_Y=" год";
		if (($mouns_Y1>1)&&($mouns_Y1<5)) 
			$S_mouns_Y=" года";
		if ($mouns_M==1) 
			$S_mouns_M=" месяц";
		if (($mouns_M>1)&&($mouns_M<5)) 
			$S_mouns_M=" месяца";
	
		return (($mouns_Y>0)?($mouns_Y.$S_mouns_Y."<br>"):"").(($mouns_M>0)?($mouns_M.$S_mouns_M):"");
	}
	public function showAdminInterface(){
	
	}
	public function unserform($str){
			$arforms=[1=>"",2=>"",3=>""];
			$s=mb_substr($str,5,-1);

			//	echo $s."<br>";
			$arforms0= explode(";",$s);
			$arforms1=array();
			foreach($arforms0 as $c){
				$arforms1[]=explode(":",$c);
			}
			$arforms[1]=$arforms1[1][2];
			$arforms[2]=$arforms1[3][2];
			$arforms[3]=$arforms1[5][2];
			return $arforms;
	}
	public function fullescape($in)
		{
		 
		 // $out = urlencode($in);
		 
		  $out = str_replace(' ','%20',$in);
		  $out = str_replace('_','%5F',$out);
		 
		  $out = str_replace('-','%2D',$out);
		  return $out;
		} 
	public function sqltoiblock($sql,$iblocks){
	
	$fields=array();$fieldsID=array();
	$sqlfld="select ID,CODE, id from  `b_iblock_property` WHERE `IBLOCK_ID` in (".implode(",",$iblocks).") and ACTIVE=\"Y\"";
	if($rez=$this->BD->query($sqlfld)){
		while ($rec=$rez->fetch()){
			$code=trim(strtoupper($rec['CODE']));
			$fields["#".$code."#"]="PROPERTY_".$rec['ID'];
			$fieldsID["#".$code."_ID#"]=$rec['ID'];
		}
	}
	$sql2=str_replace(array_keys($fields),$fields,$sql);
	$sql2=str_replace(array_keys($fieldsID),$fieldsID,$sql2);
		//echo "<pre>";print_r($fields);echo "</pre>";
	foreach($iblocks as $key=>$iblockID){
		$sql2=str_replace("prop_s#iblock".$key."#","prop_s".$iblockID,$sql2);
		$sql2=str_replace("prop_m#iblock".$key."#","prop_m".$iblockID,$sql2);
		$sql2=str_replace("IBLOCKID=#iblock".$key."#","IBLOCKID=".$iblockID,$sql2);
		$sql2=str_replace("#iblock".$key."#",$iblockID,$sql2);
	}
	return $sql2;
	}
	public function getHtml($buffer=false){
/*добавить кеширование*/
		GLOBAL $APPLICATION;
		$html="";
		if ($buffer) 
			$html=$this->showHtml($buffer);
		else
			echo $this->showHtml();
		$fcss=$this->css;
		if($fcss!=""){
			if(file_exists($fcss)){
				$fstyle=str_replace($_SERVER["DOCUMENT_ROOT"],"https://dentmaster.ru/",$fcss);
				//echo "<link rel=\"stylesheet\" href=\"$fstyle\">";
				$APPLICATION->SetAdditionalCSS($fstyle);
			}	
		}

/*
		$localcss=__DIR__."/style.css";
		if(file_exists($localcss)){
			$fstyle=str_replace($_SERVER["DOCUMENT_ROOT"],"https://asmu.ru/",$localcss);
			echo "<link rel=\"stylesheet\" href=\"$fstyle\">";
		}	
		
		$fsrcscript=__DIR__."/script.js";
		if(file_exists($fsrcscript)){ 
			$fscript=str_replace($_SERVER["DOCUMENT_ROOT"],"https://asmu.ru/",$fsrcscript);
			CJSCore::Init(array('ajax', 'window')) ;
			echo "<script src=\"$fscript\"></script>";
		}
*/

/* / кеширование*/

return $html;		
	}
	public function __construct(){
		$this->BD=Bitrix\Main\Application::getConnection();
	}
	public function setAdminGroups($x){
		$this->adminGroup=array_merge($this->adminGroup,$x);
	}
	public function isEditMode(){
		GLOBAL $USER;	
		$userlogin=$USER->IsAuthorized();
		if (isset($_GET['bitrix_include_areas'])) $rr=htmlspecialchars($_GET['bitrix_include_areas']); else $rr='N';
		if (isset($_SESSION['SESS_INCLUDE_AREAS'])) $rr1=htmlspecialchars($_SESSION['SESS_INCLUDE_AREAS']); else $rr1='0';
		$rez=$userlogin && (($rr=='Y')||($rr1=='1'));
		$arUserGroups = $USER->GetUserGroupArray();
		$rez=count(array_intersect($this->adminGroup, $arUserGroups))>0 && $rez;	
		
		return $rez;
		
	}
	
}