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
jQuery(document).ready(function () {
    // tooltip is showing on the copy Clipboard

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

    $("#toggle-password-api").click(function() {
        var passwordField = $("#api-token");
        var icon = $(this);
    
        if (passwordField.attr("type") === "password") {
          passwordField.attr("type", "text");
          icon.removeClass("fa-eye-slash").addClass("fa-eye");
        } else {
          passwordField.attr("type", "password");
          icon.removeClass("fa-eye").addClass("fa-eye-slash");
        }
    });

    $("#toggle-password-bearer").click(function() {
        var passwordField = $("#bearer-token");
        var icon = $(this);
    
        if (passwordField.attr("type") === "password") {
          passwordField.attr("type", "text");
          icon.removeClass("fa-eye-slash").addClass("fa-eye");
        } else {
          passwordField.attr("type", "password");
          icon.removeClass("fa-eye").addClass("fa-eye-slash");
        }
    });

});
jQuery(document).on('click', '#domainCheck', function (e) {
    e.preventDefault();
    clearFields();
    jQuery("body").addClass("loading");
    jQuery('.accountTokenDiv').hide();
    if (jQuery('#domainName').val() == '' || jQuery('#api-token').val() == '' || jQuery('#bearer-token').val() == '' || jQuery('#account-email').val() == '') {
        jQuery('#domainNameCheck').text('');
        jQuery('.errorMessage').text('Please fill the Email, API token, Bearer Token and Domain name.');
        showMessageDiv('.errorMessage');
    } else {
        jQuery('.errorMessage').text('');
        jQuery.ajax({
            method: 'POST',
            url: ss_cloudflare_ajax_url.ajaxurl,
            data: {
                'action' : 'domain_curl',
                'url': 'https://api.cloudflare.com/client/v4/zones?name=' + jQuery('#domainName').val(),
                'name': jQuery('#domainName').val(),
                'email': jQuery("#cloudflare_email").val(),
                'apiToken': jQuery("#cloudflare_api_token").val(),
                'bearerToken': jQuery("#cloudflare_bearer_token").val(),
            },
            cache: false,
            success: function (response) {
                var data = jQuery.parseJSON(response);
                // Given domain is not matched it will return warning
                if (!data.status) {
                    jQuery('.errorMessage').text(data.output);
                    showMessageDiv('.errorMessage');
                    $(".check-icon").remove();
                    $(".close-icon").remove();
                    jQuery('#domainNameCheck').append('<span class="close-icon">x</span>');
                } else {
                    if (data.output.length == 0) {
                        jQuery('.errorMessage').text('Domain not exist');
                        showMessageDiv('.errorMessage');
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
                        elastic_mail_list_rules();
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
            'email': jQuery("#cloudflare_email").val(),
            'apiToken': jQuery("#cloudflare_api_token").val(),
            'bearerToken': jQuery("#cloudflare_bearer_token").val(),
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
        jQuery('.errorMessage').text('Please fill the Email, API token, Bearer Token and Domain name.');
        showMessageDiv('.errorMessage');
    } else if (jQuery('#zoneName').val() == '') {
        jQuery('.errorMessage').text('Please provide a valid domain name and do a Domain check');
        showMessageDiv('.errorMessage');
    } else {
        jQuery('.errorMessage').text('');
        jQuery.ajax({
            method: 'POST',
            url: ss_cloudflare_ajax_url.ajaxurl,
            data: {
                'action' : 'accountToken',
                'zoneId': jQuery("#zoneId").val(),
                'zoneName': jQuery('#zoneName').val(),
                'email': jQuery("#cloudflare_email").val(),
                'apiToken': jQuery("#cloudflare_api_token").val(),
                'bearerToken': jQuery("#cloudflare_bearer_token").val(),
            },
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if (data.status) {
                    jQuery('#accountTokenId').val(data.output);
                    jQuery('.accountTokenDiv').show();
                } else {
                    jQuery('.errorMessage').text(data.output);
                    showMessageDiv('.errorMessage');
                }
            },
        });
    }
});

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

    }else if(btnId == "elasticEmail"){
        action = 'elastic_email_list';
    }

    e.preventDefault();
    if (jQuery('#domainName').val() == '' || jQuery('#api-token').val() == '' || jQuery('#bearer-token').val() == '' || jQuery('#account-email').val() == '') {
        jQuery('.errorMessage').text('Please fill the Email, API token, Bearer Token and Domain name.');
        showMessageDiv('.errorMessage');
    } else if (jQuery('#zoneName').val() == '') {
        jQuery('.errorMessage').text('Please provide a valid domain name and do a Domain check.');
        showMessageDiv('.errorMessage');
    } else {
        jQuery('.errorMessage').text('');
        jQuery.ajax({
            method: 'POST',
            url: ss_cloudflare_ajax_url.ajaxurl,
            data: {
                'zoneId': jQuery("#zoneId").val(),
                'zoneName': jQuery('#zoneName').val(),
                'action': action,
                'email': jQuery("#cloudflare_email").val(),
                'apiToken': jQuery("#cloudflare_api_token").val(),
                'bearerToken': jQuery("#cloudflare_bearer_token").val(),
            },
            cache: false,
            success: function (response) {
                var data = jQuery.parseJSON(response);
                if (data.status) {
                    jQuery('.successMessage').text( btnVal + ' ' + data.output );
                    showMessageDiv('.successMessage');
                } else {
                    jQuery('.errorMessage').text(data.output);
                    showMessageDiv('.errorMessage');
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
    jQuery(btn).css('display','block');
    setTimeout(function () {
        jQuery(btn).hide();
    }, 4000);
}

jQuery(document).on('change blur','.ss-cloudflare-input', function(){
    ss_cloudflare_ajax_call();
});

function ss_cloudflare_ajax_call(){
    jQuery('.successMessage').text('Please wait...').css('display','block');

    var from_cloudflare_form = jQuery("#from_cloudflarecontrol_form").val();

    var account_email = jQuery('#account-email').val();
    var api_token = jQuery('#api-token').val();
    var bearer_token = jQuery('#bearer-token').val();

    jQuery.ajax({
        type: 'POST',
        url: ss_cloudflare_ajax_url.ajaxurl, // Replace with your AJAX handler URL
        data:{
            'action' : 'save_cloudflare_details',
            'account_email' : account_email,
            'api_token' : api_token,
            'bearer_token' : bearer_token,
            'from_cloudflare_form' : from_cloudflare_form
            
        },
        success: function(response) {
            jQuery('.successMessage').text(response.data.message).css('display','block');
            setTimeout(function() {
                jQuery('.successMessage').css('display','none');
            }, 4000);
        },
        error: function(xhr, textStatus, errorThrown) {
            jQuery('.errorMessage').text("Something went worng").css('display','block');
            setTimeout(function() {
                jQuery('.errorMessage').css('display','none');
            }, 4000);
        }
    });
}

function elastic_mail_list_ajax(){

    var btnId = jQuery(".ruleBtnClick").attr("name");
    var btnVal = jQuery(".ruleBtnClick").val();

    jQuery('.successMessage').text('Please wait...').css('display','block');

    var account_email = jQuery("#cloudflare_email").val();
    var api_token = jQuery("#cloudflare_api_token").val();
    var bearer_token = jQuery("#cloudflare_bearer_token").val();

    jQuery.ajax({
        method: 'POST',
        url: ss_cloudflare_ajax_url.ajaxurl,
        data: {
            'zoneId': $("#zoneId").val(),
            'zoneName': $('#zoneName').val(),
            'email': account_email,
            'apiToken': api_token,
            'bearerToken': bearer_token,
            'action' : 'elastic_email_list'
        },
        success: function (response) {
            var data = jQuery.parseJSON(response);
            if (data.status) {
                jQuery('.successMessage').text( btnVal + ' ' + data.output );
                showMessageDiv('.successMessage');
            } else {
                jQuery('.errorMessage').text(data.output);
                showMessageDiv('.errorMessage');
            }
            jQuery('#' + btnId).append('<span class="check-icon"></span>');
        },
    });
}

function elastic_mail_list_rules(){


    jQuery.ajax({
        method: 'POST',
        url: ss_cloudflare_ajax_url.ajaxurl,
        data: {
            'action': 'show_elastic_email',
            'zoneId': jQuery("#zoneId").val(),
            'zoneName': jQuery('#zoneName').val(),
            'email': jQuery("#cloudflare_email").val(),
            'apiToken': jQuery("#cloudflare_api_token").val(),
            'bearerToken': jQuery("#cloudflare_bearer_token").val(),
        },
        cache: false,
        success: function (response) {
            var res = jQuery.parseJSON(response);
            console.log(res);
            $(".close-icon").remove();
            if (res.status == true) {
                jQuery('#elasticEmail').addClass("check-icon");
            }
        },
    });
}


