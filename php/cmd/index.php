<?php

put("
* Welcome to my blog. Just notes and musings here. I'm a
* self-taught programmer and a support engineer. I work
* remotely and pretty much live online. Hope you find
* something useful. Opinions are my own.

* Twitter: twitter.com/_mukunda
* GitHub: github.com/mukunda-
* LinkedIn: linkedin.com/in/mukunda-johnson
* Email: mukunda@mukunda.com
* Homepage: mukunda.com

[twitter.com/_mukunda](https://twitter.com/_mukunda) 
[github.com/mukunda-](https://github.com/mukunda-)
[linkedin.com/in/mukunda-johnson](https://linkedin.com/in/mukunda-johnson/)
[mukunda@mukunda.com](mailto:mukunda@mukunda.com)
[(Homepage: )(mukunda.com)](https://mukunda.com)
");

put( "" );
put( "-- Recent entries --" );
put( "" );

$recent_entries = get_recent_blogs();

foreach( $recent_entries as $entry ) {
   $name = $entry['name'];
   $linkpattern = preg_quote( $name, "+" );
   $file = $entry['file'];
   $date = date( "Y-m-d", $entry['cdate'] );
   put( "* Name: $name [$linkpattern](/cat/$file)" );
   put( "* Date: $date" );
   put( "" );
   put( "$entry[preview]" );
   put( "" );
}

put( "-- Browse --" );
put( "" );

$years = get_years();

foreach( $years as $year ) {
   put( "* $year [$year](/list/$year)" );  
}
