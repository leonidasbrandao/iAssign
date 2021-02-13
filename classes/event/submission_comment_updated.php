<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The mod_iassign submission comment update event.
 *
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.0 2015/07/12
 * @package mod_iassign
 * @since 2015/07/12
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later 
 */

namespace mod_iassign\event;

defined('MOODLE_INTERNAL') || die();

class submission_comment_updated extends \core\event\base {

  /**
   * Init method.
   */
  protected function init () {
    $this->data['crud'] = 'u'; // c(reate), r(ead), u(pdate), d(elete)
    $this->data['edulevel'] = self::LEVEL_PARTICIPATING; // LEVEL_TEACHING , LEVEL_PARTICIPATING, LEVEL_OTHER
    $this->data['objecttable'] = 'iassign';
  }

  /**
   * Returns localised general event name.
   *
   * @return string
   */
  public static function get_name () {
    return get_string('eventsubmissioncommentupdated', 'mod_iassign');
  }

  /**
   * Returns non-localised event description with id's for admin use only.
   *
   * @return string
   */
  public function get_description () {
    return "The user with id '$this->userid' has viewed  activity submission of comment updated the iAssign with id '$this->objectid' in " .
        "the iAssign activity with course module id '$this->contextinstanceid'.";
  }

  /**
   * Get URL related to the action.
   *
   * @return \moodle_url
   */
  public function get_url () {
    return new \moodle_url('/mod/iassign/view.php', array('id' => $this->contextinstanceid));
  }

  /**
   * Return the legacy event log data.
   *
   * @return array|null
   */
  public function get_legacy_logdata () {

    return array($this->courseid, 'iassign', 'update comment',
      "view.php?id={$this->contextinstanceid}",
      $this->objectid, $this->contextinstanceid);
  }

  /**
   * Custom validation.
   *
   * @throws \coding_exception
   * @return void
   */
  protected function validate_data () {
    parent::validate_data();
    // Make sure this class is never used without proper object details.
    if(!$this->contextlevel === CONTEXT_MODULE) {
      throw new \coding_exception('Context level must be CONTEXT_MODULE.');
    }
  }

}
