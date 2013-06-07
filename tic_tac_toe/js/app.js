/**
 * @todo vytvorit jeste jednen objekt tic_tac_toe a do nej prendat cast logiky z plan.class.js
 * @todo player musi vracet pole odehranych tahu v poradi id (ne asociativni, ale normalni)
 * @todo game nemusi prochazet tolikrat pole tahu, staci jednou na vytvoreni skupin
 */
$(window).ready(function(){
	var player_1=new Player(1,'&cir;','Milan',$('div.debug_info.player_1'));
	var player_2=new Player(2,'&times;','Comp.',$('.debug_info.player_2'),true);
	var plan = new Plan();
	plan.Init('tic_tac_toe').SetPlayer(1, player_1).SetPlayer(2, player_2);
	$('input[name=reset]').click(function(event){
		event.preventDefault();
		event.stopPropagation();
		plan.Reset();
	});
});