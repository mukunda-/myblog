<?php

put( get_motd() );

put( "" );

function show_browse() {
   $years = get_years();
   $yearcount = 0;
   for( $index = 0; $index < count($years); $index += 7 ) {
      $batch = array_slice( $years, $index, 7 );
      
      if( $index == 0 ) {
         $prefix = "* Browse articles: ";
      } else {
         $prefix = "*                  ";
      }
      $formats = implode( " ", array_map( function( $a ) {
         return "[$a](/list/$a)";
      }, $batch ));

      $batch = implode( ", ", $batch );
      put( "$prefix$batch $formats" );
   }
}

show_browse();

put( "" );
put( "
------------------------------------------------------------
Recent Articles
------------------------------------------------------------
" );
put( "" );

$recent_entries = get_recent_blogs();

foreach( $recent_entries as $entry ) {
   $name = $entry['name'];
   $linkpattern = preg_quote( $name, "+" );
   $file = $entry['file'];
   $date = date( "Y-m-d", $entry['cdate'] );
   
   $name = explode( "\n", $name );
   put( "
************************************************************
");
   put( "* Name: $name[0] [(Name: )(.*)](/cat/$file)" );
   array_shift( $name );
   foreach( $name as $line ) {
      put( "        $line [( *)(.*)](/cat/$file)", false );
   }
   
   put( "* Date: $date" );
   put( "" );
   put( "$entry[preview]" );
   put( "" );
}

put( "
////////////////////////////////////////////////////////////
");
