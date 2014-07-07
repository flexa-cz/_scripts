function Snake(_plan,_plan_id, _snake_game){
	var self=this;
	var direction;// [right|left|top|down]
	var snake_body;
	var snake_body_index;
	var start_cell;
	var actual_cell;
	var plan_id=_plan_id;
	var plan=_plan;
	var snake_game=_snake_game;
	var speed_minus=50;

	/* ************************************************************************ */
	/* public methods */
	/* ************************************************************************ */

	this.init=function(_start_cell, _direction){
		start_cell=_start_cell;
		actual_cell=_start_cell;
		setDirection(_direction);
		startPosition();
		keyPressHook();
		return self;
	};

	this.move=function(){
		var neighbour=plan.printNeighbour(actual_cell, direction);
		if(!neighbour){
			endGame('Jsi OUT!');
		}
		else if(neighbour.hasClass('snake-body')){
			endGame('Co te zere?');
		}
		else if(neighbour.hasClass('meal')){
			neighbour.removeClass('meal');
			var meal_count=$('#'+plan_id+' div.cell.meal').length;
			if(!meal_count){
				endGame('HURA! Jsi hadice jak svina!', true);
			}
			else{
				snakeMove(neighbour,true);
			}
		}
		else if(neighbour){
			snakeMove(neighbour);
		}
	};

	/* ************************************************************************ */
	/* private methods */
	/* ************************************************************************ */

	var endGame=function(msg, win){
		alert(msg);
		actual_cell=null;
		resetBody();
		printBody();
		snake_game.newGame((win ? speed_minus : 0), win);
	};

	var snakeMove=function(neighbour, meal){
		var old_body=snake_body;
		var old_body_index=snake_body_index-(meal ? 0 : 1);
		resetBody();
		addBody(neighbour);
		for(var x=0; x<old_body_index; x++){
			addBody(old_body[x]);
		}
		printBody();
		actual_cell=neighbour;
	};

	var setDirection=function(_direction){
		direction=printDirection(_direction);
	};

	var keyPressHook=function(){
		$("body").keydown(function(e){
				if ((e.keyCode || e.which) === 37){
					setDirection('left');
				}
				else if ((e.keyCode || e.which) === 39){
					setDirection('right');
				}
				else if ((e.keyCode || e.which) === 38){
					setDirection('top');
				}
				else if ((e.keyCode || e.which) === 40){
					setDirection('down');
				}
		});
	};

	var printDirection=function(_direction){
		return (_direction && (_direction==='left' || _direction==='right' || _direction==='top' || _direction==='down') ? _direction : 'right');
	};

	var startPosition=function(){
		if(start_cell){
			resetBody();
			addBody(start_cell);
			printBody();
		}
	};

	var addBody=function(cell){
		snake_body[snake_body_index]=cell;
		snake_body_index++;
	};

	var printBody=function(){
		$('#'+plan_id+' div.cell.snake-body').removeClass('snake-body').removeClass('snake-head');
		for (var index = 0; index <= snake_body_index-1; index++) {
			var cell=snake_body[index];
			cell.addClass('snake-body');
			if(!index){
				cell.addClass('snake-head');
			}
		}
	};

	var resetBody=function(){
		snake_body=new Object();
		snake_body_index=0;
	};

	return self;
}