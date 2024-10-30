<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<?php

if(!get_option('MintToken')){
	  
		?><div class="connection-wrapper">
    <div class="nft-img"><img src="<?php echo plugin_dir_url( __DIR__ ).'/mintnft'.'/setup'.'/nft-bg.png';?>" alt="">
        <h3 class="login-error">Please active the MintNFT plugin</h3><a href="admin.php?page=activation"
            class="clickme">
            Click here </a>
    </div>
</div><?php	die;	
 
} 
?>
<h1 class="sec-title"><?php _e('Contract Deployment','textdomain' ); ?></h1>
<div class="NFT-Metadata-notice-block">
    <strong class="red"><?php _e('Information','textdomain');?></strong>
    <p><?php _e('Connect Metamask wallet.','textdomain'); ?>
    </p>
    <p><?php _e('Please fill the form for contract deployment.','textdomain');?></p>
    <p><?php _e('You will receive the transaction details once the contract is deployed successfully.','textdomain');?>
    </p>
    <p><?php _e('We also allow you to remove/delete the active contract.','textdomain');?></p>
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

                    <div class="form-group col-md-3 col-sm-12 mt-4 mr-4 contract-network-field">
                        <div id="contract-network"><label for="inputNetwork">Network</label>
                        <select id="inputNetwork" class="form-control round-box">
                            <option value="">Please Select</option>

                        </select></div><div class="loader-network-icon-contract"><img src="<?php echo plugin_dir_url( __DIR__ ).'mintnft/assets/img/loader-large.gif'?> "
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
<section id="error-section" class="hide"></section>
<section id="deploy-contract-section" class="hide"></section>
<section id="deploy-contract-form" class="hide">

    <div class="full-container">
        <div class="">

            <h3 class="section-title">
                <b>Fill below form to Deploy Contract</b>
            </h3>
            <div id="card">
                <form id="basic-form" name="basic-form" action="" method="post" enctype="multipart/form-data">
                    <div class="form-row ml-6">


                        <div class="form-group col-md-4">
                            <label for="contract_name">Contact Name<span class="required_field">*</span></label>
                            <input type="text" class="form-control round-box" name="contract_name" id="contract_name"
                                placeholder="Contract Name">
                            <label id="contract_name-error" class="error fail-alert" for="contract_name"
                                style="display:none;margin: 0px;">Please enter your contract name</label>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="contract_symbol">Symbol<span class="required_field">*</span></label>
                            <input type="text" class="form-control round-box" name="contract_symbol"
                                id="contract_symbol" placeholder="Symbol">
                            <label id="contract_symbol-error" class="error fail-alert" for="contract_symbol"
                                style="display:none;margin: 0px;">Please enter your symbol</label>
                        </div>



                        <div class="form-group col-md-4">
                            <label for="total_supply">Enter Total Supply<span class="required_field">*</span></label>
                            <input type="text" class="form-control round-box" name="total_supply" id="total_supply"
                                placeholder="Enter Total Supply">
                            <label id="total_supply-error" class="error fail-alert" for="total_supply"
                                style="display:none;margin: 0px;">Please enter your total supply</label>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="max_mint">Enter Max Mint<span class="required_field">*</span></label>
                            <input type="text" class="form-control round-box" name="max_mint" id="max_mint"
                                placeholder="Enter Max Mint">
                            <label id="max_mint-error" class="error fail-alert" for="max_mint"
                                style="display:none;margin: 0px;">Please enter your max mint</label>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="price">Enter Price<span class="required_field">*</span></label>
                            <input type="text" class="form-control round-box" name="price" id="price"
                                placeholder="Enter Price">
                            <label id="price-error" class="error fail-alert" for="price"
                                style="display:none;margin: 0px;">Please enter price</label>
                        </div>



                    </div>
                    <div class="form-group col-md-6 p-0 ml-6">
                        <input type="hidden" name="useraddress" id="useraddress">
                        <input type="hidden" name="network_type" id="network_type">
                        <button type="button" class="btn btn-primary round-box" id="btn-deploy" name="but_submit">
                            Submit </button>
                    </div>
                </form>
            </div>




        </div>
    </div>
</section>


<script>
let adminAjaxpath = '<?php echo admin_url();?>/admin-ajax.php';
let plugin_path = '<?php echo MintNFT_PLUGIN_URL ?>';

$('<div id="pageloader"><img src="<?php echo plugins_url( 'assets/img/loader-large.gif', __FILE__ ); ?>" alt="processing..." /><span style="font-size: 20px;font-weight: bold;color:#8c8f94;left:0;right:0;top:60%; text-align:center;"><br>Please Wait, It might take some time.<br>And do not refresh the page.</span></div>')
    .insertBefore("body");
    jQuery("#inputNetwork").change(function () {
    console.log('myaccounts',myaccounts);
    if (myaccounts != null || myaccounts != undefined) {
    onConnect(); 
    }

    }); 

</script>