// Init our own custom code
jQuery(window).load(function() {

    function qsl_popup_init() {

        $('.qsl__popup').on('click', function (e) {

            window.open($(e.target).data('href'), null, "height=520,width=520,top=200,left=200,status=yes,toolbar=no,menubar=no,location=no");

        });

    }

    qsl_popup_init();
    
});
//# sourceMappingURL=custom-1468417010371142504.js.map