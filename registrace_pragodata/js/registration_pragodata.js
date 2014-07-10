$(window).ready(function(){
	// skryva/zobrazuje radky podle klicoveho selecboxu...
	var sh_rows=new show_hide_rows();
	sh_rows.init();

	// skryva/zobrazuje meny
	var sh_currency=new show_hide_currency();
	sh_currency.init();

	// skryva/zobrazuje studentske ceny
	var sh_student_price=new show_hide_student_price();

	// prida/odebira radky s ucastniky
	var add_participants=new AddRemoveCodeSegment();
	add_participants.init(null,null,function(row){
		sh_currency.init(true);
		sh_student_price.init(row);
		row.find('input[type=checkbox]').each(function(){
			$(this).click(function(){
				count_sum();
			});
		});
		$('input#pocet').attr('value',($('tr.participant').size()));
	}).addFirst();

	// souhlas s pravidly
	// vychozi nastaveni
	disable_enable_submit();
	// pri zmene souhlasu
	$('input[type=checkbox]#souhlas').change(function(){
		disable_enable_submit();
	});

	// spusteni kontoly formulare a jeho pripadne neodeslani
	$('form#register_form').submit(function(event){
		if(!control_form()){
			event.preventDefault();
			event.stopPropagation();
		}
	});
});


/**
 * skryva/zobrazuje radky
 * @returns {show_hide_rows}
 */
function show_hide_rows(){
	var self=this;

	/**
	 * @param {boolean} after_add_row
	 * @returns {show_hide_rows}
	 */
	this.init=function(after_add_row){
		$('select.show_hide_rows').each(function(){
			var sel=$(this);
			// nastavi spravne po nacteni strany
			self.work(sel);
			// nastavei spravne po zmene stavu selectboxu
			if(!after_add_row){
				sel.change(function(){
					self.work(sel,'slow');
				});
			}
		});
		return self;
	};

	/**
	 * @param {object} sel objekt selectoxu
	 * @param {string} effect_speed
	 * @returns {show_hide_rows}
	 */
	this.work=function(sel,effect_speed){
		var option=sel.children('option:selected');
		var rel=option.attr('rel');
		if(rel==='show'){
			$('li.'+sel.attr('rel')).show(effect_speed);
		}
		else if(rel==='hide'){
			$('li.'+sel.attr('rel')).hide(effect_speed);
		}
		return self;
	};
}

/**
 * skryva/zobrazuje menu
 * @returns {show_hide_currency}
 */
function show_hide_currency(){
	var self=this;

	/**
	 * @returns {show_hide_rows}
	 */
	this.init=function(){
		$('select.show_hide_currency').each(function(){
			var sel=$(this);
			// nastavi spravne po nacteni strany
			self.work(sel);
			// nastavei spravne po zmene stavu selectboxu
			sel.change(function(){
				self.work(sel);
			});
		});
		return self;
	};

	/**
	 * @param {object} sel objekt selectoxu
	 * @returns {show_hide_rows}
	 */
	this.work=function(sel){
		var option=sel.children('option:selected');
		var rel=option.attr('rel');
		if(rel==='czk'){
			$('span.czk').addClass('show_currency');
			$('span.eur').removeClass('show_currency');
		}
		else if(rel==='eur'){
			$('span.czk').removeClass('show_currency');
			$('span.eur').addClass('show_currency');
		}
		show_hide_price();
		return self;
	};
}

/**
 * skryva/zobrazuje cenu na zaklade studentskeho stavu
 * @returns {show_hide_student_price}
 */
function show_hide_student_price(){
	var self=this;

	/**
	 * @param {object} row
	 * @returns {show_hide_student_price}
	 */
	this.init=function(row){
		var checkbox=row.find('input[type=checkbox].show_hide_student_price');
		self.work(row,checkbox);
		checkbox.change(function(){
			self.work(row,checkbox);
		});
		return self;
	};

	/**
	 * @param {object} row
	 * @param {object} checkbox
	 * @returns {show_hide_student_price}
	 */
	this.work=function(row,checkbox){
		var student=checkbox.is(':checked');
		if(student){
			row.find('span.student_price').addClass('show_student_price');
			row.find('span.normal_price').removeClass('show_student_price');
		}
		else{
			row.find('span.student_price').removeClass('show_student_price');
			row.find('span.normal_price').addClass('show_student_price');
		}
		show_hide_price();
		return self;
	};
}

