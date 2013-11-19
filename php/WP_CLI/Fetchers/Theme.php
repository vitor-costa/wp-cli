<?php

namespace WP_CLI\Fetchers;

class Theme extends Base {

	protected $msg = "The '%s' theme could not be found.";

	public function get( $name ) {
		$theme = wp_get_theme( $name );

		// ugly fix to #875
		$theme->cache_delete();

		if ( !$theme->exists() ) {
			return false;
		}

		return $theme;
	}
}

