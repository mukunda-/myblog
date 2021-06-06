<?php

require_once( 'config.php' );
//////////////////////////////////////////////////////////////////////////////////////////

date_default_timezone_set( $CONFIG['timezone'] );

$Output = [
   'content' => [],
   'title' => $CONFIG["page_title"]
];

$Command = '';
$PathArg = '';
$MediaPrefix = 'media';

//----------------------------------------------------------------------------------------
function make_html( $text ) {
   global $CONFIG, $MediaPrefix;

   $text = htmlspecialchars( $text );
   // Find media items.
   $text = preg_replace_callback( '/\[([^\s\]]+\.(png|jpg|mp4))\]/',
      function( array $matches ) {
         global $CONFIG, $MediaPrefix;
         
         $media_path = $MediaPrefix . '/' . $matches[1];
         if( file_exists($CONFIG['webroot'] . "/$media_path") ) {
            $ext = $matches[2];
            if( $ext == 'jpg' || $ext == 'png' ) {
               return "<a href=\"$media_path\"><img src=\"$media_path\"></a>";
            } else {
               // todo.
            }
         }
         return $matches[0];
      }, $text );
   
   // Find links.

   $links = [];
   
   $text = preg_replace_callback( '/\[([^\]]+)\]\(([^)]+)\)/',
      function( array $matches ) use (&$links) {
         $links[] = [
            'text' => $matches[1],
            'link' => $matches[2]
         ];
         return '';
      }, $text );

   // Clean up right edges.
   $text = preg_replace( '/ *\n/', "\n", $text );

   foreach( $links as $link ) {
      $pattern = $link['text'];
      $text = preg_replace_callback( "+$pattern+", function( $matches ) use ($link) {
         if( count($matches) == 3 ) {
            return "$matches[1]<a href=\"$link[link]\">$matches[2]</a>";
         } else if( count($matches) == 4 ) {
            return "$matches[1]<a href=\"$link[link]\">$matches[2]</a>$matches[3]";
         } else {
            return "<a href=\"$link[link]\">$matches[0]</a>";
         }
      }, $text );
      
   }

   return trim($text);
}

//----------------------------------------------------------------------------------------
function export_html() {
   global $Output;
   $content = implode("\n", $Output['content']);
   
   ?><!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title><?=$Output["title"]?></title>
      <link rel="stylesheet" href="/res/style.css">
   </head>
<body><main><?=$content?></main></body>
</html><?php
}

//----------------------------------------------------------------------------------------
function put( string $text ) {
   global $Output;
   $Output["content"][] = make_html( trim($text) );
}

//----------------------------------------------------------------------------------------
// Called to produce the page. $get_content should return the page content.
//
function start_proc( string $dir = "" ) {
   
   global $CONFIG, $Command, $PathArg;

   $args = "";

   foreach( $_GET as $a => $b ) {
      if( $a == "q" ) {
         
      } else {
         $args .= " --$a $b";
      }
   }

   $args = $PathArg . $args;

   put( trim("$CONFIG[user]@$CONFIG[host]:$CONFIG[dir]$dir# ./$Command $args") );
   put( "" );
   //$cont = $get_content();
   //$cont["title"] = $cont["title"] ?? $BLOG_TITLE;
}

//----------------------------------------------------------------------------------------
function end_proc() {
   export_html();
}

//----------------------------------------------------------------------------------------
function get_file_meta( string $path ) {
   $meta = [
      'file' => preg_replace( '/^content\//', "", $path ),
      'preview' => ''
   ];
   $f = fopen( $path, "r" );
   while( !feof($f) ) {
      $line = trim( fgets($f) );
      
      if( $line == "" ) break;

      if( preg_match( '/^([^:]+):(.*)$/', $line, $matches ) ) {
         $last_meta = strtolower($matches[1]);
         $meta[$last_meta] = trim($matches[2]);
      } else {
         $meta[$last_meta] .= "\n$line";
      }
   }

   $preview_lines = 6;
   while( !feof($f) ) {
      $line = trim( fgets($f) );
      $meta['preview'] .= "$line\n";
      $preview_lines--;
      if( $preview_lines == 0 && !feof($f) ) {
         // ideally only add ... if it's incomplete.
         $meta['preview'] .= "...\n";
         break;
      }
   }
   $meta['preview'] = trim($meta['preview']);
   fclose( $f );

   if( isset($meta['date']) ) {
      $meta['cdate'] = strtotime( $meta['date']  );
   } else {
      $meta['cdate'] = 0;
   }

   return $meta;
}

//----------------------------------------------------------------------------------------
function get_years() {
   $years = [];
   foreach( glob("content/*") as $file ) {
      if( preg_match('/content\/(\d\d\d\d)/', $file, $matches) ) {
         $years[] = $matches[1];
      }
   }
   rsort( $years );
   return $years;
}

//----------------------------------------------------------------------------------------
function get_recent_blogs( $count = 10 ) {
   $Output = [];
   $now = time();

   $current_year = date("Y");
   for( $year = $current_year; $year >= $current_year - 10; $year-- ) {
      $files = glob( "content/$year/*.txt" );
      // Sort files by date.
      $meta = [];
      foreach( $files as $file ) {
         $m = get_file_meta( $file );

         // Skip files with invalid dates.
         if( $m['cdate'] == 0 || $m['cdate'] > $now ) continue;
         $meta[] = $m;
      }

      usort( $meta, function( $a, $b ) {
         if( $a['cdate'] == $b['cdate'] ) return 0;
         return ( $a['cdate'] > $b['cdate'] ) ? -1 : 1;
      });

      foreach( $meta as $m ) {
         $Output[] = $m;
         if( count($Output) > $count ) return $Output;
      }
   }
   
   return $Output;
}

//----------------------------------------------------------------------------------------
function strip_path( $path ) {
   return str_replace( "..", "", $path );
}

//----------------------------------------------------------------------------------------
function start_route() {
   global $Command, $PathArg;

   # Do routing.
   $q = $_GET['q'];
   $cmd = $q;
   $patharg = "";
   if( preg_match('/(.+?)\/(.*)/', $q, $matches) ) {
      $a = $matches[0];
      $cmd = $matches[1];
      $patharg = $matches[2];
   }

   if( $cmd == "" || $cmd == "index.php" ) $cmd = "index";
   $Command = $cmd;

   // Sanitize.
   $PathArg = strip_path($patharg);
   start_proc();


   // This is probably frowned upon. :)
   switch( $cmd ) {
      case "index":
         include( "../php/cmd/index.php" );
         break;
      case "cat":
         include( "../php/cmd/cat.php" );
         break;
      case "list":
         include( "../php/cmd/list.php" );
         break;
      default:
         include( "../php/404.php" );
   }

   end_proc();
}

start_route();