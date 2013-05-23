function Game(){
	var players=new Array();

	this.SetPlayer=function(player_num,player_object){
		if(player_num===1){
			players[1]=player_object;
		}
		else if(player_num===2){
			players[2]=player_object;
		}
		return this;
	};

	this.GetLines=function(active_player){
		var draws=players[active_player].GetDraws();
		var horizontal=this.CheckHorizontal(draws);
		console.log(horizontal);
	};

//	this.CheckWin=function(){
//		var draws=players[active_player].GetDraws();
//		var arr=new Array();
//		arr['cell']=active_cell;
//		arr['row']=active_row;
//		arr['col']=active_col;
//		var check=new Array();
//		check[round]=arr;
//		var horizontal=check;
//		for(var i=0;i<win_line_length;i++){
//			for(var round_of_draw in draws){
//				if(this.CheckHorizontal(horizontal,draws[round_of_draw])){
//					console.log('check');
//					horizontal[round_of_draw]=draws[round_of_draw];
//				}
//			}
//		}
//		console.log(horizontal);
//		return this;
//	};

	this.CheckHorizontal=function(draws){
		var ret=new Array();
		for(var round_of_draws_1 in draws){
			for(var round_of_draws_2 in draws){
				// stejne vynecha
				if(draws[round_of_draws_1]['col']!==draws[round_of_draws_2]['col'] || draws[round_of_draws_1]['row']!==draws[round_of_draws_2]['row']){
					// bere  stejne radky
					if(draws[round_of_draws_1]['row']===draws[round_of_draws_2]['row']){
						var plus=(draws[round_of_draws_1]['col']*1)+1;
						var minus=(draws[round_of_draws_1]['col']*1)-1;
						var compare=(draws[round_of_draws_2]['col']*1);
						if(plus===compare || minus===compare){
							var assoc_key='row_' + draws[round_of_draws_1]['row'];
							if(!ret[assoc_key]){
							console.log(assoc_key);
								ret[assoc_key]=new Array();
							}
							if(round_of_draws_1!==undefined && draws[round_of_draws_1]!==undefined){
								ret[assoc_key].push(draws[round_of_draws_1]);
							}
						}
					}
				}
			}
		}
//		for(var k in ret){
////			console.log(':-)');
////			console.log(ret[k]);
////			console.log(':-(');
//			for(var ret_key in ret[k]){
//				if(ret[k][ret_key]===undefined){
//					console.log('delete undefined...');
//					delete ret[k][ret_key];
//				}
//			}
//		}
		return ret;
	};
}