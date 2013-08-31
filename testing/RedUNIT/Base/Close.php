<?php

namespace RedUNIT\Base;

//Using the following RedBeanPHP Components:
use RedBean\Facade as R;
use RedUNIT\Base; 
use RedBean\Setup;
use RedBean\ToolBox;
use RedBean\Driver\RBOCI;
use RedBean\QueryWriter\Oracle;
use RedBean\QueryWriter\SQLiteT;

/**
 * Close
 *
 * @file    RedUNIT/Base/Close.php
 * @desc    Tests database closing functionality.
 * @author  Gabor de Mooij and the RedBeanPHP Community
 * @license New BSD/GPLv2
 *
 * (c) G.J.G.T. (Gabor) de Mooij and the RedBeanPHP Community.
 * This source file is subject to the New BSD/GPLv2 License that is bundled
 * with this source code in the file license.txt.
 */
class Close extends Base
{
	
	/**
	 * Test closing database connection.
	 * 
	 * @return void
	 */
	public function testClose()
	{
		asrt( R::$adapter->getDatabase()->isConnected(), true );

		R::close();

		asrt( R::$adapter->getDatabase()->isConnected(), false );

		// Can we create a database using empty setup?
		R::setup();

		$id = R::store( R::dispense( 'bean' ) );

		asrt( ( $id > 0 ), true );

		// Test freeze via kickstart in setup
		$toolbox = Setup::kickstart( 'sqlite:/tmp/bla.txt', null, null, true );

		asrt( $toolbox->getRedBean()->isFrozen(), true );

		// Test Oracle setup
		$toolbox = Setup::kickstart( 'oracle:test-value', 'test', 'test', false );

		asrt( ( $toolbox instanceof ToolBox ), true );
	}
}
