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

let buyNowChainId;
let ethrescanTxnUrl;
let contractABI;
let contractBytecode;
var myaccounts;

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
                infuraId: "a9d0dd8d98e943c985d03d6a78814c40",
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

    await getDBChainIds();
    console.log("Test");


}



async function updateTransaction_status(trans_data) {


    console.log(trans_data);
    
      jQuery.ajax({
          type: 'POST',
          url: adminAjaxpath,
          data: {
              action: 'updateDeploymentTransaction',
              post_data: trans_data 
          },
          success: function (res) {
             console.log(res);
              
            },
            statusCode: {
            404: function () {
           
            },
            200: function (res) {
              //console.log(res);
            }
            },
          error: async function (err) {
              console.log(err);
          }
      });
    
    
    
    }


async function getTransactionstatus(trans_id) {
    const INFURA_KEY = "6b7be9539bb843c9880051715220e6ca";
    
    console.log("trans_id:",trans_id);
    console.log(trans_id.transaction_hash[0]);
    console.log(trans_id.transaction_hash.length);
    //const trans_data = [];
    let response = {
          "trans_data" : []
      }
    let yourdata = [];
    let contract_address='';
    console.log(trans_id.transaction_hash.length);
      for(let i=0; i<trans_id.transaction_hash.length; i++){
 
        let chainData = await getChainIdData(trans_id.network_type[i]);  
        console.log('chainData',chainData);

        console.log('rpc',chainData.data.rpc);

        let web3;
        web3 = new Web3(chainData.data.rpc);
        
        var transaction_status = "";
        var receipt = await web3.eth.getTransactionReceipt(trans_id.transaction_hash[i]);
        console.log('receipt',receipt);
        if(receipt == null){
        transaction_status = "PENDING";
        } else {
        
        transaction_status = receipt.status;
       

        if(receipt.status == true){
          transaction_status = "SUCCESS";
          contract_address = receipt.contractAddress;
        } else {
            transaction_status = "FAILED";
        }
  
        }
   
        yourdata.push({
          transationHash:trans_id.transaction_hash[i],
          transactionStatus:transaction_status,
          contractAddress:contract_address
        })
  
    
  
      }
  
    response.trans_data = yourdata
  
    console.log(response);
    return response;
  }

async function getDeploymentTransactionIDs() {
   
    let ownerId='';
    if (myaccounts[0] != null & myaccounts[0] != undefined) {
        ownerId = myaccounts[0];
    } 

    const yourchainId = await getChainId();
    
    return jQuery.ajax({
        type: 'POST',
        url: adminAjaxpath,
        data: {
            action: 'deploymentTransaction',
            deployer: ownerId,
            chainid:yourchainId,
        },
        success: function (res) {
           return res;
          },
        
        error: async function (err) {
            console.log(err);
        }
    });
}


 

/**
 * Connect wallet button pressed.
 */
