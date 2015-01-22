<?php
/**
 * @file template.php
 *
 * Thanks to Aaron Tan and team at the Faculty of Architecture, Building and
 * Planning, University of Melbourne, and Paul Tagell and team at Marketing
 * and Communications, University of Melbourne - Media Insights 2011
 */

/**
 * Implements hook_preprocess_html().
 */
function unimelb_preprocess_html(&$variables) {
  $variables['site_name'] = _unimelb_space_tags(variable_get('site_name'));
  $variables['page_title'] = _unimelb_space_tags(drupal_get_title());

  if (empty($variables['page_title'])) {
    $variables['page_title'] = $variables['site_name'];
  }

  // MARCOM templates version.
  $variables['version'] = theme_get_setting('unimelb_settings_template');
  if (empty($variables['version'])) {
    $variables['version'] = '1-1-0';
  }

  // Body class that is used by templates to show or not show the university logo.
  $variables['brand_logo'] = theme_get_setting('unimelb_settings_custom_logo') ? 'logo' : 'no-logo';

  // Generate the meta tag content here, simply print the content in the tpl.php.
  if ($keywords = theme_get_setting('unimelb_settings_meta-keywords')) {
    $keywords = explode(',', theme_get_setting('unimelb_settings_meta-keywords'));
  }
  $keywords[] = $variables['page_title'];
  $keywords[] = $variables['site_name'];

  // There could be node keywords from an earlier hook_preprocess_html().
  if (!empty($variables['node_keywords'])) {
    // Store the node keywords in a temp variable and then merge them
    // with the site keywords. A direct assignment leads to a segfault
    // or an empty variable.
    $frog = explode(', ', $variables['node_keywords']);
    $keywords = array_merge($keywords, $frog);
  }

  // Sanitise the keywords.
  $variables['unimelb_meta_keywords'] = check_plain(implode(', ', $keywords));

  $variables['unimelb_meta_description'] = $variables['site_name'] . ': ' . $variables['page_title'];
  if ($variables['is_front'] && $description = theme_get_setting('unimelb_settings_ht-right')) {
    $variables['unimelb_meta_description'] .= ' - ' . $description;
  }

  if ($creator = theme_get_setting('unimelb_settings_maint-name')) {
    $creators[] = theme_get_setting('unimelb_settings_maint-name');
  }
  $creators[] = $variables['site_name'];

  $variables['unimelb_meta_creator'] = strip_tags( implode(', ', $creators) );
  $variables['unimelb_meta_authoriser'] = strip_tags( theme_get_setting('unimelb_settings_auth-name') );

  $variables['unimelb_meta_date'] = theme_get_setting('unimelb_settings_date-created');
  if (empty($variables['unimelb_meta_date'])) {
    $variables['unimelb_meta_date'] = format_date(time(), 'custom', 'Y-m-d');
  }

	// Viewport
	$variables['viewport_initial_scale'] = theme_get_setting('viewport_initial_scale');
  if(empty($variables['viewport_initial_scale'])) {
		$variables['viewport_initial_scale'] = '0.67';
  }

  // Add in common theme specific meta info.
  $variables += _unimelb_meta_info();

  // Including injector CSS and JS via HTTP throws up a warning if the site is
  // on HTTPS. Detect and adjust the protocol accordingly.
  global $is_https;

  $variables['scheme'] = 'http://';

  if ($is_https) {
    $variables['scheme'] = 'https://';
  }

  // Avoid warnings if the css_splitter module is not present.
  if (!module_exists('css_splitter')) {
    $variables['styles_system'] = $variables['styles_default'] = '';
    $variables['styles_theme'] = drupal_get_css();
  }

  // Background images.
  $background = theme_get_setting('background_front_path');
  $backstretch = theme_get_setting('backstretch');
  if ($variables['is_front'] == FALSE) {
    $background = theme_get_setting('background_secondary_path');
    if (empty($background)) {
      $background = theme_get_setting('background_front_path');
    }
  }

  $variables['backstretch'] = $backstretch;
  if (!empty($background)) {
    if (!empty($backstretch)) {
      drupal_add_js(array('unimelb' => array('background' => file_create_url($background), 'backstretch' => $backstretch)), 'setting');
      $variables['classes_array'][] = 'backstretch';
    }
    else {
      drupal_add_js(array('unimelb' => array('background' => file_create_url($background))), 'setting');
    }
  }

  // Check if we're running a view.
  $menu_item = menu_get_item();
  if ($menu_item['page_callback'] == 'views_page') {
    $variables['classes_array'][] = 'views-view-' . $menu_item['page_arguments'][0];
    $variables['classes_array'][] = 'views-view-' . implode('-', $menu_item['page_arguments']);
  }

  // Allow remote debuggery.
  $debug = theme_get_setting('debug');
  if (!empty($debug)) {
    $debug_host = check_plain(theme_get_setting('debug_host'));
    $debug_port = check_plain(theme_get_setting('debug_port'));

    if (empty($debug_host) || empty($debug_port)) {
      drupal_set_message(t('Remote debug is enabled but there is no debug host or port.'), 'warning');
    }
    else {
      drupal_add_js("http://${debug_host}:${debug_port}/target/target-script-min.js#anonymous", 'external');
    }
  }
}

