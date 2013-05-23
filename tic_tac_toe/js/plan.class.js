function Plan(){
	var element_id=null;
	var plan=null;
	var cols=20;
	var rows=20;
	var last_draw=null;
	var players=new Array();
	var active_player=1;
	var active_cell=null;
	var active_row=null;
	var active_col=null;
	var active_hover=null;
	var active_itemid=null;
	var round=1;
	var round_phase=1;
	var game=new Game(cols,rows);

	/**
	 * spustit po nacteni okna, aby se hra vytvorila
	 * @param {type} id
	 * @returns {undefined}
	 */
	this.Init=function(id){
		plan=$('#'+id);
		element_id=id;
		this.Create().BindActions(this);
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
				act_row.append('<div class="cell" rel="' + col + '" itemid=" ' + item_id + '"></div>');
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
			if(last_draw && last_draw.length){
				last_draw.removeClass('last-draw');
			}
			active_cell.html(players[active_player].GetSymbol()).addClass('last-draw').addClass('finished');
			last_draw=active_cell;
			this.SavePlayerHistory().CheckWinner().SwitchRound().SwitchPlayer();
		}
		return this;
	};

	this.CheckWinner=function(){
		game.GetLines(active_player);
		var wins=game.GetWins();
		if(wins.length){
			for(var key in wins){
				for(var group_key in wins[key]){
					wins[key][group_key]['cell'].css('background-color','orange');
				}
			}
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
		var itemid=cell.attr('itemid');
		active_cell=cell;
		active_row=row;
		active_col=col;
		active_itemid=itemid;
		return this;
	};

	this.SavePlayerHistory=function(){
		players[active_player].SetDraw(round, active_cell, active_row, active_col, active_itemid).DebugInfo();
		return this;
	};

	this.SwitchRound=function(){
		if(round_phase===1){
			round_phase=2;
		}
		else{
			round_phase=1;
			round++;
		}
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

	this.SetPlayer=function(player_num,player_object){
		game.SetPlayer(player_num,player_object);
		if(player_num===1){
			players[1]=player_object;
		}
		else if(player_num===2){
			players[2]=player_object;
		}
		return this;
	};
}