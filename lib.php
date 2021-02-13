<?php

/**
 *  Library of functions and constants for module of activities iAssign.
 *  
 *  - v 1.3 2014/01/10
 *  		+ Insert comment unread in course module (iassign_cm_info_view).
 *  
 *  - v 1.2 2013/12/13
 *  		+ Insert log in iAssign actions.
 *  
 *  @author Patricia Alves Rodrigues
 *  @author Leônidas O. Brandão
 *  @version v 1.3 2014/01/10
 *  @package mod_iassign_lib
 *  @since 2010/09/27
 *  @copyright iMath (http://www.matematica.br) and LInE (http://line.ime.usp.br) - Computer Science Dep. of IME-USP (Brazil)
 *  
 *  <b>License</b> 
 *   - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once($CFG->dirroot . "/mod/iassign/locallib.php");

function iassign_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $iassignnode) {
  global $USER, $PAGE, $CFG, $DB, $OUTPUT;

  if (optional_param('iassign_current', 0, PARAM_INT)) {
    $childnode = $iassignnode->create(get_string('edit_iassign', 'iassign'), new moodle_url('/mod/iassign/view.php', array('action' => 'edit', 'id' => optional_param('id', 0, PARAM_INT), 'iassign_current' => optional_param('iassign_current', 0, PARAM_INT) )), navigation_node::TYPE_SETTING);

    // 2019/02/12
    $children_key_list = $iassignnode->get_children_key_list();

    if (is_array($children_key_list) && count($children_key_list)>0)
      $iassignnode->add_node($childnode, $children_key_list[0]); //TODO: verify...
    // else $iassignnode->add_node($childnode, "");

    }

  }


/// List of features supported in iAssign module
//  @param string $feature FEATURE_xx constant for requested feature
//  @return mixed True if module supports feature, false if not, null if doesn't know
function iassign_supports ($feature) {
  switch ($feature) {
    case FEATURE_GROUPS: return false;
    case FEATURE_GROUPINGS: return false;
    case FEATURE_GROUPMEMBERSONLY: return false;
    case FEATURE_MOD_INTRO: return true;
    case FEATURE_SHOW_DESCRIPTION: return true;
    case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
    case FEATURE_GRADE_HAS_GRADE: return true;
    case FEATURE_GRADE_OUTCOMES: return true;
    case FEATURE_GRADE_HAS_GRADE: return true;
    case FEATURE_BACKUP_MOODLE2: return true;
    default: return null;
    }
  }


/// Function for insert a link with messages in iassigns for students and teacher.
//  @param cm_info $cm_info array Informations of module course.
//  @see http://docs.moodle.org/dev/Module_visibility_and_display
function iassign_cm_info_view (cm_info $cm_info) {
  global $CFG, $DB, $USER, $COURSE;

  $comment_unread = "";
  $sum_comment = 0;
  $iassign_statements = $DB->get_records("iassign_statement", array("iassignid" => $cm_info->instance));
  foreach ($iassign_statements as $iassign_statement) {

    $cm = get_coursemodule_from_instance("iassign", $iassign_statement->id, optional_param('id', 0, PARAM_INT));
    if ($cm) {
      $contextuser = context_module::instance($cm->id);
      $teacher_access = has_capability('mod/iassign:evaluateiassign', $contextuser, $USER->id);
      $student_access = has_capability('mod/iassign:submitiassign', $contextuser, $USER->id);

      $iassign_submissions = array();
      if ($teacher_access) {
        $receiver = '1';
        $iassign_submissions = $DB->get_records("iassign_submission", array("iassign_statementid" => $iassign_statement->id));
        }
      else
      if ($student_access) {
        $receiver = '2';
        $iassign_submissions = $DB->get_records("iassign_submission", array("iassign_statementid" => $iassign_statement->id, "userid" => $USER->id));
        }

      foreach ($iassign_submissions as $iassign_submission) {
        $params = array('iassign_submissionid' => $iassign_submission->id, 'return_status' => '0', 'receiver' => $receiver);
        $strQuery = 'SELECT COUNT(iassign_submissionid) FROM {iassign_submission_comment} WHERE ' .
	            ' iassign_submissionid = :iassign_submissionid AND return_status= :return_status AND receiver= :receiver';
        $verify_message = $DB->get_record_sql($strQuery, $params);
        if ($verify_message)
          foreach ($verify_message as $tmp)
            $sum_comment += $tmp;
        }
      }
    }
  if ($sum_comment != 0) {
    $comment_unread_message = get_string('comment_unread', 'iassign');
    if ($sum_comment == 1)
      $comment_unread_message = get_string('comment_unread_one', 'iassign');
    if ($teacher_access)
      $comment_unread = "&nbsp;&nbsp;<a href='" . $CFG->wwwroot . "/mod/iassign/view.php?id=" . $cm_info->id . "&action=report&iassignid=" . $cm_info->instance . "'><font color='red'>" . iassign_icons::insert('comment_unread') . "&nbsp;($sum_comment&nbsp;" . $comment_unread_message . ")</font></a>";
    else if ($student_access) //http://localhost/moodle24/mod/iassign/view.php?id=463&userid_iassign=4&action=view&iassign_current=1
      $comment_unread = "&nbsp;&nbsp;<font color='red'>" . iassign_icons::insert('comment_unread') . "&nbsp;($sum_comment&nbsp;" . get_string('comment_unread', 'iassign') . ")</font>";
    }

  $cm_info->set_after_link($comment_unread);
  }


/// This function is used by the reset_course_userdata function in moodlelib.
//  @param $data the data submitted from the reset course.
//  @return array status array
function iassign_reset_userdata ($data) {
  return array();
  }


/// List of view style log actions
//  @return array
function iassign_get_view_actions () {
  return array('view', 'view submission');
  }


/// List of update style log actions
//  @return array
function iassign_get_post_actions () {
  return array('update', 'add', 'upload', 'update comment', 'update submission', 'delete iassign', 'add comment', 'add submission');
  }


/// Adds an iAssign instance
//  @param object $iassign An object from the form in mod_form.php
//  @return Fail / id number of the new instance
function iassign_add_instance ($data, $mform) {
  global $DB;

  $cmid = $data->coursemodule;

  $iassignid = $DB->insert_record("iassign", $data);
  $data->id = $iassignid;

  $context = context_module::instance($cmid);
  $iassign = $DB->get_record('iassign', array('id' => $iassignid), '*', MUST_EXIST);

  iassign_grade_item($iassign);

  // log event -----------------------------------------------------
  iassign_log::add_log('add_iassign', 'name: ' . $data->name, $cmid);
  // log event -----------------------------------------------------

  return $iassign->id;
  }


/// Display an item in grade of activities iAssign.
//  @param object $iassign An object from the form.
function iassign_grade_item ($iassign) {
  global $DB, $CFG;
  require_once($CFG->libdir . '/gradelib.php');

  /// @todo Ver código comentado
  // if (!$iassign->id = $DB->insert_record("iassign", $iassign))
  //      return false;
  // $iassign=(stripslashes_recursive($iassign));

  $iassign->grade = 0;
  $grades = NULL;

  if (array_key_exists('cmidnumber', $iassign)) { //it may not be always present
    $params = array('itemname' => $iassign->name, 'idnumber' => $iassign->cmidnumber);
    }
  else {
    $params = array('itemname' => $iassign->name);
    }

  if ($iassign->grade > 0) {
    $params['gradetype'] = GRADE_TYPE_VALUE;
    $params['gradiLMx'] = $iassign->grade;
    $params['grademin'] = 0;
    }
  else {
    $params['gradetype'] = GRADE_TYPE_NONE;
    }

  if ($grades === 'reset') {
    $params['reset'] = true;
    $grades = NULL;
    }
  else if (!empty($grades)) {
    // Need to calculate raw grade (Note: $grades has many forms)
    if (is_object($grades)) {
      $grades = array($grades->userid => $grades);
      }
    else if (array_key_exists('userid', $grades)) {
      $grades = array($grades['userid'] => $grades);
      }
    foreach ($grades as $key => $grade) {
      $grades[$key] = $grade = (array) $grade;
      }
    $grades[$key]['rawgrade'] = ($grade['rawgrade'] * $iassign->grade / 100);
    }

  grade_update('mod/iassign', $iassign->course, 'mod', 'iassign', $iassign->id, 0, $grades, $params);
  }


/// This function will update an existing instance with new data.
//  @param object $iassign An object from the form in mod.html
//  @return boolean Fail Return the result.
function iassign_update_instance ($data, $mform) {
  global $DB, $CFG;

  $data->id = $data->instance;
  $cmid = $data->coursemodule;

  $DB->update_record("iassign", $data);

  $context = context_module::instance($cmid);

  iassign_grade_item_update($data->id);

  // log event -----------------------------------------------------
  iassign_log::add_log('update_iassign', 'name: ' . $data->name, $cmid);
  // log event -----------------------------------------------------

  return true;
  }


/// Update an item in grade of activities iAssign. 
//  @param int $iassignid Id to activities iAssign
function iassign_grade_item_update ($iassignid) {
  global $USER, $CFG, $COURSE, $DB, $OUTPUT;
  require_once($CFG->libdir . '/gradelib.php');
  // $sum_grade = $DB->get_records_sql("SELECT SUM(grade) as total FROM {$CFG->prefix}iassign_statement s WHERE s.iassignid = '$iassignid' AND s.type_iassign=3");

  $sum_grade = 0;
  $grade = $DB->get_records('iassign_statement', array('iassignid' => $iassignid, 'type_iassign' => 3));
  foreach ($grade as $tmp) {
    $sum_grade += $tmp->grade;
    }

  $grade_iassign = $DB->get_record("iassign", array("id" => $iassignid));
  $grades = NULL;
  $params = array('itemname' => $grade_iassign->name);
  $params['iteminstance'] = $iassignid;
  $params['gradetype'] = GRADE_TYPE_VALUE;
  if ($sum_grade != 0) {
    $params['gradiLMx'] = $sum_grade;
    $params['rawgradiLMx'] = $sum_grade;
    }
  else {
    $params['gradiLMx'] = 0;
    $params['rawgradiLMx'] = 0;
    }
  $params['grademin'] = 0;

  grade_update('mod/iassign', $grade_iassign->course, 'mod', 'iassign', $iassignid, 0, $grades, $params);

  $grades = $DB->get_records('grade_grades', array('itemid' => $iassignid));
  if ($grades) {
    foreach ($grades as $grade) {
      $grade->rawgradiLMx = $params['rawgradiLMx'];
      $DB->update_record("grade_grades", $grade);
      }
    }
  }


/// Delete an item in grade of activities iAssign.
//  @param int $id Id to activities iAssign
function iassign_delete_instance ($id) {
  global $DB;
  $result = true;
  if (!$iassign = $DB->get_record("iassign", array("id" => $id))) {
    return false;
    }
  $DB->delete_records('event', array('modulename' => 'iassign', 'instance' => $iassign->id));
  iassign_grade_item_delete($iassign);

  $iassign_statements = $DB->get_records("iassign_statement", array("iassignid" => $iassign->id));
  if ($iassign_statements) {
    foreach ($iassign_statements as $iassign_statement) {
      $iassign_statements_submissions = $DB->get_records("iassign_submission", array("iassign_statementid" => $iassign_statement->id));
      if ($iassign_statements_submissions) {
        foreach ($iassign_statements_submissions as $iassign_statements_submission) {
          $DB->delete_records('iassign_submission_comment', array('iassign_submissionid' => $iassign_statements_submission->id));
          }
        $DB->delete_records("iassign_submission ", array("iassign_statementid" => $iassign_statement->id));
        }
      }
    $DB->delete_records("iassign_statement", array("iassignid" => $iassign->id));
    }

  if (!$DB->delete_records("iassign", array("id" => $iassign->id))) {
    $result = false;
    }

  return $result;
  }


/// Given an ID of an instance of this module,
//  this function will permanently delete the instance
//  and any data that depends on it.
//  @param object $iassign 
function iassign_grade_item_delete ($iassign) {
  global $DB, $CFG;
  require_once($CFG->libdir . '/gradelib.php');
  grade_update('mod/iassign', $iassign->course, 'mod', 'iassign', $iassign->id, 0, NULL, array('deleted' => 1));
  }


/// Return a small object with summary information about what a
//  user has done with a given particular instance of this module
//  Used for user activity reports.
//  $return->time = the time they did it
//  $return->info = a short text description
//  @return boolean Return the state of function.
function iassign_user_outline ($course, $user, $mod, $iassign) {
  return true;
  }


/// Print a detailed representation of what a user has done with
//  a given particular instance of this module, for user activity reports.
//  @return boolean Return the state of function.
function iassign_user_complete ($course, $user, $mod, $iassign) {
  return true;
  }


/// Given a course and a time, this module should find recent activity
//  that has occurred in ia activities and print it out.
//  Return true if there was output, or false is there was none.
//  @return boolean Return the state of function.
function iassign_print_recent_activity ($course, $isteacher, $timestart) {
  global $DB, $CFG;
  return false;  //  True if anything was printed, otherwise false
  }


/// Function to be run periodically according to the moodle cron
//  This function searches for things that need to be done, such
//  as sending out mail, toggling flags etc ...
//  @return boolean Return the state of function.
function iassign_cron () {
  global $DB, $CFG;
  return true;
  }


/// Must return an array of grades for a given instance of this module,
//  indexed by user.  It also returns a maximum allowed grade.
//  Example:
//     $return->grades = array of grades;
//     $return->maxgrade = maximum allowed grade;
//     return $return;
//  @param int $iassignid ID of an instance of this module
//  @return mixed Null or object with an array of grades and with the maximum grade
function iassign_grades ($iassignid) {
  return NULL;
  }


/// Must return an array of user records (all data) who are participants
//  for a given instance of ia. Must include every user involved
//  in the instance, independient of his role (student, teacher, admin...)
//  See other modules as example.
//  @param int $iassignid ID of an instance of this module
//  @return mixed boolean/array of students
function iassign_get_participants ($iassignid) {
  return false;
  }


/// This function returns if a scale is being used by one ia
//  it it has support for grading and scales. Commented code should be
//  modified if necessary. See forum, glossary or journal modules
//  as reference.
//  @param int $iassignid ID of an instance of this module
//  @return mixed boolean/array of students
function iassign_scale_used ($iassignid, $scaleid) {
  $return = false;
  /// @todo Ver código comentado
  //$rec = $DB->get_record("iassign","id","$iassignid","scale","-$scaleid");
  //if (!empty($rec)  && !empty($scaleid)) { $return = true; }
  return $return;
  }


/// Checks if scale is being used by any instance of ia.
//  This function was added in 1.9
//  This is used to find out if scale used anywhere
//  @param $scaleid int
//  @return boolean True if the scale is used by any ia
function iassign_scale_used_anywhere ($scaleid) {
  if ($scaleid and record_exists('iassign', 'grade', -$scaleid)) {
    return true;
    }
  else {
    return false;
    }
  }


/// Search iLM
//  @param boolean $enable
//  @return array of iLM
function search_iLM ($enable) {
  global $DB;
  $ilms = $DB->get_records('iassign_ilm', array('enable' => $enable));
  return $ilms;
  }


/// Base implementation for backing up subtype specific information
//  for one single module
//  @return boolean Return the state of function.
function backup_one_mod ($bf, $preferences, $iassign) {
  return true;
  }


/// Base implementation for backing up subtype specific information
//  for one single submission
//  @return boolean Return the state of function.
function backup_one_submission ($bf, $preferences, $iassign, $submission) {
  return true;
  }


/// Base implementation for restoring subtype specific information
//  for one single module
//  @return boolean Return the state of function.
function restore_one_mod ($info, $restore, $iassign) {
  return true;
  }


/// Base implementation for restoring subtype specific information
//  for one single submission
//  @return boolean
function restore_one_submission ($info, $restore, $iassign, $submission) {
  return true;
  }


/// Serves the data attachments. Implements needed access control ;-)
//  @param object $course
//  @param object $cm
//  @param object $context
//  @param string $filearea
//  @param array $args
//  @param bool $forcedownload
//  @return bool false if file not found, does not return if found - justsend the file
function iassign_pluginfile ($course, $cm, $context, $filearea, $args, $forcedownload) {
  global $CFG, $DB;

  require_course_login($course, true, $cm);

  $fileareas = array('exercise', 'submit', 'activity', 'ilm');
  if (!in_array($filearea, $fileareas)) {
    return false;
    }


  $fs = get_file_storage();
  $postid = (int) array_shift($args);
  $relativepath = implode('/', $args);
  $fullpath = "/$context->id/mod_iassign/$filearea/$postid/$relativepath";

  if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
    return false;
    }

  // finally send the file
  send_stored_file($file, 0, 0, false); // download MUST be forced - security!

  return false;
  }


/// Display an url from access iLM.
//  @calledby settings.php
//  @param string $url Initial url
//  @return string Return the url formatted.
function display_url_ilm ($url) {
  // note: empty URL are prevented in form validation
  $url = trim($url);

  // remove encoded entities - we want the raw URI here
  $url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');

  if (!preg_match('|^[a-z]+:|i', $url) and ! preg_match('|^/|', $url)) {
    // invalid URI, try to fix it by making it normal URL, please note relative url are not allowed, /xx/yy links are ok
    $url = 'http://' . $url;
    }

  return $url;
  }
