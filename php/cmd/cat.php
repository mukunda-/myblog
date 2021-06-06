<?php
global $MediaPrefix;

$MediaPrefix = '/content/' . dirname( $PathArg ) . '/media';

// Sanitize.
$PathArg = str_replace( "..", "", $PathArg );

if( !file_exists("content/$PathArg") ) {
    put( "File not found." );
} else if( is_dir("content/$PathArg") ) {
    put( "File is a directory." );
} else if( !is_file("content/$PathArg") ) {
    put( "File is not a file..." );
} else {
    put( file_get_contents( "content/$PathArg" ) );
}

put( "" );
put( "<< Index [<< Index](/)" );