jQuery(document).ready(function ($) {
    $('.flag-btn').on('click', function () {
        var postID = $(this).data('postid');
        var button = $(this);

        $.post(flag_ajax.ajax_url, {
            action: 'toggle_flag',
            nonce: flag_ajax.nonce,
            post_id: postID
        }, function (response) {
            if (response.success) {
                button.text(button.text() === 'Flag this post' ? 'Unflag this post' : 'Flag this post');
            } else {
                alert('Error: ' + response.data);
            }
        });
    });
});
