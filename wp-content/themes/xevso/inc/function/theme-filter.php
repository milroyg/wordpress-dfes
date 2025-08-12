<?php 

// active framework 

function is_cs_framework_active() {
	return ( function_exists( 'cs_get_option' ) ) ? true : false;
}

// Comment section 
function xevso_comment_form_field($fields){
	$comment_field = $fields['comment'];
	unset($fields['comment']);
	$fields['comment'] = $comment_field;
	$cookies_field = $fields['cookies'];
	unset($fields['cookies']);
	$fields['cookies'] = $cookies_field;
	return $fields;
}
add_filter('comment_form_fields','xevso_comment_form_field');

// xevso pagination
if ( !function_exists('xevso_pagination') ) {
    function xevso_pagination(){
        the_posts_pagination(array(
            'screen_reader_text' => '',
            'prev_text'          => '<i class="fa fa-angle-left"></i>',
            'next_text'          => '<i class="fa fa-angle-right"></i>',
            'type'               => 'list',
            'mid_size'           => 1,
        ));
    }
}

// Add Span In category number
add_filter('wp_list_categories', 'xevso_cat_count_span');
function xevso_cat_count_span($links) {
  $links = str_replace('</a> (', '</a> <span>(', $links);
  $links = str_replace(')', ')</span>', $links);
  return $links;
}
// Add Span In archive number
function xevso_the_archive_count($links) {
    $links = str_replace('</a>'.esc_attr__('&nbsp;','xevso').'(', '</a> <span>(', $links);
    $links = str_replace(')', ')</span>', $links);
    return $links;
}
add_filter('get_archives_link', 'xevso_the_archive_count');