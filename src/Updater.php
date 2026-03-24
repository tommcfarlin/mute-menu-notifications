<?php
/**
 * Self-hosted updater via GitHub Releases.
 *
 * @package TomMcFarlin\MMN
 */

namespace TomMcFarlin\MMN;

defined( 'ABSPATH' ) || exit;

/**
 * Checks GitHub Releases for new versions and feeds update data
 * into the WordPress plugin update system.
 *
 * Uses the Update URI plugin header (WP 5.8+) and the
 * update_plugins_{$hostname} filter to serve updates from
 * GitHub Releases without requiring the WordPress.org repository.
 */
class Updater {

	/**
	 * GitHub API endpoint for the latest release.
	 *
	 * @var string
	 */
	const API_URL = 'https://api.github.com/repos/tommcfarlin/mute-menu-notifications/releases/latest';

	/**
	 * Transient key for caching the GitHub response.
	 *
	 * @var string
	 */
	const CACHE_KEY = 'mutemenu_github_release';

	/**
	 * Cache duration in seconds (12 hours).
	 *
	 * @var int
	 */
	const CACHE_TTL = 43200;

	/**
	 * The plugin basename (e.g. mute-menu-notifications/mute-menu-notifications.php).
	 *
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * The plugin slug (directory name, e.g. mute-menu-notifications).
	 *
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * The current installed version.
	 *
	 * @var string
	 */
	private $current_version;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_file    Full path to the main plugin file.
	 * @param string $current_version The currently installed version.
	 */
	public function __construct( $plugin_file, $current_version ) {
		$this->plugin_basename = plugin_basename( $plugin_file );
		$this->plugin_slug     = dirname( $this->plugin_basename );
		$this->current_version = $current_version;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'update_plugins_github.com', array( $this, 'check_for_update' ), 10, 4 );
		add_filter( 'upgrader_source_selection', array( $this, 'fix_source_directory' ), 10, 4 );
	}

	/**
	 * Check GitHub for a newer release.
	 *
	 * Callback for the update_plugins_github.com filter. WordPress passes
	 * the existing update data, the plugin's Update URI, and the plugin's
	 * metadata. We return update data if a newer version is available.
	 *
	 * @param array|false $update     The plugin update data or false.
	 * @param array       $plugin_data Plugin header data.
	 * @param string      $plugin_file Plugin file relative to plugins directory.
	 * @param string[]    $locales     Installed locales.
	 * @return array|false Update data array or false if no update available.
	 */
	public function check_for_update( $update, $plugin_data, $plugin_file, $locales ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( $this->plugin_basename !== $plugin_file ) {
			return $update;
		}

		$release = $this->fetch_latest_release();

		if ( ! $release ) {
			return $update;
		}

		$remote_version = ltrim( $release['tag_name'], 'v' );

		if ( ! version_compare( $remote_version, $this->current_version, '>' ) ) {
			return $update;
		}

		return array(
			'id'           => $plugin_data['UpdateURI'],
			'slug'         => $this->plugin_slug,
			'version'      => $remote_version,
			'url'          => $release['html_url'],
			'package'      => $release['zipball_url'],
			'requires_php' => '7.4',
		);
	}

	/**
	 * Rename the extracted GitHub zipball directory to match the plugin slug.
	 *
	 * GitHub zipball archives extract to a directory named
	 * "{owner}-{repo}-{hash}/" which breaks WordPress's plugin
	 * upgrader. This filter renames it to the expected slug.
	 *
	 * @param string       $source        Path to the extracted source directory.
	 * @param string       $remote_source Path to the remote source directory.
	 * @param \WP_Upgrader $upgrader      The WP_Upgrader instance.
	 * @param array        $hook_extra    Extra arguments passed to the upgrader.
	 * @return string Corrected source path or original on failure.
	 */
	public function fix_source_directory( $source, $remote_source, $upgrader, $hook_extra ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( ! isset( $hook_extra['plugin'] ) || $hook_extra['plugin'] !== $this->plugin_basename ) {
			return $source;
		}

		$expected = trailingslashit( $remote_source ) . trailingslashit( $this->plugin_slug );

		if ( $source === $expected ) {
			return $source;
		}

		global $wp_filesystem;

		if ( $wp_filesystem->move( $source, $expected ) ) {
			return $expected;
		}

		return $source;
	}

	/**
	 * Fetch the latest release from GitHub with caching.
	 *
	 * @return array|false Release data array or false on failure.
	 */
	private function fetch_latest_release() {
		$cached = get_transient( self::CACHE_KEY );

		if ( false !== $cached ) {
			return $cached;
		}

		$response = wp_remote_get(
			self::API_URL,
			array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/vnd.github.v3+json',
				),
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) || empty( $body['tag_name'] ) || empty( $body['zipball_url'] ) ) {
			return false;
		}

		$release = array(
			'tag_name'    => $body['tag_name'],
			'html_url'    => $body['html_url'],
			'zipball_url' => $body['zipball_url'],
		);

		set_transient( self::CACHE_KEY, $release, self::CACHE_TTL );

		return $release;
	}
}
