<?php

/**
 * Form to add and edit instance of iAssign.
 * 
 * Release Notes:
 * - v 1.4 2016/05/12
 *      + Function moodleform_mod::add_intro_editor() is deprecated, it was then replaced by moodleform_mod::standard_intro_elements().
 * - v 1.3 2013/10/22
 * 		+ Clean tag of messages (mod_iassign_mod_form::data_preprocessing).
 * - v 1.2 2013/09/19
 * 		+ Insert general fields for iassign statement (grade, timeavaliable, timedue, preventlate, test, max_experiment).
 * 		+ Save general fields in iassign statement table (mod_iassign_mod_form::definition_after_data).
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.4 2016/05/12
 * @package mod_iassign
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.

if (!defined('MOODLE_INTERNAL')) {
  die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
  }

require_once ($CFG->dirroot . '/course/moodleform_mod.php');


/// This class create form based moodleform. 
//  @see moodleform_mod
class mod_iassign_mod_form extends moodleform_mod {

  function definition () {
    global $CFG, $DB;

    $mform = & $this->_form;
    $course_modules_id = optional_param('update', 0, PARAM_INT);

    //-------------------------------------------------------------------------------
    /// Adding the "title_type_iassign" fieldset, where all the common settings are showed
    $mform->addElement('header', 'general', get_string('general', 'iassign'));

    /// Adding the standard "name" field
    $mform->addElement('text', 'name', get_string('iassigntitle', 'iassign'), array('size' => '55'));
    if (!empty($CFG->formatstringstriptags)) {
      $mform->setType('name', PARAM_TEXT);
      }
    else {
      $mform->setType('name', PARAM_CLEANHTML);
      }
    $mform->addRule('name', null, 'required', null, 'client');

    $this->standard_intro_elements(get_string('description', 'iassign'));

    $mform->addElement('selectyesno', 'activity_group', get_string('activity_group', 'iassign'));
    $mform->setDefault('activity_group', 1);
    $mform->addHelpButton('activity_group', 'helpactivitygroup', 'iassign');

    // general fields
    $mform->addElement('header', 'general_fields', get_string('general_fields', 'iassign'));
    $timeavailable_group = array();
    $timeavailable_group[] = & $mform->createElement('date_time_selector', 'timeavailable', '');
    $mform->setDefault('timeavailable', time());
    if ($course_modules_id != 0)
      $timeavailable_group[] = & $mform->createElement('advcheckbox', 'timeavailable_enabled', '', get_string('enable'));
    $mform->addGroup($timeavailable_group, 'timeavailable_group', get_string('availabledate', 'iassign'), ' ', false);
    if ($course_modules_id != 0)
      $mform->disabledIf('timeavailable_group', 'timeavailable_enabled');

    $timedue_group = array();
    $timedue_group[] = & $mform->createElement('date_time_selector', 'timedue', '');
    $mform->setDefault('timedue', time() + 7 * 24 * 3600);
    if ($course_modules_id != 0)
      $timedue_group[] = & $mform->createElement('advcheckbox', 'timedue_enabled', '', get_string('enable'));
    $mform->addGroup($timedue_group, 'timedue_group', get_string('duedate', 'iassign'), ' ', false);
    if ($course_modules_id != 0)
      $mform->disabledIf('timedue_group', 'timedue_enabled');

    $preventlate_group = array();
    $preventlate_group[] = & $mform->createElement('selectyesno', 'preventlate', '');
    $mform->setDefault('preventlate', 0);
    if ($course_modules_id != 0)
      $preventlate_group[] = & $mform->createElement('advcheckbox', 'preventlate_enabled', '', get_string('enable'));
    $mform->addGroup($preventlate_group, 'preventlate_group', get_string('preventlate', 'iassign'), ' ', false);
    if ($course_modules_id != 0)
      $mform->disabledIf('preventlate_group', 'preventlate_enabled');
    $mform->addHelpButton('preventlate_group', 'helppreventlate', 'iassign');

    $test_group = array();
    $test_group[] = & $mform->createElement('selectyesno', 'test', '');
    $mform->setDefault('test', 0);
    if ($course_modules_id != 0)
      $test_group[] = & $mform->createElement('advcheckbox', 'test_enabled', '', get_string('enable'));
    $mform->addGroup($test_group, 'test_group', get_string('permission_test', 'iassign'), ' ', false);
    if ($course_modules_id != 0)
      $mform->disabledIf('test_group', 'test_enabled');
    $mform->addHelpButton('test_group', 'helptest', 'iassign');

    $mform->addElement('modgrade', 'grade', get_string('grade', 'iassign'));
    $max_experiment_options = array(0 => get_string('ilimit', 'iassign'));
    for ($i = 1; $i <= 20; $i++)
      $max_experiment_options[$i] = $i;

    $max_experiment_group = array();
    $max_experiment_group[] = & $mform->createElement('select', 'max_experiment', '', $max_experiment_options);
    $mform->setDefault('max_experiment', 0);
    if ($course_modules_id != 0)
      $max_experiment_group[] = & $mform->createElement('advcheckbox', 'max_experiment_enabled', '', get_string('enable'));
    $mform->addGroup($max_experiment_group, 'max_experiment_group', get_string('experiment', 'iassign'), ' ', false);
    if ($course_modules_id != 0)
      $mform->disabledIf('max_experiment_group', 'max_experiment_enabled');
    $mform->addHelpButton('max_experiment_group', 'helpexperiment', 'iassign');

    //-------------------------------------------------------------------------------
    // Hidden fields
    $mform->addElement('hidden', 'id');
    $mform->setType('id', PARAM_TEXT);

    if ($course_modules_id != 0) {
      $course_modules = $DB->get_record("course_modules", array('id' => $course_modules_id));
      $iassign_statement = $DB->get_records("iassign_statement", array('iassignid' => $course_modules->instance));
      if ($iassign_statement) {
        $mform->addElement('header', 'header_general_fields_apply', get_string('general_fields_apply', 'iassign'));
        $mform->addHelpButton('header_general_fields_apply', 'general_fields_apply', 'iassign');

        $this->add_checkbox_controller('atividades');

        foreach ($iassign_statement as $iassign) {
          $tmp = 'iassign_statement[' . $iassign->id . ']';
          $mform->addElement('advcheckbox', $tmp, $iassign->name, null, array('group' => 'atividades'));
          }
        }
      }

    $this->standard_coursemodule_elements();
    $this->add_action_buttons();
    } // function definition()


  function data_preprocessing (&$default_values) {
    parent::data_preprocessing($default_values);

    $mform = & $this->_form;

    if (!$mform->isSubmitted() && array_key_exists('name', $default_values)) {
      //TODO Remove when updating all the iassign that are tag &lt;ia_uc&gt;
      $ia_uc = explode('&lt;ia_uc&gt;', $default_values['name']);
      $default_values['name'] = $ia_uc[0];
      }
    }


  function definition_after_data() {
    global $DB;

    $mform = & $this->_form;
    $data = $mform->exportValues();

    if ($mform->isSubmitted() && array_key_exists('iassign_statement', $data)) {

      $iassign_statements = $data['iassign_statement'];
      foreach ($iassign_statements as $key => $value) {
        if ($value == 1) {
          $updateentry = new stdClass();
          $updateentry->id = $key;
          $updateentry->timemodified = time();

          if (isset($data['grade_enabled']) && $data['grade_enabled'] == 1)
            $updateentry->grade = $data['grade'];
          if (isset($data['timedue_enabled']) && $data['timedue_enabled'] == 1)
            $updateentry->timedue = $data['timedue'];
          if (isset($data['timeavailable_enabled']) && $data['timeavailable_enabled'] == 1)
            $updateentry->timeavailable = $data['timeavailable'];
          if (isset($data['preventlate_enabled']) && $data['preventlate_enabled'] == 1)
            $updateentry->preventlate = $data['preventlate'];
          if (isset($data['test_enabled']) && $data['test_enabled'] == 1)
            $updateentry->test = $data['test'];
          if (isset($data['max_experiment_enabled']) && $data['max_experiment_enabled'] == 1)
            $updateentry->max_experiment = $data['max_experiment'];

          if (!$DB->update_record("iassign_statement", $updateentry))
            print_error('error_update', 'iassign');
          }
        }
      }
    }

  } // class mod_iassign_mod_form extends moodleform_mod