/**
 * Implements hook_preprocess_page().
 *
 * Use as a wrapper function. This runs on each request anyway and this way
 * I can test for syntax errors via the CLI without getting a bunch of
 * undefined function errors.
 */
function unimelb_preprocess_page(&$variables) {
  /**
   * If looking at a node with a redirect field, redirect now. Not later.
   */
  if (isset($variables['node']) && !empty($variables['node']->field_external_url[$variables['node']->language][0])) {
    if (valid_url($variables['node']->field_external_url[$variables['node']->language][0]['safe_value'])) {
      header("Location: {$variables['node']->field_external_url[$variables['node']->language][0]['safe_value']}");
      die;
    }
  }

  // Responsive layout using a per-layout template include.
  // This only actually does something in templates/page-front.tpl.php
  $variables['layout'] = 'layout/' . theme_get_setting('unimelb_settings_columns') . '.tpl.inc';

  // Allow us to override the layout on a node-type basis!
  if (!empty($variables['node']) && $variables['node']->type == 'study_area') {
    $variables['layout'] =  'layout/node.tpl.inc';
  }

  if (!file_exists(path_to_theme() . '/templates/' . $variables['layout'])) {
    // If there is no defined template or if the file is missing, default to 3+1.
    $variables['layout'] = 'layout/3-1.tpl.inc';
  }

  // Body class that is used by templates to show or not show the university logo.
  $variables['brand_logo'] = theme_get_setting('unimelb_settings_custom_logo', '') ? 'logo' : 'no-logo';

  $search_form = theme_get_setting('unimelb_settings_site_search_box');
  if (!empty($search_form)) {
    if (module_exists('search')) {
      $variables['site_search_box'] = drupal_get_form('search_block_form');
    }
    // @TODO: Do not hardcode this to this search form!
    elseif (function_exists('intranet_searchapi_form')) {
      $variables['site_search_box'] = drupal_get_form('intranet_searchapi_form');
    }
    else {
      $variables['site_search_box'] = FALSE;
    }
  }
  else {
    $variables['site_search_box'] = FALSE;
  }
  
  // Dropdown menu and search box
  $dropdown_and_search = theme_get_setting('unimelb_settings_dropdown_menu_and_search_box');
  if(!empty($dropdown_and_search)) {
		// Force to use the search box
		if(module_exists('search')) {
		 	$variables['site_search_box'] = drupal_get_form('search_block_form');
		 	$variables['dropdown_and_search'] = TRUE;
		}
		// @TODO: Do not hardcode this to this search form!
    elseif (function_exists('intranet_searchapi_form')) {
      $variables['site_search_box'] = drupal_get_form('intranet_searchapi_form');
			$variables['dropdown_and_search'] = TRUE;
    }
    else {
      $variables['site_search_box'] = FALSE;
    }
  }
  else
  {
  	$variables['dropdown_and_search'] = false;
  }
 
	// Viewport
	$viewport_initial_scale = theme_get_setting('viewport_initial_scale');
	if(empty($viewport_initial_scale)) {
		$variables['viewport_initial_scale'] = '0.67';
	}
	else {
		$variables['viewport_initial_scale'] = $viewport_initial_scale;
	}	

  $variables['unimelb_ht_right'] = theme_get_setting('unimelb_settings_ht-right', '');
  $variables['unimelb_ht_left'] = theme_get_setting('unimelb_settings_ht-left', '');

  // Add in common theme specific meta info.
  $variables += _unimelb_meta_info();

  /**
   * Making Unimelb Settings variables available to js
   */
  $vars = array();
  if ($value = theme_get_setting('unimelb_settings_site-name-short')) {
    $vars['sitename'] = $variables['unimelb_site_name_short'] = $value;
  }

  if ($value = theme_get_setting('unimelb_settings_parent-org-short')) {
    $vars['parentorg'] = $variables['unimelb_parent_org_short'] = $value;
  }

  if ($value = theme_get_setting('unimelb_settings_parent-org-url')) {
    $vars['parentorgurl'] = $variables['unimelb_parent_org_url'] = $value;
  }

  if (!empty($variables['unimelb_meta_parent_org_url'])) {
    $variables['home_page_url'] = $variables['unimelb_meta_parent_org_url'];
  }
  else {
    $variables['home_page_url'] = $variables['front_page'];
  }

  if (!empty($vars)) {
    drupal_add_js($vars, 'setting');
  }

  $variables['backstretch'] = theme_get_setting('backstretch');

  // Force a re-sort of the page contents.
  $variables['page']['content']['#sorted'] = FALSE;

  // Add some classes on various items to help us space stuff out.
  if (!empty($variables['page']['header_menu'])) {
    $variables['classes_array'][] = 'uom-header';
  }
  if (!empty($variables['page']['slider'])) {
    $variables['classes_array'][] = 'uom-slider';
  }
  if (!empty($variables['page']['feature_menu']) || !empty($variables['site_search_box'])) {
    $variables['classes_array'][] = 'uom-feature';
  }
  if (!empty($variables['backstretch'])) {
    $variables['classes_array'][] = 'uom-backstretch';
  }
  // Why commented out?
  // $variables['wrapper_classes'] = ' ' . implode(' ', $variables['classes_array']);

  // Remove extra stuff
  $tmp_array = array_diff($variables['classes_array'], array('page', 'contextual-links-region'));
  $variables['wrapper_classes'] = ' ' . implode(' ', $tmp_array);

  // Optionally replace site name with an image.
  $title_image = theme_get_setting('title_image_path');
  if (!empty($title_image)) {
    $variables['title_image'] = file_create_url($title_image);
  }
  else {
    $variables['title_image'] = FALSE;
  }
  
  // Remove "No front page content has been created yet. Add new content" on front page.
	unset($variables['page']['content']['system_main']['default_message']);
}

