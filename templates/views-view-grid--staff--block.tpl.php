<?php

/**
 * @file
 * Default simple view template to display a rows in a grid.
 *
 * - $rows contains a nested array of rows. Each row contains an array of
 *   columns.
 *
 * @ingroup views_templates
 */
?>

<?php if(isset($rows[0][0]) && !_views_view_grid__staff__block_is_empty($rows[0][0])):  ?>

<?php if (!empty($title)) : ?>
  <h3><?php print $title; ?></h3>
<?php endif; ?>
<table class="<?php print $class; ?>"<?php print $attributes; ?>>
  <?php if (!empty($caption)) : ?>
    <caption><?php print $caption; ?></caption>
  <?php endif; ?>

  <tbody>
    <?php foreach ($rows as $row_number => $columns): ?>
      <tr <?php if ($row_classes[$row_number]) { print 'class="' . $row_classes[$row_number] .'"';  } ?>>
        <?php foreach ($columns as $column_number => $item): ?>
          <td <?php if ($column_classes[$row_number][$column_number]) { print 'class="' . $column_classes[$row_number][$column_number] .'"';  } ?>>
            <?php print $item; ?>
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php endif; ?>


<?php

function _views_view_grid__staff__block_is_empty($html) {
	$dom = new DOMDocument();
	$dom->loadHTML($html);
	$x = new DOMXPath($dom);
	$query = '//div[contains(@class, "field-content")]/*';
	$nodes = $x->query($query);

	if($nodes->length <= 0) {
		return true;
	}
	else {
		return false;
	}
}

?>
