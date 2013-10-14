function TabSwitcher(){
	var self=this;
	var bar=$('#tab-switcher-bar');
	var effect_speed='fast';

	this.init=function(){
		// hlavni prepinaci lista
		bar.children('li').children('a').each(function(){
			var anchor=$(this).attr('href').replace('#','');
			$(this).attr('itemid',anchor).parent('li').addClass(anchor);
			$(this).click(function(event){
				event.stopPropagation();
				event.preventDefault();
				self.switchTab($(this));
			});
		});
		// ruzna prepinadla na strance
		$('.tab-switcher').click(function(event){
			event.stopPropagation();
			event.preventDefault();
			var id=$(this).attr('itemid');
			self.showTabById(id);
		});
		// zobrazi prvni polozku
		self.hideAll().showFirst();
		return self;
	};

	this.switchTab=function(item){
		var id=item.attr('itemid');
		self.showTabById(id);
		return self;
	};

	this.showTabById=function(id){
		var div=$('div#'+id);
		if(!div.hasClass('active')){
			self.hideAll();
			div.show(effect_speed).addClass('active');
			$('ul#tab-switcher-bar a[itemid='+id+']').addClass('active');
		}
		return self;
	};

	this.showFirst=function(){
		var list=0;
		bar.children('li').children('a').each(function(){
			if(!list){
				var id=$(this).attr('itemid');
				self.showTabById(id);
			}
			list++;
		});
		return self;
	};

	this.hideAll=function(){
		bar.children('li').children('a').each(function(){
			var id=$(this).attr('itemid');
			$(this).parent('li').removeClass('active');
			$('div#'+id).hide(effect_speed).removeClass('active');
			$('ul#tab-switcher-bar a').removeClass('active');
		});
		return self;
	};
}