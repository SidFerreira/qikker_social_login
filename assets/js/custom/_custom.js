// Init our own custom code
jQuery(window).load(function() {

    function qsl_popup_init() {

        $('.qsl__popup').on('click', function (e) {

            window.open($(e.currentTarget).data('href'), null, "height=400,width=580,top=200,left=200,status=yes,toolbar=no,menubar=no,location=no");

        });

    }

    qsl_popup_init();
    
});