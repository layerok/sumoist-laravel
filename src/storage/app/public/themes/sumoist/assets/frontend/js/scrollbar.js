(function($){
    $(window).load(function(){

        $.mCustomScrollbar.defaults.scrollButtons.enable=true; //enable scrolling buttons by default
        $.mCustomScrollbar.defaults.axis="yx"; //enable 2 axis scrollbars by default

        $("body").mCustomScrollbar();



    });
})(jQuery);
