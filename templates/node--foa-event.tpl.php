<?php
/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct url of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type, i.e., "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode, e.g. 'full', 'teaser'...
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined, e.g. $node->body becomes $body. When needing to access
 * a field's raw values, developers/themers are strongly encouraged to use these
 * variables. Otherwise they will have to explicitly specify the desired field
 * language, e.g. $node->body['en'], thus overriding any language negotiation
 * rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 */

?>

<div class="foa-event">
	<!-- Assume it is never empty -->
	<?php if(!empty($content['field_foa_event_time'])): ?>
		<?php echo '<strong>'. render($content['field_foa_event_time']). '</strong>'; ?>
	<?php endif; ?>	
	
	<!-- Assume it is never empty -->
	<?php if(!empty($content['body'])): ?>
		<?php echo render($content['body']); ?>
	<?php endif; ?>

	<?php
		$email = $node->field_foa_event_info_email[LANGUAGE_NONE][0]['value'];
		$phone = $node->field_foa_event_info_phone[LANGUAGE_NONE][0]['value'];
		echo _render_contact($email, $phone);
	?>

	<?php if(!empty($node->field_foa_event_location)): ?>
    <?php
			$location = $node->field_foa_event_location[LANGUAGE_NONE][0]['value'];
			$output = '<h3>Location</h3>'. $location; 
			echo $output;
		?>
  <?php endif; ?>

	<?php if(!empty($node->field_foa_event_book_url)): ?>
    <?php
			$url = $node->field_foa_event_book_url[LANGUAGE_NONE][0]['value'];
			$title_html = 'Register';
			
			$attr = array(
				'attributes' => array(
					'class' => 'button'
				)
			); 
      $output = '<h3>Booking</h3>'. l($title_html, $url, $attr);
      echo $output;
    ?>
  <?php endif; ?>

	<?php if(!empty($node->field_foa_event_p_link)): ?>
		<?php
			$presenters = $node->field_foa_event_p_link[LANGUAGE_NONE];
			echo _render_presenter($presenters);	
		?>
	<?php endif; ?>

</div>


<?php

function _render_contact($email, $phone) {
	$output = '';
	$email_out = '';
	$phone_put = '';

	if(!empty($email)) {
		$email_out = '<strong>Email: </strong>'. l($email, "mailto:$email", array('absolute' => TRUE));			
	}
	
	if(!empty($phone)) {
		$item = array(
			'value' => $phone,
			'safe_value' => $phone,
		);
		$phone = _unimelb_formatters_format_unimelb_phone($item)['safe_value'];
		$phone_out = '<strong>Phone: </strong>'. l($phone, "tel:$phone", array('absolute' => TRUE)); 
	}

	$attachment = implode('<br/>', array($email_out, $phone_out));

	if(!empty($email) || !empty($phone)) {
		$output = '<h3>Contact</h3>'. $attachment; 
	}
	else {
		$output = '';
	}

	return $output;
}


function _render_presenter($items = array()) {
	$output = '';

	// Item zero is still there, but url is empty.
	if(empty($items[0]['url'])) {

	}
	else {
		foreach($items as $item) {
			$title = $item['title'];
			$url = $item['url'];
			$url_html = l($title, $url);
			$output .= $url_html. '<br/>';
		}
		$output = '<h3>Presenter</h3>'. $output;
	}

	return $output;
}

?>
