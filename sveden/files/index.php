<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Документ");
$id=intval($_GET["id"]);
$json=isset($_GET["json"]);
if($json) $APPLICATION->RestartBuffer();
$url="";
$DESCRIPTION="";
$name="";
$docname="";
$buckUrl="";
if($id>0) {
	$arFile=CFile::GetFileArray($id);
	$url=$arFile["SRC"];
	$subDir=$arFile["SUBDIR"];
	$fileName=$arFile["FILE_NAME"];
	$CONTENT_TYPE=$arFile["CONTENT_TYPE"];
	$DESCRIPTION=$arFile["DESCRIPTION"];
	//print_r($arFile);
	
	//$urldata=base64_encode(json_encode(array("docname"=>$elementname, "urlname"=>"Вернуться", "urlpath"=>"/sveden/document/")));
	if(isset($_GET["urldata"])){
		$arUrldata=\Bitrix\Main\Web\Json::decode(base64_decode($_GET["urldata"]));
		//	print_r($arUrldata);
		if(is_array($arUrldata)){
			$name=htmlspecialchars($arUrldata["urlname"]);
			$docname=htmlspecialchars($arUrldata["docname"]);
			if($docname!="")$DESCRIPTION=$docname;
			$buckUrl=htmlspecialchars($arUrldata["urlpath"]);
		}
	}
	
	//image/jpeg 	image/png text/plain image video/mp4 video/x-ms-wmv video/webm image/webp
	if(!$json)  echo "<div class=\"container\">";
	if($buckUrl!=""){
		$arUrl=parse_url($buckUrl);
		$buckUrl=$arUrl["path"];
		$arPath=explode("/",$buckUrl);
		$fullpath="";
		print_r($arPath);
		foreach($arPath as $xpath){
			if($xpath!="index.php"){
				$fullpath.="/".$xpath;

			}else{
				if($arUrl["query"]!="") $fullpath.="?".$arUrl["query"];
			}
			if($fullpath!="/"){
				$options = array(
				        CURLOPT_RETURNTRANSFER => true,
				        CURLOPT_HEADER         => false,
				        CURLOPT_FOLLOWLOCATION => true,
				        CURLOPT_AUTOREFERER    => true,
				        CURLOPT_CONNECTTIMEOUT => 10,
					CURLOPT_RANGE=>"0-1000",
				    ); 
				    $ch = curl_init("https://asmu.ru".$fullpath); 
				    curl_setopt_array( $ch, $options ); 
				    $content = curl_exec( $ch ); 
				    curl_close( $ch ); 
			 		preg_match_all( "|<title>(.*)</title>|sUSi", $content, $titles);    
					$name=str_replace("АГМУ","",$titles[1][0]);
					if($xpath=="index.php") $name="Просмотр документа";
					$APPLICATION->AddChainItem($name, htmlspecialcharsex($buckUrl));
			}
			
		}
		//print_r($arPath);
//		if($arUrl["query"]!="") $buckUrl.="?".$arUrl["query"];
		 
//		$APPLICATION->AddChainItem("Просмотр документа", htmlspecialcharsex("/files"));
//		echo "<a class=\"link\" href=\"{$buckUrl}\">{$name}</a><br><br>";
	}
	if(!$json)  echo "<h1 class=\"brown\" >Просмотр документа</h1>";
	if($DESCRIPTION!="") echo "<h2 class=\"brown\" >{$DESCRIPTION}</h2>";
	if($url!="" &&  in_array($CONTENT_TYPE,array("application/pdf"))){
	echo "<style>@media print{body{display:none!important;}}</style>";
	echo "<object data=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"700px\"><a href=\"{$url}\">Download PDF file</a></object>";
	}
	if($url!="" &&  in_array($CONTENT_TYPE,array("image/jpeg","image/png","image"))){
	
	if(!$json)  echo "<style>@media print{body{display:none!important;}}</style>";
	if(!$json)  echo "<img src=\"{$url}\" height=\"600px\">";
	} 
	if($url=="") echo "документ не найден";
	if(!$json)  echo "</div>";
}
if(!$json)  require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>