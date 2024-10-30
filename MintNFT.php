<?php
/**
 * Plugin Name: MintNFT
 * Plugin URI: http://techforceglobal.com/
 * Description: This is a plugin for minting NFTs.
 * Version: 1.1.0
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Techforceglobal
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: MintNFT
 */ 

/*
MintNFT is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
MintNFT is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with MintNFT. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/
 
if (!defined('WPINC')) {
    die;
}
define( 'externalURL', 'https://mintnft.techforce.global' );
/* Adding Admin Submenu Page */
 

add_action('admin_menu', 'MintNFT_add_menu_page');
function MintNFT_add_menu_page()
{
	add_menu_page("Mint NFT", "Mint NFT","manage_options", "mintnft", "MintNFT_submenu_callback_function",'');
	add_submenu_page("mintnft","Activation", "Activation","manage_options", "activation", "activeKey_callback_function");
	add_submenu_page("mintnft","Mint NFT", "Contract Deployment","manage_options", "contractdeployment", "contract_deployment",'');
	add_submenu_page("mintnft","Meta Generation", "Meta Generation","manage_options", "meta-generation", "uploadfile");
	 
}
function activeKey_callback_function()
{
  include "activation.php";
       

} 

function contract_deployment(){

	include "contract_deployment.php";
}

function uploadfile(){

	include "meta_generation.php";
}

/* Admin Page Callback Function */
function MintNFT_submenu_callback_function(){

	 
	/* Check user capability */
	if ( ! current_user_can( 'manage_options' ) ) {
 		return;
 	}
 	 
	if ( isset( $_GET['settings-updated'] ) ) {
	 	add_settings_error( 'MintNFT_messages', 'MintNFT_message', __( 'Settings Saved Successfully', 'mintNFT' ), 'updated' );
?>
<div id="message" class="updated">
    <p><strong><?php _e('Settings Saved Successfully.') ?></strong></p>
</div>
<?php	
	}

	 
	?>
<div class="wrap">
    <form action="options.php" method="post" class="MintNFT_form">
		<div class="card NFT">

		
        <!-- Display Settings Here -->
        <?php

	   	 // output security fields for the registered setting "mintNFT"
		 settings_fields( 'mintNFT' );
		 
		 // output setting sections and their fields
		 do_settings_sections( 'mintNFT' );
		 
		 // output save settings button
		 submit_button( 'Save Settings' );

   	?>

    </form>
</div><!-- wrap -->
<?php
}

/***** Code to add template in dropdown*******/ 
add_filter( 'theme_page_templates', 'mc_add_page_template_to_dropdown' );

function mc_add_page_template_to_dropdown( $templates )
{
    $templates['templates/minting_page.php'] = __('Minting Page', 'text-domain');
	return $templates;
}
 

add_filter('template_include', 'mc_change_page_template', 99);

function mc_change_page_template($template)
{ 	
	 
	$post_id = get_the_ID();
	$post = get_post($post_id); 
	$slug = $post->post_name;
  

	if (is_page() && $slug=='mint') {
        $meta = get_post_meta(get_the_ID());
        
        if (!empty($meta['_wp_page_template'][0]) && $meta['_wp_page_template'][0] != $template) {
            $template = dirname( __FILE__ ) .'/'.$meta['_wp_page_template'][0]; 
        }
        
    }


    return $template;
}

 

function page_creator1(){
	$page_title = 'Mint';
	if(get_page_by_title($page_title) == NULL){

		$product = array(
		'post_title' => $page_title,
		'post_status'=> 'publish',
		'post_type' => 'page'
		);

		$insert_page = wp_insert_post($product);
  	}
 }

register_activation_hook(__FILE__, 'page_creator1');
 

/**
 * Init setting section, Init setting field and register settings page
 *
 * @since 1.0
 */

/* Register Settings with Section */
add_action( 'admin_init', 'MintNFT_init_settings' );
 

function MintNFT_init_settings(){
	register_setting( 'mintNFT', 'MintNFT_option_name' );

	// register a new section in the "wporg" page
	 add_settings_section(
		 'MintNFT_section_developers',
		 __( 'Mint NFT Settings', 'mintNFT' ), 
		 'MintNFT_section_developers_function',
		 'mintNFT'
	 );
 
 
	 
/**Server Type Field **/
	add_settings_field(
		'MintNFT_ServerType_field',
		__( 'Server Type', 'mintNFT' ),
		'MintNFT_ServerType_field_callback_function',
		'mintNFT',
		'MintNFT_section_developers',
		[
		'label_for' => 'MintNFT_ServerType_field',
		'class' => 'MintNFT_row_ServerType',
		]
		);

/**Pinata key Field **/
	add_settings_field(
		'MintNFT_PinataKey_field',
		__( 'Pinata key', 'mintNFT' ),
		'MintNFT_PinataKey_field_callback_function',
		'mintNFT',
		'MintNFT_section_developers',
		[
		'label_for' => 'MintNFT_PinataKey_field_field',
		'class' => 'MintNFT_row_PinataKey pinata_data',
		]
		);

/**Pinata Secret Field **/
		add_settings_field(
			'MintNFT_PinataSecret_field',
			__( 'Pinata Secret', 'mintNFT' ),
			'MintNFT_PinataSecret_field_callback_function',
			'mintNFT',
			'MintNFT_section_developers',
			[
			'label_for' => 'MintNFT_PinataSecret_field_field',
			'class' => 'MintNFT_row_PinataSecret pinata_data',
			]
			);
		

/**Gateway Type Field **/
add_settings_field(
	'MintNFT_getway_type_field',
	__( 'Gateway Type', 'mintNFT' ),
	'MintNFT_getway_type_field_callback_function',
	'mintNFT',
	'MintNFT_section_developers',
	[
	'label_for' => 'MintNFT_getway_type_field',
	'class' => 'MintNFT_row_getway_type pinata_data',
	]
	);
/**Image Prefix Field **/
	add_settings_field(
		'MintNFT_image_prefix_field',
		__( 'Image Prefix', 'mintNFT' ),
		'MintNFT_image_prefix_field_callback_function',
		'mintNFT',
		'MintNFT_section_developers',
		[
		'label_for' => 'MintNFT_image_prefix_field',
		'class' => 'MintNFT_row_image_prefix goerliField',
		]
		);
		/**Short Desc Field **/
		add_settings_field(
			'MintNFT_metadata_short_desc_field',
			__( 'Metadata Desc', 'mintNFT' ),
			'MintNFT_metadata_short_desc_field_callback_function',
			'mintNFT',
			'MintNFT_section_developers',
			[
			'label_for' => 'MintNFT_metadata_short_desc_field',
			'class' => 'MintNFT_row_metadata_short_desc goerliField',
			]
			);

			
		
		/** Page Heading Field **/
		add_settings_field(
			'MintNFT_heading_field', 
			__( 'Heading', 'mintNFT' ),  
			'MintNFT_heading_field_callback_function',
			'mintNFT',
			'MintNFT_section_developers',
			[
			'label_for' => 'MintNFT_heading_field',
			'class' => 'MintNFT_row_heading goerliField',
			]
		);

	  /** Contract Mint Description Field **/
	 add_settings_field(
		 'MintNFT_mintdesc_field', 
		 __( 'Mint description', 'mintNFT' ),  
		 'MintNFT_mintdesc_field_callback_function',
		 'mintNFT',
		 'MintNFT_section_developers',
		 [
		 'label_for' => 'MintNFT_mintdesc_field',
		 'class' => 'MintNFT_row_mintdesc goerliField',
		 ]
	 );
 
 
	  

 /* Background image Upload Field */
	 add_settings_field(
		 'MintNFT_bgimage_field', 
		 __( 'Upload Background image', 'mintNFT' ),  
		 'MintNFT_bgimage_field_callback_function',
		 'mintNFT',
		 'MintNFT_section_developers',
		 [
		 'label_for' => 'MintNFT_bgimage_field',
		 'class' => 'MintNFT_row_image goerliField',
		 ]
	 );
  

	 /* Logo Image Upload Field */
	 add_settings_field(
		'MintNFT_logoimage_field', 
		__( 'Upload Logo', 'mintNFT' ),  
		'MintNFT_logoimage_field_callback_function',
		'mintNFT',
		'MintNFT_section_developers',
		[
		'label_for' => 'MintNFT_logoimage_field',
		'class' => 'MintNFT_row_image goerliField',
		]
	);


}




