<?php

/**
 * Form to add and edit interactive Learning Module (iLM).
 * The fields are initially filled at locallib.php:add_edit_copy_ilm($ilm_id, $action)
 * The final processing is performed by 'locallib.php', that is called bye './settings_ilm.php' (with ilm_settings::copy_new_version_ilm($formdata))
 * then by 'ilm_handlers/(html5 or java).php' with copy_new_version_ilm($param, $files_extract)'
 * 
 * Release Notes:
 * - v 1.6.3 2020/04/28
 *   + Fixed detail: undefinde variable 'if ($filejars!='')' - it was '$filejar'
 * - v 1.6.2 2020/01/20
 *   + Avoid presence of ' and " in DB to close string to put JavaScript (avoid error)
 * - v 1.6.1 2017/12/02
 *   + New help button, improvements on some helps, new code comments and indentation.
 * - v 1.6 2013/12/12
 *   + Insert support of import iLM from zip packages.
 * - v 1.5 2013/10/31
 *   + Insert support of import iLM from zip packages.
 * - v 1.4 2013/10/24
 *   + Fix required upload an file for new iLM.
 * - v 1.3 2013/07/12
 *   + Fix error messages of 'setType' in debug mode for hidden fields.
 *   + Form now accept actions: add, edit, copy (new version from an iLM), and new version (empty new version).
 * 
 * To use help button. For instance, to a field named 'file_class' use
 *   $mform->addHelpButton('file_class', 'file_class_form_help', 'iassign');
 * it means that the first parameter is used as 'title' (mouseover) and the second when user "clickover"
 * In lang, it is necessary
 *   file_class_form_help = 'to the title - when mouse over'
 *   file_class_form_help_help = 'to the complete explanation, when the user click over the help signal'
 * 'auto_evaluate_help'] = 'If the iLM has automatic evaluation select Yes to use an activity with it.'; // 'What is automatic evaluation
 * 
 * @see ./locallib.php : 'add_edit_copy_ilm($ilm_id,$action)' load this script and provides data to it (defines initial values to the form fields)
 * @see ./settings_ilm.php : load and process this form, it uses data under the name $param
 * @see ./ilm_handlers/html5.php : save_ilm_by_xml($application_xml, $files_extract): return null;
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.6 2013/12/12
 * @package mod_iassign_settings
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.
if (!defined('MOODLE_INTERNAL')) {
  die('Direct access to this script is forbidden.'); // By security reasons, this must be included in all Moodle pages!
  }

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/iassign/lib.php');


/// This class create form based moodleform.
//  @see moodleform
class mod_ilm_form extends moodleform {

  /// Add elements to form
  function definition () {
    global $CFG, $COURSE, $USER, $DB;
    global $description; // defined in 'settings_ilm.php'

//    $mform = & $this->_form;
$mform = $this->_form; //leo

    if ($CFG->action_ilm != 'import') {

      if ($CFG->action_ilm == 'add') {
        // $params = array('parent' => '0');
        // $tmps = $DB->get_records_sql("SELECT s.id, s.name, s.extension, s.file_jar
        // FROM {iassign_ilm} s WHERE s.parent = :parent", $params); // " - jed/emacs
        $tmps = $DB->get_records('iassign_ilm', array('parent' => 0));
        }
      else {
        // $params = array('id' => $CFG->ilm_id);
        // $tmps = $DB->get_records_sql("SELECT s.id, s.name, s.extension, s.file_jar
        // FROM {iassign_ilm} s WHERE s.id != :id AND s.parent = 0", $params); // " - jed/emacs
        $tmps = $DB->get_records('iassign_ilm', array('id' => $CFG->ilm_id));
        }
      $extensions = "";
      $names = "";
      $versions = "";
      $filejars = "";
      foreach ($tmps as $tmp) {
        $exts = explode(",", $tmp->extension);
        foreach ($exts as $ext) {
          $extensions .= "'" . $ext . "',";
          }
        $names .= "'" . $tmp->name . "',";

        $filejars .= "'" . $tmp->file_jar . "',";
        }

      if (!$tmps)
        $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $CFG->ilm_id));
      else
        $iassign_ilm = $tmps; // it already has these data

      if (!$iassign_ilm) {
        $iassign_ilm = new stdClass();
        $iassign_ilm->parent = 0;
        }

      //TODO JavaScript code, verify alternatives. 
      $code_javascript = "\n  <script type='text/javascript'>
    //<![CDATA[
    document.getElementById('id_data_file_html').style.display = 'none'
    document.getElementById('id_data_file_jar').classList.remove('collapsed');
    document.getElementById('id_data_ilm').classList.remove('collapsed');

    function search_name (name) {
      var i;
      var names = new Array(";
      if ($names != '')
        $code_javascript .= addslashes($names) . "');\n"; // if the name has ' it implies close JavaScript string => error!
      $code_javascript .= "
      for (i=0;i<names.length;i++) {
        if (names[i].toLowerCase()==name.toLowerCase()) {
          document.forms['mform1'].name.value='';
          confirm('" . get_string('invalid_name_ilm', 'iassign') . " '+names[i]+'!');
          }
        }";
      $code_javascript .= "      }


    function search_extension(extensions) {
      var i;
      var ext_inserteds = extensions.split(',');
      var ext_exists = new Array(";
      $code_javascript .= $extensions . "'');" . chr(13);
      $code_javascript .= "
      for (k=0;k<ext_inserteds.length;k++) {
        for (i=0;i<ext_exists.length;i++) {
          if (ext_exists[i].toLowerCase()==ext_inserteds[k].toLowerCase()) {
            document.forms['mform1'].extension.value='';
            confirm('" . get_string('invalid_extension_ilm', 'iassign') . " '+ext_exists[i]+'!');
            }
          }
        }\n";
      $code_javascript .= "      }
 
    function search_filejar (fullfilejar) {
      var i;
      var tmp = fullfilejar.split('/');
      var filejar = tmp[tmp.length-1];

      var filejars = new Array("; // close for JavaScript code

      if ($filejars!='')
        $code_javascript .= addslashes($filejars) . "');" . chr(13); // if the name has ' it implies close JavaScript string => error!

      $code_javascript .= "
      for (i=0;i<filejars.length;i++) {
        if (filejars[i].toLowerCase()==filejar.toLowerCase()) {
          document.forms['mform1'].file_jar[0].value='';
          confirm('" . get_string('invalid_filejar_ilm', 'iassign') . " '+filejars[i]+'!');
          }
        }\n";

      $code_javascript .= "      }

    function change_language (lang) {
      if (document.forms['mform1'].description_lang.value != '') {
        descriptions = eval('(' + document.forms['mform1'].description_lang.value + ')');
        descriptions[document.forms['mform1'].set_lang.value] = tinyMCE.activeEditor.getContent();
        document.forms['mform1'].description_lang.value = JSON.stringify(descriptions);
        if (descriptions[lang] != undefined)
          tinyMCE.activeEditor.setContent(descriptions[lang]);
        else
          tinyMCE.activeEditor.setContent('');
        }
      else {
        document.forms['mform1'].description_lang.value = '{ \"' + document.forms['mform1'].set_lang.value + '\" : \"' + tinyMCE.activeEditor.getContent() + '\" }';
        tinyMCE.activeEditor.setContent('');
        }
      document.forms['mform1'].set_lang.value = lang;
      }

  //]]>
  </script>\n";

      //-------------------------------------------------------------------------------
      /// Adding the "data_ilm" fieldset, where all the common settings are showed

      if ($CFG->action_ilm != 'add') {

        // $mform->addElement('header', 'type_ilm', get_string('type_ilm', 'iassign'));
        // // Adding the select option for HTML5 or Java
        // $options = array('Java' => 'Java', 'HTML5' => 'HTML5');
        // $mform->addElement('select', 'ilm_type', get_string('ilm_type', 'iassign'), $options);
        // $mform->setDefault('ilm_type', PARAM_TEXT);
        // $mform->addHelpButton('ilm_type', 'ilm_type', 'iassign');
        // $mform->addElement('header', 'data_ilm', get_string('data_ilm', 'iassign'));

        // Adding the standard "name" field
        if ($CFG->action_ilm != 'add') {
          $mform->addElement('static', 'name_ilm', get_string('name_ilm', 'iassign'));
          $mform->addElement('hidden', 'name');
          $mform->setType('name', PARAM_TEXT);
          } 
        else {
          $mform->addElement('text', 'name', get_string('name_ilm', 'iassign'), array('size' => '55', 'onchange' => 'search_name(this.value);'));
          $mform->setType('name', PARAM_TEXT);
          }

        // Adding the standard "version" field
        $mform->addElement('text', 'version', get_string('version_ilm', 'iassign'), array('size' => '55'));
        $mform->setType('version', PARAM_TEXT);
        $mform->addHelpButton('version', 'version_ilm', 'iassign');
 
        // Adding the type of iLM
        $mform->addElement('static', 'ilm_type', get_string('type_ilm', 'iassign'));
        $mform->addElement('hidden', 'type');
        $mform->setType('type', PARAM_TEXT);

        // Adding the standard "url" field
        $mform->addElement('text', 'url', get_string('url_ilm', 'iassign'), array('size' => '55'));
        $mform->setType('url', PARAM_TEXT);

        $mform->addElement('select', 'lang', get_string('language_label', 'iassign'), get_string_manager()->get_list_of_translations(), array('onChange' => 'change_language(this.value);'));
        $mform->setDefault('lang', current_language());

        //D Came from 'settings_ilm.php: if ($action == 'edit') 
	//D With 'editor' does not work!
        //D $param = $description; // ilm_settings::add_edit_copy_ilm($ilm_id, $action);
        //D $param = $mform->get_data(); - erro, function not defined
        //D echo "settings_form.php: param="; print_r($param); echo "<br/>";
        //D echo "description=" . $description . "<br/>";
	  
        // Adding the standard "description" field
        //moodle2 $mform->addElement('htmleditor', 'description', get_string('description', 'iassign'));
        //TODO $mform->addElement('editor', 'description', get_string('description', 'iassign'), $description); // moodle3 => 'editor', but not working
        $mform->addElement('text', 'description', get_string('description', 'iassign'), array('size' => '80'));//, $description, 200); //moodle3
        $mform->setType('description', PARAM_RAW);

        // Adding the "data_file_jar" fieldset, where all the common settings are showed ---
        //REVER texto 'Pacote do novo iMA (Java) a ser inserido (JAR)'
        $mform->addElement('header', 'data_file_jar', get_string('data_file_jar', 'iassign'));

        $mform->addElement('static', 'file_jar_static', get_string('fields_info', 'iassign')); // non editable text

        // Fields: file_jar  file_class
        $mform->addElement('text', 'file_jar', get_string('file_jar_path', 'iassign'), array('size' => '55')); // editable text
        $mform->setType('file_jar', PARAM_TEXT);
	  
        $mform->addElement('text', 'file_class', get_string('file_class', 'iassign')); // editable text
        $mform->setType('file_class', PARAM_TEXT);	  

        //TODO : acho que vale a pena incluir o campo 'file_jar'...
        // $mform->addElement('static', 'file_jar', get_string('file_jar', 'iassign'));
        // $mform->addElement('hidden', 'file_jar'); - already bellow! line 288/320
        // $mform->setType('file_jar', PARAM_TEXT);
        //TD // Adding the standard "file_jar" field
        //TD $mform->addElement('text', 'file_jar', get_string('file_jar', 'iassign'), array('size' => '55'));
        //TD $mform->setType('file_jar', PARAM_TEXT);
        //TD $mform->addHelpButton('file_jar', 'file_jar_form_help', 'iassign'); // first => field name; second => text to be presented

        // Adding the standard "extension" field
        $mform->addElement('text', 'extension', get_string('extension', 'iassign'), array('size' => '30', 'onchange' => 'search_extension(this.value);'));
        $mform->setType('extension', PARAM_TEXT);
        //$mform->addRule('extension', get_string('required', 'iassign'), 'required');
        $mform->addHelpButton('extension', 'extension', 'iassign');

        // Adding the standard "width" field
        $mform->addElement('text', 'width', get_string('width', 'iassign'), array('size' => '10'));
        $mform->setType('width', PARAM_TEXT);
        //$mform->addRule('width', get_string('required', 'iassign'), 'required');

        // Adding the standard "height" field
        $mform->addElement('text', 'height', get_string('height', 'iassign'), array('size' => '10'));
        $mform->setType('height', PARAM_TEXT);
        //$mform->addRule('height', get_string('required', 'iassign'), 'required');

        // Adding the standard "evaluate" field
        $mform->addElement('selectyesno', 'evaluate', get_string('auto_evaluate', 'iassign'));
        $mform->setDefault('evaluate', 1);
        //$mform->addRule('evaluate', get_string('required', 'iassign'), 'required');
        $mform->addHelpButton('evaluate', 'auto_evaluate', 'iassign');

        // Adding the standard "submissionbehavior" field
        $mform->addElement('text', 'submissionbehavior', get_string('auto_evaluate', 'iassign'));
        $mform->setType('submissionbehavior', PARAM_INT);

        // Adding the "data_file_jar" fieldset, where all the common settings are showed
        $mform->addElement('header', 'data_file_html', get_string('data_file_html', 'iassign'));

        // Adding static text
        $mform->addElement('static', 'data_file_html_static', get_string('data_file_html_static', 'iassign'));
        } // if ($CFG->action_ilm != 'add')

      // // Upload file ilm
      // $mform->addElement('header', 'upload_jar', get_string('upload_jar', 'iassign'));
      // //$mform->setExpanded('upload_jar');
      // $options = array('subdirs' => 0, 'maxbytes' => $CFG->userquota, 'maxfiles' => -1, 'accepted_types' => '*');
      // $mform->addElement('filemanager', 'file', null, null, $options);

      $mform->addElement('header', 'upload_jar', get_string('upload_jar', 'iassign'));

      $options = array('subdirs' => 0, 'maxbytes' => $CFG->userquota, 'maxfiles' => 1, 'accepted_types' => array('*'));
      $mform->addElement('filepicker', 'file', null, null, $options);
      $mform->addRule('file', get_string('required', 'iassign'), 'required');

      if ($CFG->action_ilm == 'add' || $CFG->action_ilm == 'copy' || $CFG->action_ilm == 'new_version')
        $mform->addRule('file', get_string('required', 'iassign'), 'required');

      /// Adding the standard "hidden" field
      if ($CFG->action_ilm == 'edit') {
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        }
      $mform->addElement('hidden', 'set_lang');
      $mform->setType('set_lang', PARAM_TEXT);
      $mform->setDefault('set_lang', current_language());
      $mform->addElement('hidden', 'description_lang');
      $mform->setType('description_lang', PARAM_TEXT);

      // $mform->addElement('hidden', 'file_jar'); //Remove inserted above to allow to edit this field
      // $mform->setType('file_jar', PARAM_TEXT);  //Remove idem
      $mform->addElement('hidden', 'author');
      $mform->setType('author', PARAM_TEXT);
      $mform->addElement('hidden', 'action');

      $mform->setType('action', PARAM_TEXT); //DEBUG: is it necessary?
      $mform->addElement('hidden', 'timecreated');

      $mform->setType('timecreated', PARAM_TEXT);
      $mform->addElement('hidden', 'timemodified');
      $mform->setType('timemodified', PARAM_TEXT);
      $mform->addElement('hidden', 'parent');
      $mform->setType('parent', PARAM_INT);
      $mform->addElement('hidden', 'enable');
      $mform->setType('enable', PARAM_INT);

      $mform->addElement('html', $code_javascript);
      } // if ($CFG->action_ilm != 'import')
    else {
      $mform->addElement('header', 'upload_ilm', get_string('upload_ilm', 'iassign'));
      //$mform->setExpanded('upload_ilm');
      $options = array('subdirs' => 0, 'maxbytes' => $CFG->userquota, 'maxfiles' => 1, 'accepted_types' => array('*'));
      $mform->addElement('filepicker', 'file', null, null, $options);
      $mform->addRule('file', get_string('required', 'iassign'), 'required');

      $mform->addElement('hidden', 'action'); //DEBUG: is it necessary?
      $mform->setType('action', PARAM_TEXT);
      }

    $this->add_action_buttons();
    } // function definition()

  } // class mod_ilm_form extends moodleform