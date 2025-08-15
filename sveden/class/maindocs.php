<?
//РѕРїСЂРµРґРµР»СЏРµРј РґРµСЂРµРІРѕ РґРѕРєСѓРјРµРЅС‚РѕРІ РІ РІРёРґРµ РјР°СЃСЃРёРІР° ID СЌР»РµРјРµРЅС‚РѕРІ
class maindocs{
	
	private $sections;
	private $addelement;
	private $editscript="";
	private $items=array();
	private $itemsTree=array();
	private $elementOnly=false;
	public $cachetime=3600;
	public $useDiscription=false;
	public $backURL="/";
	public $open="";
	public $mainSection=0;

	function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);
        }
    }
    public function setBackUrl($b){
	$this->backURL=urlencode($b);
	}
    function __construct1($a1)
    {
	
	if (is_array($a1)) 
		$this->sections=$a1; 
	else
		$this->sections=array(intval($a1));
	$this->addelement=array();
	
	
    }
   
    function __construct2($a1,$a2)
    {
	if (is_array($a1)) 
		$this->sections=$a1; 
	else
		$this->sections=array(intval($a1));
	if (is_array($a2)) 
		$this->addelement=$a2; 
	else
		$this->addelement=array(intval($a2));

    }
   function __construct3($a1,$a2,$a3)
    {
	if (is_array($a1)) 
		$this->sections=$a1; 
	else
		$this->sections=array(intval($a1));
	if (is_array($a2)) 
		$this->addelement=$a2; 
	else
		$this->addelement=array(intval($a2));
	$this->elementOnly=intval($a3)==1;

    }
   private function addScriptAdd($idlink,$IBLOCK_ID){
	$return_url=$this->backURL;
	if ($this->mainSection>0){
		$sectionID=$this->mainSection;
		$adminscript='';
		$adminscript.='if(window.BX&&BX.admin){';
		$adminscript.="BX.admin.setComponentBorder('".$idlink."');";
		$adminscript.="$('#$idlink').addClass(\"bx-context-toolbar-empty-area\");}";
		$adminscript.="if(window.BX)BX.ready(function() {";
		$adminscript.="(new BX.CMenuOpener({";
		$adminscript.="'parent':'".$idlink."', 'component_id':'page_edit_control', ";
		$adminscript.="'menu':[";
	
		$adminscript.="{'ICONCLASS':'bx-context-toolbar-create-icon','TITLE':'Р РµРґР°РєС‚РёСЂРѕРІР°С‚СЊ РґР°РЅРЅС‹Рµ ', 'TEXT':'Р”РѕР±Р°РІРёС‚СЊ РґРѕРєСѓРјРµРЅС‚', ";
		$adminscript.="'ONCLICK':'";
		$adminscript.="(new BX.CAdminDialog({\'content_url\':\'/bitrix/admin/iblock_element_edit.php?";
	
	
		$adminscript.="IBLOCK_ID=$IBLOCK_ID&ID=&type=News&lang=ru&force_catalog=&filter_section=".$sectionID."&IBLOCK_SECTION_ID=".$sectionID."&bxpublic=Y&from_module=iblock";
		$adminscript.="&return_url=$return_url&siteTemplateId=asmu\',\'width\':\'1014\',\'height\':\'657\'})).Show()";
		$adminscript.="','DEFAULT':true}";	
		$adminscript.="]})).Show();});";
		$this->editscript.=$adminscript;
	   }
   }
   private function addScript($element){
	global $USER;
	$idlink="maindocs_".$element["ID"];
	$sectionID=$element["SECTION_ID"];
	$IBLOCK_ID=$element["IBLOCK_ID"];
	$elelement_id=$element["ID"];
	$return_url=$this->backURL;
	if ($this->mainSection>0) $sectionID=$this->mainSection;

	$adminscript='';

	$adminscript.='if(window.BX&&BX.admin){';
	$adminscript.="BX.admin.setComponentBorder('".$idlink."');";
	$adminscript.="$('#$idlink').addClass(\"bx-context-toolbar-empty-area\");}";

	$adminscript.="BX.ready(function() {";
	$adminscript.="(new BX.CMenuOpener({";
	$adminscript.="'parent':'".$idlink."', 'component_id':'page_edit_control', ";
	$adminscript.="'menu':[";

	$adminscript.="{'ICONCLASS':'bx-context-toolbar-edit-icon','TITLE':'Р РµРґР°РєС‚РёСЂРѕРІР°С‚СЊ РґР°РЅРЅС‹Рµ ', 'TEXT':'Р