/**
 * Implements hook_preprocess_node().
 */
function unimelb_preprocess_node(&$variables) {
  if ($variables['node']->type == 'study_area') {
		$default_width = 460;
		if(empty($variables['content']['field_shared_video'][0]['file']['#options']['width'])) {
			$width = $default_width; 
		}
		else {
			$width = $variables['content']['field_shared_video'][0]['file']['#options']['width']; 
		}

    // Do a naughty thing, resize the video from whatevs to 460px wide.
    $height = round($variables['content']['field_shared_video'][0]['file']['#options']['height'] * (460 / $width));
    $variables['content']['field_shared_video'][0]['file']['#options']['width'] = $default_width;
    $variables['content']['field_shared_video'][0]['file']['#options']['height'] = $height;

    // Pull in the node sidebar.
    $variables['sidebar'] = block_get_blocks_by_region('node_right');
  }
}

/**
 * Implements theme_date_display_range()
 *
 * Returns HTML for a date element formatted as a range. Override for
 * the theme function in date.module to output according to the UoM
 * style guide.
 */
function unimelb_date_display_range($variables) {
  $date1 = $variables['date1'];
  $date2 = $variables['date2'];
  $timezone = $variables['timezone'];
  $attributes_start = $variables['attributes_start'];
  $attributes_end = $variables['attributes_end'];

  // Wrap the result with the attributes.
  return t('!start-date–!end-date', array(
    '!start-date' => '<span class="date-display-start"' . drupal_attributes($attributes_start) . '>' . $date1 . '</span>',
    '!end-date' => '<span class="date-display-end"' . drupal_attributes($attributes_end) . '>' . $date2 . $timezone . '</span>',
  ));
}

