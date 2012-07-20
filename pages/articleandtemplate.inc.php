<?php
/**
 * ModuleAndSlices Addon
 * @author sven[ät]koalashome[punkt]de Sven Eichler
 * @package redaxo3
 * @version $Id: articleandtemplate.inc.php,v 1.1 2007/10/22 21:41:23 koala_s Exp $
 */


// Templatewechsel einleiten
if (isset ($_POST['templatewechsel']) and isset ($_POST['template_id']) and isset ($_POST['article_id'])) {

  // ermittle Template-ID
  $qry = sprintf ('SELECT '.$REX['TABLE_PREFIX'].'template.id
          FROM '.$REX['TABLE_PREFIX'].'template
          WHERE '.$REX['TABLE_PREFIX'].'template.id = "%d"' ,
          $_POST['template_id']);
  $TEMPLATE_NEW = new rex_sql();
  $TEMPLATE_NEW->setQuery($qry);
  
  // ermittle Article-ID
  $qry = sprintf ('SELECT '.$REX['TABLE_PREFIX'].'article.id
          FROM '.$REX['TABLE_PREFIX'].'article
          WHERE '.$REX['TABLE_PREFIX'].'article.id = "%d"' ,
          $_POST['article_id']);
  $ARTICLE_NEW = new rex_sql();
  $ARTICLE_NEW->setQuery($qry);
  
  // ersteinmal prüfen, ob die übergebene template_id wirklich existiert
  if ($TEMPLATE_NEW->getRows() == 1 and $ARTICLE_NEW->getRows() == 1) {
    // update der betreffenden Zeile
    $qry = sprintf ('UPDATE '.$REX['TABLE_PREFIX'].'article
            SET template_id = "%d"
            WHERE '.$REX['TABLE_PREFIX'].'article.id = "%d"
            LIMIT 1' ,
            $_POST['template_id'],
            $_POST['article_id']);

    $sql = new rex_sql();
    //$sql->debugsql = true;
    $sql->setQuery($qry);
    //DebugOut('TEST: '.$qry,'sql');
    
    $search_arr = array ('#ARTICLEID#', '#TEMPLATEIDNEU#', '#TEMPLATEIDOLD#');
    $replace_arr = array (sprintf ('%d',$_POST['article_id']),
                          sprintf ('%d',$_POST['template_id']),
                          sprintf ('%d',$_POST['template_id_old'])
                         );
    $nachricht = str_replace ($search_arr, $replace_arr, $I18N_MAS->msg("art_and_temp_update_erfolgt"));
    echo rex_warning($nachricht);

  } else {

    echo rex_warning($I18N_MAS->msg("error_datenuebergabe"));

  } // if ($TEMPLATE_NEW->getRows() == 1 and $ARTICLE_NEW->getRows() == 1)
} // if (isset ($_POST['modulwechsel']) and isset ($_POST['template_id']) and isset ($_POST['article_id']))


// gib MySQL-Speicher frei, wenn Abfragen stattfanden
if (isset ($sql) and is_object ($sql))
{ 
  $sql->freeResult();
}
if (isset ($TEMPLATE_NEW) and is_object ($TEMPLATE_NEW))
{ 
  $TEMPLATE_NEW->freeResult();
}
if (isset ($ARTICLE_NEW) and is_object ($ARTICLE_NEW))
{ 
  $ARTICLE_NEW->freeResult();
}







/* create Template instance called $t */
$t = new Template(MAS_TEMPLATEPATH, "remove");
//$t->debug = 7;
//$start_dir = $_ROOT['template'].'gb_frontend_output.html';
$start_dir = 'art_and_temp_backend_output.html';

/* lese Template-Datei */
$t->set_file(array("start" => $start_dir));



