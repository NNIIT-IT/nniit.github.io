<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Сведения об образовательной организации");
$APPLICATION->SetAdditionalCSS("/sveden/class/asmuinfo/asmuinfo.css");
?>
<?//не забыть собрать css!!?>
<br>
<!-- h1 id="voiceSveden" class="voicetext">Сведения об образовательной организации</h1 -->
<ul style="list-style-image: url('/local/templates/.default/img/cc14.png');list-style-type: none; ">
	<li><p><a href="/sveden/common/index.php"> Основные сведения </a></p></li>
	<li><p><a href="/sveden/struct/index.php"> Структура и органы управления образовательной организацией  </a></p></li>
	<li><p><a href="/sveden/document/index.php"> Документы  </a></p></li>
	<li><p><a href="/sveden/education/index.php"> Образование </a></p></li>
	<li><p><a href="/sveden/eduStandarts/index.php"> Образовательные стандарты и требования </a></p></li>
	<li><p><a href="/sveden/employees/index.php">Педагогический состав </a></p></li>
	<li><p><a href="/sveden/managers/index.php">Руководство</a></p></li>
	<li><p><a href="/sveden/objects/">Материально-техническое обеспечение и оснащённость образовательного процесса. Доступная среда</a></p></li>
	<li><p><a href="/sveden/grants/index.php"> Стипендии и иные виды материальной поддержки обучающихся</a></p></li>
	<li><p><a href="/sveden/paid_edu/index.php"> Платные образовательные услуги </a></p></li>
	<li><p><a href="/sveden/budget/index.php"> Финансово-хозяйственная деятельность </a></p></li>
	<li><p><a href="/sveden/vacant/index.php"> Вакантные места для приёма (перевода) обучающихся</a></p></li>
	<li><p><a href="/abitur/index.php"> Поступающему </a></p></li>
	<li><p><a href="/ob-universitete/inklyuzivnoe-obrazovanie"> Условия  обучения  инвалидов и лиц с ограниченными возможностями здоровья </a></p></li>
	<li><p><a href="/anticorruption"> Противодействие коррупции</a></p></li>
	<li><p><a href="/sveden/inter">Международное сотрудничество</a></p></li>
	<!-- li><p><a href="/sveden/ovz">Доступная среда</a></p></li -->
	<!--li><p><a href="/sveden/employees/rektor/vibory/">Выборы ректора</a></p></li-->
</ul>

<script> $( document ).ready(function() {$('#voiceSveden').click();})</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>