<?php
/**
 * ModuleAndSlices Addon
 * @author sven[�t]koalashome[punkt]de Sven Eichler
 * @package redaxo3
 * @version $Id: config.inc.php,v 1.1 2007/10/22 21:41:23 koala_s Exp $
 */

// addon identifier
$mypage = "ko_moduleandslices";


/**
 * Der Pfad zu den Templatedateien wird hier festegelegt.
 */
if (!defined('MAS_TEMPLATEPATH')) {
  // ein Ordner unterhalb des Guestbook-Addon
  define('MAS_TEMPLATEPATH', $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/templates/');
}

// CREATE LANG OBJ FOR THIS ADDON
if (!$REX['GG']) $I18N_MAS = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang');


$REX['ADDON']['rxid'][$mypage] = '106';
$REX['ADDON']['page'][$mypage] = $mypage;    
$REX['ADDON']['name'][$mypage] = 'MAS';
$REX['ADDON']['perm'][$mypage] = 'ko_moduleandslices[]';
$REX['ADDON']['version'][$mypage] = "0.0.7";
$REX['ADDON']['author'][$mypage] = "Sven (Koala) Eichler";
// $REX['ADDON']['supportpage'][$mypage] = "";

// add default perm for accessing the addon to user-administration
$REX['PERM'][] = 'ko_moduleandslices[]';

