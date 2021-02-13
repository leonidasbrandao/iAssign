<?php

/**
 * This php script contains all the stuff to display iAssign.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leo^nidas O. Branda~o
 * @version v 1.0 2012/10/16
 * @package mod_iassign
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/plagiarismlib.php');

//DEBUG 2020/08/31
//D require_once("ilm_debug/escreva.php"); //leo REMOVER! xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
//D $resp = writeContent("", "", "todos_files_iassign.txt", "Teste"); // ($filetype1, $pathbase, $outputFile, $msgToRegister) //leo REMOVER!
function get_all_iassign_files () {
  // Get all iAssign files on table 'files':
  // files = id; contenthash; pathnamehash; contextid; component; filearea; itemid; filepath; filename; userid; filesize; mimetype; status; source; author;
  //         license; timecreated; timemodified; sortorder; referencefileid
  global $DB;
  // $all_files = $DB->get_records('files', array('component' => 'mod_iassign')); // pegar os do iAssign
  $indices = "id=257279 OR id=257791 OR id=258303 OR id=258559 OR id=127743 OR id=64767 OR id=65023 OR id= 65535 OR id= 87394 OR " .
             "id=138498 OR id=138506 OR id=138514 OR id=138522 OR id=50463 OR id=50464 OR id= 50465 OR id=138530 OR id= 50466 OR " .
             "id= 50467 OR id= 50468 OR id= 50469 OR id=50470 OR id=50471 OR id=104744 OR id=50472";
  $str_query = "SELECT * FROM {files} WHERE " . $indices . " ORDER BY timecreated DESC";
  $all_files = $DB->get_records_sql($str_query);

  $total = count($all_files);
  $msg = "#linhas = " . $total . "\n";  
  foreach ($all_files as $linha) {
    foreach ($linha as $key => $value) {
      if ($key == "timecreated") {
        $time1 = date('Y-m-d H:i:s', $value - date('Z')); // 12:50:29
        $msg .= $time1 . ";" . $value . ";";
        }
      else
      if ($key == "source") {
        // $item = str_replace(";", "\;", $value);
        $item = addslashes($value);
        $msg .= "'" . addslashes($value) . "';";
        }
      else $msg .= $value . ";";
      }
    $msg .= "\n";
    }
  require_once("ilm_debug/escreva.php"); //leo REMOVER! xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
  $resp = writeContent("", "", "todos_files_iassign.csv", $msg); // ($filetype1, $pathbase, $outputFile, $msgToRegister) //leo REMOVER!
  }

//D get_all_iassign_files(); //leo REMOVER! xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
//D Remover todos itens de 'files' tal que: filesize = 0

//Parameters GET e POST (parâmetros GET e POST)
$id = optional_param('id', 0, PARAM_INT); // Course Module ID
$a = optional_param('a', 0, PARAM_INT); //  iAssign instance id (from table 'iassign')

$mood_url = new moodle_url('/mod/iassign/view.php');

if ($id) {
  // ./lib/datalib.php : function get_coursemodule_from_id($modulename, $cmid, $courseid=0, $sectionnum=false, $strictness=IGNORE_MISSING): returns 'course_modules.*' and 'modules.name'
  $cm = get_coursemodule_from_id('iassign', $id);
  if (!$cm) { // Moodle function 'get_coursemodule_from_id(...)' returns the object from table '*_iassign_statement'
    print_error('invalidcoursemodule');
    }

  $iassign = $DB->get_record("iassign", array("id" => $cm->instance));
  if (!$iassign) { // 'course_modules.instance = iassign.id'
    print_error('invalidid', 'iassign');
    }

  $course = $DB->get_record("course", array("id" => $iassign->course));
  if (!$course) {
    print_error('coursemisconf', 'iassign');
    }
  $mood_url->param('id', $id);
  }
else {
  $iassign = $DB->get_record("iassign", array("id" => $a));
  if (!$iassign) {
    print_error('invalidid', 'iassign');
    }
  $course = $DB->get_record("course", array("id" => $iassign->course));
  if (!$course) {
    print_error('coursemisconf', 'iassign');
    }
  $cm = get_coursemodule_from_instance("iassign", $iassign->id, $course->id);
  if (!$cm) {
    print_error('invalidcoursemodule');
    }
  $mood_url->param('a', $a);
  }

$PAGE->set_url($mood_url);

require_login($course, true, $cm);

$PAGE->set_title(format_string($iassign->name));
$PAGE->set_heading($course->fullname);

// Mark viewed by user (if required)
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// About each object
// - $iassing :: object from table '*_iassign_statement'
// - $cm      :: object from table '*_course_modules'
// - $course  :: object from table '*_course_modules_completion'

$write_solution = 1;

// locallib.php : class iassign : function __construct ($iassign, $cm, $course)
//$iassigninstance = new iassign($iassign, $cm, $course, array('write_solution' => 1));
$iassigninstance = new iassign($iassign, $cm, $course);

 // ./mod/iassign/locallib.php : in class iassign, actually who display the iAssign whose id is '$id'! (this function ignores parameters)
$iassigninstance->view(); //     will call $this->action(), that calls view_iassign_current()
