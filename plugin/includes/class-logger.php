<?php
/**
 * Logger class for ContentCraft AI.
 *
 * Handles logging of content generation requests and analytics.
 *
 * @package ContentCraft_AI
 */

namespace ContentCraft;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Logger class.
 */
class Logger {

	/**
	 * Single instance.
	 *
	 * @var Logger
	 */
	private static $instance = null;

	/**
	 * Table name.
	 *
	 * @var string
	 */
	private static $table_name;

	/**
	 * Get instance.
	 *
	 * @return Logger
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		global $wpdb;
		self::$table_name = $wpdb->prefix . 'contentcraft_logs';
	}

	/**
	 * Create database table.
	 *
	 * Called on plugin activation.
	 */
	public static function create_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'contentcraft_logs';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id bigint(20) UNSIGNED NOT NULL,
			type varchar(50) NOT NULL,
			target_id bigint(20) UNSIGNED DEFAULT NULL,
			tokens_used int(11) DEFAULT 0,
			latency_ms int(11) DEFAULT 0,
			status varchar(20) NOT NULL DEFAULT 'success',
			error_message text DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY type (type),
			KEY created_at (created_at),
			KEY status (status)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Log a content generation event.
	 *
	 * @param string $type          Type of generation (content, product, image, seo, brand).
	 * @param int    $target_id     Post/Product ID (optional).
	 * @param int    $tokens_used   Number of tokens used.
	 * @param int    $latency_ms    Request latency in milliseconds.
	 * @param string $status        Status (success, failed).
	 * @param string $error_message Error message if failed.
	 * @return int|false Log ID on success, false on failure.
	 */
	public function log( $type, $target_id = null, $tokens_used = 0, $latency_ms = 0, $status = 'success', $error_message = null ) {
		global $wpdb;

		$data = array(
			'user_id'       => get_current_user_id(),
			'type'          => sanitize_text_field( $type ),
			'target_id'     => $target_id ? absint( $target_id ) : null,
			'tokens_used'   => absint( $tokens_used ),
			'latency_ms'    => absint( $latency_ms ),
			'status'        => sanitize_text_field( $status ),
			'error_message' => $error_message ? sanitize_textarea_field( $error_message ) : null,
			'created_at'    => current_time( 'mysql' ),
		);

		$format = array(
			'%d',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
		);

		$result = $wpdb->insert( self::$table_name, $data, $format );

		if ( $result ) {
			return $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * Get statistics for a user.
	 *
	 * @param int    $user_id    User ID (0 for all users).
	 * @param string $start_date Start date (Y-m-d format).
	 * @param string $end_date   End date (Y-m-d format).
	 * @return array Statistics.
	 */
	public function get_stats( $user_id = 0, $start_date = null, $end_date = null ) {
		global $wpdb;

		// Build WHERE clause.
		$where = array( '1=1' );

		if ( $user_id > 0 ) {
			$where[] = $wpdb->prepare( 'user_id = %d', $user_id );
		}

		if ( $start_date ) {
			$where[] = $wpdb->prepare( 'DATE(created_at) >= %s', $start_date );
		}

		if ( $end_date ) {
			$where[] = $wpdb->prepare( 'DATE(created_at) <= %s', $end_date );
		}

		$where_clause = implode( ' AND ', $where );

		// Get total counts.
		$stats = array(
			'total_generations'  => 0,
			'successful'         => 0,
			'failed'             => 0,
			'total_tokens'       => 0,
			'avg_latency_ms'     => 0,
			'by_type'            => array(),
			'recent_generations' => array(),
		);

		// Total counts.
		$total = $wpdb->get_row(
			"SELECT 
				COUNT(*) as total,
				SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
				SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
				SUM(tokens_used) as total_tokens,
				AVG(latency_ms) as avg_latency
			FROM " . self::$table_name . "
			WHERE $where_clause"
		);

		if ( $total ) {
			$stats['total_generations'] = (int) $total->total;
			$stats['successful']        = (int) $total->successful;
			$stats['failed']            = (int) $total->failed;
			$stats['total_tokens']      = (int) $total->total_tokens;
			$stats['avg_latency_ms']    = (int) $total->avg_latency;
		}

		// By type.
		$by_type = $wpdb->get_results(
			"SELECT 
				type,
				COUNT(*) as count,
				SUM(tokens_used) as tokens,
				AVG(latency_ms) as avg_latency
			FROM " . self::$table_name . "
			WHERE $where_clause
			GROUP BY type
			ORDER BY count DESC"
		);

		foreach ( $by_type as $row ) {
			$stats['by_type'][ $row->type ] = array(
				'count'       => (int) $row->count,
				'tokens'      => (int) $row->tokens,
				'avg_latency' => (int) $row->avg_latency,
			);
		}

		// Recent generations.
		$stats['recent_generations'] = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM " . self::$table_name . "
				WHERE $where_clause
				ORDER BY created_at DESC
				LIMIT %d",
				10
			),
			ARRAY_A
		);

		return $stats;
	}

	/**
	 * Get total token usage for a user.
	 *
	 * @param int    $user_id    User ID (0 for all users).
	 * @param string $start_date Start date (Y-m-d format).
	 * @param string $end_date   End date (Y-m-d format).
	 * @return int Total tokens used.
	 */
	public function get_token_usage( $user_id = 0, $start_date = null, $end_date = null ) {
		global $wpdb;

		$where = array( '1=1' );

		if ( $user_id > 0 ) {
			$where[] = $wpdb->prepare( 'user_id = %d', $user_id );
		}

		if ( $start_date ) {
			$where[] = $wpdb->prepare( 'DATE(created_at) >= %s', $start_date );
		}

		if ( $end_date ) {
			$where[] = $wpdb->prepare( 'DATE(created_at) <= %s', $end_date );
		}

		$where_clause = implode( ' AND ', $where );

		$total = $wpdb->get_var(
			"SELECT SUM(tokens_used) FROM " . self::$table_name . " WHERE $where_clause"
		);

		return $total ? (int) $total : 0;
	}

	/**
	 * Delete old logs.
	 *
	 * @param int $days Delete logs older than X days.
	 * @return int|false Number of rows deleted.
	 */
	public function delete_old_logs( $days = 90 ) {
		global $wpdb;

		$date = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

		return $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM " . self::$table_name . " WHERE created_at < %s",
				$date
			)
		);
	}
}