/*
 * Implements theme_colorbox_imagefield().
 *
 * @param $variables
 *   An associative array containing:
 *   - image: image item as array.
 *   - path: The path of the image that should be displayed in the Colorbox.
 *   - title: The title text that will be used as a caption in the Colorbox.
 *   - gid: Gallery id for Colorbox image grouping.
 */
function unimelb_colorbox_imagefield($variables) {
  $class = array('colorbox', $variables['gid']);

  if ($variables['image']['style_name'] == 'hide') {
    $image = '';
    $class[] = 'js-hide';
  }
  elseif (!empty($variables['image']['style_name'])) {
    $image = theme('image_style', $variables['image']);
  }
  else {
    $image = theme('image', $variables['image']);
  }

  $options = array(
    'html' => TRUE,
    'attributes' => array(
      'title' => $variables['title'],
      'class' => implode(' ', $class),
      'rel' => $variables['gid'],
    )
  );

  return l($image, $variables['path'], $options);
}

/**
 * Implements hook_preprocess_views_view_grid().
 *
 * Our own implementation removes the complete.css grid class names from
 * the views grid and uses different ones instead.
 *
 * Specifically: col-N => view-col-N
 */
function unimelb_preprocess_views_view_grid(&$vars) {
  $columns = isset($vars['options']['columns']) ? $vars['options']['columns'] : $vars['view']->style_options['columns'];
  $replace = array();
  for ($i = 1; $i <= $columns; $i++) {
    $replace['col-' . $i . ' '] = 'view-col-' . $i . ' ';
    $replace['col-' . $i] = 'view-col-' . $i;
  }

  foreach ($vars['column_classes'] as &$row) {
    foreach ($row as $column => &$classes) {
      $classes = strtr($classes, $replace);
    }
  }
}

/**
 * Helper to populate template vars from theme settings.
 *
 * @return
 *   A keyed array to be merged into $variables.
 *
 * Used by the html and page preprocess functions.
 */
