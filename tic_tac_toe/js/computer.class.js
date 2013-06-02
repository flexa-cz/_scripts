/**
 * spocita na ktere pole bude pocitac hrat
 * @todo zahajeni hry
 * @todo ignorovat linii, ktera je kratsi nez 4 a je z jedne strany uzavrena (aspon pri obrane)
 * @todo kombinace s druhou skupinou, kdyz se rozhoduje mezi vice moznostmi
 * @todo kdyz ma nekde posledni tah k vyhre, tak nebranit
 * @todo snizit pocet pruchodu pri skladani skupin
 * @param {array} game_lines_settings informace o hracim poli
 * @param {array} game_win_line_length delka vytezne skupiny znaku
 * @param {integer} plan_cols pocet sloupcu herniho pole
 * @param {integer} plan_rows pocet radku herniho pole
 */
function Computer(game_lines_settings,game_win_line_length,plan_cols,plan_rows){
	var lines_settings=game_lines_settings;
	var success;
	var help_success;
	var min_point_to_defense=9;
	var win_line_length=game_win_line_length;
	var cols=plan_cols;
	var rows=plan_rows;
	var count_of_cells=cols*rows;

	this.GetCellToPlay=function(my_groups,rival_groups){
		var return_itemid;

		CalculateSucces(my_groups);
		var my_success=success;
		CalculateSucces(rival_groups);
		var rival_success=success;
//		console.log(rival_success);
		if((rival_success.length-1)>=min_point_to_defense){
			var last_rival_success=rival_success[(rival_success.length-1)];
			return_itemid=last_rival_success[Math.floor(Math.random()*last_rival_success.length)];
		}
		else{
			var last_my_success=my_success[(my_success.length-1)];
			return_itemid=last_my_success[Math.floor(Math.random()*last_my_success.length)];
		}
		return $('div.cell[itemid='+return_itemid+']');
	};

	var CalculateSucces=function(groups){
		success=new Array();
		help_success=new Array();
		if(groups['horizontal'].length && groups){
			for(var direction in groups){
				for(var group_key in groups[direction]){
					var group_length=groups[direction][group_key].length;
					var first_item=groups[direction][group_key][0];
					var last_item=groups[direction][group_key][group_length-1];
//					console.log('-------');
//					console.log(direction);
//					console.log(group_length);
					SetHelpSucces(group_length, first_item['itemid'],last_item['itemid'],lines_settings[direction]);
				}
			}
		}
		else{
			$('div.cell').each(function(){
				if(!$(this).hasClass('finished')){
					SetHelpSuccessPoints($(this), $(this).attr('itemid'),1);
				}
			});
		}
		for(var itemid in help_success){
			if(!success[help_success[itemid]]){
				success[help_success[itemid]]=new Array();
			}
			success[help_success[itemid]].push(itemid);
		}
		return this;
	};

	var SetHelpSucces=function(group_length,first_itemid,last_itemid,lines_direction_settings){
		var prev_cell_itemid=first_itemid-lines_direction_settings;
		var next_cell_itemid=last_itemid+lines_direction_settings;
		var prev_cell=null;
		if(prev_cell_itemid>0){
			prev_cell=$('div.cell[itemid='+prev_cell_itemid+']');
		}
		var next_cell=null;
		if(next_cell_itemid<=count_of_cells){
			next_cell=$('div.cell[itemid='+next_cell_itemid+']');
		}
		// spocita body
		var points=0;
//		if(((prev_cell && prev_cell.length) && (next_cell && next_cell.length)) || (group_length===(win_line_length-1))){
		if(((prev_cell && prev_cell.length && !prev_cell.hasClass('finished')) && (next_cell && next_cell.length && !next_cell.hasClass('finished'))) || (group_length===(win_line_length-1))){
			points=group_length*group_length*2;
//			console.log('length: ' + group_length);
//			console.log('points: ' + points);
		}
		else{
			points=Math.ceil((min_point_to_defense-1)/((win_line_length-1)-group_length));
		}
		SetHelpSuccessPoints(prev_cell,prev_cell_itemid,points);
		SetHelpSuccessPoints(next_cell,next_cell_itemid,points);
		return this;
	};

	var SetHelpSuccessPoints=function(cell,itemid,points){
//		if(points>30){
//			console.log('-----------------------------------------------------');
//			console.log('points:' + points);
//			console.log('cell:');
//			console.log(cell);
//		}
		if(cell && !cell.hasClass('finished')){
			if(help_success[itemid]){
				help_success[itemid]+=points;
			}
			else{
				help_success[itemid]=points;
			}
		}
		return this;
	};
}