// nur wenn die Suche gestartet wurde, soll etwas ausgegeben werden
if (isset ($_POST['suche'])) {

  $where_arr = array();
  if (isset ($_POST['suche_articleid']) or isset ($_POST['suche_templateid'])) {
    if (isset ($_POST['suche_articleid']) and $_POST['suche_articleid'] != '' and is_numeric ($_POST['suche_articleid'])) {  $where_arr['id'] = sprintf ('%d', $_POST['suche_articleid']);  }
    if (isset ($_POST['suche_templateid']) and $_POST['suche_templateid'] != '' and is_numeric ($_POST['suche_templateid'])) {  $where_arr['template_id'] = sprintf ('%d', $_POST['suche_templateid']);  }
  }

  $i = 0;
  if (count ($where_arr) >= 1) {
    $where = "\n".'WHERE ';
  } else {
    $where = '';
  }
  foreach ($where_arr as $key => $value) {
    $where .= $REX['TABLE_PREFIX'].'article.'.$key.' = "'.$value.'"';
    if ($i < count ($where_arr) - 1) {
      $where .= ' AND '."\n";
    }
    $i++;
  }
  $where .= "\n";


  // ermittle alle Template- und Artikel-IDs
  $qry = 'SELECT '.$REX['TABLE_PREFIX'].'article.id,
         '.$REX['TABLE_PREFIX'].'article.template_id, '.$REX['TABLE_PREFIX'].'article.name AS article_name
         FROM '.$REX['TABLE_PREFIX'].'article
         '.$where.'
         ORDER BY '.$REX['TABLE_PREFIX'].'article.id';
  $sql = new rex_sql();
  //    $sql->debugsql = true;
  $data = $sql->getArray($qry);
  //DebugOut($qry,'sql');




  // ermittle alle Template-IDs
  $qry = 'SELECT '.$REX['TABLE_PREFIX'].'template.id, '.$REX['TABLE_PREFIX'].'template.name
         FROM '.$REX['TABLE_PREFIX'].'template
         ORDER BY '.$REX['TABLE_PREFIX'].'template.name';
  $TEMPLATES = new rex_sql();
  //    $sql->debugsql = true;
  //$TEMPLATES = $sql_module->get_array($qry);
  $TEMPLATES->setQuery($qry);


  if (is_array($data)) {

    $t->set_block("start", "EintragsUebersicht", "EintragsUebersicht_s");

    foreach ($data as $row) {

      $ArticleID = $row['id'];
      $TemplateTypID = $row['template_id'];

      $TEMPLATEHIDDEN = '';
      $TEMPLATE = '<select name="template_id">'."\n";

      for ($i=0; $i < $TEMPLATES->getRows(); $i++) {
        $TemplateName = $TEMPLATES->getValue("name");
        $TemplateID = $TEMPLATES->getValue("id");
        if ($TemplateTypID == $TemplateID) {
          $TEMPLATE .= '<option value="'.$TemplateID.'" selected="selected">['.$TemplateID.'] - '.htmlentities ($TemplateName,ENT_QUOTES).'</option>'."\n";
          $TEMPLATEHIDDEN = $TemplateID;
        } else {
          $TEMPLATE .= '<option value="'.$TemplateID.'">['.$TemplateID.'] - '.htmlentities ($TemplateName,ENT_QUOTES).'</option>'."\n";
        }

        $TEMPLATES->next();
      }
      $TEMPLATES->reset();

      $TEMPLATE .= '</select>'."\n";

      // Article-Name
      $ARTICLE_NAME = htmlentities ($row['article_name'],ENT_QUOTES);

      // Article-Name-Kurzform
      $ARTICLE_NAME_KURZ = mas_kurzanzeige_106($row['article_name'] , 20);
      $ARTICLE_NAME_KURZ = htmlentities ($ARTICLE_NAME_KURZ,ENT_QUOTES);

      // Artikel-ID mit einem Link versehen index.php?page=content&article_id=83
      $ARTICLEID = '<a href="index.php?page=content&amp;article_id='.$row['id'].'" title="'.$ARTICLE_NAME.'">['.$row['id'].'] - '.$ARTICLE_NAME_KURZ.'</a>';

      $t->set_var(array("ARTICLEID" => $ARTICLEID,
                        "TEMPLATETYPID" => $TemplateTypID,
                        "TEMPLATE"      => $TEMPLATE,
                        "SENDBUTTON" => $I18N_MAS->msg('art_and_temp_form_button_send'),
                        "HIDDEN_ARTICLEID" => $ArticleID,
                        "HIDDEN_TEMPLATEIDOLD" => $TEMPLATEHIDDEN
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
if (isset ($TEMPLATES) and is_object ($TEMPLATES))
{ 
  $TEMPLATES->freeResult();
}




?>