/* Settings Section Callback function */
function MintNFT_section_developers_function( $args ){
?>
<div class="NFT-Metadata-notice-block">
    <strong class="red"><?php _e('Information','textdomain');?></strong>
    <p><?php _e('you can add/edit below fields.','textdomain'); ?>
    </p>
    <p><?php _e('Please double check the changes you make and do not forgot to save the settings, it make result in problems while generating metadata.','textdomain');?>
	</div>

<?php
}



/* Settings Image Prefix Callback function */
function MintNFT_ServerType_field_callback_function( $args ){
	$mc_options = get_option( 'MintNFT_option_name' );
	
	$value = $mc_options[esc_attr($args['label_for'])] ; 
 	$ipfSelected='';
	$pinataSelected='';

	if($value == 'ipfs'){ $ipfSelected="selected";}
	if($value == 'pinata'){ $pinataSelected="selected";}
	$selectNetwork ='<select id="inputServerType" class="regular-text code" name="MintNFT_option_name['.esc_attr($args['label_for']).']">';
	$selectNetwork .='<option value="ipfs" '.$ipfSelected.'>IPFS</option>';
	$selectNetwork .='<option value="pinata" '.$pinataSelected.'>PINATA</option>';
	$selectNetwork .='</select>';
	echo $selectNetwork;

   
	?>
	<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
	<span class="tooltiptext">Please select Server Type</div>
	<p class="description">
		<?php _e('Network Type','mintNFT'); ?>
	</p>
	<?php
	}


	

	
/* Settings MintNFT_PinataKey_field_callback_function */
function MintNFT_PinataKey_field_callback_function( $args ){
	$mc_options = get_option( 'MintNFT_option_name' );
	
	?>
<input id="MintNFT_PinataKey_field" class="regular-text code" type="text"
    name="MintNFT_option_name[<?php echo esc_attr($args['label_for']); ?>]"
    placeholder="<?php _e('PinataKey','mintNFT'); ?>"
    value="<?php echo !empty($mc_options[esc_attr($args['label_for'])]) ?( esc_attr($mc_options[$args['label_for']]) ): '' ;?>">
<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
  <span class="tooltiptext">Please add Pinata Key.</span>
</div>
<p class="description">
    <?php _e('PinataKey','mintNFT'); ?>
</p>
<?php
	}

	
/* Settings MintNFT_PinataSecret_field_callback_function */
function MintNFT_PinataSecret_field_callback_function( $args ){
	$mc_options = get_option( 'MintNFT_option_name' );
	
	?>
<input id="MintNFT_PinataSecret_field" class="regular-text code" type="text"
    name="MintNFT_option_name[<?php echo esc_attr($args['label_for']); ?>]"
    placeholder="<?php _e('PinataSecret','mintNFT'); ?>"
    value="<?php echo !empty($mc_options[esc_attr($args['label_for'])]) ?( esc_attr($mc_options[$args['label_for']]) ): '' ;?>">
<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
  <span class="tooltiptext">Please add Pinata Secret Key.</span>
</div>
<p class="description">
    <?php _e('PinataSecret','mintNFT'); ?>
</p>
<?php
	}


/* Settings Image Prefix Callback function */
function MintNFT_image_prefix_field_callback_function( $args ){
	$mc_options = get_option( 'MintNFT_option_name' );
	
	?>
<input required id="MintNFT_image_prefix_field" class="regular-text code" type="text"
    name="MintNFT_option_name[<?php echo esc_attr($args['label_for']); ?>]"
    placeholder="<?php _e('Image Prefix','mintNFT'); ?>"
    value="<?php echo !empty($mc_options[esc_attr($args['label_for'])]) ?( esc_attr($mc_options[$args['label_for']]) ): '' ;?>">
<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
  <span class="tooltiptext">This is the Image Name prefix of Your NFT Images. And If It will not match
with all of Your Image Name your metadata will not be generated so your NFT Image name prefix must be matched with this. Ex. If your image prefix img your NFT Images name must be
like img1, img2, img3, img4........... imgxx. The same will be applicable if you have metadata generated or already have metadata then the file name must be like img1.json, img2.json...</span>
</div>
<p class="description">
    <?php _e('Image Prefix','mintNFT'); ?>
</p>
<?php
	}


	
/* Settings Getway Type Callback function */
function MintNFT_getway_type_field_callback_function( $args ){
	$mc_options = get_option( 'MintNFT_option_name' );
	
	?>
<input id="MintNFT_getway_type_field" class="regular-text code" type="text"
    name="MintNFT_option_name[<?php echo esc_attr($args['label_for']); ?>]"
    placeholder="<?php _e('Gateway Type','mintNFT'); ?>"
    value="<?php echo !empty($mc_options[esc_attr($args['label_for'])]) ?( esc_attr($mc_options[$args['label_for']]) ): '' ;?>">
<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
    <span class="tooltiptext">It's a Gateway of Your NFT's Metadata to get access/retrieve from the IPFS. default IPFS
        gateway is https://ipfs.io/ipfs/... You can also give your any dedicated gateway.
    </span>
</div>
<p class="description">
    <?php _e('Gateway Type','mintNFT'); ?>
</p>
<?php
	}

	
	
/* Settings Short Description Callback function */
function MintNFT_metadata_short_desc_field_callback_function( $args ){
	$mc_options = get_option( 'MintNFT_option_name' );
	
	?>
<input required id="MintNFT_metadata_short_desc_field" class="regular-text code" type="text"
    name="MintNFT_option_name[<?php echo esc_attr($args['label_for']); ?>]"
    placeholder="<?php _e('Metadata Desc','mintNFT'); ?>"
    value="<?php echo !empty($mc_options[esc_attr($args['label_for'])]) ?( esc_attr($mc_options[$args['label_for']]) ): '' ;?>">
<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
    <span class="tooltiptext">It's description for the Metadata (Please put an example on the Metageneration page that
        how your Metadata will look like) Please check and verify properly once your metadata will be generated and
        using this if anyone will mint the NFT's you can't change it later.
    </span>
</div>
<p class="description">
    <?php _e('Metadata Desc','mintNFT'); ?>
</p>
<?php
	}
 
 
 
/* Settings Contract Address 1 Callback function */
function MintNFT_heading_field_callback_function( $args ){
	$mc_options = get_option( 'MintNFT_option_name' );
	 
?>
<input required id="MintNFT_heading_responsive_field" class="regular-text code" type="text"
    name="MintNFT_option_name[<?php echo esc_attr($args['label_for']); ?>]"
    placeholder="<?php _e('Heading','mintNFT'); ?>"
    value="<?php echo !empty($mc_options[esc_attr($args['label_for'])]) ?( esc_attr($mc_options[$args['label_for']]) ): '' ;?>">

<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
    <span class="tooltiptext">It's heading for Your Mint NFT page. Whatever you will be writing here is directly
        reflected on the Mint NFT page. You can change/update it later also.
    </span>
</div>
<p class="description">
    <?php _e('Write Heading','mintNFT'); ?>
</p>
<?php
}



