<?php

if( !preg_match('/^\d\d\d\d$/', $PathArg) ) {
   put( "Invalid input." );
}

$meta = [];

put( "Listing files for $PathArg." );
put( "---" );
put( "" );

// Sort files by date
foreach( glob( "content/$PathArg/*.txt" ) as $file ) {
   $m = get_file_meta( $file );
   $meta[] = $m;
}

usort( $meta, function( $a, $b ) {
   if( $a['cdate'] == $b['cdate'] ) return 0;
   return ( $a['cdate'] > $b['cdate'] ) ? -1 : 1;
});

foreach( $meta as $m ) {
   $file = $m['file'];
   $date = date("Y-m-d", $m['cdate']);
   $name = $m['name'] ?? "<Unknown name>";
   $linkpattern = preg_quote($name,"+");

   $name = explode( "\n", $name );
   put( "* $date $name[0] [(\* \d\d\d\d-\d\d-\d\d )(.*)](/cat/$file)" );
   array_shift( $name );
   foreach( $name as $line ) {
      put( "             $line [( *)(.*)](/cat/$file)", false );
   }
   
}

put( "" );
put( "<< Index [<< Index](/)" );