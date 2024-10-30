<?php

/**
 * Template Name: Mint Page
 */

//get_header('new');
?>
<!doctype html>
<html lang="en">
<?php //wp_head();
 
?>

<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> Mint Page</title>
    <link rel='stylesheet' id='MintNFT_mint_css-css'
        href='<?php echo plugin_dir_url( __DIR__ )?>/assets/css/mint.css' media='all' />
    <link rel='stylesheet' id='bootstrap-css'
        href='<?php echo plugin_dir_url( __DIR__ )?>/assets/css/NFT/bootstrap.min.css' media='all' />
    <link rel='stylesheet' id='font-awesome-css'
        href='<?php echo plugin_dir_url( __DIR__ )?>/assets/fonts/css/all.css' media='all' />
    <link rel='stylesheet' id='toastr-min-css-css'
        href='<?php echo plugin_dir_url( __DIR__ )?>/assets/css/NFT/toastr.min.css' media='all' />

    <script src='<?php echo site_url()?>/wp-includes/js/jquery/jquery.min.js' id='jquery-core-js'></script>
    <script src='<?php echo site_url()?>/wp-includes/js/jquery/jquery-migrate.min.js' id='jquery-migrate-js'>
    </script>
    <script src='<?php echo plugin_dir_url( __DIR__ )?>/assets/js/NFT/bootstrap.min.js' id='bootstrap-js'>
    </script>
    <script src='<?php echo plugin_dir_url( __DIR__ )?>/assets/js/NFT/web3.min.js' id='web3-min-js'></script>
    <script src='<?php echo plugin_dir_url( __DIR__ )?>/assets/js/NFT/index.js' id='index-js-js'></script>
    <script src='<?php echo plugin_dir_url( __DIR__ )?>/assets/js/NFT/index.min.js' id='index-min-js-js'>
    </script>
    <script src='<?php echo plugin_dir_url( __DIR__ )?>/assets/js/NFT/wallet_index.min.js'
        id='wallet-index-min-js-js'></script>
    <script src='<?php echo plugin_dir_url( __DIR__ )?>/assets/js/NFT/fortmatic.js' id='fortmatic-js-js'>
    </script>
    <script src='<?php echo plugin_dir_url( __DIR__ )?>/assets/js/NFT/toastr.min.js' id='toastr-js-js'>
    </script>

</head>
<style>
html,
body {
    margin-top: 0px !important;
}
</style>
<?php

$mc_options = get_option( 'MintNFT_option_name' );  
$techNFTHeading = $mc_options['MintNFT_heading_field'];
    
$logo = $mc_options['MintNFT_image_field']; 
$description1 = $mc_options['MintNFT_mintdesc_field'];
$logoimage = $mc_options['MintNFT_logoimage_field']; 
$bg_image = $mc_options['MintNFT_bgimage_field'];
 
?>

<script src="https://cdn.jsdelivr.net/npm/web3@1.9.0/dist/web3.min.js"></script>
<script>
let techPagePath = window.location.pathname

let adminAjaxpath = '<?php echo admin_url();?>/admin-ajax.php';
// console.log("path : " + techPagePath);
// Global Variable to maintain accounts
let accounts;
let erc721 = {
    maxSupply: 10000,
    maxPurchase: 5,
    salePrice: 0,
    accountConnected: false,
    provider: null,
    totalNFT: 10000,
    totalReserved: 100
};

let chainDeteched = {
    "0x1": "Ethereum Mainnet Network",
    "0x3": "Ropsten Test Network",
    "0x4": "Rinkeby Test Network",
    "0x5": "Goerli Test Network",
    "0x2a": "Kovan Test Network",
    "0x539": "Ganache Network",
    "0x13881": "Mumbai",
}
let externalURL = '<?php echo externalURL?>';


let mainTechDomain = "<?php echo admin_url();?>/";
let mainTechDomainMiintPagePath = "<?php echo admin_url();?>/mint/";
let plugin_path = '<?php echo plugin_dir_url( __DIR__ ) ?>';
let techTxnUrl;
let techNFTContractABI;
let techNFTContractAddress;

const Web3Modal = window.Web3Modal.default;
const WalletConnectProvider = window.WalletConnectProvider.default;
const Fortmatic = window.Fortmatic;
const evmChains = window.evmChains;

// Web3modal instance
let web3Modal

// Chosen wallet provider given by the dialog window
let provider;


// Address of the selected account
let selectedAccount;

let web3;

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


async function getChainIdData(chainId) {

    return jQuery.ajax({
        type: 'POST',
        url: adminAjaxpath,
        dataType: "json",
        data: {
            action: 'getChainIdData',
            chainId: chainId,
        },
        success: async function(data) {

            console.log(data);
            return data;
        },
        error: async function(error) {
            console.log(`Error ${error}`);
            return false;
        },
    });
}



