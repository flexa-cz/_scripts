function RangeSlider(){
	var log_enable = true;
	var log_trace = false;


	this.Make=function(s){
		var data=new Array();
		data['type']=s.attr('type');
		data['min']=s.attr('min');
		data['max']=s.attr('max');
		data['name']=s.attr('name');
		data['value']=s.attr('value');
		data['step']=s.attr('step');
		if(data['type']=='range'){
			s.after('<div class="input_type_range ' + data['name'] + '"><div class="slider"></div></div>').hide();
			var range_slider=$('div.input_type_range.' + data['name'] + ' .slider');
			jQuery.data(range_slider,'data',data);
			range_slider.mousemove(function(event){
			  var msg = "Handler for .mousemove() called at ";
				msg += event.pageX + ", " + event.pageY;
				Log(msg);
			});
		}
		return this;
	}

	this.Move=function(){

	}

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