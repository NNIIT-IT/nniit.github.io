function prepodImport(){
		var btn_importFile  = {
		  title: 'Импортировать из файла',
		  id: 'addFilebtn',
		  name: 'addFilebtn',
		  className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-close',
		  action: function () {importFile();}

		};
		var popup = new BX.CDialog({
			'title': 'Import',
			'content_url': "/sveden/class/cl-teachingStaff/cl-teachingStaff_import.php",
			'content_post':'',
			'draggable': true,
			'resizable': true,
			'width':600,
			'height':150,
			'buttons': [
				btn_importFile,
				BX.CDialog.btnCancel	
			]

		});
		popup.Show();
}
function importFile(){
	opp=document.getElementById('opp').value;
	file=document.getElementById('xmlFile').files[0];
	filename=file.name;
	filetype=file.type;
	fileext=filename.split('.').pop();
	var reader = new FileReader();
	reader.readAsDataURL(file);
	reader.onload = readerEvent => {
		var content = readerEvent.target.result; // this is the content!
		
		if (fileext=="xml"){
			$.post(
				"/sveden/class/cl-teachingStaff/cl-teachingStaff_import.php",
				{"xmlFile":filename, "xmlFileType":filetype,"content":content,"opp":opp},
				function(data){
					$("#status").html(data);
				}
			);
		}else{
			alert("Не поддерживаемый тип файла\n\r допустимые типы файлов: .xml (msExcel XML");
		}
	   }
}


     

