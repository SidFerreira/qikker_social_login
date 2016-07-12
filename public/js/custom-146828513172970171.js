// Init our own custom code
jQuery(window).load(function() {

    function qsl_popup_init() {

        $('.qsl_popup').on('click', function (e) {

            console.log(e);
            window.open($(e.target).data('href'), null, "height=520,width=520,top=200,left=200,status=yes,toolbar=no,menubar=no,location=no");


        });

    }

    qsl_popup_init();
    
});
//# sourceMappingURL=custom-146828513172970171.js.map