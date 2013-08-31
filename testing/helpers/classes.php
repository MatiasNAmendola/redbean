<?php

/**
 * RedUNIT Shared Test Classes / Mock Objects
 * This file contains a collection of test classes that can be used by
 * and shared by tests.
 */

use RedBean\Observable;
use RedBean\Observer;
use RedBean\Facade as R;
use RedBean\IModelFormatter;
use RedBean\SimpleModel;
use RedBean\QueryWriter\MySQL;
use RedBean\RException\SQL;
use RedBean\Logger\LDefault;

/**
 * Observable Mock
 * This is just for testing
 */
class ObservableMock extends Observable
{
	/**
	 * @param $eventname
	 * @param $info
	 */
	public function test( $eventname, $info )
	{
		$this->signal( $eventname, $info );
	}
}

/**
 * Observer Mock
 * This is just for testing
 */
class ObserverMock implements Observer
{
	/**
	 * @var bool
	 */
	public $event = false;

	/**
	 * @var bool
	 */
	public $info = false;

	/**
	 * @param string $event
	 * @param        $info
	 */
	public function onEvent( $event, $info )
	{
		$this->event = $event;
		$this->info  = $info;
	}
}

/**
 * Shared helper class for tests.
 * A Basic Model Formatter for FUSE tests.
 */
class mymodelformatter implements IModelFormatter
{
	/**
	 * @param string $model
	 *
	 * @return string
	 */
	public function formatModel( $model )
	{
		return "my_weird_" . $model . "_model";
	}
}

/**
 * Shared helper class for tests.
 * Default Model Formatter to reset model formatting in FUSE tests.
 */
class DefaultModelFormatter implements IModelFormatter
{
	/**
	 * @param string $model
	 *
	 * @return string
	 */public function formatModel( $model )
	{
		return '\\Model_' . ucfirst( $model );
	}
}

/**
 * Shared helper class for tests.
 * A Basic Model Formatter for FUSE tests.
 */
class my_weird_weirdo_model extends SimpleModel
{
	/**
	 * @return string
	 */
	public function blah()
	{
		return "yes!";
	}
}

/**
 * Shared helper class for tests.
 * A test model to test FUSE functions.
 */
class Model_Band extends SimpleModel
{
	public function after_update() { }

	/**
	 * @throws Exception
	 */
	public function update()
	{
		if ( count( $this->ownBandmember ) > 4 ) {
			throw new Exception( 'too many!' );
		}
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return 'bigband';
	}

	/**
	 * @param $prop
	 * @param $value
	 */
	public function setProperty( $prop, $value )
	{
		$this->$prop = $value;
	}

	/**
	 * @param $prop
	 *
	 * @return bool
	 */
	public function checkProperty( $prop )
	{
		return isset( $this->$prop );
	}
}

/**
 * Shared helper class for tests.
 * A Model class for testing Models/FUSE and related features.
 */
class Model_Box extends SimpleModel
{
	public function delete() { $a = $this->bean->ownBottle; }
}

/**
 * Shared helper class for tests.
 * A Model class for testing Models/FUSE and related features.
 */
class Model_CandyBar extends SimpleModel
{
	/**
	 * @param $custom
	 *
	 * @return string
	 */
	public function customMethod( $custom )
	{
		return $custom . "!";
	}

	/**
	 * @throws Exception
	 */
	public function customMethodWithException()
	{
		throw new Exception( 'Oops!' );
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return 'candy!';
	}
}

/**
 * Shared helper class for tests.
 * A Model class for testing Models/FUSE and related features.
 */
class Model_Cocoa extends SimpleModel
{
	public function update()
	{
	}
}

/**
 * Shared helper class for tests.
 * A Model class for testing Models/FUSE and related features.
 */
class Model_Taste extends SimpleModel
{
	public function after_update()
	{
		asrt( count( $this->bean->ownCocoa ), 0 );
	}
}

/**
 * Shared helper class for tests.
 * A Model class for testing Models/FUSE and related features.
 */
class Model_Coffee extends SimpleModel
{
	public function update()
	{
		while ( count( $this->bean->ownSugar ) > 3 ) {
			array_pop( $this->bean->ownSugar );
		}
	}
}

