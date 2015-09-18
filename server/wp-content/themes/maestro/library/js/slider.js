jQuery(document).ready(function($) {

	var swrapper = $("#slider");
	var slider = $('#slider .bxslider');
	
	var ww = $(window).width();
    var wh = $(window).height();
    var hh = $(".header").height();
	
	var bp = {
        bpcbmob: 320,
        bpcbtab: 768,
        bpcbdesk: 1366
    }
	
	var resizeTimer;
	
	if( $("body").hasClass("home") ){
	
		// 1 - On calcule d'abord les dimensions du viewport pour un slide full screen
        function sliderDimensions(ww, wh, hh, changeDataMedia) {

        	swrapper.height(wh - hh);
            // appel du callback
            changeDataMedia(initSlider);

        }
        
        // 2 - On récupère chaque source d'image et on la place dans l'attribut 'data-media' en fonction de la résolution
        function changeDataMedia(initSlider) {
        	
        	var element = $("li", swrapper);

            element.each(function(idx, el){
            	
            	mobMedia = $(this).attr('data-mob');
            	tabMedia = $(this).attr('data-tab');
            	deskMedia = $(this).attr('data-desk');
            	
            	// Backgrounds
                enquire
                .register('screen and (min-width: ' + bp.bpcbmob + 'px) and (max-width: ' + (bp.bpcbtab-1) + 'px)', function() { // Mobile (320)
                    console.log("mob : media match");
                    $(el).css("background-image", "url("+mobMedia+")");
                })
                .register('screen and (min-width: ' + bp.bpcbtab + 'px) and (max-width: ' + (bp.bpcbdesk-1) + 'px)', function() { // Tablette (768)
                    console.log("tab : media match");
                    $(el).css("background-image", "url("+tabMedia+")");
                })
                .register('screen and (min-width: ' + bp.bpcbdesk + 'px)', function() { // Desktop (1024)
                    console.log("desk : media match");
                    $(el).css("background-image", "url("+deskMedia+")")
                });
            	
            });

            // Initialisation du slider quand toutes les opération préalables ont été effectué.
            initSlider();
            
        }
	
        // 3 -Initialisation du slider (appelé en callback de changeDataMedia())
        function initSlider() {
        	
        	slider.bxSlider({
        		
        		auto:true,
        		
        	});
        	
        }
        
        sliderDimensions(ww, wh, hh, changeDataMedia);
	
	}
	
	$(window).on('resize', function(e) {
		
		clearTimeout(resizeTimer);
		
		resizeTimer = setTimeout(function() {
		
	        var ww = $(window).width();
	        var wh = $(window).height();
	        var hh = $(".header").height();
	        sliderDimensions(ww, wh, hh, changeDataMedia);
	        
		}, 250);
		
    });
	
	
	
	
	
});