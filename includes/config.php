<?php
// Now only using these DNS records so values set as static
define('DNS_RECORDS_ARRAY', [
    [
        "content" => "k=rsa;t=s;p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCbmGbQMzYeMvxwtNQoXN0waGYaciuKx8mtMh5czguT4EZlJXuCt6V+l56mmt3t68FEX5JJ0q4ijG71BGoFRkl87uJi7LrQt1ZZmZCvrEII0YO4mp8sDLXC8g1aUAoi8TJgxq2MJqCaMyj5kAm3Fdy2tzftPCV/lbdiJqmBnWKjtwIDAQAB",
        "name" => "api._domainkey",
        "proxied" => false,
        "type" => "TXT",
        "comment" => "Elastic Mail DKIM"
    ],
    [
        "content" => "v=spf1 a mx include:_spf.elasticemail.com ~all",
        "name" => "@",
        "proxied" => false,
        "type" => "TXT",
        "comment" => "Elastic Mail SPF"
    ], [
        "content" => "v=DMARC1;p=none;pct=100;aspf=r;adkim=r;",
        "name" => "_dmarc",
        "proxied" => false,
        "type" => "TXT",
        "comment" => "Elastic Mail DMARC"
    ], [
        "content" => "api.elasticemail.com",
        "name" => "tracking",
        "proxied" => false,
        "type" => "CNAME",
        "comment" => "Elastic Mail Tracking"
    ],
]);
 