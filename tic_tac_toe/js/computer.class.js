/**
 * spocita na ktere pole bude pocitac hrat
 */
function Computer(game_lines_settings){
	var lines_settings=game_lines_settings;
	var success;
	var help_success;

	this.GetCellToPlay=function(my_groups,rival_groups){
		CalculateSucces(my_groups);
		var my_success=success;
		console.log(my_success);
		CalculateSucces(rival_groups);
		var rival_success=success;
	};

	var CalculateSucces=function(groups){
		success=new Array();
		help_success=new Array();
		if(groups){
			for(var direction in groups){
				for(var group_key in groups[direction]){
					var group_length=groups[direction][group_key].length;
					var first_item=groups[direction][group_key][0];
					var last_item=groups[direction][group_key][group_length-1];
					SetHelpSucces(group_length, first_item['itemid']-lines_settings[direction]);
					SetHelpSucces(group_length, last_item['itemid']+lines_settings[direction]);
				}
			}
			for(var itemid in help_success){
				if(!success[help_success[itemid]]){
					success[help_success[itemid]]=new Array();
				}
				success[help_success[itemid]].push(itemid);
			}
		}
		else{

		}
		return this;
	};

	var SetHelpSucces=function(group_length,itemid){
		var cell=$('div.cell[itemid='+itemid+']');
		if(cell.length && !cell.hasClass('finished')){
			var points=group_length*group_length*2;
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