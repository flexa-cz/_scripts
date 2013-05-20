$(document).ready(function(){
	register_form.Init();
})

function RegisterForm(){
	this.form = null;

	this.Init = function(){
		form=$('form#register_form');
		form.children('input[type=submit]').click(function(event){
			event.stopPropagation();
			event.preventDefault();
			register_form.Submit();
		});
		return this;
	}
// 86greasygrass
	this.Submit = function(){
		$.ajax({
			url: "./register_form.php",
			type: 'post',
			dataType: 'json',
			data: form.serialize(),
			success: function(data){
				alert(data['alert']);
				if(!data['error']){
					register_form.EmptyForm();
				}
			}
		});
		return this;
	}

	this.EmptyForm = function(){
		form.children('input[type=text]').attr('value','');
		form.children('input[type=checkbox]').attr('checked',false);
	}
}
var register_form=new RegisterForm();