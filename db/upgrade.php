<?php

/**
 * This file keeps track of upgrades to the lams module.
 * 
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installtion to the current version.
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 * The commands in here will all be database-neutral,
 * using the functions defined in lib/ddllib.php
 * 
 * - v 1.5.2 2020/08/03
 *     + Fixed 'ALTER TABLE' of 'iassign_submission.grade' from BIGINT(11) to REAL 
 *     + New version of iHanoi 1.0.20200803
 * - v 1.5.1 2020/05/28-30
 *     + Avoid to update one iLM causing colision with other instance of the same iLM
 * - v 1.4 2013/09/19
 *     + Insert general fields for iassign statement (grade, timeavaliable, timedue, preventlate, test, max_experiment).
 *     + Change index field 'name' in 'iassign_ilm' table to index field 'name,version'.
 * - v 1.2 2013/08/30
 * + Change 'filearea' for new concept for files.
 * + Change path file for ilm, consider version in pathname.
 * 
 * @author Leônidas O. Brandão
 * @author Patricia Alves Rodrigues
 * @author Igor Moreira Félix
 * @version v 1.5.1 2020/05/28-30
 * @version v 1.5 2019/03/13
 * @version v 1.4 2013/09/19
 * @package mod_iassign_db
 * @since 2010/12/21
 * @copyright iMath (http://www.matematica.br) and LInE (http://line.ime.usp.br) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  
 * @param $oldversion Number of the old version. 
 */

require_once ($CFG->dirroot . '/mod/iassign/locallib.php');