async function getTotalNFTs(contractaddress) {

    return jQuery.ajax({
        type: 'GET',
        url: adminAjaxpath,
        dataType: "json",
        data: {
            action: 'getTotalNFTs',
            contractaddress: contractaddress,

        },
        success: async function(response) {
            console.log('getTotalNFTs', response.data);

            if (response.data.length > 0) {

                document.querySelector("#totalNFTNo").innerHTML = response.data.length;
                return response.data.length;
            } else {
                document.querySelector("#totalNFTNo").innerHTML = 0;
                
                jQuery("#nft-info").remove();
                jQuery("#max_nftQty").remove();
                
                jQuery(".newsLetterForm").remove();
                jQuery("#mint_price").append(
                "<div class='error-div'>No NFT's are available for Minting</div>"
                );
                spinnerDiv.classList.remove('show');
                return false;
            }
           
        },
        error: async function(error) {
            console.log(`Error ${error}`);
            return false;
        },
    });
}

async function getTotalSupply() {


    const yourchainId = await getChainId();
    const singleChainData = await getChainIdData(yourchainId);
    console.log("%c Line:203 🍔 singleChainData", "color:#42b983", singleChainData);
    let rpc = singleChainData.data.rpc;
    techTxnUrl = singleChainData.data.explorerurl;



    const web3ForPriceMaxmint = new Web3(rpc[0]);
    await jQuery.getJSON(plugin_path + "/assets/NFT_Plugin_ABI.json", function(data) {
        techNFTContractABI = data;
        console.log("techNFTContractABI",techNFTContractABI);
    });

    let techNFTContractforPrice = new web3ForPriceMaxmint.eth.Contract(techNFTContractABI, techNFTContractAddress);
    console.log("%c Line:216 🧀 techNFTContractforPrice", "color:#ea7e5c", techNFTContractforPrice);
    totalSupply = await techNFTContractforPrice.methods.MAX_NFTS().call();
    console.log("%c Line:218 🍢 totalSupply", "color:#2eafb0", totalSupply);

    let nftPrice = await techNFTContractforPrice.methods.nftPrice().call();
    let nftPriceInETH = web3ForPriceMaxmint.utils.fromWei(nftPrice, 'ether');
    document.querySelector("#mint_price").innerHTML = "1 Token Price : " + nftPriceInETH + " ETH";
    let maxMint = await techNFTContractforPrice.methods.maxMint().call();
    document.querySelector("#max_nftQty").innerHTML = "Max Mint : " + maxMint;

}


async function getTotalMint() {

    let url = externalURL + "/api/tokenmetadata/getmetadatabycontractaddress?contractaddress=" +
        techNFTContractAddress + "&reserved=1";
    var settings = {
        "url": url,
        "method": "GET"

    };

    jQuery.ajax(settings).done(async function(response) {
        console.log('getTotalMint', response.data);

        for (let i = 0; i < response.data.length; i++) {
            if (response.data[i].txstatus == "PENDING") {
                var transaction_status = "";
                var receipt = await web3.eth.getTransactionReceipt(response.data[i].txhash);
                console.log(receipt);
                if (receipt == null) {
                    transaction_status = "PENDING";
                } else {

                    transaction_status = receipt.status;
                    if (receipt.status == true) {
                        transaction_status = "SUCCESS";
                    } else {
                        transaction_status = "FAILED";
                    }

                }




                jQuery.ajax({
                    type: 'POST',
                    url: adminAjaxpath,
                    data: {
                        action: 'onloadUpdateMetadata',
                        transaction_status: transaction_status,
                        contract_address: response.data[i].contractaddress,
                        tokenid: response.data[i].tokenid
                    },
                    success: function(res) {

                        console.log('res', res);

                    },
                    statusCode: {
                        404: function() {

                        },
                        200: function(res) {

                        }
                    },
                    error: async function(err) {
                        console.log(err);
                    }
                });

            }
        }


        if (response.data.length > 0) {

            document.querySelector("#totalMint").innerHTML = response.data.length;
        } else {
            document.querySelector("#totalMint").innerHTML = 0;
        }
        return response.data.length;
    });

}


/**
 * Setup the orchestra
 */
