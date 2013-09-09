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

      // Theme the search box and search button of intranet, as we are not able to enable standard search module,
      // so we cannot enable the search configuration module, hence need to theme it here, which is bad.
      //_theme_intranet_search_box_and_button();
    }
  }


  function _control_width() {
    // Initial
    var document_width = $(document).width();
    $('body').width(document_width);

    $(window).resize(function() {
      if(!$.browser.msie) {
        var threshold_window_width = 617;
        var window_width = $(window).width();
        if(window_width <= threshold_window_width) {
          $('body').width(window_width);
        }
        else {
          var document_width = $(document).width();
          $('body').width(document_width);
        }
      }
    });
  }

  function _theme_intranet_search_box_and_button() {
    $('form#searchapi-form input[name="search_api_views_fulltext"]').attr('size', '12');
    $('form#searchapi-form input[name="op"]').attr('value', 'Keyword search'); 
  }

})(jQuery);
