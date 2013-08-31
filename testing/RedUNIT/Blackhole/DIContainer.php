<?php

namespace RedUNIT\Blackhole;

//Using the following RedBeanPHP Components:
use RedBean\DependencyInjector;
use RedBean\ModelHelper;
use RedBean\SimpleModel;
use RedBean\Facade as R;
use RedUNIT\Blackhole;

/**
 * DIContainer
 *
 * @file    RedUNIT/Blackhole/DIContainer.php
 * @desc    Tests dependency injection architecture.
 * @author  Gabor de Mooij and the RedBeanPHP Community
 * @license New BSD/GPLv2
 *
 * (c) G.J.G.T. (Gabor) de Mooij and the RedBeanPHP Community.
 * This source file is subject to the New BSD/GPLv2 License that is bundled
 * with this source code in the file license.txt.
 */
class DIContainer extends Blackhole
{
	/**
	 * Test dependency injection with RedBeanPHP.
	 * 
	 * @return void
	 */
	public function testDependencyInjection()
	{
		// base scenario
		$geek = R::dispense( 'geek' );

		list( $coffee, $cocoa ) = $geek->getObjects();

		asrt( ( $coffee instanceof \Dependency_Coffee ), false );
		asrt( ( $cocoa instanceof \Dependency_Cocoa ), false );

		// Base scenario with empty container, don't fail
		$di = new DependencyInjector;

		$geek = R::dispense( 'geek' );

		list( $coffee, $cocoa ) = $geek->getObjects();

		asrt( ( $coffee instanceof \Dependency_Coffee ), false );
		asrt( ( $cocoa instanceof \Dependency_Cocoa ), false );

		// Succesfull scenario, one missing
		$di = new DependencyInjector;

		$di->addDependency( 'Coffee', new \Dependency_Coffee );

		ModelHelper::setDependencyInjector( $di );

		$geek = R::dispense( 'geek' );

		list( $coffee, $cocoa ) = $geek->getObjects();

		asrt( ( $coffee instanceof \Dependency_Coffee ), true );
		asrt( ( $cocoa instanceof \Dependency_Cocoa ), false );

		// Success scenario
		$di = new DependencyInjector;

		$di->addDependency( 'Coffee', new \Dependency_Coffee );
		$di->addDependency( 'Cocoa', new \Dependency_Cocoa );

		ModelHelper::setDependencyInjector( $di );

		$geek = R::dispense( 'geek' );

		list( $coffee, $cocoa ) = $geek->getObjects();

		asrt( ( $coffee instanceof \Dependency_Coffee ), true );
		asrt( ( $cocoa instanceof \Dependency_Cocoa ), true );

		// Don't fail if not exists
		$di->addDependency( 'NonExistantObject', new \Dependency_Coffee );

		$geek = R::dispense( 'geek' );
		$geek = R::dispense( 'geek' );

		list( $coffee, $cocoa ) = $geek->getObjects();

		asrt( ( $coffee instanceof \Dependency_Coffee ), true );
		asrt( ( $cocoa instanceof \Dependency_Cocoa ), true );

		// Can we go back to base scenario?
		ModelHelper::clearDependencyInjector();

		$geek = R::dispense( 'geek' );

		list( $coffee, $cocoa ) = $geek->getObjects();

		asrt( ( $coffee instanceof \Dependency_Coffee ), false );
		asrt( ( $cocoa instanceof \Dependency_Cocoa ), false );
	}
}
