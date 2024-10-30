// Unpkg imports
const Web3Modal = window.Web3Modal.default;
const WalletConnectProvider = window.WalletConnectProvider.default;

// Web3modal instance
let web3Modal

// Chosen wallet provider given by the dialog window
let provider;
 
let erc721 = {
    gasLimit: 5000000
    //gasLimit: 3000000
};
let chainDeteched = {}
// let chainDeteched = {
//     "0x1": "Ethereum Mainnet Network",
//     "0x3": "Ropsten Test Network",
//     "0x4": "Rinkeby Test Network",
//     "0x5": "Goerli Test Network",
//     "0x2a": "Kovan Test Network",
//     "0x539": "Ganache Network",
// }

let buyNowChainId;
 

let contractABI;
let contractBytecode;

async function alertMessage(message, type) {

    switch (type) {

        case "info": {
            toastr.info(message, 'Info');
            break;
        }
        case "success": {
            toastr.info(message, 'Success');
            break;
        }
        case "warning": {
            toastr.info(message, 'Warning');
            break;
        }
        case "danger": {
            toastr.info(message, 'Danger');
            break;
        }

    }

}

async function refreshPage(val) {
    console.log(val);

    if (val == 'first') {
        //document.querySelector("#btn-disconnect").style.display = "none";
    } else if (val == 'wallet-connected') {
        document.querySelector("#btn-connect").style.display = "none";
        document.querySelector("#btn-disconnect").style.display = "inline";
      
    }

}

/**
 * Setup the orchestra
 */
