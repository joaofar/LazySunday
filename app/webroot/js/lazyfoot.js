$(document).ready(function() {

    $('.link, .index tr, .team').click(function() {
        var href = $(this).find("a").attr("href");
        if(href) {
            window.location = href;
        }
    });

});