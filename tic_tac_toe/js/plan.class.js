function Plan(plan_id,plan_cols,plan_rows){
	var plan=$('#'+plan_id);
	var players=new Array();
	var last_draw=null;
	var active_player_number=1;
	var active_player=null;
	var active_cell=null;
	var active_row=null;
	var active_col=null;
	var active_itemid=null;
	var round=1;
	var cols=plan_cols;
	var rows=plan_rows;
	var game=null;
	var round_phase=1;
	var computer=null;

	this.Reset=function(){
		plan.children('.row').children('.cell').html('').removeClass('last-draw').removeClass('win');
		last_draw=null;
		active_player_number=1;
		active_cell=null;
		active_row=null;
		active_col=null;
		active_itemid=null;
		round=1;
		round_phase=1;
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
	 * zkusi provest tah na aktualnim poli
	 * @returns {Plan}
	 */
	this.Play=function(){
		if(!active_cell.html()){
			if(last_draw && last_draw.length){
				last_draw.removeClass('last-draw');
			}
			active_cell.html(active_player.GetSymbol()).addClass('last-draw').addClass('finished');
			last_draw=active_cell;
			this.SavePlayerHistory().CheckWinner().SwitchRound().SwitchPlayer();
			active_player.LetPlay(game,computer);
		}
		return this;
	};

	this.CheckWinner=function(){
		game.GetGroups(active_player_number);
		var wins=game.GetWins();
		if(wins.length){
			for(var key in wins){
				for(var group_key in wins[key]){
					wins[key][group_key]['cell'].addClass('win');
				}
			}
			this.StopGame();
		}
		return this;
	};

	this.StopGame=function(){
		alert('Player ' + active_player.GetName() + ' is winner!');
		active_player.AddWin();
		this.Reset();
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

	this.SetComputer=function(c){
		computer=c;
		return this;
	};

	this.SetGame=function(g){
		game=g;
		return this;
	};

	this.SavePlayerHistory=function(){
		active_player.SetDraw(round, active_cell, active_row, active_col, active_itemid).DebugInfo();
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
		active_player_number=(active_player_number===1 ? 2 : 1);
		active_player=players[active_player_number];
		return this;
	};

	this.SetPlayer=function(player_num,player_object){
		game.SetPlayer(player_num,player_object);
		player_object.DebugInfo();
		if(player_num===1){
			players[1]=player_object;
			active_player=player_object;
		}
		else if(player_num===2){
			players[2]=player_object;
		}
		return this;
	};
}
