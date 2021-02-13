<?php

/**
 * 
 * This PHP script is invoked by Moodle every time it enters at Admin section (./admin/search.php).
 * It provides a link to the iAssign general configurations area ('pluginname').
 * 
 * Release Notes:
 * - v 1.9.3 2020/08/03
 *   + Debug security added into "else if ($action == 'config')": if (!isset($ilm) || !$ilm) return;* - v 1.9.2 2020/02/15
 *   + Fixed 2 problems inside 'if ($ilm_parent)': 1. Error: '+' -> '.'; 2. Changed 'if (is_object($ilm_parent)) if (is_object($ilm_parent->description))'
 *     to '$current_language = current_language(); $description_obj = iassign_language::get_description_lang(...);... $str_description = $description_obj;'
 * - v 1.9.1 2017/12/02
 *   + Indentation and comments improvements
 * - v 1.9 2013/12/12
 *   + Allow use the language in iLM description.
 * - v 1.8 2013/10/31
 *   + Insert support of export iLM in zip packages.
 * - v 1.7 2013/10/24
 *   + Insert support of iLM upgrade.
 * - v 1.6 2013/09/11
 *   + Insert support of iLM params.
 * - v 1.5 2013/08/01
 *   + Fix bug for block change visibility in iLM wiht statement.
 * - v 1.4 2013/07/12
 *   + Insert action config for accept versions of iLM.
 *   + Insert new informations in iLMs table: created date, modified date, author, version, modified date of JAR.
 *   + Now view separate only iLMs for filter versions.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.9.1 2017/12/02
 * @package mod_iassign_settings
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.
defined('MOODLE_INTERNAL') || die();

global $OUTPUT, $CFG, $DB;
require_once($CFG->dirroot . '/mod/iassign/lib.php');
require_once($CFG->dirroot . '/mod/iassign/locallib.php');

$action = optional_param('action', 'view', PARAM_TEXT);
$ilm_id = optional_param('ilm_id', 0, PARAM_INT);
$ilm_param_id = optional_param('ilm_param_id', 0, PARAM_INT);
$ilm_id_parent = optional_param('ilm_id_parent', 0, PARAM_INT);
$status = optional_param('status', 0, PARAM_INT);

if (!$action && !$ilm_id) {
  return; // nothing to be done (it is the Admin entering at the initial administrative section)
  }

if ($action == 'visible') {
  ilm_settings::visible_ilm($ilm_id, $status);
  $action = 'config';
  $ilm_id = $ilm_id_parent;
  }
else
if ($action == 'visible_param') {
  ilm_settings::visible_param($ilm_param_id, $status);
  $action = 'config';
  }
else
if ($action == 'export') {
  ilm_settings::export_ilm($ilm_id);
  $action = 'config';
  }

$str = '';
if (!file_exists($CFG->dirroot . '/filter/iassign_filter/version.php')) {
  $str .= $OUTPUT->box_start();
  $str .= '<center>' . $OUTPUT->error_text(get_string('error_check_iassign_filter', 'iassign')) . '</center>';
  $str .= $OUTPUT->box_end();
  }
if (!file_exists($CFG->dirroot . '/blocks/iassign_block/version.php')) {
  $str .= $OUTPUT->box_start();
  $str .= '<center>' . $OUTPUT->error_text(get_string('error_check_iassign_block', 'iassign')) . '</center>';
  $str .= $OUTPUT->box_end();
  }
if (!file_exists($CFG->dirroot . '/lib/editor/tinymce/plugins/iassign/version.php')) {
  $str .= $OUTPUT->box_start();
  $str .= '<center>' . $OUTPUT->error_text(get_string('error_check_iassign_tinymce', 'iassign')) . '</center>';
  $str .= $OUTPUT->box_end();
  }

if ($action == 'view') {
  $url_add = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'add'));
  $action_add = new popup_action('click', $url_add, 'popup', array('width' => 900, 'height' => 650));
  $link_add = $OUTPUT->action_link($url_add, get_string('add_ilm_iassign', 'iassign'), $action_add) . $OUTPUT->help_icon('add_ilm_iassign', 'iassign');

  $url_import = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'import'));
  $action_import = new popup_action('click', $url_import, 'popup', array('width' => 900, 'height' => 650));
  $link_import = $OUTPUT->action_link($url_import, get_string('import_ilm', 'iassign'), $action_import) . $OUTPUT->help_icon('import_ilm', 'iassign');

  // First list all iLM from type HTML
  // $iassign_ilms = $DB->get_records('iassign_ilm', array('parent' => 0)); // id, version, parent, name
  $iassign_ilms = $DB->get_records_sql("SELECT id, description, name, parent, url, version FROM {iassign_ilm} WHERE parent = 0 ORDER BY type");

  $str .= '<table id="outlinetable" class="generaltable boxaligncenter" cellpadding="5" width="100%">' . chr(13);
  $str .= '<tr><td colspan=2 align=left>' . $link_add . '</td>';
  $str .= '<td colspan=2 align=right>' . $link_import . '</td></tr>';

  if ($iassign_ilms) {

    foreach ($iassign_ilms as $ilm) {
      $url_config = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign', 'action' => 'config', 'ilm_id' => $ilm->id));
      $link_config = $OUTPUT->action_link($url_config, iassign_icons::insert('config_ilm'));

      $ilm_count = 1;
      $ilm_version = 0;
      //R $iassign_ilm_list = $DB->get_records('iassign_ilm', array('parent' => $ilm->id)); //*******************************************
      $iassign_ilm_list = $DB->get_records_sql("SELECT id, version FROM {iassign_ilm} WHERE parent = " . $ilm->id );

      if ($iassign_ilm_list) {
        foreach ($iassign_ilm_list as $ilm_parent) {
          $ilm_count++;
          if (floatval(preg_replace('/[^0-9]+/', '', $ilm_parent->version)) > $ilm_version)
            $ilm_version = floatval(preg_replace('/[^0-9]+/', '', $ilm_parent->version));
          }
        }

      $str_sql = "SELECT COUNT(id) FROM {iassign_statement} WHERE iassign_ilmid =" . $ilm->id;
      $iassign_count = $DB->count_records_sql($str_sql, null);

      //R $iassign_ilm_parent = $DB->get_records('iassign_ilm', array('parent' => $ilm->id)); //*******************************************
      $iassign_ilm_parent = $DB->get_records_sql("SELECT id FROM {iassign_ilm} WHERE parent = " . $ilm->id);
      foreach ($iassign_ilm_parent as $ilm_parent) {
        //R $iassign_statement = $DB->get_records('iassign_statement', array('iassign_ilmid' => $ilm_parent->id)); //*******************************************
        //R if ($iassign_statement) { // $iassign_count += count($iassign_statement);
          $str_sql_parents = "SELECT COUNT(id) FROM {iassign_statement} WHERE iassign_ilmid =" . $ilm_parent->id;
          $iassign_count += $DB->count_records_sql($str_sql_parents, null);
        //R   }
        }

      $link_upgrade = "";
      $upgrade_file = $ilm->url . 'ilm-upgrade_' . strtolower($ilm->name) . '.xml';
      if ($upgrade_xml = @simplexml_load_file($upgrade_file, null, LIBXML_NOCDATA)) {
        $upgrade_version = floatval(preg_replace('/[^0-9]+/', '', $upgrade_xml->version));
        if ($ilm_version < $upgrade_version) {
          $url_upgrade = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign', 'action' => 'confirm_upgrade', 'ilm_id' => $ilm->id));
          $link_upgrade = $OUTPUT->action_link($url_upgrade, iassign_icons::insert('upgrade_ilm', $upgrade_xml->version));
          }
        }

      // Get field '*_iassign_ilm.description' that has the JSON text format: "{"pt_br":"...","en":"..." ...}"
      // locallib.php: class iassign_language : get text from JSON {"en":"...","pt":"..."}
      $current_language = current_language();
      $description_obj = iassign_language::get_description_lang($current_language, $ilm->description); // gets a pure text description
      //D echo "----------<br/>";print_r($description_obj); echo "<br/>";
      //D Warning: 'get_description_lang(...)' avoid the error "Debug info: Object of class stdClass could not be converted to string"
      if (is_object($description_obj)) // just in case (if some changes in Moodle/PHP 'json_decode(...)' function...
        $str_description = $description_obj->text;
      else
        $str_description = $description_obj;

      $str .= '<tr>';
      $str .= '<td class="header c1" width=75% title="fields: name, description"><strong>' . $ilm->name . ' (' . $ilm->version . ')<br/>' .
          $str_description . '</strong></td>' . chr(13);

      $str .= '<td class="header c1" width=10% ><strong>' . get_string('versions_ilm', 'iassign') . ':</strong>&nbsp;' . $ilm_count . '</td>' . chr(13);
      $str .= '<td class="header c1" width=10% ><strong>' . get_string('activities', 'iassign') . ':</strong>&nbsp;' . $iassign_count . '</td>' . chr(13);
      $str .= '<td class="header c1" width=5% align=center valign=bottom>' . $link_upgrade . '&nbsp;&nbsp;' . $link_config . '</td>' . chr(13);
      $str .= '</tr>';
      } // foreach ($iassign_ilms as $ilm)
    } // if ($iassign_ilms)
  $str .= '</table>';

  $settings->add(new admin_setting_heading('iassign', get_string('config_ilm', 'iassign') . $OUTPUT->help_icon('modulename', 'iassign'), $str));
  } // if ($action == 'view')
else if ($action == 'confirm_upgrade') {

  $ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

  $upgrade_file = $ilm->url . 'ilm-upgrade_' . strtolower($ilm->name) . '.xml';

  $upgrade_xml = @simplexml_load_file($upgrade_file, null, LIBXML_NOCDATA);

  $lang = current_language();

  if (array_key_exists($lang, $upgrade_xml->description))
    $description = $upgrade_xml->description->$lang;
  else
    $description = $upgrade_xml->description->en;


  $str .= '<table id="outlinetable" class="generaltable boxaligncenter" width="100%">' . chr(13);
  $str .= '<tr><td>' . $description;

  $str .= '</td></tr></table>';

  $optionsno = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign', 'action' => 'view'));
  $optionsyes = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'upgrade', 'ilm_id' => $ilm_id));

  $str .= "<center>" . $OUTPUT->heading(get_string('confirm_upgrade_ilm', 'iassign'), 3, 'helptitle', 'uniqueid');

  $url_yes = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'upgrade', 'ilm_id' => $ilm_id));
  $link_yes = $OUTPUT->action_link($url_yes, "<font color='green'><b>" . get_string('yes', 'iassign') . "</b></font>");

  $url_no = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign', 'action' => 'view'));
  $link_no = $OUTPUT->action_link($url_no, "<b>" . get_string('no', 'iassign') . "</b>");


  $str .= $link_no . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $link_yes . "</center>";


  $settings->add(new admin_setting_heading('iassign', get_string('upgrade_ilm_title', 'iassign'), $str));
  } // else if ($action == 'confirm_upgrade')

else if ($action == 'config') { // Administration > plugins > iAssign : after select the iLM reaches this point
  //D echo "settings.php: action==config<br/>";

  $url_return = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign'));
  $link_return = $OUTPUT->action_link($url_return, get_string('return', 'iassign'));

  $url_new_version = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'new_version', 'ilm_id' => $ilm_id));
  $action_new_version = new popup_action('click', $url_new_version, 'popup', array('width' => 900, 'height' => 650));
  $link_new_version = $OUTPUT->action_link($url_new_version, get_string('new_version_ilm', 'iassign'), $action_new_version);

  $str .= '<table id="outlinetable" class="generaltable boxaligncenter" cellpadding="5" width="100%">' . chr(13);
  $str .= '<tr><td colspan=2 align=left>' . $link_return . '</td><td colspan=1 align=right>' . $link_new_version . '</td></tr>';

  $ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

  if (!isset($ilm) || !$ilm) return; // debug security...

  $iassign_ilm_parent = $DB->get_records_sql(
    'SELECT s.* FROM {iassign_ilm} s WHERE s.parent = :parent ORDER BY s.version DESC', array('parent' => $ilm->id));
  array_unshift($iassign_ilm_parent, $ilm);

  if ($iassign_ilm_parent) {
    foreach ($iassign_ilm_parent as $ilm_parent) {

      $url_default = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'confirm_default_ilm', 'ilm_id' => $ilm_parent->id, 'ilm_parent' => $ilm->id));
      if (count($iassign_ilm_parent) == 1)
        $link_default = "";
      else if ($ilm_parent->parent == 0) {
        $link_default = iassign_icons::insert('default_ilm');
        $ilm_parent->enable = -1;
        }
      else
        $link_default = $OUTPUT->action_link($url_default, iassign_icons::insert('default_ilm_disabled'));

      //if ($ilm_parent->file_jar != $ilm_parent->id) {
      $url_view = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'view', 'ilm_id' => $ilm_parent->id, 'from' => 'admin'));
      $action_view = new popup_action('click', $url_view, 'iplookup', array('title' => get_string('view_ilm', 'iassign'), 'width' => 1200, 'height' => 650));
      $link_view = $OUTPUT->action_link($url_view, iassign_icons::insert('view_ilm'), $action_view);
      //  } else {
      //$link_view = '';
      //  }
      $url_edit = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'edit', 'ilm_id' => $ilm_parent->id));
      $action_edit = new popup_action('click', $url_edit, 'iplookup', array('title' => get_string('edit_ilm', 'iassign'), 'width' => 900, 'height' => 650));
      $link_edit = $OUTPUT->action_link($url_edit, iassign_icons::insert('edit_ilm'), $action_edit);

      $url_visible = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign', 'action' => 'visible', 'ilm_id' => $ilm_parent->id, 'status' => $ilm_parent->enable, 'ilm_id_parent' => $ilm->id));

      $iassign_statement = $DB->get_records('iassign_statement', array('iassign_ilmid' => $ilm_parent->id));
      $total = 0;
      if ($iassign_statement)
        $total = count($iassign_statement);

      if ($ilm_parent->enable == 0 && $total == 0) {
        $link_visible = $OUTPUT->action_link($url_visible, iassign_icons::insert('show_ilm'));
        }
      elseif ($ilm_parent->enable == 1 && $total == 0) {
        $link_visible = $OUTPUT->action_link($url_visible, iassign_icons::insert('hide_ilm'));
        }
      else {
        $link_visible = '&nbsp;' . iassign_icons::insert('unlock');
        }

      $url_copy = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'copy', 'ilm_id' => $ilm_parent->id));
      $action_copy = new popup_action('click', $url_copy, 'iplookup', array('title' => get_string('copy_ilm', 'iassign'), 'width' => 900, 'height' => 650));
      $link_copy = $OUTPUT->action_link($url_copy, iassign_icons::insert('copy_ilm'), $action_copy);
      
      if ($total == 0 && ($ilm_parent->parent != 0 || count($iassign_ilm_parent) == 1) && !ilm_settings::applet_default($ilm_parent->file_jar)) {
        $url_delete = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'confirm_delete_ilm', 'ilm_id' => $ilm_parent->id, 'ilm_parent' => $ilm->id));
        $link_delete = $OUTPUT->action_link($url_delete, iassign_icons::insert('delete_ilm'));
        }
      else if (ilm_settings::applet_default($ilm_parent->file_jar)) {
        $url_delete = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'confirm_delete_ilm', 'ilm_id' => $ilm_parent->id, 'ilm_parent' => $ilm->id));
        $link_delete = $OUTPUT->action_link($url_delete, iassign_icons::insert('delete_ilm'));
        }
      else
        $link_delete = iassign_icons::insert('delete_ilm_disable');
      
      
      if (strtolower($ilm_parent->type) == 'html5') {
        $url_delete = new moodle_url('/mod/iassign/settings_ilm.php', array('action' => 'confirm_delete_ilm', 'ilm_id' => $ilm_parent->id, 'ilm_parent' => $ilm->id));
        $link_delete = $OUTPUT->action_link($url_delete, iassign_icons::insert('delete_ilm'));
        }

      $url_export = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign', 'action' => 'export', 'ilm_id' => $ilm_parent->id, 'ilm_id_parent' => $ilm->id));
      $link_export = $OUTPUT->action_link($url_export, iassign_icons::insert('export_ilm'));

      $str .= '<tr><td colspan=3>';
      $str .= '<table width="100%">';

      if (!empty($ilm_parent->url))
        $url_ilm = display_url_ilm($ilm_parent->url);
      else
        $url_ilm = $ilm_parent->url;

      if ($ilm_parent->file_jar == $ilm_parent->id) {
        $ilm_parent->file_jar = "";
        }

      $str .= '<tr>' . chr(13);
      $str .= '<td width="50%"><strong>' . get_string('version_ilm', 'iassign') . ':</strong>&nbsp;' . $ilm_parent->version . '</td>' . chr(13);
      $str .= '<td width="50%" align="right" valign=bottom>';
      $str .= $link_default . '&nbsp;&nbsp;';
      $str .= $link_edit . '&nbsp;&nbsp;';
      $str .= $link_copy . '&nbsp;&nbsp;';
      $str .= $link_visible . '&nbsp;&nbsp;';
      $str .= $link_view . '&nbsp;&nbsp;';
      $str .= $link_export . '&nbsp;&nbsp;';
      $str .= $link_delete;

      $str .= '</td>' . chr(13) . '</tr>' . chr(13);

      // ./mod/iassign/locallib.php : function get_description_lang(...)
      //D print_r($ilm_parent->description); echo "<br/>"; exit(); // {"en":"Visual Interactive Programming on the Internet HTML5","pt_br":"Programação visual interativa na Internet"}
      //D ilm_parent->description = {"en":{"text":"iVProgH: interactive Visual ProgrammingA free educational tool of LInE - IME - USP.","format":"1"}, "pt_br":"Programa\u00e7\u00e3o visual interativa na Internet"}

      $str .= '<tr>' . chr(13);
      $current_language = current_language();

      // locallib.php: class iassign_language : get text from JSON {"en":"...","pt":"..."}
      $str_description = "";
      if ($ilm_parent) {
        $current_language = current_language();
        $description_obj = iassign_language::get_description_lang($current_language, $ilm_parent->description); // gets a pure text description
        if (is_object($description_obj)) // just in case (if some changes in Moodle/PHP 'json_decode(...)' function...
          $str_description = $description_obj->text;
        else
          $str_description = $description_obj;
        }
      $str .= '<td width="50%"><strong>' . get_string('description', 'iassign') . ':</strong>&nbsp;' .
              $str_description . '</td>'; // iassign_language::get_description_lang($current_language, $ilm_parent->description)

      $str .= '<td width="50%"><strong>' . get_string('activities', 'iassign') . ':</strong>&nbsp;' . $total . '</td>' . chr(13);
      $str .= '</tr>' . chr(13);

      $langs_str = iassign_language::get_all_lang($ilm_parent->description);
      $str .= '<tr>';
      if ($langs_str != "")
        $str .= '<td><strong>' . get_string('language_label', 'iassign') . ':</strong>&nbsp;' . $langs_str . '</td>';
      $str .= '<td><strong>' . get_string('type_ilm', 'iassign') . ':</strong>&nbsp;' . $ilm_parent->type . '</td>';
      $str .= '</tr>';

      
      if (strtolower($ilm_parent->type) == 'java') {
        $ilm_parent->file_jar = basename($ilm_parent->file_jar);
        }

      $str .= '<tr><td><strong>' . get_string('url_ilm', 'iassign') . ':</strong>&nbsp;<a href="' . $url_ilm . '" target="_blank">' . $url_ilm . '</a></td>';
      
      if ($ilm_parent->enable == 1 || $ilm_parent->enable == -1)
        $enable = get_string('yes', 'iassign');
      else
        $enable = get_string('no', 'iassign');
      $str .= '<td width="50%"><strong>' . get_string('enable', 'iassign') . ':</strong>&nbsp;' . $enable . '</td></tr>';

      $str .= '<tr><td width="50%" title="field: file_jar"><strong>' . get_string('file_jar', 'iassign') . ':</strong>&nbsp;' . $ilm_parent->file_jar . '</td>';
      $str .= '<td width="50%" title="field: file_class"><strong>' . get_string('file_class', 'iassign') . ':</strong>&nbsp;' . $ilm_parent->file_class . '</td></tr>';

      $str .= '<tr><td width="50%" title="field: extension"><strong>' . get_string('extension', 'iassign') . ':</strong>&nbsp;' . $ilm_parent->extension . '</td>';
      $str .= '<td width="50%" title="field: width"><strong>' . get_string('width', 'iassign') . ':</strong>&nbsp;' . $ilm_parent->width;
      $str .= '&nbsp;&nbsp;<strong>' . get_string('height', 'iassign') . ':</strong>&nbsp;' . $ilm_parent->height . '</td></tr>';

      if ($ilm_parent->evaluate == 1)
        $evaluate = get_string('yes', 'iassign');
      else
        $evaluate = get_string('no', 'iassign');

      $str .= '<tr><td width="50%" title="field: evaluate"><strong>' . get_string('evaluate', 'iassign') . ':</strong>&nbsp;' . $evaluate . '</td>';

      if ($ilm_parent->reevaluate == 1)
        $reevaluate = get_string('yes', 'iassign');
      else
        $reevaluate = get_string('no', 'iassign');
      $str .= '<td width="50%"><strong>' . get_string('auto_evaluate_name_config', 'iassign') . ':</strong>&nbsp;' . $reevaluate . '</td></tr>';
      
      $str .= '<tr><td><strong>' . get_string('editing_behavior_view', 'iassign') . ':</strong> ';

      if ($ilm_parent->editingbehavior == 0) {
        $str .=  get_string('editing_behavior_0', 'iassign');
      } elseif ($ilm_parent->editingbehavior == 1) {
        $str .=  get_string('editing_behavior_1', 'iassign');
      }
      $str .= ' </td><td><strong>' . get_string('submissionbehavior_view', 'iassign') . ':</strong> ';

      if ($ilm_parent->submissionbehavior == 0) {
        $str .= get_string('submission_behavior_0', 'iassign');
      } elseif ($ilm_parent->submissionbehavior == 1) {
        $str .= get_string('submission_behavior_1', 'iassign');
      }
      $str .= '</td></tr>';

      $str .= '<tr>' . chr(13);
      $str .= '<td width="50%"><strong>' . get_string('file_created', 'iassign') . ':</strong>&nbsp;' . userdate($ilm_parent->timecreated) . '</td>';
      $str .= '<td width="50%"><strong>' . get_string('file_modified', 'iassign') . ':</strong>&nbsp;' . userdate($ilm_parent->timemodified) . '</td>' . chr(13);
      $str .= '</tr>' . chr(13);


      $user_ilm = $DB->get_record('user', array('id' => $ilm_parent->author));
      if ($user_ilm) {
        $str .= '<tr>' . chr(13);
        $str .= '<td colspan=2><strong>' . get_string('author', 'iassign') . ':</strong>&nbsp;' . $user_ilm->firstname . '&nbsp;' . $user_ilm->lastname . '</td>';
        $str .= '</tr>' . chr(13);
        }
        
      $str .= '<tr>' . chr(13);
      $str .= '<td colspan=2><center>';

      $url_add_param = new moodle_url('/mod/iassign/settings_params.php', array('action' => 'add', 'ilm_id' => $ilm_id));
      $action_add_param = new popup_action('click', $url_add_param, 'popup', array('width' => 900, 'height' => 650));
      $link_add_param = $OUTPUT->action_link($url_add_param, iassign_icons::insert('add_param'), $action_add_param);

      $str .= '<table width="100%">' . chr(13);
      $str .= '<tr>' . chr(13);
      $str .= '<th colspan=3><center><strong>' . get_string('config_param', 'iassign') . $OUTPUT->help_icon('config_param', 'iassign') . '</strong></center></th>';
      $str .= '<th>' . $link_add_param . '</th>';
      $str .= '</tr>' . chr(13);
      $str .= '<tr>' . chr(13);
      $str .= '<td><strong>' . get_string('config_param_name', 'iassign') . '</strong></td>';
      $str .= '<td><strong>' . get_string('config_param_value', 'iassign') . '</strong></td>';
      $str .= '<td><strong>' . get_string('config_param_description', 'iassign') . '</strong></td>';
      $str .= '<td width="10%"><center><strong>' . get_string('config_param_actions', 'iassign') . '</strong></center></td>';
      $str .= '</tr>' . chr(13);

      $iassign_ilm_config = $DB->get_records('iassign_ilm_config', array('iassign_ilmid' => $ilm_parent->id));
      foreach ($iassign_ilm_config as $ilm_config) {

        $url_edit_param = new moodle_url('/mod/iassign/settings_params.php', array('action' => 'edit', 'ilm_param_id' => $ilm_config->id));
        $action_edit_param = new popup_action('click', $url_edit_param, 'iplookup', array('title' => get_string('edit_param', 'iassign'), 'width' => 900, 'height' => 650));
        $link_edit_param = $OUTPUT->action_link($url_edit_param, iassign_icons::insert('edit_param'), $action_edit_param);

        $url_visible_param = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign', 'action' => 'visible_param', 'ilm_id' => $ilm_parent->id, 'status' => $ilm_config->visible, 'ilm_param_id' => $ilm_config->id));
        if ($ilm_config->visible == 0) {
          $link_visible_param = $OUTPUT->action_link($url_visible_param, iassign_icons::insert('show_param'));
          }
        elseif ($ilm_config->visible == 1) {
          $link_visible_param = $OUTPUT->action_link($url_visible_param, iassign_icons::insert('hide_param'));
          }

        $url_copy_param = new moodle_url('/mod/iassign/settings_params.php', array('action' => 'copy', 'ilm_param_id' => $ilm_config->id));
        $action_copy_param = new popup_action('click', $url_copy_param, 'iplookup', array('title' => get_string('copy_param', 'iassign'), 'width' => 900, 'height' => 650));
        $link_copy_param = $OUTPUT->action_link($url_copy_param, iassign_icons::insert('copy_param'), $action_copy_param);

        $url_delete_param = new moodle_url('/mod/iassign/settings_params.php', array('action' => 'delete', 'ilm_param_id' => $ilm_config->id, 'ilm_id' => $ilm_parent->id));
        $link_delete_param = $OUTPUT->action_link($url_delete_param, iassign_icons::insert('delete_param'));

        $str .= '<tr>' . chr(13);
        $str .= '<td>' . $ilm_config->param_name . '</td>';
        $str .= '<td>' . $ilm_config->param_value . '</td>';
        $str .= '<td width="50%">' . $ilm_config->description . '</td>';
        $str .= '<td width="10%"><center>';
        $str .= $link_edit_param . '&nbsp;&nbsp;';
        $str .= $link_copy_param . '&nbsp;&nbsp;';
        $str .= $link_visible_param . '&nbsp;&nbsp;';
        $str .= $link_delete_param;
        $str .= '</center></td>';
        $str .= '</tr>' . chr(13);
        }

      $str .= '</table>' . chr(13);

      $str .= '</center></td>';
      $str .= '</tr>' . chr(13);

      $str .= '</table>';
      $str .= '</td></tr>';
      $str .= '</tr><td colspan="3"></td></tr>';
      }
    }
  $str .= '</table>';

  $settings->add(new admin_setting_heading('iassign', $ilm->name . '&nbsp;', $str));
  }