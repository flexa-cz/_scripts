$(window).ready(function(){
	var player_1=new Player(1,'&cir;','Petr',$('div.debug_info.player_1'));
	var player_2=new Player(2,'&times;','Pavel',$('.debug_info.player_2'));
	var plan = new Plan();
	plan.Init('tic_tac_toe').SetPlayer(1, player_1).SetPlayer(2, player_2);
});