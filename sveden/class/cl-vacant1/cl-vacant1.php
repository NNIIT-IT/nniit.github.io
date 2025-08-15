<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/dompdf8/autoload.inc.php');
		require_once($_SERVER["DOCUMENT_ROOT"]."/local/common/class/pdfsign/pdfsign.php");
		use Dompdf\Dompdf;
		use Dompdf\Options;

use asmuinfoclasses;
/*необходимо скорректировать на численность вакантных мест*/
class vacant1 extends  iasmuinfo{
	const cashTime=342000;
	private $cashGUID;
	private $arVacant;
	private $testmode;
	public function setparams($params){
		$this->edulevelID=0;
		$this->russ=0;
		$this->testmode=false;
		if(isset($params["testmode"])) {
			$this->testmode=true;
		} 
		if(isset($params["edulevelID"])) {
			$this->edulevelID=intval($params["edulevelID"]);
		}
		if(isset($params["russ"])) {
			$this->russ=intval($params["russ"]);
		}
		$this->cashGUID=md5("educhislen_".$this->edulevelID."_".$this->russ);
	}
	function sortArray(&$a) { 
		foreach( $a as &$b){
			foreach( $b as &$c){
				ksort($c["data"]);
			}
		}
		  return $a;
	}
	private function generateItems(){
		$studentVacant=array();
		try {
			
			$wsdl = new SoapClient("http://1C.asmu.local/Univer/ws/ws4.1cws?wsdl",//dit
				array('login'          => "web",
				'password'       => "Dtl0vjcnM",//Dtl0vjcnM
				'trace'          => 1,
				'soap_version'   => SOAP_1_2,
				'cache_wsdl'     => WSDL_CACHE_NONE, //WSDL_CACHE_MEMORY, //, WSDL_CACHE_NONE, WSDL_CACHE_DISK or WSDL_CACHE_BOTH
				'encoding'=>'UTF-8',
				'exceptions'=>0));//Dtl0vjcnM
			}catch(SoapFault $e) {var_dump($e);}
			$reccount=0;
			$zap=array('parameters'=>array('Period'=>""));
			$resultF =  $wsdl->__soapCall("stCountNow",$zap);
			if (is_soap_fault($resultF)) {
				if ($debug){echo 'ERROR SOAP ';echo '<pre>';var_dump($resultF);echo '</pre>';}
				trigger_error("SOAP Fault: (faultcode: {$resultF->faultcode}, faultstring: {$resultF->faultstring})", E_ERROR);
			} else {

				$stroka_json=$resultF->return;
				$arr=json_decode($stroka_json,true);

				$cg=intval(date("Y"));
				$cm=intval(date("m"));
				if($cm<7) $curGod=$cg; else $curGod=$cg-1;
				
				$reccount=count($arr);
				$studentVacant=Array();
				foreach ($arr as $stCountRec){
					$kurs=intval($stCountRec["kurs"]);	
					$xkey=$stCountRec["specCode"]."-".((trim($stCountRec["faculty"])=="ФИС")?"1":"0");
					$budLevel=trim($stCountRec["budLevel"]);
					$stCountRec["startgod"]=intval(preg_replace("/[^0-9]/", '', $stCountRec["startgod"]));
					$AC="";
					//условный перевод академистов на курс соответствующий плану 
					if(intval($stCountRec["akadem"])==1 && $stCountRec["startgod"]>0 && $kurs>0){
						$nkurs=$curGod-$stCountRec["startgod"];
						//$AC="AC";

						if($stCountRec["kurs"]!=$nkurs) {
							if($nkurs<7 && $nkurs>0) {
								//$kurs=$nkurs;
								//$stCountRec["kurs"]=$kurs;
								
							}
						}
						if($budLevel=="FB"){
							$studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$kurs]["FBAC"]+=intval($stCountRec["studentCount"]);
						}
						if($budLevel=="FBC"){
							$studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$kurs]["FBCAC"]+=intval($stCountRec["studentCount"]);
						}	
						if($budLevel=="P"){
							$studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$kurs]["PAC"]+=intval($stCountRec["studentCount"]);
						}	
					}
					if($kurs>0 && strlen($xkey)>2){
						$skurs=$kurs;
						$cntFB=intval($studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$skurs]["FB".$AC]);
						$cntFBC=intval($studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$skurs]["FBC".$AC]);
						$cntP=intval($studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$skurs]["P".$AC]);
						//$xkey=$stCountRec["specCode"]."-".((trim($stCountRec["faculty"])=="ФИС")?"1":"0");

						$studentVacant[$stCountRec["eduLevel"]][$xkey]["code"]=$stCountRec["specCode"];

						if($budLevel=="FB"){

							$studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$skurs]["FB".$AC]=$cntFB+$stCountRec["studentCount"];
						}
						if($budLevel=="FBC"){
							$studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$skurs]["FBC".$AC]=$cntFBC+$stCountRec["studentCount"];
						}
						if($budLevel=="FBI"){
							//$studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$skurs]["FB".$AC]=$cntFB+$stCountRec["studentCount"];
							$studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$skurs]["FBI".$AC]=$stCountRec["studentCount"];
						}
						if($budLevel=="P"){
							$studentVacant[$stCountRec["eduLevel"]][$xkey]["data"][$stCountRec["eduForm"]][$skurs]["P".$AC]=$cntP+$stCountRec["studentCount"];
						}
							
					}
				}
