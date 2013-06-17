function TicTacToe(){
	var player_1;
	var player_2;
	var plan;
	var game;
	var computer;
	var self;
	var element_id;
	var active_hover=null;

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
		plan.SetComputer(computer).SetGame(game).Create().SetPlayer(1, player_1).SetPlayer(2, player_2);

		self.BindActions();
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
					plan.SetCell($(this)).Play();
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
}