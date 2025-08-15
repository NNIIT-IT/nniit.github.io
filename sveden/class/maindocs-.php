<?
//РѕРїСЂРµРґРµР»СЏРµРј РґРµСЂРµРІРѕ РґРѕРєСѓРјРµРЅС‚РѕРІ РІ РІРёРґРµ РјР°СЃСЃРёРІР° ID СЌР»РµРјРµРЅС‚РѕРІ
class maindocs{
	
	private $sections;
	private $addelement;
	private $editscript="";
	public $cachetime=3600;
	public $useDiscription=false;
	function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);
        }
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
   private function addScript($element){

	$idlink="maindocs_".$element["ID"];
	$sectionID=$element["SECTION_ID"];
	$IBLOCK_ID=$element["IBLOCK_ID"];
	$elelement_id=$element["ID"];
	$return_url='/';
	$adminscript='<script type="text/javascript">';
	$adminscript.='if(window.BX&&BX.admin){';
	$adminscript.="BX.admin.setComponentBorder('".$idlink."');";
	$adminscript.="$('#$idlink').addClass(\"bx-context-toolbar-empty-area\");}";
	$adminscript.="if(window.BX)BX.ready(function() {";
	$adminscript.="(new BX.CMenuOpener({";
	$adminscript.="'parent':'".$idlink."', 'component_id':'page_edit_control', ";
	$adminscript.="'menu':[";

	$adminscript.="{'ICONCLASS':'bx-context-toolbar-edit-icon','TITLE':'Р РµРґР°РєС‚РёСЂРѕРІР°С‚СЊ РґР°РЅРЅС‹Рµ ', 'TEXT':'Р