/**
 * finalni rozhodnuti, jaka cena se ma zobrazit
 * @returns {undefined}
 */
function show_hide_price(){
	$('span.price').each(function(){
		if($(this).hasClass('show_currency') && $(this).hasClass('show_student_price')){
			$(this).show();
		}
		else{
			$(this).hide();
		}
	});
	count_sum();
}

/**
 * spocita a dosadi sumu
 * neni to v show_hide_price protoze se to musi odpalovat i pri vyberu kurzu
 * @returns {undefined}
 */
function count_sum(){
	var sum=0;
	$('span.price.show_currency.show_student_price').each(function(){
		var price=$(this);
		var label=price.parent('label');
		var checkbox=label.children('input[type=checkbox]');
		if(checkbox.is(':checked')){
			sum+=parseInt(price.attr('rel'));
		}
	});
	// hodnota
	$('span#final_sum').html(sum);
	$('input#odeslani_cena').attr('value',sum);
	// mena
	var curr=$('select#mena').children('option:selected').html();
	$('span#final_currency').html(curr);
	disable_enable_submit();
}

/**
 * @returns {undefined}
 */
function disable_enable_submit(){
	var submit=$('input[type=submit]#odeslat');
	if($('input[type=checkbox]#souhlas').is(':checked') && parseInt($('span#final_sum').html())>0){
		submit.removeAttr('disabled');
	}
	else{
		submit.attr('disabled','disabled');
	}
}


function control_form(){
	var report='';
	var pscRegex1 = /^\d{3} ?\d{2}$/;
	var pscRegex2 = /^\d{5}$/;
	var icRegex = /^\d{8}$/;
	var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,5}$/;

	if ($('#jmeno').val() === ""){
		report+="\n- organizaci (nebo Vaše jméno)";
	}
	if ($('#ulice').val() === ""){
		report+="\n- adresu";
	}
	if ($('#mesto').val() === ""){
		report+="\n- město";
	}
	if (  !pscRegex1.test($('#psc').val()) && !pscRegex2.test($('#psc').val()) &&($('#cr').val()==="1") ){
		report+="\n- PSČ";
	}
	if (($('#osoba').val() === "2") && ($('#cr').val()==="1") && (!icRegex.test($('#ic').val()))){
		report+="\n- IČ";
	}
	if ($('#kontakt').val() === ""){
		report+="\n- kontaktní osobu";
	}
	if (!emailRegex.test($('#k-mail').val())){
		report+="\n- kontaktní email";
	}
	if ($('#telefon').val() === ""){
		report+="\n- kontaktní telefon";
	}
	if (parseFloat($('#pocet').val()) === 0){
		report+="\n- alespoň jednoho účastníka";
	}
	if ($('#odeslani_cena').val() === 0){
		report+="\n- cena se nesmí rovnat 0";
	}
	if ($('#souhlas').val() === ""){
		report+="\n- Váš souhlas s podmínkami registrace";
	}
	// kontroly ucastniku
	for (i=1;i<=$('#pocet').val();i++){
		if ($("input[name='jm["+i+"]']").val() === ""){
			report+="\n- jméno a přijmení "+i+". účastníka";
		}
		if ($("input[name='em["+i+"]']").val() === ""){
			report+="\n- e-mail "+i+". účastníka";
		}
//		if($("input:checked[name='ko_"+i+"']").length != 1 && $("input:checked[name='u1_"+i+"']").length != 1 && $("input:checked[name='u2_"+i+"']").length != 1 && $("input:checked[name='a_"+i+"']").length != 1){
//			report+="Není vybrán typ účasti "+tmp+". účastníka";
//		}
	}

	if(report.length>0){
		alert('Musíte zadat:\n' + report);
		return false;
	}

	return true;
}