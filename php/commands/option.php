<?php

/**
 * Manage WordPress options.
 *
 * @package wp-cli
 */
class Option_Command extends WP_CLI_Command {

	/**
	 * Get an option.
	 *
	 * @synopsis <key> [--format=<format>]
	 */
	public function get( $args, $assoc_args ) {
		list( $key ) = $args;

		$value = get_option( $key );

		if ( false === $value )
			die(1);

		WP_CLI::print_value( $value, $assoc_args );
	}

	private static function get_value( $args, $assoc_args ) {
		if ( isset( $args[1] ) ) {
			$raw_value = $args[1];
		} else {
			// We don't use file_get_contents() here because it doesn't handle
			// Ctrl-D properly, when typing in the value interactively.
			$raw_value = '';
			while ( ( $line = fgets( STDIN ) ) !== false ) {
				$raw_value .= $line;
			}
		}

		return WP_CLI::read_value( $raw_value, $assoc_args );
	}

	/**
	 * Add an option. If the _value_ parameter is ommited, the value is read from STDIN.
	 *
	 * @synopsis <key> [<value>] [--format=<format>]
	 */
	public function add( $args, $assoc_args ) {
		$key = $args[0];

		$value = self::get_value( $args, $assoc_args );

		if ( !add_option( $key, $value ) ) {
			WP_CLI::error( "Could not add option '$key'. Does it already exist?" );
		} else {
			WP_CLI::success( "Added '$key' option." );
		}
	}

	/**
	 * Update an option. If the _value_ parameter is ommited, the value is read from STDIN.
	 *
	 * @alias set
	 * @synopsis <key> [<value>] [--format=<format>]
	 */
	public function update( $args, $assoc_args ) {
		$key = $args[0];

		$value = self::get_value( $args, $assoc_args );

		$result = update_option( $key, $value );

		// update_option() returns false if the value is the same
		if ( !$result && $value !== get_option( $key ) ) {
			WP_CLI::error( "Could not update option '$key'." );
		} else {
			WP_CLI::success( "Updated '$key' option." );
		}
	}

	/**
	 * Delete an option.
	 *
	 * @synopsis <key>
	 */
	public function delete( $args ) {
		list( $key ) = $args;

		if ( !delete_option( $key ) ) {
			WP_CLI::error( "Could not delete '$key' option. Does it exist?" );
		} else {
			WP_CLI::success( "Deleted '$key' option." );
		}
	}
}

WP_CLI::add_command( 'option', 'Option_Command' );

