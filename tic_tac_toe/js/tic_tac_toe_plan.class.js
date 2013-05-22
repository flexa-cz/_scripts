function TicTacToePlan(){
	var id=null;
	var plan=null;
	var cols=20;
	var rows=20;

	this.Init=function(id){
		plan=$('#'+id);
		this.Create().BindActions();
	};

	this.Create=function(){
		var act_row=null;
		for (var row=1;row<=rows;row++){
			plan.append('<div class="row" rel="' + row + '"></div>');
			act_row=plan.children('div[rel=' + row +']');
			for(var col=1;col<=cols;col++){
				act_row.append('<div class="cell" rel="' + col + '"></div>');
			}
		}
		return this;
	};

	this.BindActions=function(){
		$('#'+id+' .cell').each(function(){
			$(this).click(function(){
				this.Click($(this));
			});
		});
	};

	this.Click=function(cell){
		console.log('click...');
//		var col=cell.attr('rel');
//		var row=cell.parent('div.row').attr('rel');
//		console.log('row: ' + row + '; col: ' + col);
	};
}