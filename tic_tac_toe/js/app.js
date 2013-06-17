/**
 * @todo vytvorit jeste jednen objekt tic_tac_toe a do nej prendat cast logiky z plan.class.js
 * @todo player musi vracet pole odehranych tahu v poradi id (ne asociativni, ale normalni)
 * @todo game nemusi prochazet tolikrat pole tahu, staci jednou na vytvoreni skupin
 */
$(window).ready(function(){
	var tic_tac_toe=new TicTacToe();
	tic_tac_toe.Init(tic_tac_toe,'tic_tac_toe',20,20);
});