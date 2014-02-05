$(window).ready(function(){
	repair_cycle2_margin();
});

$(window).resize(function(){
	repair_cycle2_margin();
});

function repair_cycle2_margin(){
	$('#cycle2_header').each(function(){
		var cycle2=$(this);
		var slideshow=cycle2.children('.cycle-slideshow');
		var cycle2_width=cycle2.width();
		var slideshow_width=slideshow.width();
		var margin=(cycle2_width-slideshow_width)/2;
		slideshow.css('margin-left',margin);
	});
}