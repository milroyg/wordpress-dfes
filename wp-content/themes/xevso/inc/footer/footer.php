<?php 
if(get_post_meta( get_the_ID(), 'xevso_metabox', true)) {
	$xevso_meta = get_post_meta( get_the_ID(), 'xevso_metabox', true );
} else {
  $xevso_meta = array();
}
if(is_array($xevso_meta) && array_key_exists('xevso_meta_footer_styles', $xevso_meta) && $xevso_meta['xevso_meta_footer_styles'] == true ){
	$select_footer = $xevso_meta['xevso_meta_footer_styles'];
}elseif(!empty(xevso_options('xevso_footer_styles'))){
	$select_footer = xevso_options('xevso_footer_styles');
}else{
	$select_footer = 'one';
}
get_template_part('inc/footer/footer-'.$select_footer.'');
?>
