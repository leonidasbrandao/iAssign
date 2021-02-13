<?php

/**
 * iLM manager
 * 
 * Release Notes
 * - v 2.7.1 2020/08/03
 *   + added parameter 'iLM_PARAM_Authoring=true&' to indicate is teacher accessing (authoring process)
 * - v 2.7 2017/03/10
 *   + added the first parameter in call 'locallib.php : view_files_ilm($iassign_ilm, $iassign_ilm->extension)'
 *     locallib.php : function view_files_ilm($iassign_ilm, $extension) 6184/6732
 *     $link_filter = "&nbsp;&nbsp;<a href='#' onclick='preview_ilm(" . $fileid . "," . $ilmid . ");'>"... 6441/6734
 *    $link_filter = "&nbsp;&nbsp;<a href='#' onclick='preview_ilm(" . $fileid . "," . $ilmid . ");'>"... 6447/6734
 * - v 2.7 2020/08/03
 *   + the 'utils' class was changed to "iassign utils" 
 * - v 2.6 2016/05/12
 *   + the 'utils' class was changed to "iassign utils" 
 * - v 2.5 2016/02/16
 *   + In 'optional_param(...)', some 'PARAM_ALPHANUMEXT' changed by 'PARAM_TEXT'
 *   + Fixed bug, now allow to rename iLM files
 * - v 2.4 2013/10/24
 *   + Insert function for recover iassign file in course.
 * - v 2.3 2013/08/26
 *   + Fix bug to upload file from block.
 * - v 2.2 2013/08/23
 *   + Fix bug to import zip files.
 * - v 2.1 2013/08/22
 *   + Merge for import zip files and iassign files.
 *   + Insert function for rename iassign file.
 * - v 2.0 2013/08/21
 *   + Change title link with message for get file for donwload file.
 *   + Manage import files.
 *   + Rename files for format accepted.
 *   + Change position of close and return buttons.
 * - v 1.9 2013/08/15
 *   + Insert functions for import files, export files and remove selected files.
 * - v 1.8 2013/08/02
 *   + Insert return button for block view.
 *   + Insert close button for iassign view.
 * - v 1.7 2013/07/03
 *   + Replace var 'DIRECTORY_SEPARATOR' for '/' (Server on Windows error of section)
 *   + Diferent view of block and iassign in files views.
 *   + Change button of open online editor ('open_online_ilm()').
 *   + View modified and created date in files views.
 * - v 1.3 2013/06/28
 *   + Correction function delete and duplicate.
 *   + Allow copying the file from another user.
 * - v 1.1 2013/06/26
 *   + Filter file extension for permission only compatilbe with iLM and block view all user files.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 2.7 2019/03/13
 * @version v 2.6 2016/05/12
 * @package mod_iassign_ilm
 * @since 2012/01/10
 * @copyright iMath (http://www.matematica.br) and LInE (http://line.ime.usp.br) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once("../../config.php");
require_once($CFG->dirroot . '/mod/iassign/lib.php');
require_once($CFG->dirroot . '/mod/iassign/locallib.php');
require_once($CFG->dirroot . '/mod/iassign/ilm_manager_form.php');

//DEBUG To exam files in Moodle data
//D $id = optional_param('id', 0, PARAM_INT); // Course Module ID
//D $action = optional_param('action', NULL, PARAM_TEXT); //2016:: PARAM_ALPHANUMEXT
//D $from = optional_param('from', NULL, PARAM_TEXT); //2016:: PARAM_ALPHANUMEXT
//D $ilmid = optional_param('ilmid', 1, PARAM_INT);
//D $fileid = optional_param('fileid', 1, PARAM_INT);
//D $url = "$CFG->wwwroot/mod/iassign/ilm_manager.php?from=$from&id=$id&ilmid=$ilmid";
//D $ilm_manager_instance = new ilm_manager($id, $url, $from);
//D echo "ilm_manager.php: ilmid=$ilmid, fileid=$fileid<br/>" . $ilm_manager_instance->get_file_ilm($ilmid, $fileid); exit;
//D print $ilm_manager_instance->get_file_ilm($ilmid, $fileid);
//D exit;

require_login();
if (isguestuser()) { // Security!
  die();
  }

if (session_id() === "")
  session_start();

// Prepare iLM content file in secure are to the iLM access it
function prepare_secure_access ($ilmid, $fileid, $userid) {
  require_once ('ilm_security.php');
  // ./lib/moodlelib.php : function get_file_storage(): $fs = new file_storage($filedir, $trashdirdir, "$CFG->tempdir/filestorage", $CFG->directorypermissions, $CFG->filepermissions);
  // ./lib/filestorage/file_storage.php : class file_storage
  $fs = get_file_storage();
  $md_file = $fs->get_file_by_id($fileid);
  $ilm_content_file = $md_file->get_content();  
  $timecreated = time(); $token = md5($timecreated);

  $filename = $md_file->get_filename(); // $md_file is instanceof 'class file_storage' from './lib/filestorage/file_storage.php'

  $id_iLM_security = ilm_security::write_iLM_security($userid, $timecreated, -1, $ilm_content_file); // insert in 'iassign_security': class with: iassign_statementid, userid, file,...
  $security = array('filename' => $filename, 'content' => $ilm_content_file, 'token' => $token, 'secure_id' => $id_iLM_security);

  return $security;
  }


// Parameters GET or POST
$id = optional_param('id', 0, PARAM_INT); // Course Module ID
$action = optional_param('action', NULL, PARAM_TEXT); //2016:: PARAM_ALPHANUMEXT
$from = optional_param('from', NULL, PARAM_TEXT); //2016:: PARAM_ALPHANUMEXT
$ilmid = optional_param('ilmid', 1, PARAM_INT);

if ($id>0) { // if reach here by iLM get request, id is not defined!
  $contextuser = context_user::instance($USER->id);
  $context = context_course::instance($id);
  }

$url = $CFG->wwwroot . "/mod/iassign/ilm_manager.php?iLM_PARAM_Authoring=true&from=" . $from . "&id=" . $id . "&ilmid=" . $ilmid;

//xx $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST); //QUARANTINE it is not necessary to present the course in the header...
$iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilmid));

if (empty($iassign_ilm)) {
  $iassign_ilm = new stdClass();
  $iassign_ilm->name = "";
  $iassign_ilm->extension = "";
  }

$returnurl = optional_param('returnurl', NULL, PARAM_TEXT);

if ($returnurl != NULL)
  $_SESSION['returnurl'] = optional_param('returnurl', $CFG->wwwroot . "/course/view.php?id=$id&ilmid=$ilmid", PARAM_TEXT); //2016:: PARAM_ALPHANUMEXT

$title = get_string('ilm_manager_title', 'iassign');

if ($id>0) { // if reach here by iLM get request, id is not defined!
  $PAGE->set_url($url);
  $PAGE->set_context($context);
  //xx $PAGE->set_course($course);
  $PAGE->blocks->show_only_fake_blocks();
  //xx $PAGE->set_title($course->fullname);
  if (is_null($action)) // first load: with iLM identification and with all iLM files of this iLM
    $PAGE->navbar->add($title);
  else
    $PAGE->navbar->add($title, $url);
  $PAGE->set_heading($title);
  }

//Test: if (has_capability('mod/iassign:editiassign', $context, $USER->id)) echo "ilm_manager.php: permissao!<br/>"; else  echo "ilm_manager.php: SEM permissao!<br/>";

if (has_capability('mod/iassign:editiassign', $context, $USER->id)) {

  $ilm_manager_instance = new ilm_manager($id, $url, $from); // ./locallib.php: class ilm_manager
  //echo "ilm_manager.php: $id, $url, $from<br/>";
  $dirid = $ilm_manager_instance->get_dir_ilm('dirid'); // ./locallib.php: class ilm_manager: 

  // Enter here whenever : teacher is viewing/creating an iLM content (preview), here is called by the iLM do not verify credentials.
  if ($action == 'get') {
    $fileid = optional_param('fileid', 1, PARAM_INT);
    return $ilm_manager_instance->get_file_ilm($ilmid, $fileid);
    }

  if ($action) { // avoid several tests when empty...
    switch ($action) { // '$ilm_manager_instance' instace of 'locallib.php : class ilm_manager'
      case 'new':

        $ilm_manager_instance->ilm_editor_new(); // ./mod/iassign/locallib.php: function ilm_editor_new()

        break;
      case 'update':
        $fileid = optional_param('fileid', NULL, PARAM_INT);
        $security = prepare_secure_access($ilmid, $fileid, $USER->id);
        //x $ilm_manager_instance->ilm_editor_update();
        $ilm_manager_instance->ilm_editor_update($security['filename'], $security['content'], $security['token'], $security['secure_id']);
        break;
      case 'delete':
        $ilm_manager_instance->delete_file_ilm();
        break;
      case 'duplicate':
        $ilm_manager_instance->duplicate_file_ilm();
        break;
      case 'rename':
        $ilm_manager_instance->rename_file_ilm();
        break;
      case 'preview':
        // The function bellow calls '/filter/iassign_filter/filter.php' function 'filter($text, array $options = array())' and exit (do not continue bellow)
        //// prepare_secure_access(): array('content' => $ilm_content_file, 'token' => $token, 'secure_id' => $id_iLM_security)
        if (isset($iassign_statementid))
          $ilm_manager_instance->preview_ilm($id, $iassign_ilm, $iassign_statementid); // in '/mod/iassign/locallib.php'
        else
          $ilm_manager_instance->preview_ilm($id, $iassign_ilm); // in '/mod/iassign/locallib.php'
        break;
      case 'addilm':
        $ilm_manager_instance->add_ilm();
        break;
      case 'tinymceilm':
        $fileid = optional_param('fileid', NULL, PARAM_INT);
        $ilm_manager_instance->editor_ilm($fileid, 'tinyMCE');
        // $ilm_manager_instance->tinymce_ilm($fileid);
        break;
      case 'attoilm':
        $fileid = optional_param('fileid', NULL, PARAM_INT);
        $ilm_manager_instance->editor_ilm($fileid, 'atto');
        // $ilm_manager_instance->atto_ilm($fileid);
        break;
      case 'export':
        $ilm_manager_instance->export_files_ilm();
        break;
      case 'import':
        $ilm_manager_instance->import_files_ilm();
        break;
      case 'selected_delete':
        $ilm_manager_instance->delete_selected_ilm();
        break;
      case 'new_dir':
        $ilm_manager_instance->new_dir_ilm();
        break;
      case 'delete_dir':
        $ilm_manager_instance->delete_dir_ilm();
        break;
      case 'rename_dir':
        $ilm_manager_instance->rename_dir_ilm();
        break;
      case 'selected_move':
        $ilm_manager_instance->selected_move_ilm();
        break;
      case 'move':
        $ilm_manager_instance->move_files_ilm();
        break;
      case 'recover':
        $ilm_manager_instance->recover_files_ilm();
        break;
      }
    } // if ($action)

  $mform = new ilm_manager_form(); // ./mod/iassign/ilm_manager_form.php
  $param = new stdClass();
  $param->id = $id;
  $param->from = $from;
  $param->ilmid = $ilmid;
  $param->dirid = $dirid;
  $mform->set_data($param);

  if ($mform->is_cancelled()) {
    redirect(new moodle_url("/course/view.php?id=" . $id));
    }
  else if ($formdata = $mform->get_data()) { // if exists '$mform->get_data()' use with '$formdata'
    $fs = get_file_storage();
    if ($formdata->dirid == 0)
      $dir_base = '/';
    else {
      $dir_base = $fs->get_file_by_id($formdata->dirid);
      //$dir_base = $dir_base->get_filepath();
      }

    if ($newfilename = $mform->get_new_filename('file')) {
      $url = $CFG->wwwroot . "/mod/iassign/ilm_manager.php?from=" . $formdata->from . "&id=" . $id . "&ilmid=" . $ilmid . "&dirid=" . $formdata->dirid;

      $file_extension_array = explode(".", $newfilename);
      $index_last_ext = count($file_extension_array) - 1;
      if ($index_last_ext < 0)
        $file_extension = "";
      else
        $file_extension = $file_extension_array[$index_last_ext];

      if (strtolower($file_extension) != 'zip') {
        $filename = $newfilename;

        $files_course = $fs->get_directory_files($context->id, 'mod_iassign', 'activity', 0, $dir_base, false, true, 'filename');

        if ($files_course) {
          foreach ($files_course as $value) {
            if ($value->get_filename() == iassign_utils::format_filename($newfilename))
              $filename = iassign_utils::version_filename($value->get_filename());
            }
          }

        $ilm_extensions = explode(",", $iassign_ilm->extension);
        if (in_array($file_extension, $ilm_extensions))
          $file = $mform->save_stored_file('file', $context->id, 'mod_iassign', 'activity', 0, $dir_base, iassign_utils::format_filename($filename), 0, $USER->id);
        else if ($from == 'block' || $from == 'tinymce' || $from == 'atto')
          $file = $mform->save_stored_file('file', $context->id, 'mod_iassign', 'activity', 0, $dir_base, iassign_utils::format_filename($filename), 0, $USER->id);
        else
          $url .= "&error=incompatible_extension_file";
        }
      else { // if (strtolower($file_extension[1]) != 'zip')
        $zip_filename = $CFG->dataroot . '/temp/' . $newfilename;
        $zip = new zip_packer();
        $mform->save_file('file', $zip_filename, true) or die("Save file not found");
        $zip_files = $zip->list_files($zip_filename);
        $files = $fs->get_directory_files($context->id, 'mod_iassign', 'activity', 0, $dir_base, false, true, 'filename');

        //TODO: --- inicio : linhas abaixo estavam comentadas, mas noutra versao funcional (MOOC) estao ativas
        $rename_files = array();
        foreach ($zip_files as $zip_file) {
          foreach ($files as $file) {
            if (iassign_utils::format_filename($zip_file->original_pathname) == $file->get_filename())
              $rename_files = array_merge($rename_files, array(iassign_utils::version_filename(iassign_utils::format_filename($zip_file->original_pathname)) => iassign_utils::format_filename($zip_file->original_pathname)));
            }
          } //TODO: --- final 2016/02/16

        $zip->extract_to_storage($zip_filename, $context->id, 'mod_iassign', 'activity', 0, $dir_base, $USER->id);

        //TODO: --- inicio : linhas abaixo estavam comentadas, mas noutra versao funcional (MOOC) estao ativas
        $files = $fs->get_area_files($context->id, 'mod_iassign', 'activity', 0, 'filename');
        foreach ($files as $file) {
          if ($file->get_author() == "") {
            $file->set_author($USER->firstname . ' ' . $USER->lastname);
            if ($new_name = array_search($file->get_filename(), $rename_files))
              $file->rename($dir_base, $new_name);
            else if ($file->get_filename() != '.' && $file->get_filename() != iassign_utils::format_filename($file->get_filename()))
              $file->rename($dir_base, iassign_utils::format_filename($file->get_filename()));
            }
          } //TODO: --- final 2016/02/16

        unlink($zip_filename);
        } // else if (strtolower($file_extension[1]) != 'zip')

      $fs->delete_area_files($contextuser->id, 'user', 'draft', $formdata->file);
      } // if ($newfilename = $mform->get_new_filename('file'))

   redirect(new moodle_url($url));
    } // else if ($formdata = $mform->get_data()) - 172/271,10

  print $OUTPUT->header();
  if ($from == 'iassign') { // came from iAssign (./mod/iassign/ilm_manager_form.php) => is selection of iLM activity file
    print ' <div width=100% align=right style="margin: 20px 20px 20px 20px;">' . "\n" .
          ' <input type=button value="' . get_string('close', 'iassign') . '" ' .
          ' title="' . get_string('close_alt', 'iassign') . '" onclick="javascript:window.close();" /></div>' . "\n"; // 'Close this window (any change will be lost)'
    }
  else
  if ($from == 'block') {
    //returnurl
    if (isset($_SERVER['HTTP_REFERER']))
      $strh=$_SERVER['HTTP_REFERER']; else $strh="<>, from=$from<br/>";

    if (isset($_SERVER['HTTP_REFERER']))
      print '<div width=100% align=right style="margin: 20px 20px 20px 20px;"><input type=button value="' . get_string('return', 'iassign') . '" onclick="javascript:window.location = \'' . $_SESSION['returnurl'] . '\';"></div>' . "\n";
    }
  else if ($from == 'tinymce' || $from == 'atto') {
    print '<div width=100% align=right style="margin: 20px 20px 20px 20px;"><input type=button value="' . get_string('close', 'iassign') . '" onclick="javascript:window.close ();"></div>' . "\n";
    }
  else if ($from == 'qtype') {
    //  print $OUTPUT->header();
    print $OUTPUT->heading($title);
    print '<div width=100% align=right style="margin: 20px 20px 20px 20px;"><input type=button value="' . get_string('close', 'iassign') . '" onclick="javascript:window.close ();"></div>' . "\n";
    }

  if (!is_null($error = optional_param('error', NULL, PARAM_TEXT)))
    print $OUTPUT->notification(get_string($error, 'iassign'), 'notifyproblem');

  $mform->display();

  $ilm_manager_instance->view_files_ilm($iassign_ilm, $iassign_ilm->extension); // locallib.php : function view_files_ilm($iassign_ilm, $extension)

  print $OUTPUT->footer();

  die;
  } // if (has_capability('mod/iassign:editiassign', $context, $USER->id))