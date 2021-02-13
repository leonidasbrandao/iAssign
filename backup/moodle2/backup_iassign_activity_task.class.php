<?php

/**
 * This file contains the backup task for the iAssign module
 * 
 * Release Notes:
 * - v 1.1 2014/01/06
 * 		+ Fix bug in activity name, remove tag filter (backup_iassign_activity_task::define_my_settings).
 *
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.1 2014/01/06
 * @package mod_iassign_backup
 * @since 2012
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  
 * @see backup_activity_task
 */
/**
 * Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/iassign/backup/moodle2/backup_iassign_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the iAssign instance
 * @see backup_activity_task
 */
class backup_iassign_activity_task extends backup_activity_task {

  /**
   * No specific settings for this activity
   */
  protected function define_my_settings () {

    //TODO Retirar quando atualizar todo os iassigns que estão com a tag &lt;ia_uc&gt;
    $temp = explode("&lt;ia_uc&gt;", $this->name);
    $this->name = $temp[0];
  }

  /**
   * Defines a backup step to store the instance data in the iassign.xml file
   */
  protected function define_my_steps () {
    $this->add_step(new backup_iassign_activity_structure_step('iassign_structure', 'iassign.xml'));
  }

  /**
   * Encodes URLs to various iAssign scripts
   * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
   * @return string The content with the URLs encoded
   */
  static public function encode_content_links ($content) {
    global $CFG;

    $base = preg_quote($CFG->wwwroot, "/");
    //  $base = preg_quote($CFG->wwwroot.'/mod/iassign','#');
    // Link to the list of choices
    $search = "/(" . $base . "\/mod\/iassign\/index.php\?id\=)([0-9]+)/";
    $content = preg_replace($search, '$@IASSIGNINDEX*$2@$', $content);

    // Link to choice view by moduleid
    $search = "/(" . $base . "\/mod\/iassign\/view.php\?id\=)([0-9]+)/";
    $content = preg_replace($search, '$@IASSIGNVIEWBYID*$2@$', $content);

    return $content;
  }

}
