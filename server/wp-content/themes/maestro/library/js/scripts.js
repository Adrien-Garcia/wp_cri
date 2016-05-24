/*
 * Bones Scripts File
 * Author: Eddie Machado
 *
 * This file should contain any js scripts you want to add to the site.
 * Instead of calling it in the header or throwing it inside wp_head()
 * this file will be called automatically in the footer so as not to
 * slow the page load.
 *
 * There are a lot of example functions and tools in here. If you don't
 * need any of it, just remove it. They are meant to be helpers and are
 * not required. It's your world baby, you can do whatever you want.
*/


/*
 * Get Viewport Dimensions
 * returns object with viewport dimensions to match css in width and height properties
 * ( source: http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript )
*/
function updateViewportDimensions() {
	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],x=w.innerWidth||e.clientWidth||g.clientWidth,y=w.innerHeight||e.clientHeight||g.clientHeight;
	return { width:x,height:y }
}
// setting the viewport width
var viewport = updateViewportDimensions();


/*
 * Throttle Resize-triggered Events
 * Wrap your actions in this function to throttle the frequency of firing them off, for better performance, esp. on mobile.
 * ( source: http://stackoverflow.com/questions/2854407/javascript-jquery-window-resize-how-to-fire-after-the-resize-is-completed )
*/
var waitForFinalEvent = (function () {
	var timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) { uniqueId = "Don't call this twice without a uniqueId"; }
		if (timers[uniqueId]) { clearTimeout (timers[uniqueId]); }
		timers[uniqueId] = setTimeout(callback, ms);
	};
})();

// how long to wait before deciding the resize has stopped, in ms. Around 50-100 should work ok.
var timeToWaitForLast = 100;


/*
 * Here's an example so you can see how we're using the above function
 *
 * This is commented out so it won't work, but you can copy it and
 * remove the comments.
 *
 *
 *
 * If we want to only do it on a certain page, we can setup checks so we do it
 * as efficient as possible.
 *
 * if( typeof is_home === "undefined" ) var is_home = $('body').hasClass('home');
 *
 * This once checks to see if you're on the home page based on the body class
 * We can then use that check to perform actions on the home page only
 *
 * When the window is resized, we perform this function
 * $(window).resize(function () {
 *
 *    // if we're on the home page, we wait the set amount (in function above) then fire the function
 *    if( is_home ) { waitForFinalEvent( function() {
 *
 *      // if we're above or equal to 768 fire this off
 *      if( viewport.width >= 768 ) {
 *        console.log('On home page and window sized to 768 width or more.');
 *      } else {
 *        // otherwise, let's do this instead
 *        console.log('Not on home page, or window sized to less than 768.');
 *      }
 *
 *    }, timeToWaitForLast, "your-function-identifier-string"); }
 * });
 *
 * Pretty cool huh? You can create functions like this to conditionally load
 * content and other stuff dependent on the viewport.
 * Remember that mobile devices and javascript aren't the best of friends.
 * Keep it light and always make sure the larger viewports are doing the heavy lifting.
 *
*/

/*
 * We're going to swap out the gravatars.
 * In the functions.php file, you can see we're not loading the gravatar
 * images on mobile to save bandwidth. Once we hit an acceptable viewport
 * then we can swap out those images since they are located in a data attribute.
*/
function loadGravatars() {
  // set the viewport using the function above
  viewport = updateViewportDimensions();
  // if the viewport is tablet or larger, we load in the gravatars
  if (viewport.width >= 768) {
  jQuery('.comment img[data-gravatar]').each(function(){
    jQuery(this).attr('src',jQuery(this).attr('data-gravatar'));
  });
	}
} // end function


/*
 * Put all your regular jQuery in here.
*/
jQuery(document).ready(function($) {

	/*
	 * Obfusction des liens menant vers l'accueil & de www.addonline.fr
	 */
	demoer = {'home':'hmdjf@|'};
	$('.lienhome').bind('click', function(){
		str = demoer["home"];
		str = str.replace('h', 'http://');
		str = str.replace('m', 'www');
		str = str.replace('d', '.');
		str = str.replace('j', 'adresse');
		str = str.replace('f', 'projet');
		str = str.replace('@', '.');
		str = str.replace('|', 'com');
		document.location=str;
	});
	
	aolink = {'jpfooter':'gkey@&'};
	$('.lienjp').bind('click', function(){
		str = aolink["jpfooter"];
		str = str.replace('g', 'http://');
		str = str.replace('k', 'www');
		str = str.replace('e', '.');
		str = str.replace('y', 'jetpulp');
		str = str.replace('@', '.');
		str = str.replace('&', 'fr');
		document.location=str;
	});	
	
	/*
	 * Let's fire off the gravatar function
	 * You can remove this if you don't need it
	 */
  loadGravatars();


	/*--
		PAGE MON COMPTE > LISTE QUESTIONS
	*/


	/*datepicker*/






  /* MMENU */

  	$("#menu").clone().attr("id","menu_mobile").insertBefore("#menu");

	$("#menu_mobile").mmenu({
       "extensions": [
          "border-none",
          "effect-menu-slide",
          "effect-listitems-slide",
          null,
          "pageshadow",
          "theme-dark"
       ],
       "autoHeight": true,
       "navbars": [
          {
             "position": "top",
             "content": [
                "prev",
                "",
                "close"
             ]
          }
          /*{
             "position": "bottom",
             "content": [
               "<a href='http://www.lexbase.fr/' target='_blank'><img src='/wp-content/themes/maestro/library/images/origin/logo-lexbase-2.png' alt='lexbase'></a>",
				"<a href='http://www.wolterskluwerfrance.fr/' target='_blank'><img src='/wp-content/themes/maestro/library/images/origin/logo-woltersKluwer-2.png' alt='Wolters Kluwer'></a>"
             ]
          }*/
       ]
    });



    /* LIENS FOOTER */

    $(".block-right > li > a.js-panel-connexion-open").click(function( event ){
    	event.preventDefault();
    });

    $(".row_03 a.js-panel-connexion-open").click(function( event ){
    	event.preventDefault();
    });

     



     /* ANNIM MENU ACCEDER A MON COMPTE*/

    $("#acceder-compte > li > a.acceder-compte").mouseenter(function(){
    	$("#acceder-compte .logout-2").addClass('visible');
    });
    $("#acceder-compte > li > a.acceder-compte").mouseleave(function(){
    	$("#acceder-compte .logout-2").removeClass('visible');
    });

    $(".logout-2").hover(function(){
    	$("#acceder-compte .logout-2").addClass('visible');
    });
    $(".logout-2").mouseleave(function(){
    	$("#acceder-compte .logout-2").removeClass('visible');
    })
    

    /* ANNIM MENU RECHERCHER DANS LES BASES DE CONNAISSANCES */

    $("#rechercher > li > a").mouseenter(function(){
      $("#rechercher .overlay").addClass('visible');
    });
    $("#rechercher > li").mouseleave(function(){
      $("#rechercher .overlay").removeClass('visible');
    });

    $("#rechercher .overlay").hover(function(){
      $("#rechercher .overlay").addClass('visible');
    });
    $("#rechercher .overlay").mouseleave(function(){
      $("#rechercher .overlay").removeClass('visible');
    })



    /* FILTRE LISTE VEILLES */

    $("#tri_matiere").click(function(){

    	$("#tri_matiere > span").toggleClass("active"); 

    	$("#tri_matiere + .panel").toggle('slow');

    });


}); /* end of as page load scripts */