async function init() {
    // await refreshPage('wallet-connected');


    // This will be used to update Pending Transaction status
    // whether its SUCCESS or FAILED

    console.log("Initializing example");
    console.log("WalletConnectProvider is", WalletConnectProvider);
    console.log("Fortmatic is", Fortmatic);
    console.log("window.web3 is", window.web3, "window.ethereum is", window.ethereum);

    // Tell Web3modal what providers we have available.
    // Built-in web browser provider (only one can exist as a time)
    // like MetaMask, Brave or Opera is added automatically by Web3modal
    const providerOptions = {
        // walletconnect: {
        //     package: WalletConnectProvider,
        //     options: {
        //         // Mikko's test key - don't copy as your mileage may vary
        //         //  infuraId: "8043bb2cf99347b1bfadfb233c5325c0",
        //         infuraId: "70ccb9df33854cd79fdee4473568b91f",
        //     }
        // },


    };

    web3Modal = new Web3Modal({
        cacheProvider: true, // optional
        providerOptions, // required
        disableInjectedProvider: false, // optional. For MetaMask / Brave / Opera.
    });

    // Web3 modal not Disconnect on Refresh the page.
    console.log("Web3Modal instance is", web3Modal);

    if (web3Modal.cachedProvider && provider == null) {
        console.log("Your Provider value is ", provider);
        console.log("Not Opening a dialog Because its already connected !!!!", web3Modal);
        let total_purchasedNFT = '<?php  echo $total_purchasedNFT?>';
        console.log("Your Total purchase NFT's ", total_purchasedNFT);
        let total = '<?php echo $total?>';
        console.log("Your total value is ", total);

        if (total_purchasedNFT == total && total != 0) {
            console.log("You shoud disconnect");
            await onDisconnectWhenAllNFTsAreMinted();
        } else {
            await onConnect();

        }
    }




}



/**
 * Kick in the UI action after Web3modal dialog has chosen a provider
 */
async function fetchAccountData() {

    // Get a Web3 instance for the wallet
    //  const web3 = new Web3(provider);
    web3 = new Web3(provider);

    console.log("Web3 instance is", web3);

    // Get connected chain id from Ethereum node
    const chainId = await getChainId();
    console.log("%c Line:378 🍊 chainId", "color:#e41a6a", chainId);
    
    // Load chain information over an HTTP API
  //  const chainData = evmChains.getChain(chainId);

    //document.querySelector("#network-name").textContent = chainData.name;

    // Get list of accounts of the connected wallet
    const accounts = await web3.eth.getAccounts();

    // MetaMask does not give you all accounts, only the selected account
    console.log("Got accounts", accounts);

    selectedAccount = accounts[0];


    let yourResponse = await getContractByUseraddress().then(async function(res) {
        return res;
    }).catch(async function(message) {
        return false;
    });

    spinnerDiv = document.getElementById("divLoading");
    spinnerDiv.classList.add('show');

    let checkResponse = JSON.parse(yourResponse);
    console.log('checkResponse' + checkResponse.status);
    let flag = 1;
    if (checkResponse.status == "FAILED" || checkResponse.contractaddress == null) {
        flag = 0
        jQuery("#nft-info").remove();
        jQuery(".newsLetterForm").remove();
        jQuery("#max_nftQty").append(
            "<div class='error-div'>Please switch to valid network or check contract deployed on the current network.</div>"
            );
            spinnerDiv.classList.remove('show');
        return false;
    }
    if (flag) {
        if (yourResponse != 0) {
            let response = JSON.parse(yourResponse);
            let totalsupply = response.totalsupply;
            let maxmint = response.maxmint;
            let nftprice = response.nftprice;
            let contractaddress = response.contractaddress;

            techNFTContractAddress = contractaddress;
            await getTotalSupply();
           
            document.querySelector("#totalsupply").innerHTML = "Total Supply : " + totalsupply;
            await getTotalMint();
            await getTotalNFTs(contractaddress);
        }
       



        document.querySelector("#selected-account").textContent = selectedAccount;

        // Display fully loaded UI for wallet data
        document.querySelector("#prepare").style.display = "none";
        document.querySelector("#connected").style.display = "block";
        document.querySelector("#btn-mint").style.display = "block";
        document.querySelector("#btn-disconnect").style.display = "block";
        document.querySelector(".account-detail").style.display = "block";

        let techNFTContractforPrice = new web3.eth.Contract(techNFTContractABI, techNFTContractAddress)
        let nftPrice = await techNFTContractforPrice.methods.nftPrice().call();
        let nftPriceInETH = web3.utils.fromWei(nftPrice, 'ether');
        document.querySelector("#mint_price").innerHTML = "1 Token Price : " + nftPriceInETH + " ETH";

        let maxMint = await techNFTContractforPrice.methods.maxMint().call();
        document.querySelector("#nftQty").setAttribute("max", maxMint);

        document.querySelector("#max_nftQty").innerHTML = "Max Mint : " + maxMint;
        spinnerDiv.classList.remove('show');
        //console.log(maxMint);

    }

}