function _unimelb_meta_info() {
  $variables = array();

  $variables['unimelb_meta_parent_org'] = theme_get_setting("unimelb_settings_parent-org");
  $variables['unimelb_meta_parent_org_url'] = theme_get_setting("unimelb_settings_parent-org-url");

  $variables['unimelb_ht_right'] = theme_get_setting('unimelb_settings_ht-right');
  $variables['unimelb_ht_left'] = theme_get_setting('unimelb_settings_ht-left');

  $variables['unimelb_ad_line1'] = theme_get_setting('unimelb_settings_ad-line1');
  $variables['unimelb_ad_line2'] = theme_get_setting('unimelb_settings_ad-line2');
  $variables['unimelb_ad_sub'] = theme_get_setting('unimelb_settings_ad-sub');
  $variables['unimelb_ad_postcode'] = theme_get_setting('unimelb_settings_ad-postcode');
  $variables['unimelb_ad_state'] = theme_get_setting('unimelb_settings_ad-state');
  $variables['unimelb_ad_country'] = theme_get_setting('unimelb_settings_ad-country');

  $variables['unimelb_meta_email'] = theme_get_setting("unimelb_settings_ad-email");
  $variables['unimelb_meta_phone'] = theme_get_setting("unimelb_settings_ad-phone");
  $variables['unimelb_meta_fax'] = theme_get_setting("unimelb_settings_ad-fax");

  $variables['unimelb_meta_facebook'] = theme_get_setting("unimelb_settings_fb-url");
  $variables['unimelb_meta_twitter'] = theme_get_setting("unimelb_settings_tw-url");

  $variables['unimelb_meta_auth_name'] = theme_get_setting("unimelb_settings_auth-name");
  $variables['unimelb_meta_maint_name'] = theme_get_setting("unimelb_settings_maint-name");

  $variables['unimelb_meta_date_created'] = theme_get_setting("unimelb_settings_date-created");

  return $variables;
}

/**
 * Implements phptemplate_image_widget()
 */
function unimelb_image_widget($variables) {
  $element = $variables['element'];
  if ($element['#field_name'] != 'field_account_image') {
    return theme_image_widget($variables);
  }

  if ($element['fid']['#value'] != 0) {
    $element['filename']['#markup'] .= ' <span class="file-size">(' . format_size($element['#file']->filesize) . ')</span> ';
  }
  $element['filename']['#weight'] = 100;

  $output = '';
  $output .= '<div class="image-widget form-managed-file clearfix">';

  if (isset($element['preview'])) {
    $output .= '<div class="image-preview">';
    $output .= drupal_render($element['preview']);
    $output .= '</div>';
  }

  hide($element['filename']);
  $output .= '<div class="image-widget-data">';
  $output .= drupal_render_children($element);
  $output .= '</div>';
  $output .= '</div>';
  $output .= '<div class="image-filename">';
  show($element['filename']);
  $output .= drupal_render($element['filename']);
  $output .= '</div>';

  return $output;
}

/**
 * Implements phptemplate_jquerymenu_links().
 *
 * Override the theme function for a jQuerymenu entry. Used to override menu
 * item status for specific URLs.
 */
function unimelb_jquerymenu_links($variables) {

  // Open the Scholarships item if we'er looking at a node.
  if ($variables['title'] == 'Scholarships') {
    $object = menu_get_object();
    // Check that we're looking at an award node.
    if (!empty($object->nid) && $object->type == 'award') {
      $variables['state'] = 'open';
      if (($idx = array_search('closed', $variables['classes'])) !== FALSE) {
        unset($variables['classes'][$idx]);
      }
      $variables['classes'][] = 'open';
    }
  }

  // create values from the parameter array
  $title        = $variables["title"];
  $path         = $variables["path"];
  $options      = $variables["options"];
  $state        = $variables["state"];
  $classes      = $variables["classes"];
  $has_children = $variables["has_children"];
  $edit_path    = $variables["edit_path"];
  $edit_text    = $variables["edit_text"];
  $edit_access  = $variables["edit_access"];

  $parentlink = variable_get('jquerymenu_parentlink', 0);
  $output = '';

  // This is the span that becomes the little plus and minus symbol.
  $plus = '<span' . (empty($classes) ? '>' : ' class="' . implode(' ', $classes) . '">') . '</span>';
  $link = l($title, $path, $options);
  if ($edit_path != NULL && user_access($edit_access)) {
    $edit_box = jquerymenu_edit_box($edit_path, $edit_text);
    if ($has_children != 0) {
      $output .= $parentlink ? $edit_box . $plus . $title : $edit_box . $plus . $link;
    }
    else {
      $output .= $edit_box . $link;
    }
  }
  else {
    if ($has_children != 0) {
      $output .= $parentlink ? $plus . $title : $plus . $link;
    }
    else {
      $output .= $link;
    }
  }
  return $output;
}

