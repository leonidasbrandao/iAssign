<?php

/**
 * Form to display activities iAssign.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.0 2012/10/14
 * @package mod_iassign
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.
 */
if(!defined('MOODLE_INTERNAL')) {
  die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/gradelib.php');

$id = required_param('id', PARAM_INT);   // course

if(!$course = $DB->get_record('course', array('id' => $id))) {
  print_error('invalidcourseid');
}

require_course_login($course);
$PAGE->set_pagelayout('incourse');
add_to_log($course->id, "iassign", "view all", "index.php?id=$course->id", "");


/// Get all required stringsia
$striassigns = get_string("modulenameplural", "iassign");
$striassign = get_string("modulename", "iassign");

$PAGE->set_url('/mod/iassign/index.php', array('id' => $course->id));
$PAGE->navbar->add($striassigns);
$PAGE->set_title($striassign);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

/// Get all the appropriate data
if(!$ias = get_all_instances_in_course("iassign", $course)) {
  notice(get_string('thereareno', 'moodle', $striassigns), "../../course/view.php?id=$course->id");
  //notice("There are no ias", "../../course/view.php?id=$course->id");
  die;
}

/// Print the list of instances (your module will probably extend this)
$timenow = time();
$strname = get_string("name");
$strweek = get_string("week");
$strtopic = get_string("topic");

$table = new html_table();

if($course->format == "weeks") {
  $table->head = array($strweek, $strname);
  $table->align = array("center", "left");
} else if($course->format == "topics") {
  $table->head = array($strtopic, $strname);
  $table->align = array("center", "left", "left", "left");
} else {
  $table->head = array($strname);
  $table->align = array("left", "left", "left");
}

foreach ($ias as $iassign) {
  if(!$iassign->visible) {
    //Show dimmed if the mod is hidden
    $link = "<a class=\"dimmed\" href=\"view.php?id=$iassign->coursemodule\">$iassign->name</a>";
  } else {
    //Show normal if the mod is visible
    $link = "<a href=\"view.php?id=$iassign->coursemodule\">$iassign->name</a>";
  }

  if($course->format == "weeks" or $course->format == "topics") {
    $table->data[] = array($iassign->section, $link);
  } else {
    $table->data[] = array($link);
  }
}

echo "<br />";

echo html_writer::table($table);

/// Finish the page
echo $OUTPUT->footer();