/**
 * Fetch account data for UI when
 * - User switches accounts in wallet
 * - User switches networks in wallet
 * - User connects wallet initially
 */
async function refreshAccountData() {

    // If any current data is displayed when
    // the user is switching acounts in the wallet
    // immediate hide this data
    //document.querySelector("#connected").style.display = "none";
    document.querySelector("#prepare").style.display = "block";
    document.querySelector("#connWallet").style.display = "block";
    document.querySelector("#nft-info").style.display = "block";
    document.querySelector('#qty-div-section').classList.remove('hide');


    // Disable button while UI is loading.
    // fetchAccountData() will take a while as it communicates
    // with Ethereum node via JSON-RPC and loads chain data
    // over an API call.
    document.querySelector("#btn-connect").setAttribute("disabled", "disabled")
    await fetchAccountData(provider);
    document.querySelector("#btn-connect").removeAttribute("disabled")
}


/**
 * Connect wallet button pressed.
 */
async function onConnect() {

    console.log("Opening a dialog", web3Modal);
    try {
        provider = await web3Modal.connect();
    } catch (e) {
        await alertMessage("Please Select Metamask or any Wallet Provider.", "info");
    }

    if (provider) {
        const yourchainId = await getChainId();

        if (yourchainId != undefined) {

            await jQuery.getJSON(plugin_path + "assets/NFT_Plugin_ABI.json", function(data) {
                techNFTContractABI = data;
                console.log(techNFTContractABI);
            });

            // Subscribe to accounts change
            provider.on("accountsChanged", async function(accounts) {
                // fetchAccountData();
                if (accounts.length == 0) {
                    // location.reload();
                    await onDisconnect();
                } else {
                    const web3 = new Web3(provider);
                    const myaccounts = await web3.eth.getAccounts();
                    if (myaccounts[0] != null & myaccounts[0] != undefined) {
                        fetchAccountData();
                    }
                }
            });

            // Subscribe to chainId change
            provider.on("chainChanged", async function(chainId) {
                console.log("%c Line:504 🥥 chainId", "color:#93c0a4", chainId);
                await onChainChangeDisconnect();
                //await alertMessage("Please Connect to valid Network And try to connect.", "info");
                setTimeout(() => {
                    location.reload();
                }, 1000);

            });

            await refreshAccountData();
        } else {
            // provider = null;
            // await web3Modal.clearCachedProvider();
            await providerDisconnect();
            await alertMessage("Please Connect to valid Network" +
                " And try to connect.", "info");
        }


    }

}

/**
 * Disconnect wallet button pressed.
 */
async function onDisconnect() {

    console.log("Killing the wallet connection", provider);

    // TODO: Which providers have close method?
    if (provider.close) {
        await provider.close();

        // If the cached provider is not cleared,
        // WalletConnect will default to the existing session
        // and does not allow to re-scan the QR code with a new wallet.
        // Depending on your use case you may want or want not his behavir.
        await web3Modal.clearCachedProvider();
        provider = null;
    }
    await web3Modal.clearCachedProvider();
    provider = null;
    selectedAccount = null;
    location.reload();
}

/**
 * Disconnect wallet While wrong change Selected.
 */
async function onChainChangeDisconnect() {

    console.log("Killing the wallet connection: onChainChangeDisconnect", provider);

    // TODO: Which providers have close method?
    if (provider.close) {
        await provider.close();

        // If the cached provider is not cleared,
        // WalletConnect will default to the existing session
        // and does not allow to re-scan the QR code with a new wallet.
        // Depending on your use case you may want or want not his behavir.
        await web3Modal.clearCachedProvider();
        provider = null;
    }
    await web3Modal.clearCachedProvider();
    provider = null;
    selectedAccount = null;
}

/**
 * Disconnect wallet button pressed.
 */
async function onDisconnectWhenAllNFTsAreMinted() {

    console.log("Killing the wallet connection: onDisconnectWhenAllNFTsAreMinted", provider);

    await web3Modal.clearCachedProvider();
    provider = null;
    selectedAccount = null;
}
 

/**
 * Disconnect wallet ProviderWhile wrong change Selected.
 */
async function providerDisconnect() {

    console.log("Killing the wallet connection", provider);

    // TODO: Which providers have close method?
    if (provider.close) {
        console.log("427 Line");
        await provider.close();

        // If the cached provider is not cleared,
        // WalletConnect will default to the existing session
        // and does not allow to re-scan the QR code with a new wallet.
        // Depending on your use case you may want or want not his behavir.
        await web3Modal.clearCachedProvider();
        provider = null;
    }
    await web3Modal.clearCachedProvider();
    provider = null;
    selectedAccount = null;
}

