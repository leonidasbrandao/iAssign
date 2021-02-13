<?php
/** 
 * This file keeps track of upgrades to the lams module.
 * 
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installtion to the current version.
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 * The commands in here will all be database-neutral,
 * using the functions defined in lib/ddllib.php
 * 
 * - v 1.4 2013/09/19
 * 		+ Insert general fields for iassign statement (grade, timeavaliable, timedue, preventlate, test, max_experiment).
 * 		+ Change index field 'name' in 'iassign_ilm' table to index field 'name,version'.
 * - v 1.2 2013/08/30
 * 		+ Change 'filearea' for new concept for files.
 * 		+ Change path file for ilm, consider version in pathname.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.4 2013/09/19
 * @package mod_iassign_db
 * @since 2010/12/21
 * @copyright iMath (http://www.matematica.br) and LInE (http://line.ime.usp.br) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://opensource.org/licenses/gpl-license.php GNU Public License
 *  
 * @param $oldversion Number of the old version. 
 */

require_once ("../../config.php");

echo "TODO: upgrade_files.php: not in use...<br/>\n";

global $CFG, $DB, $USER;

$DB->delete_records('iassign_ilm',array('id'=>'8'));

$ilm_files = $DB->get_records('iassign_ilm');
echo "<pre>";
print_r($ilm_files);
echo "</pre>";

   //foreach ($ilm_files as $ilm_file) {
   //  //$tmp = explode (".",$ilm_file->name);
   //  $ilm_file->file_jar=$ilm_file->name.".jar";
   //  echo $ilm_file->file_jar."</br>";
   //  $DB->update_record('iassign_ilm', $ilm_file);
   //  }
?>
