<?php

namespace FernleafSystems\Wordpress\Services\Core;

/**
 * Class Db
 */
class Db {

	/**
	 * @var \wpdb
	 */
	protected $wpdb;

	/**
	 * @param string $sql
	 * @return array
	 */
	public function dbDelta( string $sql ) {
		require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
		return dbDelta( $sql );
	}

	/**
	 * @param string $table
	 * @param array  $where - delete where (associative array)
	 * @return false|int
	 */
	public function deleteRowsFromTableWhere( string $table, array $where ) {
		return $this->loadWpdb()->delete( $table, $where );
	}

	/**
	 * Will completely remove this table from the database
	 *
	 * @param string $table
	 * @return bool|int
	 */
	public function doDropTable( string $table ) {
		return $this->doSql( sprintf( 'DROP TABLE IF EXISTS `%s`', $table ) );
	}

	/**
	 * Alias for doTruncateTable()
	 *
	 * @param string $table
	 * @return bool|int
	 */
	public function doEmptyTable( string $table ) {
		return $this->doTruncateTable( $table );
	}

	/**
	 * Given any SQL query, will perform it using the WordPress database object.
	 *
	 * @param string $sqlQuery
	 * @return mixed|int|bool (number of rows affected or just true/false)
	 */
	public function doSql( string $sqlQuery ) {
		return $this->loadWpdb()->query( $sqlQuery );
	}

	/**
	 * @param string $table
	 * @return bool|int
	 */
	public function doTruncateTable( string $table ) {
		return $this->getIfTableExists( $table ) ?
			$this->doSql( sprintf( 'TRUNCATE TABLE `%s`', $table ) )
			: false;
	}

	public function getCharCollate() :string {
		return $this->getWpdb()->get_charset_collate();
	}

	public function getIfTableExists( string $table ) :bool {
		$mResult = $this->loadWpdb()->get_var( sprintf( "SHOW TABLES LIKE '%s'", $table ) );
		return !is_null( $mResult );
	}

	/**
	 * @param string   $tableName
	 * @param callable $callBack
	 * @return array
	 */
	public function getColumnsForTable( $tableName, $callBack = '' ) :array {
		$columns = $this->loadWpdb()->get_col( "DESCRIBE ".$tableName, 0 );

		if ( !empty( $callBack ) && function_exists( $callBack ) ) {
			return array_map( $callBack, $columns );
		}
		return is_array( $columns ) ? $columns : [];
	}

	public function getPrefix( bool $siteBase = true ) :string {
		return $siteBase ? $this->loadWpdb()->base_prefix : $this->loadWpdb()->prefix;
	}

	public function getTable_Comments() :string {
		return $this->loadWpdb()->comments;
	}

	public function getTable_Options() :string {
		return $this->loadWpdb()->options;
	}

	public function getTable_Posts() :string {
		return $this->loadWpdb()->posts;
	}

	public function getTable_Users() :string {
		return $this->loadWpdb()->users;
	}

	/**
	 * @param string $sSql
	 * @return null|mixed
	 */
	public function getVar( $sSql ) {
		return $this->loadWpdb()->get_var( $sSql );
	}

	/**
	 * @param string $table
	 * @param array  $data
	 * @return int|bool
	 */
	public function insertDataIntoTable( $table, $data ) {
		return $this->loadWpdb()->insert( $table, $data );
	}

	/**
	 * @param string $table
	 * @param string $format
	 * @return mixed
	 */
	public function selectAllFromTable( string $table, $format = ARRAY_A ) {
		return $this->loadWpdb()
					->get_results( sprintf( "SELECT * FROM `%s` WHERE `deleted_at` = 0", $table ), $format );
	}

	/**
	 * @param string $query
	 * @param        $format
	 * @return array|bool
	 */
	public function selectCustom( $query, $format = ARRAY_A ) {
		return $this->loadWpdb()->get_results( $query, $format );
	}

	/**
	 * @param string $query
	 * @param string $format
	 * @return null|object|array
	 */
	public function selectRow( string $query, $format = ARRAY_A ) {
		return $this->loadWpdb()->get_row( $query, $format );
	}

	/**
	 * @param string $table
	 * @param array  $data  - new insert data (associative array, column=>data)
	 * @param array  $where - insert where (associative array)
	 * @return int|bool (number of rows affected)
	 */
	public function updateRowsFromTableWhere( string $table, array $data, array $where ) {
		return $this->loadWpdb()->update( $table, $data, $where );
	}

	public function loadWpdb() :\wpdb {
		if ( !$this->wpdb instanceof \wpdb ) {
			$this->wpdb = $this->getWpdb();
		}
		return $this->wpdb;
	}

	/**
	 * @return \wpdb
	 */
	private function getWpdb() {
		global $wpdb;
		return $wpdb;
	}
}