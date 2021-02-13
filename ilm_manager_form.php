<?php

/**
 * Form to manager ilm installed into iAssign.
 *
 * Release Notes:
 * - v 1.7 2013/10/24
 * 		+ View extension when iassign view is active.
 * - v 1.6 2013/08/22
 * 		+ Merge for import zip files and iassign files.
 * - v 1.5 2013/08/21
 * 		+ Insert functions for import files.
 * - v 1.4 2013/07/23
 * 		+ Fix filter iLM from select of iLMs only iassign mode and not block mode..
 * 		+ Fix change form message with function 'disable_form_change_checker()'.
 * - v 1.3 2013/07/12
 * 		+ Fix filter iLM from select of iLMs.
 * 		+ Fix error messages of 'setType' in debug mode for hidden fields.
 * - v 1.1 2013/06/26
 * 		+ Remove the button of choose iLM (ID of iLM send of parent page).
 *
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.7 2013/10/24
 * @package mod_iassign_ilm
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 *
 * <b>License</b>
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
  die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
  }

require_once($CFG->libdir . '/formslib.php');


/// This class create form based on moodleform.
//  @calledby ./mod/iassign/ilm_manager.php
//  @see moodleform
class ilm_manager_form extends moodleform {

  function definition () {
    global $CFG, $USER, $DB, $PAGE, $OUTPUT;
    $id = optional_param('id', 0, PARAM_INT); // Course Module ID
    $from = optional_param('from', 0, PARAM_TEXT);
    $ilmid = optional_param('ilmid', NULL, PARAM_INT);

    $mform = & $this->_form;
    //TODO Javascript code: verify alternatives...
    $code_javascript_open_online_ilm = "
  <script type='text/javascript'>
  //<![CDATA[
    function open_online_ilm(ilmid) {
      if (ilmid == null || ilmid == '')
	ilmid = document.getElementById('id_iassign_ilmid').value;
	dirid = document.forms[0].dirid.value;
	window.location='$CFG->wwwroot/mod/iassign/ilm_manager.php?from=" . $from . "&id=" . $id . "&action=new&ilmid='+ilmid+'&dirid='+dirid;
        }
  //]]>
  </script>\n";

    /// Adding new iLM
    $mform->addElement('header', 'new_ilm', get_string('new_ilm', 'iassign'));

    $extension_text = '';

    if ($from == 'block' || $from == 'tinymce') {
      // Search iMA registered in the database
      $ilms = $DB->get_records('iassign_ilm', array('enable' => 1));
      $applets = array();
      foreach ($ilms as $ilm)
        $applets[$ilm->id] = $ilm->name . ' ' . $ilm->version;

      $ia_array = array();

      $ia_array[] = & $mform->createElement('select', 'iassign_ilmid', get_string('choose_iLM', 'iassign'), $applets);
      $ia_array[] = & $mform->createElement('button', 'online_new_iassign', get_string('open_editor_ilm', 'iassign', ''), array('onClick' => 'open_online_ilm()'));
      $mform->addGroup($ia_array, 'select_iassign', get_string('choose_iLM', 'iassign'), array(' '), false);
      }
    else if ($from == 'iassign') {
      $iassign_ilm = $DB->get_record("iassign_ilm", array("id" => $ilmid));
      if ($iassign_ilm) {
        $extension_text = "(" . $iassign_ilm->extension . ") ";
        $ia_array[] = & $mform->createElement('button', 'online_new_iassign', get_string('open_editor_ilm', 'iassign', ' ' . $iassign_ilm->name), array('onClick' => 'open_online_ilm(' . $ilmid . ')'));
        $mform->addGroup($ia_array, 'select_iassign', '', array(' '), false);
        }
      }

    /// Upload file ilm
    $mform->addElement('html', $code_javascript_open_online_ilm);
    $mform->addElement('header', 'upload_ilm_file', get_string('upload_ilm_file', 'iassign'));
    $options = array('subdirs' => 0, 'maxbytes' => $CFG->userquota, 'maxfiles' => -1, 'accepted_types' => array('*'));
    $mform->addElement('filepicker', 'file', get_string('import_file', 'iassign', $extension_text), null, $options);
    $mform->addElement('submit', 'submitbutton', get_string('add_file', 'iassign'));

    // Hidden fields
    $mform->addElement('hidden', 'id');
    $mform->setType('id', PARAM_TEXT);
    $mform->addElement('hidden', 'from');
    $mform->setType('from', PARAM_TEXT);
    $mform->addElement('hidden', 'ilmid');
    $mform->setType('ilmid', PARAM_TEXT);
    $mform->addElement('hidden', 'dirid');
    $mform->setType('dirid', PARAM_TEXT);

    $mform->disable_form_change_checker();
    }

  } // class ilm_manager_form extends moodleform
