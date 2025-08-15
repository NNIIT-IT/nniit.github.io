<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(__DIR__."/../cl-iasmuinfo/cl-iasmuinfo.php");
		//require_once($_SERVER['DOCUMENT_ROOT'].'/dompdf8/autoload.inc.php');
		//require_once($_SERVER["DOCUMENT_ROOT"]."/local/common/class/pdfsign/pdfsign.php");
		//use Dompdf\Dompdf;
		//use Dompdf\Options;
use asmuinfoclasses;
/*необходимо скорректировать на численность вакантных мест*/
class educhislen extends  iasmuinfo{
	private $edulevelID;
	private $russ;
	private $listitems=array();
	const cashTime=342000;
	const arBlocks=array(6,12);
	const emptyCell="Отсутствует";
	private $cashGUID;
	
	public function setparams($params){
		$this->edulevelID=0;
		$this->russ=0;

		if(isset($params["edulevelID"])) {
			$this->edulevelID=intval($params["edulevelID"]);
		}
		if(isset($params["russ"])) {
			$this->russ=intval($params["russ"]);
		}
		$this->cashGUID=md5("educhislen_".$this->edulevelID."_".$this->russ);
	}
	private function generateItems(){
$this->listitems=array();
$sql=<<<ssql
select 
sc.ID as id,
el.ID as opp,
pr.#EDUCODE# as code,
el.NAME as name,
if(pr.#PROFILE# is NULL,"",pr.#PROFILE#)  as profile,
el.IBLOCK_SECTION_ID as section,
if(sc.UF_EDU_FORM is NULL,"",sc.UF_EDU_FORM) as form,
pr.#SROKOBUCH#  as srokobuch,
if(sc.UF_CNT_FB is null,0,sc.UF_CNT_FB) as fb,
if(sc.UF_CNT_RB is null,0,sc.UF_CNT_RB) as rb, 
if(sc.UF_CNT_MB is null,0,sc.UF_CNT_MB) as mb, 
if(sc.UF_CNT_P is null,0,sc.UF_CNT_P) as p, 
if(sc.UF_CNT_FBI is null,0,sc.UF_CNT_FBI) as fbi,
if(sc.UF_CNT_RBI is null,0,sc.UF_CNT_RBI) as rbi, 
if(sc.UF_CNT_MBI is null,0,sc.UF_CNT_MBI)  as mbi, 
if(sc.UF_CNT_PI is null,0,sc.UF_CNT_PI) as pi, 
if(sc.UF_CNT_ALL is null,0,sc.UF_CNT_ALL) as sall
from `b_iblock_element` el 
left join `b_iblock_element_prop_s#iblock0#` pr on pr.IBLOCK_ELEMENT_ID=el.id
left join stud_count sc on sc.UF_OPP=el.id
where 
el.IBLOCK_ID=#iblock0#
and el.IBLOCK_SECTION_ID is not NULL 
and (((((now()>el.ACTIVE_FROM) or (el.ACTIVE_FROM is NULL)) and ((now()<=el.ACTIVE_TO)or (el.ACTIVE_TO is NULL))) and el.ACTIVE="Y")  or el.id is NULL)

ssql;
		if($this->edulevelID>0) $sql.=" and el.IBLOCK_SECTION_ID=".$this->edulevelID;
		$sql=$this->sqltoiblock($sql,$this::arBlocks);
//echo "<!--".$sql."-->";
		$rez=$this->BD->query($sql);
		while ($rec=$rez->fetch()){
			//$srokobuch= unserialize($rec["srokobuch"]);
			$forms=array();
			//print_r($srokobuch);
			$arforms=$this->unserform($rec["srokobuch"]);


			if (strlen($arforms[1])>4) $forms[]="Очная";
			if (strlen($arforms[2])>4) $forms[]="Заочная";
			if (strlen($arforms[3])>4) $forms[]="Очно-заочная";

			$key=md5($rec["id"].$rec["code"].$rec["profile"]);
			if($rec["form"]==""){
				foreach($forms as $form){
					$this->listitems[$rec["section"]]["name"]=$this::sectionsName[$rec["section"]];
					$this->listitems[$rec["section"]]["items"][$key][$form]=$rec;
					$this->listitems[$rec["section"]]["items"][$key][$form]["form"]=$form;
				}
			}elseif(in_array($rec["form"],$forms)){
				$this->listitems[$rec["section"]]["name"]=$this::sectionsName[$rec["section"]];
				$this->listitems[$rec["section"]]["items"][$key][$rec["form"]]=$rec; 


			}
		}
		
		//добавление недостающих данных
		foreach($this->listitems as $level=>$items){
			foreach($items["items"] as $itemKey=>$item){
				$xitem=array();
				if(is_array($item["Очная"])) {
						$xitem=$item["Очная"];
				}elseif(is_array($item["Заочная"])){
						$xitem=$item["Заочная"];
				}elseif(is_array($item["Очно-заочная"])){
						$xitem=$item["Очно-заочная"];
				}
				if(is_array($xitem)){
					//echo "<pre>";print_r($xitem);echo "</pre><br><br>";
					$xitem["fb"]=0;$xitem["rb"]=0;$xitem["mb"]=0;$xitem["p"]=0;
					$xitem["fbi"]=0;$xitem["rbi"]=0;$xitem["mbi"]=0;$xitem["pi"]=0;
				
				if(!is_array($item["Очная"])){
					$this->listitems[$level]["items"][$itemKey]["Очная"]=$xitem;
				}		
				if(!is_array($item["Заочная"])){
					$this->listitems[$level]["items"][$itemKey]["Заочная"]=$xitem;
					
				}		
				if(!is_array($item["Очно-заочная"])){
					$this->listitems[$level]["items"][$itemKey]["Очно-заочная"]=$xitem;
				}
	
				
				$yitem=$item["Очная"]["profile"];
				if($yitem!=""){
					$yitem=trim(preg_replace('/^(\d+).(\d+).(\d+)/','', $yitem));
					$yitem=trim(preg_replace('/^(\-)/',"", $yitem)); 
					$yitem=trim(preg_replace('/^(\–)/',"", $yitem));
					$this->listitems[$level]["items"][$itemKey]["Очная"]["profile"]=$yitem;
				}
				$yitem=$item["Заочная"]["profile"];
				if($yitem!=""){
					$yitem=trim(preg_replace('/^(\d+).(\d+).(\d+)/','', $yitem));
					$yitem=trim(preg_replace('/^(\-)/',"", $yitem)); 
					$yitem=trim(preg_replace('/^(\–)/',"", $yitem));
					$this->listitems[$level]["items"][$itemKey]["Заочная"]["profile"]=$yitem;
				}

				if($yitem!=""){
					$yitem=$item["Очно-заочная"]["profile"];
					$yitem=trim(preg_replace('/^(\d+).(\d+).(\d+)/','', $yitem));
					$yitem=trim(preg_replace('/^(\-)/',"", $yitem)); 
					$yitem=trim(preg_replace('/^(\–)/',"", $yitem));
					$this->listitems[$level]["items"][$itemKey]["Очно-заочная"]["profile"]=$yitem;
				}
				
				}
			}	
		}
		//echo "<pre>";print_r($this->listitems);echo "</pre>";
}
public function showHtml($buffer=true){
$this->cashGUID=md5("educhislen_ALL");
$memcache = new Memcache;
$memcache->addServer('unix:///tmp/memcached.sock', 0);

$html=trim($memcache->get($this->cashGUID));
if ($html=="" || $html==false || $this->isEditmode()){
	$this->generateItems();
$stylehtml=<<<stylehtml
<style type="text/css">
.eduChislen{
min-width: 600px;
width: 90%;
border: solid thin lightgray;
border-collapse: collapse;
font-size:10px;
}
.eduChislen th {border: solid thin lightgray;font-weight: normal;padding: 3px;}
.eduChislen thead th {background-color: #b0b0b01a}
.eduChislen tbody th {background-color: #f4feff;}
.eduChislen td {border: solid thin lightgray;padding: 3px;}
.eduChislen td:nth-of-type(n+3) {text-align:center;}
.eduChislen tbody td:nth-of-type(2) span:nth-of-type(2) {font-size: 0.9em;color:blue;}
</style>
<h2>Информация о численности обучающихся по реализуемым образовательным программам по источникам финансирования</h2>
stylehtml;
$html=<<<htmlh
<table class="eduChislen" >
<thead>
<tr>
<th rowspan="2">Код</th>
<th rowspan="2">Наименование специальности / направления подготовки</th>
<th rowspan="2">Уровень образования</th>
<th rowspan="2">Форма обучения</th>
<th colspan="4">Численность обучающихся / из них иностранных граждан за счет (количество человек)</th>
<th rowspan="2">Общая численность обучающихся </th>
</tr>
<tr>
<th style="top: 2em;">За счет бюджетных ассигнований федерального бюджета</th>
<th style="top: 2em;">за счёт бюджетов субъектов Российской Федерации</th>
<th style="top: 2em;">за счёт местных бюджетов</th>
<th style="top: 2em;">за счёт средств физических и (или) юридических лиц</th>

</tr>
</thead>
<tbody>
htmlh;

if(count($this->listitems)==0){
		
		$html.="<tr itemprop=\"eduChislen\" class=\"maindocselementHB\" id=\"eduChislen_new\" data-id=\"0\" data-iblock=\"8\" title=\" \">\r\n";
		$html.="<td itemprop=\"eduCode\">-</td>\r\n";
		$html.="<td ><span itemprop=\"eduName\" >".$this::emptyCell."</span><br><span itemprop=\"eduProfile\">".$this::emptyCell."</span></td>\r\n";
		$html.="<td itemprop=\"eduLevel\" >-</td>\r\n";
		$html.="<td itemprop=\"eduForm\">-</td>\r\n";
		$html.="<td><span itemprop=\"numberBF\">0</span> / <span itemprop=\"numberBFF\">0</span></td>\r\n";
		$html.="<td><span itemprop=\"numberBR\">0</span> / <span itemprop=\"numberBRF\">0</span></td>\r\n";
		$html.="<td><span itemprop=\"numberBM\">0</span> / <span itemprop=\"numberBMF\">0</span></td>\r\n";
		$html.="<td><span itemprop=\"numberP\">0</span> / <span itemprop=\"numberPF\">0</span></td>\r\n";
		$html.="<td itemprop=\"numberAll\">0</td>\r\n";					
		$html.="</tr>\r\n";


}
foreach($this->listitems as $sectionID=>$section){
	if(count($section["items"])>0) $html.="<tr>\r\n<th colspan=\"9\">".$section["name"]."</th>\r\n</tr>\r\n";
	foreach($section["items"] as $items){
		foreach	($items as $formName=>$item){
			//$formName=$item["form"];
			$addClass=($formName=="Очная")?"":" hide ";
			$addAttr=($formName=="Очная")?" rowspan=\"3\" ":"";
			$idrow="row-".rand(1,99999).$item['ID'];
			$html.="<tr itemprop=\"eduChislen\" class=\"maindocselementHB \"  data-opp=\"{$item['opp']}\" id=\"{$idrow}\" data-id=\"{$item['id']}\" data-iblock=\"8\" title=\"{$item["name"]}\">\r\n";
			$html.="<td {$addAttr} class=\"{$addClass}\" itemprop=\"eduCode\"  >{$item["code"]}</td>\r\n";
			$html.="<td {$addAttr} class=\"{$addClass}\" ><span itemprop=\"eduName\" >{$item["name"]}</span><br><span itemprop=\"eduProfile\">{$item["profile"]}</span></td>\r\n";
			$html.="<td {$addAttr} itemprop=\"eduLevel\" class=\"hide\">{$item["level"]}</td>\r\n";
			$html.="<td itemprop=\"eduForm\">{$formName}</td>\r\n";
			$cntall=intval($item["fb"])+intval($item["rb"])+intval($item["mb"])+intval($item["p"]);
			//$cntall=intval($item["sall"]);
			$cntalli=intval($item["fbi"])+intval($item["rbi"])+intval($item["mbi"])+intval($item["pi"]);
			
			$html.="<td><span itemprop=\"numberBF\">{$item['fb']}</span> / <span itemprop=\"numberBFF\">{$item['fbi']}</span></td>\r\n";
			$html.="<td><span itemprop=\"numberBR\">{$item['rb']}</span> / <span itemprop=\"numberBRF\">{$item['rbi']}</span></td>\r\n";
			$html.="<td><span itemprop=\"numberBM\">{$item['mb']}</span> / <span itemprop=\"numberBMF\">{$item['mbi']}</span></td>\r\n";
			$html.="<td><span itemprop=\"numberP\">{$item['p']}</span> / <span itemprop=\"numberPF\">{$item['pi']}</span></td>\r\n";
			$html.="<td itemprop=\"numberAll\">".($cntall)."</td>\r\n";	
			$html.="</tr>\r\n";
		}
	}
}
$html.="</tbody></table>";
/*
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
			$f_name='rezpriem_'.$god.".pdf";
			$fs_name='rezpriem_'.$god."_s.pdf";
			$texthtml='<html><head><meta http-equiv=Content-Type content="text/html; charset=utf-8"><head></head><body>'.$stylehtml.$DOG_FILE_TMP.'</body></html>';

			$dompdf = new DOMPDF($options);// Создаем обьект
			$dompdf->load_html($texthtml); // Загружаем в него наш html код

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


			$hrefF="<p><a title=\"Информация о численности обучающихся по реализуемым образовательным программам за счет бюджетных ассигнований федерального бюджета, ";
			$hrefF.="бюджетов субъектов Российской Федерации, местных бюджетов и по договорам об образовании за счет средств физических и (или) юридических лиц, ";
			$hrefF.="размещенная в форме электронного документа, подписанного электронной подписью\" target=\"blank\" download=\"\" class=\"linkicon\" href=\"";
			$hrefF.=str_replace($_SERVER['DOCUMENT_ROOT'],"",$ref)."\"";
			$hrefF.=" itemprop=\"eduChislenEl\">Информация о численности обучающихся по реализуемым образовательным программам за счет бюджетных ассигнований федерального бюджета, ";
			$hrefF.=" бюджетов субъектов Российской Федерации, местных бюджетов и по договорам об образовании за счет средств физических и (или) юридических лиц, ";
			$hrefF.=" размещенная в форме электронного документа, подписанного электронной подписью</a>";
			$hrefF.=" <span class=\"epinfo\" title=\"Документ зарегистрирован в системе документооборота АГМУ Подписант **** \"></span></p>";
			$html=$hrefF.$html;
*/			
			$memcache->set($this->cashGUID, $html, false, $this->cashTime);
} //html=

$memcache->close();
if($buffer) return $html; else echo $html;

}

}//class
?>