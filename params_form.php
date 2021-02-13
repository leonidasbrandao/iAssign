<?php

/**
 * Form to add and edit iLM params.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.0 2013/09/11
 * @package mod_iassign_settings
 * @since 2010/09/11
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.
if (!defined('MOODLE_INTERNAL')) {
  die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
  }

require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->dirroot . '/course/moodleform_mod.php');
require_once ($CFG->dirroot . '/mod/iassign/lib.php');

/// This class create form based moodleform.
//  @see moodleform
class param_ilm_form extends moodleform {

  // Add elements to form
  function definition () {
    global $CFG, $COURSE, $USER, $DB;

    $mform = & $this->_form;

    //-------------------------------------------------------------------------------
    /// Adding the "data_ilm" fieldset, where all the common settings are showed

    $mform->addElement('header', 'data_param', get_string('data_param', 'iassign'));

    /// Adding the standard "name" field
    $mform->addElement('text', 'param_name', get_string('config_param_name', 'iassign'), array('size' => '55'));
    $mform->setType('param_name', PARAM_TEXT);
    $mform->addRule('param_name', get_string('required', 'iassign'), 'required');

    /// Adding the standard "version" field
    $mform->addElement('text', 'param_value', get_string('config_param_value', 'iassign'), array('size' => '55'));
    $mform->setType('param_value', PARAM_TEXT);
    $mform->addRule('param_value', get_string('required', 'iassign'), 'required');

    /// Adding the standard "description" field
    $mform->addElement('htmleditor', 'description', get_string('config_param_description', 'iassign'));
    $mform->setType('description', PARAM_RAW);
    $mform->addRule('description', get_string('required', 'iassign'), 'required');

    /// Adding the standard "evaluate" field
    $mform->addElement('selectyesno', 'visible', get_string('visible', 'iassign'));
    $mform->setDefault('visible', 1);
    $mform->addRule('visible', get_string('required', 'iassign'), 'required');

    $mform->addElement('hidden', 'id');
    $mform->setType('id', PARAM_INT);
    $mform->addElement('hidden', 'iassign_ilmid');
    $mform->setType('iassign_ilmid', PARAM_INT);
    $mform->addElement('hidden', 'action');
    $mform->setType('action', PARAM_TEXT);

    $this->add_action_buttons();
    }

  }
