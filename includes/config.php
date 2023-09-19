<?php

    /** 
     * Provide Details for domain check and rules set 
    */
    
    //Set API Token
    define('APITOKEN','90258b33ff1292a79f865da2a43fffa425839');

    //Set Email
    define('EMAIL','lingeshg2t@gmail.com');

    //Set Bearer Token
    define('BEARERTOKEN','ET6KwnNw5dhUqwv9ZT8CCcjf-Urz9Yqssfo45WAc');


    /** 
     * Provide GitHub details for plugin update 
    */

    $pluginFile =  plugin_basename(dirname(__FILE__)).'/ss-cloudflare.php';

    // DEFINE plugin SLUG
    define('CloudFlareSLUG', $pluginFile);

    // DEFINE GITHUB user name
    define('CloudFlareGITHUBUSERNAME', 'lingeshdeepakg2');

    // DEFINE GITHUB project repo name
    define('CloudFlareGITHUBPROJECTREPO', 'ss-cloudflare');

    // DEFINE plugin GITHUB project branch name
    define('CloudFlareGITHUBBRANCH','main');

    // DEFINE AccessToken created in GITHUB
    // If the Git repo is private provide the Access Token, if the Git repo is public leave it empty
    define('CloudFlareACCESSTOKEN',"");