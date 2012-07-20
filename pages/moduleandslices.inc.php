<?php
/**
 * ModuleAndSlices Addon
 * @author sven[ät]koalashome[punkt]de Sven Eichler
 * @package redaxo3
 * @version $Id: moduleandslices.inc.php,v 1.1 2007/10/22 21:41:23 koala_s Exp $
 */

// Modulwechsel einleiten
if (isset ($_POST['modulwechsel']) and isset ($_POST['module_id']) and isset ($_POST['slice_id'])) {

  // ermittle Module-ID
  $qry = sprintf ('SELECT '.$REX['TABLE_PREFIX'].'module.id
          FROM '.$REX['TABLE_PREFIX'].'module
          WHERE '.$REX['TABLE_PREFIX'].'module.id = "%d"',
          $_POST['module_id']);
  $MODULE_NEW = new rex_sql();
  $MODULE_NEW->setQuery($qry);

  // ermittle Slice-ID
  $qry = sprintf ('SELECT '.$REX['TABLE_PREFIX'].'article_slice.id
          FROM '.$REX['TABLE_PREFIX'].'article_slice
          WHERE '.$REX['TABLE_PREFIX'].'article_slice.id = "%d"',
          $_POST['slice_id']);
  $SLICE_NEW = new rex_sql();
  $SLICE_NEW->setQuery($qry);

  // ersteinmal prüfen, ob die übergebene modul_id wirklich existiert
  if ($MODULE_NEW->getRows() == 1 and $SLICE_NEW->getRows() == 1) {
    // update der betreffenden Zeile
    $qry = sprintf ('UPDATE '.$REX['TABLE_PREFIX'].'article_slice
            SET modultyp_id = "%d"
            WHERE '.$REX['TABLE_PREFIX'].'article_slice.id = "%d"
            LIMIT 1',
            $_POST['module_id'],
            $_POST['slice_id']);

    $sql = new rex_sql();
    //$sql->debugsql = true;
    $sql->setQuery($qry);
    //DebugOut('TEST: '.$qry,'sql');

    $search_arr = array ('#SLICEID#', '#MODULIDNEU#', '#MODULIDOLD#');
    $replace_arr = array (sprintf ('%d',$_POST['slice_id']),
                          sprintf ('%d',$_POST['module_id']),
                          sprintf ('%d',$_POST['modul_id_old'])
                         );
    $nachricht = str_replace ($search_arr, $replace_arr, $I18N_MAS->msg("update_erfolgt"));
    echo rex_warning($nachricht);
  } else {
    echo rex_warning($I18N_MAS->msg("error_datenuebergabe"));
  } // if ($MODULE_NEW->getRows() == 1 and $SLICE_NEW->getRows() == 1)
} // if (isset ($_POST['modulwechsel']) and isset ($_POST['module_id']) and isset ($_POST['slice_id']))


// gib MySQL-Speicher frei, wenn Abfragen stattfanden
if (isset ($sql) and is_object ($sql))
{ 
  $sql->freeResult();
}
if (isset ($SLICE_NEW) and is_object ($SLICE_NEW))
{ 
  $SLICE_NEW->freeResult();
}
if (isset ($MODULE_NEW) and is_object ($MODULE_NEW))
{ 
  $MODULE_NEW->freeResult();
}








/* create Template instance called $t */
$t = new Template(MAS_TEMPLATEPATH, "remove");
//$t->debug = 7;
//$start_dir = $_ROOT['template'].'gb_frontend_output.html';
$start_dir = 'mas_backend_output.html';

/* lese Template-Datei */
$t->set_file(array("start" => $start_dir));