/* Settings Mint Description Callback function */
function MintNFT_mintdesc_field_callback_function( $args ){
	$mc_options = get_option( 'MintNFT_option_name' );
	 
?>
<textarea required rows="15" cols="90" id="MintNFT_mintdesc_responsive_field"
    name="MintNFT_option_name[<?php echo esc_attr($args['label_for']); ?>]"><?php echo !empty($mc_options[esc_attr($args['label_for'])]) ?( esc_attr($mc_options[$args['label_for']]) ): '' ;?></textarea>

<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
    <span class="tooltiptext">It's the same as the heading It's Mint description for your Mint NFT page. Whatever you
        will be writing here is directly reflected on the Mint NFT page. You can change/update it later also.</span>
</div>
<p class="description">
    <?php _e('Write Mint Description.','mintNFT'); ?>
</p>
<?php
}
  
 
/**Settings Bgimage callback funciton */
function MintNFT_bgimage_field_callback_function( $args ) {
	$mc_options = get_option( 'MintNFT_option_name' );
?><div class="bg_wrapper"><p>
    <input id="MintNFT_bgimage_button" type="button" value="Media Library" class="button-secondary" />
    <input id="MintNFT_bgimage" class="regular-text code" type="text"
        name="MintNFT_option_name[<?php echo esc_attr($args['label_for']); ?>]"
        value="<?php echo !empty($mc_options[esc_attr($args['label_for'])]) ?( esc_attr($mc_options[$args['label_for']]) ):(_e('Select Image','mintNFT')) ;?>">
    </p>
	<div class="bg-tooltip">
<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
  <span class="tooltiptext">This is the background image. Whatever you will upload here will be directly reflected on the Mint NFT page background.</span>
</div>
</div>
</div>
<p class="description"><?php _e('Enter an background image URL or use an image from media library.','mintNFT'); ?></p>

<img id="MintNFT_bgimage_admin_hover_preview" class="<?php //echo esc_attr($mc_options['MintNFT_hover_effect_field']); ?>"
    src="<?php echo esc_attr($mc_options['MintNFT_bgimage_field']); ?>" alt="bgimage" width="250" height="250" />



<?php		
}

/**Settings logoimage callback function */
  
function MintNFT_logoimage_field_callback_function( $args ) {
	$mc_options = get_option( 'MintNFT_option_name' );
?><div class="bg_wrapper"><p>
    <input id="MintNFT_logoimage_button" type="button" value="Media Library" class="button-secondary" />
    <input id="MintNFT_logoimage" class="regular-text code" type="text"
        name="MintNFT_option_name[<?php echo esc_attr($args['label_for']); ?>]"
        value="<?php echo !empty($mc_options[esc_attr($args['label_for'])]) ?( esc_attr($mc_options[$args['label_for']]) ):(_e('Select Slideimage 2','mintNFT')) ;?>">
</p>
<div class="bg-tooltip">
<div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
    <span class="tooltiptext">This will be a logo for your Mint NFT page.</span>
</div>
</div>
</div>
<p class="description"><?php _e('Enter an Logo URL or use an image from media library.','mintNFT'); ?></p>
<img id="MintNFT_logoimage_admin_hover_preview"
    class="<?php //echo esc_attr($mc_options['MintNFT_hover_effect_field']); ?>"
    src="<?php echo esc_attr($mc_options['MintNFT_logoimage_field']); ?>" alt="logoimage" width="200" height="70" />

<?php		
} 


/* Enqueuing Admin Scripts and Styles */
function MintNFT_admin_modal_js()
{
	 
	define( 'MintNFT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'MintNFT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

	 
	// Enqueue the JS & CSS:
	wp_enqueue_media(); // Fixing media library button
	wp_enqueue_style( 'thickbox' );
	
	

	if($_REQUEST['page'] == 'meta-generation'){
	wp_enqueue_script('meta_generation_js', plugins_url( 'assets/js/metaGeneration.js', __FILE__ ), array('jquery'),'1.2','all');
	wp_enqueue_script('meta_generation_js');
	wp_enqueue_script('validate-js', plugins_url( 'assets/js/jquery.validate.min.js', __FILE__ ), array());
	wp_register_script('bootstrap',plugins_url('assets/js/NFT/bootstrap.min.js', __FILE__ ), array('jquery'));
	wp_enqueue_script('bootstrap');
	wp_enqueue_script('jquery');
	}


	if($_REQUEST['page'] == 'activation' ){
	wp_enqueue_style('font', plugins_url('assets/css/fontawesome.css', __FILE__ ), array());	
	wp_enqueue_script('validate-js', plugins_url( 'assets/js/jquery.validate.min.js', __FILE__ ), array());

	wp_enqueue_script('activation_js', plugins_url( 'assets/js/activation.js', __FILE__ ), array('jquery'),'1.2','all');
	wp_enqueue_script('activation_js');

	}
	wp_register_style('font-awesome-min', plugins_url('assets/fonts/sss-font-awesome/css/font-awesome.min.css', __FILE__ ), array());
	wp_enqueue_style('font-awesome-min');
	
	wp_enqueue_style('MintNFT_admin_css', plugins_url( 'assets/css/mintnft-admin.css', __FILE__ ), array(),'1.1', 'all');
	wp_enqueue_style('MintNFT_admin_setting_css', plugins_url( 'assets/css/mintnft-admin-setting.css', __FILE__ ), array(),'1.0', 'all');
	wp_enqueue_script('MintNFT_admin_js', plugins_url( 'assets/js/mintnft-admin.js', __FILE__ ), array('jquery', 'media-upload', 'thickbox'),'1.0', true);
	 
	wp_enqueue_style('alertify_core', plugins_url( 'assets/alertify/alertify.core.css', __FILE__ ), array());
	wp_enqueue_style('alertify_default_css', plugins_url( 'assets/alertify/alertify.default.css', __FILE__ ));
	wp_enqueue_script('alertify_min', plugins_url( 'assets/alertify/alertify.min.js', __FILE__ ), array());

	wp_enqueue_style('bootstrap-min-css', 'https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css', array(),'4.1.3', 'all');
if($_REQUEST['page'] == 'contractdeployment'){
	wp_enqueue_script('connection_js', plugins_url( 'assets/js/contract_deployment.js', __FILE__ ), array('jquery'),'1.2','all');
	wp_enqueue_script('connection_js');
	wp_enqueue_script('jquery-min-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js', array('jquery'));
}
 	//wp_enqueue_script('jquery-min-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js', array('jquery'));
	 
	wp_register_script('web3-min',plugins_url('assets/js/NFT/web3.min.js', __FILE__ ), array('jquery'));
	wp_register_script('index-js',plugins_url('assets/js/NFT/index.js', __FILE__ ), array('jquery'));
	wp_register_script('index-min-js',plugins_url('assets/js/NFT/index.min.js', __FILE__ ), array('jquery'));
	wp_register_script('wallet-index-min-js',plugins_url('assets/js/NFT/wallet_index.min.js', __FILE__ ), array('jquery'));
	wp_register_script('fortmatic-js',plugins_url('assets/js/NFT/fortmatic.js', __FILE__ ), array('jquery'));
	wp_register_script('toastr-js',plugins_url('assets/js/NFT/toastr.min.js', __FILE__ ), array('jquery'));
	wp_register_style('toastr-min-css', plugins_url('assets/css/NFT/toastr.min.css', __FILE__ ), array(),'', 'all');
	
	wp_enqueue_script('web3-min');
	wp_enqueue_script('index-js');
	wp_enqueue_script('index-min-js');
	wp_enqueue_script('wallet-index-min-js');
	wp_enqueue_script('fortmatic-js');
	wp_enqueue_script('toastr-js');
	wp_enqueue_style('toastr-min-css');
 

}

add_action( 'admin_enqueue_scripts', 'MintNFT_admin_modal_js'); 

/* Enqueuing Frontend Styles */
function MintNFT_front_css()
{
	$max_quantity_mint = get_option('MintNFT_option_name');
	
	global $wpdb, $post;
	if($post->post_name == 'mint'){
	// Enqueue the styles:
	
	wp_enqueue_style('MintNFT_mint_css', plugins_url('assets/css/mint.css', __FILE__ ), array(),'', 'all');
	wp_register_style('bootstrap',plugins_url('assets/css/NFT/bootstrap.min.css', __FILE__ ), array(),'', 'all');
	//popup will not work if i removed below jquery
	 wp_enqueue_script('jquery');
	wp_register_script('bootstrap',plugins_url('assets/js/NFT/bootstrap.min.js', __FILE__ ), array('jquery'));
     wp_register_style('font-awesome', plugins_url('assets/fonts/css/all.css', __FILE__ ), array(),'1.0','all');
	wp_enqueue_style('bootstrap');
	wp_enqueue_script('bootstrap');
	 wp_enqueue_style('font-awesome');
	wp_register_script('web3-min',plugins_url('assets/js/NFT/web3.min.js', __FILE__ ), array('jquery'));
	wp_register_script('index-js',plugins_url('assets/js/NFT/index.js', __FILE__ ), array('jquery'));
	wp_register_script('index-min-js',plugins_url('assets/js/NFT/index.min.js', __FILE__ ), array('jquery'));
	wp_register_script('wallet-index-min-js',plugins_url('assets/js/NFT/wallet_index.min.js', __FILE__ ), array('jquery'));
	wp_register_script('fortmatic-js',plugins_url('assets/js/NFT/fortmatic.js', __FILE__ ), array('jquery'));
	wp_register_script('toastr-js',plugins_url('assets/js/NFT/toastr.min.js', __FILE__ ), array('jquery'));
	wp_register_style('toastr-min-css', plugins_url('assets/css/NFT/toastr.min.css', __FILE__ ), array(),'', 'all');
	wp_enqueue_script('web3-min');
	wp_enqueue_script('index-js');
	wp_enqueue_script('index-min-js');
	wp_enqueue_script('wallet-index-min-js');
	wp_enqueue_script('fortmatic-js');
	wp_enqueue_script('toastr-js');
	wp_enqueue_style('toastr-min-css');
	}
 	
 
}
add_action( 'wp_enqueue_scripts', 'MintNFT_front_css'); 

add_action( 'wp_enqueue_scripts', 'mintNft_enqueue_scripts' );
function mintNft_enqueue_scripts(){
  wp_register_script( 
    'ajaxHandle', 
    plugins_url('PATH TO YOUR SCRIPT FILE/jquery.ajax.js', __FILE__), 
    array(), 
    false, 
    true 
  );
  wp_enqueue_script( 'ajaxHandle' );
  wp_localize_script( 
    'ajaxHandle', 
    'ajax_object', 
    array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) 
  );
}



