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
        document.querySelector("#btn-deploy").style.display = "block";
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
  
    let response = {
          "trans_data" : []
      }
    let yourdata = [];
    let contract_address='';
    console.log(trans_id.transaction_hash.length);
      for(let i=0; i<trans_id.transaction_hash.length; i++){
 
        let chainData = await getChainIdData(trans_id.network_type[i]);  
        console.log("%c Line:177 🍖 chainData", "color:#ed9ec7", chainData);
   
        let web3;
        web3 = new Web3(chainData.data.rpc[0]);
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
  
        // trans_data[trans_id.transaction_hash[i]] = transaction_status;
        yourdata.push({
          transationHash:trans_id.transaction_hash[i],
          transactionStatus:transaction_status,
          contractAddress:contract_address
        })
   
      }
  
    response.trans_data = yourdata
    console.log('0',response);
    return response;
  }

async function getDeploymentTransactionIDs() {
   
    let ownerId='';
    if (myaccounts[0] != null & myaccounts[0] != undefined) {
        ownerId = myaccounts[0];
    } 

    let yourchainId = await getChainId();
    return jQuery.ajax({
        type: 'GET',
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
    document.getElementById("network_type").value = buyNowChainId;
     
    console.log(buyNowChainId);
    try {
        provider = await web3Modal.connect();
    } catch (e) {
        // console.log("Could not get a wallet connection", e);
        // return;
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
                    //location.reload();
                    alertMessage("could not find account", "info");
                } else {
                    let getaccounts = await provider.request({ method: 'eth_requestAccounts' });
                     
                    if (getaccounts.length != 0) {
                        // await reloadPageProperNetwork();
                    }

                    const web3 = new Web3(provider);
                    myaccounts = await web3.eth.getAccounts();
                     
                    if (myaccounts[0] != null & myaccounts[0] != undefined) {
                        var selectobject = document.getElementById("inputNetwork");
                        console.log(selectobject.length);
                        let first_four = myaccounts[0].substring(0, 4);
                        let last_four = myaccounts[0].substr(myaccounts[0].length - 4);
                          
                        var element = document.getElementById("btn-disconnect");
                        element.style.display="block";
                        element.classList.remove("buyNow");
                        element.classList.add("buyNow3");

                        document.getElementById("useraddress").value=myaccounts[0]; 
                        var deploy_element_form = document.getElementById("deploy-contract-form");
                        var deploy_element = document.getElementById("deploy-contract-section");
                        var error_section = document.getElementById("error-section");


                       console.log("onConnect");
                        
                        let contract_result = await getContractDeploymentData(myaccounts[0],buyNowChainId);
                        console.log(JSON.parse(contract_result).status);

                        if(JSON.parse(contract_result).status == 'Error'){ 
                        
                            deploy_element.classList.add("hide");
                            deploy_element_form.classList.remove("hide");
                            deploy_element_form.classList.add("hide");
                            error_section.classList.remove("hide");
                            error_section.innerHTML=' <div class="contract-deploy-addrs"><strong class="red">'+JSON.parse(contract_result).msg+'</strong></div>';
    
                        }else if(JSON.parse(contract_result).status == 'FAILED'){ 
                            
                            deploy_element.classList.add("hide");
                            deploy_element_form.classList.remove("hide");
                            deploy_element_form.classList.add("show");
                            error_section.classList.add("hide");
    
                        }else{

                        
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
         
         
                            if((deployer_address != myaccounts[0]) && (deployer_address != null)){
                               
                                deploy_element.classList.add("hide");
                                error_section.classList.remove("hide");
                                error_section.innerHTML=' <div class="contract-deploy-addrs"><strong class="red">Please connect with account: '+deployer_address+'</strong></div>';
                            }else{   
         
                                
                            if(caddress != null){
                                let first_four_caddress = caddress.substring(0, 18);
                                let last_four_caddress = caddress.substr(caddress.length - 18);
                                let newcaddress = first_four_caddress + '...' + last_four_caddress;
                                document.getElementById("existing_contract_address").value= newcaddress;
                            }
                               
                               error_section.classList.add("hide");
                               deploy_element.classList.remove("hide");
        
                                let deploy_contract_details='';
                                if(caddress != null && deployer_transaction_status == 'SUCCESS'){
                                let network_name = deployer_network_type;
                                deploy_contract_details +='<div class="full-container"> <div class="col-md-12" style="margin-left: -1rem;"> <h3 class="section-title"><b>Contract Details</b> </h3> <div id="card"> <div id="contract_detail" class="form-row"><div class="form-group col-md-3"> <label for="contract_network_type">Network Type:</label> </div><div class="form-group col-md-9"> '+network_name+' </div> <div class="form-group col-md-3"> <label for="contract_name">Contact Name:</label> </div><div class="form-group col-md-9"> '+deployer_contract_name+' </div><div class="form-group col-md-3"> <label for="contract_name">Contact Address:</label> </div><div class="form-group col-md-9" id="caddress"> '+caddress+' </div><div class="form-group col-md-3"> <label for="contract_name">Total Supply:</label> </div><div class="form-group col-md-9"> '+deployer_total_supply+' </div><div class="form-group col-md-3"> <label for="contract_name">Symbol:</label> </div><div class="form-group col-md-9"> '+deployer_contract_symbol+' </div><div class="form-group col-md-3"> <label for="contract_name">Price:</label> </div><div class="form-group col-md-9"> '+deployer_nftPrice+' </div><div class="form-group col-md-3"> <label for="contract_name">Max Mint:</label> </div><div class="form-group col-md-9"> '+deployer_max_mint+' </div><div class="form-group col-md-3"> <label for="contract_name">Price in wei:</label> </div><div class="form-group col-md-9"> '+deployer_nftPriceInWei+' </div><div class="form-group col-md-3"> <label for="contract_name">Transaction Status:</label> </div><div class="form-group col-md-9"> '+deployer_transaction_status+' </div><div class="form-group col-md-3"> <label for="contract_name">Deployer Address:</label> </div><div class="form-group col-md-9"> '+deployer_address+' </div><div class="form-group col-md-3"> <label for="contract_name">Transaction Hash:</label> </div><div class="form-group col-md-9 trans"> '+deployer_transaction_hash+' </div></div></div><div class="form-group col-md-12 delete_exit_contract"><strong style=" display: block; margin-top: 20px;">If you want to delete existing contract</strong> <button type="button" class="btn btn-primary round-box" name="deleteContract" onClick="deleteContract();"> Click me </button> </div></div></div>';
                                deploy_element.innerHTML = deploy_contract_details;    
                                }else{
                                    deploy_element.classList.add("hide");
                                    deploy_element_form.classList.remove("hide");
                                }
        
        
                              
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

                if (chainDeteched[chainId] && chainDeteched[chainId] != undefined && chainId == buyNowChainId) {
                    onConnect(); 
                    // if (provider != null) {
                    //     let accounts = await provider.request({ method: 'eth_requestAccounts' });
                    //     if (accounts.length != 0) {
                    //         // await reloadPageProperNetwork();
                    //     }
                    // }
                } else {
             
                    provider = null;
                    await refreshPage('first');
                    await alertMessage("Please Connect to " + chainDeteched[buyNowChainId] + " And try to connect.", "info");
                    document.querySelector("#btn-connect").style.display = "block";
                    document.querySelector("#btn-disconnect").style.display = "none";
                    var deploy_element = document.getElementById("deploy-contract-section");
                    var deploy_element_form = document.getElementById("deploy-contract-form");
                    deploy_element.classList.add("hide");
                    deploy_element_form.classList.add("hide");
                    document.getElementById("existing_contract_address").value="";
                   

                }

            });

            await refreshAccountData();
        } else {
            provider = null;
            await alertMessage("Please Connect to " + chainDeteched[buyNowChainId] + " And try to connect.", "info");
            document.querySelector("#btn-connect").style.display = "block";
            document.querySelector("#btn-disconnect").style.display = "none";
            var deploy_element = document.getElementById("deploy-contract-section");
            var deploy_element_form = document.getElementById("deploy-contract-form");
            deploy_element.classList.add("hide");
            deploy_element_form.classList.add("hide");
            document.getElementById("existing_contract_address").value="";
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
  
    const chainId = await getChainId(); 
   // Get list of accounts of the connected wallet
    const accounts = await web3.eth.getAccounts();
    myaccounts = accounts;
    const isMetaMaskConnected = () => accounts && accounts.length > 0

    if (isMetaMaskConnected()) {
        if (chainId != undefined) {
            if (chainId == buyNowChainId) {

              
                if (accounts[0] != null & accounts[0] != undefined) {
                  
                    var selectobject2 = document.getElementById("inputNetwork");
                  
                    let first_four = accounts[0].substring(0, 4);
                    let last_four = accounts[0].substr(accounts[0].length - 4);
                   

                    var element = document.getElementById("btn-disconnect");
                    element.classList.remove("buyNow");
                    element.classList.add("buyNow3");

                    document.getElementById("useraddress").value=accounts[0]; 
                    var deploy_element_form = document.getElementById("deploy-contract-form");
                    var deploy_element = document.getElementById("deploy-contract-section");
                    var error_section = document.getElementById("error-section");

                    console.log("Fetch");
                    let contract_res = await getContractDeploymentData(accounts[0],buyNowChainId);
                   // console.log(JSON.parse(contract_res).status);
                    if(JSON.parse(contract_res).status == 'Error'){ 
                        
                        deploy_element.classList.add("hide");
                        deploy_element_form.classList.remove("hide");
                        deploy_element_form.classList.add("hide");
                        error_section.classList.remove("hide");
                        error_section.innerHTML=' <div class="contract-deploy-addrs"><strong class="red">'+JSON.parse(contract_res).msg+'</strong></div>';

                    }else if(JSON.parse(contract_res).status == 'FAILED'){ 
                        
                        deploy_element.classList.add("hide");
                        deploy_element_form.classList.remove("hide");
                        error_section.classList.add("hide");
                       

                    }else{

                        console.log("00000");
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
  
 
                    if((deployer_address != accounts[0]) && (deployer_address != null)){
                        
                       
                        deploy_element.classList.add("hide");
                        error_section.classList.remove("hide");
                        error_section.innerHTML=' <div class="contract-deploy-addrs"><strong class="red">Please connect with account: '+deployer_address+'</strong></div>';
                    }else{   
 
                       
                    if(caddress != null){  console.log("33333");
                        let first_four_caddress = caddress.substring(0, 18);
                        let last_four_caddress = caddress.substr(caddress.length - 18);
                        let newcaddress = first_four_caddress + '...' + last_four_caddress;
                        document.getElementById("existing_contract_address").value= newcaddress;
                    }
                       
                        let chainData = await getChainIdData(chainId);  
                        ethrescanTxnUrl = chainData.data.explorerurl+'/';
                        
                       error_section.classList.add("hide");
                       deploy_element.classList.remove("hide");

                       console.log("deployer_transaction_status",deployer_transaction_status);
                        let deploy_contract_details='';
                        if(caddress != null && deployer_transaction_status == 'SUCCESS'){ console.log("44444");
                            let network_name = deployer_network_type;
                            deploy_contract_details +='<div class="full-container"> <div class="col-md-12" style="margin-left: -1rem;"> <h3 class="section-title"><b>Contract Details</b> </h3> <div id="card"> <div id="contract_detail" class="form-row"><div class="form-group col-md-3"> <label for="contract_network_type">Network Type:</label> </div><div class="form-group col-md-9"> '+network_name+' </div> <div class="form-group col-md-3"> <label for="contract_name">Contact Name:</label> </div><div class="form-group col-md-9"> '+deployer_contract_name+' </div><div class="form-group col-md-3"> <label for="contract_name">Contact Address:</label> </div><div class="form-group col-md-9" id="caddress"> '+caddress+' </div><div class="form-group col-md-3"> <label for="contract_name">Total Supply:</label> </div><div class="form-group col-md-9"> '+deployer_total_supply+' </div><div class="form-group col-md-3"> <label for="contract_name">Symbol:</label> </div><div class="form-group col-md-9"> '+deployer_contract_symbol+' </div><div class="form-group col-md-3"> <label for="contract_name">Price:</label> </div><div class="form-group col-md-9"> '+deployer_nftPrice+' </div><div class="form-group col-md-3"> <label for="contract_name">Max Mint:</label> </div><div class="form-group col-md-9"> '+deployer_max_mint+' </div><div class="form-group col-md-3"> <label for="contract_name">Price in wei:</label> </div><div class="form-group col-md-9"> '+deployer_nftPriceInWei+' </div><div class="form-group col-md-3"> <label for="contract_name">Transaction Status:</label> </div><div class="form-group col-md-9"> '+deployer_transaction_status+' </div><div class="form-group col-md-3"> <label for="contract_name">Deployer Address:</label> </div><div class="form-group col-md-9"> '+deployer_address+' </div><div class="form-group col-md-3"> <label for="contract_name">Transaction Hash:</label> </div><div class="form-group col-md-9 trans"> '+deployer_transaction_hash+' </div></div></div><div class="form-group col-md-12 delete_exit_contract"><strong style=" display: block; margin-top: 20px;">If you want to delete existing contract</strong> <button type="button" class="btn btn-primary round-box" name="deleteContract" onClick="deleteContract();"> Click me </button> </div></div></div>';
                            deploy_element.innerHTML = deploy_contract_details;    
                        }else{ console.log("55555");
                            deploy_element.classList.add("hide");
                            deploy_element_form.classList.remove("hide");
                            deploy_element_form.classList.add("hide");
                            error_section.classList.remove("hide");

                            let result_html = "Deployment transaction still in progress, to view your transaction <a style='color: ccc;' target='_blank' href='" + ethrescanTxnUrl +deployer_transaction_hash+ "'>Click Here! </a>";
                            result_html += " Once your transaction is confirmed, please reconnect your wallet.";   
                            error_section.innerHTML=' <div class="contract-deploy-addrs"><strong class="">'+result_html+'</strong></div>';


                        }


                      
                    }
                } 

                    document.querySelector("#btn-disconnect").innerHTML = 'Connected Wallet ' + first_four + '...' + last_four + '<br><a style="color:red;" href="javascript:void(0)" onclick="return onDisconnect()">Disconnect</a>';
                    await refreshPage('wallet-connected');

                    jQuery("#pageloader").css('display', 'none');

                    /*****Get data of user transaction********/
                        let yourResponse =  await getDeploymentTransactionIDs().then(async function (res) {
                            return res;
                        }).catch(async function (message) { console.log(message);
                            return false;
                        });


                       

                        console.log('yourResponse'+yourResponse);
                        console.log(JSON.parse(yourResponse).status);
                       
                        if(JSON.parse(yourResponse).status == 'SUCCESS'){
                        let response = JSON.parse(yourResponse);
                        const res = await getTransactionstatus(response);
                        console.log('getTransactionstatus',res);
                        await updateTransaction_status(res);
                        } 
                    /***** EOF Get data of user transaction********/

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
            console.log(`Error ${error}`);
            return false;
        },
    });
}



async function deployContract(name, symbol, totalSupply, maxMint, nftPrice) {
    
    $("#pageloader").css('display', 'block');

    console.log(provider);
    if (provider) {
         
        let chainId = await getChainId(); // Mehul code change
        console.log("%c Line:736 🎂 chainId", "color:#e41a6a", chainId);
      
      

        await getAbiBytecode(chainId); 
        const web3 = new Web3(provider);
        let nftPriceInWei = web3.utils.toWei(nftPrice, 'ether');
         

        let contract = new web3.eth.Contract(contractABI);
        let encodedConstArgs = web3.eth.abi.encodeParameters(['string', 'string', 'uint256', 'uint256', 'uint256'], [name, symbol, totalSupply, maxMint, nftPriceInWei]);

        let accounts = await web3.eth.getAccounts();
        console.log("Accounts:", accounts); //it will show all the Metamask web3 Provided accounts

        let defaultAccount = accounts[0];
        console.log("Default Account:", defaultAccount);  //to deploy the contract from default Account

 
        let chainData = await getChainIdData(chainId);  
        let chainIdName;
        if(chainData.msg == 'SUCCESS'){ 
            ethrescanTxnUrl = chainData.data.explorerurl+'/';
            chainIdName = chainData.data.name;
        }else if(chainData.msg == 'FAILED'){ 
            $("#pageloader").css('display', 'none');
            let result_html = "ChainID not available";
            alertify.alert(result_html);

        }

 
        await contract.deploy({
            data: contractBytecode,
            arguments: [name, symbol, totalSupply, maxMint, nftPriceInWei]
        }).send({ from: defaultAccount, gas: erc721.gasLimit,value: chainData.data.feeinwei })
            .on('transactionHash', async function (hash) {
                console.log("Your Transaction Hash ", hash);
                // Please Add This transaction Hash, Deployer address, encodedConstArgs in mint_contractDeployment & softMint_contractDeployment respective table and also add status as a PENDING.
                // also add collection name, symbol, totalsupply

                await addContractDeploymentData(name, symbol, totalSupply, maxMint, nftPrice, nftPriceInWei, hash, "PENDING", defaultAccount, encodedConstArgs, chainId, chainIdName);
                $("#pageloader").css('display', 'none');
                let result_html = "Deployment transaction still in progress, to view your transaction <a style='color: ccc;' target='_blank' href='" + ethrescanTxnUrl + hash + "'>Click Here! </a>";
                result_html += " Once your transaction is confirmed, please reconnect your wallet.";   
        
                alertify.alert(result_html, async function(){ await onDisconnect(); });
                 
            })
            .on('error', (error) => {
                
                var result_html = '';

                if (error.code == 4001) {
                    result_html = "Contract deployment transaction has been rejected by user.";
                    alertMessage("Contract deployment transaction has been rejected by user.", "info");
                } else {
                    result_html = "Contract deployment transaction failed. Please try again.";
                    alertMessage("Contract deployment transaction failed. Please try again.", "info");
                }
                $("#pageloader").css('display', 'none');
                alertify.alert(result_html);

                // Please Show alert Message that User denied the Transaction.
                return;
            });

    } else {
        $("#pageloader").css('display', 'none');
        alertify.alert("Please Connect to the MetaMask or any Ethereum wallet Provider.");
        await alertMessage("Please Connect to the MetaMask or any Ethereum wallet Provider.", "info");
        

    }
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



async function getAbiBytecode(chainId) {
   
    return jQuery.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: adminAjaxpath,
        data: {
          action: 'getAbiBytecode',
          chainId:chainId
        },
        success: async function (res) {
            console.log("%c Line:870 🍑 res", "color:#ffdd4d", res);

            contractABI = res.data.abi;
            contractBytecode = res.data.bytecode.object;
 
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



function deleteContract(){
   
    alertify.confirm("Are you sure you want to delete the active contract?", function (e) {
    if (e) {
        $("#pageloader").css('display', 'block');
        var inputNetwork = $("#inputNetwork").val();
        var caddress = $("#caddress").text();
         
        $.ajax({
        type: 'POST',
        url: adminAjaxpath,
        dataType: "json",
        data: {
            action: 'deleteContractData',
            network_type:inputNetwork,
            contract_address:caddress,
        },
        success: async function (data) {
            $("#pageloader").css('display', 'none');
            if (data.msg == 1) {
                   $('#deploy-contract-section').remove();
                   $("#existing_contract_address").val('');
                    
                   alertify.alert("Active contract deleted successfully.", async function(){ await onDisconnect(); });
                     
                }else{
                    alertify.alert("Active contract deletion failed."); 
                }
                
           
        },
        error: async function (error) {
            console.log(`Error ${error}`);
            return false;
        },
    });
 

    } 

    });



}


jQuery("#btn-deploy").click(async function(e) { 
    e.preventDefault();

    
    let account = jQuery("#useraddress").val();
    console.log('account',account);
    let network_type = jQuery("#network_type").val();
    let contract_name = jQuery("#contract_name").val();
    let symbol = jQuery("#contract_symbol").val();
    let totalSupply = jQuery("#total_supply").val();
    let max_mint = jQuery("#max_mint").val();
    let price = jQuery("#price").val();
    let cotract_name_flag = 0;
    let symbol_flag = 0;
    let totalSupply_flag = 0;
    let max_mint_flag = 0;
    let price_flag = 0;
    const pattern = /^[0-9]+$/;
    const patternForPrice = /^[0-9]{0,2}(\.[0-9]{1,18})?$|^(100)(\.[0]{1,4})?$/;
              
    if (contract_name == "") {

    $("#contract_name-error").css("display", "block");
    $("#contract_name-error").text("Please enter your contract name");
    cotract_name_flag = 1;
    } else {
    $("#contract_name-error").css("display", "none");
    cotract_name_flag = 0;
    }
    if (symbol == "") {

    $("#contract_symbol-error").css("display", "block");
    $("#contract_symbol-error").text("Please enter your symbol");
    symbol_flag = 1;
    } else {
    $("#contract_symbol-error").css("display", "none");
    symbol_flag = 0;
    }

    if (totalSupply == "") {
    $("#total_supply-error").css("display", "block");
     
    totalSupply_flag = 1;
    console.log("Your flag value is ", totalSupply_flag);
    } else {
     
        if(pattern.test(totalSupply)) {
            $("#total_supply-error").css("display", "none");
            totalSupply_flag = 0;
            console.log("Here at 244");
        } else {
            $("#total_supply-error").css("display", "block");
            $("#total_supply-error").text("Please enter valid Totalsupply value");
            totalSupply_flag = 1;
            console.log("Here at 249");
        }
     
    }

    if (max_mint == "") {

    $("#max_mint-error").css("display", "block");
    $("#max_mint-error").text("Please enter mint limit");
    max_mint_flag = 1;
     
    } else {
        if(pattern.test(max_mint)) {
            $("#max_mint-error").css("display", "none");
            max_mint_flag = 0;
           
            console.log(Number(max_mint) <= Number(totalSupply));
            console.log(!(Number(max_mint) <= Number(totalSupply)));
            if(pattern.test(totalSupply)){
                if(!(Number(max_mint) <= Number(totalSupply))) {
                $("#max_mint-error").css("display", "block");
                $("#max_mint-error").text("Max Mint value must be less or equal to the TotalSupply value");
                max_mint_flag = 1;
                console.log("Here at 271");
                }
            }
        } else {
            $("#max_mint-error").css("display", "block");
            $("#max_mint-error").text("Please enter valid Max mint value");
            max_mint_flag = 1;
             
        }
     
    }

    if (price == "") {

    $("#price-error").css("display", "block");
    $("#price-error").text("Please enter price");
    price_flag = 1;
     
    } else {
        if(patternForPrice.test(price)) {
            $("#price-error").css("display", "none");
            price_flag = 0;
            console.log("Here at 286");
        } else {
            $("#price-error").css("display", "block");
            $("#price-error").text("Please enter valid Price value");
            price_flag = 1;
            
        }
     
    }

 
    if(cotract_name_flag == 0 && symbol_flag == 0 && totalSupply_flag == 0 && max_mint_flag == 0 && price_flag == 0){
          
          let existingContract =  await checkExistingContract(account,network_type).then(async function (res) {
              return res;
          }).catch(async function (message) {
              return false;
          });

          
         console.log(JSON.parse(existingContract).status); 
         
          if(JSON.parse(existingContract).status == 'FAILED'){
              await deployContract(contract_name, symbol, totalSupply, max_mint, price);
          }else{
  
               
            alertify.confirm("Your contract deployment process is done", function (e) {
            if (e) {
                location.reload();
            }

            });
              
          } 
       
         
      }else{
        return false;
    }

  


});

 
async function checkExistingContract(account,network_type) {
    

    console.log(account);
    return jQuery.ajax({
        type: 'POST',
        url: adminAjaxpath,
        data: {
            action: 'getDeploycontract',
            myaccount:account,
            network_type:network_type,
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