async function getChainId() {
    let chainId = await provider.request({
        method: 'eth_chainId'
    });
    return chainId;
}

async function purchaseNFT() {

    console.log('techTxnUrl', techTxnUrl);
    let nftQty = document.getElementById('nftQty').value;
    console.log('techNFTContractAddress', techNFTContractAddress);
    jQuery.ajax({
        type: 'GET',
        url: adminAjaxpath,
        data: {
            action: 'purchaseNFT',
            nftQty: nftQty,
            contractaddress: techNFTContractAddress,
        },
        success: function(res) {
            let response = JSON.parse(res);
            console.log("%c Line:660 🍭 response", "color:#ea7e5c", response);
            console.log(response.count);
            if (response.count < nftQty) {
                if (response.count == 0) {
                    jQuery(".error-div").text("No NFT's are available for Minting");
                } else if (response.count == 1) {
                    jQuery(".error-div").text("Only " + response.count +
                        " NFT is available for Minting");
                } else {
                    jQuery(".error-div").text("Only " + response.count +
                        " NFTs are available for Minting");
                }

                return false;
            }

            mintMultipleNft(res);
        },
        statusCode: {
            404: function() {

            },
            200: function(res) {

            }
        },
        error: async function(err) {
            console.log(err);
        }
    });


}

async function nftQtyChanged() {

    nftQty = document.getElementById('nftQty').value;
    var max_qty = MintNFT.max_quantity_mint * 1;

    if (nftQty > max_qty) {
        //nftQty = 5;
        document.getElementById('nftQty').value = nftQty;
        await alertMessage("A maximum of only " + MintNFT.max_quantity_mint + " NFTs can be minted at a time.",
            "info");
        //await alertMessage("A maximum of only 5 NFTs can be minted at a time.", "info");
    }
    if (nftQty < 1) {
        nftQty = 1;
        document.getElementById('nftQty').value = nftQty;
        await alertMessage("Atleast 1 NFT have to be minted.", "info");
    }

}



