/**
 * pocita BMI
 * odesilaci tlacitko musi mit tridu bmi-calculate
 * tag pro zobrazeni vysledku musi mit tridu bmi-score
 */
function BmiCalculator(){
	var log_enable = false;
	var log_trace = false;
	var obj=null;
	var score=null;
	var bmi=null;

	var bmi_table=new Array();
	bmi_table['extra_mallnutrition']=new Array(0,16.5);
	bmi_table['mallnutrition']=new Array(16.5,18.5);
	bmi_table['ideal']=new Array(18.8,25);
	bmi_table['overweight']=new Array(25,30);
	bmi_table['soft_obsesity']=new Array(30,35);
	bmi_table['medium_obsesity']=new Array(35,40);
	bmi_table['heavy_obsesity']=new Array(40,999999);

	/**
	 * poveseni akci kalkulacky
   * @param root_id id korenoveho prvku
	 */
	this.Init=function(root_id){
		obj=$('#' + root_id);
		score=obj.find('.bmi-score');

		obj.find('input[type=range]').each(function(){
			range_slider.Make($(this));
		});

		obj.find('.bmi-calculate').click(function(event){
			event.stopPropagation();
			event.preventDefault();
			bmi_calculator.Calculate();
		})
		return this;
	}

	this.Calculate=function(){
		var height=(range_slider.GetValue('height')/100);
		var weight=range_slider.GetValue('weight');
		bmi=Math.round((weight/(height*height))*100)/100;
		score.html(bmi + ' (' + bmi_calculator.GetResolve() + ')');
		return this;
	}

	this.GetResolve=function(){
		var resolve='';
		if(bmi){
			for(var res in bmi_table){
				var from = bmi_table[res][0];
				var to = bmi_table[res][1];
				if(from < bmi && bmi <= to){
					resolve=res;
					break;
				}
			}
		}
		return resolve;
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

var bmi_calculator=new BmiCalculator();