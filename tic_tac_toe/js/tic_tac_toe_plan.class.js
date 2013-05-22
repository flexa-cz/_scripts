function TicTacToePlan(){
	var element_id=null;
	var plan=null;
	var cols=20;
	var rows=20;
	var active_player=1;
	var player_1=new Array();
	player_1['symbol']='&times;';
	var player_2=new Array();
	player_2['symbol']='&cir;';
	var players=new Array();
	players[1]=player_1;
	players[2]=player_2;
	var active_cell=null;
	var active_row=null;
	var active_col=null;
	var active_hover=null;
	var last_play=null;

	/**
	 * spustit po nacteni okna, aby se hra vytvorila
	 * @param {type} id
	 * @returns {undefined}
	 */
	this.Init=function(id){
		plan=$('#'+id);
		element_id=id;
		this.Create().BindActions(this);
	};

	/**
	 * vytvori bunku
	 * @returns {TicTacToePlan}
	 */
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

	/**
	 * povesi vsechny potrebne udalosti
	 * @param {type} self
	 * @returns {TicTacToePlan}
	 */
	this.BindActions=function(self){
		$('#'+element_id+' .cell').each(function(){
			$(this)
				.click(function(){
					self.Click($(this));
				})
				.mouseenter(function(){
					active_hover=$(this);
					$(this).addClass('hover');
				})
				.mouseleave(function(){
					active_hover.removeClass('hover');
				});
		});
		return this;
	};

	/**
	 * provede klik na bunku
	 * @param {type} cell
	 * @returns {TicTacToePlan}
	 */
	this.Click=function(cell){
		this.SetCell(cell).Play();
		return this;
	};

	/**
	 * zkusi provest tah na aktualnim poli
	 * @returns {TicTacToePlan}
	 */
	this.Play=function(){
		if(!active_cell.html()){
			if(last_play && last_play.length){
				last_play.removeClass('last-play');
			}
			active_cell.html(players[active_player]['symbol']).addClass('last-play');
			last_play=active_cell;
			this.SwitchPlayer();
		}
		return this;
	};

	/**
	 * nastaveni aktualni bunky
	 * @param {type} cell
	 * @returns {TicTacToePlan}
	 */
	this.SetCell=function(cell){
		var col=cell.attr('rel');
		var row=cell.parent('div.row').attr('rel');
		active_cell=cell;
		active_row=row;
		active_col=col;
		return this;
	};

	/**
	 * prepne na druheho hrace
	 * @returns {TicTacToePlan}
	 */
	this.SwitchPlayer=function(){
		active_player=(active_player===1 ? 2 : 1);
		return this;
	};
}