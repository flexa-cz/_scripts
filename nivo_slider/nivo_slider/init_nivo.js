$(window).ready(function() {
    $('#nivo_slider_header').nivoSlider({
        effect:'fade', //Specify sets like: random,"sliceDownRight","sliceDownLeft","sliceUpRight","sliceUpLeft","sliceUpDown","sliceUpDownLeft","fold","fade"
        animSpeed:500, //Slide transition speed
        pauseTime:5000000,
        directionNav:true, //Next & Prev
        directionNavHide:true, //Only show on hover
        controlNav:true, //1,2,3...
        pauseOnHover:true, //Stop animation while hovering
        captionOpacity:0.5 //Universal caption opacity


//        slices:4
//        startSlide:0, //Set starting Slide (0 index)
//        controlNavThumbs:false, //Use thumbnails for Control Nav
//        controlNavThumbsFromRel:false, //Use image rel for thumbs
//        controlNavThumbsSearch: '.jpg', //Replace this with...
//        controlNavThumbsReplace: '_thumb.jpg', //...this in thumb Image src
//        keyboardNav:true, //Use left & right arrows
//        manualAdvance:false, //Force manual transitions
//        beforeChange: function(){},
//        afterChange: function(){},
//        slideshowEnd: function(){}, //Triggers after all slides have been shown
//        lastSlide: function(){}, //Triggers when last slide is shown
//        afterLoad: function(){} //Triggers when slider has loaded
    });
});