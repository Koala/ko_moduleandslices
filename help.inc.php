<?php
/**
 * ModuleAndSlices Addon
 * @author sven[ät]koalashome[punkt]de Sven Eichler
 * @package redaxo3
 * @version $Id: help.inc.php,v 1.1 2007/10/22 21:41:23 koala_s Exp $
 */


if ( !isset( $mode)) $mode = '';
switch ( $mode) {
   case 'changelog': $file = '_changelog.txt'; break;
   default: $file = '_readme.txt';
}

echo str_replace( '+', '&nbsp;&nbsp;+', nl2br( file_get_contents( dirname( __FILE__) .'/'. $file)));








?>