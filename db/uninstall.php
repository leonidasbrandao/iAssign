<?php

/**
 * This file replaces the legacy STATEMENTS section in db/install.xml,
 * lib.php/modulename_install() post installation hook and partially defaults.php
 *
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.2 2013/08/20
 * @package mod_iassign_db
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/// Moodle core defines constant MOODLE_INTERNAL which shall be used to make sure that the script is included and not called directly.
defined('MOODLE_INTERNAL') || die();

/// This is called at the beginning of the uninstallation process to give the module
//  a chance to clean-up its hacks, bits etc. where possible.
//  @return bool Return true if success.

require_once ($CFG->dirroot . '/mod/iassign/locallib.php');

function xmldb_iassign_uninstall () {
  global $DB;
  $fs = get_file_storage();

  $context_system = context_system::instance();
  $fs->delete_area_files($context_system->id, 'mod_iassign', 'ilm');

  $courses = $DB->get_records('course');
  foreach ($courses as $course) {
    $context_course = context_course::instance($course->id);
    $fs->delete_area_files($context_course->id, 'mod_iassign', 'exercise');
    $fs->delete_area_files($context_course->id, 'mod_iassign', 'activity');
    }

  return true;
  }
