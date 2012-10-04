/**
 * funkce beget vytvoří poděděný objekt
 * var child = beget(parent); // child bude potomkem parent
 */
var beget = function(parent) {
	// F je pomocný dočasný konstruktor
	var F = function() {};
	F.prototype = parent;
	var child = new F;
	return child;
};

/* ************************************************************************** */
/* mobiFlex																																		*/
/* ************************************************************************** */

/**
 * trida pro snazsi praci s mobilnimi weby (obdoba jQuery mobile, ktera mi nevyhovovala)
 * stavi ale na jQuery, takze musi byt do projektu taky nacteno
 * aplikaci zmen lze potlacit nastavenim tridy "no-mf" konkretnimu prvku
 */
var mobiFlex = function(){
	this.protected_class_name='no-mf';
	mobiFlex.prototype
		.setLogEnable(true)
		.setLogTrace(false);
	return this;
}


/* -------------------------------------------------------------------------- */
/* inicializacni metody																												*/
/* -------------------------------------------------------------------------- */
/**
 * je potreba spustit pred pouzitim
 */
mobiFlex.prototype.init = function(form){
	if(form){
		mobiFlex.prototype
			.initSelect()
			.initCheckbox();
	}
	return this;
}

mobiFlex.prototype.initSelect = function(){
	$('select').each(function(){
		alert(mobiFlex.prototype.getProtectedClassName());
		if(!$(this).hasClass(this.protected_class_name)){
			$(this).hide();
		}
	})
	return this;
}

mobiFlex.prototype.initCheckbox = function(){
	return this;
}
/* -------------------------------------------------------------------------- */

mobiFlex.prototype.getProtectedClassName = function(){
	return this.protected_class_name;
}

/**
 * zapina/vypina logovani do konzole
 * @param log_enable boolean
 * @return this
 */
mobiFlex.prototype.setLogEnable = function(log_enable){
	if(!log_enable){mobiFlex.prototype.log('Log of mobiFlex is turned OFF.');}
	this.log_enable=(log_enable ? true : false);
	if(log_enable){mobiFlex.prototype.log('Log of mobiFlex is turned ON.');}
	return this;
}

/**
 * zapina/vypina detailniho logovani
 * @param log_trace boolean
 * @return this
 */
mobiFlex.prototype.setLogTrace = function(log_trace){
	if(!log_trace){mobiFlex.prototype.log('Log with trace of mobiFlex is turned OFF.');}
	this.log_trace=(log_trace ? true : false);
	if(log_trace){mobiFlex.prototype.log('Log with trace of mobiFlex is turned ON.');}
	return this;
}

/**
 * vypisuje retezec do konzole a prida debugovaci data
 * @param data co se vypise
 * @return this
 */
mobiFlex.prototype.log = function(data){
	if(this.log_enable){
		console.log(data);
		if(this.log_trace){
			console.trace();
		}
	}
	return this;
}