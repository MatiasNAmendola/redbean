<?php

namespace RedUNIT\Base;

//Using the following RedBeanPHP Components:
use RedBean\Facade as R;
use RedUNIT\Base; 
use RedBean\SimpleModel;

/**
 * Nuke
 *
 * @file    RedUNIT/Base/Nuke.php
 * @desc    Test the nuke() function.
 * @author  Gabor de Mooij and the RedBeanPHP Community
 * @license New BSD/GPLv2
 *
 * (c) G.J.G.T. (Gabor) de Mooij and the RedBeanPHP Community.
 * This source file is subject to the New BSD/GPLv2 License that is bundled
 * with this source code in the file license.txt.
 */
class Nuke extends Base
{
	/**
	 * Nuclear test suite.
	 * 
	 * @return void
	 */
	public function testNuke()
	{
		$bean = R::dispense( 'bean' );

		R::store( $bean );

		asrt( count( R::$writer->getTables() ), 1 );

		R::nuke();

		asrt( count( R::$writer->getTables() ), 0 );

		$bean = R::dispense( 'bean' );

		R::store( $bean );

		asrt( count( R::$writer->getTables() ), 1 );

		R::freeze();

		R::nuke();

		// No effect
		asrt( count( R::$writer->getTables() ), 1 );

		R::freeze( false );
	}
}