function xmldb_iassign_upgrade ($oldversion) {

  global $CFG, $DB, $USER;

  $dbman = $DB->get_manager();

  //L if ($oldversion < 2019112219) {
  if ($oldversion < 2020070613) {
    $table = new xmldb_table('iassign_submission');
    $field = new xmldb_field('previous_grade', XMLDB_TYPE_FLOAT, null, null, null, null, null);
    if (!$dbman->field_exists($table, $field)) {
      $dbman->add_field($table, $field);
      }

    $records = array( // iLM adjusted to iAssign 2020/08/03
      array_combine( // iGeom 5.9.22
        array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate', 'reevaluate'),
        array('iGeom', 'http://www.matematica.br/igeom', '5.9.22', 'Java', '{"en":"Interactive Geometry on the Internet","pt_br":"Geometria Interativa na Internet"}', 'geo', 'ilm/iGeom/5.9.22/iGeom.jar', 'IGeomApplet.class', 800, 600, 1, time(), $USER->id, time(), 1, 0)),
      array_combine( // iGraf 4.4.0.10
        array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate', 'reevaluate'),
        array('iGraf', 'http://www.matematica.br/igraf', '4.4.0.10', 'Java', '{"en":"Interactive Graphic on the Internet","pt_br":"Gráficos Interativos na Internet"}', 'grf', 'ilm/iGraf/4.4.0.10/iGraf.jar', 'igraf.IGraf.class', 840, 600, 1, time(), $USER->id, time(), 1, 0)),
      array_combine( // iHanoi 1.0.20200803
        array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate', 'reevaluate'),
        array('iHanoi', 'http://www.matematica.br/ihanoi', '1.0.20200803', 'HTML5', '{"en":"interactive Tower os Hanoi (by LInE)", "pt_br":"Torres de Hanói (do LInE)"}', 'ihn', 'ilm/iHanoi/1.0.20200803/ihanoi/', 'index.html', 1100, 500, 1, time(), $USER->id, time(), 1, 0)),
      array_combine( // iVProg 1.0.20200221/ - HTML5 - 2020
        array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate', 'reevaluate'),
        array('iVProg', 'http://www.usp.br/line/ivprog/', '1.0.20200221', 'HTML5', '{"en":"Visual Interactive Programming on the Internet (HTML)","pt_br":"Programação visual interativa na Internet"}', 'ivph', 'ilm/iVProg/1.0.20200221/ivprog/', 'index.html', 800, 600, 1, time(), $USER->id, time(), 1, 1)),
      array_combine( // iFractions 0.1.20200221 - HTML5
        array('name', 'url', 'version', 'type', 'description', 'extension', 'file_jar', 'file_class', 'width', 'height', 'enable', 'timemodified', 'author', 'timecreated', 'evaluate', 'reevaluate'), 
        array('iFractions', 'http://www.matematica.br/ifractions', '0.1.20200221', 'HTML5', '{"en":"Interactive Fractions game","pt_br":"Jogo interativa de frações"}', 'frc', 'ilm/iFractions/0.1.20200221/ifractions/', 'index.html', 1000, 600, 1, time(), $USER->id, time(), 1, 0))
        );

    $iassign_ilm = $DB->get_records('iassign_ilm');

    $strNot_installed = '';
    foreach ($records as $record) { // this version 'iassign_ilm' does not has field 'reevaluate'
      $newentry = new stdClass();
      $newentry->name = $record['name'];
      $newentry->version = $record['version'];
      $newentry->type = $record['type'];
      $newentry->url = $record['url'];
      $newentry->description = $record['description'];
      $newentry->extension = $record['extension'];
      $newentry->file_jar = $record['file_jar'];
      $newentry->file_class = $record['file_class'];
      $newentry->width = $record['width'];
      $newentry->height = $record['height'];
      $newentry->enable = $record['enable'];
      $newentry->timemodified = time();
      $newentry->author = $USER->id;
      $newentry->timecreated = time();
      $newentry->evaluate = $record['evaluate'];

      $exists = 0;
      if ($iassign_ilm) { // Search if there is any iLM of this with this version
        $record_type = strtolower($record['type']);
        foreach ($iassign_ilm as $iassign) {
          if ($iassign->name == $record['name'] && strtolower($iassign->type) == $record_type) {
            if ($iassign->version == $record['version']) { // or with the one with the same version
              $exists = 1; // iLM found - exit with the last one, same version
              $strNot_installed += "\n" + '  <li>' + $record['name'] + ';' + $record['type'] + ';' + $record['version'] + ' </li>' + "\n";
              break;
              }
            }
          }
        }
      if ($exists == 0) try { // iLM does not exists or it has old version
        $DB->insert_record("iassign_ilm", $newentry, false);
      } catch (Exception $e) {
        print 'Caught exception: ' . $e->getMessage() . "<br/>\n";
        }
      } // foreach ($records as $record)
    if ($strNot_installed != '') { // ATTENTION: this implies that Moodle administrator updated this iLM, please verify if it is really the current one.
      print '<div class="alert alert-warning alert-block fade in " role="alert" data-aria-autofocus="true" tabindex="0" >' + "\n";
      print get_string('upgrade_alert_exists', 'iassign'); // iLM previouly existent
      print ' <ul style="margin-top: 1rem;">' + "\n";
      print $strNot_installed;
      print ' </ul>' + "\n";
      print '</div>' + "\n";
      }

    // Verify if each iLM previously installed is present in fresh new iAssign. This does not means problem, perhaps the admin installed a particular iLM.
    $strNot_found = '';
    if ($iassign_ilm) { // exists iLM ($DB->get_records('iassign_ilm'))
      foreach ($iassign_ilm as $iassign) {
        $found = false;
        foreach ($records as $record) {
          if ($iassign->name == $record['name']) {
            $found = true;
            break;
            }
          }
        if (!$found) {
          $strNot_found .= '<li>' . $iassign->name . ' - <a href="' . $iassign->url . '" target="_blank">' . $iassign->url . '</a></li>' . "\n";
          $updateentry = new stdClass();
          $updateentry->id = $iassign->id;
          $updateentry->enable = 0;
          $updateentry->timemodified = time();
          $DB->update_record("iassign_ilm", $updateentry); // insert new iLM
          }
        } // foreach ($iassign_ilm as $iassign)
      } // if ($iassign_ilm)

    if ($strNot_found != '') {
      print '<div class="alert alert-warning alert-block fade in " role="alert" data-aria-autofocus="true" tabindex="0" >' + "\n";
      print get_string('upgrade_alert_iMA_msg', 'iassign'); // Updated but some previous iLM installed are not available in fresh iAssign
      print '<ul style="margin-top: 1rem;">' + "\n";
      print $strNot_found;
      print '</ul>' + "\n";
      print get_string('upgrade_alert_iMA_solution_pt1', 'iassign');
      print '<a href="'.new moodle_url('/admin/settings.php?section=modsettingiassign').'">' . get_string('upgrade_alert_iMA_solution_pt2', 'iassign') . '</a>.' . "\n";
      print '</div>' + "\n";
      }

    } // if ($oldversion < 2020070613)

  if ($oldversion < 2020070613) {
    $table = new xmldb_table('iassign_ilm');
    $field = new xmldb_field('reevaluate');
    $field->set_attributes(XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', null, null, null);
    if (!$dbman->field_exists($table, $field)) { // if 'iassign_ilm.reevaluate' does not exist, then create this field
      $dbman->add_field($table, $field);
      }

    $iassign_ilm = $DB->get_records('iassign_ilm');
    foreach ($iassign_ilm as $iassign) { // If already installed iVProg/JS or iHanoi/JS set it with re-evaluate feature
      if ($iassign->type == 'HTML5' && ($iassign->name == 'iVProg' || $iassign->name == 'iHanoi')) {
        $updateentry = new stdClass();
        $updateentry->id = $iassign->id;
        $updateentry->reevaluate = 1;
        $updateentry->timemodified = time();

        $DB->update_record("iassign_ilm", $updateentry);
        break;
        }
      }
    } // if ($oldversion < 2020070612)

  if ($oldversion < 2020080300) { // new iHanoi
    // iassign_submission . grade : from 'BIGINT(11)' to 'real'
    if ($dbman->field_exists('iassign', 'grade')) {
      $sql = 'ALTER TABLE {iassign} CHANGE grade grade FLOAT NOT NULL DEFAULT 0.0';
      $DB->execute($sql);
      }
    if ($dbman->field_exists('iassign_statement', 'grade')) {
      $sql = 'ALTER TABLE {iassign_statement} CHANGE grade grade FLOAT NOT NULL DEFAULT 0.0';
      $DB->execute($sql);
      }
    if ($dbman->field_exists('iassign_submission', 'grade')) {
      $sql = 'ALTER TABLE {iassign_submission} CHANGE grade grade FLOAT NOT NULL DEFAULT 0.0';
      $DB->execute($sql);
      }
    if ($dbman->field_exists('iassign_submission', 'previous_grade')) {
      // $sql = 'ALTER TABLE {iassign_submission} CHANGE previous_grade previous_grade REAL NOT NULL DEFAULT 0.0'; // Invalid use of NULL value
      $sql = 'ALTER TABLE {iassign_submission} CHANGE previous_grade previous_grade FLOAT DEFAULT 0.0';
      $DB->execute($sql);
      }

    // Update iHanoi, iVProg, iFractions and iGeom
    $records = array(
      array_combine( // iHanoi 1.0.20200803
        array('name', 'url', 'version', 'type', 'description',
              'extension', 'file_jar', 'file_class', 'width', 'height',
              'enable', 'timemodified', 'evaluate', 'reevaluate', 'author', 'timecreated'),
        array('iHanoi', 'http://www.matematica.br/ihanoi', '1.0.20200803', 'HTML5', '{"en":"interactive Tower os Hanoi (by LInE)", "pt_br":"Torres de Hanói (do LInE)"}',
              'ihn', 'ilm/iHanoi/1.0.20200803/ihanoi/', 'index.html', 1100, 500,
              1, time(), 1, 1, $USER->id, time()))
        );
    $iassign_ilm = $DB->get_records('iassign_ilm');
    foreach ($records as $record) { // For each iLM in the current version of iAssign
      $newentry = new stdClass();
      $newentry->name = $record['name'];
      $newentry->version = $record['version'];
      $newentry->type = $record['type'];
      $newentry->url = $record['url'];
      $newentry->description = $record['description'];
      $newentry->extension = $record['extension'];
      $newentry->file_jar = $record['file_jar'];
      $newentry->file_class = $record['file_class'];
      $newentry->width = $record['width'];
      $newentry->height = $record['height'];
      $newentry->enable = $record['enable'];
      $newentry->timemodified = time();
      $newentry->evaluate = $record['evaluate'];
      $newentry->reevaluate = $record['reevaluate'];
      $newentry->author = $USER->id;
      $newentry->timecreated = time();
      $newentry->evaluate = $record['evaluate'];

      $exists = 0;
      $last_id = -1;
      if ($iassign_ilm) { // Search if there is any previous installed iLM that is also in the current version of iAssign
        $record_type = strtolower($record['type']);
        foreach ($iassign_ilm as $iassign) {
          if ($iassign->name == $record['name'] && strtolower($iassign->type) == $record_type) {
            if ($iassign->id > $last_id)
              $last_id = $iassign->id; // last ID => last version (hopefully)
            if ($iassign->version == $record['version']) { // or with the one with the same version
              $exists = 1; // iLM found - exit with the last one, same version
              $strNot_installed += "\n" + '  <li>' + $record['name'] + ';' + $record['type'] + ';' + $record['version'] + ' </li>' + "\n";
              break;
              }
            }
          }
        }
      if ($exists == 0) { // iLM does not exists or it has old version
	$newentry->parent = $record['parent'];
        $DB->insert_record("iassign_ilm", $newentry, false);
        }
      } // foreach ($records as $record)

    if ($strNot_installed != '') {
      print '<div class="alert alert-warning alert-block fade in " role="alert" data-aria-autofocus="true" tabindex="0" >' + "\n";
      print get_string('upgrade_alert_exists', 'iassign'); // iLM previouly existent
      print ' <ul style="margin-top: 1rem;">' + "\n";
      print $strNot_installed;
      print ' </ul>' + "\n";
      print '</div>' + "\n";
      }

    // iassign_ilm: atualizou iHanoi existente, mas nao era isso! Deveria ter inserido novo!
    //   id name   version type  ...  parent file_jar              file_class  width height  ...  evaluate reevaluate
    //   53 iHanoi 2       HTML5 ...  0      ilm/iHanoi/2/ihanoi/  index.html  1100  700     ...  1        0
    $iassign_ilm = $DB->get_records('iassign_ilm');
    foreach ($iassign_ilm as $iassign) { // for iLM iHanoi update the new field 'reevaluate' as 1
      if ($iassign->name == 'iHanoi' && $iassign->type == 'HTML5' && $iassign->reevaluate!=1) {
        $updateentry = new stdClass();
        $updateentry->id = $iassign->id;
        $updateentry->reevaluate = 1;
        $updateentry->timemodified = time();
        $DB->update_record("iassign_ilm", $updateentry);
        break;
        }
      }

    // iAssign savepoint reached.
    upgrade_mod_savepoint(true, 2020080300, 'iassign');

    } // if ($oldversion < 2020080300)

  //TODO Codigo do Igor para atualizar 'files.itemid' e 'iassign_statement.filesid':
  if ($oldversion < 2020120500) {

    // Adding field iassing_statement.filesid
    $table = new xmldb_table('iassign_statement');

    $field_filesid = new xmldb_field('filesid', XMLDB_TYPE_CHAR, '255', null, null, null, null, null);

    if (!$dbman->field_exists($table, $field_filesid))
      $dbman->add_field($table, $field_filesid);
    
    // Updating all registers from iassing_statement.filesid
    $DB->execute("UPDATE {iassign_statement} SET filesid = file");


    // 1. encontrar os contextos dos arquivos do itarefa:
    $iassign_contexts_list = $DB->get_records_sql("SELECT DISTINCT contextid FROM {files} f WHERE component='mod_iassign'");
    // 2. compor um array com todos os contextos encontrados: 
    $contexts = array();
    foreach ($iassign_contexts_list as $iassign_context_item) {
      array_push($contexts, $iassign_context_item->contextid);
      }
    // 3. encontrar todas as atividades do itarefa, em que o arquivo não tenha o mesmo id do statement:
    $iassign_statement_list = $DB->get_records_sql("SELECT * FROM {iassign_statement} s WHERE s.id != s.file");
    $fs = get_file_storage();

    // 4. percorrer o conjunto de atividades:
    foreach ($iassign_statement_list as $iassign_statement_activity_item) {
        
      // 5. encontrar o arquivo, considerando os possíveis contextos:
      $files = array();
      foreach ($contexts as $context) {

        $files = $fs->get_area_files($context, 'mod_iassign', 'exercise', $iassign_statement_activity_item->file);

        // 6. se o arquivo for encontrado, fazer uma cópia do conteúdo, 
        // com o itemid novo, atualizar o iassign_statement, e apagar o arquivo antigo:
        if ($files) {
          foreach ($files as $value) {
            if ($value != null && $value->get_filename() != ".") {
              // 6.A. Fazer uma cópia:
              $newfile = $fs->create_file_from_storedfile(array('contextid' => $context, 'component' => 'mod_iassign', 'filearea' => 'exercise', 'itemid' => $iassign_statement_activity_item->id), $value);

              // 6.B. Atualizar o registro da atividade para o arquivo novo:
              $update_entry = new stdClass();
              $update_entry->id = $iassign_statement_activity_item->id;
              $update_entry->file = $newfile->get_itemid();
              $update_entry->filesid = $newfile->get_itemid();
              $DB->update_record("iassign_statement", $update_entry);

              // 6.C. Remover o arquivo antigo:
              $value->delete();
              }
            else if ($value != null && $value->get_filename() == ".") {
              // 6.C.I. Remover também os indicadores de diretório:
              $value->delete();
              }
            }
            break;
          } // if ($files)
        } // foreach ($contexts as $context)
      } // foreach ($iassign_statement_list as $iassign_statement_activity_item)
    } // if ($oldversion < 2020120500)

  /// @Igor - adicionar a tabela iassign_allsubmissions
  if ($oldversion < 2020122900) { 

    // Define table iassign_allsubmissions to be created.
    $table = new xmldb_table('iassign_allsubmissions');

    // Adding fields to table iassign_allsubmissions.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('iassign_statementid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '16', null, XMLDB_NOTNULL, null, null);
    $table->add_field('grade', XMLDB_TYPE_FLOAT, null, null, null, null, null, null);
    $table->add_field('answer', XMLDB_TYPE_TEXT, 'long', null, null, null, null, null);

    // Adding keys to table iassign_allsubmissions.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally launch create table for iassign_allsubmissions.
    if (!$dbman->table_exists($table)) {
      $dbman->create_table($table);
      }

    // Adding fields to table iassign_ilm.
    $table = new xmldb_table('iassign_ilm');

    $field_editingbehavior = new xmldb_field('editingbehavior', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);

    if (!$dbman->field_exists($table, $field_editingbehavior))
      $dbman->add_field($table, $field_editingbehavior);

    $field_submissionbehavior = new xmldb_field('submissionbehavior', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);

    if (!$dbman->field_exists($table, $field_submissionbehavior))
      $dbman->add_field($table, $field_submissionbehavior);

    $field_action_buttons = new xmldb_field('action_buttons', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', null);
    
    if (!$dbman->field_exists($table, $field_action_buttons))
      $dbman->add_field($table, $field_action_buttons);

    // Adding field to table iassign_statement:
    $table = new xmldb_table('iassign_statement');

    $field_store_all_submissions = new xmldb_field('store_all_submissions', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);

    if (!$dbman->field_exists($table, $field_store_all_submissions))
      $dbman->add_field($table, $field_store_all_submissions);

    // Update new fields for previous installed iLM:
    $iassign_ilm = $DB->get_records('iassign_ilm');
    foreach ($iassign_ilm as $iassign) { 
      $updateentry = new stdClass();
      $updateentry->id = $iassign->id;
      if (($iassign->name == 'iHanoi' && $iassign->type == 'HTML5')) {
        $updateentry->editingbehavior = 0;
        $updateentry->submissionbehavior = 0;
        }
      if (($iassign->name == 'iGeom' && $iassign->type == 'Java')) {
        $updateentry->editingbehavior = 0;
        $updateentry->submissionbehavior = 0;
        }
      if (($iassign->name == 'iVProg' && $iassign->type == 'HTML5')) {
        $updateentry->editingbehavior = 1;
        $updateentry->submissionbehavior = 0;
        }
      if (($iassign->name == 'iFractions' && $iassign->type == 'HTML5')) {
        $updateentry->editingbehavior = 0;
        $updateentry->submissionbehavior = 1;
        }
      if (($iassign->name == 'Risko' && $iassign->type == 'Java')) {
        $updateentry->editingbehavior = 1;
        $updateentry->submissionbehavior = 0;
        }
      if (isset($updateentry->editingbehavior)) {
        $updateentry->timemodified = time();
        $DB->update_record("iassign_ilm", $updateentry);
        }
      }

    // Add iassign_allsubmissions table
    $table = new xmldb_table('iassign_allsubmissions');

    if (!$dbman->table_exists($table)) {
      $field1 = new xmldb_field('id');
      $field1->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);

      $field2 = new xmldb_field('iassign_statementid');
      $field2->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);

      $field3 = new xmldb_field('userid');
      $field3->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);
      
      $field4 = new xmldb_field('timecreated');
      $field4->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);

      $field5 = new xmldb_field('grade');
      $field5->set_attributes(XMLDB_TYPE_FLOAT, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null);

      $field6 = new xmldb_field('answer');
      $field6->set_attributes(XMLDB_TYPE_TEXT, 'long', null, null, null, null, 'type');

      $table->addIndex($field1);
      $table->addField($field2);
      $table->addField($field3);
      $table->addField($field4);
      $table->addField($field5);
      $table->addField($field6);

      $dbman->create_table($table);
      } // if (!$dbman->table_exists($table))

    // Update file_jar field:
    try {
      $DB->execute("ALTER TABLE {iassign_ilm} DROP INDEX {iassilm_fil_uix}");
    } catch (Exception $ex) {}

    // Create $CFG->dataroot/temp/iassign_files
    $tempfilespath = $CFG->dataroot . DIRECTORY_SEPARATOR . 'temp';
    if (!file_exists($tempfilespath)) {
      mkdir($tempfilespath, 0777, true);
      }
    $iassignfilespath = $tempfilespath . DIRECTORY_SEPARATOR . 'iassign_files';
    if (!file_exists($iassignfilespath)) {
      mkdir($iassignfilespath, 0777, true);
      }

    } // if ($oldversion < 2020122900)

    if ($oldversion < 2021020700) { 

      // Update SAW iVProg to inform reevalute is enabled:
      $updateentry = new stdClass();
      $updateentry->id = 57;
      $updateentry->reevaluate = 1;
      $updateentry->timemodified = time();
      $DB->update_record("iassign_ilm", $updateentry); // insert new iLM
    }
    

  // log event -----------------------------------------------------
  if (class_exists('plugin_manager'))
    $pluginman = plugin_manager::instance();
  else
    $pluginman = core_plugin_manager::instance();
  $plugins = $pluginman->get_plugins();
  iassign_log::add_log('upgrade', 'version: ' . $plugins['mod']['iassign']->versiondisk);
  // log event -----------------------------------------------------

  return true;

  } // function xmldb_iassign_upgrade($oldversion)