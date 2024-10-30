<?php
/* Uninstall File IT Popup */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
/* Deleting Options */
delete_option('MintNFT_option_name');

?>