// nur wenn die Suche gestartet wurde, soll etwas ausgegeben werden
if (isset ($_POST['suche'])) {

  $where_arr = array();
  if (isset ($_POST['suche_articleid']) or isset ($_POST['suche_sliceid']) or isset ($_POST['suche_modulid'])) {
    if (isset ($_POST['suche_articleid']) and $_POST['suche_articleid'] != '' and is_numeric ($_POST['suche_articleid'])) {  $where_arr['article_id'] = sprintf ('%d', $_POST['suche_articleid']); }
    if (isset ($_POST['suche_sliceid']) and $_POST['suche_sliceid'] != '' and is_numeric ($_POST['suche_sliceid'])) {  $where_arr['id'] = sprintf ('%d', $_POST['suche_sliceid']); }
    if (isset ($_POST['suche_modulid']) and $_POST['suche_modulid'] != '' and is_numeric ($_POST['suche_modulid'])) {  $where_arr['modultyp_id'] = sprintf ('%d', $_POST['suche_modulid']); }
  }

  $i = 0;
  if (count ($where_arr) >= 1) {
    $where = "\n".'WHERE ';
  } else {
    $where = '';
  }
  foreach ($where_arr as $key => $value) {
    $where .= $REX['TABLE_PREFIX'].'article_slice.'.$key.' = "'.$value.'"';
    if ($i < count ($where_arr) - 1) {
      $where .= ' AND '."\n";
    }
    $i++;
  }
  $where .= "\n";


  // ermittle alle Slices- und Artikel-IDs
  $qry = 'SELECT '.$REX['TABLE_PREFIX'].'article_slice.id, '.$REX['TABLE_PREFIX'].'article_slice.article_id,
         '.$REX['TABLE_PREFIX'].'article_slice.modultyp_id, '.$REX['TABLE_PREFIX'].'article.name AS article_name
         , '.$REX['TABLE_PREFIX'].'article_slice.ctype
         FROM '.$REX['TABLE_PREFIX'].'article_slice
         JOIN '.$REX['TABLE_PREFIX'].'article ON ('.$REX['TABLE_PREFIX'].'article_slice.article_id = '.$REX['TABLE_PREFIX'].'article.id)
         '.$where.'
         ORDER BY article_id, '.$REX['TABLE_PREFIX'].'article_slice.id';
  $sql = new rex_sql();
  //    $sql->debugsql = true;
  $data = $sql->getArray($qry);
  //DebugOut('TEST: '.$qry,'sql');




  // ermittle alle Module-IDs
  $qry = 'SELECT '.$REX['TABLE_PREFIX'].'module.id, '.$REX['TABLE_PREFIX'].'module.name
         FROM '.$REX['TABLE_PREFIX'].'module
         ORDER BY '.$REX['TABLE_PREFIX'].'module.name';
  $MODULE = new rex_sql();
   //   $MODULE->debugsql = true;
  //$MODULE = $sql_module->get_array($qry);
  $MODULE->setQuery($qry);


  if (is_array($data)) {

    $t->set_block("start", "EintragsUebersicht", "EintragsUebersicht_s");

    foreach ($data as $row) {

      $SliceID = $row['id'];
      $ModulTypID = $row['modultyp_id'];

      $MODULHIDDEN = '';
      $MODUL = '<select name="module_id">'."\n";

      for ($i=0; $i < $MODULE->getRows(); $i++) {
        $ModulName = $MODULE->getValue("name");
        $ModulID = $MODULE->getValue("id");
        if ($ModulTypID == $ModulID) {
          $MODUL .= '<option value="'.$ModulID.'" selected="selected">['.$ModulID.'] - '.htmlentities ($ModulName,ENT_QUOTES).'</option>'."\n";
          $MODULHIDDEN = $ModulID;
        } else {
          $MODUL .= '<option value="'.$ModulID.'">['.$ModulID.'] - '.htmlentities ($ModulName,ENT_QUOTES).'</option>'."\n";
        }

        $MODULE->next();
      }
      $MODULE->reset();

      $MODUL .= '</select>'."\n";

      // Article-Name
      $ARTICLE_NAME = htmlentities ($row['article_name'],ENT_QUOTES);

      // Article-Name-Kurzform
      $ARTICLE_NAME_KURZ = mas_kurzanzeige_106($row['article_name'] , 20);
      $ARTICLE_NAME_KURZ = htmlentities ($ARTICLE_NAME_KURZ,ENT_QUOTES);

      // Artikel-ID mit einem Link versehen index.php?page=content&article_id=83
      $ARTICLEID = '<a href="index.php?page=content&amp;article_id='.$row['article_id'].'&amp;ctype='.$row['ctype'].'" title="'.$ARTICLE_NAME.' - CTYPE: '.$row['ctype'].'">['.$row['article_id'].'] - '.$ARTICLE_NAME_KURZ.'</a>';


      $t->set_var(array("SLICEID"   => $SliceID,
                        "ARTICLEID" => $ARTICLEID,
                        "MODULTYPID" => $ModulTypID,
                        "MODUL"      => $MODUL,
                        "SENDBUTTON" => $I18N_MAS->msg('form_button_send'),
                        "HIDDEN_SLICEID" => $SliceID,
                        "HIDDEN_MODULIDOLD" => $MODULHIDDEN
                        ));

      $t->parse("EintragsUebersicht_s", "EintragsUebersicht", true);



    } // foreach ($data as $row)

    // Module zur Ausgabe da, dann anzeigen
    $t->set_var(array("AUSGABEVORHANDEN_BEGINN" => '',
                      "AUSGABEVORHANDEN_ENDE"   => ''
                      ));

  } else {
    // Module zur Ausgabe nicht da, dann nichts anzeigen
    $t->set_var(array("AUSGABEVORHANDEN_BEGINN" => '{*',
                      "AUSGABEVORHANDEN_ENDE"   => '*}'
                      ));

  }// if (is_array($data))


} else { // if (isset ($_POST['suche']))

  // Module zur Ausgabe nicht da, dann nichts anzeigen
  $t->set_var(array("AUSGABEVORHANDEN_BEGINN" => '{*',
                    "AUSGABEVORHANDEN_ENDE"   => '*}'
                    ));




}
// komplette Seite ausgeben
$t->pparse("output", "start");


// gib MySQL-Speicher frei, wenn Abfragen stattfanden
if (isset ($sql) and is_object ($sql))
{ 
  $sql->freeResult();
}
if (isset ($MODULE) and is_object ($MODULE))
{ 
  $MODULE->freeResult();
}






?>