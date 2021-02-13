<?php

/**
 * Settings iLM manager. Reaches this code from administrative Moodle area, in setting 'plugins' of iAssign.
 * 1. action==new_version: allow enter a new version of a selected iLM
 * 2. action==new_version
 * 
 * Release Notes:
 * - v 1.6.1 2017/12/02
 *   + Changed 'echo' to 'print'
 * - v 1.6 2013/10/31
 *   + Insert support of import iLM from zip packages.
 * - v 1.5 2013/10/24
 *   + Insert function for upgrade an iLM.
 * - v 1.4 2013/08/02
 *   + Insert list of iLMs informations for teacher view.
 * - v 1.3 2013/07/12
 *   + Insert actions: copy (new version from an iLM) and new version (empty new version).
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.6.1 2017/12/02
 * @package mod_iassign_settings
 * @since 2013/01/29
 * @see   locallib.php : class ilm_settings
 * @see   settings_form.php
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $USER, $PAGE, $OUTPUT, $DB;

require_once("../../config.php");
require_once($CFG->dirroot . '/mod/iassign/locallib.php');
require_once($CFG->dirroot . '/mod/iassign/settings_form.php');


require_login();
if (isguestuser()) {
  die();
  }

//Parameters GET e POST (parâmetros GET e POST)
$ilm_id = optional_param('ilm_id', 0, PARAM_INT);
$ilmid = optional_param('ilmid', 0, PARAM_INT);
$ilm_parent = optional_param('ilm_parent', 0, PARAM_INT);
$status = optional_param('status', 0, PARAM_INT);
$action = optional_param('action', NULL, PARAM_TEXT);
$url = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign'));
$from = optional_param('from', NULL, PARAM_TEXT);

$contextuser = context_user::instance($USER->id);

$PAGE->set_url($url);
$PAGE->set_context($contextuser);
$PAGE->blocks->show_only_fake_blocks(); //
$PAGE->set_pagelayout('popup');

if ($action == 'edit') { // Edit data of an iLM => processed in 'settings_form.php'

  $title = get_string('edit_ilm', 'iassign') . $OUTPUT->help_icon('add_ilm_iassign', 'iassign');
  $PAGE->set_title($title);

  // Get all fields of this iLM: name, type, set_lang, description_lang, author, action, timecreated, timemodified, parent, ...
  $param = ilm_settings::add_edit_copy_ilm($ilm_id, $action); // locallib.php: class ilm_settings: add_edit_copy_ilm($ilm_id, $action)

  //D echo "settings_ilm.php: edit: $title"; // echo "param->description="; print_r($param->description); 
  //D $description = $param->description_lang; // used to present the iLM description in 'settings_form.php'
  //D $description = $param->description; //TODO in 'settings_form.php' it does NOT present the description!!!
  $description = $param->description_lang; // used to present the iLM description in 'settings_form.php' - {"en":"...","pt":"..."}

  $mform = new mod_ilm_form(); // in 'settings_form.php': class mod_ilm_form
  //DEBUG: do NOT use "mod_ilm_form($param)" Warning: htmlspecialchars() expects parameter 1 to be string, object given in /var/www/html/saw/lib/pear/HTML/Common.php on line 177
  //DEBUg: since bellow fills form data
  $mform->set_data($param);

  if ($mform->is_cancelled()) {
    close_window();
    die;
    }
  else if ($formdata = $mform->get_submitted_data()) { // chegando aqui com $formdata->type vazio!
    // $formdata = { [name] [version] [type]vazio! [url] [lang] [description] [extension] [width] [height] [evaluate] [file] [id] [set_lang] [description_lang] [author] [action] [timecreated] [timemodified] [parent] [enable] [submitbutton]
    ilm_settings::edit_ilm($formdata, $formdata->file); // localib.php: class ilm_settings
    close_window(0, true);
    die;
    }

  print $OUTPUT->header();
  print $OUTPUT->heading($title); // put the header title
  $mform->display();
  print $OUTPUT->footer();

  die;
  } // if ($action == 'edit')
else

if ($action == 'new_version') { // Administration > plugins > iAssign : after select the iLM and the option 'Add new iLM version'

  $title = get_string('new_version_ilm', 'iassign') . $OUTPUT->help_icon('add_ilm_iassign', 'iassign');
  $PAGE->set_title($title);
  $param = ilm_settings::add_edit_copy_ilm($ilm_id, $action); // locallib.php: class iassign
    
  $mform = new mod_ilm_form();
  $mform->set_data($param);

  if ($mform->is_cancelled()) {
    close_window();
    die;
    }
  else { // Final form processing! Registered by 'locallib.php' with ilm_settings::copy_new_version_ilm($formdata)
    $formdata = $mform->get_data();
    if ($formdata) { // already exists the iLM
      //D echo "settings_ilm.php: FINAL!<br/>"; print_r($formdata);
      ilm_settings::copy_new_version_ilm($formdata); // locallib.php: class ilm_settings
      close_window(0, true);
      die;
      }
    }

  print($OUTPUT->header());
  print($OUTPUT->heading($title));
  $mform->display();
  print($OUTPUT->footer());
  die;
  }
else

if ($action == 'copy') {
  $title = get_string('copy_ilm', 'iassign') . $OUTPUT->help_icon('add_ilm_iassign', 'iassign');
  $PAGE->set_title($title);
  $param = ilm_settings::add_edit_copy_ilm($ilm_id, $action);

  $mform = new mod_ilm_form();
  $mform->set_data($param);
  if ($mform->is_cancelled()) {
    close_window();
    die;
    }
  else if ($formdata = $mform->get_data()) {
    ilm_settings::copy_new_version_ilm($formdata);
    close_window(0, true);
    die;
    }

  print($OUTPUT->header());
  print($OUTPUT->heading($title));
  $mform->display();
  print($OUTPUT->footer());
  die;
  }
else

if ($action == 'add') {
  $title = get_string('add_ilm_iassign', 'iassign') . $OUTPUT->help_icon('add_ilm_iassign', 'iassign');
  $PAGE->set_title($title);
  $param = ilm_settings::add_edit_copy_ilm($ilm_id, $action);

  $mform = new mod_ilm_form();
  $mform->set_data($param);

  if ($mform->is_cancelled()) {
    close_window();
    die;
    }
  else if ($formdata = $mform->get_data()) {

    $extension = explode(".", $mform->get_new_filename('file'));
    
    if ($extension[count($extension) - 1] == 'ipz') {
       $retorno = ilm_settings::new_ilm($formdata->file);
       if($retorno == true) {
         close_window(0, true);
         die;
       }
     } else
       print($OUTPUT->notification(get_string('error_upload_ilm', 'iassign'), 'notifyproblem'));
    }
  print($OUTPUT->header());
  print($OUTPUT->heading($title));
  $mform->display();
  print($OUTPUT->footer());
  die;
  }
else

if ($action == 'import') {
  $title = get_string('import_ilm', 'iassign') . $OUTPUT->help_icon('import_ilm', 'iassign');
  $PAGE->set_title($title);
  $param = new stdClass();
  $param->action = $action;
  $CFG->action_ilm = $action;

  $mform = new mod_ilm_form();
  $mform->set_data($param);
  if ($mform->is_cancelled()) {
    close_window();
    die;
    }
  else if ($formdata = $mform->get_data()) {
    $extension = explode(".", $mform->get_new_filename('file'));
    if ($extension[count($extension) - 1] == 'ipz') {
       $retorno = ilm_settings::import_ilm($formdata->file);
       if ($retorno == true) {
         close_window(5, true);
         die;
         }
      }
    else
      print($OUTPUT->notification(get_string('error_upload_ilm', 'iassign'), 'notifyproblem'));
    }
  print($OUTPUT->header());
  print($OUTPUT->heading($title));
  $mform->display();
  print($OUTPUT->footer());
  die;
  }
else

if ($action == 'confirm_delete_ilm') {
  $title = get_string('delete_ilm', 'iassign');
  $PAGE->set_title($title);
  $PAGE->set_pagelayout('base');
  $delete_ilm = ilm_settings::confirm_delete_ilm($ilm_id, $ilm_parent);
  print($OUTPUT->header());
  print($OUTPUT->heading($title));
  print($delete_ilm);
  print($OUTPUT->footer());
  die;
  }
else

if ($action == 'delete') {
  
  $title = get_string('delete_ilm', 'iassign');
  $PAGE->set_title($title);
  
  $PAGE->set_pagelayout('redirect');
  $parent = ilm_settings::delete_ilm($ilm_id);

  if ($parent == null) {
    $title = get_string('delete_ilm', 'iassign');
    $PAGE->set_title($title);
    $PAGE->set_pagelayout('base');
    $delete_ilm = ilm_settings::confirm_delete_ilm($ilm_id, $ilm_parent);
    print($OUTPUT->header());
    print($OUTPUT->heading($title));
    print($OUTPUT->notification(get_string('error_folder_permission_denied', 'iassign'), 'notifyproblem'));
    print($delete_ilm);
    print($OUTPUT->footer());
    die;
    }
  
  if ($parent == 0)
    redirect(new moodle_url('/admin/settings.php?', array('section' => 'modsettingiassign', 'action' => 'view')));
  else
    redirect(new moodle_url('/admin/settings.php?', array('section' => 'modsettingiassign', 'action' => 'config', 'ilm_id' => $ilm_parent)));
  }
else

if ($action == 'confirm_default_ilm') {
  $title = get_string('confirm_default', 'iassign');
  $PAGE->set_title($title);
  $PAGE->set_pagelayout('base');
  $default_ilm = ilm_settings::confirm_default_ilm($ilm_id, $ilm_parent);

  print($OUTPUT->header());
  print($default_ilm);
  print($OUTPUT->footer());
  die;
  }
else

if ($action == 'default') {
  $title = get_string('default_ilm', 'iassign');
  $PAGE->set_title($title);
  $PAGE->set_pagelayout('redirect');
  ilm_settings::default_ilm($ilm_id);
  redirect(new moodle_url('/admin/settings.php?', array('section' => 'modsettingiassign', 'action' => 'config', 'ilm_id' => $ilm_parent)));
  }
else

if ($action == 'list') {
  $title = get_string('list_ilm', 'iassign');
  $PAGE->set_title($title);
  $list_ilm = ilm_settings::list_ilm();
  print($OUTPUT->header());
  print($OUTPUT->heading($title . $OUTPUT->help_icon('list_ilm', 'iassign')));
  print($list_ilm);
  print($OUTPUT->footer());
  die;
  }
else

if ($action == 'upgrade') {
  $title = get_string('upgrade_ilm_title', 'iassign');
  $PAGE->set_title($title);
  $PAGE->set_pagelayout('redirect');

  $ilm = ilm_settings::upgrade_ilm($ilm_id);
  if ($ilm == 0)
    redirect(new moodle_url('/admin/settings.php?', array('section' => 'modsettingiassign', 'action' => 'view')));
  else
    redirect(new moodle_url('/admin/settings.php?', array('section' => 'modsettingiassign', 'action' => 'config', 'ilm_id' => $ilm)));
  }
else

if ($action == 'view') {
  $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));
  $title = get_string('view_ilm', 'iassign') . $OUTPUT->help_icon('add_ilm_iassign', 'iassign');
  $PAGE->set_title($title . ': ' . $iassign_ilm->name . ' ' . $iassign_ilm->version);
  $view_ilm = ilm_settings::view_ilm($ilm_id, $from);
  print($OUTPUT->header());
  print($OUTPUT->heading($title . ': ' . $iassign_ilm->name . ' ' . $iassign_ilm->version));
  print($view_ilm);
  print($OUTPUT->footer());
  die;
  }
