const axios = require('axios')
let cosmosConfig = [];

async function remoteConfig() {
 
  // Make a request for a user with a given ID
  axios.get('https://store-api.bitcanna.io')
    .then(function (response) {
      response.data.forEach(function(item){ 
        cosmosConfig.push( item )        
      });      
    })
    .catch(function (error) {
      // handle error
      console.log(error);
    })
    //console.log(cosmosConfig)
    return cosmosConfig
};

remoteConfig()
export default cosmosConfig;
