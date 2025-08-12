<div class="jltma-master-addons-tab-panel" id="jltma-master-addons-changelogs" style="display: none;">
	<div class="jltma-master-addons-features is-flex">
		<div class="jltma-left_column">
			<?php
				require_once(JLTMA_PATH . '/lib/readme-parser.php');
				$changelogs = new WordPress_Readme_Parser();
				$changelog_section = $changelogs->parse_readme(JLTMA_PATH . '/changelog.txt');
				echo '<pre>' . wp_filter_nohtml_kses($changelog_section['sections']['changelog']) . '</pre>';
			?>
		</div>

		<div class="jltma-right_column">
			<?php require(JLTMA_PATH . '/inc/admin/welcome/right-column.php'); ?>
		</div>
	</div>
</div>
