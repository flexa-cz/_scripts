/**
 * funkce beget vytvoří poděděný objekt
 * var child = __extends(parent); // child bude potomkem parent
 * @param parent {object} rodicovska trida
 */
var __extends = function(parent) {
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
function mobiFlex(){
	var protected_class_name='no-mf';
	var log_enable=null;
	var log_trace=null;

	/**
	 *
	 * @returns {mobiFlex}
	 */
	__construct=function(that){
		that.log_enable=true;
		that.log_trace=false;
		return this;
	}(this);


	/* -------------------------------------------------------------------------- */
	/* inicializacni metody																												*/
	/* -------------------------------------------------------------------------- */
	/**
	 * je potreba spustit pred pouzitim
	 * @param form {boolean} jestli se ma aplikovat sada metod pro praci s formulari
	 * @return {mobiFlex}
	 */
	mobiFlex.prototype.init = function(form){
		if(form){
			mobiFlex.prototype
				.initSelect()
				.initCheckbox();
		}
		// ukonceni vsech aktivnich akci, pri kliku "jinam"
		/**
		 * @todoje potreba vymyslet, tak detekovat klik "jinam"
		 */
		$(window).click(function(){
//			$('.mf-universal.active').removeClass('active');
		})
		return this;
	};

	/**
	 *
	 * @returns {mobiFlex}
	 */
	mobiFlex.prototype.initSelect = function(){
		$('select').each(function(){
			if(!$(this).hasClass(protected_class_name)){
				var list='';
				$(this).children('option').each(function(){
					list+='<li rel="' + $(this).attr('value') + '" class="' + ($(this).attr('class') ? $(this).attr('class') : '') + '">' + $(this).html() + '</li>';
				});
				$(this).wrap('<div class="mf-universal mf-select" />').after('<ul>' + list + '</ul>').hide();
				// nastaveni sirky
				$('.mf-select').each(function(){
					$(this).css('width',$(this).children('ul').width());
				});
				// poveseni udalosti
				$('div.mf-select ul li').mouseover(function(){
					$(this).addClass('hover');
				});
				$('div.mf-select,div.mf-select li').mouseout(function(){
					$(this).removeClass('actives').removeClass('hover');
				});
				// nastaveni hodnoty selectboxu
				$('div.mf-select li').click(function(event){
					if($(this).parent('ul').parent('.mf-select').hasClass('active')){
						var rel=$(this).attr('rel');
						var line_height=parseInt($(this).css('line-height'));
						var list=-1;
						var sel_list=0;
						$(this).parent('ul').parent('div').children('select').children('option').each(function(){
							list++;
							var val=$(this).attr('value');
							if(val === rel){
								$(this).attr('selected','selected');
								sel_list=list;
								$(this).parent('select').parent('div').children('ul').children('li').each(function(){
									if($(this).attr('rel')===rel){
										$(this).addClass('selected');
									}
									else{
										$(this).removeClass('selected');
									}
								});
							}
							else{
								$(this).attr('selected',false);
							}
						});

						$(this).removeClass('hover').parent('ul').css('top','-' + (sel_list * line_height) + 'px').parent('.mf-select').removeClass('active');
					}
					else{
						$(this).parent('ul').css('top','-1px').parent('.mf-select').addClass('active');
					}
				});
			}
		});
		return this;
	};

	/**
	 *
	 * @returns {mobiFlex}
	 */
	mobiFlex.prototype.initCheckbox = function(){
		return this;
	};
	/* -------------------------------------------------------------------------- */

	/**
	 * public
	 * vraci class name prvku, ktere se nemaji tridou zpracovavat
	 * @returns string {mobiFlex}
	 */
	mobiFlex.prototype.getProtectedClassName = function(){
		return protected_class_name;
	};

	/**
	 * public
	 * zapina/vypina logovani do konzole
	 * @param log_enable boolean
	 * @returns {mobiFlex}
	 */
	mobiFlex.prototype.setLogEnable = function(log_enable){
		if(!log_enable){this.log('Log of mobiFlex is turned OFF.');}
		this.log_enable=(log_enable ? true : false);
		if(log_enable){this.log('Log of mobiFlex is turned ON.');}
		return this;
	};

	/**
	 * public
	 * zapina/vypina detailniho logovani
	 * @param log_trace boolean
	 * @return {mobiFlex}
	 */
	mobiFlex.prototype.setLogTrace = function(log_trace){
		if(!log_trace){this.log('Log with trace of mobiFlex is turned OFF.');}
		this.log_trace=(log_trace ? true : false);
		if(log_trace){this.log('Log with trace of mobiFlex is turned ON.');}
		return this;
	};

	/**
	 * private
	 * vypisuje retezec do konzole a prida debugovaci data
	 * @param data co se vypise
	 * @return {mobiFlex}
	 */
	this.log = function(data){
		if(this.log_enable){
			console.log(data);
			if(this.log_trace){
				console.trace();
			}
		}
		return this;
	};

}