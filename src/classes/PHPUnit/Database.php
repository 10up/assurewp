<?php
/**
 * Database (MySQL) helper functionality to integrate with test class
 *
 * @package  wpassure
 */

namespace WPAssure\PHPUnit;

use WPAssure\EnvironmentFactory;

/**
 * Database trait
 */
trait Database {

	/**
	 * Execute MySQL query and return results.
	 *
	 * @static
	 * @access protected
	 * @param string $query A query to execute.
	 * @return \mysqli_result Results of execution.
	 */
	protected static function query( $query ) {
		$mysql = EnvironmentFactory::get()->getMySQLClient();

		// @todo: log query
		return $mysql->query( $query );
	}

	/**
	 * Return table name with proper prefix.
	 *
	 * @static
	 * @access protected
	 * @param string $table A table name without a prefix.
	 * @return string A table name with a prefix.
	 */
	protected static function getTableName( $table ) {
		return EnvironmentFactory::get()->getMySQLClient()->getTablePrefix() . $table;
	}

	/**
	 * Parse arguments and return WHERE statements for a query.
	 *
	 * Valid arguments:
	 *   post_title  - (array|string) a post title or array of titles.
	 *   post_type   - (array|string) a post type or array of types.
	 *   post_status - (array|string) a post status or array of statuses.
	 *
	 * @static
	 * @access protected
	 * @param array $args Array of arguments.
	 * @return string Query conditions without "WHERE" keyword.
	 */
	protected static function parsePostsWhere( array $args = array() ) {
		$defaults = array(
			'post_type'   => 'post',
			'post_status' => 'publish',
		);

		$params = array_merge( $defaults, $args );

		$conditions = array();
		$mysql      = EnvironmentFactory::get()->getMySQLClient();

		$keys = array( 'ID', 'post_title', 'post_status', 'post_type' );
		foreach ( $keys as $key ) {
			if ( ! empty( $params[ $key ] ) ) {
				$condition = sprintf( '`%s` = ', $mysql->escape( $key ) );
				if ( is_array( $params[ $key ] ) ) {
					$values     = array_map( array( $mysql, 'escape' ), $params[ $key ] );
					$condition .= sprintf( 'IN ("%s")', implode( '", "', $values ) );
				} else {
					$condition .= sprintf( '"%s"', $mysql->escape( $params[ $key ] ) );
				}

				$conditions[] = $condition;
			}
		}

		return implode( ' AND ', $conditions );
	}

	/**
	 * Return ID of the latest post in the posts table.
	 *
	 * Valid arguments:
	 *   post_type   - (array|string) a post type or array of types.
	 *   post_status - (array|string) a post status or array of statuses.
	 *
	 * @access public
	 * @param array $args Array of arguments.
	 * @return int The latest post ID if found, otherwise FALSE.
	 */
	public static function getLastPostId( array $args = array() ) {
		$table = static::getTableName( 'posts' );
		$where = static::parsePostsWhere( $args );
		if ( ! empty( $where ) ) {
			$where = ' WHERE ' . $where;
		}

		$results = self::query( "SELECT ID FROM {$table}{$where} ORDER BY `ID` DESC LIMIT 1" )->fetch_assoc();

		if ( ! empty( $results ) ) {
			return (int) $results['ID'];
		}

		return false;
	}

	/**
	 * Assert if new posts exist after a post with provided id. Use "self::getLastPostId(...)" to get the current latest ID.
	 *
	 * @static
	 * @access public
	 * @param int    $since_post_id A post id to compare new posts with.
	 * @param array  $args Array of arguments that has been used to receive the latest post id.
	 * @param string $message Optinal. A message to use on failure.
	 */
	public static function assertNewPostsExist( $since_post_id, array $args = array(), $message = '' ) {
		$new_last_id = self::getLastPostId( $args );

		if ( empty( $message ) ) {
			$message = 'The latest ID must be bigger than provided post ID.';
		}

		static::assertGreaterThan( (int) $since_post_id, (int) $new_last_id, $message );
	}

	/**
	 * Assert that a post exists in the database.
	 *
	 * Valid arguments:
	 *   post_title  - (array|string) a post title or array of titles.
	 *   post_type   - (array|string) a post type or array of types.
	 *   post_status - (array|string) a post status or array of statuses.
	 *
	 * @static
	 * @acess public
	 * @param array  $args Array of arguments.
	 * @param string $message Optinal. A message to use on failure.
	 */
	public static function assertPostExists( array $args, $message = '' ) {
		$post_id = static::getLastPostId( $args );

		if ( empty( $message ) ) {
			$message = 'A post must exist in the database.';
		}

		static::assertGreaterThan( 0, (int) $post_id, $message );
	}

}