//if($this->testmode) echo 'actual: <pre>';print_r($studentVacant);echo '</pre>';	
//echo '<pre>';print_r($studentVacant);echo '</pre>';				
				$zap=array('parameters'=>array());
				$resultF =  $wsdl->__soapCall('CountMestPlan',$zap);
				if (is_soap_fault($resultF)) {
					echo 'ERROR SOAP ';var_dump($resultF);
					trigger_error("SOAP Fault: (faultcode: {$resultF->faultcode}, faultstring: {$resultF->faultstring})", E_ERROR);
				} else {

					$arRes=json_decode($resultF->return,true);
//if($this->testmode) echo 'plan: <pre>';print_r($arRes);echo '</pre>';	
					$arvRes=array();
					foreach ($arRes as $drec){
						$kurs=intval($drec["kurs"]);
						$skurs=$kurs;
						$vkey=trim($drec["sрecKod"])."-".(($drec["faculty"]=="ФИС")?"1":"0");
						$budgetKod=trim($drec["budgetKod"]);

/*=======================*/						
						//набор на 1 курс 1 семестр не проводим
						$currentM=intval(date("m"));


						$studentVacant[$drec["level"]][$vkey]["data"][$drec["form"]][$skurs]["noVacant"]=false;

						// $studentVacant[$drec["level"]][$vkey]["data"][$drec["form"]][$skurs]["noVacant"]=($currentM>6 && ($kurs==1));
/*======================*/
						if($kurs>0 && (strlen($vkey)>1)) {
							
							if(!isset($studentVacant[$drec["level"]][$vkey]["spec"])) $studentVacant[$drec["level"]][$ckey]["spec"]=$drec["sрecKod"];

							if($budgetKod=="FB"){
								$cntFB0=intval($studentVacant[$drec["level"]][$vkey]["data"][$drec["form"]][$skurs]["FB_Plan"]);
								$studentVacant[$drec["level"]][$vkey]["data"][$drec["form"]][$skurs]["FB_Plan"]=$cntFB0+intval($drec["vacancies"]);
							}
							if($budgetKod=="FBC"){
								$cntFBC0=intval($studentVacant[$drec["level"]][$vkey]["data"][$drec["form"]][$skurs]["FBC_Plan"]);
								$studentVacant[$drec["level"]][$vkey]["data"][$drec["form"]][$skurs]["FBC_Plan"]=$cntFBC0+intval($drec["vacancies"]);
							}
							if($budgetKod=="FBI"){
								$cntFBI0=intval($studentVacant[$drec["level"]][$vkey]["data"][$drec["form"]][$skurs]["FBI_Plan"]);
								$studentVacant[$drec["level"]][$vkey]["data"][$drec["form"]][$skurs]["FBI_Plan"]=$cntFBI0+intval($drec["vacancies"]);
							}
							if($budgetKod=="P"){
								$cntP0=intval($studentVacant[$drec["level"]][$vkey]["data"][$drec["form"]][$kurs]["P_Plan"]);
								$studentVacant[$drec["level"]][$vkey]["data"][$drec["form"]][$skurs]["P_Plan"]=$cntP0+intval($drec["vacancies"]);
							}
						}
					}

//if($this->testmode) echo 'actual: <pre>';print_r($studentVacant);echo '</pre>';	

					//добавляем недостоющие данные из BD
					$arSpec=array();
					$sql="SELECT distinct trim(e.name) as name, trim(s.PROPERTY_84) as skod,trim(s.PROPERTY_121) as napr, IF(s.PROPERTY_577=22700,\"1\",\"0\") as fis  FROM `b_iblock_element_prop_s104` s  left join b_iblock_element e on e.id=s.IBLOCK_ELEMENT_ID where e.ACTIVE='Y'";
					//if (count($studentVacant)>0){
						$rez=$this->BD->query($sql);
						while ($ob=	$rez->fetch()){
							
							$kod=$ob["skod"];
							$kod2="";
							$fis=$ob["fis"];
							$napr=$ob["napr"];
							if($ob["napr"]) {
									$napr=trim($ob["napr"]);
									if(mb_strlen($napr)>7){

										
										$snprKod=preg_replace("/[^.0-9]/", '', mb_substr($napr,0,10));
										if(strlen($snprKod)>2){
											$kod2=$snprKod;
										} else $kod2="";
										
									}
									
							}		
							
							if($kod2!=""){
								$arSpec[$kod2."-".$fis]=array("name"=>$ob["name"],"kod"=>$kod,"prof"=>$napr);
							}else{
								$arSpec[$kod."-".$fis]=array("name"=>$ob["name"],"kod"=>$kod,"prof"=>$napr);
							}
						}
					//}
//echo '<pre>';print_r($arSpec);echo '</pre>';
					foreach($studentVacant as $levelKey=>$level){
						foreach ($level as $specKod=>$specData){
							//echo '<hr>'.$levelKey."  ".$specKod."  <br>";	print_r($arSpec[$specKod]);echo "<br>";
							if(isset($arSpec[$specKod])) {
									$this->arVacant[$levelKey][$specKod]=$specData;
									$this->arVacant[$levelKey][$specKod]["addData"]=$arSpec[$specKod];
							}	
						}
					}
//echo '<pre><hr>';print_r($this->arVacant);echo '</pre>';					
					//$this->arVacant=$studentVacant;
				}
			}
 }// generateItems
