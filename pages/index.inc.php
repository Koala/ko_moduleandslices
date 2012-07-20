<?php
/**
 * ModuleAndSlices Addon
 * @author sven[�t]koalashome[punkt]de Sven Eichler
 * @package redaxo3
 * @version $Id: index.inc.php,v 1.1 2007/10/22 21:41:23 koala_s Exp $
 */


// Parameter
$Basedir = dirname(__FILE__);




// wenn Template-Klasse noch nicht eingebunden, dann hole sie jetzt rein
if (!class_exists ('Template')) {
  include_once ($REX['INCLUDE_PATH'].'/addons/ko_moduleandslices/classes/template.inc.php');
}


if (!isset ($_GET['subpage'])) { $_GET['subpage'] = '';  }

// Default Seite
$include_site = 'moduleandslices.inc.php';

// Default Addonmenutitel
$addontitelausgabe = 'menu_title_mod_and_sli';

switch ($_GET['subpage']) {
  case 'templates': $include_site = 'articleandtemplate.inc.php';
                    $addontitelausgabe = 'menu_title_art_and_temp';
    break;
  case 'statistik_module':
                    $MAS_STATISTIK_SUCHE = 'module';
                    $include_site = 'statistik_module.inc.php';
                    $addontitelausgabe = 'menu_title_statistik_module';
    break;
  case 'statistik_templates':
                    $MAS_STATISTIK_SUCHE = 'templates';
                    $include_site = 'statistik_module.inc.php';
                    $addontitelausgabe = 'menu_title_statistik_templates';
    break;
  case 'statistik_actions':
                    $MAS_STATISTIK_SUCHE = 'actions';
                    $include_site = 'statistik_module.inc.php';
                    $addontitelausgabe = 'menu_title_statistik_actions';
    break;

  default: $include_site = 'moduleandslices.inc.php';
           $addontitelausgabe = 'menu_title_mod_and_sli';
    break;
}




// Include Header and Navigation
include $REX['INCLUDE_PATH'].'/layout/top.php';


$AuswahlLinks = '<a href="index.php?page=ko_moduleandslices&amp;subpage=leer">[ModuleAndSlices]</a>';

$AuswahlLinks .= '&#160;<a href="index.php?page=ko_moduleandslices&amp;subpage=templates">[ArticleAndTemplates]</a>';
$AuswahlLinks .= '<br />Statistik::';
$AuswahlLinks .= '<a href="index.php?page=ko_moduleandslices&amp;subpage=statistik_module">[Module]</a>';
$AuswahlLinks .= '&#160;<a href="index.php?page=ko_moduleandslices&amp;subpage=statistik_templates">[Templates]</a>';
$AuswahlLinks .= '&#160;<a href="index.php?page=ko_moduleandslices&amp;subpage=statistik_actions">[Actions]</a>';

rex_title($I18N_MAS->msg($addontitelausgabe), $AuswahlLinks);





include ($Basedir . '/' . $include_site);


// Include Footer
include $REX['INCLUDE_PATH'].'/layout/bottom.php';






/**
 * Kuerzt einen String auf eine vorgegebene Zeichenlaenge
 *
 * Wird keine Zeichenlaenge vorgegeben, wird der String auf 30 Zeichen gekuerzt
 * Aufruf: mas_kurzanzeige($string,30);
 *
 * @param    string    der zu kuerzende String
 * @param    int       die Laenge auf die der String gekuerzt werden soll
 * @return   string    der gekuerzte String
 */
function mas_kurzanzeige_106($string, $laenge = 30) {
  $len_anzeige = strlen($string);
  if ($len_anzeige > $laenge) {
    $string = substr($string, 0, $laenge);
    $string .= '...';
    return $string;
  } else {
    return $string;
  }
}











