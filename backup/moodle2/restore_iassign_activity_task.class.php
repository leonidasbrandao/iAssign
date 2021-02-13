<?php

/**
 * Define all the backup steps that will be used by the backup_iassign_activity_task
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
 * @see restore_activity_task
 */
/**
 * Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/iassign/backup/moodle2/restore_iassign_stepslib.php');

/**
 * iassign restore task that provides all the settings and steps to perform one complete restore of the activity.
 */
class restore_iassign_activity_task extends restore_activity_task {

  /**
   * Define (add) particular settings this activity can have.
   */
  protected function define_my_settings () {
    // No particular settings for this activity
  }

  /**
   * Define (add) particular steps this activity can have.
   */
  protected function define_my_steps () {
    // iAssign only has one structure step
    $this->add_step(new restore_iassign_activity_structure_step('iassign_structure', 'iassign.xml'));
  }

  /**
   * Define the contents in the activity that must be
   * processed by the link decoder.
   * @return array Return a content of activity
   */
  static public function define_decode_contents () {
    $contents = array();

    $contents[] = new restore_decode_content('iassign', array('name'), 'iassign');

    return $contents;
  }

  /**
   * Define the decoding rules for links belonging
   * to the activity to be executed by the link decoder.
   * @return array Return the restore decode rule.
   */
  static public function define_decode_rules () {
    $rules = array();

    $rules[] = new restore_decode_rule('IASSIGNVIEWBYID', '/mod/iassign/view.php?id=$1', 'course_module');
    $rules[] = new restore_decode_rule('IASSIGNINDEX', '/mod/iassign/index.php?id=$1', 'course_module');

    return $rules;
  }

  /**
   * Define the restore log rules that will be applied
   * by the {@link restore_logs_processor} when restoring
   * iassign logs. It must return one array
   * of {@link restore_log_rule} objects.
   * @return array Return the restore log rule.
   */
  static public function define_restore_log_rules () {
    $rules = array();

    $rules[] = new restore_log_rule('iassign', 'add', 'view.php?id={course_module}', '{iassign}');
    $rules[] = new restore_log_rule('iassign', 'update', 'view.php?id={course_module}', '{iassign}');
    $rules[] = new restore_log_rule('iassign', 'view', 'view.php?id={course_module}', '{iassign}');
    $rules[] = new restore_log_rule('iassign', 'view submission', 'view.php?id={course_module}', '{iassign}');
    $rules[] = new restore_log_rule('iassign', 'upload', 'view.php?id={course_module}', '{iassign}');
    $rules[] = new restore_log_rule('iassign', 'update comment', 'view.php?id={course_module}', '{iassign}');
    $rules[] = new restore_log_rule('iassign', 'update submission', 'view.php?id={course_module}', '{iassign}');
    $rules[] = new restore_log_rule('iassign', 'delete iassign', 'view.php?id={course_module}', '{iassign}');
    $rules[] = new restore_log_rule('iassign', 'add comment', 'view.php?id={course_module}', '{iassign}');
    $rules[] = new restore_log_rule('iassign', 'add submission', 'view.php?id={course_module}', '{iassign}');
    // more...

    return $rules;
  }

  /**
   * Define the restore log rules that will be applied
   * by the {@link restore_logs_processor} when restoring
   * course logs. It must return one array
   * of {@link restore_log_rule} objects
   *
   * Note this rules are applied when restoring course logs
   * by the restore final task, but are defined here at
   * activity level. All them are rules not linked to any module instance (cmid = 0)
   *
   * @return array Return the restore log rule of course.
   */
  static public function define_restore_log_rules_for_course () {
    $rules = array();

    return $rules;
  }

}
