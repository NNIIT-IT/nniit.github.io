function btneditfile(file){
	dlg=new BX.CEditorDialog({
		'TITLE':"Редактирование таблицы стилей страницы",
		'content_url':'/bitrix/admin/public_file_edit_src.php?lang=ru&path='+file+'&site=s1&back_url=%3Fbitrix_include_areas%3DN&templateID=asmu&siteTemplateId=niit',
		'width':'888',
		'height':'450',
		'min_width':'780',
		'min_height':'400'});
	dlg.Show();
}

