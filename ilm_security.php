<?php

/**
 * This file is used to allow a more secure access to the iLM file contents.
 * 
 * ATTENTION: DO NOT USE any 'print' (or 'echo') other then the one that print the iLM file content.
 * Otherwise the iLM will receive this printed message as its file (probably nothing will be shown).
 * 
 * How:
 * The principle is to allow a single access to the file content, providing a "token" that is erased on the first use.
 * Every access to the content, by any iLM, must be provided by this vehicle.
 * 
 * Why:
 * The iLM must requires the file content by a GET connection. But if the file content is opened,
 * this means that the user (usually the learner) can get access to it by copying the URL directly.
 * In this case, if the iLM is based on "model answer" (like iGeom), the learner can open a local version of iLM
 * with this "model answer" (iGeom provides a special format to exercises to avoid this).
 * 
 * Table 'iassign_security': id iassign_statementid userid file timecreated view
 * 
 * TODO : the insertion in 'iassign_security' table must be provided by functions inside this code (not in './mod/iassign/locallib.php'
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.1 2017/11/02: fixed error in '$stringDebugAuxFile = "";' (it was with ".=") 
 * @version v 1.0 2010/12/10
 * @package mod_iassign_ilm
 * @since 2012/03/10
 * @by iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

global $DB;

//Debug: debug iLM security scheme
//Debug: ATTENTION, this requests the directory './mod/iassign/ilm_debug/' with write permition to www-data !!!!
$DEBUG = 0; //Debug: help to debug, register data in file 'MOODLE/mod/iassign/ilm_debug/YYYY_mm_dd_m_s_int'

class ilm_security {
  // Table 'iassign_security' : id iassign_statementid userid file timecreated view

  /// Warning message
  static function warning_message_iassign ($strcode) { // errado no 'locallib.php' sempre com constante 'error_view_without_actiontype'!!!
    return "<div classs='warning' style='display:inline; font-weight: bold; color:#a00'>" . get_string($strcode, 'iassign') . "</div>\n";
    }

  // @calledby here : after print $strFileContent;
  static function remove_records ($userid) { //, $iassign_statementid
    if (!isset($userid) || !isset($iassign_statementid)) {
      // self::warning_message_iassign('???');
      print self::warning_message_iassign('error_security_no_userid'); // 'Internal error: must be informed the user identification. Inform the Administrator.'
      return null;
      }
    if (!isset($iassign_statementid) || $iassign_statementid<1)
      $array_param = array("userid" => $userid); // erase all entries of this user
    else
      $array_param = array("userid" => $userid, "iassign_statementid" => $iassign_statementid);
    $DB->delete_records("iassign_security", $array_param); // erase only for this iAssign activity
    }

  // @calledby locallib.php : class ilm : function view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view)
  static function remove_old_iLM_security_entries ($userid) { // substituir 'locallib' de mesmo nome!
    global $DB;
    // This is an additional security: erase eventually old entries in 'iassign_security' table (do not remove '$iassign_statementid' since it is going to be used "now")
    $result = $DB->delete_records_select("iassign_security", "userid=" . $userid . " AND view>1", null);
    }

  // $id_iLM_security = $this->write_iLM_security($iassign_statement_activity_item->id, $content_or_id_from_ilm_security); // insert in 'iassign_security'
  /// Function to give a single access to an iLM content avoi (after used, 'view()', after 'view_iLM(...)', will erase the entry)
  //  @calledby locallib.php : class ilm_manager : function preview_ilm($courseid, $iassign_ilm): $id_iLM_security = ilm_security::write_iLM_security($USER->id, $timecreated, -1, $content_or_id_from_ilm_security);
  // ? @calledby locallib.php : class iassign : function view() : ...
  // ? @calledby locallib.php : view_iLM($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view) : $id_iLM_security=$this->write_iLM_security($iassign_statement_activity_item->id,$content_or_id_from_ilm_security);
  //  @param int $iassign_statement_activity_itemid Id of iassign statement, when from iLM 'preview' (there is none activity), -1
  //  @param Object $file File in use in activity
  //  @return int Return the id of log
  static function write_iLM_security ($userid, $timecreated, $iassign_statementid = -1, $content_or_id_from_ilm_security) { // subst. de locallib!
    global $DB;
    $newentry = new stdClass();
    $newentry->iassign_statementid = $iassign_statementid; // when came from iLM previw => there is none activity, use -1
    $newentry->userid = $userid;
    $newentry->file = $content_or_id_from_ilm_security;
    $newentry->timecreated = $timecreated; // who calls will generate: $timecreated = time(); $token == md5($timecreated);
    $newentry->view = 1;
    $id_iLM_security = $DB->insert_record("iassign_security", $newentry);
    if (!$id_iLM_security) {
      print_error('error_security', 'iassign'); // ./lib/setuplib.php: moodle_exception thrown
      }
    //D echo "ilm_security.php: write_iLM_security(...): acabou de inserir em 'iassign_security' (id_iLM_security=$id_iLM_security)<br/>\n";
    //D echo "ilm_security.php: write_iLM_security(...): file=" . $content_or_id_from_ilm_security . "<br/>\n";
    //D $aux_iassign_security = $DB->get_records('iassign_security', array('id' => $id_iLM_security));
    //D foreach ($aux_iassign_security as $item)
    //D echo " * iassign_security.id=" . $item->id . ", iassign_statementid=" . $item->iassign_statementid . ', iassign_security.id=' . $id_iLM_security . "<br/>\n"; //", file=" . $item->file  . - tem o conteudo do arquivo
    return $id_iLM_security;
    }

    //D $iassign_iLM_security = $DB->get_record("iassign_security", array("iassign_statementid" => $iassign_statementid));
    //D if ($iassign_iLM_security) foreach ($iassign_iLM_security as $item) { echo $iassign_iLM_security->id . " ; " . $iassign_iLM_security->iassign_statementid . " ; " . $iassign_iLM_security->userid . " ; "  . $iassign_iLM_security->timecreated . " ; " .  $iassign_iLM_security->view . " ; " . $iassign_iLM_security->file . "<br/>\n"   }
    //D else echo "Apagou!<br/>";

  } // class ilm_security

$view = optional_param('view', NULL, PARAM_TEXT); //$view = $_GET['view'];
$token = optional_param('token', NULL, PARAM_TEXT); //$token = $_GET['token'];
$id = optional_param('id', NULL, PARAM_TEXT); //$id = $_GET['id']; //id of the table iassign_security

$stringDebugAux = "";
$strFileContent = "";

// Debug
if ($DEBUG) {
  $file_name = "ilm_debug/" . date('Y') . "_" . date('m') . "_" . date('d') . "_" . date('H_i') . "_" . $id;
  $file_debug = "id=" . $id . "<br/>\nview=" . $view . "<br/>\ntoken=" . $token;
  $stringDebugAux = "user.id=" . $USER->id . ", user.name=" . $USER->firstname . " " . $USER->lastname . "\n";
  }

if ($view == -1) { //view free
  //xx echo "view==-1: DEBUG=$DEBUG<br/>\n"; //DO NOT USE, unless by direct access to debug 'ilm_security.php'...
  $fs = get_file_storage();
  $file = $fs->get_file_by_id($id);
  $strFileContent .= $file->get_content();
  $stringDebugAux .= "1: file content:" . $strFileContent;
  print $strFileContent;
  ilm_security::remove_old_iLM_security_entries($USER->id); // for security reason erase the used entry in 'iassign_security' (and others for this user/activity)
  }
else {

  // Get data from table 'iassign_security'
  $iassign_security = $DB->get_record("iassign_security", array("id" => $id)); // id iassign_statementid userid file timecreated view
  //xx echo "<br/>iassign_security="; print_r($iassign_security); echo "<br/>"; //DO NOT USE, unless by direct access to debug 'ilm_security.php'...
  if ($DEBUG) {
    $strAux = "iassign_security = { id=" . $iassign_security->id . ", " . $iassign_security->iassign_statementid . ", | " . $iassign_security->file . " |, " . $iassign_security->view . " }";
    $stringDebugAux .= $strAux;
    }

  if ($iassign_security) {

    $fileid = $iassign_security->file;

    if ($iassign_security) { //TODO must be 'if ($fileid)'?

      $update = new stdClass();
      $update->id = $iassign_security->id;
      $update->view = $iassign_security->view + 1;
      $DB->update_record("iassign_security", $update);
      if ($DEBUG) $stringDebugAux .= " view++ = " . $iassign_security->view . "\n";

//????? apos acertos deixar apenas '$update->view == 2' ??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
      if ($update->view >= 2 && $token == md5($iassign_security->timecreated)) { //

        //Security iLM: remove the entry
        // $DB->delete_records("iassign_security", array("id" => $id));

        if ($view) {

	  // If it is view of the exercise, then get it on the Moodle data (usually '/var/moodledata') => file is a number = '*_files.id'
          // If it is learner answer get it in data base => file is the iLM file content

          // $stringDebugAuxFile = ""; //Debug
          $fs = get_file_storage();
          $file = $fs->get_file_by_id($fileid);
          $strFileContent = $file->get_content();
          $stringDebugAuxFile = $file->get_filename() . "/"; //Debug
          if ($DEBUG) {
            $stringDebugAux .= "view>=2: view=$view: update->view=" . $update->view . "\n" . $token . "=" . md5($iassign_security->timecreated) . "?\n";
            }
          } // if ($update->view == 2 && $token == md5($iassign_security->timecreated))
        else { // not view - get the student content answer

          

          // *_iassign_security : id iassign_statementid userid file timecreated view  (where 'file' is longtext utf8_unicode_ci)
          // passei para 'blob'
          $strFileContent = $iassign_security->file; //ERROR: usa algum filtro, elimina '.', '/' e outros caracteres
          //$strFileContent = $contextid; - tb nao funciona!!

          if ($DEBUG) {
            $stringDebugAux .= "view>=2: else view=$view: update->view=" . $update->view . "\n" . $token . "=" . md5($iassign_security->timecreated) . "?\n";
            $stringDebugAux .= " " . $iassign_security->id . ", " . $iassign_security->timecreated . "\n";
            }
          }
	  
        // Here is the print to the iLM request the content
        print $strFileContent;
        ilm_security::remove_old_iLM_security_entries($USER->id); // for security reason erase the used entry in 'iassign_security' (and others for this user/activity)

        } // if ($update->view == 2 && $token == md5($iassign_security->timecreated))
      else {
        if ($DEBUG) {
          $countF = 0;
          foreach ($files as $thefile) {
            $strFileName = $thefile->get_filename(); //Debug
            $stringDebugAux .= " " . ($countF++) . ": " . $strFileName . "\n";
            $stringDebugAuxFile = $strFileName . "/"; //Debug
            if ($strFileName != '.') {
              $strFileContent = $thefile->get_content();
              }
            }

          $stringDebugAux .= "view<=2: NOT update->view=" . $update->view . "\n" . $token . "=" . md5($iassign_security->timecreated) . "?\nstrFileContent=" . $strFileContent . "\n";

          }
        }
      } // if ($iassign_security)
    } // if ($iassign_security)
  }

//NAO pode deixar 'echo' aqui, pois o resultado daqui alimentara o iMA! echo "ilm_security.php: file_name=$file_name<br/>";
if ($DEBUG) {
  //xxecho "file_name=$file_name"; //DO NOT USE, unless by direct access to debug 'ilm_security.php'...
  $fpointer = fopen($file_name, "w");
  $file_debug .= "\nAuxiliary information: " . $stringDebugAux . "";
  $file_debug .= "\nContent iLM file: |" . $strFileContent . "|";
  fwrite($fpointer, "From: ./mod/iassign/ilm_security.php<br/>\n" . $file_debug);
  fclose($fpointer);
  }
