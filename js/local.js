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
          
          //_backstretch_fix();
          //_backstretch_fix_1();
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
     
    }
  }
})(jQuery);


function _backstretch_fix()
{
	// Quick fix: backstretch missing
	var default_adjust = 30;
  var slider_height = $('#slider').height();
  var main_content_height = $('#main-content').height();
  
  var sidebar_height = $('.sidebar-right').height();
  var the_nav_height = $('.the_nav').height();
  
  if(slider_height > 0)
  {
  	// On home page
  	if(sidebar_height > 0)
  	{
  		if(main_content_height >= sidebar_height)
			{
				back_height = slider_height + main_content_height + default_adjust;
			}
			else
			{
				back_height = slider_height + sidebar_height + 60;
			}
  	}
  	else
  	{
  		back_height = slider_height + main_content_height + default_adjust;
  	}
  }
  else
  {
  	// Not on home page
  	if(sidebar_height > 0)
  	{
  		if(main_content_height >= sidebar_height)
			{
				back_height = main_content_height + default_adjust;
			}
			else
			{
				back_height = sidebar_height + 40;
			}
  	}
  	else
  	{
  		// Doesn't have sidebar, but navigation menu
  		if(the_nav_height > 0)
  		{
  			if(main_content_height >= the_nav_height)
  			{
  				back_height = main_content_height + default_adjust;
  			}
  			else
  			{
  				back_height = the_nav_height + default_adjust;
  			}
  		}
  		else
  		{
  			back_height = main_content_height + default_adjust;
  		}
  	}
  }
  
  var back_height_px = back_height + 'px';
  $('#backstretch-wrapper').css('height', back_height_px);
}

function _backstretch_fix_1()
{
	var g_header_height = $('#g-header').height();
	var header_height = $('.header').height();
	var footer_height = $('.footer').height();
	var footernav_height = $('#footernav').height();
	var document_height = $(document).height();
	
	var adjust = 180;
	var back_height = document_height - g_header_height - header_height - footer_height - footernav_height - adjust;
	var back_height_px = back_height + 'px';
  $('#backstretch-wrapper').css('height', back_height_px);
}
