<?php

/**
 * This script contains all the stuff to display evaluate.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.0 2010/12/21
 * @package mod_iassign
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

// Recovery th ID of active user.
$id = required_param('id', PARAM_INT); // Course module ID

// Constrain the url for redirect user.
$url = new moodle_url('/mod/iassign/view.php'); // novo

if ($id) {
  if (!$cm = get_coursemodule_from_id('iassign', $id)) {
    print_error('invalidcoursemodule');
    }

  if (!$iassign = $DB->get_record("iassign", array("id" => $cm->instance))) {
    print_error('invalidid', 'iassign');
    }

  if (!$course = $DB->get_record("course", array("id" => $iassign->course))) {
    print_error('coursemisconf', 'iassign');
    }
  $url->param('id', $id);
  }
else {
  if (!$iassign = $DB->get_record("iassign", array("id" => $a))) {
    print_error('invalidid', 'iassign');
    }
  if (!$course = $DB->get_record("course", array("id" => $iassign->course))) {
    print_error('coursemisconf', 'iassign');
    }
  if (!$cm = get_coursemodule_from_instance("iassign", $iassign->id, $course->id)) {
    print_error('invalidcoursemodule');
    }
  $url->param('a', $a);
  }

$PAGE->set_url($url);

require_login($course, true, $cm);

$PAGE->set_title(format_string($iassign->name));
$PAGE->set_heading($course->fullname);

require_once ("$CFG->dirroot/mod/iassign/locallib.php");


/// Get an instance of iassign.
$iassigninstance = new iassign($iassign, $cm, $course);

if (has_capability('mod/iassign:viewreport', context_module::instance($cm->id))) {
  $iassigninstance->action = 'report';
  $iassigninstance->stats();
  }
elseif (has_capability('mod/iassign:submitiassign', context_module::instance($cm->id)))
  $iassigninstance->stats_students();
else
  $iassigninstance->view();