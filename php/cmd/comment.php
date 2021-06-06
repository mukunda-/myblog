<?php

put( "
------------------------------------------------------------
");

$article = $_GET['article'] ?? "";
if( $article ) {
    put( "" );
    put( "*** Commenting on article: " . $_GET["article"] );
}

putraw( "<form action='sendcomment' method='POST'>"
. "<input type='hidden' name='article' value='$article'>"
. "<input type='hidden' name='abc' value='abc'>" );

put( "What email can I reach you?" );
put( "" );
putraw( ">> <input name='email'>" );

put( "" );
put( "If responding to your comment on my blog, can I share
your identity?" );

put( "" );
putraw( "
>> <select name='ident'><option>No, I want to remain anonymous.</option><option>Yes, that's fine.</option></select>
" );

//put( "" );
//put( "Are you a human?" );
//put( "" );
//
//putraw( ">> <input name='scheck'>" );

put( "" );
put( "Please write your comment below and then press Submit." );
put( "
------------------------------------------------------------" );

putraw( "
<textarea name='comment' rows='12' cols='60'></textarea>
");

putraw( "<input type='submit'>" );