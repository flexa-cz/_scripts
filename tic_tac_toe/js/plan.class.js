function Plan(plan_id,plan_cols,plan_rows){
	var plan=$('#'+plan_id);
	var active_cell=null;
	var active_row=null;
	var active_col=null;
	var active_itemid=null;
	var cols=plan_cols;
	var rows=plan_rows;

	this.Reset=function(){
		plan.children('.row').children('.cell').html('').removeClass('last-draw').removeClass('win');
		active_cell=null;
		active_row=null;
		active_col=null;
		active_itemid=null;
//		console.log('============================================================');
//		console.log('RESET ======================================================');
//		console.log('============================================================');
		return this;
	};

	/**
	 * vytvori bunku
	 * @returns {TicTacToePlan}
	 */
	this.Create=function(){
		var act_row=null;
		var item_id=0;
		for (var row=1;row<=rows;row++){
			plan.append('<div class="row" rel="' + row + '"></div>');
			act_row=plan.children('div[rel=' + row +']');
			for(var col=1;col<=cols;col++){
				item_id++;
				act_row.append('<div class="cell" rel="' + col + '" itemid="' + item_id + '"></div>');
			}
		}
		return this;
	};

	/**
	 * nastaveni aktualni bunky
	 * @param {type} cell
	 * @returns {Plan}
	 */
	this.SetCell=function(cell){
		var col=cell.attr('rel');
		var row=cell.parent('div.row').attr('rel');
		var itemid=cell.attr('itemid');
		active_cell=cell;
		active_row=row;
		active_col=col;
		active_itemid=itemid;
		return this;
	};

	this.GetActiveCell=function(){
		return active_cell;
	};

	this.GetActiveCol=function(){
		return active_col;
	};

	this.GetActiveRow=function(){
		return active_row;
	};

	this.GetActiveItemId=function(){
		return active_itemid;
	};
}
