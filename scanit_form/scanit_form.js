$(document).ready(function(){
//	var scanit_form=new ScanitForm('http://flexa.cz/scanit_form/scanit_form.php');
	var tab_switcher=new TabSwitcher();
	tab_switcher.init();

	var scanit_form=new ScanitForm('http://localhost/_scripts/scanit_form/scanit_form.php', tab_switcher);
	scanit_form.init();
});