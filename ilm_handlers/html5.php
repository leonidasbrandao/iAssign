<?php

/**
 * Class that implements ilm_handle, in order to allow manipulation and management of HTML5 iLM
 * 
 * @author Igor Moreira Félix
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1 2017/17/10
 * @package mod_iassign_ilm_handlers
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once $CFG->dirroot . '/mod/iassign/ilm_handle.php';

class html5 implements ilm_handle {


  /// Produce HTML code to load iLM
  public static function build_ilm_tags ($ilm_id, $options = array()) {
    global $DB, $OUTPUT;
    global $CONF_WWW; //TODO 1 => use iLM under WWW; otherwise use under MoodleData
    $html = "";

    if (empty($options['Proposition']))
      $options['Proposition'] = "";
    if (empty($options['addresPOST']))
      $options['addresPOST'] = "";
    if (empty($options['student_answer']))
      $options['student_answer'] = "";
    if (empty($options['notSEND']))
      $options['notSEND'] = "";
    else // Case it is authoring put 'notSEND' (important to iVProgH5 to present authoring tool)
    if ($options['type'] == "editor_update")
      $options['notSEND'] = "true";
    if (empty($options['id_iLM_security'])) // if defined, it is from 'iassign_security'
      $options['id_iLM_security'] = "";
    $id_iLM_security = $options['id_iLM_security'];

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

    if ($iassign_ilm) {

      // md_files : filename
      $ilm_extension = $iassign_ilm->extension; // use local variavel to efficiency (several use)
      if ($ilm_extension) { // avoid problems
        $ilm_extension = strtolower($ilm_extension);
        }

      // Attention: in iAssign 2014 on, all the iLM is located on the Moodle filesystem (usually /var/moodledata/filedir/).
      // This means that '$iassign_ilm->file_jar' = '*_files.id'

      $file_url = array();
      $fs = get_file_storage();
      $files_jar = explode(",", $iassign_ilm->file_jar);

      $url = $iassign_ilm->file_class; // to HTML5 package, this 'file_class' must have the main HTML file
      array_push($file_url, $url);

      $lang = substr(current_language(), 0, 2);

      if ($options['type'] == "filter") { //leo
        $iassign_ilm_width = $options['width']; // or use? $iassign_ilm->width
        $iassign_ilm_height = $options['height']; // or use? $iassign_ilm->height
      } else { //leo
        $iassign_ilm_width = $iassign_ilm->width;
        $iassign_ilm_height = $iassign_ilm->height; // or use? $iassign_ilm->height
        }

      if (!empty($file_url)) { // There is an iLM file
        //TODO iLM_HTML5 :: Change to 'object', tag 'applet' was deprecated.
        $paramsStr = "?1=1";
        $html .= html5::show_ilm_commands();

        switch ($options['type']) {
          case "view":
            $paramsStr .= "&iLM_PARAM_Assignment=" . urlencode($options['Proposition']); //leo
            $paramsStr .= "&iLM_PARAM_SendAnswer=true";
            //TODO: REVIEW: this code is to insert iLM as HTML5 and to allow general parameter to any iLM
            //TODO: For now, 'iassign_ilm_config' is empty... let comment these lines
            //n $iassign_ilm_config = $DB->get_records('iassign_ilm_config', array('iassign_ilmid' => $ilm_id));
            //n foreach ($iassign_ilm_config as $ilm_config) {
            //n   if (array_key_exists($ilm_config->param_name, $options)) {
            //n     $ilm_config->param_value = $options[$ilm_config->param_name];
            //n     $paramsStr .= "&" . $ilm_config->param_name . "=" . urlencode($ilm_config->param_value);
            //n       }
            //n     }
            break;
          case "filter":
            if ($options['toolbar'] == "disable")
              $paramsStr .= "&SOH_ADD=ADD";
            $paramsStr .= "&iLM_PARAM_AssignmentURL=true";
            $paramsStr .= "&iLM_PARAM_Assignment=" . urlencode($options['Proposition']);
            $paramsStr .= "&iLM_PARAM_SendAnswer=" . urlencode($options['notSEND']);
            $paramsStr .= "&iLM_PARAM_ServerToGetAnswerURL=" . urlencode($ilm_config->param_value);
            break; // static function build_ilm_tags($ilm_id, $options=array())
          case "activity": // build_ilm_tags
            //TODO To generalize to any HTML5 iLM, it is necessary to use 'iLM_PARAM_Assignment' and 'iLM_PARAM_SendAnswer'
            //TODO iLM_PARAM_Assignment=Proposition ; iLM_PARAM_SendAnswer=notSEND

            $paramsStr .= "&iLM_PARAM_AssignmentURL=true";
            // if ($options['special_param'] == 1) {   }
            $paramsStr .= "&iLM_PARAM_Assignment=" . urlencode($options['Proposition']);
            $paramsStr .= "&iLM_PARAM_SendAnswer=" . urlencode($options['notSEND']);
            $paramsStr .= "&iLM_PARAM_ServerToGetAnswerURL=" . urlencode($options['addresPOST']);
            //TODO iLM_HTML5 :: To extend to any iLM in HTML5
            //TODO  iLM_HTML5 :: it will allow to load dynamic parameters
            //T $iassign_activity_item_configs = $DB->get_records('iassign_statement_config', array('iassign_statementid' => $options['iassign_statement'] ));
            //T if ($iassign_activity_item_configs) {
            //T   foreach ($iassign_activity_item_configs as $iassign_activity_item_config)
            //T     $paramsStr .= "&" . $iassign_activity_item_config->param_name . "=" . urlencode($iassign_activity_item_config->param_value);
            //T       }      
            break;
          case "editor_new":
            $paramsStr .= "&iLM_PARAM_AssignmentURL=true";
            $paramsStr .= "&iLM_PARAM_SendAnswer=" . urlencode($options['notSEND']);
            $paramsStr .= "&iLM_PARAM_Authoring=true";
            break;
          case "editor_update":
            $paramsStr .= "&iLM_PARAM_AssignmentURL=true";
            $paramsStr .= "&iLM_PARAM_Assignment=" . urlencode($options['Proposition']);
            $paramsStr .= "&iLM_PARAM_SendAnswer=" . urlencode($options['notSEND']);
            $paramsStr .= "&iLM_PARAM_Authoring=true";
            break;
          default:
            $html .= iassign::warning_message_iassign('error_view_without_actiontype'); // $OUTPUT->notification(get_string('error_view_without_actiontype', 'iassign'), 'notifyproblem'); // The API allows for creation of four types of notification: error, warning, info, and success.
            } // switch($options['type'])

          $paramsStr .= "&lang=" . $lang; // get the language defined in Moodle
          $parameters = ' style="width: ' . $iassign_ilm_width . 'px; height: ' . $iassign_ilm_height . 'px;" ';

          $html .= '<iframe frameborder="0" name="iLM" id="iLM" src="' . $iassign_ilm->file_jar . $iassign_ilm->file_class . $paramsStr . '" ' . $parameters . '>' . "\n";
          $html .= '</iframe>' . "\n";

        } // if (!empty($file_url))
      } // if ($iassign_ilm)

    return $html;
    } // public static function build_ilm_tags($ilm_id, $options = array())


  public static function show_ilm_commands() {
    $html = "<script>function full_screen() {
  if('fullscreenEnabled' in document || 'webkitFullscreenEnabled' in document || 'mozFullScreenEnabled' in document || 'msFullscreenEnabled' in document) {
    if(document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || document.msFullscreenEnabled) {
      var element = document.getElementsByName('iLM').item(0);
      //requestFullscreen is used to display an element in full screen mode.
      if('requestFullscreen' in element) {
        element.requestFullscreen();
      } 
      else if ('webkitRequestFullscreen' in element) {
        element.webkitRequestFullscreen();
      } 
      else if ('mozRequestFullScreen' in element) {
        element.mozRequestFullScreen();
      } 
      else if ('msRequestFullscreen' in element) {
        element.msRequestFullscreen();
      }
    }
  } else {
    $('.expand_button').addClass('disabled');
  }
}</script>";
    $html .= "<div id='fullscreen' style='width: 800px;text-align: right;'><div onClick='full_screen()' style='color: blue; cursor: pointer; display: contents;'>".get_string('full_screen', 'iassign')."</div></div>";

    return $html;
  }

  /// Exibe a atividade no iLM
  //  @calledby locallib.php : view_iLM(...)
  public static function show_activity_in_ilm ($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view_teacherfileversion) {

    global $USER, $CFG, $COURSE, $DB, $OUTPUT;

    $special_param1 = $iassign_statement_activity_item->special_param1;

    $ilm = $DB->get_record('iassign_ilm', array('id' => $iassign_statement_activity_item->iassign_ilmid));

    $context = context_module::instance($USER->cm);

    //TODO Given an activity => find its correspondent file in Moodle data. Bad solution!
    //TODO Change the meaning of 'iassign_statement.file' from insertion order to the ID in table 'files'.
    //TODO This demands update to each 'iassign_statement', find its corresponding on in 'files', and update 'iassign_statement.file = files.id'
    if ($view_teacherfileversion) { // get the exercise in Moodle data (teacher file)
      $fileid = "";
      $fs = get_file_storage();
      $files = $fs->get_area_files($context->id, 'mod_iassign', 'exercise', $iassign_statement_activity_item->filesid); // iassign_statement_activity_item = table 'iassign_statement'
      if ($files) {
        foreach ($files as $value) {
          if ($value->get_filename() != '.')
            $fileid = $value->get_id();
          }
        }
      if (!$fileid) { // 'Something is wrong. Maybe your teacher withdrew this exercise file. Please, inform your teacher.';
        print iassign::warning_message_iassign('error_exercise_removed') . "<br/>\n"; // I couldn't find the file in table 'files'!
        }
      }

    $ilm_name = strtolower($ilm->name);
    $extension = iassign_utils::filename_extension($ilm_name);

    if ($view_teacherfileversion) { // $view_teacherfileversion==1 => load the exercise ('activity') from the 'moodledata' (id in 'files')
      // $content_or_id_from_ilm_security = $this->context->id;
      $content_or_id_from_ilm_security = $fileid; // $iassign_statement_activity_item->file;
      }
    else { // $view_teacherfileversion==null => load the learner answer from the data base (iassign_submission)
      $content_or_id_from_ilm_security = $student_answer;
      }

    $allow_submission = false; // There is permission to 'submission' button?
    //VERIFICAR ESTE IF
    if ($USER->iassignEdit == 1 && $student_answer) { // for now, only iVProg2 and iVProgH5 allows editions of exercise already sent
      $allow_submission = true; // yes!
      $write_solution = 1;
      $enderecoPOST .= "&write_solution=1"; // complement POST address indicating that the learner could send edited solution
      }

    // Security: this avoid the student get a second access to the file content (usually an exercise)
    // Data are registered in the table '*_iassign_security' bellow and is erased by function 'view()' above.
    // IMPORTANT: the '$end_file' will receive the iLM content URL using the security filter './mod/iassign/ilm_security.php'
    // the iLM must request the content using this URL. Data are registered in the table '*_iassign_security'.
    // Attention : using iVProgH5 there are lot of " and the use of slashes (as '\"') will imply in iVProgH5 do not read the file!
    // do not use: $id_iLM_security = $this->write_iLM_security($iassign_statement_activity_item->id, addslashes($content_or_id_from_ilm_security));
    //2017 $id_iLM_security = $this->write_iLM_security($iassign_statement_activity_item->id, $content_or_id_from_ilm_security); // insert in 'iassign_security'
    //2017 $this->remove_old_iLM_security_entries($USER->id, $iassign_statement_activity_item->id);  // additional security: erase eventually old entries
    require_once ($CFG->dirroot . '/mod/iassign/ilm_security.php');
    $timecreated = time();
    $token = md5($timecreated); // iassign_iLM_security->timecreated);

    $id_iLM_security = ilm_security::write_iLM_security($USER->id, $timecreated, $iassign_statement_activity_item->id, $content_or_id_from_ilm_security); // insert in 'iassign_security'

    // $iassign_iLM_security = $DB->get_record("iassign_security", array("id" => $id_iLM_security));

    $end_file = $CFG->wwwroot . '/mod/iassign/ilm_security.php?id=' . $id_iLM_security . '&token=' . $token . '&view=' . $view_teacherfileversion; // need full path...

    //
    $iassign = "
  <script type='text/javascript'>
  //<![CDATA[
  var strAnswer = '';
  var evaluationResult = '';
  var comment = '';

  function jsAnalyseAnswer () {

    document.getElementById('spinner-loading').style.visibility = 'visible';
    setTimeout(function(){
      document.getElementById('spinner-loading').style.visibility = 'hidden';
    }, 5000);

    // iVProgH5 will call function 'getEvaluationCallback(...)': /var/www/html/ivprogh5/js/services.js
    // 'getEvaluation()' calls 'js/services.js : endTest function(index)' that calls 'getEvaluationCallback(apro/100);'

    // sumEval = getSummation(); alert('mod/iassign/ilm/ifractions_5/index.html: sumEval = ' + sumEval);
    //CUIDADO 2017/11/22 - usar 'window.frames.iLM' resultava neste ponto 'TypeError: window.frames.iLM.getEvaluation is not a function'
    //CUIDADO resp = window.frames.iLM.getEvaluation();
    resp = window.frames[0].getEvaluation(); // pegar diretamente a primeir janela (nao pode haver outra!)

    if (resp == 'undefined') // in './mod/iassign/ilm/ivprog-html/js/services.js'; './mod/iassign/ilm/ivprog-html/main.html'
      return false;

    return true;
    }

  window.flagclick = false;
  // ./mod/iassign/ilm/ivprog-html/js/services.js : call this to define the variable 'evaluationResult'
  function getEvaluationCallback (evaluation) {
    let submissionbehavior = ".$ilm->submissionbehavior.";
    evaluationResult = evaluation;
    //leo 2017/11/22 strAnswer = window.frames.iLM.getAnswer();
    strAnswer = window.frames[0].getAnswer();      
    // alert('getEvaluationCallback(...)' + evaluation + ', strAnswer=' + strAnswer);
    //comment = document.formEnvio.submission_comment.value;
    //leo alert('getEvaluationCallback: enviando evaluationResult=' + evaluation + ', strAnswer=' + strAnswer);
    if ((strAnswer==null || strAnswer=='' || strAnswer==-1)/* && (comment==null || comment=='')*/) { // undefined
      alert('" . get_string('activity_empty', 'iassign') . "'); // 'Activity sent without content.'
      return false; // error...
      }
    else if (submissionbehavior == 1) {
      document.formEnvio.iLM_PARAM_ArchiveContent.value = strAnswer;
      document.formEnvio.iLM_PARAM_ActivityEvaluation.value = evaluationResult;
      document.formEnvio.iLM_PARAM_RealGrade.value = evaluation;
      //D alert(strAnswer);
      document.formEnvio.submit();
      return true; // success
      } else if (submissionbehavior == 0) {
        var formData = new FormData();
        formData.append('iLM_PARAM_ArchiveContent', strAnswer);
        formData.append('iLM_PARAM_ActivityEvaluation', evaluationResult);
        formData.append('iLM_PARAM_RealGrade', evaluation);
        formData.append('iLM_ajax', 1);
        var request = new XMLHttpRequest();
        request.open('POST', '$enderecoPOST');
        request.onload = function (e) {
          if (!window.flagclick) return;
          window.flagclick = false;
          document.getElementById('spinner-loading').style.visibility = 'hidden';
          if (request.readyState === 4) {
            if (request.status === 200) {
              document.getElementById('check-message-success-submission').style.visibility = 'visible';
              
              setTimeout(function(){
                document.getElementById('check-message-success-submission').style.visibility = 'hidden';
              }, 3000);
            } else {
              document.getElementById('error-message-submission').style.visibility = 'visible';
            }
          } else {
            document.getElementById('error-message-submission').style.visibility = 'visible';
          }
        };
        request.send(formData);
      }
     }
  //]]>
  </script>\n";

    $iassign .= "\n<center>\n<form name='formEnvio' id='formEnvio' method='post' action='$enderecoPOST' enctype='multipart/form-data'>\n";

    // Attention: The actual iLM content will be provided by the indirect access in './mod/iassign/ilm_security.php',
    // bellow only the 'token' to the content will be shown in URL (by security reason). The iLM must use this URL on
    // 'MA_PARAM_Proposition' to request the content.
    // Calls static function bellow: parameters are data to store in table '*_iassign_submission'
    // In 'locallib.php ! class ilm_settings ! static function build_ilm_tags($ilm_id, $options = array())' that calls 'build_ilm_tags(.)' from here
    $iassign .= ilm_settings::build_ilm_tags($ilm->id, array(
      "type" => "activity",
      "notSEND" => "false",
      "addresPOST" => $enderecoPOST,
      "Proposition" => $end_file,
      "special_param" => $special_param1,
      "student_answer" => $student_answer,
      "id_iLM_security" => $id_iLM_security,
      "iassign_statement" => $iassign_statement_activity_item->id // MOOC 2016
      ));

    //DEBUG To verify iLM
    //D $iassign .= "
    //D <script type='text/javascript'>
    //D //alert('./mod/iassign/ilm_handlers/html5.php: 1 window.frames[0].getEvaluation()=' + window.frames[0].getEvaluation()); //
    //D alert('./mod/iassign/ilm_handlers/html5.php: 1 window.frames[0].getAnswer()=' + window.frames[0].getAnswer()); //
    //D </script>\n";

    if (!isguestuser() && $iassign_statement_activity_item->type_iassign != 1
    && ($ilm->editingbehavior == 1 || ($ilm->editingbehavior == 0 && !in_array($_GET['action'], array('viewsubmission', 'view'))))) {

      $iassign .= " <input type='hidden' name='iLM_PARAM_ArchiveContent' value=''>\n";
      $iassign .= " <input type='hidden' name='iLM_PARAM_ActivityEvaluation' value=''>\n";
      $iassign .= " <input type='hidden' name='iLM_PARAM_RealGrade' value=''>\n";

      $disabled_button = "";
      if ($ilm->action_buttons == 1)
        $disabled_button = "";
      elseif ($ilm->action_buttons == 0)
        $disabled_button = " disabled='true' ";

      //$iassign .= "<p><textarea rows='2' cols='60' name='submission_comment'></textarea></p>\n";
      $iassign .= "<center>\n<!-- load button -->\n" .
      " <br> <br> <button $disabled_button class='btn btn-primary' type=button value='" . get_string('submit_iassign', 'iassign') . "' onClick = 'window.flagclick = true; window.jsAnalyseAnswer();' title='" .
      get_string('message_submit_iassign', 'iassign') . "'>" . get_string('submit_iassign', 'iassign') . "</button> <i id='check-message-success-submission' class='fa fa-check' aria-hidden='true' style='position: absolute; color: green; font-size: 2.0rem; margin-left: 1rem; visibility: hidden;'><span style='font-size: 1.0rem;'>".get_string('get_answer', 'iassign')."</span></i><i id='error-message-submission' class='fa fa-times' aria-hidden='true' style='position: absolute; color: red; font-size: 2.0rem; margin-left: 1rem; visibility: hidden;'><span style='font-size: 1.0rem;'>".get_string('error_insert_submissions', 'iassign')."</span></i>\n" . " <div class='spinner-border' style='position: absolute; margin-left: 1rem; visibility: hidden;' id='spinner-loading' role='status'>
        <span class='sr-only'>Loading...</span>
      </div> </center>\n";
      

    } // if (!isguestuser() && $iassign_statement_activity_item->type_iassign != 1)
    elseif ($ilm->editingbehavior == 0 && $student_answer) {
      
      $iassign .= "<center><br><a href=\"view.php?action=repeat&id=".$_GET['id']
            ."&iassign_current=".$_GET['iassign_current']."\">
              <button type='button' class='btn btn-success' value='".get_string('repeat', 'iassign')."'>".get_string('repeat', 'iassign')."</button></a></center>";
    }
    
    $iassign .= "</form></center>\n\n";
    return $iassign;
    } // public static function show_activity_in_ilm($iassign_statement_activity_item, $student_answer, $enderecoPOST, $view_teacherfileversion)


  /// Presents iLM information
  public static function view_ilm ($ilmid, $from) {
    global $DB;

    $url = new moodle_url('/admin/settings.php', array('section' => 'modsettingiassign'));
    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilmid));

    $str = "";
    $str .= '<table id="outlinetable" cellpadding="5" width="100%" >' . "\n";
    $str .= '<tr>';
    $str .= '<td colspan=3 align=right>';
    if ($from != 'admin') {
      $str .= '<input type=button value="' . get_string('return', 'iassign') . '"  onclick="javascript:window.location = \'' . $_SERVER['HTTP_REFERER'] . '\';">' . "\n";
      }
    $str .= '<input type=button value="' . get_string('close', 'iassign') . '"  onclick="javascript:window.close();">';
    $str .= '</td>' . "\n";
    $str .= '</tr>' . "\n";

    if ($iassign_ilm) {
      $iassign_statement_activity_item = $DB->get_records('iassign_statement', array("iassign_ilmid" => $iassign_ilm->id));
      if ($iassign_statement_activity_item) {
         $total = count($iassign_statement_activity_item);
      } else {
         $total = 0;
         }

      if ($from == 'admin') {
        $str .= '<tr><td colspan=2>' . "\n";
        $str .= '<table width="100%" class="generaltable boxaligncenter" >' . "\n";
        $str .= '<tr>' . "\n";
        $str .= '<td class=\'cell c0 actvity\' ><strong>' . get_string('activities', 'iassign') . ':</strong>&nbsp;' . $total . '</td>' . "\n";
        $str .= '<td><strong>' . get_string('url_ilm', 'iassign') . '</strong>&nbsp;<a href="' . $iassign_ilm->url . '">' . $iassign_ilm->url . '</a></td>' . "\n";
        $str .= '</tr>' . "\n";
        $str .= '<tr><td colspan=2><strong>' . get_string('description', 'iassign') . ':</strong>&nbsp;' . iassign_language::get_description_lang(current_language(), $iassign_ilm->description) . '</td></tr>' . "\n";
        $str .= '<tr><td width="50%"><strong>' . get_string('type_ilm', 'iassign') . ':</strong>&nbsp;' . $iassign_ilm->type . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . get_string('extension', 'iassign') . ':</strong>&nbsp;' . $iassign_ilm->extension . '</td>' . "\n";
        $str .= '<td width="50%"><strong>' . get_string('width', 'iassign') . ':</strong>&nbsp;' . $iassign_ilm->width;
        $str .= '&nbsp;&nbsp;<strong>' . get_string('height', 'iassign') . ':</strong>&nbsp;' . $iassign_ilm->height . '</td></tr>' . "\n";

        $date_jar = $iassign_ilm->file_jar;

        $str .= '<tr><td><strong>' . get_string('file_jar', 'iassign') . ':</strong>&nbsp;' . $date_jar . '</td>' . "\n";
        $str .= '<td ><strong>' . get_string('file_class', 'iassign') . ':</strong>&nbsp;' . $iassign_ilm->file_class . '</td></tr>' . "\n";
        if ($iassign_ilm->evaluate == 1) {
            $evaluate = get_string('yes', 'iassign');
          } else {
            $evaluate = get_string('no', 'iassign');
          }
        $str .= '<tr><td width="50%"><strong>' . get_string('evaluate', 'iassign') . ':</strong>&nbsp;' . $evaluate . '</td>' . "\n";
        if ($iassign_ilm->enable == 1) {
            $enable = get_string('yes', 'iassign');
          } else {
            $enable = get_string('no', 'iassign');
          }
        $str .= '<td width="50%"><strong>' . get_string('enable', 'iassign') . ':</strong>&nbsp;' . $enable . '</td></tr>' . "\n";
        $str .= '<tr>' . "\n";
        $str .= '<td width="50%"><strong>' . get_string('file_created', 'iassign') . ':</strong>&nbsp;' . userdate($iassign_ilm->timecreated) . '</td>' . "\n";
        $str .= '<td width="50%"><strong>' . get_string('file_modified', 'iassign') . ':</strong>&nbsp;' . userdate($iassign_ilm->timemodified) . '</td>' . "\n";
        $str .= '</tr>' . "\n";
        $user_ilm = $DB->get_record('user', array('id' => $iassign_ilm->author));
        if ($user_ilm) {
            $str .= '<tr>' . "\n";
            $str .= '<td colspan=2><strong>' . get_string('author', 'iassign') . ':</strong>&nbsp;' . $user_ilm->firstname . '&nbsp;' . $user_ilm->lastname . '</td>' . "\n";
            $str .= '</tr>' . "\n";
          }
        $str .= '</table>' . "\n";
        $str .= '</td></tr>' . "\n";
        }

      if (!empty($iassign_ilm->file_jar)) {
        //TODO: REVIEW: to be used for parameters of "applet" from DB
        $options = array("type" => "view"); //MOOC2014: start

        $str .= '<tr class=\'cell c0 actvity\'><td  colspan=3 align=center bgcolor="#F5F5F5">' . "\n";

        // Second parameter null since 'iassign_security' are not define yet
        $str .= ilm_settings::build_ilm_tags($iassign_ilm->id, $options);

        //TODO: REVIEW: missing code to manage parameters
        //MOOC2014: tem este codigo!
        } else {
        $str .= '<tr class=\'cell c0 actvity\'>' . "\n";
        $str .= '<td colspan=2 align=center>' . get_string('null_file', 'iassign') . '</td>' . "\n";
        $str .= '<td align=center><a href="' . $url . '</a></td>' . "\n";
        $str .= '</tr>' . "\n";
        }
      $str .= '</td></tr>' . "\n";
      }

    $str .= '</table>' . "\n";

    return $str;
    } // public static function view_ilm($ilmid, $from)


  /// Make a copy or produce a new version of an iLM
  //  @see locallib.php : copy_new_version_ilm($param)
  public static function copy_new_version_ilm ($param, $files_extract) {
    global $DB, $CFG;

    $iassign_ilm = new stdClass();
    $iassign_ilm->name = $param->name;
    $iassign_ilm->version = $param->version;
    $iassign_ilm->file_jar = null;

    $application_xml = @simplexml_load_file($CFG->dataroot . '/temp/' . 'ilm-application.xml', null, LIBXML_NOCDATA); //2019
    $file_jar = self::save_ilm_by_xml($application_xml, $files_extract); // iLM ṕath (since iAssign)> ./ilm/...

    if ($file_jar == null) { // 'save_ilm_by_xml(...)' detected a problem (perhaps no write permission)
      return false;
      }

    $file_jar = str_replace("./", "", $file_jar) . "/";

    // From a single text in default language => generate multi-language
    //R $description = json_decode($param->description_lang);
    //R $description->{$param->set_lang} = $param->description;
    $description_json_lang = $param->description_lang;

    $newentry = new stdClass();
    $newentry->name = $param->name;
    $newentry->version = $param->version;
    $newentry->type = 'HTML5';
    $newentry->url = $param->url;
    $newentry->description = $description_json_lang; //R strip_tags(json_encode($description));
    $newentry->extension = strtolower($param->extension);
    $newentry->file_jar = $file_jar;
    $newentry->file_class = $param->file_class;
    $newentry->width = $param->width;
    $newentry->height = $param->height;
    $newentry->enable = 0;
    $newentry->timemodified = $param->timemodified;
    $newentry->timecreated = $param->timecreated;
    $newentry->evaluate = $param->evaluate;
    $newentry->author = $param->author;
    $newentry->parent = $param->parent;

    $newentry->id = $DB->insert_record("iassign_ilm", $newentry);

    // log event --------------------------------------------------------------------------------------
    iassign_log::add_log('copy_iassign_ilm', 'name: ' . $param->name . ' ' . $param->version, 0, $newentry->id);
    // log event --------------------------------------------------------------------------------------
    } // public static function copy_new_version_ilm($param, $files_extract)


  /// Export the iLM to the IPZ package
  public static function export_ilm ($ilm_id) {
    global $DB, $CFG;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

    $iassign_ilm_configs = $DB->get_records('iassign_ilm_config', array('iassign_ilmid' => $ilm_id)); //MOOC 2016
    /*if (!$iassign_ilm_configs) return;*/

    $files_jar = $iassign_ilm->file_jar;

    $zip_filename = $CFG->dataroot . '/temp/ilm-' . iassign_utils::format_pathname($iassign_ilm->name . '-v' . $iassign_ilm->version) . '_' . date("Ymd-Hi") . '.ipz';
    $zip = new zip_archive;
    $zip->open($zip_filename);

    $rootdir = $CFG->dirroot . '/mod/iassign/' . $files_jar;

    $first_folder = str_replace($CFG->dirroot . '/mod/iassign/ilm/', "", $rootdir);
    $zip->add_directory($first_folder);

    $allfiles = self::list_directory($rootdir);
    $i = 0;

    foreach ($allfiles as $file) {
      $mini = str_replace($CFG->dirroot . '/mod/iassign/ilm/', "", $file);
      $mini = str_replace('//', "/", $mini);

      if (is_dir($file)) {
        $zip->add_directory($mini);
      } else {
        $zip->add_file_from_pathname($mini, $file);
        }
      }

    $folder = str_replace('ilm/', "", $files_jar);
    $application_descriptor = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $application_descriptor .= '<application xmlns="http://line.ime.usp.br/application/1.5">' . "\n";
    $application_descriptor .= '  <name>' . $iassign_ilm->name . '</name>' . "\n";
    $application_descriptor .= '  <url>' . $iassign_ilm->url . '</url>' . "\n";
    $application_descriptor .= '  <version>' . $iassign_ilm->version . '</version>' . "\n";
    $application_descriptor .= '  <type>' . $iassign_ilm->type . '</type>' . "\n";
    $application_descriptor .= '  <description>' . html_entity_decode(str_replace(array('<p>', '</p>'), array('', ''), $iassign_ilm->description)) . '</description>' . "\n";
    $application_descriptor .= '  <extension>' . $iassign_ilm->extension . '</extension>' . "\n";
    $application_descriptor .= '  <file_jar>' . $folder . '</file_jar>' . "\n";
    $application_descriptor .= '  <file_class>' . $iassign_ilm->file_class . '</file_class>' . "\n";
    $application_descriptor .= '  <width>' . $iassign_ilm->width . '</width>' . "\n";
    $application_descriptor .= '  <height>' . $iassign_ilm->height . '</height>' . "\n";
    $application_descriptor .= '  <evaluate>' . $iassign_ilm->evaluate . '</evaluate>' . "\n";
    $application_descriptor .= '  <reevaluate>' . $iassign_ilm->reevaluate . '</reevaluate>' . "\n";
    $application_descriptor .= '  <editingbehavior>' . $iassign_ilm->editingbehavior . '</editingbehavior>' . "\n";
    $application_descriptor .= '  <submissionbehavior>' . $iassign_ilm->submissionbehavior . '</submissionbehavior>' . "\n";
    $application_descriptor .= '  <action_buttons>' .
    $iassign_ilm->action_buttons . '</action_buttons>' . "\n";

    if ($iassign_ilm_configs) { //MOOC 2016
      $application_descriptor .= '   <params>' . "\n";
      foreach ($iassign_ilm_configs as $iassign_ilm_config) {
        $application_descriptor .= '    <param>' . "\n";
        $application_descriptor .= '     <type>' . $iassign_ilm_config->param_type . '</type>' . "\n";
        $application_descriptor .= '     <name>' . $iassign_ilm_config->param_name . '</name>' . "\n";
        $application_descriptor .= '     <value>' . $iassign_ilm_config->param_value . '</value>' . "\n";
        $application_descriptor .= '     <description>' . htmlentities(str_replace("\n", "", $iassign_ilm_config->description)) . '</description>' . "\n";
        $application_descriptor .= '     <visible>' . $iassign_ilm_config->visible . '</visible>' . "\n";
        $application_descriptor .= '    </param>' . "\n";
        }
      $application_descriptor .= '   </params>' . "\n";
      } //MOOC 2016

    $application_descriptor .= '</application>' . "\n";

    $zip->add_file_from_string('ilm-application.xml', $application_descriptor);
    $zip->close();

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=\"" . basename($zip_filename) . "\";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . (filesize($zip_filename)));
    set_time_limit(0);
    readfile($zip_filename) || die("File not found.");
    unlink($zip_filename);
    exit;
    } // public static function export_ilm($ilm_id)


  /// Function for list the directory where iLM is allocated.
  //  @param type $dir
  //  @return type
  static function list_directory ($dir) {
    $files = array();
    $cont = 0;
    $ffs = scandir($dir);
    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);
    if (count($ffs) < 1) {
      return;
      }
    foreach ($ffs as $ff) {
      $files[$cont] = $dir . "/" . $ff;
      $cont++;
      if (is_dir($dir . '/' . $ff)) {
        $temp = self::list_directory($dir . '/' . $ff);
        foreach ($temp as $t) {
            $files[$cont] = $t;
            $cont++;
          }
        }
      }
    return $files;
    }


  public static function delete_ilm($ilm_id) {
    global $DB, $CFG, $OUTPUT;

    $iassign_ilm = $DB->get_record('iassign_ilm', array('id' => $ilm_id));

    // Prepare the path of directory to be removed
    $path_w = rtrim($iassign_ilm->file_jar, "/");
    $folder_to_remove = substr($path_w, 0, strrpos($path_w, '/') + 1);

    // Check if the iLM directory is writable
    if (!is_writable($iassign_ilm->file_jar)) {
      return null;
      }

    self::delete_dir($folder_to_remove);

    $ilm_folder = "ilm/" . $iassign_ilm->name . "/";
    $k = 0;

    // Verify if iLM parent directory is empty, if yes, remove it
    foreach(glob($ilm_folder . "*", GLOB_ONLYDIR) as $dir) {
      $k ++;
      break;
      }

    if ($k == 0) {
      self::delete_dir($ilm_folder);
      }

    $DB->delete_records("iassign_ilm", array('id' => $ilm_id));
    $DB->delete_records("iassign_ilm_config", array('iassign_ilmid' => $ilm_id)); //MOOC 2016
    // log event --------------------------------------------------------------------------------------
    iassign_log::add_log('delete_iassign_ilm', 'name: ' . $iassign_ilm->name . ' ' . $iassign_ilm->version, 0, $iassign_ilm->id);
    // log event --------------------------------------------------------------------------------------

    return $iassign_ilm->parent;
    }


  /// Receive the updated data from an iLM to process it
  //  @calledby locallib.php: static function edit_ilm($param,$itemid): $typec::edit_ilm($param,$itemid,$files_extract,$contextuser);
  public static function edit_ilm ($param, $itemid, $files_extract, $contextuser) {
    global $DB, $CFG;

    $iassign_ilm = new stdClass();
    $iassign_ilm->name = $param->name;
    $iassign_ilm->version = $param->version;
    $iassign_ilm->file_jar = $param->file_jar;

    $file_jar = null;
    if (!is_null($files_extract)) { // if there is a new iLM (from IPZ pachage)
      $file_jar = self::save_ilm_by_xml(null, $files_extract);
      if ($file_jar == null) {
        return false;
        }
      $file_jar = str_replace("./", "", $file_jar) . "/";
      }
    if (is_null($file_jar)) {
      // there is not definition to 'file_jar' in the IPZ packege, use eventually something entered by the form
      $file_jar = $param->file_jar;
      }

    $description = json_decode($param->description_lang);
    $description->{$param->set_lang} = $param->description;

    $updentry = new stdClass();
    $updentry->id = $param->id;
    $updentry->version = $param->version;
    $updentry->url = $param->url;

    // $updentry->description = json_encode($description); //R strip_tags(json_encode($description)); // will replace any special character by tags
    // $updentry->description = html_entity_decode((String)$param->description_lang); - the same as above
    $updentry->description = json_encode($description, JSON_UNESCAPED_UNICODE); // encode using UTF8

    $updentry->extension = strtolower($param->extension);

    if (!is_null($file_jar)) {
      $updentry->file_jar = $file_jar;
      }

    $updentry->file_class = $param->file_class;
    $updentry->width = $param->width;
    $updentry->height = $param->height;
    $updentry->enable = $param->enable;
    $updentry->timemodified = $param->timemodified;
    $updentry->evaluate = $param->evaluate;
    //D echo "./ilm_handlers/html5.php: edit_ilm(...): file_jar=$file_jar, file_class=" . $updentry->file_class . "<br/>"; //D exit;

    $DB->update_record("iassign_ilm", $updentry);

    // log event --------------------------------------------------------------------------------------
    iassign_log::add_log('update_iassign_ilm', 'name: ' . $param->name . ' ' . $param->version, 0, $param->id);
    // log event --------------------------------------------------------------------------------------
    } // public static function edit_ilm($param, $itemid, $files_extract, $contextuser)


  /// Function for save iLM file in moodledata
  //  @param int $itemid Itemid of file save in draft (upload file)
  //  @param int $ilm_id Id of iLM
  //  @return string Return an string with ids of iLM files
  static function new_file_ilm ($itemid, $fs, $contextuser, $contextsystem, $files_ilm) {
    global $CFG, $USER, $DB;

    if ($files_ilm) {

      foreach ($files_ilm as $value) {
        // Check if it is an HTML5 iLM
        // Copy:
        $destination = 'ilm_debug/' . $value->get_filename();
        $value->copy_content_to($destination);

        // Extract the content:
        $zip = new ZipArchive();
        $extracted = './ilm';
        $dir = "";
        if ($zip->open($destination) === TRUE) {
          $dir = './ilm/' . trim($zip->getNameIndex(0));
          if (is_dir($dir)) {
            $i = 1;
            $previous = str_replace("/", "", $zip->getNameIndex(0));

            while (file_exists('./ilm/' . $previous . "_" . $i)) {
              $i ++;
              }

            $name = $previous . "_" . $i;
            $dir = './ilm/' . $name . "/";
            $j = 0;
            while ($item_name = $zip->getNameIndex($j)) {
              $zip->renameIndex($j, str_replace($previous, $name, $item_name));
              $j++;
              }
            $zip->close();
            }

          $zip->open($destination);
          $zip->extractTo($extracted);
          $zip->close();

          // After extract, remove from debug:
          unlink($destination);
        } else { // if ($zip->open($destination) === TRUE)
          // After trying to extract, occurring error, erase ZIP file:
          unlink($destination);

          print_error('error_add_ilm_zip', 'iassign');
          }

        return $dir;
        } // foreach ($files_ilm as $value)
      } // if ($files_ilm)
    } // static function new_file_ilm($itemid, $fs, $contextuser, $contextsystem, $files_ilm)


  /// Register data to the new iLM (in database and in proper directory)
  public static function new_ilm ($itemid, $files_extract, $application_xml, $contextuser, $fs) {
    global $DB, $CFG, $USER, $OUTPUT;

    $description_str = str_replace(array('<description>', '</description>'), array('', ''), $application_xml->description->asXML());

    $iassign_ilm = $DB->get_record('iassign_ilm', array("name" => (String) $application_xml->name, "version" => (String) $application_xml->version));
    if ($iassign_ilm) {
      foreach ($files_extract as $key => $value) {
        $rootfolder = $CFG->dataroot . '/temp/' . $key;
        self::delete_dir($rootfolder);
        break;
        }

      print($OUTPUT->notification(get_string('error_import_ilm_version', 'iassign'), 'notifyproblem')); // There is another iLM with the sama version!
      return false;
      }
    else {
      $file_jar = self::save_ilm_by_xml($application_xml, $files_extract);
     if ($file_jar == null) {
       return false;
       }

      $file_jar = str_replace("./", "", $file_jar);

      if (empty($file_jar)) {
        $msg_error = get_string('error_add_ilm', 'iassign') . "<br/>In new_ilm: file_jar empty, files_extract=" . $files_extract . "<br/>\n";
        print_error($msg_error);
        //xx print_error('error_add_ilm', 'iassign');
        //print("New file = " . file_jar . "<br/>");	  
        }
      else { // if (empty($file_jar))
        $iassign_ilm = $DB->get_record('iassign_ilm', array("parent" => 0, "name" => (String) $application_xml->name));
        if (!$iassign_ilm) {
          $iassign_ilm = new stdClass(); //MOOC 2016
          $iassign_ilm->id = 0;
          }

        $newentry = new stdClass();
        $newentry->name = (String) $application_xml->name;
        $newentry->version = (String) $application_xml->version;
        $newentry->type = (String) $application_xml->type;
        $newentry->url = (String) $application_xml->url;
        $newentry->description = strip_tags($description_str);
        $newentry->extension = strtolower((String) $application_xml->extension);
        $newentry->file_jar = $file_jar . "/";

        $newentry->file_class = (String) $application_xml->file_class;
        $newentry->width = (String) $application_xml->width;
        $newentry->height = (String) $application_xml->height;
        $newentry->enable = 0;
        $newentry->timemodified = time();
        $newentry->author = $USER->id;
        $newentry->timecreated = time();
        $newentry->evaluate = (String) $application_xml->evaluate;
        $newentry->reevaluate = (String) $application_xml->reevaluate;
        $newentry->parent = $iassign_ilm->id;
        $newentry->editingbehavior = (String) $application_xml->editingbehavior;
        $newentry->submissionbehavior = (String) $application_xml->submissionbehavior;
        $newentry->action_buttons = (String) $application_xml->action_buttons;

        $newentry->id = $DB->insert_record("iassign_ilm", $newentry);

        // log event --------------------------------------------------------------------------------------
        iassign_log::add_log('add_iassign_ilm', 'name: ' . $newentry->name . ' ' . $newentry->version, 0, $newentry->id);
        // log event --------------------------------------------------------------------------------------

        if ($application_xml->params->param) {
          foreach ($application_xml->params->param as $value) {
            $newentry = new stdClass();
            $newentry->iassign_ilmid = $iassign_ilmid;
            $newentry->param_type = (String) $value->type;
            $newentry->param_name = (String) $value->name;
            $newentry->param_value = (String) $value->value;
            $newentry->description = html_entity_decode((String) $value->description);
            $newentry->visible = (String) $value->visible;

            $newentry->id = $DB->insert_record("iassign_ilm", $newentry);

            if (!$newentry->id) {
              print_error('error_add_param', 'iassign');
              }
            }
          }
        }
      }

    $fs->delete_area_files($contextuser->id, 'user', 'draft', $itemid);
    return true;
    } // public static function new_ilm($itemid, $files_extract, $application_xml, $contextuser, $fs)


  /// Import an iLM
  public static function import_ilm ($itemid, $files_extract, $application_xml, $contextuser, $fs) {
    global $DB, $CFG, $USER, $OUTPUT;

    $description_str = str_replace(array('<description>', '</description>'), array('', ''), $application_xml->description->asXML());

    $iassign_ilm = $DB->get_record('iassign_ilm', array("name" => (String) $application_xml->name, "version" => (String) $application_xml->version));
    if ($iassign_ilm) {
      foreach ($files_extract as $key => $value) {
        $rootfolder = $CFG->dataroot . '/temp/' . $key;
        self::delete_dir($rootfolder);
        break;
        }

      print($OUTPUT->notification(get_string('error_import_ilm_version', 'iassign'), 'notifyproblem'));
      }
    else {
      $file_jar = self::save_ilm_by_xml($application_xml, $files_extract);

      if ($file_jar == null) {
        return false;
        }

      $file_jar = str_replace("./", "", $file_jar);

      if (empty($file_jar)) {
        $msg_error = get_string('error_add_ilm', 'iassign') . "<br/>In import_ilm: file_jar empty, files_extract=" . $files_extract . "<br/>\n";
        print_error($msg_error);
        //xx print_error('error_add_ilm', 'iassign');
        //print("Import file = " . file_jar . "<br/>");	  
        }	
      else { // if (empty($file_jar))
        $iassign_ilm = $DB->get_record('iassign_ilm', array("parent" => 0, "name" => (String) $application_xml->name));
        if (!$iassign_ilm) {
          $iassign_ilm = new stdClass(); //MOOC 2016
          $iassign_ilm->id = 0;
          }

        $newentry = new stdClass();
        $newentry->name = (String) $application_xml->name;
        $newentry->version = (String) $application_xml->version;
        $newentry->type = (String) $application_xml->type;
        $newentry->url = (String) $application_xml->url;
        $newentry->description = strip_tags($description_str);
        $newentry->extension = strtolower((String) $application_xml->extension);
        $newentry->file_jar = $file_jar . "/";

        $newentry->file_class = (String) $application_xml->file_class;
        $newentry->width = (String) $application_xml->width;
        $newentry->height = (String) $application_xml->height;
        $newentry->enable = 0;
        $newentry->timemodified = time();
        $newentry->author = $USER->id;
        $newentry->timecreated = time();
        $newentry->evaluate = (String) $application_xml->evaluate;
        $newentry->reevaluate = (String) $application_xml->reevaluate;
        $newentry->editingbehavior = (String) $application_xml->editingbehavior;
        $newentry->submissionbehavior = (String) $application_xml->submissionbehavior;
        $newentry->action_buttons = (String) $application_xml->action_buttons;
        $newentry->parent = $iassign_ilm->id;

        //MOOC 2016 $newentry->id = $DB->insert_record("iassign_ilm", $newentry);
        $iassign_ilmid = $DB->insert_record("iassign_ilm", $newentry);
        if ($application_xml->params->param) {
          foreach ($application_xml->params->param as $value) {
            $newentry = new stdClass();
            $newentry->iassign_ilmid = $iassign_ilmid;
            $newentry->param_type = (String) $value->type;
            $newentry->param_name = (String) $value->name;
            $newentry->param_value = (String) $value->value;
            $newentry->description = html_entity_decode((String) $value->description);
            $newentry->visible = (String) $value->visible;

            $newentry->id = $DB->insert_record("iassign_ilm", $newentry);

            if (!$newentry->id) {
              print_error('error_add_param', 'iassign');
              }
            }
          } // if ($application_xml->params->param)
        } // else if (empty($file_jar))

      print($OUTPUT->notification(get_string('ok_import_ilm_version', 'iassign'), 'notifysuccess'));
      }

    $fs->delete_area_files($contextuser->id, 'user', 'draft', $itemid);
    } // public static function import_ilm($itemid, $files_extract, $application_xml, $contextuser, $fs)

  // static function export_update_ilm($ilm_id) //MOOC 2016
  /// Function for save iLM from XML descriptor
  //  @see html5.php : copy_new_version_ilm($param,$files_extract); edit_ilm($param,$itemid,$files_extract,$contextuser); new_ilm($itemid,$files_extract,$application_xml,$contextuser,$fs); import_ilm($itemid,$files_extract,$application_xml,$contextuser,$fs);
  //  @param array $application_xml Data of XML descriptor
  //  @param array $files_extract Filenames of extract files
  //  @return string with the iLM path (since ./ilm/...)
  static function save_ilm_by_xml ($application_xml, $files_extract) {
    global $CFG, $USER, $OUTPUT;

    $source = "";
    $diretorio = "";

    // Check if the iLM directory is writable
    if (!is_writable("ilm/")) {
      print($OUTPUT->notification(get_string('error_folder_permission_denied', 'iassign'), 'notifyproblem'));
      exit;
      // return null; // Is safer to stop the processing in order to allow the reader of this error! 
      }

    // The filter 'filename_from_iLM_name(...)' is essential to clear the name for directory from the iLM original name
    $application_file_name =  iassign_utils::filename_from_iLM_name($application_xml->name, false); //2019

    // Check if iLM directory already exists
    if (!file_exists("ilm/" . $application_file_name)) {
      mkdir("ilm/" . $application_file_name, 0755, true); // 755 = rwxr-xr-x
      touch("ilm/" . $application_file_name . DIRECTORY_SEPARATOR . "index.html");
      }

    // Check if iLM version already exists in directory
    if (!file_exists("ilm/" . $application_file_name . "/" . $application_xml->version)) {
      mkdir("ilm/" . $application_file_name . DIRECTORY_SEPARATOR . $application_xml->version, 0755, true);
      touch("ilm/" . $application_file_name . DIRECTORY_SEPARATOR . $application_xml->version . DIRECTORY_SEPARATOR . "index.html");
      }
    else {
      $diretorio = "./" . "ilm/" . $application_file_name . DIRECTORY_SEPARATOR . $application_xml->version;
      //RR echo $diretorio."<br/>";
      print($OUTPUT->notification(get_string('error_import_ilm_version', 'iassign'), 'notifyproblem'));
      //RR return null;
      return $diretorio;
      }

    $root_ilm = "ilm/" . $application_file_name . "/" . $application_xml->version;
    // Extract iLM files to directory
    foreach ($files_extract as $key => $value) {
      $file = $CFG->dataroot . '/temp/' . $key;

      if (is_dir($file)) {
        $source = $file;
        $diretorio = $root_ilm . "/" . basename($file);
        // mkdir($diretorio, 0777, true);
        mkdir($diretorio, 0755, true);
        touch($diretorio . DIRECTORY_SEPARATOR . "index.html");
        break;
        }
      } // foreach ($files_extract as $key => $value)

    // Write in the MoodleData 'temp' directory, also in the WWW 'mod/iassign/ilm/'
    //D foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item
    foreach ($iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
      $directory_name = $diretorio . DIRECTORY_SEPARATOR . $iterator->getSubPathName();	
      if ($item->isDir()) {
        mkdir($directory_name);
        touch($directory_name . DIRECTORY_SEPARATOR . "index.html");
        } 
      else {
        copy($item, $directory_name);
        }
      }

    self::delete_dir($source);
    return "./" . $diretorio;
    } // static function save_ilm_by_xml($application_xml, $files_extract)


  /// Function for delete directory where the iLM is allocated.
  //  @param string $dirPath
  //  @throws InvalidArgumentException
  public static function delete_dir ($dirPath) {
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
      $dirPath .= '/';
      }
    $files = glob($dirPath . '*', GLOB_MARK); //TODO This option 'glob' do not get file with dot (like '.versions'), chang to 'scandir($dir)'...
    foreach ($files as $file) {
      if (is_dir($file)) { //TODO if ($file != '.' && $file != '..')
        self::delete_dir($file);
        }
      else {
        unlink($file);
        }
      }
    if (is_dir($dirPath))
      rmdir($dirPath); //TODO  Warning: rmdir(/var/data/moodle_data_saw/temp/ivprog-html/js/semantic/): Directory not empty in /var/www/html/saw_clone/mod/iassign/ilm_handlers/html5.php on line 962
    else
      print($OUTPUT->notification(get_string('error_file_jar_exists', 'iassign'), 'notifyproblem')); // just warning the user
    }

  } // class html5 implements ilm_handle

?>
