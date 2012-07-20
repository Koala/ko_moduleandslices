<?php
/**
 * ModuleAndSlices Addon
 * @author sven[ät]koalashome[punkt]de Sven Eichler
 * @package redaxo3
 * @version $Id: statistik_module.inc.php,v 1.1 2007/10/22 21:41:23 koala_s Exp $
 */


/* create Template instance called $t */
$t = new Template(MAS_TEMPLATEPATH, "remove");
//$t->debug = 7;
//$start_dir = $_ROOT['template'].'gb_frontend_output.html';
$start_dir = 'statistik.html';

/* lese Template-Datei */
$t->set_file(array("start" => $start_dir));


switch ($MAS_STATISTIK_SUCHE) {
  case 'templates':
    // ermittle
    $qry = 'SELECT COUNT('.$REX['TABLE_PREFIX'].'article.template_id) AS anzahl, '.$REX['TABLE_PREFIX'].'article.template_id,
           '.$REX['TABLE_PREFIX'].'template.name
           FROM '.$REX['TABLE_PREFIX'].'template
           LEFT JOIN '.$REX['TABLE_PREFIX'].'article ON ('.$REX['TABLE_PREFIX'].'template.id = '.$REX['TABLE_PREFIX'].'article.template_id)
           GROUP BY '.$REX['TABLE_PREFIX'].'template.id
           ORDER BY anzahl DESC, '.$REX['TABLE_PREFIX'].'template.name';
    $sql = new rex_sql();
    //    $sql->debugsql = true;
    $data = $sql->getArray($qry);
    //DebugOut($qry,'sql');
    break;
  case 'actions':
    // ermittle
    $qry = 'SELECT COUNT('.$REX['TABLE_PREFIX'].'module_action.action_id) AS anzahl, '.$REX['TABLE_PREFIX'].'module_action.action_id,
           '.$REX['TABLE_PREFIX'].'action.name
           FROM '.$REX['TABLE_PREFIX'].'action
           LEFT JOIN '.$REX['TABLE_PREFIX'].'module_action ON ('.$REX['TABLE_PREFIX'].'action.id = '.$REX['TABLE_PREFIX'].'module_action.action_id)
           GROUP BY '.$REX['TABLE_PREFIX'].'action.id
           ORDER BY anzahl DESC, '.$REX['TABLE_PREFIX'].'action.name';
    $sql = new rex_sql();
    //    $sql->debugsql = true;
    $data = $sql->getArray($qry);
    //DebugOut($qry,'sql');
    break;

  case 'module':
  default:
    // ermittle
    $qry = 'SELECT COUNT('.$REX['TABLE_PREFIX'].'article_slice.modultyp_id) AS anzahl, '.$REX['TABLE_PREFIX'].'article_slice.modultyp_id,
           '.$REX['TABLE_PREFIX'].'module.name
           FROM '.$REX['TABLE_PREFIX'].'module
           LEFT JOIN '.$REX['TABLE_PREFIX'].'article_slice ON ('.$REX['TABLE_PREFIX'].'module.id = '.$REX['TABLE_PREFIX'].'article_slice.modultyp_id)
           GROUP BY '.$REX['TABLE_PREFIX'].'module.id
           ORDER BY anzahl DESC, '.$REX['TABLE_PREFIX'].'module.name';
    $sql = new rex_sql();
    //    $sql->debugsql = true;
    $data = $sql->getArray($qry);
    //DebugOut($qry,'sql');
    break;
}




if (is_array($data)) {

  $t->set_block("start", "EintragsUebersicht", "EintragsUebersicht_s");


  foreach ($data as $row) {

    if ($row['anzahl'] == 0) {
      $ANZAHL = '<span style="color:red;">'.$row['anzahl'].'</span>';
    } else {
      $ANZAHL = $row['anzahl'];
    }

    $t->set_var(array("NAME"   => $row['name'],
                      "ANZAHL" => $ANZAHL
                      ));

    $t->parse("EintragsUebersicht_s", "EintragsUebersicht", true);



  } // foreach ($data as $row)
} else {
  // Module zur Ausgabe nicht da, dann nichts anzeigen
  $t->set_var(array("AUSGABEVORHANDEN_BEGINN" => '{*',
                    "AUSGABEVORHANDEN_ENDE"   => '*}'
                    ));

}// if (is_array($data))



// komplette Seite ausgeben
$t->pparse("output", "start");



// gib MySQL-Speicher frei, wenn Abfragen stattfanden
if (isset ($sql) and is_object ($sql))
{ 
  $sql->freeResult();
}
 





?>