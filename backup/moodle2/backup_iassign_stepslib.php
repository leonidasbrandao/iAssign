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
 * @see backup_activity_structure_step
 */
/**
 * Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete choice structure for backup, with file and id annotations
 * @see backup_activity_structure_step
 */
class backup_iassign_activity_structure_step extends backup_activity_structure_step {

  /**
   * Define the structure for the iassign activity
   * @return void Return the root element (choice), wrapped into standard activity structure
   */
  protected function define_structure () {

    // To know if we are including userinfo
    $userinfo = $this->get_setting_value('userinfo');

    $iassign = new backup_nested_element('iassign', array('id'), array('name',
      'course',
      'intro',
      'introformat',
      'activity_group',
      'grade',
      'timeavailable',
      'timedue',
      'preventlate',
      'test',
      'max_experiment'));

    $statements = new backup_nested_element('statements');

    $statement = new backup_nested_element('statement', array('id'), array('name',
      'iassignid',
      'type_iassign',
      'proposition',
      'author_name',
      'author_modified_name',
      'iassign_ilmid',
      'file',
      'grade',
      'timecreated',
      'timemodified',
      'timeavailable',
      'timedue',
      'preventlate',
      'test',
      'special_param1',
      'position',
      'visible',
      'max_experiment',
      'dependency',
      'automatic_evaluate',
      'show_answer',
      'store_all_submissions',
      'filesid'));

    $iassign_submissions = new backup_nested_element('iassign_submissions');

    $iassign_submission = new backup_nested_element('iassign_submission', array('id'), array('iassign_statementid',
      'userid',
      'timecreated',
      'timemodified',
      'grade',
      'teacher',
      'answer',
      'experiment',
      'status'));

    $iassign_submission_comments = new backup_nested_element('iassign_submission_comments');

    $iassign_submission_comment = new backup_nested_element('iassign_submission_comment', array('id'), array('iassign_submissionid',
      'comment_authorid',
      'timecreated',
      'comment',
      'return_status',
      'receiver'));
    // Build the tree

    $iassign->add_child($statements);
    $statements->add_child($statement);

    $statement->add_child($iassign_submissions);
    $iassign_submissions->add_child($iassign_submission);

    $iassign_submission->add_child($iassign_submission_comments);
    $iassign_submission_comments->add_child($iassign_submission_comment);


    // Define sources
    $iassign->set_source_table('iassign', array('id' => backup::VAR_ACTIVITYID));

    $statement->set_source_sql('
            SELECT *
              FROM {iassign_statement}
             WHERE iassignid = ?', array(backup::VAR_PARENTID));

    if($userinfo) {
      $iassign_submission->set_source_table('iassign_submission', array('iassign_statementid' => backup::VAR_PARENTID));

      $iassign_submission_comment->set_source_table('iassign_submission_comment', array('iassign_submissionid' => backup::VAR_PARENTID));
    }

    // Define id annotations
    $iassign_submission->annotate_ids('user', 'userid');
    $iassign_submission->annotate_ids('user', 'teacher');
    $iassign_submission_comment->annotate_ids('user', 'comment_authorid');

    // Define file annotations
    $iassign->annotate_files('mod_iassign', 'exercise', null); // This file area hasn't itemid

    return $this->prepare_activity_structure($iassign);
  }

}
