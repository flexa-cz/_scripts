function RangeSlider(){
	var log_enable = true;
	var log_trace = false;
	var all_range_sliders=new Array();


	/**
	 * nahradi input typu range grafickym sliderem
	 * @param rs objekt, ktery se nahrazuje
	 * @return this
	 */
	this.Make=function(rs){
		// data objektu
		// aby se nemusela pokazde znovu ziskavat
		// uschovam pomoci jquery.data
		var data=new Array();
		data['type']=rs.attr('type');
		data['min']=parseInt(rs.attr('min'));
		data['max']=parseInt(rs.attr('max'));
		data['name']=rs.attr('name');
		data['value']=parseInt(rs.attr('value'));
		data['step']=parseInt(rs.attr('step'));
		data['movable']=false;
		data['mouse_correction_x']=0;

		if(data['type']=='range'){
			// nahradim input grafickym sliderem
			rs.after('<div class="range-slider-container" style="position: relative;"><div class="input_type_range ' + data['name'] + '"><div class="slider"></div></div><input type="text" class="range-slider-value name="' + data['name'] + '" /></div>').remove();
			// odkaz na graficky slider
			var this_range_slider=$('div.input_type_range.' + data['name'] + ' .slider');
			data['range']=this_range_slider.parent('div');
			data['input']=data['range'].next();
			// kolik jednotek prezentuje jeden pixel
			data['pixel_percent']=((data['range'].width()) - (this_range_slider.width()))/100;
			// ulozeni dat
			jQuery.data(this_range_slider,'data',data);
			// poveseni udalosti grafickeho slideru
			this_range_slider
				.mousedown(function(event){
					range_slider.SetMovable(this_range_slider,true,event)
				});

			data['range']
			.mouseover(function(){$(this).addClass('hover')})
			.mouseout(function(){$(this).removeClass('hover')})
			.click(function(event){range_slider.Click(event,this_range_slider)});
			// poveseni udalosti pri psani do inputu
			data['input'].blur(function(){
				range_slider.Write(this_range_slider);
			});
			// nastavi pozice slideru a inputu
			range_slider.SetValue(this_range_slider, data['value']).LocateSlider(this_range_slider).UpdateInput(this_range_slider);
		}
		all_range_sliders.push(this_range_slider);
		return this;
	}

	/**
	 * nastavuje, jestli lze se sliderem pohybovat (je na nem mousedown)
	 * @param this_range_slider objekt se kterym se hybe
	 * @param val {boolean} nastavovana hodnota
	 * @param event pokud je vyvolano klikem na element, pak se jeste nastavi korekce pro pohyb mysi
	 * @return this
	 */
	this.SetMovable=function(this_range_slider,val,event){
		var data=(jQuery.data(this_range_slider,'data'));
		// (ne)povoli hybani
		data['movable']=val;
		// rozdil pro korekci pohybu mysi
		data['mouse_correction_x']=0;
		if(val){
			this_range_slider.addClass('active');
		}
		if(event){
			data['mouse_correction_x']=event.pageX-data['slider_position_x'];
		}
		else{
			this_range_slider.removeClass('active');
		}
		jQuery.data(this_range_slider,'data',data);
		return this;
	}

	/**
	 * opravi value a nastavi slidery z input po zapsani hodnoty
	 * @param this_range_slider
	 * @return this
	 */
	this.Write=function(this_range_slider){
		var data=(jQuery.data(this_range_slider,'data'));
		var value=data['input'].attr('value');
		range_slider.SetValue(this_range_slider,value).LocateSlider(this_range_slider).UpdateInput(this_range_slider);
		return this;
	}

	/**
	 * spracovava pohyb kurzoru a meni hodnoty slideru v zavislosti na jeho pohybu
	 * @param event udalost hybani
	 * @param this_range_slider objekt se kterym se hybe
	 * @return this
	 */
	this.Move=function(event,this_range_slider){
		this_range_slider=(!this_range_slider ? range_slider.GetActiveSlider() : this_range_slider);
		if(this_range_slider){
//			Log(this_range_slider);
//			Log(event.pageX);
			var data=(jQuery.data(this_range_slider,'data'));
			var slider_position_x=event.pageX-data['mouse_correction_x'];
			var value=Math.ceil((slider_position_x / data['pixel_percent'])*((data['max']-data['min'])/100)+data['min']);
			range_slider.SetValue(this_range_slider,value).LocateSlider(this_range_slider).UpdateInput(this_range_slider);
		}
		return this;
	}

	this.Click=function(event,this_range_slider){
		event.stopPropagation();
		event.preventDefault();
		var data=(jQuery.data(this_range_slider,'data'));
		Log(event.pageX + ' vs. ' +data['slider_position_x']);
//		data['mouse_correction_x']=range_slider.GetMouseCorrectionX(this_range_slider, event);
//		Log(data['mouse_correction_x']);
//		jQuery.data(this_range_slider,'data',data);
		range_slider.Move(event,this_range_slider);
		return this;
	}

	/**
	 * ulozeni hodnoty do dat
	 * @param this_range_slider objekt se kterym se hybe
	 * @param value {integer}
	 * @return this
	 */
	this.SetValue=function(this_range_slider,value){
		var data=(jQuery.data(this_range_slider,'data'));
		value=range_slider.ValueCorrection(this_range_slider,value);
		data['value']=value;
		jQuery.data(this_range_slider,'data',data);
		return this;
	}

	/**
	 * opravi hodnotu value
	 * spravne nastavi krajni pozice a krok slideru
	 * @param this_range_slider objekt se kterym se hybe
	 * @param value {integer}
	 * @return integer
	 */
	this.ValueCorrection=function(this_range_slider,value){
		var data=(jQuery.data(this_range_slider,'data'));
		value=parseInt(value);
		if(isNaN(value)){
			value=data['min'];
		}
		else{
			value=(value<data['min'] ? data['min'] : (value>data['max'] ? data['max'] : value));
		}
		// korekce kroku
		if(value%data['step']){
			value=Math.round(value/data['step'])*data['step'];
			value=(value<data['min'] ? Math.ceil(data['min']/data['step'])*data['step'] : (value>data['max'] ? Math.floor(data['max']/data['step'])*data['step'] : value));
		}
		return value;
	}

	/**
	 * pokud je nejaky jezdec aktivni tak vraci jeho data
	 */
	this.GetActiveSlider=function(){
		for(var key in all_range_sliders){
			var data=(jQuery.data(all_range_sliders[key],'data'));
			if(data['movable']==true){
				return all_range_sliders[key];
			}
		}
		return null;
	}

	/**
	 * umisti jezdce na spravne misto na ose
	 * @param this_range_slider objekt se kterym se hybe
	 * @return this
	 */
	this.LocateSlider=function(this_range_slider){
		var data=(jQuery.data(this_range_slider,'data'));
		// hodnota muze byt jen v povolenem rozsahu
		var value=range_slider.ValueCorrection(this_range_slider, data['value']);
		// vypocet umisteni
		var slider_position_x=Math.ceil(((value-data['min']) / ((data['max']-data['min'])/100)) * data['pixel_percent']);
		// posune
		this_range_slider.css({left:slider_position_x});
		// ulozi data
		data['slider_position_x']=slider_position_x;
		jQuery.data(this_range_slider,'data',data);
		return this;
	}

	/**
	 * vsem sliderum sebere moznost pohybu
	 */
	this.StopMoving=function(){
		var this_range_slider=range_slider.GetActiveSlider();
		if(this_range_slider){
			range_slider.SetMovable(this_range_slider, false);
		}
		return this;
	}

	/**
	 * nastavi spravnou hodnotu inputu
	 * @param this_range_slider objekt se kterym se hybe
	 * @return this
	 */
	this.UpdateInput=function(this_range_slider){
		var data=(jQuery.data(this_range_slider,'data'));
		data['input'].attr('value',data['value']);
		return this;
	}

	/**
	 * vraci hodnoty vsech slideru
	 * return array
	 */
	this.GetValues=function(){
		var values=new Array();
		for(var key in all_range_sliders){
			var data=(jQuery.data(all_range_sliders[key],'data'));
			values[data['name']]=data['value'];
		}
		return values;
	}

	this.GetValue=function(name){
		var values=range_slider.GetValues();
		var value=(values[name] ? values[name] : null);
		return value;
	}

	/**
	 * vypis do konzole
	 * @param data co se vypise
	 * @return this
	 */
	var Log = function(data){
		if(log_enable){
			console.log(data);
			if(log_trace){
				console.trace();
			}
		}
		return this;
	}
}
var range_slider=new RangeSlider();

// kdyz jde mys nahoru, prestanou se slidery hybat
$(window).mouseup(function(){
	range_slider.StopMoving();
})

// kurzor se nemusi hybat jen nad jezdcem a prece ma byt aktivni
$(window).mousemove(function(event){
	range_slider.Move(event);
})