// Once ajax is started loader is display on the screen
jQuery(document).on({
    ajaxStart: function () {
        jQuery('#errorMessage').html('');
        jQuery('#successMessage').html('');
        jQuery("body").addClass("loading");
    },
    ajaxStop: function () {
        // Once ajax is done loader is removed
        jQuery("body").removeClass("loading");
    }
});
$(document).ready(function () {
    // tooltip is showing on the copy Clipboard
    // var clipboard = new Clipboard('.copy-icon');

    // Tooltip
    jQuery('.copy-icon').tooltip({
        trigger: 'click',
        placement: 'top',
        content: ""
    });

    var clipboard = new Clipboard('.copy-icon');

    clipboard.on('success', function (e) {
        var btn = $(e.trigger);
        setTooltip(btn, 'Copied');
        hideTooltip(btn);
    });
    
    // $("#copy-icon").click(function () {
    //     // Your copy logic here

    //     // Set the tooltip text
    //     $("#copy-icon").tooltip("option", "content", "Copied!");

    //     // Show the tooltip
    //     $("#copy-icon").tooltip("open");

    //     // Close the tooltip after a delay (optional)
    //     setTimeout(function () {
    //         $("#copy-icon").tooltip("close");
    //     }, 2000); // Close after 2 seconds (adjust as needed)
    // });
});
jQuery(document).on('click', '#domainCheck', function (e) {
    e.preventDefault();
    clearFields();
    jQuery('.accountTokenDiv').hide();
    if (jQuery('#domainName').val() == '' || jQuery('#api-token').val() == '' || jQuery('#bearer-token').val() == '' || jQuery('#account-email').val() == '') {
        jQuery('#domainNameCheck').text('');
        jQuery('#errorMessage').html('<div uk-alert class="uk-alert-warning">Please fill the Email, API token, Bearer Token and Domain name.<a class="uk-alert-close" uk-close></a></div>');
        showMessageDiv('#errorMessage');
    } else {
        console.log('https://api.cloudflare.com/client/v4/zones?name=' + jQuery('#domainName').val());
        jQuery('#errorMessage').text('');
        jQuery.ajax({
            method: 'POST',
            url: ss_cloudflare_ajax_url.ajaxurl,
            data: {
                'action' : 'domain_curl',
                'url': 'https://api.cloudflare.com/client/v4/zones?name=' + jQuery('#domainName').val(),
                'name': jQuery('#domainName').val(),
                'email': jQuery("#account-email").val(),
                'apiToken': jQuery("#api-token").val(),
                'bearerToken': jQuery("#bearer-token").val(),
            },
            cache: false,
            success: function (response) {
                console.log(response);
                var data = jQuery.parseJSON(response);
                // Given domain is not matched it will return warning
                if (!data.status) {
                    jQuery('#errorMessage').html('<div uk-alert class="uk-alert-warning">' + data.output + '<a class="uk-alert-close" uk-close></a></div>');
                    showMessageDiv('#errorMessage');
                    $(".check-icon").remove();
                    $(".close-icon").remove();
                    jQuery('#domainNameCheck').append('<span class="close-icon">x</span>');
                } else {
                    if (data.output.length == 0) {
                        jQuery('#errorMessage').html('<div uk-alert class="uk-alert-warning">Domain not exist<a class="uk-alert-close" uk-close></a></div>');
                        showMessageDiv('#errorMessage');
                        $(".check-icon").remove();
                        $(".close-icon").remove();
                        jQuery('#domainNameCheck').append('<span class="close-icon">x</span>');
                    } else {
                        // Once zone is there it's set value for particular fields. otherwise it go to else part
                        jQuery('#zoneId').val(data.output.zoneId);
                        jQuery('#zoneName').val(data.output.zoneName);
                        jQuery('#domainName').val(data.output.zoneName);
                        $(".close-icon").remove();
                        jQuery('#domainNameCheck').addClass("check-icon");
                        wafrules();
                    }
                }
            },
        });
    }
});
function wafrules() {
    jQuery.ajax({
        method: 'POST',
        url: ss_cloudflare_ajax_url.ajaxurl,
        data: {
            'action': 'waf_rules_list',
            'zoneId': jQuery("#zoneId").val(),
            'zoneName': jQuery('#zoneName').val(),
            'email': jQuery("#account-email").val(),
            'apiToken': jQuery("#api-token").val(),
            'bearerToken': jQuery("#bearer-token").val(),
        },
        cache: false,
        success: function (response) {
            var res = jQuery.parseJSON(response);
            var data = res.output;
            var myNewArray = data.filter(function (elem, index, self) {
                return index === self.indexOf(elem);
            });
            $(".close-icon").remove();
            jQuery.each(myNewArray, function (key, value) {
                if (value == 'XMLRPC Block') {
                    jQuery('#xmlrpc').addClass("check-icon");

                } else if (value == 'UK Admin / Login') {
                    jQuery('#adminLogin').addClass("check-icon");

                } else if (value == 'Failover') {
                    jQuery('#failover').addClass("check-icon");

                } else if (value == 'Admin Security Check') {
                    jQuery('#adminSecurity').addClass("check-icon");

                }
            });
        },
    });
}

