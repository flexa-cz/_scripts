function ScanitForm(_php_script_address){
	var self=this;
	var form;
	var php_script_address=_php_script_address;
	var prices;
	var sum_all;

	this.init=function(){
		form=$('form#scanit_form');
		self.initPrices();
		// bind actions
		form.find('input.pieces').keyup(function(){
			self.calculateAllPrices();
		});
		form.find('select.dpi').change(function(){
			self.calculateAllPrices();
		});
		form.find('input[type=radio]').click(function(){
			self.calculateAllPrices();
		});
		form.find('input[type=submit]').click(function(event){
			event.stopPropagation();
			event.preventDefault();
			self.submit();
		});
		self.calculateAllPrices();
		return self;
	};

	this.initPrices=function(){
    $.ajax({
			type: 'post',
			url: php_script_address,
    	data: 'get_actual_prices=1',
			dataType: 'json',
			async: false
    }).done(function(data){
			prices=data;
			var inputs_names=new Array(
				'glass_frame',
				'48_bit_tiff',
				'dvd',
				'archive_dvd',
				'transport_2_post',
				'transport_2_manual',
				'payment_cash',
				'payment_on_delivery',
				'payment_transfer'
			);
			for(var key in inputs_names){
				$('input[name='+inputs_names[key]+'_price_per_pieces]').val(prices[inputs_names[key]]);
			}
		}).error(function(){
			self.showError().removeTable();
		});
		return self;
	};

	this.showError=function(){
		form.prepend('<p class="error">Omlouváme se, ale momentálně je objednávkový systém mimo provoz.<br /> Zkuste to prosím později, nebo nám zašlete e-mail.</p>');
		return self;
	};

	this.removeTable=function(){
		form.children('table').remove();
		return self;
	};

	this.calculateAllPrices=function(){
		sum_all=0;
		form.find('input.pieces').each(function(){
			self.calculatePrice($(this));
		});
		form.find('input.radio-binding').each(function(){
			self.calculatePriceWithRadioBinding($(this));
		});
		$('input#sum_all').val(sum_all);
	};

	this.calculatePrice=function(input){
		var type=input.attr('id');
		var pieces=(parseInt(input.val()) ? parseInt(input.val()) : 0);
		var dpi=form.find('select[name='+type+'_dpi]').val();
		var input_price_per_pieces=form.find('input[name='+type+'_price_per_pieces]');
		var input_price_sum=form.find('input[name='+type+'_price_sum]');
		var price_per_pieces=self.getPricePerPieces(type, dpi, pieces);
		input_price_per_pieces.attr('value',price_per_pieces);
		var sum=pieces*price_per_pieces;
		sum_all+=sum;
		input_price_sum.attr('value',sum);
		return self;
	};

	this.calculatePriceWithRadioBinding=function(input){
		var id=input.attr('itemid');
		if($('input#'+id+'[type=radio]').is(':checked')){
			sum_all+=parseInt(input.val());
		}
	};

	this.getPricePerPieces=function(type, dpi, pieces){
		var price_per_pieces=0;
		// isnt it fix price?
		var input_price_per_pieces=$('input[name='+type+'_price_per_pieces]');
		if(input_price_per_pieces.hasClass('fix-price')){
			price_per_pieces=input_price_per_pieces.val();
		}
		// floating price
		else{
			for(var key in prices[type][dpi]){
				var int_key=parseInt(key);
				if(pieces<int_key || key==='infinity'){
					price_per_pieces=prices[type][dpi][key];
					break;
				}
			}
		}
		return price_per_pieces;
	};

	this.submit=function(){
		self.calculateAllPrices();
		var post_data=form.serialize();
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
		}).error(function(){
			self.showError();
		});
		return self;
	};
}