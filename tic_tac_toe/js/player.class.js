function Player(player_number,player_symbol,player_name,player_debug){
	this.player_no=player_number;// jen kvuli prehlednosti v console.log
	var number=player_number;
	var symbol=player_symbol;
	var name=player_name;
	var debug=player_debug;
	var draws=new Array();

	this.SetDraw=function(round,cell,row,col){
		var arr=new Array();
		arr['cell']=cell;
		arr['row']=row;
		arr['col']=col;
		draws[round]=arr;
		return this;
	};

	this.DebugInfo=function(){
		debug.html(this.GetDrawsString());
		return this;
	};

	this.GetDrawsString=function(){
		var ret='';
		for(var key in draws){
			ret+=key + ') ' + 'row ' + draws[key]['row'] + '; col ' + draws[key]['col'] + '<br>';
		}
		return ret;
	};

	this.GetDraws=function(){
		return draws;
	};

	this.GetSymbol=function(){
		return symbol;
	};

	this.GetNumber=function(){
		return number;
	};

	this.GetName=function(){
		return name;
	};
}