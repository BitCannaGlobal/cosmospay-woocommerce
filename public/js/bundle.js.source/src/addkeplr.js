const initConfig = require('./remote.config');
const {
  SigningCosmosClient
} = require("@cosmjs/launchpad");
var count = 1;

exports.addKeplrChain = async function(getChainId) {
 
    let foundChain = initConfig.default.find(element => element.name === getChainId); 
    // console.log(foundChain)
    
    if (!window.getOfflineSigner || !window.keplr) {
      alert("Please install keplr extension");
    } else {
      if (window.keplr.experimentalSuggestChain) {
        try {
          await window.keplr.experimentalSuggestChain({
            // Chain-id of the Cosmos SDK chain.
            chainId: foundChain.chainId,
            // The name of the chain to be displayed to the user.
            chainName: foundChain.name,
            // RPC endpoint of the chain.
            rpc: foundChain.rpcURL,
            // REST endpoint of the chain.
            rest: foundChain.apiURL,
            // Staking coin information
            stakeCurrency: {
              // Coin denomination to be displayed to the user.
              coinDenom: foundChain.coinLookup.viewDenom,
              // Actual denom (i.e. uatom, uscrt) used by the blockchain.
              coinMinimalDenom: foundChain.coinLookup.chainDenom,
              // # of decimal points to convert minimal denomination to user-facing denomination.
              coinDecimals: 6,
              // (Optional) Keplr can show the fiat value of the coin if a coingecko id is provided.
              // You can get id from https://api.coingecko.com/api/v3/coins/list if it is listed.
              // coinGeckoId: ""
            },
            // The BIP44 path.
            bip44: {
              // You can only set the coin type of BIP44.
              // 'Purpose' is fixed to 44.
              coinType: 118,
            },
            // Bech32 configuration to show the address to user.
            // This field is the interface of
            // {
            //   bech32PrefixAccAddr: string;
            //   bech32PrefixAccPub: string;
            //   bech32PrefixValAddr: string;
            //   bech32PrefixValPub: string;
            //   bech32PrefixConsAddr: string;
            //   bech32PrefixConsPub: string;
            // }
            bech32Config: {
              bech32PrefixAccAddr: foundChain.coinLookup.addressPrefix,
              bech32PrefixAccPub: foundChain.coinLookup.addressPrefix + "pub",
              bech32PrefixValAddr: foundChain.coinLookup.addressPrefix + "valoper",
              bech32PrefixValPub: foundChain.coinLookup.addressPrefix + "valoperpub",
              bech32PrefixConsAddr: foundChain.coinLookup.addressPrefix + "valcons",
              bech32PrefixConsPub: foundChain.coinLookup.addressPrefix + "valconspub"
            },
            // List of all coin/tokens used in this chain.
            currencies: [{
              // Coin denomination to be displayed to the user.
              coinDenom: foundChain.coinLookup.viewDenom,
              // Actual denom (i.e. uatom, uscrt) used by the blockchain.
              coinMinimalDenom: foundChain.coinLookup.chainDenom,
              // # of decimal points to convert minimal denomination to user-facing denomination.
              coinDecimals: 6,
              // (Optional) Keplr can show the fiat value of the coin if a coingecko id is provided.
              // You can get id from https://api.coingecko.com/api/v3/coins/list if it is listed.
              // coinGeckoId: ""
            }],
            // List of coin/tokens used as a fee token in this chain.
            feeCurrencies: [{
              // Coin denomination to be displayed to the user.
              coinDenom: foundChain.coinLookup.viewDenom,
              // Actual denom (i.e. ubcna, uscrt) used by the blockchain.
              coinMinimalDenom: foundChain.coinLookup.chainDenom,
              // # of decimal points to convert minimal denomination to user-facing denomination.
              coinDecimals: 6,
              // (Optional) Keplr can show the fiat value of the coin if a coingecko id is provided.
              // You can get id from https://api.coingecko.com/api/v3/coins/list if it is listed.
              // coinGeckoId: ""
            }],
            coinType: 118,
            // (Optional) This is used to set the fee of the transaction.
            // If this field is not provided, Keplr extension will set the default gas price as (low: 0.01, average: 0.025, high: 0.04).
            // Currently, Keplr doesn't support dynamic calculation of the gas prices based on on-chain data.
            // Make sure that the gas prices are higher than the minimum gas prices accepted by chain validators and RPC/REST endpoint.
            gasPriceStep: {
              low: 0.01,
              average: 0.025,
              high: 0.04
            }
          });
        } catch {
          alert("Failed to suggest the chain");
        }
      } else {
        alert("Please use the recent version of keplr extension");
      }
    }

    const chainId = foundChain.chainId;

    // You should request Keplr to enable the wallet.
    // This method will ask the user whether or not to allow access if they haven't visited this website.
    // Also, it will request user to unlock the wallet if the wallet is locked.
    // If you don't request enabling before usage, there is no guarantee that other methods will work.
    await window.keplr.enable(chainId);

    const offlineSigner = window.getOfflineSigner(chainId);
    window.keplr.defaultOptions = {
      sign: {
        preferNoSetMemo: true,
        preferNoSetFee: true,
        disableBalanceCheck: false,
      },
    };
    // You can get the address/public keys by `getAccounts` method.
    // It can return the array of address/public key.
    // But, currently, Keplr extension manages only one address/public key pair.
    // XXX: This line is needed to set the sender address for SigningCosmosClient.
    const accounts = await offlineSigner.getAccounts();

    // Initialize the gaia api with the offline signer that is injected by Keplr extension.
    const cosmJS = new SigningCosmosClient(
      foundChain.rpcURL,
      accounts[0].address,
      offlineSigner,
    );

    //document.getElementById("address").html(accounts[0].address);
  //};
    return {accounts, foundChain}
};

exports.getCount = function() {
    return count;
}; 
