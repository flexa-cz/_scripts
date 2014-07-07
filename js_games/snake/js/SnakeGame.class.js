/*
 */
function SnakeGame(_id, _cols, _rows){
	var self=this;
	var id=_id;
	var cols=parseInt(_cols);
	var rows=parseInt(_rows);
	var plan;
	var snake1;
	var count_of_meal=10;
	var count_of_added_meal=5;
	var speed=500;
	var round=1;
	var interval_id;

	/* ************************************************************************ */
	/* public methods */
	/* ************************************************************************ */

	this.init=function(){
		plan = new Plan(id,cols,rows).Create();
		self.newGame();
		$('input[name=reset]').click(function(){self.newGame();});
		return self;
	};

	this.newGame=function(speed_minus, win){
		if(interval_id){
			window.clearInterval(interval_id);
		}
		round=round+(win ? 1 : 0);
		count_of_meal=count_of_meal+(win ? count_of_added_meal : 0);
		alert(round+'. ROUND');
		speed=speed-(speed_minus ? speed_minus : 0);
		$('#'+id+' div.cell.meal').removeClass('meal');
		printMeal();
		startSnake1(plan, id);
		interval_id=window.setInterval(function(){
			snake1.move();
		},speed);
	};

	/* ************************************************************************ */
	/* private methods */
	/* ************************************************************************ */

	var startSnake1=function(){
		var center_cell=plan.getCenterCell();
		if(center_cell){
			snake1=new Snake(plan, id, self).init(center_cell, 'right');
		}
	};

	var printMeal=function(){
		var max_item_id=cols*rows;
		for(var x=0; x<count_of_meal; x++){
			var item_id=Math.floor((Math.random() * max_item_id) + 1);
			$('#'+id+' div.cell.item-id-'+item_id).addClass('meal');
		}
	};

	return self;
}