async function onConnect() {

    buyNowChainId = document.getElementById("inputNetwork").value;
    
    try {
        provider = await web3Modal.connect();
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
                   // location.reload();
                   alertMessage("could not find account", "info");
                } else {
                    let getaccounts = await provider.request({ method: 'eth_requestAccounts' });
                     
                    if (getaccounts.length != 0) {
                        // await reloadPageProperNetwork();
                    }

                    const web3 = new Web3(provider);
                    myaccounts = await web3.eth.getAccounts();
                    console.log("Hello: ",myaccounts);
                    if (myaccounts[0] != null & myaccounts[0] != undefined) {
                        
                        // var selectobject = document.getElementById("inputNetwork");
                        // console.log(selectobject.length);
                        // for (var i=0; i<selectobject.length; i++) {
                        // if (selectobject.options[i].value != buyNowChainId)
                        // selectobject.remove(i);
                        // }


                        let first_four = myaccounts[0].substring(0, 4);
                        let last_four = myaccounts[0].substr(myaccounts[0].length - 4);
                        console.log( first_four );
                       
                        var element = document.getElementById("btn-disconnect");
                        element.style.display="block";
                        element.classList.remove("buyNow");
                        element.classList.add("buyNow3");

                        var deploy_element = document.getElementById("deploy-contract-section");
                        var error_section = document.getElementById("error-section");
                        
                        let contract_result = await getContractDeploymentData(myaccounts[0],buyNowChainId);

                        console.log("000000");
                        console.log(contract_result);
                        var metadata_element = document.getElementById("upload-metadata-section");
                        var error_section = document.getElementById("error-section");

                        if(JSON.parse(contract_result).status == 'Error'){ 
                            
                          
                            metadata_element.classList.add("hide");
                            error_section.classList.remove("hide");
                            error_section.innerHTML=' <div class="contract-deploy-addrs 12"><strong class="red">'+JSON.parse(contract_result).msg+'</strong></div>';
                            document.getElementById("existing_contract_address").value= "";
                            document.getElementById("contract_address").value="";

                        }else if(JSON.parse(contract_result).status == 'SUCCESS'){ 


                            metadata_element.classList.remove("hide");
                            error_section.classList.add("hide");

                            let caddress = JSON.parse(contract_result).contract_address;
                            let deployer_address = JSON.parse(contract_result).deployer_address;
        
                            let deployer_contract_name = JSON.parse(contract_result).deployer_contract_name;
                            let deployer_contract_symbol = JSON.parse(contract_result).deployer_contract_symbol;
                            let deployer_total_supply = JSON.parse(contract_result).deployer_total_supply;
                            let deployer_max_mint = JSON.parse(contract_result).deployer_max_mint;
                            let deployer_nftPrice = JSON.parse(contract_result).deployer_nftPrice;
                            let deployer_nftPriceInWei = JSON.parse(contract_result).deployer_nftPriceInWei;
                            let deployer_transaction_hash = JSON.parse(contract_result).deployer_transaction_hash;
                            let deployer_transaction_status = JSON.parse(contract_result).deployer_transaction_status;
                            let deployer_network_type = JSON.parse(contract_result).deployer_network_type;
                            let deployer_encodedConstArgs = JSON.parse(contract_result).deployer_encodedConstArgs;
                            console.log('deployer_total_supply',deployer_total_supply);
                             
                            if(caddress != null){
                                let first_four_caddress = caddress.substring(0, 18);
                                let last_four_caddress = caddress.substr(caddress.length - 18);
                                let newcaddress = first_four_caddress + '...' + last_four_caddress;
                                document.getElementById("existing_contract_address").value= newcaddress;
                                document.getElementById("contract_address").value=caddress;
                             
                            }
                         
                        }
 
                        document.querySelector("#btn-disconnect").innerHTML = 'Connected Wallet ' + first_four + '...' + last_four + '<br><a style="color:red;" href="javascript:void(0)" onclick="return onDisconnect()">Disconnect</a>';
                        await refreshPage('wallet-connected');
                        jQuery("#pageloader").css('display', 'none');

                    }
                }

            });

            // // Subscribe to chainId change
            provider.on("chainChanged", async function (chainId) {
                console.log("Acoounts Changed");

                if (chainDeteched[chainId] && chainDeteched[chainId] != undefined && chainId == buyNowChainId) {  console.log("Acoounts Changed12");
                console.log("%c Line:361 🍆 provider", "color:#33a5ff", provider);  
               // await refreshAccountData();
                onConnect(); 
                // if (provider != null) { 
                        
                //         console.log("%c Line:361 🍰 provider", "color:#fca650", chainId);
                //         let accounts = await provider.request({ method: 'eth_requestAccounts' });

                //         if (accounts.length != 0) {
                //             // await reloadPageProperNetwork();
                //             console.log("Change change in");
                //             onConnect(); 
                //         }
                //     }
                } else {
                
                    provider = null;
                    await refreshPage('first');
                    await alertMessage("Please Connect to " + chainDeteched[buyNowChainId] + " And try to connect.", "info");
                    document.querySelector("#btn-connect").style.display = "block";
                    document.querySelector("#btn-disconnect").style.display = "none";
                    var metadata_element = document.getElementById("upload-metadata-section");
                    metadata_element.classList.add("hide");
                    document.getElementById("existing_contract_address").value= "";
                    document.getElementById("contract_address").value="";
                  

                }

            });

            await refreshAccountData();
        } else {
            provider = null;
            await alertMessage("Please Connect to " + chainDeteched[buyNowChainId] + " And try to connect.", "info");
            document.querySelector("#btn-connect").style.display = "block";
            document.querySelector("#btn-disconnect").style.display = "none";
            var metadata_element = document.getElementById("upload-metadata-section");
            metadata_element.classList.add("hide");
            document.getElementById("existing_contract_address").value= "";
            document.getElementById("contract_address").value="";
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
    jQuery("#pageloader").css('display', 'block');

    // Get connected chain id from Ethereum node
    // const chainId = await web3.eth.getChainId(); // Sanju code
    const chainId = await getChainId(); // Mehul code change
    console.log("Your chain Id fetch ", chainId); // Mehul code change
    console.log(chainId == buyNowChainId); // Mehul code change

    // Get list of accounts of the connected wallet
    const accounts = await web3.eth.getAccounts();
    myaccounts = accounts;
    const isMetaMaskConnected = () => accounts && accounts.length > 0

    if (isMetaMaskConnected()) {
        if (chainId != undefined) {
            if (chainId == buyNowChainId) {
 
                if (accounts[0] != null & accounts[0] != undefined) {

                    // var selectobject = document.getElementById("inputNetwork");
                    // console.log(selectobject.length);
                    // for (var i=0; i<selectobject.length; i++) {
                    //     console.log(selectobject.options[i].value);
                    // if (selectobject.options[i].value != buyNowChainId)
                    // selectobject.remove(i);
                    // }
 
                    let first_four = accounts[0].substring(0, 4);
                    let last_four = accounts[0].substr(accounts[0].length - 4);
                    var element = document.getElementById("btn-disconnect");
                    element.classList.remove("buyNow");
                    element.classList.add("buyNow3");

                   

                    let contract_res = await getContractDeploymentData(accounts[0],buyNowChainId);
                    console.log("%c Line:453 🍫 contract_res", "color:#e41a6a", contract_res);
                    
                    var metadata_element = document.getElementById("upload-metadata-section");
                    var error_section = document.getElementById("error-section");
                    if(JSON.parse(contract_res).status == 'Error' || JSON.parse(contract_res).status == 'FAILED'){ 
                        
                        metadata_element.classList.add("hide");
                        error_section.classList.remove("hide");
                        error_section.innerHTML=' <div class="contract-deploy-addrs 23"><strong class="red">'+JSON.parse(contract_res).msg+'</strong></div>';
                        document.getElementById("existing_contract_address").value= "";
                        document.getElementById("contract_address").value="";
                    }else if(JSON.parse(contract_res).status == 'SUCCESS'){ 
                        
                    metadata_element.classList.remove("hide");
                    error_section.classList.add("hide");
                        
                    let caddress = JSON.parse(contract_res).contract_address;
                    let deployer_address = JSON.parse(contract_res).deployer_address;

                    let deployer_contract_name = JSON.parse(contract_res).deployer_contract_name;
                    let deployer_contract_symbol = JSON.parse(contract_res).deployer_contract_symbol;
                    let deployer_total_supply = JSON.parse(contract_res).deployer_total_supply;
                    let deployer_max_mint = JSON.parse(contract_res).deployer_max_mint;
                    let deployer_nftPrice = JSON.parse(contract_res).deployer_nftPrice;
                    let deployer_nftPriceInWei = JSON.parse(contract_res).deployer_nftPriceInWei;
                    let deployer_transaction_hash = JSON.parse(contract_res).deployer_transaction_hash;
                    let deployer_transaction_status = JSON.parse(contract_res).deployer_transaction_status;
                    let deployer_network_type = JSON.parse(contract_res).deployer_network_type;
                    let deployer_encodedConstArgs = JSON.parse(contract_res).deployer_encodedConstArgs;
                    console.log('deployer_total_supply',deployer_total_supply);
                   
                    if(caddress != null){
                        let first_four_caddress = caddress.substring(0, 18);
                        let last_four_caddress = caddress.substr(caddress.length - 18);
                        let newcaddress = first_four_caddress + '...' + last_four_caddress;
                        document.getElementById("existing_contract_address").value= newcaddress;
                        document.getElementById("contract_address").value=caddress;
 
                    }
  
                } 

                    document.querySelector("#btn-disconnect").innerHTML = 'Connected Wallet ' + first_four + '...' + last_four + '<br><a style="color:red;" href="javascript:void(0)" onclick="return onDisconnect()">Disconnect</a>';
                    await refreshPage('wallet-connected');
                    jQuery("#pageloader").css('display', 'none');
                }

            } else {

                await alertMessage("Please Connect to " + chainDeteched[buyNowChainId] + "  And try to connect.", "info");
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
    console.log("Here")

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

 

async function addContractDeploymentData(name, symbol, totalSupply, maxMint, nftPrice, nftPriceInWei, hash, status, defaultAccount, encodedConstArgs, chainId,chainIdName) {
    return jQuery.ajax({
        type: 'POST',
        url: adminAjaxpath,
        dataType: "json",
        data: {
            action: 'deploycontract',
            contract_name: name,
            symbol:symbol,
            totalSupply:totalSupply,
            max_mint:maxMint,
            nftPrice:nftPrice,
            nftPriceInWei:nftPriceInWei,
            hash:hash,
            status:status,
            defaultAccount:defaultAccount,
            chainId:chainId,
            chainIdName:chainIdName,
            encodedConstArgs:encodedConstArgs
            
        },
        success: async function (data) {
             
            console.log(data);
            return data;
        },
        error: async function (error) {
            console.log(`Error ${error}`);
            return false;
        },
    });
}


async function getContractDeploymentData(account,buyNowChainId) {
    
    return jQuery.ajax({
        type: 'POST',
        url: adminAjaxpath,
        data: {
            action: 'getDeploycontract',
            myaccount:account,
            network_type:buyNowChainId,
        },
        success: async function (data) {
             
            return data;
        },
        error: async function (error) {
          
            return false;
        },
    });
}


 

async function getChainIdData(chainId) {
    
    return jQuery.ajax({
        type: 'POST',
        url: adminAjaxpath,
        dataType: "json",
        data: {
            action: 'getChainIdData',
            chainId:chainId,     
        },
        success: async function (data) {
            return data;
        },
        error: async function (error) {
            return false;
        },
    });
}
 

async function getDBChainIds() {
    jQuery('.loader-network-icon').css('display', 'block');
    return jQuery.ajax({
      type: 'GET',
      dataType: 'JSON',
      url: adminAjaxpath,
      data: {
        action: 'mintGetNetworks'
      },
      success: async function (res) {
        jQuery('.loader-network-icon').css('display', 'none');
        let option='';
        for (let i = 0; i < res.data.length; i++) {
          chainDeteched[res.data[i].chainid] = res.data[i].name;
          option +='<option value="'+res.data[i].chainid+'">'+res.data[i].name+'</option>';
        }
        jQuery('#inputNetwork').html(option);
        
      },
      error: async function (err) {
        console.log(err);
        return false;
      }
    });
   }

 
/**
 * Main entry point.
 */
window.addEventListener('load', async () => {
    await init();
    document.querySelector("#btn-connect").addEventListener("click", onConnect);
    document.querySelector("#btn-disconnect").addEventListener("click", onDisconnect);
     
});
 
async function createMetaDataAPI(metadata_outer_array,token){
    console.log("%c Line:788 🥕 metadata_outer_array", "color:#f5ce50", metadata_outer_array[0].metadata);
    console.log("%c Line:788 🥕 metadata_outer_array", "color:#f5ce50", metadata_outer_array[0]);
    let settings = {
    "url": externalURL+"/api/tokenmetadata",
    "method": "POST",
    "timeout": 0,
    "headers": {
    "Authorization": `Bearer ${token}`,
    "Content-Type": "application/json"
    },
    "data": JSON.stringify(metadata_outer_array[0]),
    
    };

    jQuery.ajax(settings).done( async function (response) {
        console.log('response',response);
         
        jQuery("#pageloader").css('display', 'none');
       
       let result_html = "";
       document.getElementById('res1').innerHTML = "";
       result_html += "<ul>";
       result_html +="<li style='text-align:left;'>"+response.msg+"</li>";
       result_html += "</ul>";
       document.getElementById('res2').innerHTML = result_html;
       let successModal = document.getElementById('mintsuccessModal');
       successModal.click();


     
    }).fail( async function(response, textStatus, xhr) {
       let res = JSON.parse(response.responseText);
       console.log('res',res);
  
      // alertify.alert(res.msg, async function(){ await onDisconnect(); });
     //  return res;
     let result_html = "";

     jQuery("#pageloader").css('display', 'none');

     document.getElementById('res1').innerHTML = "";
     result_html += "<ul>";
    // result_html += "<li style='text-align:left;'> An error occured. </li>";
     result_html +="<li style='text-align:left;'>"+res.msg+"</li>";
     result_html += "</ul>";
     document.getElementById('res2').innerHTML = result_html;
     let successModal = document.getElementById('mintsuccessModal');
     successModal.click();

   });
  

}


var valid = false;
var valid_json = false;



function checkFileUploadExt(fieldObj) {
    // let spinnerDiv = document.getElementById("divLoading");
    // spinnerDiv.classList.add('show');
    jQuery("#pageloader").css('display', 'block');
    var control = document.getElementById("image");
    var filelength = control.files.length;
    var allowed_extensions = new Array("jpg", "jpeg", "png");

    for (var i = 0; i < control.files.length; i++) {
        var file = control.files[i];
        var FileName = file.name;
        var FileExt = FileName.substr(FileName.lastIndexOf('.') + 1);
        console.log(FileExt);
        console.log(allowed_extensions.includes(FileExt));

        if (!allowed_extensions.includes(FileExt)) {
            var error = "Please ensure your file(s) is/are only in jpg, jpeg and png format.\n\n";
          //  alertify.alert(error);
          let result_html = "";

          jQuery("#pageloader").css('display', 'none');
     
          document.getElementById('res1').innerHTML = "Process failed with error.";
          result_html += "<ul>";
         // result_html += "<li style='text-align:left;'> An error occured. </li>";
          result_html +="<li style='text-align:left;'>"+error+"</li>";
          result_html += "</ul>";
          document.getElementById('res2').innerHTML = result_html;
          let successModal = document.getElementById('mintsuccessModal');
          successModal.click();
          
            return false;
        } else {
            valid = true;
            jQuery("#pageloader").css('display', 'none');

            var metadata_type = jQuery('input[name="metadata_type"]:checked').val();
            console.log(metadata_type);
            if (metadata_type == 'Image') {
            jQuery('#Imgbut_submit').removeAttr("disabled");
            }
            else if(metadata_type == 'metadatawithimage')
            {
                if (valid == true && valid_json == true) {
                    jQuery('#Imgbut_submit').removeAttr("disabled");
                }
    
            }

            return;
        }

    }
    
}


function checkJsonFileUploadExt(fieldObj) {
    // let spinnerDiv = document.getElementById("divLoading");
    // spinnerDiv.classList.add('show');
    jQuery("#pageloader").css('display', 'block');
    var control = document.getElementById("metadata");
    var filelength = control.files.length;
    var allowed_extensions = new Array("json");

    for (var i = 0; i < control.files.length; i++) {
        var file = control.files[i];
        var FileName = file.name;
        var FileExt = FileName.substr(FileName.lastIndexOf('.') + 1);
        console.log('FileExt',FileExt);
        console.log('allowed_extensions',allowed_extensions.includes(FileExt));

        if (!allowed_extensions.includes(FileExt)) {
            var error = "Please ensure your file(s) is/are in json format only. \n\n";
            //alertify.alert(error);

            let result_html = "";

            jQuery("#pageloader").css('display', 'none');
       
            document.getElementById('res1').innerHTML = "Process failed with error.";
            result_html += "<ul>";
           // result_html += "<li style='text-align:left;'> An error occured. </li>";
            result_html +="<li style='text-align:left;'>"+error+"</li>";
            result_html += "</ul>";
            document.getElementById('res2').innerHTML = result_html;
            let successModal = document.getElementById('mintsuccessModal');
            successModal.click();

            return false;
        } else {
            valid_json = true;
            jQuery("#pageloader").css('display', 'none');
            var metadata_type = jQuery('input[name="metadata_type"]:checked').val();
            if(metadata_type == 'metadatawithimage')
            {
                if (valid == true && valid_json == true) {
                    jQuery('#Imgbut_submit').removeAttr("disabled");
                }
    
            }
            return;
        }

    }
    
    
}
 

jQuery(document).ready(function() {




    jQuery(".close").click(function() {
        location.reload(true);
    });

    jQuery(".buy_pop").click(function() {
        location.reload(true);
    });

    
    jQuery("input[name$='metadata_type']").click(function() {
        var metadata_type = jQuery(this).val();
         
        if (metadata_type == 'Image') {
            jQuery("#ismetadata").val(0);
            jQuery(".metadata-box").hide();
            jQuery(this).css('border','2px solid #000;');
        } else if (metadata_type == 'metadatawithimage') {
            jQuery("#ismetadata").val(1);
            jQuery(".metadata-box").show();
        }

    });
 

    jQuery("#Imgbut_submit").click(function() {

        let flag = 0;
        var metadata_type = jQuery('input[name="metadata_type"]:checked').val();

        var serverType = jQuery("#serverType").val();

        var pinataKey = jQuery("#pinataKey").val();
        var pinataSecret = jQuery("#pinataSecret").val();
        var customgateway = jQuery("#getway_type").val();

        if(metadata_type == 'metadatawithimage')
        {
            if (valid == true && valid_json == true) {
                flag = 1;
            }

        }else if(metadata_type == 'Image'){

            if (valid == true) {
                flag = 1;
            }

        }
        console.log('pinataKey',pinataKey); 
        console.log('pinataSecret',pinataSecret); 
        console.log('customgateway',customgateway); 
        if (serverType == 'pinata') {
            if(pinataKey == '' || pinataSecret== '' || customgateway== ''){
                var error_text = "Please add Pinata Key, Pinata Secret Key and Gateway Type properly on the <a target='_blank' href='admin.php?page=mintnft'>Setting Page</a> \n\n";
                //alertify.alert(error_text);

                 
                let result_html = "";

                jQuery("#pageloader").css('display', 'none');
        
                document.getElementById('res1').innerHTML = "Process failed with error.";
                result_html += "<ul>";
                //result_html += "<li style='text-align:left;'> An error occured. </li>";
                result_html +="<li style='text-align:left;'>"+error_text+"</li>";
                result_html += "</ul>";
                document.getElementById('res2').innerHTML = result_html;
                let successModal = document.getElementById('mintsuccessModal');
                successModal.click();

                    
                    flag=0;
                    return false;
                }

        }
        if (flag) {
        // spinnerDiv = document.getElementById("divLoading");
        // spinnerDiv.classList.add('show');
        jQuery("#pageloader").css('display', 'block');
        var token = jQuery("#token").val();
        var useraddress = jQuery("#useraddress").val();
        const image_prefix = jQuery(".image_prefix").val();

        var imagedescription = jQuery(".short_desc").val();
        var ismetadata = jQuery("#ismetadata").val();
        if(ismetadata == 1){
            ismetadataVal = true;
        }else{
            ismetadataVal = false;
        }
      
        
        var contract_address = jQuery("#contract_address").val();
        
        var form = new FormData();
         


        if (serverType == 'pinata') {
            
            var url = externalURL+'/api/upload/uploadtopinata';
            form.append('pinatakey', pinataKey);
            form.append('pinatasecret', pinataSecret);
            form.append('customgateway', customgateway);
            form.append('title', 'PintaImage');
            console.log('customgateway',customgateway);

        } else {
            var url = externalURL+'/api/upload/uploadtoipfs';
        }
 
        form.append("ismetadata", ismetadataVal);
        form.append("imagename", image_prefix);
        form.append("imagedescription", imagedescription);
        form.append("contractaddress", contract_address);
        form.append("useraddress", useraddress);
        if (metadata_type == 'metadatawithimage') {

            var Mtotalfiles = document.getElementById('metadata').files.length;
            for (let i = 0; i < Mtotalfiles; i++) {
                form.append("metadata", document.getElementById('metadata').files[i]);
            }

            var totalfiles = document.getElementById('image').files.length;
            for (let i = 0; i < totalfiles; i++) {
                form.append("image", document.getElementById('image').files[i]);
            }


        } else {

            var totalfiles = document.getElementById('image').files.length;
            for (let i = 0; i < totalfiles; i++) {
               
                form.append("image", document.getElementById('image').files[i]);
            }

        }

        form.append("prefix", image_prefix);

        var settings = {
            "url": url,
            "method": "POST",
            "timeout": 0,
            "headers": {
                "Authorization": `Bearer ${token}`
            },
            "processData": false,
            "mimeType": "multipart/form-data",
            "contentType": false,
            "data": form
        };

        jQuery.ajax(settings).done(async function(response) {
            let res = JSON.parse(response);
           console.log('status',res.status);

          

            if(res.status == 'FAILED'){
                console.log("%c Line:1059 🍕 msg", "color:#ed9ec7", res);
                console.log("%c Line:1059 🧀 data", "color:#7f2b82", res.data);
               // alertify.alert(res.msg);
               let result_html = "";

               jQuery("#pageloader").css('display', 'none');

                document.getElementById('res1').innerHTML = "";
                result_html += "<ul>";
              //  result_html += "<li style='text-align:left;'> An error occured. </li>";
                result_html += "<li style='text-align:left;'> " + res.msg +"</li>";
              
                result_html += "</ul>";
                document.getElementById('res2').innerHTML = result_html;
                let successModal = document.getElementById('mintsuccessModal');
                successModal.click();

            }else if(res.status == 'SUCCESS'){
               
                    let imagelength = res.data.imagecid.length;
                    let cnt_image_prefix = image_prefix.length;
                    let metadata_array = [];
                    let metadata_outer_array=[];
                    for (let j = 0; j < imagelength; j++) {

                        let lastSegment = res.data.imagecid[j].split("/").pop();
                        let first = lastSegment.split(".");
                        let imageName = first[0];
                       
                        var tokenid = imageName.slice(cnt_image_prefix);
                         
                        metadata_array.push({
                            imagename: imageName,
                            imagecid: res.data.imagecid[j],
                            metadatacid: res.data.metadatacid[j],
                            contractaddress: contract_address,
                            tokenid: tokenid,
                            useraddress:useraddress
                             
                        });
                         
                    }
                    metadata_outer_array.push({ metadata:metadata_array });
                    console.log("%c Line:1039 🍪 metadata_outer_array", "color:#4fff4B", metadata_outer_array);
                    await createMetaDataAPI(metadata_outer_array,token);
 
                    return;
                
            }
            
        }).fail(function(response, textStatus, xhr) {
            let res = JSON.parse(response.responseText);
            //spinnerDiv.classList.remove('show');
          //  alertify.alert(res.msg, async function(){ await onDisconnect(); });
           
            
            let result_html = "";

            jQuery("#pageloader").css('display', 'none');
       
            document.getElementById('res1').innerHTML = "";
            result_html += "<ul>";
            //result_html += "<li style='text-align:left;'> An error occured. </li>";
            result_html +="<li style='text-align:left;'>"+res.msg+"</li>";
            result_html += "</ul>";
            document.getElementById('res2').innerHTML = result_html;
            let successModal = document.getElementById('mintsuccessModal');
            successModal.click();

           
       })


    } else {
           
        var error_text = "Please upload file(s). \n\n";
       // alertify.alert(error_text);
       let result_html = "";

       jQuery("#pageloader").css('display', 'none');
  
       document.getElementById('res1').innerHTML = "Process failed with error.";
       result_html += "<ul>";
       //result_html += "<li style='text-align:left;'> An error occured. </li>";
       result_html +="<li style='text-align:left;'>"+error_text+"</li>";
       result_html += "</ul>";
       document.getElementById('res2').innerHTML = result_html;
       let successModal = document.getElementById('mintsuccessModal');
       successModal.click();

        return false;
    }



    });

 


});

