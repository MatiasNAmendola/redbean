<?php

//Namespace script for RedBeanPHP 4.0
function globr($path, $find) 
{
    $dh = opendir($path);
    while (($file = readdir($dh)) !== false) {
        if (substr($file, 0, 1) == '.') continue;
        $rfile = "{$path}/{$file}";
        if (is_dir($rfile)) {
            foreach (globr($rfile, $find) as $ret) {
                yield $ret;
            }
        } else {
            if (fnmatch($find, $file)) yield $rfile;
        }
    }
    closedir($dh);
}

function mapNamespaceStr( $str ) 
{
		static $map = array(
			 'Exception' => 'RException',			 
		);
		
		if ( isset( $map[$str] ) ) {
			return $map[ $str ];
		} else {
			return $str;
		}
}

function mapNamespaceArray( $arr ) 
{
	foreach( $arr as $key => $value ) {
		$arr[ $key ] = mapNamespaceStr( $value );
	}
	
	return $arr;
}


$filter = [
	 'RedBean/autoloader.php' => true,
	 'RedBean/redbean.inc.php' => true,
];

$natObjects = [
	'IteratorAggregate', 
	'ArrayAccess',
	'RuntimeException',
	'LogicException',
	'Exception',
	'PDO',
	'ArrayIterator',
	'Countable'
];


foreach( globr('RedBean', '*.php') as $file ) {
	
	echo "\n PROCESSING: $file";
	
	//filter
	if ( isset( $filter[ $file ] ) ) {
		echo "\n SKIP!";
		continue;
	}
	
	//determine namespace
	$nsRaw      = str_replace( '.php', '', str_replace( '/', '\\', $file ) );
	$nsElements = explode( '\\', $nsRaw );
	$className  = array_pop( $nsElements );
	$ns         = implode( '\\', $nsElements );
	
	$nsElements = mapNamespaceArray( $nsElements );
	$className  = mapNamespaceStr( $className );
	
	$ps = str_replace( '.php', '', str_replace( '/', '_', $file ) );
	echo "\n CONVERT $ps -> $ns : $className ";
	
	//load the code
	$code = file_get_contents( $file );
	
	//replace the class name in the code
	$code = str_replace( $ps, $className, $code );
	
	//add the namespace
	$beginOfCode = '<' . '?' . 'php';
	
	//now find any other namespace usage
	$useList = [];
	$code = preg_replace_callback( '/RedBean_\w+/', function( $matches ) 
	use(
			  &$useList
	) {
		
		$pRef            = $matches[0];
		$pRefElements    = explode( '_', $pRef );		
		$lastElement     = end( $pRefElements );
		
		$pRefElements    = mapNamespaceArray( $pRefElements );
		$lastElement     = mapNamespaceStr( $lastElement );
	
		
		$use             = implode( '\\', $pRefElements );  
		$useList[ $use ] = $use;
		
		return $lastElement;
		
	}, $code );
	
	$namespaceDeclaration = $beginOfCode . "\n\n" . 'namespace '.$ns.";";
	
	$useDeclaration = '';
	
	if ( count( $useList ) > 0 ) {
		$useDeclaration .= "\n\n//Using the following RedBeanPHP Components: \n";
		foreach( $useList as $useItem ) {
			$useDeclaration .= "\nuse $useItem;" ;
		}
	}
	 
	$newBeginOfCode = $namespaceDeclaration . $useDeclaration ."\n";
	$code           = str_replace( $beginOfCode, $newBeginOfCode, $code );
	
	//Fix native objects
	foreach ( $natObjects as $natObj ) {
		$code = str_replace( " " . $natObj, "\\$natObj",  $code );
		$code = str_replace( "(" . $natObj, "(\\$natObj", $code );
		$code = str_replace( "," . $natObj, ",\\$natObj", $code );
		$code = str_replace( ";" . $natObj, ";\\$natObj", $code );
		$code = str_replace( "{" . $natObj, "{\\$natObj", $code );
	}
	
	echo "\n CODE BECOMES: \n=========================\n". substr( $code, 0, 700) . "\n=========================\n";
	
	file_put_contents( $file, $code );
	
}

foreach( globr('testing/RedUNIT', '*.php') as $file ) {
	
	echo "\n PROCESSING: $file";
	
	//determine namespace
	$cleanFile  = str_replace( 'testing/', '', $file );
	$nsRaw      = str_replace( '.php', '', str_replace( '/', '\\', $cleanFile ) );
	$nsElements = explode( '\\', $nsRaw );
	$className  = array_pop( $nsElements );
	$ns         = implode( '\\', $nsElements );
	
	$nsElements = mapNamespaceArray( $nsElements );
	$className  = mapNamespaceStr( $className );
	
	$ps = str_replace( '.php', '', str_replace( '/', '_', $cleanFile ) );
	echo "\n CONVERT $ps -> $ns : $className ";
	
	//load the code
	$code = file_get_contents( $file );
	
	//replace the class name in the code
	$code = str_replace( $ps, $className, $code );
	
	//add the namespace
	$beginOfCode = '<' . '?' . 'php';
	
	//now find any other namespace usage
	$useList = [];
	$code = preg_replace_callback( '/RedBean_\w+/', function( $matches ) 
	use(
			  &$useList
	) {
		
		$pRef            = $matches[0];
		$pRefElements    = explode( '_', $pRef );		
		$lastElement     = end( $pRefElements );
		
		$pRefElements    = mapNamespaceArray( $pRefElements );
		$lastElement     = mapNamespaceStr( $lastElement );
	
		
		$use             = implode( '\\', $pRefElements );  
		$useList[ $use ] = $use;
		
		return $lastElement;
		
	}, $code );
	
	$namespaceDeclaration = $beginOfCode . "\n\n" . 'namespace '.$ns.";";
	
	$useDeclaration = '';
	
	if ( count( $useList ) > 0 ) {
		$useDeclaration .= "\n\n//Using the following RedBeanPHP Components: \n";
		foreach( $useList as $useItem ) {
			$useDeclaration .= "\nuse $useItem;" ;
		}
	}
	
	$newBeginOfCode = $namespaceDeclaration . $useDeclaration ."\n";
	$code           = str_replace( $beginOfCode, $newBeginOfCode, $code );
	
	//Fix native objects
	foreach ( $natObjects as $natObj ) {
		$code = str_replace( " " . $natObj, "\\$natObj",  $code );
		$code = str_replace( "(" . $natObj, "(\\$natObj", $code );
		$code = str_replace( "," . $natObj, ",\\$natObj", $code );
		$code = str_replace( ";" . $natObj, ";\\$natObj", $code );
		$code = str_replace( "{" . $natObj, "{\\$natObj", $code );
	}
	
	echo "\n CODE BECOMES: \n=========================\n". substr( $code, 0, 700) . "\n=========================\n";
	
	file_put_contents( $file, $code );
}