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

					if($('div#backstretch').length <= 0) {
						$.backstretch(bgPath, {speed: 0});
					}

          // Fix transparent background issue
          $('#backstretch-wrapper').css('opacity','.9'); 
        }
        else {
          var bgPath = Drupal.settings.unimelb.background;
          if (bgPath) {
            $('body').css('background-image', 'url(' + bgPath + ')').css('background-repeat', 'no-repeat');
          }
        }
      }
      
      // Hide webform_table_element's 1st columns (all 1st columns)
      $("table.webform-component-table-element td:nth-child(1), table.webform-component-table-element th:nth-child(1)").hide();
     
      // Control width for uni global header and footer
			_control_width(); 
    }
  }


  function _control_width() {
    // Initial
		var document_width = null;
		var window_width = null;

		if(!$.browser.msie) {
			// Non IE
			window_width = $(window).width();
			if(window_width <= 630) {
				// Assume viewing in mobile
				$('body').once('body').width(window_width);
			}
			else {
    		document_width = $(document).width();
				$('body').once('body').width(document_width);
			}
		}
		else {
			// IE
			document_width = document.body.clientWidth;
		}

		// Listen	
    $(window).resize(function() {
			var threshold_window_width = 617;			
			var window_width = null;
			var document_width = null;

			if(!$.browser.msie) {
				window_width = $(window).width();
				document_width = $(document).width();
			}
			else {
				window_width = document.documentElement.clientWidth;
        document_width = document.body.clientWidth;
			}
			
			if(window_width <= threshold_window_width) {
      	$('body').width(window_width);
      }
      else {
        var document_width = $(document).width();
        $('body').width(document_width);
      }
    });
  }
})(jQuery);
