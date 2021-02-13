<?php

/**
 * This file keeps track of brand new installation of iAssign.
 * 
 * iAssign allow insertion of new "interactive Learning Modules" (iLM),
 * at any times, by the Moodle administrator.
 * It cames with some iLM from Laboratory of Informatics in Education (LInE),
 * like iVProg (www.matematica.br/ivprog) and iFractions (www.matematica.br/ifractions).
 * The purpose of this scritp is to insert theses initial iLM in your iAssign module.
 * 
 * - v 1.5.3 2020/08/30
 *     + Removed all commands associated to 'upgrade'
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
 * @version v 1.5.3 2020/08/30
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

///
//  Code run after the mod_iassign module database tables have been created.
//  Disables this plugin for new installs
//  @return bool
function xmldb_iassign_install () {
// function xmldb_iassign_upgrade ($oldversion)

  global $CFG, $DB, $USER;

  $dbman = $DB->get_manager();

  // iLM availables: iFractions; iGeom; iGraf; iHanoi; iVProg; Risko
  // HTML+JS iLM   : iFractions; iHanoi; iVProg
  // Java iLM      : iGeom; iGraf

    $table = new xmldb_table('iassign_submission');
    $field = new xmldb_field('previous_grade', XMLDB_TYPE_FLOAT, null, null, null, null, null);
    if (!$dbman->field_exists($table, $field)) {
      $dbman->add_field($table, $field);
      }

    $records = array(
      array_combine( // iVProg 1.0.20200121 - HTML5
        array('name', 'url', 'version', 'type', 'description',
              'extension', 'file_jar', 'file_class', 'width', 'height',
              'enable', 'evaluate', 'reevaluate', 'timemodified', 'author', 'timecreated',
              'editingbehavior', 'submissionbehavior', 'action_buttons'),
        array('iVProg', 'http://www.usp.br/line/ivprog/', '1.0.20200121', 'HTML5', '{"en":"Visual Interactive Programming on the Internet (HTML)","pt_br":"Programação visual interativa na Internet"}',
              'ivph', 'ilm/iVProg/1.0.20200221/ivprog/', 'index.html', 800, 600,
              1, 1, 1, time(), $USER->id, time(),
              1, 0, 1)),
      array_combine( // iHanoi 1.0.20200803 - HTML5
        array('name', 'url', 'version', 'type', 'description',
              'extension', 'file_jar', 'file_class', 'width', 'height',
              'enable', 'evaluate', 'reevaluate', 'timemodified', 'author', 'timecreated',
              'editingbehavior', 'submissionbehavior', 'action_buttons'),
        array('iHanoi', 'http://www.matematica.br/ihanoi', '1.0.20200803', 'HTML5', '{"en":"interactive Tower os Hanoi (by LInE)", "pt_br":"Torres de Hanói (do LInE)"}',
              'ihn', 'ilm/iHanoi/1.0.20200803/ihanoi/', 'index.html', 1100, 500,
              1, 1, 1, time(), $USER->id, time(),
              0, 0, 1)),
      array_combine( // iFractions 0.1.20200221 - HTML5
        array('name', 'url', 'version', 'type', 'description',
              'extension', 'file_jar', 'file_class', 'width', 'height',
              'enable', 'evaluate', 'reevaluate', 'timemodified', 'author', 'timecreated',
              'editingbehavior', 'submissionbehavior', 'action_buttons'),
        array('iFractions', 'http://www.matematica.br/ifractions', '0.1.20200221', 'HTML5', '{"en":"Interactive Fractions game","pt_br":"Jogo interativa de frações"}',
              'frc', 'ilm/iFractions/0.1.20200221/ifractions/', 'index.html', 1000, 600,
              1, 1, 0, time(), $USER->id, time(),
              0, 1, 0)),
      array_combine( // iGeom 5.9.22 - Java
        array('name', 'url', 'version', 'type', 'description',
              'extension', 'file_jar', 'file_class', 'width', 'height',
              'enable', 'evaluate', 'reevaluate', 'timemodified', 'author', 'timecreated',
              'editingbehavior', 'submissionbehavior', 'action_buttons'),
        array('iGeom', 'http://www.matematica.br/igeom', '5.9.22', 'Java', '{"en":"Interactive Geometry on the Internet","pt_br":"Geometria Interativa na Internet"}',
              'geo', 'ilm/iGeom/5.9.22/iGeom.jar', 'IGeomApplet.class', 800, 600, 
              1, 1, 0, time(), $USER->id, time(),
              0, 0, 1)),
      array_combine( // Risco 2.2.23 - Java
        array('name', 'url', 'version', 'type', 'description',
              'extension', 'file_jar', 'file_class', 'width', 'height',
              'enable', 'evaluate', 'reevaluate', 'timemodified', 'author', 'timecreated',
              'editingbehavior', 'submissionbehavior', 'action_buttons'),
        array('Risko', 'http://risko.pcc.usp.br/', '2.2.23', 'Java', '{"en":"Technical drawing with triangle and ruler","pt_br":"Desenho Geomẽtrico com esquadro"}',
              'rsk', 'ilm/Risko/2.2.23/Risko.jar', 'RiskoApplet.class', 800, 600,
              1, 0, 0, time(), $USER->id, time(),
              1, 0, 1))
      );

    $strInstalled_iLM = "";
    $error = 0;
    foreach ($records as $record) {
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
      $newentry->evaluate = $record['evaluate'];
      $newentry->reevaluate = $record['reevaluate'];
      $newentry->timemodified = time();
      $newentry->author = $USER->id;
      $newentry->timecreated = time();
      $newentry->editingbehavior = $record['editingbehavior'];
      $newentry->submissionbehavior = $record['submissionbehavior'];
      $newentry->action_buttons = $record['action_buttons'];
      try {
        $DB->insert_record("iassign_ilm", $newentry, false);
        $strInstalled_iLM .= "\n" . '  <li>' . $record['name'] . ';' . $record['type'] . ';' . $record['version'] . ' </li>' . "\n";	  
      } catch (Exception $e) {
        print "Error install.php: " . $e->getMessage() . "<br/>";
        $error = 1;
        }
      }

    if ($error == 1) {
      // $string['error_security_no_userid']       = 'Internal error: must be informed the user identification. Inform the Administrator.';
      print '<div class="alert alert-warning alert-block fade in " role="alert" data-aria-autofocus="true" tabindex="0" >' + "\n";
      print get_string('error_security_no_userid', 'iassign'); // Internal error: must be informed the user identification. Inform the Administrator
      // print '<a href="'.new moodle_url('/admin/settings.php?section=modsettingiassign').'">' . get_string('upgrade_alert_iMA_solution_pt2', 'iassign') . '</a>.' + "\n";
      print '</div>' + "\n";
      }
    else { // $string['cliinstallfinished'] = 'Installation completed successfully.';
      print '<div class="modal-content"><p>' . get_string('cliinstallfinished', 'install') . '</p>' . "\n";
      print ' <ul style="margin-top: 1rem;">' . "\n";
      print $strInstalled_iLM;
      print ' </ul>' . "\n";
      print '</div' . "\n";;
      }

    // Create $CFG->dataroot/temp/iassign_files
    $tempfilespath = $CFG->dataroot . DIRECTORY_SEPARATOR . 'temp';
    if (!file_exists($tempfilespath)) {
      mkdir($tempfilespath, 0777, true);
      }
    $iassignfilespath = $tempfilespath . DIRECTORY_SEPARATOR . 'iassign_files';
    if (!file_exists($iassignfilespath)) {
      mkdir($iassignfilespath, 0777, true);
      }

  // log event -----------------------------------------------------
  if (class_exists('plugin_manager'))
    $pluginman = plugin_manager::instance();
  else
    $pluginman = core_plugin_manager::instance();
  $plugins = $pluginman->get_plugins();
  iassign_log::add_log('install', 'version: ' . $plugins['mod']['iassign']->versiondisk);
  // log event -----------------------------------------------------

  return true;

  } // function xmldb_iassign_install()