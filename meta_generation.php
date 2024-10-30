<script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>

<?php 

global  $table_prefix, $wpdb;
$mc_options = get_option( 'MintNFT_option_name' );  
$image_prefix = $mc_options['MintNFT_image_prefix_field'];
$getway_type = $mc_options['MintNFT_getway_type_field'];
$short_desc = $mc_options['MintNFT_metadata_short_desc_field'];
$serverType = $mc_options['MintNFT_ServerType_field'];
$pinataKey = $mc_options['MintNFT_PinataKey_field_field'];
$pinataSecret = $mc_options['MintNFT_PinataSecret_field_field'];
$token = get_option('MintToken'); 
$useraddress= get_option('Mintdeployeraddress'); 
?>
<h1 class="sec-title"><?php _e('Upload Images to Generate Metadata','textdomain' ); ?></h1>
<div class="NFT-Metadata-notice-block">
    <strong class="red"><?php _e('Information','textdomain');?></strong>
    <p><?php _e('Connect Metamask wallet.','textdomain'); ?>
    </p>

    <p><?php _e('Only contract deployer would be able to generate Metadata here.','textdomain'); ?>
    </p>
    <p><?php _e('Make sure to have the same prefix for both image files and json files.','textdomain');?>
    </p>
    <p><?php _e('Make sure upload image files sequence and json files sequence must be same.','textdomain');?>
    </p>
    <p><?php _e('You need to follow all the above steps carefully for this plugin to work successfully. ','textdomain');?>
    </p>
    <p><?php _e('Once the NFT Metadata is generated. Click on the "Pages" in the left Nav bar, you will see "Mint" page. Click on "Quick edit" and select "Minting Page" template then click on Update button.','textdomain');?>
    </p>
</div>

<section>
    <div class="full-container">
        <div calss="row">
            <h3 class="section-title">
                <b>Metamask Connection</b>
            </h3>
            <div class="clearfix"></div>

            <form class="connect-main-container">
                <div class="form-row">
                    <div class="form-group col-md-3 p-0" id="connect-metamask">
                        <button type="button" class="btn btn-primary round-box btn-metamask" id="btn-connect"> Connect to Metamask </button>
                        <button type="button" class="btn btn-primary round-box" id="btn-disconnect"
                            style="display:none"> Disconnect </button>

                    </div>

                    <div class="form-group col-md-3 col-sm-12 mt-4 mr-4 metadata-network-field">
                        <div id="metadata-network"><label for="inputNetwork">Network</label>
                        <select id="inputNetwork" class="form-control round-box">
                            <option value="">Please Select</option>
                     </select></div><div class="loader-network-icon-metadata"><img src="<?php echo plugin_dir_url( __DIR__ ).'mintnft/assets/img/loader-large.gif'?> "
                    class="loader-network-icon" style="display:none"></div>
                    </div>
                    <div class="form-group col-md-3 col-sm-12 mt-4">
                        <label for="inputState">Collection</label>
                        <input type="text" class="form-control round-box pr-2" name="existing_contract_address"
                            id="existing_contract_address" placeholder="Contract Address"
                            value="<?php //echo @$resultData->contract_address;?>" disabled>

                    </div>
                </div>

            </form>
        </div>
    </div>
