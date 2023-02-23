jQuery(function ($) {
//--woo star popup
    $(".star-rating").hoverIntent (function () {
        var thisForm = $(this);
        var dataId = thisForm.parents('li').find('.pid').val();
       $('.woocommerce-product-rating').find('.star-rating').css('cursor', 'pointer');
        if (thisForm.parent(".comment-text").length)
            return;
        if (!dataId) {
            var dataId = $('.entry-summary .pid').val();
        }
        if (!dataId)
            return;
        var action_data = {
            'action': 'show_all_rating',
            'id': dataId
        };
        $(".star-rating").attr("title", "");
        if (thisForm.parent().find('#page-wrap').length === 0) {
            if (thisForm.hasClass('loading'))
                return;
            thisForm.addClass('loading');
            $.post(StarCount.ajaxUrl, action_data, function (response) {
                thisForm.after(response);
                $('.woocommerce-product-rating').find('#page-wrap').css('margin-top', '30px');
                thisForm.removeClass('loading');
            });
        }    
    });
//--Update like dislike status
    $('.mg-cmnt-like,.mg-cmnt-unlike').click(function () {
        var thisForm = $(this);
        var commentId = $(this).attr('commentId');
        var authcheck = $(this).attr('authcheck');
        var loginUrl = $(this).attr('loginUrl');
        var commentStatus = $(this).val();
        var action_data = {commentId: commentId, commentStatus: commentStatus, action: "comment_helpful"};
        $.post(StarCount.ajaxUrl, action_data, function (response) {
            if (authcheck === "") {
                window.location.href = loginUrl;
                return false;
            } else {
                if (commentStatus === '1') {
                    thisForm.parent().find('.mg-cmnt-unlike').removeClass("mg-active");
                    thisForm.addClass("mg-active");
                } else {
                    thisForm.parent().find('.mg-cmnt-like').removeClass("mg-active");
                    thisForm.addClass("mg-active");
                }
                thisForm.parent().find('.likeid').html(response);
            }
        });
    });
});
