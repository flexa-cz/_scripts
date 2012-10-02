/**
 * pocita BMI
 * @param log_enable povoli/zakaze logovani
 * @param log_trace kdyz uz se loguje, jestli vypise i debug backtrace
 * @param obj jquery ukazatel na hlavni objekt kalkulacky
 */
function BmiCalculator(){
	var log_enable = true;
	var log_trace = false;
	var obj=null;

	this.Init=function(){
		obj=$('#bmi-calculator');

		obj.find('input[type=range]').each(function(){
			range_slider.Make($(this));
		});
		return this;
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