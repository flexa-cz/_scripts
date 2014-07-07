function Plan(_plan_id,_cols,_rows){
	var plan_id=_plan_id;
	var plan=$('#'+plan_id);
	var active_cell=null;
	var active_row=null;
	var active_col=null;
	var active_itemid=null;
	var cols=_cols;
	var rows=_rows;

	this.Reset=function(){
		plan.children('.row').children('.cell').html('').removeClass('last-draw').removeClass('win');
		active_cell=null;
		active_row=null;
		active_col=null;
		active_itemid=null;
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
				act_row.append('<div class="cell item-id-'+item_id+'" rel="' + col + '" itemid="' + item_id + '"></div>');
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

	this.printNeighbour=function(cell,direction){
		var ret=null;
		if(cell){
			var column=parseInt(cell.attr('rel'));
			var row=parseInt(cell.parent('div').attr('rel'));
			var item_id=parseInt(cell.attr('itemid'));
			if(direction==='right'){
				if(column<cols){
					ret=$('#'+plan_id+' div.cell.item-id-'+(++item_id));
				}
			}
			else if(direction==='left'){
				if(column>1){
					ret=$('#'+plan_id+' div.cell.item-id-'+(--item_id));
				}
			}
			else if(direction==='top'){
				if(row>1){
					ret=$('#'+plan_id+' div.cell.item-id-'+(item_id-cols));
				}
			}
			else if(direction==='down'){
				if(row<rows){
					ret=$('#'+plan_id+' div.cell.item-id-'+(item_id+cols));
				}
			}
		}
		return ret;
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

	this.getCenterCell=function(){
		var center_row_rel=Math.floor((rows)/2);
		var center_col_rel=Math.floor((cols)/2);
		var center_cell_rel=((center_row_rel-1)*cols)+center_col_rel;
		return $('#'+plan_id+' div.cell.item-id-'+center_cell_rel);
	};

	return this;
}
