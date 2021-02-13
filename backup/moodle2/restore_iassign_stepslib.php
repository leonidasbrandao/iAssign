<?php

/**
 * Define all the restore steps that will be used by the restore_iassign_activity_task
 *
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.0 2012
 * @package mod_iassign_backup
 * @since 2012
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  
 * @see restore_activity_structure_step
 */
/**
 * Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete assignment structure for restore, with file and id annotations.
 * @see restore_activity_structure_step
 */
class restore_iassign_activity_structure_step extends restore_activity_structure_step {

  /**
   * Define the structure of the restore workflow
   * @return void Adds support for the 'exercise' path that is common to all the activities.
   */
  protected function define_structure () {

    $paths = array();
    // To know if we are including userinfo
    $userinfo = $this->get_setting_value('userinfo');


    // Define each element separated
    $paths[] = new restore_path_element('iassign', '/activity/iassign');
    $paths[] = new restore_path_element('iassign_statement', '/activity/iassign/statements/statement');



    if($userinfo) {
      $iassign_submissions = new restore_path_element('iassign_submission', '/activity/iassign/statements/statement/iassign_submissions/iassign_submission');
      $paths[] = $iassign_submissions;
      $iassign_submission_comments = new restore_path_element('iassign_submission_comment', '/activity/iassign/statements/statement/iassign_submissions/iassign_submission/iassign_submission_comments/iassign_submission_comment');
      $paths[] = $iassign_submission_comments;
    }
    return $this->prepare_activity_structure($paths);
  }

  /**
   * Process an ia restore
   * @param object $data The data in object form
   */
  protected function process_iassign ($data) {
    global $DB;

    $data = (object) $data;
    $oldid = $data->id;
    $data->course = $this->get_courseid();
    $newitemid = $DB->insert_record('iassign', $data);
    $this->apply_activity_instance($newitemid);
  }

  /**
   * Process a iassign_statement restore.
   * @param object $data The data in object form
   */
  protected function process_iassign_statement ($data) {
    global $DB, $CFG;

    require_once($CFG->dirroot . '/mod/iassign/locallib.php');
    $data = (object) $data;
    $oldid = $data->id;
    $data->iassignid = $this->get_new_parentid('iassign');
    $newitemid = $DB->insert_record('iassign_statement', $data);
    $this->set_mapping('iassign_statement', $oldid, $newitemid, true); // Has related fileareas
    activity::add_calendar($newitemid);
  }

  /**
   * Process a iassign_submission restore.
   * @param object $data The data in object form
   */
  protected function process_iassign_submission ($data) {
    global $DB;

    $data = (object) $data;
    $oldid = $data->id;
    $data->iassign_statementid = $this->get_new_parentid('iassign_statement');
    $data->userid = $this->get_mappingid('user', $data->userid);
    $data->teacher = $this->get_mappingid('user', $data->teacher);
    $newitemid = $DB->insert_record('iassign_submission', $data);
    $this->set_mapping('iassign_submission', $oldid, $newitemid, true); // Has related fileareas
  }

  /**
   * Process a iassign_submission_comment restore.
   * @param object $data The data in object form
   */
  protected function process_iassign_submission_comment ($data) {
    global $DB;

    $data = (object) $data;
    $oldid = $data->id;
    $data->iassign_submissionid = $this->get_new_parentid('iassign_submission');
    $data->comment_authorid = $this->get_mappingid('user', $data->comment_authorid);
    $newitemid = $DB->insert_record('iassign_submission_comment', $data);
    $this->set_mapping('iassign_submission_comment', $oldid, $newitemid, true); // Has related fileareas
  }

  /**
   * Once the database tables have been fully restored, restore the files.
   */
  protected function after_execute () {
    global $CFG, $DB;

    $this->add_related_files('mod_iassign', 'exercise', null);

    $fs = get_file_storage();

    $iassigns = $DB->get_records('iassign', array('course' => $this->get_courseid()));
    foreach ($iassigns as $iassign) {
      $iassign_statements = $DB->get_records('iassign_statement', array('iassignid' => $iassign->id));
      foreach ($iassign_statements as $iassign_statement) {
        $files = $DB->get_records('files', array('component' => 'mod_iassign', 'filearea' => 'exercise', 'itemid' => $iassign_statement->file));
        if($files) {
          $filename = array();
          foreach ($files as $value) {
            if($value->filename != '.') {
              $filename = explode(".", $value->filename);
            }
          }
          $extension = "";
          if(count($filename) > 1)
            $extension = strtolower($filename[count($filename) - 1]);

          $iassign_ilms = $DB->get_records('iassign_ilm', array('parent' => 0, 'enable' => 1));
          foreach ($iassign_ilms as $iassign_ilm) {
            $extensions = explode(",", $iassign_ilm->extension);
            if(in_array($extension, $extensions))
              $iassign_statement->iassign_ilmid = $iassign_ilm->id;
          }
          $DB->update_record("iassign_statement", $iassign_statement);
        }
      }
    }
  }

}
