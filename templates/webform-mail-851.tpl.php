<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

/**
 * @file
 * Customize the e-mails sent by Webform after successful submission.
 *
 * This file may be renamed "webform-mail-[nid].tpl.php" to target a
 * specific webform e-mail on your site. Or you can leave it
 * "webform-mail.tpl.php" to affect all webform e-mails on your site.
 *
 * Available variables:
 * - $node: The node object for this webform.
 * - $submission: The webform submission.
 * - $email: The entire e-mail configuration settings.
 * - $user: The current user submitting the form.
 * - $ip_address: The IP address of the user submitting the form.
 *
 * The $email['email'] variable can be used to send different e-mails to different users
 * when using the "default" e-mail template.
 */
?>
<?php print ($email['html'] ? '<p>' : '') . t('Submitted on [submission:date:long]'). ($email['html'] ? '</p>' : ''); ?>

<?php if ($user->uid): ?>
<?php print ($email['html'] ? '<p>' : '') . t('Submitted by user: [submission:user]') . ($email['html'] ? '</p>' : ''); ?>
<?php else: ?>
<?php print ($email['html'] ? '<p>' : '') . t('Submitted by anonymous user: [submission:ip-address]') . ($email['html'] ? '</p>' : ''); ?>
<?php endif; ?>

<?php print ($email['html'] ? '<p>' : '') . t('Submitted values are') . ':' . ($email['html'] ? '</p>' : ''); ?>

[submission:values]

<?php print _output_subject($array_index = 21, $submission); ?>

<?php print ($email['html'] ? '<p>' : '') . t('The results of this submission may be viewed at:') . ($email['html'] ? '</p>' : '') ?>

<?php print ($email['html'] ? '<p>' : ''); ?>[submission:url]<?php print ($email['html'] ? '</p>' : ''); ?>


<?php
function _output_subject($array_index = null, $submission = null) {
	$output = "";	
	$index = 1;

	if(isset($submission->data[$array_index]['rows']))
	{
		$output = "<p>Subject(s) you wish to study<br/>";
		foreach($submission->data[$array_index]['rows'] as $row)
		{
			$output .= "<p>Subject code $index:". $row['subject_code']. "<br/>";
			$output .= "Subject name $index:". $row['subject_name']. "<br/>";
			$output .= "Subject name $index:". $row['semester']. "</p>";
			++$index;
		}

		$output .= "</p>";
	}
	else
	{
		die("no data");
	}

	return $output;
}


?>
