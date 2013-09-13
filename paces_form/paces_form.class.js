function PacesForm(){
	var self=this;
	var form;
	var php_script_address='http://localhost/_scripts/paces_form/paces_form.php';
	var token='';

	this.init=function(){
		form=$('form#paces_form');
		form.find('input[type=submit]').click(function(event){
			event.stopPropagation();
			event.preventDefault();
			self.submit();
		});
	};

	this.submit=function(){
		var post_data=form.serialize();
		console.log(post_data);
    $.ajax({
			type: 'post',
			url: php_script_address,
    	data: post_data,
			dataType: 'json'
    }).done(function(data){
			console.log(data);
			if(data['error']){
				alert('odeslani se nezdarilo');
			}
			else{
				alert('bylo odeslano');
			}
		});
	};
}