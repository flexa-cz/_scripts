/**
 * spocita na ktere pole bude pocitac hrat
 * @todo zahajeni hry
 * @todo ignorovat linii, ktera je kratsi nez 4 a je z jedne strany uzavrena (aspon pri obrane)
 * @todo kombinace s druhou skupinou, kdyz se rozhoduje mezi vice moznostmi
 */
function Computer(game_lines_settings){
	var lines_settings=game_lines_settings;
	var success;
	var help_success;
	var min_point_to_defense=9;

	this.GetCellToPlay=function(my_groups,rival_groups){
		var return_itemid;

		CalculateSucces(rival_groups);
		var rival_success=success;
		CalculateSucces(my_groups);
		var my_success=success;

		if((rival_success.length-1)>=min_point_to_defense){
			var last_rival_success=rival_success[(rival_success.length-1)];
			return_itemid=last_rival_success[Math.floor(Math.random()*last_rival_success.length)];
		}
		else{
			var last_my_success=my_success[(my_success.length-1)];
			return_itemid=last_my_success[Math.floor(Math.random()*last_my_success.length)];
		}
		console.log(return_itemid);
		return $('div.cell[itemid='+return_itemid+']');
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
			$('div.cell').each(function(){
				if(!$(this).hasClass('finished')){
					SetHelpSucces(0, $(this).attr('itemid'));
				}
			});
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