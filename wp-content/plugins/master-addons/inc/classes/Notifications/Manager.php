<?php

namespace MasterAddons\Inc\Classes\Notifications;

use MasterAddons\Inc\Classes\Notifications\Base\Data;

// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Notice Manager
 *
 * Jewel Theme <support@jeweltheme.com>
 */
class Manager extends Data
{

	/**
	 * Constructor method
	 *
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
	public function __construct()
	{

		// Register Latest_Updates Notice.
		$this->register(new Latest_Updates());

		// Register Ask_For_Rating Notice.
		$this->register(new Ask_For_Rating());

		// Register Subscribe Notice .
		$this->register(new Subscribe());

		// Register What we Collect Notice .
		$this->register(new What_We_Collect());

		// Register Upgrade_Notice Notice for managing all notices .
		$this->register(new Upgrade_Notice());

		// Register New_Features_Notice with confetti animation.
		// $this->register(new New_Features_Notice());

		// Register Pro_Sale_Notice with confetti animation.
		// if(ma_el_fs()->is_free_plan() ){
		// 	$this->register(new Pro_Sale_Notice());
		// }
	}
}
