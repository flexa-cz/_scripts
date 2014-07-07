/**
 * klonuje zadanou sablonu
 * retezce #num# nahradi poradovym cislem kopie sablony (i vicecetne)
 * retezce #num# se budou nahrazovat poradovym cislem kopie, bez ohledu na smazane kopie
 * retezce #list# se budou nahrazovat aktualnim (bere ohled na smazane kopie) poradovym cislem - POZOR - pouzivat jen v textovych castech sablony (pro udrzeni pozice se obaluje do pomocneho tagu span)
 * <ul>
 * <li>sablonu co se ma klonovat oznacit #code-segment-temp, nebo zadat vlastni</li>
 * <li>prvek do ktereho se maji vkladat klony oznacit #code-segment-root, nebo zadat vlastni</li>
 * <li>pridani pres a.code-segment-add, nebo zadat vlastni</li>
 * <li>odebrani pres a.code-segment-remove, nebo zadat vlastni</li>
 * </ul>
 * @param {string} temp_id [optional] vlastni identifikator sablony (id)
 * @param {string} root_id [optional] vlastni identifikator mista pro vlozeni klonu (id)
 * @param {string} add_class [optional] vlastni identifikator elementu pro pridani klonu (class)
 * @param {string} remove_class [optional] vlastni identifikator elementu pro odebrani klonu (class)
 * @author Vlahovic
 * @version 1.1
 */
function AddRemoveCodeSegment(temp_id,root_id,add_class,remove_class){
	var self=this;
	var temp=$('#' + (temp_id ? temp_id : 'code-segment-temp'));
	var root=$('#' + (root_id ? root_id : 'code-segment-root'));
	var add=(add_class ? add_class : 'code-segment-add');
	var remove=(remove_class ? remove_class : 'code-segment-remove');
	var next_num;
	var count=0;
	var max_segments;
	var complete;
	var efect;
	var temp_is_ready=false;

	/* ************************************************************************ */
	/* init methods */

	/**
	 * metoda, ktera vse zaridi...
	 * @param {integer} _max_segments [optional] maximalni pocet klonu (0=neomezeny)
	 * @param {string} _efect [optional] jak rychle se maji provest efekty (show, hide); kdy je null, nebo false, tak se nastavi "fast"
	 * @param {function} _complete anonymni funkce, ktera se provede po pridani klonu
	 * @returns {AddRemoveCodeSegment}
	 */
	this.init=function(_max_segments,_efect,_complete){
		efect=(_efect===null || _efect===false ? 'fast' : _efect);
		complete=_complete;
		max_segments=parseInt(_max_segments);

		self.initTemp().initAdd().initRemove().initNextNum();
		return this;
	};

	this.initTemp=function(){
		// prida nezbytnosti
		temp.attr('itemid','#num#');
		temp.find('a.'+remove).attr('rel','#num#');
		// vytvori klon
		var clone=temp.clone().removeAttr('id').addClass('code-segment').hide().wrap('<div class="temp" />');
		temp.remove();
		temp=clone.parent('div.temp');
		if(temp.html()){
			temp_is_ready=true;
		}
		return this;
	};

	this.initAdd=function(){
		$('a.' + add).click(function(event){
			event.preventDefault();
			event.stopPropagation();
			self.add();
		});
		return this;
	};

	this.initRemove=function(only_num){
		$('a.' + remove).click(function(event){
			event.preventDefault();
			event.stopPropagation();
			var segment_num=parseInt($(this).attr('rel'));
			// aby se nekupily udalosti na predchozich lektorech
			if(!only_num || only_num===segment_num){
				self.remove(segment_num);
			}
		});
		return this;
	};

	this.initNextNum=function(){
		var act_num=0;
		root.children('.code-segment').each(function(){
			act_num=$(this).attr('itemid');
			count++;
		});
		next_num=parseInt(act_num)+1;
		return this;
	};

	/* ************************************************************************ */
	/* other methods */
	this.add=function(){
//		console.log('add lector with num ' + next_num);
		if(max_segments && max_segments <= count){
			alert('Maximální počet je ' + max_segments + '.');
		}
		else{
			if(temp_is_ready){
				count++;
				root.append(temp.html().replace(/\#num#/g,next_num).replace(/\#list#/g,'<span class="code-segment-list">' + count + '</span>'));
				this.initRemove(next_num).show(next_num);
				next_num++;
			}
		}
		return this;
	};

	/**
	 * prida prvni polozku, pokud jeste neni
	 * @returns {AddRemoveCodeSegment}
	 */
	this.addFirst=function(){
		if(!count){
			this.add();
		}
		else if(complete){
			complete();
		}
		return this;
	};

	this.remove=function(segment_num){
		var segment=this.getSegment(segment_num);
		if(segment){
			var complete_function=function(){
				segment.remove();
				self.repairList();
				if(complete){
					complete(segment);
				}
			};
			segment.hide(efect,complete_function());
			// kdyz show nema zadanou rychlost, tak neprovadi complete
			if(!efect){
				complete_function();
			}
			count--;
		}
		return this;
	};

	this.repairList=function(){
		var list=1;
		root.children('.code-segment').each(function(){
			$(this).find('.code-segment-list').html(list);
			list++;
		});
		return this;
	};

	this.show=function(segment_num){
		var segment=this.getSegment(segment_num);
		if(segment){
			var complete_function=function(){
				segment.css('display','');
				if(complete){
					complete(segment);
				}
			};
			segment.show(efect,complete_function());
			// kdyz show nema zadanou rychlost, tak neprovadi complete
			if(!efect){
				complete_function();
			}
		}
		return this;
	};

	this.getSegment=function(segment_num){
		var ret;
		if(segment_num){
			root.children('.code-segment').each(function(){
				if(parseInt($(this).attr('itemid'))===segment_num){
					ret=$(this);
				}
			});
		}
		return ret;
	};
}