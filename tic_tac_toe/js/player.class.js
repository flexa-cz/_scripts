function Player(player_number,player_symbol,player_name,player_debug){
	this.player_no=player_number;// jen kvuli prehlednosti v console.log
	var number=player_number;
	var symbol=player_symbol;
	var name=player_name;
	var debug=player_debug;
	var draws=new Array();

	/**
	 * ulozi tah hrace
	 */
	this.SetDraw=function(round,cell,row,col,itemid){
		var arr=new Array();
		arr['cell']=cell;
		arr['row']=row*1;
		arr['col']=col*1;
		arr['itemid']=itemid*1;
		draws[round]=arr;
		return this;
	};

	/**
	 * vypisuje tahy
	 */
	this.DebugInfo=function(){
		debug.html(this.GetDrawsString());
		return this;
	};

	/**
	 * radek pro vypis tahu
	 */
	this.GetDrawsString=function(){
		var ret='';
		for(var key in draws){
			ret+=key + ') ' + 'row ' + draws[key]['row'] + '; col ' + draws[key]['col'] + '<br>';
		}
		return ret;
	};

	/**
	 * vraci tahy hrace
	 */
	this.GetDraws=function(){
		return draws;
	};

	/**
	 * vraci symbol, kterym hrac hraje
	 */
	this.GetSymbol=function(){
		return symbol;
	};

	/**
	 * vraci cislo hrace [1,2]
	 */
	this.GetNumber=function(){
		return number;
	};

	/**
	 * vraci jmeno hrace
	 */
	this.GetName=function(){
		return name;
	};
}