async function mintMultipleNft(data) {
    techNFTContract = new web3.eth.Contract(techNFTContractABI, techNFTContractAddress)
    console.log("data=" + data);
    const nftData = JSON.parse(data);
    console.log("%c Line:720 🍡 nftData", "color:#465975", nftData);

    accounts = await web3.eth.getAccounts();
    console.log(accounts);

    spinnerDiv = document.getElementById("divLoading");
    spinnerDiv.classList.add('show');

    let mintModal = document.getElementById('closeModal');
    mintModal.click();
 
    let nftSalePrice = await techNFTContract.methods.nftPrice().call();
    console.log("Your NFT sale Price is ", nftSalePrice);
   // let noOfETH = BigInt(nftSalePrice) * BigInt(nftData.count);

   let noOfETH = nftSalePrice * nftData.count;
    
    console.log("Your No of ETH ", noOfETH);
    console.log(accounts[0]);
    console.log(nftData.metadata_cid);
    console.log(nftData.count);
    console.log(nftData);
    console.log(techNFTContract);



    techNFTContract.methods.buyNFT(accounts[0], nftData.metadata_cid, nftData.count, nftData.tokenid).send({
        from: accounts[0],
        value: noOfETH
    }, async function(err, result) {
        console.log("%c Line:746 🥥 result", "color:#33a5ff", result);
        let result_html = "";


        spinnerDiv.classList.remove('show');
        console.log("%c Line:752 🍻 err", "color:#7f2b82", err);
        if (err) {
          
            console.log("error");
            document.getElementById('res1').innerHTML = "Your minting process have been terminated";
            result_html += "<ul>";
            result_html += "<li style='text-align:left;'> An error occured. </li>";
            if (err.code == 4001) {
                // toastr.info('You rejected to confirm the transaction.');
                result_html +=
                    "<li style='text-align:left;'> You rejected to confirm the transaction.</li>";
            } else {
                result_html += "<li style='text-align:left;'> " + err.message + "</li>";
            }
            result_html += "</ul>";
            document.getElementById('res2').innerHTML = result_html;
            let successModal = document.getElementById('mintsuccessModal');
            successModal.click();


            return false;

        } else {
            let result_html = "";
            document.getElementById('res1').innerHTML = "You have successfully minted your NFT";
            console.log(result);
            result_html += "<ul>";
            // console.log("Hash of the transaction: " + res)
            result_html +=
                "<li style='text-align:left;'> To view your transaction <a style='color: #f37c23;' target='_blank' href='" +
                techTxnUrl + '/' + result + "'>Click Here !!!</a>.</li>";
            result_html +=
                // "<li style='text-align:left;'> After your transaction is confirmed, with in next 5 to 15 minutes you can view your images on my account page. </li>";
                "<li style='text-align:left;'> After your transaction is confirmed, with in next 5 to 15 minutes your NFTs will reflected in your account. </li>";
            result_html += "</ul>";
            document.getElementById('res2').innerHTML = result_html;
            let successModal = document.getElementById('mintsuccessModal');
            successModal.click();

        }

        accountno = await web3.eth.getAccounts();
        let owner_type = '';
        if (await techNFTContract.methods.owner().call() == accountno[0]) {
            owner_type = 'Admin';

        } else {
            owner_type = 'User';
        }



        var transaction_status = "";
        var receipt = await web3.eth.getTransactionReceipt(result);
        console.log(receipt);
        if (receipt == null) {
            transaction_status = "PENDING";
        } else {

            transaction_status = receipt.status;
            if (receipt.status == true) {
                transaction_status = "SUCCESS";
            } else {
                transaction_status = "FAILED";
            }

        }

        console.log(nftData.tokenid);

        for (let i = 0; i < nftData.tokenid.length; i++) {

            jQuery.ajax({
                type: 'POST',
                url: adminAjaxpath,
                data: {
                    action: 'updateMetadata',
                    transaction_hash: result,
                    transaction_status: transaction_status,
                    mintedby: accounts[0],
                    contract_address: techNFTContractAddress,
                    tokenid: nftData.tokenid[i],
                    reserved: 1

                },
                success: function(res) {

                    console.log('res', res);

                },
                statusCode: {
                    404: function() {

                    },
                    200: function(res) {

                    }
                },
                error: async function(err) {
                    console.log(err);
                }
            });


        }




     });
    
   // .on('transactionHash', async function(result){
        
    //     let result_html = "";
    //     document.getElementById('res1').innerHTML = "You have successfully minted your NFT";
    //     console.log(result);
    //     result_html += "<ul>";
    //     // console.log("Hash of the transaction: " + res)
    //     result_html +=
    //         "<li style='text-align:left;'> To view your transaction <a style='color: #f37c23;' target='_blank' href='" +
    //         techTxnUrl + '/' + result + "'>Click Here !!!</a>.</li>";
    //     result_html +=
    //         // "<li style='text-align:left;'> After your transaction is confirmed, with in next 5 to 15 minutes you can view your images on my account page. </li>";
    //         "<li style='text-align:left;'> After your transaction is confirmed, with in next 5 to 15 minutes your NFTs will reflected in your account. </li>";
    //     result_html += "</ul>";
    //     document.getElementById('res2').innerHTML = result_html;
    //     let successModal = document.getElementById('mintsuccessModal');
    //     successModal.click();

    // }).on('confirmation', async function (data){
    //     accountno = await web3.eth.getAccounts();
    //     let owner_type = '';
    //     if (await techNFTContract.methods.owner().call() == accountno[0]) {
    //         owner_type = 'Admin';

    //     } else {
    //         owner_type = 'User';
    //     }



    //     var transaction_status = "";
    //     var receipt = await web3.eth.getTransactionReceipt(result);
    //     console.log(receipt);
    //     if (receipt == null) {
    //         transaction_status = "PENDING";
    //     } else {

    //         transaction_status = receipt.status;
    //         if (receipt.status == true) {
    //             transaction_status = "SUCCESS";
    //         } else {
    //             transaction_status = "FAILED";
    //         }

    //     }

    //     console.log(nftData.tokenid);

    //     for (let i = 0; i < nftData.tokenid.length; i++) {

    //         jQuery.ajax({
    //             type: 'POST',
    //             url: adminAjaxpath,
    //             data: {
    //                 action: 'updateMetadata',
    //                 transaction_hash: result,
    //                 transaction_status: transaction_status,
    //                 mintedby: accounts[0],
    //                 contract_address: techNFTContractAddress,
    //                 tokenid: nftData.tokenid[i],
    //                 reserved: 1

    //             },
    //             success: function(res) {

    //                 console.log('res', res);

    //             },
    //             statusCode: {
    //                 404: function() {

    //                 },
    //                 200: function(res) {

    //                 }
    //             },
    //             error: async function(err) {
    //                 console.log(err);
    //             }
    //         });


    //     }

    // })
    // .catch(async function (err) {
    //         console.log("error ", err);
    //         if (err.code == 4001) {
    //             console.log('Trasaction rejected.!!');
    //             // await alertMessage("Trasaction rejected.", "info");
    //         } else {
    //             // await alertMessage("Something went wrong.", "info");
             
    //         let result_html='';    
    //         console.log("error");
    //         document.getElementById('res1').innerHTML = "Your minting process have been terminated";
    //         result_html += "<ul>";
    //         result_html += "<li style='text-align:left;'> An error occured. </li>";
    //         if (err.code == 4001) {
    //         // toastr.info('You rejected to confirm the transaction.');
    //         result_html +=
    //             "<li style='text-align:left;'> You rejected to confirm the transaction.</li>";
    //         } else {
    //         result_html += "<li style='text-align:left;'> " + err.message + "</li>";
    //         }
    //         result_html += "</ul>";
    //         document.getElementById('res2').innerHTML = result_html;
    //         let successModal = document.getElementById('mintsuccessModal');
    //         successModal.click();
    //         return false;

    //         }
    //     })
  


    jQuery(".close").click(function() {
        location.reload(true);
    });

    jQuery(".buy_pop").click(function() {
        location.reload(true);
    });


}


