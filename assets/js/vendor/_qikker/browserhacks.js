jQuery(document).ready(function() {
    
    var ua = navigator.userAgent;
    var ua_lower = ua.toLowerCase();

    // Custom check for IE10
    if (jQuery.browser.msie && jQuery.browser.version == 10) {

        jQuery('html').addClass('ie10');

    }

    // Custom check for IE11
    if(ua.match(/Trident.*rv:11\./)) {

        jQuery('html').addClass('ie11');

    }

    // Custom check for MS Edge
    if(ua.match(/Edge\/12./)) {

        jQuery('html').addClass('edge');

    }

    // Custom check for iOS 7 safari browser
    var IS_MOBILE_SAFARI_7 = !!ua.match(/i(Pad|Phone|Pod).+(Version\/7\.\d+ Mobile)/i);

    if(IS_MOBILE_SAFARI_7){

        jQuery('html').addClass('ios7');

    }

    // Custom check for Android stock browser
    var isAndroidStock = ua_lower.indexOf("android") > -1 && ua_lower.indexOf("mobile") && ua_lower.indexOf("chrome")==-1;

    if(isAndroidStock) {

        jQuery('html').addClass('android_stock');

    }
    
});