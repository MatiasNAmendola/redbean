<?php

namespace RedBean;

//Using the following RedBeanPHP Components: 

use RedBean\ToolBox;
use RedBean\Driver\RBPDO;
use RedBean\Driver\RBOCI;
use RedBean\Adapter\DBAdapter;
use RedBean\QueryWriter\PostgreSQL;
use RedBean\QueryWriter\SQLiteT;
use RedBean\QueryWriter\CUBRID;
use RedBean\QueryWriter\Oracle;
use RedBean\QueryWriter\MySQL;
use RedBean\OODB;

/**
 * RedBean Setup
 * Helper class to quickly setup RedBean for you.
 *
 * @file    RedBean/Setup.php
 * @desc    Helper class to quickly setup RedBean for you
 * @author  Gabor de Mooij and the RedBeanPHP community
 * @license BSD/GPLv2
 *
 * copyright (c) G.J.G.T. (Gabor) de Mooij and the RedBeanPHP Community
 * This source file is subject to the BSD/GPLv2 License that is bundled
 * with this source code in the file license.txt.
 */
class Setup
{

	/**
	 * This method checks the DSN string.
	 * Checks the validity of the DSN string.
	 * If the DSN contains an invalid database identifier this method
	 * will trigger an error.
	 *
	 * @param string $dsn
	 *
	 * @return boolean
	 */
	private static function checkDSN( $dsn )
	{
		if ( !preg_match( '/^(mysql|sqlite|pgsql|cubrid|oracle):/', strtolower( trim( $dsn ) ) ) ) {
			trigger_error( 'Unsupported DSN' );
		}

		return true;
	}

	/**
	 * Initializes the database and prepares a toolbox.
	 *
	 * @param  string|PDO $dsn      Database Connection String (or\PDO instance)
	 * @param  string     $username Username for database
	 * @param  string     $password Password for database
	 * @param  boolean    $frozen   Start in frozen mode?
	 *
	 * @return ToolBox
	 */
	public static function kickstart( $dsn, $username = null, $password = null, $frozen = false )
	{
		if ( $dsn instanceof\PDO ) {
			$db  = new RBPDO( $dsn );
			$dsn = $db->getDatabaseType();
		} else {
			self::checkDSN( $dsn );

			if ( strpos( $dsn, 'oracle' ) === 0 ) {
				$db = new RBOCI( $dsn, $username, $password );
			} else {
				$db = new RBPDO( $dsn, $username, $password );
			}
		}

		$adapter = new DBAdapter( $db );

		if ( strpos( $dsn, 'pgsql' ) === 0 ) {
			$writer = new PostgreSQL( $adapter );
		} else if ( strpos( $dsn, 'sqlite' ) === 0 ) {
			$writer = new SQLiteT( $adapter );
		} else if ( strpos( $dsn, 'cubrid' ) === 0 ) {
			$writer = new CUBRID( $adapter );
		} else if ( strpos( $dsn, 'oracle' ) === 0 ) {
			$writer = new Oracle( $adapter );
		} else {
			$writer = new MySQL( $adapter );
		}

		$redbean = new OODB( $writer );

		if ( $frozen ) {
			$redbean->freeze( true );
		}

		$toolbox = new ToolBox( $redbean, $adapter, $writer );

		return $toolbox;
	}
}
