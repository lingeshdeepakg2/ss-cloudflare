<?php

    /** Provide Details for domain check and rules set */
    //Set API Token
    define('APITOKEN','90258b33ff1292a79f865da2a43fffa425839');

    //Set Email
    define('EMAIL','lingeshg2t@gmail.com');

    //Set Bearer Token
    define('BEARERTOKEN','ET6KwnNw5dhUqwv9ZT8CCcjf-Urz9Yqssfo45WAc');

    /** Provide GitHub details for plugin update */

    $pluginFile =  plugin_basename(dirname(__FILE__)).'/ss-toolkit.php';

    // DEFINE plugin SLUG
    define('SLUG', $pluginFile);

    // DEFINE GITHUB user name
    define('GITHUBUSERNAME', 'lingeshdeepakg2');

    // DEFINE GITHUB project repo name
    define('GITHUBPROJECTREPO', 'ss-cloudflare');

    // DEFINE plugin GITHUB project branch name
    define('GITHUBBRANCH','main');

    // DEFINE AccessToken created in GITHUB
    define('ACCESSTOKEN',"");