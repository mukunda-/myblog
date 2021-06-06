<?php

global $CONFIG;

$email = $_POST['email'] ?? "";
$ident = $_POST['ident'] ?? "";
$comment = $_POST['comment'] ?? "";

if( $comment == "" || $_POST['abc'] != 'abc' ) {
    header( "Location: /" );
    die();
}

$msg = trim("
A new comment has been submitted from your blog.

Email address: $email
Share identity: $ident

$comment
");

$headers = "";

if( $email ) {
    $headers = "Reply-To: $email";
}

mail( $CONFIG['comment_email'], "Comment from $email", $msg, $headers );

put( "Your comment has been sent. Thanks!" );

put( "" );
put( "<< Index [<< Index](/)" );