function gethtmlevel($levelName,$levelSpec){
	$html='<h3>'.$levelName.'</h3>';
	$html.='<table class="eduVacant"><thead><tr class="spheadR0"><th colspan="9"> </th></tr>';
	$html.='<tr class="spheadR2" >';
	$html.='<th>Код</th>';
	$html.='<th>Наименование профессии, специальности, направления подготовки, наименование группы научных специальностей</th>';
	$html.='<th>Уровень образования</th>';
	$html.='<th>Образовательная программа, направленность, профиль, шифр и наименование научной специальности</th>';
	$html.='<th>Курс</th>';
	$html.='<th>Форма обучения (очная/заочная/очно-заочная)</th>';
	$html.='<th>Количество мест за счёт бюджетных ассигнований федерального бюджета</th>';
	$html.='<th>Количество мест за счёт бюджетных ассигнований бюджетов субъекта Российской Федерации</th>';
	$html.='<th>Количество мест за счёт  бюджетных ассигнований местных бюджетов</th>';
	$html.='<th>Количество мест за счёт средств физических и (или) юридических лиц</th>';
	$html.='</tr></thead><tbody>';
	foreach($levelSpec as $specKey=>$formSpec){
		$kursSpecData=$formSpec["addData"];
		$specCode=$formSpec["code"];
		
		foreach($formSpec["data"] as $formName=>$formSpec){
			ksort($formSpec);
			foreach($formSpec as $kursName=>$kursSpec){
				$vacantFB=0;
				$vacantP=0;
				$vacantFBI="";
				if($kursName>0 ){
					$FB_Plan=intval($kursSpec["FB_Plan"])+intval($kursSpec["FBC_Plan"]);
					$FB_Fact=intval($kursSpec["FB"])+intval($kursSpec["FBC"]);
					$FB_AC=intval($kursSpec["FBAC"])+intval($kursSpec["FBCAC"]);
					$P_Plan=$kursSpec["P_Plan"];
					$P_Fact=$kursSpec["P"];
					/*
					$FB_Fact+=intval($kursSpec["FBCAC"])+intval($kursSpec["FBAC"]);
					$P_Fact+=intval($kursSpec["PAC"]);
					*/
/* ----------------------------------------------------------------------------------------------------------------
					if($FB_Fact<=$FB_Plan  && $FB_Plan>=0) {
						$vacantFB=intval($FB_Plan)."-".$FB_Fact."=<b>".($FB_Plan-$FB_Fact)."</b>";


					}
					if($kursSpec["FB"]>$kursSpec["FB_Plan"]  && $kursSpec["FB_Plan"]>0) {
						$vacantFB=intval($kursSpec["FB_Plan"])." < ".intval($kursSpec["FB"])."=<b>0</b>";

					}
					if($kursSpec["FBI"]<=$kursSpec["FBI_Plan"]  && $kursSpec["FBI_Plan"]>=0) {
						$vacantFBI=intval($kursSpec["FBI_Plan"])."-".intval($kursSpec["FBI"])."=<b>".($kursSpec["FBI_Plan"]-$kursSpec["FBI"])."</b>";
						
					}	
					if($kursSpec["P"]<=$kursSpec["P_Plan"] && $kursSpec["P_Plan"]>=0) {
						$vacantP=intval($kursSpec["P_Plan"])."-".intval($kursSpec["P"])."=<b>".($kursSpec["P_Plan"]-$kursSpec["P"])."</b>";
					}
					if($kursSpec["noVacant"]){$vacantFB="0"; $vacantP="0";}

					if($vacantFBI!=""){$vacantFB.=" <span class=\"red\"> {$vacantFBI} </span>";}

 ------------------------------------------------------------------------------------------------------------------*/
					
					if($FB_Fact<=$FB_Plan  && $FB_Plan>=0) {
						$vacantFB=$FB_Plan-$FB_Fact;
						if($this->testmode) $vacantFB.="<br>$FB_Plan / FB:{$kursSpec["FB"]} / FBC:{$kursSpec["FBC"]} /AC: $FB_AC ";

					}
					
					
					if($P_Fact<=$P_Plan && $P_Plan>=0) {
						$vacantP=($P_Plan-$P_Fact);
					}
					if($kursSpec["noVacant"]){$vacantFB="0"; $vacantP="0";}

					

					

					if($kursSpecData['prof']=="") {$kursSpecData['prof']="отсутствует";}
					$html.="<tr>";
					$html.="<td itemprop=\"eduCode\">{$kursSpecData['kod']}</td>";
					$html.="<td itemprop=\"eduName\">{$kursSpecData['name']}</td>";
					$html.="<td itemprop=\"eduLevel\">{$levelName}</td>";
					$html.="<td itemprop=\"eduProf\">{$kursSpecData['prof']}</td>";
					$html.="<td itemprop=\"eduCourse\">{$kursName}</td>";
					$html.="<td itemprop=\"eduForm\">{$formName}</td>";
					$html.="<td itemprop=\"numberBFVacant\">{$vacantFB}</td>";
					$html.="<td itemprop=\"numberBRVacant\">0</td>";
					$html.="<td itemprop=\"numberBMVacant\">0</td>";
					$html.="<td itemprop=\"numberPVacant\">{$vacantP}</td>";
					$html.="</tr>";
					
				}	
			}
		}
	}
	return $html."</tbody></table>";
}
public function showHtml($buffer=true){
	$this->cashGUID=md5("cl-vacantALL");
	$memcache = new Memcache;
	$memcache->addServer('unix:///tmp/memcached.sock', 0);
$stylehtml0=file_get_contents(__DIR__."/cl-vacant.css");
$stylehtml='<style type="text/css">'.$stylehtml0.'</style><h2>Вакантные места для приема (перевода) обучающихся</h2><span>[по состоянию на '.date("d.m.Y").']</span><br><br>';

global $USER;
$editMode=$USER->isAdmin() && $this->isEditmode();
//строим таблицу

	$html="";//trim($memcache->get($this->cashGUID));//$html=false;
	if ($html=="" || $html==false || $editMode || $this->testmode){
		$this->generateItems();
		//echo "<pre>";print_r($this->arVacant);echo "</pre>";
		$html="";  
		$html.="<div itemprop=\"vacant\">";
		$html.=$this->gethtmlevel("СПО",$this->arVacant["СПО"]);
		$html.=$this->gethtmlevel("Специалитет",$this->arVacant["Специалитет"]);
		$html.=$this->gethtmlevel("Ординатура",$this->arVacant["Ординатура"]);
//echo "<pre>";print_r($this->arVacant["Аспирантура"]);echo "</pre>";
		$html.=$this->gethtmlevel("Аспирантура",$this->arVacant["Аспирантура"]);
		//foreach ($this->arVacant as $levelName=>$levelSpec){}
		$html.="</div>";
		
		//генерация ссылки но pdf
				$options = new Options();
				$options->set('defaultFont', 'times');
				$options->set('defaultPaperSize', 'a4');
				$options->set('orientation', 'landscape');
				$rectorID=19144;
				$documentfolder	="/sveden/education/study/";
		//генерация файла
					//echo "ID_DOC=".$id_doc;
					$fid=0;$god=date("Y");
					$DOG_FILE_TMP=$html."<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";//место для подписи
					$f_name='vacant_'.$god.".pdf";
					$fs_name='vacant_'.$god."_s.pdf";
					$texthtml='<html><head><meta http-equiv=Content-Type content="text/html; charset=utf-8"><head></head><body>'.$stylehtml.$DOG_FILE_TMP.'</body></html>';

					$dompdf = new DOMPDF($options);// Создаем обьект
					$dompdf->load_html($texthtml); // Загружаем в него наш html код
					$dompdf->set_paper('letter', 'landscape');
					$dompdf->render(); // Создаем из HTML PDF
					$doutput = $dompdf->output();
				//file_put_contents($_SERVER['DOCUMENT_ROOT'].$f_name, $output);
					$outfile=$_SERVER['DOCUMENT_ROOT'].$documentfolder.$f_name;
					$outsfile=$_SERVER['DOCUMENT_ROOT'].$documentfolder.$fs_name;
					if (file_exists($outfile)) {unlink($outfile);}
					if (!file_exists($outfile)) {touch($outfile);}
					file_put_contents($outfile, $doutput, LOCK_EX);
							
					//подпись документа ЭЦП
					$pdfSigner = new  Signatures_lib();
						try {
							$ref=$pdfSigner->signfile($outfile,true,$rectorID);
						} catch (Exception $e) {
							$ref=$outfile;
						} finally {
							
						}
						unset($pdfSigner);
					
					$hrefF="<p><a title=\"Вакантные места для приема (перевода)  обучающихся от ".date("d.m.Y");
					$hrefF.=" в форме электронного документа, подписанного электронной подписью\" ";
					$hrefF.=" target=\"blank\" download=\"\" class=\"linkicon\" href=\"";
					$hrefF.=str_replace($_SERVER['DOCUMENT_ROOT'],"",$ref)."\"";
					$hrefF.=" itemprop=\"localAct\">Вакантные места для приема (перевода) от ".date("d.m.Y")."</a>";
					$hrefF.=" <span class=\"epinfo\" title=\"Документ зарегистрирован в системе документооборота АГМУ Подписант Шереметьева Ирина Игоревна\"></span></p>";
					$html=$hrefF.$html;
		
		if(!$this->testmode) $memcache->set($this->cashGUID, $html, false, $this->cashTime);
	} 
	$memcache->close();
if($buffer) return $html; else echo $html;
}

}//class
?>