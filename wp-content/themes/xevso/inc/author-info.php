<?php
	if (defined('CS_ACTIVE_FRAMEWORK') && function_exists('cs_get_option')) :
		$xevso_ainfo_enable = cs_get_option('xevso_author_info_enable');
	 if(!empty($xevso_ainfo_enable == true)) : ?>
	<div class="xevso-author-info">
		<div class="row">
			<div class="col-md-2 col-sm-3 xevso-authorimage">
				<?php echo get_avatar( get_the_author_meta( 'ID' )); ?>
			</div>
			<div class="col-md-10 col-sm-9">

				<div class="xevso-author-name">
					<h3><?php the_author_meta( 'display_name'); ?></h3>
				</div>
				<div class="xevso-author-dec">
					<p>
						<?php the_author_meta('description'); ?>
					</p>
				</div>
				<?php
					$xevso_user_cm = wp_get_user_contact_methods()
				?>
				<div class="xevso-author-socila">
					<ul>
						<?php
						foreach ($xevso_user_cm as $xevso_ucm_key => $xevso_ucm_value) :
							$xevso_cm_link = get_user_meta(get_the_author_meta('ID'), $xevso_ucm_key, true);
						?>
						<?php if($xevso_cm_link) : ?>
						<li>
							<a data-placement="top" data-original-title="<?php echo esc_attr($xevso_ucm_key) ?>" href="<?php echo esc_url($xevso_cm_link); ?>"><span class="fa fa-<?php echo esc_attr($xevso_ucm_key) ?>"></span>
							</a>
						</li>
						<?php endif; ?>
						<?php
						endforeach;	
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?php endif; endif; ?>