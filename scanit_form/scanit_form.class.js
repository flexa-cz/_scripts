function ScanitForm(){
	var self=this;
	var form;
	var php_script_address='http://localhost/_scripts/scanit_form/scanit_form.php';
	var token='';
	var prices={
		'35_mm':{
			'1400':{'100':7,'500':6.5,'infinity':5.5},
			'2700':{'100':9.5,'500':9,'infinity':8.5},
			'4000':{'100':12,'500':11,'infinity':10}
		},
		'roll':{
			'1400':{'4.5':17,'6':19,'7':20,'8':22,'9':25},
			'2700':{'4.5':27,'6':32,'7':34,'8':36,'9':40},
			'4000':{'4.5':35,'6':40,'7':45,'8':50,'9':55}
		}
	};

	this.init=function(){
		form=$('form#scanit_form');
		form.find('input.pieces').keyup(function(){
			self.calculatePrice($(this));
		});
		form.find('select.dpi').change(function(){
			self.calculatePrice($(this).parent('td').parent('tr').find('input.pieces'));
		});
		form.find('input[type=submit]').click(function(event){
			event.stopPropagation();
			event.preventDefault();
			self.submit();
		});
		form.find('input.pieces').keyup();
		return self;
	};

	this.calculatePrice=function(input){
		var type=input.attr('id');
		var pieces=(parseInt(input.val()) ? parseInt(input.val()) : 0);
		var dpi=form.find('select[name='+type+'_dpi]').val();
		var input_price_per_pieces=form.find('input[name='+type+'_price_per_pieces]');
		var input_price_sum=form.find('input[name='+type+'_price_sum]');
		var price_per_pieces=self.getPricePerPieces(type, dpi, pieces);
		input_price_per_pieces.attr('value',price_per_pieces);
		input_price_sum.attr('value',(pieces*price_per_pieces));
		return self;
	};

	this.getPricePerPieces=function(type, dpi, pieces){
		var price_per_pieces=0;
		for(var key in prices[type][dpi]){
			var int_key=parseInt(key);
			if(pieces<int_key || key==='infinity'){
				price_per_pieces=prices[type][dpi][key];
				break;
			}
		}
		return price_per_pieces;
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
//				alert('odeslani se nezdarilo');
			}
			else{
//				alert('bylo odeslano');
			}
		});
		return self;
	};
}