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
            speed: 800
          });
        }
        else {
          var bgPath = Drupal.settings.unimelb.background;
          if (bgPath) {
            $('body').css('background-image', 'url(' + bgPath + ')').css('background-repeat', 'no-repeat');
          }
        }
      }
    }
  }
})(jQuery);


