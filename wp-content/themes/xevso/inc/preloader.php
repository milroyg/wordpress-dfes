<?php 
if( is_cs_framework_active() ) {
	$xevso_enable_preloader = cs_get_option('xevso_enable_preloader');
}
?>
<?php if( is_cs_framework_active()) :  ?>
	<?php if(!empty($xevso_enable_preloader == true )) {
		?>
     j
		<?php 
	}else{
		$xevso_enable_preloader = '';
	}
	?>
<?php  else : ?>
  
      <div class="loader_bg">
            <div class="loader"></div>
      </div>
<?php endif; ?>