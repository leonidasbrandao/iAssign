<?php

/**
 * Settings iLM params manager.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.0 2013/09/11
 * @package mod_iassign_settings
 * @since 2013/09/11
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG, $USER, $PAGE, $OUTPUT, $DB;

require_once("../../config.php");
require_once ($CFG->dirroot . '/mod/iassign/locallib.php');
require_once ($CFG->dirroot . '/mod/iassign/params_form.php');


require_login();
if (isguestuser()) { // security!
  die();
  }

//Parameters GET e POST (parâmetros GET e POST)
$ilm_param_id = optional_param('ilm_param_id', 0, PARAM_INT);
$ilm_id = optional_param('ilm_id', 0, PARAM_INT);
$status = optional_param('status', 0, PARAM_INT);
$action = optional_param('action', NULL, PARAM_TEXT);
$url = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign'));
$from = optional_param('from', NULL, PARAM_TEXT);

$contextuser = context_user::instance($USER->id);

$PAGE->set_url($url);
$PAGE->set_context($contextuser);
$PAGE->blocks->show_only_fake_blocks(); //
$PAGE->set_pagelayout('popup');

if ($action == 'edit') {
  $title = get_string('edit_param', 'iassign') . $OUTPUT->help_icon('config_param', 'iassign');
  $PAGE->set_title($title);
  $param = ilm_settings::add_edit_copy_param($ilm_param_id, $action);

  $mform = new param_ilm_form();
  $mform->set_data($param);
  if ($mform->is_cancelled()) {
    close_window();
    die;
    }
  else if ($formdata = $mform->get_submitted_data()) {
    ilm_settings::edit_param($formdata);
    close_window(0, true);
    die;
    }

  echo $OUTPUT->header();
  echo $OUTPUT->heading($title);
  $mform->display();
  echo $OUTPUT->footer();
  die;
  }

if ($action == 'copy') {
  $title = get_string('copy_param', 'iassign') . $OUTPUT->help_icon('config_param', 'iassign');
  $PAGE->set_title($title);
  $param = ilm_settings::add_edit_copy_param($ilm_param_id, $action);

  $mform = new param_ilm_form();
  $mform->set_data($param);
  if ($mform->is_cancelled()) {
    close_window();
    die;
    }
  else if ($formdata = $mform->get_data()) {
    ilm_settings::copy_param($formdata);
    close_window(0, true);
    die;
    }

  echo $OUTPUT->header();
  echo $OUTPUT->heading($title);
  $mform->display();
  echo $OUTPUT->footer();
  die;
  }

if ($action == 'add') {
  $title = get_string('add_param', 'iassign') . $OUTPUT->help_icon('config_param', 'iassign');
  $PAGE->set_title($title);
  $param = ilm_settings::add_edit_copy_param($ilm_id, $action);

  $mform = new param_ilm_form();
  $mform->set_data($param);
  if ($mform->is_cancelled()) {
    close_window();
    die;
    }
  else if ($formdata = $mform->get_data()) {
    ilm_settings::add_param($formdata);
    close_window(0, true);
    die;
    }
  echo $OUTPUT->header();
  echo $OUTPUT->heading($title);
  $mform->display();
  echo $OUTPUT->footer();
  die;
  }

if ($action == 'delete') {
  $title = get_string('delete_param', 'iassign');
  $PAGE->set_title($title);
  $PAGE->set_pagelayout('redirect');
  ilm_settings::delete_param($ilm_param_id);
  redirect(new moodle_url('/admin/settings.php?', array('section' => 'modsettingiassign', 'action' => 'config', 'ilm_id' => $ilm_id)));
  }