add_action( "wp_ajax_deleteContractData", "mintNFT__deleteContractData" );
add_action( "wp_ajax_nopriv_deleteContractData", "mintNFT__deleteContractData" );
function mintNFT__deleteContractData(){
	
	$network_type = sanitize_text_field($_POST['network_type']);
	$contract_address = sanitize_text_field($_POST['contract_address']);
	$token = get_option('MintToken');

	$url = externalURL.'/api/contract/deletecontract?contractaddress='.$contract_address;
	
	$curl = curl_init();
	curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'DELETE',
	CURLOPT_HTTPHEADER => array(
	'Authorization: Bearer '.$token.''
	),
	));

	$resp = curl_exec($curl);

	curl_close($curl);
	$response= json_decode($resp);
	 
	if($response->status == 'SUCCESS' ){
		$msg="1"; 
	}else{
		$msg = "0";
	}
 
	$content = (object) [
	'msg' => $msg

	];

	$result = json_encode($content,JSON_UNESCAPED_SLASHES);
	echo wp_kses_post($result);
	die();
 
}

add_action( "wp_ajax_deploycontract", "mintNFT__deploycontract" );
add_action( "wp_ajax_nopriv_deploycontract", "mintNFT__deploycontract" );
function mintNFT__deploycontract(){
	
	$token = get_option( 'MintToken' ); 
	$contract_name = sanitize_text_field($_POST['contract_name']);
	$symbol = sanitize_text_field($_POST['symbol']);
	$totalSupply = sanitize_text_field($_POST['totalSupply']);
	$max_mint = sanitize_text_field($_POST['max_mint']);
	$nftPrice = sanitize_text_field($_POST['nftPrice']);
	$nftPriceInWei = sanitize_text_field($_POST['nftPriceInWei']);
	$hash = sanitize_text_field($_POST['hash']);
	$status = sanitize_text_field($_POST['status']);
	$defaultAccount = sanitize_text_field($_POST['defaultAccount']);
	$encodedConstArgs = sanitize_text_field($_POST['encodedConstArgs']);
	$chainId = sanitize_text_field($_POST['chainId']);
	$chainIdName = sanitize_text_field($_POST['chainIdName']);
	
	
	$data = array(
		'contractname'=> $contract_name,
		'contractsymbol'=> $symbol,
		'totalsupply'=> $totalSupply,
		'maxmint'=> $max_mint,
		'nftprice'=> $nftPrice,
		'nftpriceinwei'=> $nftPriceInWei,
		'txhash'=> $hash,
		'txstatus'=> $status,
		'deployer'=> $defaultAccount,
		'chainid'=> $chainId,
		'chainname'=> $chainIdName,
		'encodedargs'=> $encodedConstArgs

	);
	$data_json = json_encode($data);  
 

	$url = externalURL.'/api/contract';
	 
	$curl = curl_init();
		
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => $data_json,
	  CURLOPT_HTTPHEADER => array(
	    'Authorization: Bearer '.$token.'',
	    'Content-Type: application/json'
	  ),
	));

	 
	$resp = curl_exec($curl);
	curl_close($curl);
	$response= json_decode($resp);
 
	
	if(!empty($response)){
  
		$data = $response->data;
		
		$content = (object) [
		  'msg' => "SUCCESS",
		  'data' => $data
		 ];
	  }else{
		$content = (object) [
		  'msg' => 'FAILED' 
		];
		
	  }  
  
	 $result = json_encode($content,JSON_UNESCAPED_SLASHES);
	 echo wp_kses_post($result);
	 die();
	 
}


  

add_action( "wp_ajax_getChainIdData", "mintNFT__getChainIdData" );
add_action( "wp_ajax_nopriv_getChainIdData", "mintNFT__getChainIdData" );
function mintNFT__getChainIdData(){
	 
	$chainId = sanitize_text_field($_POST['chainId']);
	 
	$url = externalURL.'/api/getchaindata?chainid='.$chainId;
	 
	$curl = curl_init();
	curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
	));

	$resp = curl_exec($curl);


	 
	curl_close($curl);
	$response= json_decode($resp);
   //print_r($response); die;
 
   if(!empty($response)){
  
    $data = $response->data;
    
    $content = (object) [
      'msg' => "SUCCESS",
      'data' => $data
     ];
  }else{
    $content = (object) [
      'msg' => 'FAILED' 
    ];
    
  }  
	 $result = json_encode($content,JSON_UNESCAPED_SLASHES);
	 echo wp_kses_post($result);
	 die();
	  
}

  



add_action( "wp_ajax_purchaseNFT", "mintNFT_purchaseNFT" );
add_action( "wp_ajax_nopriv_purchaseNFT", "mintNFT_purchaseNFT" );
function mintNFT_purchaseNFT(){
    
   
	$nftQty = sanitize_text_field($_GET['nftQty']); 
	$contractaddress = sanitize_text_field($_GET['contractaddress']);
	 
	$nftQty = sanitize_text_field($_GET['nftQty']);
	 
	$url = externalURL.'/api/tokenmetadata/getmetadatabycontractaddress?contractaddress='.$contractaddress.'&reserved=0';
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	));

	$resp = curl_exec($curl);
	curl_close($curl);
	$response = json_decode($resp);
	$data = $response->data;
	//print_r($response);

	$metadata_cid = array();
	$metadata_tokenid = array();
	if(count($data) >=  $nftQty){

		for($i=0;$i<$nftQty;$i++) {
		$metadata_cid[] = $data[$i]->metadatacid;
		$metadata_tokenid[] = $data[$i]->tokenid;
		}

		$count = count($metadata_cid);
		$content = (object) [
		'metadata_cid' => $metadata_cid,
		'tokenid' => $metadata_tokenid,
		'count' => $count
		];

	}else{
		$content = (object) [
			'count' => count($data)
			];

	}
 
	
	 $result = json_encode($content,JSON_UNESCAPED_SLASHES);
	 echo wp_kses_post($result);
	 die();
}

 

