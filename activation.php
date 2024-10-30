<h1 class="sec-title"><?php _e('Mint NFT Activation','textdomain' ); ?></h1>
<div class="NFT-Metadata-notice-block">
    <strong class="red"><?php _e('Information','textdomain');?></strong>
    <p><?php _e('Please click on the Envelop icon in the below form to receive the activation key in your inbox.','textdomain');?>
    </p>
    <p><?php _e('Please add your email address and press "Send", you will receive the activation key in your inbox.','textdomain');?>
    </p>
    <p><?php _e('Connect Metamask wallet.','textdomain'); ?></p>
    <p><?php _e('Use the activation key you have received in your inbox to activate the plugin.','textdomain'); ?></p>
    <p><?php _e('After activation, you can proceed with the contract deployment.','textdomain');?></p>
</div>

<div class="main-wrapper">
    <form method="post" id="apiform" class="myform hide">
        <div class="card" style="border-radius: 10px;">
            <div class="card-content">
                <h1 class="head"> GET ACTIVATION KEY</h1>
                <label> <b>Email Address:</b></label>
                <div class="key-field"> <input type="text" name="email_address" id="email_address" value=""
                        placeholder="Enter Email Address" oninput="validateEmail(this);">&nbsp;<a class="get-email-tag"
                        title="Add Key"><i class="fa fa-key" aria-hidden="true"></i></a></div>
                <label id="email_address_invalid"></label>

                <div class="form-submit-btn" autofocus="false"><input type='submit' autofocus="false"
                        class="button button-primary" autofocus="false" value='Send' id='submit'>
                    <img src="<?php echo plugin_dir_url( __DIR__ ).'mintnft/assets/img/loader-large.gif'?> "
                        class="loader-icon" style="display:none">
                </div>

            </div>

            <div id="msg-div"></div>
    </form>



</div>

<form method="post" id="file_form" class="myform show">
    <div class="card" style="border-radius: 10px;">

        <div class="card-content">
            <h1 class="head"> SET ACTIVATION KEY</h1>


            <div class="form-group">
                <button type="button" class="btn btn-primary round-box btn-metamask" id="btn-connect"> Connect to Metamask </button>
                <button type="button" class="btn btn-primary round-box" id="btn-disconnect" style="display:none">
                    Disconnect </button>
                <input type="hidden" name="useraddress" id="useraddress" value="">
            </div>

            <label> <b>Please add Activation Key </b></label>
            <div class="key-field"><input type="text" name="api_key" id="api_key" value=""
                    placeholder="Enter an Active Key" required="">&nbsp;<a class="get-key-tag" title="Get Key"><i
                        class="fa fa-envelope" aria-hidden="true"></i></a></div>
            <label> <b>Network Type :</b></label>
            <div class="network-type-field">
                <select name="network_type" id="network_type">
                    <option value="0">Please Select</option>
                </select> <div class="loader-network-icon-div"><img src="<?php echo plugin_dir_url( __DIR__ ).'mintnft/assets/img/loader-large.gif'?> "
                    class="loader-network-icon" style="display:none"></div>
            </div>
            <div class="form-submit-btn" autofocus="false" id="form-submit-btn"><input type="submit"
                    class="button button-primary" value="Active" id="keysubmit">
                <img src="<?php echo plugin_dir_url( __DIR__ ).'mintnft/assets/img/loader-large.gif'?> "
                    class="loader-icon" style="display:none">
            </div>
        </div>
        <div id="msg-div2"></div>
        <div class="contract-link">After Activation go for contract deployment: <a
                href="admin.php?page=contractdeployment" class="">Click here</a></div>

    </div>
</form>

<form method="post" id="file_form2" class="myform2">
    <div class="card" style="border-radius: 10px;">
        <img src="<?php echo plugin_dir_url( __DIR__ ).'mintnft/assets/img/Tech-logo.svg'?> " class="logo">
        <h6 class="cont-email">Connect with us on:&nbsp;<a
                href="mailto:reach@techforceglobal.com">reach@techforceglobal.com</a><br />
            For more details Visit:&nbsp;<a href="https://techforceglobal.com/"
                target="_blank">www.techforceglobal.com</a></h6>
        <h6>Location:</h6>
        <div class="address_info">
            <div class="single_info">
                <div class="icon-flag">
                    <img src="<?php echo plugin_dir_url( __DIR__ ).'mintnft/assets/img/india.png'?>">
                </div>
                <div class="info">
                    <p></p>
                    <p>403, Venus Benecia,<br>
                        Nr.Pakwan Cross Road,<br>
                        Bodakdev, Ahmedabad-<br>
                        380054</p>
                    <p></p>
                </div>
            </div>

            <div class="single_info">
                <div class="icon-flag">
                    <img src="<?php echo plugin_dir_url( __DIR__ ).'mintnft/assets/img/us.png'?>">
                </div>
                <div class="info">
                    <p></p>
                    <p>1755 Park Street,<br>
                        Suite 200,<br>
                        Naperville, IL 60563</p>
                    <p></p>
                </div>
            </div>
            <div class="single_info">
                <i class="fa fa-phone-square" aria-hidden="true"></i>
                <p>India: +91 (79) 48904529</p>
                <p>Us: +1 (630) 296-6606</p>
            </div>
        </div>

        <h5 class="social-title">Social media Links :</h5>
        <div class="social-div">
            <div class="social-left">
                <p><a href="https://linkedin.com/company/techforceglobal/" target="_blank"><b><i class="fa fa-linkedin"
                                aria-hidden="true"></i></b></a></p>
                <p><a href="https://instagram.com/techforce_global/" target="_blank"><b><i class="fa fa-instagram"
                                aria-hidden="true"></i></b> </a></p>
                <p><a href="https://youtube.com/channel/UCZN7tN9UnC_ObqTtkHstUHw" target="_blank"><b><i
                                class="fa fa-youtube" aria-hidden="true"></i></b></a></p>

                <!--div class="social-right"> -->
                <p><a href="https://facebook.com/techforceglobal" target="_blank"><b><i class="fa fa-facebook"
                                aria-hidden="true"></i></b></a></p>
                <p><a href="https://twitter.com/techforceglobal" target="_blank"><b><i class="fa fa-twitter"
                                aria-hidden="true"></i></b> </a></p>
                <p><a href="https://dribbble.com/TechForceGlobal" target="_blank"><b><i class="fa fa-dribbble"
                                aria-hidden="true"></i></b> </a></p>
            </div>
            <!--</div>-->
        </div>
</form>
<div class="footer text-center mt-1">
    <p>Techforce©2023. All Rights Reserved</p>
</div>
</div>
</div>

<script>
let adminAjaxpath = '<?php echo admin_url();?>/admin-ajax.php';
</script>