var AdivPublicBundle = AdivPublicBundle || {};

AdivPublicBundle = (function($, window, undefined) {

    var init;

    init = function() {
        cargobay.videolink.init();
        cargobay.scrollToTop.init();
        AdivPublicBundle.cookieConsent.init();
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    AdivPublicBundle.init();
});