add_action( "wp_ajax_getTotalNFTs", "mintNFT_getTotalNFTs" );
add_action( "wp_ajax_nopriv_getTotalNFTs", "mintNFT_getTotalNFTs" );
function mintNFT_getTotalNFTs(){
    
    
	$contractaddress = sanitize_text_field($_GET['contractaddress']);  
	  
	$url = externalURL.'/api/tokenmetadata/getmetadatabycontractaddress?contractaddress='.$contractaddress;
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	));

 
	$resp = curl_exec($curl);
	curl_close($curl);
	$response = json_decode($resp);
	$data = $response->data;
	  
	$content = (object) [
		'data' => $data
		];
 	
	 $result = json_encode($content,JSON_UNESCAPED_SLASHES);
	 echo wp_kses_post($result);
	 die();
}



add_action( "wp_ajax_deploymentTransaction", "mintNFT_deploymentTransaction_function" );
add_action( "wp_ajax_nopriv_deploymentTransaction", "mintNFT_deploymentTransaction_function" );
function mintNFT_deploymentTransaction_function(){
   
	$deployer = get_option('Mintdeployeraddress');
	$chainid = sanitize_text_field($_GET['chainid']);
	 
	$url = externalURL.'/api/contract/getsinglecontract?deployer='.$deployer.'&chainid='.$chainid;
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
	));

	$resp = curl_exec($curl);

	curl_close($curl);
	$response= json_decode($resp);
 
 
	if(!empty($response)){
		$data = $response->data;
		 //print_r($response);
		$transaction_hash = array();
		$network_type = array();
 
		$transaction_hash[] = $data->txhash;
		$network_type[] = $data->chainid;

		$content = (object) [
			'transaction_hash' => $transaction_hash,
			'network_type' => $network_type,
			'status' => $response->status,
			'totalsupply'=>$data->totalsupply,
			'maxmint'=>$data->maxmint,
			'nftprice'=>$data->nftprice,
			'contractaddress'=>$data->contractaddress
			
		  ];
	
	} 

	$result = json_encode($content,JSON_UNESCAPED_SLASHES);
	echo wp_kses_post($result);  die();
  
}
 
add_action( "wp_ajax_getDeploycontract", "mintNFT_getDeploycontract" );
add_action( "wp_ajax_nopriv_getDeploycontract", "mintNFT_getDeploycontract" );
function mintNFT_getDeploycontract(){
	
	$myaccount = sanitize_text_field($_POST['myaccount']);
	$chainId = sanitize_text_field($_POST['network_type']);
	 
	 
	$deployer = get_option('Mintdeployeraddress');
	if($deployer == ""){
		
		$content = (object) [
			'status' =>"Error",
			'msg' => "Please active the MintNFT plugin <a href='admin.php?page=activation' class='click-me'>Click here </a>"
		];	
	
		$result = json_encode($content,JSON_UNESCAPED_SLASHES);
		echo wp_kses_post($result);
		die();


	}else if($myaccount == $deployer){

	$url = externalURL.'/api/contract/getsinglecontract?deployer='.$myaccount.'&chainid='.$chainId;
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
	));

	$resp = curl_exec($curl);
 
  
	curl_close($curl);
	$response= json_decode($resp);
 
   if(!empty($response) && $response->status == 'SUCCESS'){
  
    $results = $response->data;
	$content = (object) [
		'status' =>$response->status,
		'contract_address' => $results->contractaddress,
		'deployer_address' => $results->deployer,
		'deployer_contract_name' => $results->contractname,
		'deployer_contract_symbol' => $results->contractsymbol,
		'deployer_total_supply' => $results->totalsupply,
		'deployer_max_mint' => $results->maxmint,
		'deployer_nftPrice' => $results->nftprice,
		'deployer_nftPriceInWei' => $results->nftpriceinwei,
		'deployer_transaction_hash' => $results->txhash,
		'deployer_transaction_status' => $results->txstatus,
		'deployer_network_type' => $results->chainid,
		'deployer_network_name' => $results->chainname,
		//'deployer_encodedConstArgs' => $results->encodedConstArgs,
		 
	];
	}else{
		$content = (object) [
			'status' =>$response->status,
			'msg' => $response->msg
		];

	} 
	 $result = json_encode($content,JSON_UNESCAPED_SLASHES);
	 echo wp_kses_post($result);
	 die();
}else{

	$content = (object) [
		'status' =>"Error",
		'msg' => "Please connect with account: ".$deployer
	];	

	$result = json_encode($content,JSON_UNESCAPED_SLASHES);
	echo wp_kses_post($result);
	die();

}

}


add_action( "wp_ajax_updateDeploymentTransaction", "mintNFT_updateDeploymentTransaction" );
add_action( "wp_ajax_nopriv_updateDeploymentTransaction", "mintNFT_updateDeploymentTransaction" );
function mintNFT_updateDeploymentTransaction(){
 
 
	$trans_data = rest_sanitize_array($_POST['post_data']['trans_data']);
  	$token = get_option('MintToken');

	for($i=0;$i<count($trans_data);$i++){
	$transaction_hash = $trans_data[$i]['transationHash'];
	$transaction_status = $trans_data[$i]['transactionStatus'];
	$contract_address = $trans_data[$i]['contractAddress'];
	
	$data = array('txstatus'=> $transaction_status,'contractaddress'=> $contract_address);
  
	$data_json = json_encode($data);  
	
	
	$url = externalURL.'/api/contract?txhash='.$transaction_hash;
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'PUT',
	CURLOPT_POSTFIELDS => $data_json,
	CURLOPT_HTTPHEADER => array(
	'Authorization: Bearer '.$token.'',
	'Content-Type: application/json'
	),
	));
 
 
	$resp = curl_exec($curl);
	curl_close($curl);
	$response= json_decode($resp);
	 
	if($response->status == 'SUCCESS' ){
		echo wp_kses_post("Updated Successfully");
	}else{
		echo wp_kses_post("Not updated Successfully");
	}

}

die();
 
}

 
add_action( "wp_ajax_updateMetadata", "mintNFT_updateMetadata" );
add_action( "wp_ajax_nopriv_updateMetadata", "mintNFT_updateMetadata" );
function mintNFT_updateMetadata(){
	 
	 
	$contract_address = sanitize_text_field($_POST['contract_address']);
	$tokenid = sanitize_text_field($_POST['tokenid']);
 	$transaction_hash = sanitize_text_field($_POST['transaction_hash']);
	$transaction_status = sanitize_text_field($_POST['transaction_status']);
	$mintedby = sanitize_text_field($_POST['mintedby']);
	$reserved = sanitize_text_field($_POST['reserved']);

	$data = array('txhash'=>$transaction_hash,'txstatus'=> $transaction_status,'reserved'=> $reserved,'mintedby'=> $mintedby);
	$data_json = json_encode($data);  
	$url = externalURL.'/api/tokenmetadata?contractaddress='.$contract_address.'&tokenid='.$tokenid;
 
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'PUT',
		CURLOPT_POSTFIELDS =>'{
			"txhash":"'.$transaction_hash.'",
			"txstatus":"'.$transaction_status.'",
			"reserved": "1",
			"mintedby":"'.$mintedby.'"

		}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;


		 die();
	 

}



add_action( "wp_ajax_onloadUpdateMetadata", "mintNFT_onloadUpdateMetadata" );
add_action( "wp_ajax_nopriv_onloadUpdateMetadata", "mintNFT_onloadUpdateMetadata" );
function mintNFT_onloadUpdateMetadata(){
	 
	 
	$contract_address = sanitize_text_field($_POST['contract_address']);
	$tokenid = sanitize_text_field($_POST['tokenid']);
 	$transaction_status = sanitize_text_field($_POST['transaction_status']);
 
	$data = array('txstatus'=> $transaction_status);
	$data_json = json_encode($data);  
	$url = externalURL.'/api/tokenmetadata?contractaddress='.$contract_address.'&tokenid='.$tokenid;
 
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'PUT',
		CURLOPT_POSTFIELDS =>'{
			"txstatus":"'.$transaction_status.'"
			 
		}',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		echo $response;
		die();
}


 

add_filter('plugin_action_links', 'MintNFT_add_action_plugin', 10, 5);