function increment() {
    jQuery(".error-div").text("");
    document.getElementById('nftQty').stepUp();
}



function decrement() {
    jQuery(".error-div").text("");
    document.getElementById('nftQty').stepDown();
}



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



async function getstatus(result) {


    var transaction_status = "";
    var receipt = await web3.eth.getTransactionReceipt(result);
    //console.log(receipt);
    if (receipt == null) {
        transaction_status = "PENDING";
    } else {

        transaction_status = receipt.status;
        if (receipt.status == true) {
            transaction_status = "SUCCESS";
        } else {
            transaction_status = "FAILED";
        }

    }
    console.log("Your Transaction is " + transaction_status);
}


async function getTransactionstatus(trans_id) {

    const yourchainId = await getChainId();
    const singleChainData = await getChainIdData(yourchainId);
    let rpc = singleChainData.data.rpc;
    console.log('singleChainData', singleChainData);
    console.log('singleChainData', singleChainData);

    const INFURA_KEY = "9b629f0b7e6f4c4cab19bcad5afc87a2";
    const web3 = new Web3(rpc[0]);

    console.log(trans_id.transaction_hash[0]);
    console.log(trans_id.transaction_hash.length);
    //const trans_data = [];
    let response = {
        "trans_data": []
    }
    let yourdata = [];
    for (let i = 0; i < trans_id.transaction_hash.length; i++) {

        var transaction_status = "";
        var receipt = await web3.eth.getTransactionReceipt(trans_id.transaction_hash[i]);

        if (receipt == null) {
            transaction_status = "PENDING";
        } else {

            transaction_status = receipt.status;
            if (receipt.status == true) {
                transaction_status = "SUCCESS";
            } else {
                transaction_status = "FAILED";
            }

        }

        // trans_data[trans_id.transaction_hash[i]] = transaction_status;
        yourdata.push({
            transationHash: trans_id.transaction_hash[i],
            transactionStatus: transaction_status
        })

        // console.log(transaction_status); 

    }

    response.trans_data = yourdata

    console.log(response);
    return response;
    // console.log(trans_data); 


}



async function updateTransaction_status(trans_data) {


    console.log(trans_data);

    jQuery.ajax({
        type: 'POST',
        url: adminAjaxpath,
        data: {
            action: 'updateTransaction',
            post_data: trans_data
        },
        success: function(res) {
            //console.log(res);

        },
        statusCode: {
            404: function() {

            },
            200: function(res) {
                //console.log(res);
            }
        },
        error: async function(err) {
            console.log(err);
        }
    });



}



async function getContractByUseraddress() {
    console.log('selectedAccount0', selectedAccount);

   

    const yourchainId = await getChainId();
    console.log('yourchainId',yourchainId);
    const singleChainData = await getChainIdData(yourchainId);
    console.log('singleChainData',singleChainData);
   
    return jQuery.ajax({
        type: 'GET',
        url: adminAjaxpath,
        data: {
            action: 'deploymentTransaction',
            deployer: selectedAccount,
            chainid: singleChainData.data.chainid,
        },
        success: function(res) {
            console.log(res);
            return res;
        },

        error: async function(err) {
            console.log(err);
        }
    });

}
</script>

