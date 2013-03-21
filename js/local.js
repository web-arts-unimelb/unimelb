/* Local JavaScript additions. */
(function ($) {
  Drupal.behaviors.unimelb = {
    attach: function(context, settings) {

      $('ul.jquerymenu-processed li.parent span.closed').attr('title', 'Expand this menu item');
      $('ul.jquerymenu-processed li.parent span.open').attr('title', 'Collapse this menu item');

      $('ul.jquerymenu-processed li.parent span').bind('click', function() {
        if ($(this).is('.open')) {
          $(this).attr('title', 'Collapse this menu item');
        }
        else {
          $(this).attr('title', 'Expand this menu item');
        }
      });

      // Do not cause javascript errors (and break wysiwyg) if there are no
      // settings to check for.
      if (Drupal.settings.unimelb) {
        var useBackStretch = Drupal.settings.unimelb.backstretch;

        if (useBackStretch) {
          var bgPath = Drupal.settings.unimelb.background;
          $.backstretch(bgPath, {
            speed: 0
          });
        }
        else {
          var bgPath = Drupal.settings.unimelb.background;
          if (bgPath) {
            $('body').css('background-image', 'url(' + bgPath + ')').css('background-repeat', 'no-repeat');
          }
        }
      }

	// Control backstretch-wrapper with / without slider
        var slider = document.getElementById('slider');
        
        var current_url = document.URL;
        current_url = current_url.replace(/\/+$/, "");
        current_url = current_url.replace(/^http:\/\//, "");
	
	var art_site = "arts.unimelb.edu.au";
	var art_dev_site = "www.dev.arts.unimelb.edu.au";       
 
        var slashes = current_url.match(/\//g); 

        if(slider != null && slashes == null) {
        	$('#backstretch-wrapper').css('top', '155px');
        }
        else if(slider == null && slashes == null) {
        	$('#backstretch-wrapper').css('top', '190px');
        }
	else if(slider == null && 
		slashes != null && 
		(window.location.hostname == art_site ||
		 window.location.hostname == art_dev_site
		) 
	)
	{
		$('#backstretch-wrapper').css('top', '155px');	
	}
        else {
        	$('#backstretch-wrapper').css('top', '180px');
        }
    }
  }
})(jQuery);