/* Tech Mint action links */
function MintNFT_add_action_plugin($actions, $plugin_file) 
{
	static $plugin;
	if (!isset($plugin))
	$plugin = plugin_basename(__FILE__);
	if ($plugin == $plugin_file) {
	$settings 	= array('settings' 	=> '<a href="admin.php?page=activation">' . __('Settings', 'General') . '</a>');
	$actions = array_merge($settings, $actions);
	}
	return $actions;
}   


function mintNFT_activation_actions(){
    do_action( 'wp_writehere_extension_activation' );
}
register_activation_hook( __FILE__, 'mintNFT_activation_actions' );
// Set default values here
function mintNFT_default_options(){
	
	//mintNft_db_myplugin();
	//'MintNFT_PinataKey_field_field'=>'5b82a4d3d2dfa94eb869',
	//'MintNFT_PinataSecret_field_field'=>'2cb0421685286b576063684d1d5f63f8508491d1ed5f13f73bb671cc06ec1c8e',

    $default = array(
		'MintNFT_ServerType_field'=>'ipfs',
		'MintNFT_PinataKey_field_field'=>'',
		'MintNFT_PinataSecret_field_field'=>'',
       	'MintNFT_mintdesc_field'     => '10,000 unique & original Techforce NFTs',
		'MintNFT_contract_address1_field'   => '',
		'MintNFT_bgimage_field'=> plugin_dir_url( __FILE__ ).'setup/nft-bg.png',
		'MintNFT_logoimage_field'=>plugin_dir_url( __FILE__ ).'setup/logo.png',
		'MintNFT_image_prefix_field' => 'img',
		'MintNFT_getway_type_field' => '',
		'MintNFT_heading_field'=> 'Mint The',
		'MintNFT_metadata_short_desc_field' => 'This is Techforce Global Test Desc',
  
		 

	);
    update_option( 'MintNFT_option_name', $default );
}
add_action( 'wp_writehere_extension_activation', 'mintNFT_default_options' );
 


function updateActiovationStatus() 
{
	$email_address = get_option('MintOwnerEmail');
	$api_key = sanitize_text_field($_POST['apikey']);
	$network_type = sanitize_text_field($_POST['network_type']);
	$useraddress = sanitize_text_field($_POST['useraddress']);

	$data = array('email'=> $email_address,'useraddress'=> $useraddress,'chainid'=> $network_type);
	$data_json = json_encode($data);  

	$url = externalURL.'/api/verifykey';
	 
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => $data_json,
	  CURLOPT_HTTPHEADER => array(
	    'x-apikey: '.$api_key.'',
	    'Content-Type: application/json'
	  ),
	));

 
	$resp = curl_exec($curl);
	curl_close($curl);
	
	$response= json_decode($resp);

	if(!empty($response) && ($response->status == 'SUCCESS') ){
	
		if(get_option('Mintdeployeraddress')){
			update_option('Mintdeployeraddress', $useraddress);
		}else{
			add_option('Mintdeployeraddress', $useraddress);
		}

		//add_option('Mintdeployeraddress', $useraddress);

	if(get_option('MintApikey')){
		update_option('MintApikey', $api_key);
	}else{
		add_option('MintApikey', $api_key);
	}
	
	if(get_option('MintToken')){
		update_option('MintToken', $response->token);
	}else{
		add_option('MintToken', $response->token);
	}
	
	}

	$content = (object) [
	'content' => $response
	
	];
	
	$result = json_encode($content,JSON_UNESCAPED_SLASHES);
	echo wp_kses_data($result);  die();
	
   
}

add_action( "wp_ajax_updateActiovationStatus", "updateActiovationStatus" );
add_action( "wp_ajax_nopriv_updateActiovationStatus", "updateActiovationStatus" );


function activation_API() 
{
 
$email_address = sanitize_text_field($_POST['email_address']);

if(get_option('MintOwnerEmail')){
	update_option('MintOwnerEmail', $email_address);
  }else{
	add_option('MintOwnerEmail', $email_address);
  }

$data = array('email'=> $email_address);
$data_json = json_encode($data);  

$url = externalURL.'/api/generatekey';

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $data_json,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));
 
 
$resp = curl_exec($curl);
curl_close($curl);
$response= json_decode($resp);
$api_key = '';
if(!empty($response)){

  $api_key = $response->apikey;
} 
 