<body class="subscribe-form-main-2"
    style="background: linear-gradient(rgba(89,5,89,0.9),rgba(0,0,0,0.9)),url(<?php echo wp_kses_post($bg_image);?>);">
    <header>
        <div class="logo_top">
            <a class="logo-img" >
                <img src="<?php echo  wp_kses_post($logoimage); ?>">
            </a>
            <div class="top_center_head">

            </div>
        </div>
    </header>
    <section class="middle_sec">

        <div class="midd">

            <div class="middle_container">
                <div class="row subscribe-title">
                    <div class="col-md-6 tech_midd_img">

                        <h2 class="mint_title"><?php echo wp_kses_post($techNFTHeading); ?></h2>
                        <p><?php echo wp_kses_post($description1);?></p>
                        <p id="totalsupply"></p>
                        <p id="mint_price"></p>
                        <p id="max_nftQty"></p>


                        <h4 class="nft-info" id="nft-info" style="display: none;">
                            <span id="totalMint"></span>/<span id="totalNFTNo"></span>
                        </h4>
                        <form action="javascript:void(0)" class="newsLetterForm">

                            <div id="connWallet" style="display: none;margin:0 auto;">
                                <h4 id="connWalletSpan"></h4>
                            </div>

                            <div class="input-group plus-minus-input hide" id="qty-div-section">
                                <div class="input-group-button ">
                                    <button type="button" class="button hollow circle inc_btn" data-quantity="minus"
                                        data-field="quantity" onclick="decrement()">
                                        <i class="fa fa-minus" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <input class="input-group-field inc_num" disabled onInput="nftQtyChanged()" value="1"
                                    min="1"
                                    max="<?php if(isset($mc_options) && isset($qty_amt)){ echo wp_kses_post($qty_amt); } else { echo wp_kses_post('5'); } ?>"
                                    type="number" class="form-control" id="nftQty" aria-describedby="emailHelp"
                                    placeholder="">
                                <div class="input-group-button">
                                    <button type="button" class="button hollow circle inc_btn" data-quantity="plus"
                                        data-field="quantity" onclick="increment()">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <p id="mint_price"></p>
                            <p id="max_nftQty"></p>
                            <div id="prepare">
                                <button class="buy" id="btn-connect" style="margin:0 auto;"
                                    onclick="onConnect()">Connect</button>
                            </div>




                            <div class="contain-btn" id="connected">
                                <div class="account-detail" style="display: none;margin:0 auto;"><span
                                        id="selected-account"></span></div>
                                <button class="buy" id="btn-mint" style="display: none;margin:0 auto;"
                                    onclick="purchaseNFT()">Mint</button>
                                <button class="buy" id="btn-disconnect" style="display: none;margin:0 auto;"
                                    onclick="onDisconnect()">Disconnect</button>

                            </div>
                            <div class="contain-btn2">
                                <button class="buy" id="btn-myaccount" style="display: none;margin:0 auto;">My
                                    Account</button>
                            </div>

                            <div class="clearfix"></div>
                            <div class="error-div"></div>

                        </form>
                        <div class="clearfix"></div>

                        <!-- <div class="row">

                            <div class="col-md-6">
                                &nbsp;
                            </div>
                        </div> -->

                    </div><!-- //tech_midd_img-->

                </div><!-- //subscribe-title-->

            </div><!-- //middle_container -->
            <div class="clear-fix"></div>

        </div><!-- //midd -->

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog mint_modal" role="document">
                <div class="modal-content mint-nft">

                    <div class="modal-header">

                        <h5 class="modal-title mint_the" id="exampleModalLabel"><b>MINT NFT!</b></h5>
                        <button id="closeModal" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                    </div>

                    <div class="clearfix"></div>
                    <h6 class="ran">You will receive a random NFT.</h6>
                    <div class="clearfix"></div>
                    <ul style="margin-bottom:0px;">
                        <li>Please select the number of NFT you wish to buy then click MINT button.</li>
                        <li>A maximum of 5 NFTs can be minted at a time.</li>
                        <li>Be quick; once they're gone, they're gone!</li>
                    </ul>

                    <form method="post" id="simple-form">
                        <div class="modal-body">
                            <div class="form-group pop_form">
                                <input onInput="nftQtyChanged()" value="1" type="number" class="form-control"
                                    id="nftQty" aria-describedby="emailHelp" placeholder="">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="purchaseNFT()" class="buy_pop" id="mint-btn">MINT!</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="divLoading">
            <div class="spinner">
                <i style="font-size: 80px;color: white;" class="fa fa-spinner fa-spin"></i>
                <span style="font-size: 20px;font-weight: bold;color: white;">
                    <br>
                    Please Wait, It might take some time.
                    <br>
                    And don't refresh the page.
                </span>
            </div>
        </div>
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
                        <h5 class="modal-title mint_the" id="exampleModalLabel"><b>MINT NFT!</b></h5>
                        <button id="closeModal" type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="clearfix"></div>
                    <h6 class="ran" id="res1">You have successfully minted your NFT</h6>
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
    </section>
    <?php //get_footer(); ?>
    <script>
    window.addEventListener("DOMContentLoaded", init);
    </script>