async function init() {

    toastr.options = {
        "closeButton": true,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "linear",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    await refreshPage('first');


    // Tell Web3modal what providers we have available.
    // Built-in web browser provider (only one can exist as a time)
    // like MetaMask, Brave or Opera is added automatically by Web3modal
    const providerOptions = {
        walletconnect: {
            package: WalletConnectProvider,
            options: {
                // Mikko's test key - don't copy as your mileage may vary
                infuraId: "70ccb9df33854cd79fdee4473568b91f",
            }
        },

        // fortmatic: {
        //   package: Fortmatic,
        //   options: {
        //     // Mikko's TESTNET api key
        //     key: "pk_test_391E26A3B43A3350"
        //   }
        // }
    };

    web3Modal = new Web3Modal({
        cacheProvider: false, // optional
        providerOptions, // required
    });
 

}


 

/**
 * Connect wallet button pressed.
 */
async function onConnect() {

    buyNowChainId = document.getElementById("network_type").value;
   
    console.log(buyNowChainId);
    try {
        console.log("%c Line:135 🥛", "color:#fca650");
        provider = await web3Modal.connect();
        console.log("%c Line:136 🌭 provider", "color:#33a5ff", provider);
    } catch (e) {
         
        await alertMessage("Please Select Metamask or any Wallet Provider.", "info");
    }
    console.log(provider);
    if (provider) {
       

        const yourchainId = await getChainId();
        console.log("Your chain Id ",yourchainId); // Mehuls code
        if (yourchainId != undefined && yourchainId == buyNowChainId) {

            provider.on("accountsChanged", async function (accounts) {
                console.log("Acoounts Changed");
                if (accounts.length == 0) {
                    location.reload();
                } else {
                    let getaccounts = await provider.request({ method: 'eth_requestAccounts' });
                     
                    if (getaccounts.length != 0) {
                        // await reloadPageProperNetwork();
                    }

                    const web3 = new Web3(provider);
                    const myaccounts = await web3.eth.getAccounts();
                    
                    if (myaccounts[0] != null & myaccounts[0] != undefined) {
                        
                        var selectobject = document.getElementById("network_type");
                        console.log(selectobject.length);
                        for (var i=0; i<selectobject.length; i++) {
                        if (selectobject.options[i].value != buyNowChainId)
                        selectobject.remove(i);
                        }
 
                        document.getElementById("useraddress").value = myaccounts[0];
                        

                        let first_four = myaccounts[0].substring(0, 4);
                        let last_four = myaccounts[0].substr(myaccounts[0].length - 4);
                        
                        var element = document.getElementById("btn-disconnect");
                        element.style.display="block";
                        element.classList.remove("buyNow");
                        element.classList.add("buyNow3");
  
               
                        
                        document.querySelector("#btn-disconnect").innerHTML = 'Connected Wallet ' + first_four + '...' + last_four + '<br><a style="color:red;" href="javascript:void(0)" onclick="return onDisconnect()">Disconnect</a>';
                        await refreshPage('wallet-connected');
                    }
                }

            });

            // // Subscribe to chainId change
            provider.on("chainChanged", async function (chainId) {
                console.log("Acoounts Changed");

                if (chainDeteched[chainId] && chainDeteched[chainId] != undefined && chainId == buyNowChainId) {
                    if (provider != null) {
                        let accounts = await provider.request({ method: 'eth_requestAccounts' });
                        if (accounts.length != 0) {
                            // await reloadPageProperNetwork();
                        }
                    }
                } else {
                    
                    provider = null;
                    await refreshPage('first');
                    await alertMessage("Please Connect to " + chainDeteched[buyNowChainId] + " And try to connect.", "info");
                    setTimeout(() => {
                        location.reload();
                    }, 2000);

                }

            });

            await refreshAccountData();
        } else {
            provider = null;
            await alertMessage("Please Connect to " + chainDeteched[buyNowChainId] + " And try to connect.", "info");
        }

    }

}

/**
 * Fetch account data for UI when
 * - User switches accounts in wallet
 * - User switches networks in wallet
 * - User connects wallet initially
 */
async function refreshAccountData() {
    document.querySelector("#btn-connect").setAttribute("disabled", "disabled")
    await fetchAccountData(provider);
    document.querySelector("#btn-connect").removeAttribute("disabled")
}

/**
 * Kick in the UI action after Web3modal dialog has chosen a provider
 */
async function fetchAccountData() {
   
    // Get a Web3 instance for the wallet
    const web3 = new Web3(provider);


    // Get connected chain id from Ethereum node
    // const chainId = await web3.eth.getChainId(); // Sanju code
    const chainId = await getChainId(); // Mehul code change
    console.log("Your chain Id ", chainId); // Mehul code change
    console.log(chainId == buyNowChainId); // Mehul code change

    // Get list of accounts of the connected wallet
    const accounts = await web3.eth.getAccounts();
    const isMetaMaskConnected = () => accounts && accounts.length > 0

    if (isMetaMaskConnected()) {
        if (chainId != undefined) {
            if (chainId == buyNowChainId) {

                // if (accounts[0] != null & accounts[0] != undefined) {
                if (accounts[0] != null & accounts[0] != undefined) {

                    var selectobject = document.getElementById("network_type");
                    console.log(selectobject.length);
                    for (var i=0; i<selectobject.length; i++) {
                    if (selectobject.options[i].value != buyNowChainId)
                    selectobject.remove(i);
                    }

                    document.getElementById("useraddress").value = accounts[0];
                    document.getElementById("form-submit-btn").setAttribute("style", "visibility:visible");
                    
                    let first_four = accounts[0].substring(0, 4);
                    let last_four = accounts[0].substr(accounts[0].length - 4);
                     
                    var element = document.getElementById("btn-disconnect");
                    element.classList.remove("buyNow");
                    element.classList.add("buyNow3");
 
                    document.querySelector("#btn-disconnect").innerHTML = 'Connected Wallet ' + first_four + '...' + last_four + '<br><a style="color:red;" href="javascript:void(0)" onclick="return onDisconnect()">Disconnect</a>';
                    await refreshPage('wallet-connected');
                }

            } else {

                await alertMessage("Please Connect to " + chainDeteched[buyNowChainId] + ".", "info");
            }
        }
    }



}

async function getChainId() {
    let chainId = await provider.request({
        method: 'eth_chainId'
    });
    return chainId;
}

/**
 * Disconnect wallet button pressed.
 */
async function onDisconnect() {
     
    // TODO: Which providers have close method?
    if (provider.close) {
        console.log("Provider.close");
        await provider.close();

        // If the cached provider is not cleared,
        // WalletConnect will default to the existing session
        // and does not allow to re-scan the QR code with a new wallet.
        // Depending on your use case you may want or want not his behavir.
        await web3Modal.clearCachedProvider();
        provider = null;
        // location.reload();
    }

    location.reload();
}

 
 

/**
 * Main entry point.
 */
window.addEventListener('load', async () => {
    await init();
    document.querySelector("#btn-connect").addEventListener("click", onConnect);
    document.querySelector("#btn-disconnect").addEventListener("click", onDisconnect);
    mintGetNetworks();
    document.getElementById("form-submit-btn").setAttribute("style", "visibility:hidden");
 
});


function mintGetNetworks() {
    jQuery('.loader-network-icon').css('display', 'block');
    return jQuery.ajax({
      type: 'GET',
      dataType: 'JSON',
      url: adminAjaxpath,
      data: {
        action: 'mintGetNetworks'
      },
      success: function (res) {
        jQuery('.loader-network-icon').css('display', 'none');
        let option='';
        for (let i = 0; i < res.data.length; i++) {
            chainDeteched[res.data[i].chainid] = res.data[i].name;
          option +='<option value="'+res.data[i].chainid+'">'+res.data[i].name+'</option>';
        }
        jQuery('#network_type').html(option);
        
      },
      error:function (err) {
        console.log(err);
        return false;
      }
    });
  }


let validEmail = false;

function validateEmail(elem) {
    jQuery('#email_address-error').text('');
    var email_address = document.getElementById("email_address").value;
    var regEx = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/g;
    validEmail = regEx.test(email_address);

    if (!validEmail && (email_address != '')) {
        jQuery('#email_address_invalid').text('Invalid email format');

    } else {
        jQuery('#email_address_invalid').text('');
    }
}


jQuery(document).ready(function() {

    
    jQuery("#submit").click(function() {

        jQuery("#apiform").validate({
            rules: {
                'email_address': {
                    required: true,
                }
            },
            messages: {
                'email_address': "Please enter an email address.",
            },
            submitHandler: function(form) {
                var emailaddress = jQuery("#email_address").val();
                if (validEmail != false) {

                    jQuery('.loader-icon').css('display', 'block');
                    jQuery.ajax({
                        type: "POST",
                        url: adminAjaxpath,
                        data: {
                            action: 'activation_API',
                            email_address: emailaddress
                        },
                    }).success(function(data) {

                        if (data == 1) {
                            jQuery("#msg-div2").fadeIn(3000);
                            setTimeout(function() {
                            jQuery("#apiform").removeClass('show')
                                    .addClass('hide');
                            jQuery("#file_form").removeClass('hide')
                                    .addClass('show');
                            jQuery("#msg-div2").addClass("valid-msg");
                            jQuery("#msg-div2").removeClass("invalid-msg");
                            jQuery("#msg-div2").text(
                                'Email sent successfully. Please enter key here');
                            jQuery("#msg-div2").fadeOut(3000);
                            jQuery('.loader-icon').css('display', 'none');
                            jQuery("#email_address").val('');
                        }, 1000); // <-- time in milliseconds

                        } else {
                            jQuery("#msg-div").fadeIn(3000);
                            setTimeout(function() {
                                jQuery("#msg-div").addClass("invalid-msg");
                                jQuery("#msg-div").removeClass("valid-msg");
                                jQuery("#msg-div").text(
                                    'Email not send. Please check SMTP email setting.'
                                );
                                jQuery("#msg-div").fadeOut(3000);
                                jQuery('.loader-icon').css('display',
                                    'none');
                                jQuery("#email_address").val('');
                            }, 1000); // <-- time in milliseconds


                        }


                    });

                }


            }
        });




    });

    jQuery('.get-key-tag').click(function() {
        jQuery("#file_form").removeClass('show').addClass('hide');
        jQuery("#apiform").removeClass('hide').addClass('show');

    });

    jQuery('.get-email-tag').click(function() {
        jQuery("#apiform").removeClass('show').addClass('hide');
        jQuery("#file_form").removeClass('hide').addClass('show');

    });

    jQuery("#keysubmit").click(function() {
        jQuery("#msg-div2").text("");
        jQuery("#msg-div2").removeClass("invalid-msg");
        jQuery("#msg-div2").removeClass("valid-msg");
        var api_key = jQuery("#api_key").val();
        var network_type = jQuery("#network_type").val();
        var useraddress = jQuery("#useraddress").val();
        
        if (api_key != '') {
            jQuery('.loader-icon').css('display', 'block');
            jQuery.ajax({
                type: "POST",
                url: adminAjaxpath,
                data: {
                    action: 'updateActiovationStatus',
                    apikey: api_key,
                    network_type: network_type,
                    useraddress: useraddress
                },
            }).success(function(data) {
                let responseData = jQuery.parseJSON(data).content; 
                console.log(responseData);
                if(responseData.status == 'SUCCESS'){   
                    jQuery("#msg-div2").fadeIn(3000);
                    setTimeout(function() {
                        jQuery("#msg-div2").addClass("valid-msg");
                        jQuery("#msg-div2").removeClass("invalid-msg");
                        jQuery("#msg-div2").text(responseData.msg);
                        jQuery("#msg-div2").fadeOut(3000);

                        jQuery('.loader-icon').css('display', 'none');
                        jQuery('#btn-disconnect').trigger('click');

                    }, 1000); // <-- time in milliseconds

                } else if (responseData.status == 'FAILED') {
                    jQuery("#msg-div2").fadeIn(3000);
                    setTimeout(function() {
                        jQuery("#msg-div2").addClass("invalid-msg");
                        jQuery("#msg-div2").removeClass("valid-msg");
                        jQuery("#msg-div2").text(responseData.msg);
                        jQuery("#msg-div2").fadeOut(3000);
                        jQuery('.loader-icon').css('display', 'none');

                    }, 1000); // <-- time in milliseconds


                 }  


            });

            return false;
        } else {
            jQuery("#file_form").validate({
                rules: {
                    'api_key': {
                        required: true,

                    }
                },
                messages: {
                    'api_key': "Please enter an active Key.",
                }
            });
        }
    });


});

 
