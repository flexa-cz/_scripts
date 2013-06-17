function TicTacToe(){
	var player_1;
	var player_2;
	var plan;
	var game;
	var computer;
	var self;
	var element_id;
	var active_hover;
	var last_draw;
	var active_player;
	var active_player_number=1;
	var round=1;
	var round_phase=1;

	/**
	 *
	 * @param {TicTacToe} self_obj
	 * @param {string} id
	 * @param {integer} cols
	 * @param {integer} rows
	 * @returns {TicTacToe}
	 */
	this.Init=function(self_obj,id,cols,rows){
		self=self_obj;
		element_id=id;
		player_1=new Player(1,'&cir;','Milan',$('div.debug_info.player_1'));
		player_2=new Player(2,'&times;','Comp.',$('.debug_info.player_2'),true);
		game=new Game(cols,rows);
		computer=new Computer(game.GetLinesSettings(),game.GetWinLineLength(),cols,rows);
		plan = new Plan(element_id,cols,rows);
		plan.Create();

		self.SetActivePlayer().BindActions();
		return this;
	};

	/**
	 * spusti novou hru
	 * @returns {TicTacToe}
	 */
	this.ResetGame=function(){
		player_1.Reset();
		player_2.Reset();
		game.Reset();
		plan.Reset();
		last_draw=null;
		active_player_number=1;
		return this;
	};


	/**
	 * povesi vsechny potrebne udalosti
	 * @returns {TicTacToe}
	 */
	this.BindActions=function(){
		// jednotlive bunky
		$('#'+element_id+' .cell').each(function(){
			$(this)
				.click(function(){
					plan.SetCell($(this));
					self.Play();
				})
				.mouseenter(function(){
					active_hover=$(this);
					$(this).addClass('hover');
				})
				.mouseleave(function(){
					active_hover.removeClass('hover');
				});
		});
		// resetovaci tlacitko
		$('input[name=reset]').click(function(event){
			event.preventDefault();
			event.stopPropagation();
			self.ResetGame();
		});
		return this;
	};


	/**
	 * zkusi provest tah na aktualnim poli
	 * @returns {Plan}
	 */
	this.Play=function(){
		if(!plan.GetActiveCell().html()){
			if(last_draw && last_draw.length){
				last_draw.removeClass('last-draw');
			}
			plan.GetActiveCell().html(active_player.GetSymbol()).addClass('last-draw').addClass('finished');
			last_draw=plan.GetActiveCell();
			this.SavePlayerHistory().CheckWinner().SwitchRound().SwitchPlayer();
			active_player.LetPlay(game,computer);
		}
		return this;
	};

	this.SavePlayerHistory=function(){
		active_player.SetDraw(round, plan.GetActiveCell(), plan.GetActiveRow, plan.GetActiveCol, plan.GetActiveItemId()).DebugInfo();
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
	 * @returns {TicTacToe}
	 */
	this.SwitchPlayer=function(){
		active_player_number=(active_player_number===1 ? 2 : 1);
		this.SetActivePlayer();
		return this;
	};

	this.SetActivePlayer=function(){
		active_player=(active_player_number===1 ? player_1 : player_2);
		return this;
	};

	this.CheckWinner=function(){
		game.GetGroups(active_player);
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
}