<?php
/**
 * @file
 * Layout-specific regions!
 *
 * 4    - Four responsive in the main content, no navigation.
 * 3-1  - Three responsive in the main content, one fixed in navigation.
 * 2-1  - Two responsive in the main content, one fixed in navigation.
 * 4-2  - Four plus two responsive in the main content, no navigation.
 * 4-4  - Four plus four responsive in the main content, no navigation.
 * 8-4  - Eight plus four responsive in the main content, no navigation.
 * 6-1  - Six plus three responsive in the main content, one fixed in navigation.
 * node - Use the node-specific template only. This layout should really only
 *        ever be chosen via code in template.php.
 */
?>

<?php print render($page['content']); ?>
