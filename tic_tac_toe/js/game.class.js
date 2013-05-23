function Game(num_of_cols,num_of_rows){
	var players=new Array();
	var win_line_length=5;
	var cols=num_of_cols;
	var rows=num_of_rows;
	var lines_settings=new Array();
	lines_settings['horizontal']=1;
	lines_settings['vertical']=cols*1;
	lines_settings['lt_to_rb']=cols*1+1;
	lines_settings['rt_to_lb']=cols*1-1;
	var lines=new Array();
	var groups=new Array();
	var wins=new Array();

	/**
	 * nastaveni hrace
	 */
	this.SetPlayer=function(player_num,player_object){
		if(player_num===1){
			players[1]=player_object;
		}
		else if(player_num===2){
			players[2]=player_object;
		}
		return this;
	};

	/**
	 * vrati skupiny radku, sloupcu, diagonal
	 */
	this.GetLines=function(active_player){
		var draws=players[active_player].GetDraws();
		this.CheckLines(draws).CheckGroups();

//		console.log('LINES');
//		console.log(lines);
//		console.log('GROUPS');
//		console.log(groups);
//		console.log('------------------------------------------------------------');
		return lines;
	};

	/**
	 * vrati vytezne skupiny
	 */
	this.GetWins=function(){
		return wins;
	}

	/**
	 * vytahne vytezne skupiny
	 */
	this.CheckWins=function(group){
		if(group.length===win_line_length){
			wins.push(group);
		}
		return this;
	}

	/**
	 * grupuje tahy po skupinach v radcich, sloupcich a diagonalach
	 */
	this.CheckGroups=function(){
		var group=new Array();
		for(var direction in lines){
			groups[direction]=new Array();
			for(var assoc_key in lines[direction]){
				var prev_itemid=null;
				for(var itemid in lines[direction][assoc_key]){
					if(
						prev_itemid &&
						lines[direction][assoc_key][itemid]['itemid']!==(prev_itemid+lines_settings[direction]) &&
						lines[direction][assoc_key][itemid]['itemid']!==(prev_itemid-lines_settings[direction])
					){
						groups[direction].push(group);
						this.CheckWins(group);
						group=new Array();
					}
					group.push(lines[direction][assoc_key][itemid]);
					prev_itemid=lines[direction][assoc_key][itemid]['itemid'];
				}
				if(group.length){
					groups[direction].push(group);
					this.CheckWins(group);
					group=new Array();
				}
			}
		}
		return this;
	}

	/**
	 * grupuje tahy po radcich, sloupcich a diagonalach
	 */
	this.CheckLines=function(draws){
		lines=new Array();
		var assoc_key=null;
		for(var direction_key in lines_settings){
			lines[direction_key]=new Array();
		}
		for(var round_of_draws_1 in draws){
			for(var round_of_draws_2 in draws){
					for(var direction in lines_settings){
						assoc_key=this.GetAssocKey(direction,draws,round_of_draws_1);
						if(
							(draws[round_of_draws_1]['itemid']===draws[round_of_draws_1]['itemid']) ||
							(draws[round_of_draws_1]['itemid']===draws[round_of_draws_1]['itemid']+lines_settings[direction]) ||
							(draws[round_of_draws_1]['itemid']===draws[round_of_draws_2]['itemid']-lines_settings[direction])
						){
							if(!lines[direction][assoc_key]){
								lines[direction][assoc_key]=new Array();
							}
							lines[direction][assoc_key]['itemid-' + draws[round_of_draws_1]['itemid']]=draws[round_of_draws_1];
							lines[direction].sort();
							lines[direction][assoc_key].sort();
						}
					}
			}
		}
		return this;
	};

	/**
	 * posklada klic k danemu radku, sloupci, diagonale
	 */
	this.GetAssocKey=function(direction,draws,round_of_draws_1){
		var assoc_key=null;
		// horizontal
		if(direction==='horizontal'){
			assoc_key='row-' + draws[round_of_draws_1]['row'];
		}
		// vertical
		else if(direction==='vertical'){
			assoc_key='col-' + draws[round_of_draws_1]['col'];
		}
		// lt_to_rb
		else if(direction==='lt_to_rb'){
			if(draws[round_of_draws_1]['row']===draws[round_of_draws_1]['col']){
				assoc_key='row-0-col-0';
			}
			else if(draws[round_of_draws_1]['row']>draws[round_of_draws_1]['col']){
				assoc_key='row-' + (draws[round_of_draws_1]['row']-draws[round_of_draws_1]['col']) +'-col-0';
			}
			else{
				assoc_key='row-0-col-' + (draws[round_of_draws_1]['col']-draws[round_of_draws_1]['row']);
			}
		}
		// rt_to_lb
		else{
			var last_col=(cols*1)+1;
			var plus=((draws[round_of_draws_1]['row']*1)+(draws[round_of_draws_1]['col']*1));
			if(plus<=last_col){
				assoc_key='row-0-col-' + plus;
			}
			else{
				assoc_key='row-' + (plus-last_col) + 'col-' + last_col;
			}
		}
		return assoc_key;
	};
}