if(!empty($api_key)){
      $headers = array('Content-Type: text/html; charset=UTF-8');
      
      $user_subject = 'Congrats! Now you can access your active key (MintNFT Plugin)';
      $user_email = $email_address; 
      $admin_subject = 'User Detail from MintNFT Plugin';
      $admin_email = 'bhavin.shah@techforceglobal.com'; 

      $admin_mail_message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title>Untitled Document</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.minimg">
      <style>
      .fa {color:#fff;}
      a.linkedin {
          background-color: #007bb5;
          color: #fff;
      }
      .csocial_icon a {
          
          border-radius: 50%;
          display: inline-block;
          font-size: 20px;
          height: 30px;
          line-height: 31px;
          margin-right: 10px;
          margin-top: 6px;
          text-align: center;
          width: 30px;
          transition-duration: .2s;
      }
      a.pinterest-p {
          background-color: red;
          color: #fff;
      }
      a.facebook {
          background-color: #007bb5;
          color: #fff;
      }
      a.twitter {
          background-color: #00aced;
          color: #fff;
      }
      a.youtube {
          background-color: #ba0000;
          color: #fff;
      }
      .csocial_icon a i{    margin: 5px 0px 0px 0px;}
      
      </style>
      </head>
      
      <body style="background: #f8f8f8;">
      <div>
      <div>
      <div id="main_section">
      <div>
      <div class="template_section" style="border-radius:10px;width:100%;max-width:600px;margin:0px auto;color:#727882;font-family:Open Sans,sans-serif;font-size:16px;font-weight:400;line-height:1.82857143">
      
      <table border="0" cellpadding="10" cellspacing="0" style="border-radius:10px;background-color:#ffffff;border-collapse:collapse!important;box-shadow: 1px 1px 10px #e3e3e3;" width="100%">
      <tbody style="border-radius:10px;">
      <tr>
      <td>
      <table border="0" cellpadding="0" cellspacing="0" class="BottomPadd-two" id="templateContainer" width="100%" style="background-clip:padding-box;border-spacing:0;border-collapse:collapse!important">
      <tbody>
      <tr class="logo_row">
      <div class="header_logo" style="background-color: #f7f7f7;padding: 20px 40px;text-align: center;border-radius: 10px 10px;margin: 10px;"><img src="https://techforceglobal.com/wp-content/uploads/2022/02/Tech-logo200x40.svg" alt="Logo" style="vertical-align:middle;width:50%;max-width:50%" class="logo_img"></div>
      </tr>
      <tr>
      <td class="Content_section" valign="top" style="color:#505050;font-family:Helvetica;font-size:14px;line-height:150%;padding-top:3.143em;padding-right:3.5em;padding-left:3.5em;padding-bottom:.857em;text-align:left">
      <p style="color:#000;display:block;font-family:Helvetica;font-size:16px;line-height:1.5em;font-style:normal;font-weight:700;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:0px;margin-left:0;text-align:left">Dear Admin,</p>
      <p style="color:#2e2e2e;display:block;font-family:Helvetica;font-size:16px;line-height:1.385em;font-style:normal;font-weight:400;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:15px;margin-left:0;text-align:left">User requested activation key from this email: '.$user_email.'</p>
      <p style="color:#000;display:block;font-family:Helvetica;font-size:16px;line-height:1.5em;font-style:normal;font-weight:700;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:0px;margin-left:0;text-align:left">Regards,</p>
      <p style="color:#2e2e2e;display:block;font-family:Helvetica;font-size:16px;line-height:1.385em;font-style:normal;font-weight:400;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:15px;margin-left:0;text-align:left">Team Techforce Global.</p>
      </td>
      </tr>
      <tr>
      <td align="center" class="SubContent" id="bodyCellFooter" valign="top" style="margin:0;border-radius: 10px;padding:0;padding-top: 9px;padding-bottom: 1px;width:100%!important">
      <div class="footer" style="background-color:rgb(230, 230, 230);text-align:center;border-radius: 10px;">
      <div class="footer_inner" style="background-color:rgb(230, 230, 230);text-align:center;border-radius: 10px;">
      <div style="padding:10px 40px;border-radius: 10px;"><span style="font-size:18px;color:#fff;margin-bottom:-10px;display:block">&nbsp;</span>
      <div>
      <div style="vertical-align:middle;display:inline-block;"><a href="https://www.linkedin.com/company/techforceglobal/" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:white;font-size:20px;text-align:center;background-color:#007BB5;display:inline-block;width:30px;height:30px;border-radius:50%;margin-top:6px;margin-right:10px;line-height:31px;"><img data-imagetype="External" src="https://techforceglobal.com/wp-content/uploads/2022/01/linkedin.png" originalsrc="https://techforceglobal.com/wp-content/uploads/2022/01/linkedin.png" data-connectorsauthtoken="1" data-imageproxyendpoint="/actions/ei" data-imageproxyid="" style="width: 18px; height: auto; padding-top: 5px; align-content: center;"></a>
      </div>
      <div style="vertical-align:middle;display:inline-block;"><a href="https://www.facebook.com/techforceglobal" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:white;font-size:20px;text-align:center;background-color:#007BB5;display:inline-block;width:30px;height:30px;border-radius:50%;margin-top:6px;margin-right:10px;line-height:31px;"><img data-imagetype="External" src="https://techforceglobal.com/wp-content/uploads/2022/01/facebook.png" originalsrc="https://techforceglobal.com/wp-content/uploads/2022/01/facebook.png" data-connectorsauthtoken="1" data-imageproxyendpoint="/actions/ei" data-imageproxyid="" style="width: 18px; height: auto; padding-top: 5px; align-content: center;"></a>
      </div>
      <div style="vertical-align:middle;display:inline-block;"><a href="https://twitter.com/techforceglobal" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:white;font-size:20px;text-align:center;background-color:#00ACED;display:inline-block;width:30px;height:30px;border-radius:50%;margin-top:6px;margin-right:10px;line-height:31px;"><img data-imagetype="External" src="https://techforceglobal.com/wp-content/uploads/2022/01/tiwtter.png" originalsrc="https://techforceglobal.com/wp-content/uploads/2022/01/tiwtter.png" data-connectorsauthtoken="1" data-imageproxyendpoint="/actions/ei" data-imageproxyid="" style="width: 18px; height: auto; padding-top: 5px; align-content: center;"></a>
      </div>
      <div style="vertical-align:middle;display:inline-block;"><a href="https://www.youtube.com/channel/UCZN7tN9UnC_ObqTtkHstUHw" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:white;font-size:20px;text-align:center;background-color:#BA0000;display:inline-block;width:30px;height:30px;border-radius:50%;margin-top:6px;margin-right:10px;line-height:31px;"><img data-imagetype="External" src="https://techforceglobal.com/wp-content/uploads/2022/01/Youtube.png" originalsrc="https://techforceglobal.com/wp-content/uploads/2022/01/Youtube.png" data-connectorsauthtoken="1" data-imageproxyendpoint="/actions/ei" data-imageproxyid="" style="width: 18px; height: auto; padding-top: 5px; align-content: center;"></a>
      </div>
      <div style="vertical-align:middle;display:inline-block;"><a href="https://www.instagram.com/techforce_global/" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:white;font-size:20px;text-align:center;background-color:red;display:inline-block;width:30px;height:30px;border-radius:50%;margin-top:6px;margin-right:10px;line-height:31px;"><img data-imagetype="External" src="https://techforceglobal.com/wp-content/uploads/2022/01/inastagram.png" originalsrc="https://techforceglobal.com/wp-content/uploads/2022/01/inastagram.png" data-connectorsauthtoken="1" data-imageproxyendpoint="/actions/ei" data-imageproxyid="" style="width: 18px; height: auto; padding-top: 5px; align-content: center;"></a></div>
      <div style="vertical-align:middle;display:inline-block;"></div>
      </div>
      </div>
      <p style="margin:10px auto;"><a href="https://techforceglobal.com/about-us" rel="nofollow" style="text-align:center;color:#000;font-weight: 700;" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://techforceglobal.com/about-us&amp;source=gmail&amp;ust=1555998308760000&amp;usg=AFQjCNE7U4heQR2qXETytZ19BI16pYLAxg">About Us</a></p></div>
      <div style="background-color:#33353f;border-top:1px solid #3d3d3d;padding:5px 10px;color:#ff8513;font-size:13px;border-radius: 0px 0px 10px 10px;">
      <div>Techforce©2023. All Rights Reserved.</div>
      </td></tr></tbody></table></td></tr></tbody></table></div><div class="yj6qo"></div><div class="adL">
      </div><div class="m_adL"></div><div class="adL">
      </div></div><div class="adL">
      </div></div><div class="adL">
      </div></div><div class="adL">
      
      
      </div></div>
      
      </body>
      </html>
      ';
      $user_mail_message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title>Untitled Document</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.minimg">
      <style>
      .fa {color:#fff;}
      a.linkedin {
          background-color: #007bb5;
          color: #fff;
      }
      .csocial_icon a {
          
          border-radius: 50%;
          display: inline-block;
          font-size: 20px;
          height: 30px;
          line-height: 31px;
          margin-right: 10px;
          margin-top: 6px;
          text-align: center;
          width: 30px;
          transition-duration: .2s;
      }
      a.pinterest-p {
          background-color: red;
          color: #fff;
      }
      a.facebook {
          background-color: #007bb5;
          color: #fff;
      }
      a.twitter {
          background-color: #00aced;
          color: #fff;
      }
      a.youtube {
          background-color: #ba0000;
          color: #fff;
      }
      .csocial_icon a i{    margin: 5px 0px 0px 0px;}
      
      </style>
      </head>
      
      <body style="background: #f8f8f8;">
      <div>
      <div>
      <div id="main_section">
      <div>
      <div class="template_section" style="border-radius:10px;width:100%;max-width:600px;margin:0px auto;color:#727882;font-family:Open Sans,sans-serif;font-size:16px;font-weight:400;line-height:1.82857143">
      
      <table border="0" cellpadding="10" cellspacing="0" style="border-radius:10px;background-color:#ffffff;border-collapse:collapse!important;box-shadow: 1px 1px 10px #e3e3e3;" width="100%">
      <tbody style="border-radius:10px;">
      <tr>
      <td>
      <table border="0" cellpadding="0" cellspacing="0" class="BottomPadd-two" id="templateContainer" width="100%" style="background-clip:padding-box;border-spacing:0;border-collapse:collapse!important">
      <tbody>
      <tr class="logo_row">
      <div class="header_logo" style="background-color: #f7f7f7;padding: 20px 40px;text-align: center;border-radius: 10px 10px;margin: 10px;"><img src="https://techforceglobal.com/wp-content/uploads/2022/02/Tech-logo200x40.svg" alt="Logo" style="vertical-align:middle;width:50%;max-width:50%" class="logo_img"></div>
      </tr>
      <tr>
      <td class="Content_section" valign="top" style="color:#505050;font-family:Helvetica;font-size:14px;line-height:150%;padding-top:3.143em;padding-right:3.5em;padding-left:3.5em;padding-bottom:.857em;text-align:left">
      <p style="color:#000;display:block;font-family:Helvetica;font-size:16px;line-height:1.5em;font-style:normal;font-weight:700;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:0px;margin-left:0;text-align:left">Dear '.$user_email.',</p>
      <p style="color:#2e2e2e;display:block;font-family:Helvetica;font-size:16px;line-height:1.385em;font-style:normal;font-weight:400;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:15px;margin-left:0;text-align:left">Your activation key: '.$api_key.'</p>
      <p style="color:#2e2e2e;display:block;font-family:Helvetica;font-size:16px;line-height:1.385em;font-style:normal;font-weight:400;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:15px;margin-left:0;text-align:left">You can use this key to active the MintNFT plugin.</p>
      <p style="color:#000;display:block;font-family:Helvetica;font-size:16px;line-height:1.5em;font-style:normal;font-weight:700;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:0px;margin-left:0;text-align:left">Regards,</p>
      <h1 style="color:#2e2e2e;display:block;font-family:Helvetica;font-size:16px;line-height:1.385em;font-style:normal;font-weight:400;letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:15px;margin-left:0;text-align:left">Team Techforce Global</h1>
      </td>
      </tr>
      <tr>
      <td align="center" class="SubContent" id="bodyCellFooter" valign="top" style="margin:0;border-radius: 10px;padding:0;padding-top: 9px;padding-bottom: 1px;width:100%!important">
      <div class="footer" style="background-color:rgb(230, 230, 230);text-align:center;border-radius: 10px;">
      <div class="footer_inner" style="background-color:rgb(230, 230, 230);text-align:center;border-radius: 10px;">
      <div style="padding:10px 40px;border-radius: 10px;"><span style="font-size:18px;color:#fff;margin-bottom:-10px;display:block">&nbsp;</span>
      <div>
      <div style="vertical-align:middle;display:inline-block;"><a href="https://www.linkedin.com/company/techforceglobal/" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:white;font-size:20px;text-align:center;background-color:#007BB5;display:inline-block;width:30px;height:30px;border-radius:50%;margin-top:6px;margin-right:10px;line-height:31px;"><img data-imagetype="External" src="https://techforceglobal.com/wp-content/uploads/2022/01/linkedin.png" originalsrc="https://techforceglobal.com/wp-content/uploads/2022/01/linkedin.png" data-connectorsauthtoken="1" data-imageproxyendpoint="/actions/ei" data-imageproxyid="" style="width: 18px; height: auto; padding-top: 5px; align-content: center;"></a>
        </div>
        <div style="vertical-align:middle;display:inline-block;"><a href="https://www.facebook.com/techforceglobal" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:white;font-size:20px;text-align:center;background-color:#007BB5;display:inline-block;width:30px;height:30px;border-radius:50%;margin-top:6px;margin-right:10px;line-height:31px;"><img data-imagetype="External" src="https://techforceglobal.com/wp-content/uploads/2022/01/facebook.png" originalsrc="https://techforceglobal.com/wp-content/uploads/2022/01/facebook.png" data-connectorsauthtoken="1" data-imageproxyendpoint="/actions/ei" data-imageproxyid="" style="width: 18px; height: auto; padding-top: 5px; align-content: center;"></a>
        </div>
        <div style="vertical-align:middle;display:inline-block;"><a href="https://twitter.com/techforceglobal" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:white;font-size:20px;text-align:center;background-color:#00ACED;display:inline-block;width:30px;height:30px;border-radius:50%;margin-top:6px;margin-right:10px;line-height:31px;"><img data-imagetype="External" src="https://techforceglobal.com/wp-content/uploads/2022/01/tiwtter.png" originalsrc="https://techforceglobal.com/wp-content/uploads/2022/01/tiwtter.png" data-connectorsauthtoken="1" data-imageproxyendpoint="/actions/ei" data-imageproxyid="" style="width: 18px; height: auto; padding-top: 5px; align-content: center;"></a>
        </div>
        <div style="vertical-align:middle;display:inline-block;"><a href="https://www.youtube.com/channel/UCZN7tN9UnC_ObqTtkHstUHw" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:white;font-size:20px;text-align:center;background-color:#BA0000;display:inline-block;width:30px;height:30px;border-radius:50%;margin-top:6px;margin-right:10px;line-height:31px;"><img data-imagetype="External" src="https://techforceglobal.com/wp-content/uploads/2022/01/Youtube.png" originalsrc="https://techforceglobal.com/wp-content/uploads/2022/01/Youtube.png" data-connectorsauthtoken="1" data-imageproxyendpoint="/actions/ei" data-imageproxyid="" style="width: 18px; height: auto; padding-top: 5px; align-content: center;"></a>
        </div>
        <div style="vertical-align:middle;display:inline-block;"><a href="https://www.instagram.com/techforce_global/" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color:white;font-size:20px;text-align:center;background-color:red;display:inline-block;width:30px;height:30px;border-radius:50%;margin-top:6px;margin-right:10px;line-height:31px;"><img data-imagetype="External" src="https://techforceglobal.com/wp-content/uploads/2022/01/inastagram.png" originalsrc="https://techforceglobal.com/wp-content/uploads/2022/01/inastagram.png" data-connectorsauthtoken="1" data-imageproxyendpoint="/actions/ei" data-imageproxyid="" style="width: 18px; height: auto; padding-top: 5px; align-content: center;"></a></div>
        <div style="vertical-align:middle;display:inline-block;"></div>
        </div>
      </div>
      <p style="margin:10px auto;"><a href="https://techforceglobal.com/about-us" rel="nofollow" style="text-align:center;color:#000;font-weight: 700;" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://techforceglobal.com/about-us&amp;source=gmail&amp;ust=1555998308760000&amp;usg=AFQjCNE7U4heQR2qXETytZ19BI16pYLAxg">About Us</a></p></div>
      <div style="background-color:#33353f;border-top:1px solid #3d3d3d;padding:5px 10px;color:#ff8513;font-size:13px;border-radius: 0px 0px 10px 10px;">
      <div>Techforce©2023. All Rights Reserved.</div>
      </td></tr></tbody></table></td></tr></tbody></table></div><div class="yj6qo"></div><div class="adL">
      </div><div class="m_adL"></div><div class="adL">
      </div></div><div class="adL">
      </div></div><div class="adL">
      </div></div><div class="adL">
      
      
      </div></div>
      
      </body>
      </html>
      ';
      
 
      wp_mail( $admin_email, $admin_subject, $admin_mail_message, $headers);
      $sent = wp_mail( $user_email, $user_subject, $user_mail_message, $headers);

      
      if($sent) {
       echo wp_kses_post("1");       
      }
      else  {
        echo wp_kses_post("0");       
      }
    die();

}else{
    echo wp_kses_post("0");  
    die();  

  }

}

add_action( "wp_ajax_activation_API", "activation_API" );
add_action( "wp_ajax_nopriv_activation_API", "activation_API" );
 
 
add_action( "wp_ajax_mintGetNetworks", "mintGetNetworks" );
add_action( "wp_ajax_nopriv_mintGetNetworks", "mintGetNetworks" );

function mintGetNetworks(){
   
  $url = externalURL.'/api/getchains';
  $curl = curl_init();

  curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
  ));
  
  $resp = curl_exec($curl);
  
  curl_close($curl);
  $response= json_decode($resp);

 
	if(!empty($response)){
	
	  $data = $response->data;
	  
	  $content = (object) [
		'msg' => "SUCCESS",
		'data' => $data
	   ];
	}else{
	  $content = (object) [
		'msg' => 'FAILED' 
	  ];
	  
	}  
	   
	 $result = json_encode($content,JSON_UNESCAPED_SLASHES);  
	 echo wp_kses_data($result);  die();
	}
  
	 
	
add_action( "wp_ajax_getAbiBytecode", "getAbiBytecode" );
add_action( "wp_ajax_nopriv_getAbiBytecode", "getAbiBytecode" );

function getAbiBytecode(){
  
$chainId = sanitize_text_field($_POST['chainId']);
$token = get_option('MintToken');
$url = externalURL.'/api/contract/deployNFT?chainid='.$chainId;
 
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
	'Authorization: Bearer '.$token.''
	),
));

$resp = curl_exec($curl);
   
  curl_close($curl);
  $response= json_decode($resp);

 
	if(!empty($response)){
	
	  $data = $response->data;
	  
	  $content = (object) [
		'msg' => "SUCCESS",
		'data' => $data
	   ];
	}else{
	  $content = (object) [
		'msg' => 'FAILED' 
	  ];
	  
	}  
	   
	 $result = json_encode($content,JSON_UNESCAPED_SLASHES);  
	 echo wp_kses_data($result);  die();
	}
  

 
	