</section>
<section id="NFT-form-section">


    <div class="upload-boxes hide" id="upload-metadata-section">

        <div class="col-md-6 margin-30px-bottom xs-margin-20px-bottom" style="  margin: 0px auto;">
            <div class="box-inner">
                <div class="tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    <span class="tooltiptext">Please make sure that the prefix for images is the same as you have
                        mentioned in the Mint NFT setting page.
                    </span>
                </div>
                <!-- Form -->
                <form method='post' id="ImageForm" name='myform' enctype='multipart/form-data'>
                    <div class="NFT-metadata-section">
                        <strong><?php _e('Upload NFTs to Generate <span>New</span> Metadata on '.ucfirst($serverType),'textdomain' ); ?></strong>
                        <div class="NFTupload-form">

                            <div class="radio-metadata-box">
                                <label class="layersType">
                                    <input type="radio" id="metadata_radio" name="metadata_type"
                                        value="metadatawithimage" checked />
                                    <img src="<?php echo plugin_dir_url( __FILE__ );?>/setup/meta_image.svg"
                                        class="meta-icon">
                                    <div>Metadata & Image</div>
                                </label>

                                <label class="layersType">
                                    <input type="radio" id="image_radio" name="metadata_type" value="Image" />
                                    <img src="<?php echo plugin_dir_url( __FILE__ );?>/setup/image.svg"
                                        class="meta-icon">
                                    <div for="image">Only Image</div>
                                </label>
                            </div>

                            <div class="row metadata-box">
                                <div class="col-lg-8 col-md-12 file-input">
                                    <input class="files-data" type='file' name="metadata[]" id="metadata"
                                        multiple="multiple" accept=".json" onchange="checkJsonFileUploadExt(this);">
                                </div>
                                <div class="col-lg-4 col-md-12 file-note">
                                    <span class="img-type-msg">Allowed .json only</span>
                                </div>
                            </div>
                            <div class="row image-box">
                                <div class="col-lg-8 col-md-12 file-input">
                                    <input class="files-data" type='file' name="image[]" id="image" multiple="multiple"
                                        accept=".jpg,.jpeg,.png" onchange="checkFileUploadExt(this);">
                                </div>
                                <div class="col-lg-4 col-md-12 file-note">
                                    <span class="img-type-msg">Allowed .jpg,.jpeg,.png</span>
                                </div>
                            </div>


                            <input type="hidden" name="image_prefix" class="image_prefix"
                                value="<?php echo wp_kses_post($image_prefix);?>">
                            <input type="hidden" name="getway_type" class="getway_type" id="getway_type"
                                value="<?php echo esc_url($getway_type); ?>">
                            <input type="hidden" name="short_desc" class="short_desc"
                                value="<?php echo wp_kses_post($short_desc); ?>">
                            <?php if($serverType == 'pinata'){ ?>

                            <input type="hidden" name="pinataKey" id="pinataKey"
                                value="<?php echo wp_kses_post($pinataKey);?>">
                            <input type="hidden" name="pinataSecret" id="pinataSecret"
                                value="<?php echo wp_kses_post($pinataSecret);?>">
                            <?php } ?>

                            <input type="hidden" name="ismetadata" id="ismetadata" value="1">
                            <input type="hidden" name="serverType" id="serverType"
                                value="<?php echo wp_kses_post($serverType);?>">
                            <input type="hidden" name="token" id="token" value="<?php echo wp_kses_post($token);?>">
                            <input type="hidden" name="useraddress" id="useraddress" value="<?php echo wp_kses_post($useraddress);?>">
                            <input type="hidden" name="contract_address" id="contract_address" class="contract_address"
                                value="">



                        </div>

                        <input type='button' name='Imgbut_submit' id="Imgbut_submit" value='Submit' class="buy" disabled>

                    </div>

                </form>
            </div>
        </div>

<!-- 
        <div id="divLoading">
            <div class="spinner">
                <i style="font-size: 80px;color: white;" class="fa fa-spinner fa-spin"></i>
                <span style="font-size: 20px;font-weight: bold;color: white; visibility: visible;">
                    <br>
                    Please Wait, It might take some time.
                    <br>
                    And don't refresh the page.
                </span>
            </div>
        </div>

    </div> -->


    <button type="button" id="mintsuccessModal" class="btn btn-primary" data-bs-toggle="modal"
            data-bs-target="#resultPopup" style="display:none;visibility:hidden;">
            Launch static backdrop modal
        </button>

        <!-- Modal -->
        <div class="modal fade" id="resultPopup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog mint_modal">
                <div class="modal-content  mint-nft">
                    <div class="modal-header">
                        <h5 class="modal-title mint_the" id="exampleModalLabel"><b>Upload to IPFS/Pinata</b></h5>
                        <button id="closeModal" type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="clearfix"></div>
                    <h6 class="ran" id="res1">You have successfully generated metadata.</h6>
                    <div class="ran2" id="res2">Your Token id's:</div>
                    <div class="clearfix"></div>
                    <div class="modal-body">
                        <ul class="result-token-ids" id="tokenIDS" style="margin-bottom:0px;"></ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="buy_pop" data-dismiss="modal">Ok</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- EOD popup modal-->

    <!-- Form -->
</section>
<section id="error-section" class="hide"></section>
<script>
let adminAjaxpath = '<?php echo admin_url();?>/admin-ajax.php';
let externalURL = '<?php echo externalURL;?>';

jQuery('<div id="pageloader"><img src="<?php echo plugins_url( 'assets/img/loader-large.gif', __FILE__ ); ?>" alt="processing..." /><span style="font-size: 20px;font-weight: bold;color:#8c8f94;left:0;right:0;top:60%; text-align:center;"><br>Please Wait, It might take some time.<br>And do not refresh the page.</span></div>')
    .insertBefore("body");
console.log(externalURL);
jQuery("#inputNetwork").change(function () {
    console.log('myaccounts',myaccounts);
    if (myaccounts != null || myaccounts != undefined) {
        onConnect(); 
    }
  
        });
</script>