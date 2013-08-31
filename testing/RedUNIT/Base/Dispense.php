<?php

namespace RedUNIT\Base;

//Using the following RedBeanPHP Components:
use RedBean\Facade as R;


//Using the following RedBeanPHP Components: 

use RedUNIT\Base; 

use RedBean\RException\Security;

/**
 * Dispense
 *
 * @file    RedUNIT/Base/Dispense.php
 * @desc    Tests bean dispensing functionality.
 * @author  Gabor de Mooij and the RedBeanPHP Community
 * @license New BSD/GPLv2
 *
 * (c) G.J.G.T. (Gabor) de Mooij and the RedBeanPHP Community.
 * This source file is subject to the New BSD/GPLv2 License that is bundled
 * with this source code in the file license.txt.
 */
class Dispense extends Base
{
	/**
	 * Test dispense.
	 * 
	 * @return void
	 */
	public function testBasicsDispense()
	{
		$redbean = R::$redbean;

		// Can we dispense a bean?
		$page = $redbean->dispense( "page" );

		// Does it have a meta type?
		asrt( ( (bool) $page->getMeta( "type" ) ), true );

		// Does it have an ID?
		asrt( isset( $page->id ), true );

		// Type should be 'page'
		asrt( ( $page->getMeta( "type" ) ), "page" );

		// ID should be 0 because bean does not exist in database yet.
		asrt( ( $page->id ), 0 );

		// Try some faulty dispense actions.
		foreach ( array( "", ".", "-") as $value ) {
			try {
				$redbean->dispense( $value );

				fail();
			} catch ( Security $e ) {
				pass();
			}
		}

		$bean = $redbean->dispense( "testbean" );

		$bean["property"] = 123;
		$bean["abc"]      = "def";

		asrt( $bean["property"], 123 );
		asrt( $bean["abc"], "def" );
		asrt( $bean->abc, "def" );

		asrt( isset( $bean["abd"] ), false );
		asrt( isset( $bean["abc"] ), true );
	}
}
