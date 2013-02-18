<?php
/**
 * @file
 * Unimelb Views template for displaying a the Banner slideshow.
 *
 * - $slideshow: The slideshow.
 * - $options: Settings for the active style.
 * - $rows: The rows output from the View.
 * - $title: The title of this group of rows. May be empty.
 *
 * This is a copy of views-slideshow.tpl.php renamed to views-slideshow--banner.tpl.php
 * so that it applies only to the FoA banner.
 *
 * It is quite stripped down.
 *
 * The pager is rendered to the right of the images, regardless of setting.
 *
 * @see: views-slideshow-pager-fields.tpl.php
 */
?>
<!-- <?php print __FILE__ ?> -->
<div class="slider col-6 first">
  <!-- Slideshow -->
  <?php print $slideshow; ?>
</div>

<?php if (!empty($top_widget_rendered)): ?>
  <div class="col-2 rightside">
    <?php print $top_widget_rendered; ?>
  </div>
<?php endif; ?>

<?php if (!empty($bottom_widget_rendered)): ?>
  <div class="col-2 rightside">
    <?php print $bottom_widget_rendered; ?>
  </div>
<?php endif; ?>
