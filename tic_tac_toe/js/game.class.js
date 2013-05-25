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

	this.GetLinesSettings=function(){
		return lines_settings;
	}

	this.GetWinLineLength=function(){
		return win_line_length;
	};

	/**
	 * nastaveni hrace
	 * @param {integer} player_num id hrace
	 * @param {object} player_object objekt hrace
	 * @return {Game}
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
	 * @param {integer} player_number id aktivniho hrace
	 * @return {array} pole rad tahu
	 */
	this.GetGroups=function(player_number){
		var draws=players[player_number].GetDraws();
		this.CheckLines(draws).CheckGroups();
//		console.log('LINES');
//		console.log(lines);
//		console.log('GROUPS');
//		console.log(groups);
//		console.log('------------------------------------------------------------');
		return groups;
	};

	/**
	 * vrati vytezne skupiny
	 * @return {array} pole vyteznych skupin tahu
	 */
	this.GetWins=function(){
		return wins;
	};

	/**
	 * vytahne vytezne skupiny
	 * @param {array} group jedna skupina tahu
	 * @return {Game}
	 */
	this.CheckWins=function(group){
		if(group.length>=win_line_length){
			wins.push(group);
		}
		return this;
	};

	var SortArray=function(arr){
		var sorted_array=new Array();
		for(var itemid in arr){
			sorted_array[arr[itemid]['itemid']]=arr[itemid];
		}
		var return_array=new Array();
		for(var key in sorted_array){
			if(sorted_array[key]!==undefined){
				return_array.push(sorted_array[key]);
			}
		}
		return return_array;
	};

	/**
	 * grupuje tahy po skupinach v radcich, sloupcich a diagonalach
	 * @return {Game}
	 */
	this.CheckGroups=function(){
		wins=new Array();
		groups=new Array();
		var group=new Array();
		for(var direction in lines){
			groups[direction]=new Array();
			for(var assoc_key in lines[direction]){
				var prev_itemid=null;
				var sorted_array=SortArray(lines[direction][assoc_key]);
				for(var key in sorted_array){
					if(
						prev_itemid &&
						lines[direction][assoc_key][sorted_array[key]['itemid']+'-itemid']['itemid']!==(prev_itemid+lines_settings[direction]) &&
						lines[direction][assoc_key][sorted_array[key]['itemid']+'-itemid']['itemid']!==(prev_itemid-lines_settings[direction])
					){
						groups[direction].push(group);
						this.CheckWins(group);
						group=new Array();
					}
					group.push(lines[direction][assoc_key][sorted_array[key]['itemid']+'-itemid']);
					prev_itemid=lines[direction][assoc_key][sorted_array[key]['itemid']+'-itemid']['itemid'];
				}
				if(group.length){
					groups[direction].push(group);
					this.CheckWins(group);
					group=new Array();
				}
			}
		}
		return this;
	};

	/**
	 * vysti pro novou hru
	 * @returns {Game}
	 */
	this.Reset=function(){
		lines=new Array();
		groups=new Array();
		wins=new Array();
		return this;
	};

	/**
	 * grupuje tahy po radcich, sloupcich a diagonalach
	 * @param {array} draws pole tahu
	 * @returns {Game}
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
							(draws[round_of_draws_1]['itemid']===draws[round_of_draws_2]['itemid']) ||
							(draws[round_of_draws_1]['itemid']===draws[round_of_draws_1]['itemid']+lines_settings[direction]) ||
							(draws[round_of_draws_1]['itemid']===draws[round_of_draws_2]['itemid']-lines_settings[direction])
						){
							if(!lines[direction][assoc_key]){
								lines[direction][assoc_key]=new Array();
							}
							lines[direction][assoc_key][draws[round_of_draws_1]['itemid'] + '-itemid']=draws[round_of_draws_1];
						}
					}
			}
		}
		return this;
	};

	/**
	 * posklada klic k danemu radku, sloupci, diagonale
	 * @param {string} direction
	 * @param {array} draws
	 * @param {integer} round_of_draws_1
	 * @returns {String}
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