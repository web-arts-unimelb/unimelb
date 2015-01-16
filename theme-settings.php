<?php
/**
 * Implementation of THEMEHOOK_settings() function.
 *
 * @param $saved_settings
 *   array An array of saved settings for this theme.
 * @return
 *   array A form array.
 */
function unimelb_form_system_theme_settings_alter(&$form, $form_state) {

  // Flatten form.
  $form['#tree'] = FALSE;

  $form['unimelb'] = array(
    '#type' => 'fieldset',
    '#title' => t('UniMelb Settings'),
    '#description' => t('Settings specific to the University of Melbourne theme.'),
  );

  // Images.
  $form['unimelb']['img'] = array(
    '#type' => 'fieldset',
    '#title' => t('Images'),
    '#description' => t("Upload title, background and page header images. Note that if a title image is set, the site name is not displayed in text."),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  // Images.
  $form['unimelb']['img']['title_image_path'] = array(
    '#type' => 'textfield',
    '#title' => t('Path to title image'),
    '#default_value' => theme_get_setting('title_image_path'),
  );
  // Upload a front page background image.
  $form['unimelb']['img']['title_image_upload'] = array(
    '#type' => 'file',
    '#title' => t('Upload title image'),
  );

  // Front page background.
  $form['unimelb']['img']['background_front_path'] = array(
    '#type' => 'textfield',
    '#title' => t('Path to home page background image'),
    '#default_value' => theme_get_setting('background_front_path'),
  );
  // Upload a front page background image.
  $form['unimelb']['img']['background_front_upload'] = array(
    '#type' => 'file',
    '#title' => t('Upload front page background image'),
  );

  // Sub-page background.
  $form['unimelb']['img']['background_secondary_path'] = array(
    '#type' => 'textfield',
    '#title' => t('Path to sub-page background image'),
    '#default_value' => theme_get_setting('background_secondary_path'),
  );
  // Upload a sub-page background image.
  $form['unimelb']['img']['background_secondary_upload'] = array(
    '#type' => 'file',
    '#title' => t('Upload sub-page background image'),
  );

  $form['unimelb']['img']['backstretch'] = array(
    '#type' => 'checkbox',
    '#title' => t('Stretch background image'),
    '#description' => t('Scale the background image with the page'),
    '#default_value' => theme_get_setting('backstretch'),
    '#required' => false,
  );

  // Validate handler to save images to disk.
  $form['#validate'][] = '_unimelb_settings_img_validate';

  $version = theme_get_setting('unimelb_settings_template');
  // Create the settings form.
  $form['unimelb']['unimelb_settings_template'] = array(
    '#type' => 'select',
    '#title' => t('Web Templates Version'),
    '#description' => t('Choose the version of the MARCOM web templates you want to use.'),
    '#options' => array(
      '1-1-0' => t('Version 1.1.0'),
      '1-2-0-ALPHA' => t('Version 1.2.0 alpha'),
    ),
    '#default_value' => empty($version) ? '1-1-0' : $version,
  );

  $columns = theme_get_setting('unimelb_settings_columns');
  if (empty($columns)) {
    $columns = '3-1';
  }
  $form['unimelb']['unimelb_settings_columns'] = array(
    '#type' => 'select',
    '#title' => t('Column Grid'),
    '#description' => t('Choose the column layout for the front page.'),
    '#options' => array(
      '2' => t('2 - Two responsive in the main content, no navigation.'),
      '2-1' => t('2-2-2-2+1 - Two responsive in the main content, one fixed in navigation.'),
			'2-1-12' => t('2-2-2-2+1 12 blocks - There are 12 blocks. Two responsive in the main content, one fixed in navigation.'),
			'1-2-1-12' => t('1+2-2-2-2+1 - similar to 2-1-12, but with a block on top'),
      '3-1' => t('3-3-3-3+1 - Three responsive in the main content, one fixed in navigation.'),
      '6-1' => t('6-3-3+1 - Six plus three responsive in the main content, one fixed in navigation.'),
      '4-2' => t('4-2-4-2 - Four plus two responsive in the main content, no navigation.'),
      '4-4' => t('4-4-4-4 - Four plus four responsive in the main content, no navigation.'),
      '8-4' => t('8-4 - Eight plus four responsive in the main content, no navigation.'),
    ),
    '#default_value' => $columns,
    '#required' => TRUE,
  );

  $form['unimelb']['unimelb_settings_custom_logo'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use Custom Logo'),
    '#description' => t('If you do not want to use the logo provider by the brand.unimelb.edu.au injector, check this box. You will need to use your theme\'s logo or include some custom CSS.'),
    '#default_value' => theme_get_setting('unimelb_settings_custom_logo'),
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_site_search_box'] = array(
    '#type' => 'checkbox',
    '#title' => t('Site search box'),
    '#description' => t('Display a site search box'),
    '#default_value' => theme_get_setting('unimelb_settings_site_search_box'),
    '#required' => false,
  );

	$form['unimelb']['unimelb_settings_dropdown_menu_and_search_box'] = array(
    '#type' => 'checkbox',
    '#title' => t('Dropdown menu and search box'),
    '#description' => t('Display dropdown menu and search box'),
    '#default_value' => theme_get_setting('unimelb_settings_dropdown_menu_and_search_box'),
    '#required' => false,
  );

	$form['unimelb']['viewport_initial_scale'] = array(
    '#type' => 'textfield',
    '#title' => t('Viewport initial scale'),
    '#description' => t('Viewport initial scale'),
    '#size' => 20,
    '#maxlength' => 10,
		'#default_value' => theme_get_setting('viewport_initial_scale'),
    '#required' => FALSE,
  );


  $form['unimelb']['unimelb_settings_parent-org'] = array(
    '#type' => 'textfield',
    '#title' => t('Parent organisational unit (optional)'),
    '#description' => t('The parent organisational unit appears on the home page above the site name'),
    '#default_value' => theme_get_setting('unimelb_settings_parent-org'),
    '#size' => 60,
    '#maxlength' => 256,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_parent-org-url'] = array(
    '#type' => 'textfield',
    '#title' => t('Parent organisational unit URL (optional)'),
    '#description' => t('eg. http://www.unimelb.edu.au'),
    '#default_value' => theme_get_setting('unimelb_settings_parent-org-url'),
    '#size' => 60,
    '#maxlength' => 256,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_ht-left'] = array(
    '#type' => 'textfield',
    '#title' => t('Headingtext left (optional)'),
    '#description' => t('The headingtext appears on the home page below the site name. The left portion is a very short phrase providing context for the right portion, eg. "Who we are"'),
    '#default_value' => theme_get_setting('unimelb_settings_ht-left'),
    '#size' => 40,
    '#maxlength' => 256,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_ht-right'] = array(
    '#type' => 'textfield',
    '#title' => t('Headingtext right (optional)'),
    '#description' => t('The right portion of the headingtext is a succinct statement describing the web site'),
    '#default_value' => theme_get_setting('unimelb_settings_ht-right'),
    '#size' => 120,
    '#maxlength' => 1024,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_meta-keywords'] = array(
    '#type' => 'textfield',
    '#title' => t('Meta keywords (optional)'),
    '#description' => t('Comma-separated list of keywords for use in meta tags'),
    '#default_value' => theme_get_setting('unimelb_settings_meta-keywords'),
    '#size' => 120,
    '#maxlength' => 1024,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_ad-line1'] = array(
    '#type' => 'textfield',
    '#title' => t('Address Line 1 (optional)'),
    '#description' => t('eg. Level 1, Raymond Priestly Building'),
    '#default_value' => theme_get_setting('unimelb_settings_ad-line1'),
    '#size' => 60,
    '#maxlength' => 512,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_ad-line2'] = array(
    '#type' => 'textfield',
    '#title' => t('Address Line 2 (optional)'),
    '#description' => t('eg. University of Melbourne'),
    '#default_value' => theme_get_setting('unimelb_settings_ad-line2'),
    '#size' => 60,
    '#maxlength' => 512,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_ad-sub'] = array(
    '#type' => 'textfield',
    '#title' => t('City or suburb (optional)'),
    '#description' => t('eg. Parkville'),
    '#default_value' => theme_get_setting('unimelb_settings_ad-sub'),
    '#size' => 40,
    '#maxlength' => 512,
    '#required' => FALSE,
  ); 

  $form['unimelb']['unimelb_settings_ad-postcode'] = array(
    '#type' => 'textfield',
    '#title' => t('Postcode (optional)'),
    '#description' => t('eg. 3010'),
    '#default_value' => theme_get_setting('unimelb_settings_ad-postcode'),
    '#size' => 20,
    '#maxlength' => 10,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_ad-state'] = array(
    '#type' => 'textfield',
    '#title' => t('State or territory (optional)'),
    '#description' => t('eg. VIC'),
    '#default_value' => theme_get_setting('unimelb_settings_ad-state'),
    '#size' => 20,
    '#maxlength' => 64,
    '#required' => FALSE,
  ); 

  $form['unimelb']['unimelb_settings_ad-country'] = array(
    '#type' => 'textfield',
    '#title' => t('Country (optional)'),
    '#description' => t('eg. Australia'),
    '#default_value' => theme_get_setting('unimelb_settings_ad-country'),
    '#size' => 20,
    '#maxlength' => 64,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_ad-phone'] = array(
    '#type' => 'textfield',
    '#title' => t('Phone (required)'),
    '#description' => t('eg. +61 3 8344 1670'),
    '#default_value' => theme_get_setting('unimelb_settings_ad-phone'),
    '#size' => 20,
    '#maxlength' => 64,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_ad-fax'] = array(
    '#type' => 'textfield',
    '#title' => t('Fax (optional)'),
    '#description' => t('eg. +61 3 8344 1670'),
    '#default_value' => theme_get_setting('unimelb_settings_ad-fax'),
    '#size' => 20,
    '#maxlength' => 64,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_ad-email'] = array(
    '#type' => 'textfield',
    '#title' => t('Email (required)'),
    '#description' => t('Email address for general enquiries, eg. example@unimelb.edu.au'),
    '#default_value' => theme_get_setting('unimelb_settings_ad-email'),
    '#size' => 40,
    '#maxlength' => 256,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_fb-url'] = array(
    '#type' => 'textfield',
    '#title' => t('Facebook URL (optional)'),
    '#description' => t('eg. http://www.facebook.com/melbuni'),
    '#default_value' => theme_get_setting('unimelb_settings_fb-url'),
    '#size' => 60,
    '#maxlength' => 1024,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_tw-url'] = array(
    '#type' => 'textfield',
    '#title' => t('Twitter (optional)'),
    '#description' => t('eg. http://www.facebook.com/unimelb'),
    '#default_value' => theme_get_setting('unimelb_settings_tw-url'),
    '#size' => 60,
    '#maxlength' => 1024,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_wordpress-url'] = array(
    '#type' => 'textfield',
    '#title' => t('Wordpress (optional)'),
    '#description' => t('eg. Your wordpress url'),
    '#default_value' => theme_get_setting('unimelb_settings_wordpress-url'),
    '#size' => 60,
    '#maxlength' => 1024,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_auth-name'] = array(
    '#type' => 'textfield',
    '#title' => t('Authoriser Name and or Position (required)'),
    '#description' => t('eg. Jane Smith, Faculty of Bar'),
    '#default_value' => theme_get_setting('unimelb_settings_auth-name'),
    '#size' => 80,
    '#maxlength' => 256,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_maint-name'] = array(
    '#type' => 'textfield',
    '#title' => t('Maintainer Name and or Position (required)'),
    '#description' => t('eg. Pat Doe, Faculty of Bar'),
    '#default_value' => theme_get_setting('unimelb_settings_maint-name'),
    '#size' => 80,
    '#maxlength' => 256,
    '#required' => FALSE,
  );

  $form['unimelb']['unimelb_settings_date-created'] = array(
    '#type' => 'textfield',
    '#title' => t('Date created (optional)'),
    '#description' => t('The date the site was launched, eg. 11 February 2010'),
    '#default_value' => theme_get_setting('unimelb_settings_date-created'),
    '#size' => 20,
    '#maxlength' => 256,
    '#required' => FALSE,
  );

  $form['unimelb']['debug'] = array(
    '#type' => 'fieldset',
    '#title' => t('Debug'),
    '#description' => t('Set options for remote debugging. This is mainly useful for iOS and Android browser problems. You need to be running a <a href="http://people.apache.org/~pmuellr/weinre/docs/latest/Home.html">weinre</a> debug server.'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['unimelb']['debug']['debug'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable remote debug'),
    '#description' => t('Enable or disable remote debugging via <em>weinre</em>.'),
    '#default_value' => theme_get_setting('debug'),
  );

  $form['unimelb']['debug']['debug_host'] = array(
    '#type' => 'textfield',
    '#title' => t('Debug host'),
    '#description' => t('Debug host name or IP address.'),
    '#default_value' => theme_get_setting('debug_host'),
  );

  $form['unimelb']['debug']['debug_port'] = array(
    '#type' => 'textfield',
    '#title' => t('Debug port'),
    '#size' => 6,
    '#maxlength' => 6,
    '#description' => t('Debug port.'),
    '#default_value' => theme_get_setting('debug_port'),
  );

  return $form;
}

/**
 * Validate handler to process uploaded background images.
 */
function _unimelb_settings_img_validate($form, &$form_state) {
  $file = file_save_upload('title_image_upload');
  if (isset($file)) {
    if ($file) {
      // Put the temporary file in form_values so we can save it on submit.
      $form_state['values']['title_image_upload'] = $file;
    }
    else {
      // File upload failed.
      form_set_error('title_image_upload', t('The title image could not be uploaded.'));
    }
  }

  $file = file_save_upload('background_front_upload');
  if (isset($file)) {
    if ($file) {
      // Put the temporary file in form_values so we can save it on submit.
      $form_state['values']['background_front_upload'] = $file;
    }
    else {
      // File upload failed.
      form_set_error('background_front_upload', t('The front page background image could not be uploaded.'));
    }
  }

  $file = file_save_upload('background_secondary_upload');
  if (isset($file)) {
    // File upload was attempted.
    if ($file) {
      // Put the temporary file in form_values so we can save it on submit.
      $form_state['values']['background_secondary_upload'] = $file;
    }
    else {
      // File upload failed.
      form_set_error('background_secondary_upload', t('The sub-page background image could not be uploaded.'));
    }
  }

  $values = $form_state['values'];

  // Check for a new uploaded title_image, and use that if available.
  if ($file = $values['title_image_upload']) {
    unset($values['title_image_upload']);
    $filename = file_unmanaged_copy($file->uri);
    $values['title_image_path'] = $filename;
    form_set_value($form['unimelb']['img']['title_image_path'], $values['title_image_path'], $form_state);
  }

  // Check for a new uploaded header_image_front, and use that if available.
  if ($file = $values['background_front_upload']) {
    unset($values['background_front_upload']);
    $filename = file_unmanaged_copy($file->uri);
    $values['background_front_path'] = $filename;
    form_set_value($form['unimelb']['img']['background_front_path'], $values['background_front_path'], $form_state);
  }

  // Check for a new uploaded header_image_secondary, and use that if available.
  if ($file = $values['background_secondary_upload']) {
    unset($values['background_secondary_upload']);
    $filename = file_unmanaged_copy($file->uri);
    $values['background_secondary_path'] = $filename;
    form_set_value($form['unimelb']['img']['background_secondary_path'], $values['background_secondary_path'], $form_state);
  }
}
