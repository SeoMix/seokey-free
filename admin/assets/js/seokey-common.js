/* Tooltips position */
/* Detect if an element is off-screen */
(function($) {
    $.extend($.expr[':'], {
        'off-top': function(el) {
            return $(el).offset().top < $(window).scrollTop();
        },
        'off-right': function(el) {
            return $(el).offset().left + $(el).outerWidth() - $(window).scrollLeft() > $(window).width();
        },
        'off-bottom': function(el) {
            return $(el).offset().top + $(el).outerHeight() - $(window).scrollTop() > $(window).height();
        },
        'off-left': function(el) {
            return $(el).offset().left < $(window).scrollLeft();
        },
        'off-horizontal': function(el) {
            return $(el).is(':off-right, :off-left');
        },
        'off-screen': function(el) {
            return $(el).is(':off-top, :off-right, :off-bottom, :off-left');
        }
    });
})(jQuery);

jQuery(document).ready(function($) {
    /* Begin Tooltip always visible */
    // Tooltips always visible ... Not definitive version
    $(".seokey-tooltip-icon").hover(function(){
        var tooltip = $(this).children('.seokey-tooltip-text');
        var change = "nothing";
        // Left Off Screen
        if(tooltip.is(':off-left')){
            change = 'FromRightToLeft';
        }
        // Right off Screen
        if(tooltip.is(':off-right')){
            change = 'FromLeftToRight';
        }
        // In future : Up & down also
        switch(change) {
            case 'FromRightToLeft':
                tooltip.removeClass('right').addClass('left sx_changed');
                break;
            case 'FromLeftToRight':
                tooltip.removeClass('left').addClass('right sx_changed');
                break;
        }
        if(tooltip.hasClass('sx_changed') && tooltip.is(':off-horizontal')){
            tooltip.removeClass('left').removeClass('right').addClass('center');
        }
    });
    /* End Tooltip always visible */
});