/**
 * Shared helper class for tests.
 * A Model class for testing Models/FUSE and related features.
 */
class Model_Test extends SimpleModel
{
	public function update()
	{
		if ( $this->bean->item->val ) {
			$this->bean->item->val        = 'Test2';
			$can                          = R::dispense( 'can' );
			$can->name                    = 'can for bean';
			$s                            = reset( $this->bean->sharedSpoon );
			$s->name                      = "S2";
			$this->bean->item->deep->name = '123';
			$this->bean->ownCan[]         = $can;
			$this->bean->sharedPeas       = R::dispense( 'peas', 10 );
			$this->bean->ownChip          = R::dispense( 'chip', 9 );
		}
	}
}

global $lifeCycle;

/**
 * Shared helper class for tests.
 * A Model class for testing Models/FUSE and related features.
 */
class Model_Bandmember extends SimpleModel
{
	public function open()
	{
		global $lifeCycle;

		$lifeCycle .= "\n called open: " . $this->id;
	}

	public function dispense()
	{
		global $lifeCycle;

		$lifeCycle .= "\n called dispense() " . $this->bean;
	}

	public function update()
	{
		global $lifeCycle;

		$lifeCycle .= "\n called update() " . $this->bean;
	}

	public function after_update()
	{
		global $lifeCycle;

		$lifeCycle .= "\n called after_update() " . $this->bean;
	}

	public function delete()
	{
		global $lifeCycle;

		$lifeCycle .= "\n called delete() " . $this->bean;
	}

	public function after_delete()
	{
		global $lifeCycle;

		$lifeCycle .= "\n called after_delete() " . $this->bean;
	}
}


/*
 * Mock object needed for DI testing
 */
class Dependency_Coffee
{
}

/*
 * Mock object needed for DI testing
 */
class Dependency_Cocoa
{
}

/*
 * Mock object needed for DI testing
 */
class Model_Geek extends SimpleModel
{
	private $cocoa;
	private $coffee;

	public function setCoffee( Dependency_Coffee $coffee )
	{
		$this->coffee = $coffee;
	}

	public function setCocoa( Dependency_Cocoa $cocoa )
	{
		$this->cocoa = $cocoa;
	}

	public function getObjects()
	{
		return array( $this->coffee, $this->cocoa );
	}
}

/**
 * A model to box soup models :)
 */
class Model_Soup extends SimpleModel
{

	public function taste()
	{
		return 'A bit too salty';
	}
}

/**
 * Test Model.
 */
class Model_Boxedbean extends SimpleModel
{
}


/**
 * Mock class for testing purposes.
 */
class Model_Ghost_House extends SimpleModel
{
	public static $deleted = false;

	public function delete()
	{
		self::$deleted = true;
	}
}

/**
 * Mock class for testing purposes.
 */
class Model_Ghost_Ghost extends SimpleModel
{
	public static $deleted = false;

	public function delete()
	{
		self::$deleted = true;
	}
}

/**
 * Mock class for testing purposes.
 */
class FaultyWriter extends MySQL
{

	protected $sqlState;

	/**
	 * Mock method.
	 *
	 * @param string $sqlState sql state
	 */
	public function setSQLState( $sqlState )
	{
		$this->sqlState = $sqlState;
	}

	/**
	 * Mock method
	 *
	 * @param string $sourceType destination type
	 * @param string $destType   source type
	 *
	 * @throws SQL
	 */
	public function addConstraintForTypes( $sourceType, $destType )
	{
		$exception = new SQL;
		$exception->setSQLState( $this->sqlState );
		throw $exception;
	}
}


class Model_Page extends SimpleModel
{
	public function mail( $who )
	{
		return 'mail has been sent to ' . $who;
	}

	public function err()
	{
		throw new\Exception( 'fake error', 123 );
	}
}

class Model_Setting extends SimpleModel
{
	public static $closed = false;

	public function open()
	{
		if ( self::$closed ) throw new\Exception( 'closed' );
	}
}

/**
 * Custom Logger class.
 * For testing purposes.
 */
class CustomLogger extends LDefault
{

	private $log;

	public function getLogMessage()
	{
		return $this->log;
	}

	public function log()
	{
		$this->log = func_get_args();
	}
}