/**
 * Custom search form.
 */
function unimelb_form_search_block_form_alter(&$form, &$form_state) {
	global $base_url;
	global $conf;

	if(isset($conf['possible_intranet_url'])) {
		if(in_array($base_url, $conf['possible_intranet_url'])) {
			$form['search_block_form']['#title'] = ''; // Set a default value for the textfield
	
    	// Gary
    	$form['search_block_form']['#default_value'] = t(''); // Set a default value for the textfield

    	$form['actions']['submit']['#value'] = t('Search');

    	// Add extra attributes to the text box
    	$form['search_block_form']['#attributes']['onblur'] = "if (this.value == '') {this.value = '';}";
    	$form['search_block_form']['#attributes']['onfocus'] = "if (this.value == 'Search') {this.value = '';}";
    	// Prevent user from searching the default text
    	$form['#attributes']['onsubmit'] = "if(this.search_block_form.value=='Search'){ alert('Please enter a search'); return false; }";

    	// Alternative (HTML5) placeholder attribute instead of using the javascript
    	$form['search_block_form']['#attributes']['placeholder'] = t('');
		}
	}
	else {

  	$form['search_block_form']['#title'] = ''; // Set a default value for the textfield

		// Gary
 		$form['search_block_form']['#default_value'] = t('Search'); // Set a default value for the textfield

  	$form['actions']['submit']['#value'] = t('Go');

  	// Add extra attributes to the text box
  	$form['search_block_form']['#attributes']['onblur'] = "if (this.value == '') {this.value = 'Search';}";
  	$form['search_block_form']['#attributes']['onfocus'] = "if (this.value == 'Search') {this.value = '';}";
  	// Prevent user from searching the default text
  	$form['#attributes']['onsubmit'] = "if(this.search_block_form.value=='Search'){ alert('Please enter a search'); return false; }";

  	// Alternative (HTML5) placeholder attribute instead of using the javascript
  	$form['search_block_form']['#attributes']['placeholder'] = t('Search');
	}
}

/**
 * Implements hook_preprocess_shoutbox_post().
 *
 * Change the text format from plain to filtered, so links works.
 */
function unimelb_preprocess_shoutbox_post(&$vars) {
	$shout = $vars['shout']->shout;
  $shout = preg_replace('/^\s+|\n|\r|\s+$/m', '', $shout); // Remove line break + spaces at both ends

  $vars['shout']->shout = check_markup($shout, 'filtered_html');	
}

/**
 * Theme the shoutbox block message regarding auto-update interval
 *
 * @param $interval
 *     The number of seconds the shouts auto-refresh
 */
function unimelb_shoutbox_interval_message($variables) {
  // Check if autoupdate is enabled
  $interval = $variables['interval'];
  if ($interval) {
    return '<div class="shoutbox-interval-msg">' . t('Announcements refresh every !interval', array('!interval' => format_interval($interval)));
  }
}

/**
 * Theme the link on the bottom of the block pointing to the shout page
 *
 * @param $page_path
 *     Path to the shout page
 */
function unimelb_shoutbox_block_page_link($variables) {
  return '';
}


/**
 * Helper to replace tags in page title with spaces.
 *
 * This is the last function in this file because the ?> tag in the regex
 * upsets the syntax hilighter.
 *
 * @param $text
 *   A string.
 *
 * @return
 *   A string without HTML tags.
 */
function _unimelb_space_tags($text) {
  // May contain encoded entities from drupal_get_title().
  $text = html_entity_decode($text);
  $text = preg_replace('/<[^>]*?>/', ' ', $text);
  //return check_plain($text);
  return $text; 
}
