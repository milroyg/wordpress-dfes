<?php 
if(get_post_meta( get_the_ID(), 'xevso_metabox', true)) {
	$xevso_meta = get_post_meta( get_the_ID(), 'xevso_metabox', true );
} else {
  $xevso_meta = array();
}
if(is_array($xevso_meta) && array_key_exists('xevso_meta_select_header', $xevso_meta) && $xevso_meta['xevso_meta_enable_header'] == true ){
	$select_header = $xevso_meta['xevso_meta_select_header'];
}elseif(!empty(xevso_options('xevso_header_styles'))){
	$select_header = xevso_options('xevso_header_styles');
}else{
	$select_header = 'one';
}
get_template_part('inc/header/header-'.$select_header.'');
?>
