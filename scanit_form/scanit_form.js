$(document).ready(function(){
	var scanit_form=new ScanitForm('http://flexa.cz/scanit_form/scanit_form.php');
	var tab_switcher=new TabSwitcher();
	tab_switcher.init();

//	var scanit_form=new ScanitForm('http://localhost/_scripts/scanit_form/scanit_form.php', tab_switcher);
	scanit_form.init();

	rewrite_input_to_input($('input#name'),$('input#invoice_name'));
	rewrite_input_to_input($('input#surname'),$('input#invoice_surname'));
});

function rewrite_input_to_input(from, to){
	to.addClass('virgin').val(from.val());
	from.keyup(function(){
		if(to.hasClass('virgin')){
			to.val(from.val());
		}
	});
	to.keyup(function(){
		to.removeClass('virgin');
	});
}