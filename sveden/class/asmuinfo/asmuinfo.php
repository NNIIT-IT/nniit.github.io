<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CJSCore::Init(array("jquery","popup","window"));
include_once $_SERVER["DOCUMENT_ROOT"]."/sveden/lang/".$_SESSION["SESS_LANG_UI"]."/index.php";
class asmuinfo{
	public $adminGroup=array(1,8);
	private $adminScriptLoaded=false;
	private $localCssLoaded=false;
	private $classList=array();//список имен классов для вывода
	public function setСlassList($list){
		$this->classList=array();
		foreach ($list as $classData){
			$classname=$classData["classname"];
			$params=$classData["params"];
			$fileclass=__DIR__."/../cl-{$classname}/cl-{$classname}.php";
			require_once($fileclass);
			$class = '\\'.$classname;
			$ob=new $class();
			$ob->setparams($params);
			$this->classList[$classname]["ob"]=$ob;
		}
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
	public function setAdminGroups($x){
		
		foreach($this->classList as $className=>$classob){
			$classob["ob"]->setAdminGroups($x);
		}
		$this->adminGroup=array_merge($this->adminGroup,$x);
		
	}

	public function getHtml($buffer=false,$addscript=true,$addCss=true){
		$html="";$htmls="";$htmlcss="";
		global $APPLICATION;
		foreach($this->classList as $className=>$classob){
			if($buffer){ 
				$html.=$classob["ob"]->getHtml($buffer);
			}
			else {
				$html.=$classob["ob"]->getHtml();
			}

			
			$classDir="/sveden/class/cl-{$className}/";

			$localcss=$classDir."cl-".$className.".css";
			$localcssA=$_SERVER["DOCUMENT_ROOT"].$localcss;

			$localjs=$classDir."cl-".$className.".js";
			$localjsA=$_SERVER["DOCUMENT_ROOT"].$localjs;

			$localcssA=str_replace("//","/",$localcssA);
			$localjsA=str_replace("//","/",$localjsA);

			if(is_readable($localcssA)){
				$APPLICATION->SetAdditionalCSS($localcss);
				//$htmlcss.="\r\n<link rel=\"stylesheet\" href=\"https://dentmaster.ru$localcss\">\r\n";
			}

			if(file_exists($localjsA)){
				$APPLICATION->AddHeadScript($localjs);
				//{$htmls.="\r\n<script src=\"https://asmu.ru/$localjs\"></script>\r\n";}
			}
		}
		$localcss=__DIR__."/asmuinfo.css";
		if(file_exists($localcss) && $this->localCssLoaded==false){
			$fstyle=str_replace($_SERVER["DOCUMENT_ROOT"],"/",$localcss);
			$fstyle=str_replace("//","/",$fstyle);
			//$htmlcss.="\r\n<link rel=\"stylesheet\" href=\"$fstyle\">\r\n";
			$APPLICATION->SetAdditionalCSS($fstyle);
			$this->localCssLoaded=true;
		}		
		$localjs=__DIR__."/asmuinfo.js";
		if(file_exists($localjs)){
			$fjs=str_replace($_SERVER["DOCUMENT_ROOT"],"/",$localjs);
			$fjs=str_replace("//","/",$fjs);
			{$htmls.="\r\n<script src=\"$fjs\"></script>\r\n";}
		
		}		
		$htmls.="\r\n<script src=\"/sveden/files/files.js\"></script>\r\n";

		$fadmscript=str_replace($_SERVER["DOCUMENT_ROOT"],"https://dentmaster.ru/",__DIR__."/adm-script.js");
		$fadmscript=str_replace($_SERVER["DOCUMENT_ROOT"],"https://www.dentmaster.ru/",__DIR__."/adm-script.js");
		
		if ($this->isEditMode() && !$this->adminScriptLoaded){
			$this->adminScriptLoaded=true; 
			$htmls.="<script src=\"$fadmscript\"></script>\r\n";
			$htmls.="<script src=\"/bitrix/js/fileman/core_file_input.min.js?154874517921486\"></script>";


		}
		//$html.='<div class="popupInfo" id="wininfo"><p></p></div>';

		if($buffer===true){
			if($addscript) $html.=$htmls;
			$html.=$htmlcss;
			return $html;
		}else {	
			echo $html.$htmlcss;
			if($addscript) echo $htmls.$htmlcss;
		}
		
	}
}
$iip=getRealIpAddr2();
echo "<!-- ".$iip."-->";
$nofooter=false;
if(substr($iip,0,10)=="80.250.167"){
$APPLICATION->RestartBuffer();
$nofooter=true;
}
/*
if(substr($iip,0,10)=="78.109.138"){
$APPLICATION->RestartBuffer();
$nofooter=true;
}
*/