function clearFields() {

    jQuery('#xmlrpc').text('');
    jQuery('#adminLogin').text('');
    jQuery('#failover').text('');
    jQuery('#adminSecurity').text('');
    jQuery('#adminbypass').text('');
    // jQuery('#elasticMail').text('');
    jQuery('#zoneId').val('');
    jQuery('#zoneName').val('');
    jQuery('.accountTokenDiv').hide();
}
jQuery(document).on('click', '#account-token', function (e) {
    e.preventDefault();
    if (jQuery('#domainName').val() == '' || jQuery('#api-token').val() == '' || jQuery('#bearer-token').val() == '' || jQuery('#account-email').val() == '') {
        jQuery('#errorMessage').html('<div uk-alert class="uk-alert-warning">Please fill the Email, API token, Bearer Token and Domain name.<a class="uk-alert-close" uk-close></a></div>');
        showMessageDiv('#errorMessage');
    } else if (jQuery('#zoneName').val() == '') {
        jQuery('#errorMessage').html('<div uk-alert class="uk-alert-warning">Please provide a valid domain name and do a Domain check<a class="uk-alert-close" uk-close></a></div>');
        showMessageDiv('#errorMessage');
    } else {
        jQuery('#errorMessage').text('');
        jQuery.ajax({
            method: 'POST',
            url: ss_cloudflare_ajax_url.ajaxurl,
            data: {
                'action' : 'accountToken',
                'zoneId': jQuery("#zoneId").val(),
                'zoneName': jQuery('#zoneName').val(),
                'email': jQuery("#account-email").val(),
                'apiToken': jQuery("#api-token").val(),
                'bearerToken': jQuery("#bearer-token").val(),
            },
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if (data.status) {
                    jQuery('#accountTokenId').val(data.output);
                    jQuery('.accountTokenDiv').show();
                } else {
                    jQuery('#errorMessage').html('<div uk-alert class="uk-alert-warning">' + data.output + '<a class="uk-alert-close" uk-close></a></div>');
                    showMessageDiv('#errorMessage');
                }
            },
        });
    }
});
// if (jQuery('#domainName').val() == '' || jQuery('#api-token').val() == '' || jQuery('#bearer-token').val() == '') 
jQuery(document).on('change', '#domainName, #api-token, #bearer-token, #account-email', function (e) {
    // alert("test");
    clearFields();
});
jQuery(document).on('click', '.ruleBtnClick', function (e) {
    var btnId = jQuery(this).attr("name");
    var btnVal = jQuery(this).val();
    var action = '';

    if(btnId == 'adminLogin'){
        action = 'adminLogin';

    }else if(btnId == 'xmlrpc'){
        action = 'xmlrpcCheck';

    }else if(btnId == 'adminSecurity'){
        action = 'adminSecurity';

    }else if(btnId == 'failover'){
        action = 'failover';
    }

    e.preventDefault();
    if (jQuery('#domainName').val() == '' || jQuery('#api-token').val() == '' || jQuery('#bearer-token').val() == '' || jQuery('#account-email').val() == '') {
        jQuery('#errorMessage').html('<div uk-alert class="uk-alert-warning">Please fill the Email, API token, Bearer Token and Domain name.<a class="uk-alert-close" uk-close></a></div>');
        showMessageDiv('#errorMessage');
    } else if (jQuery('#zoneName').val() == '') {
        jQuery('#errorMessage').html('<div uk-alert class="uk-alert-warning">Please provide a valid domain name and do a Domain check<a class="uk-alert-close" uk-close></a></div>');
        showMessageDiv('#errorMessage');
    } else {
        jQuery('#errorMessage').text('');
        jQuery.ajax({
            method: 'POST',
            url: ss_cloudflare_ajax_url.ajaxurl,
            data: {
                'zoneId': jQuery("#zoneId").val(),
                'zoneName': jQuery('#zoneName').val(),
                'action': action,
                'email': jQuery("#account-email").val(),
                'apiToken': jQuery("#api-token").val(),
                'bearerToken': jQuery("#bearer-token").val(),
            },
            cache: false,
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if (data.status) {
                    jQuery('#successMessage').html('<div uk-alert class="uk-alert-primary">' + btnVal + ' ' + data.output + '<a class="uk-alert-close" uk-close></a></div>');
                    showMessageDiv('#successMessage');
                } else {
                    jQuery('#errorMessage').html('<div uk-alert class="uk-alert-warning">' + data.output + '<a class="uk-alert-close" uk-close></a></div>');
                    showMessageDiv('#errorMessage');
                }
                jQuery('#' + btnId).append('<span class="check-icon"></span>');
            },
        });
    }
});

function copyToClipboard(element) {
    var jQuerytemp = jQuery("<input>");
    jQuery("body").append(jQuerytemp);
    jQuerytemp.val(jQuery(element).val()).select();
    document.execCommand("copy");
    jQuerytemp.remove();
}

// Tooltip
// $('.copy-icon').tooltip({
//     trigger: 'click',
//     placement: 'top'
// });

function setTooltip(btn, message) {
    btn.tooltip('hide')
        .attr('data-tooltip', message)
        .tooltip('show');
}

function hideTooltip(btn) {
    setTimeout(function () {
        btn.tooltip('hide');
    }, 1000);
}

function showMessageDiv(btn) {
    jQuery(btn).show();
    setTimeout(function () {
        jQuery(btn).hide();
    }, 2500);
}