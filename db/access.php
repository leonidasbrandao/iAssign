<?php

/**
 * Capability definitions for the ia module.
 * 
 * The system has four possible values for a capability:
 *  - CAP_ALLOW
 *  - CAP_PREVENT
 *  - CAP_PROHIBIT
 *  - CAP_INHERIT
 * Hardening_new_Roles_system: <a href="http://docs.moodle.org/en/Development">http://docs.moodle.org/en/Development</a>
 *  - manager - manageristrators can do anything at all courses.
 *  - coursecreator - Creators can create new courses to courses and act as teachers.
 *  - editingteacher - Teachers can do everything on a course, change, and evaluate activities.
 *  - teacher - Moderators can interact and evaluate but can not modify the activities.
 *  - student - Students usually have fewer privileges on a course.
 *  - guest - Guests have minimal privileges and can not publish texts.
 *  - user  - All users who login.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.0 2010/09/27
 * @package mod_iassign_db
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$capabilities = array(
  'mod/iassign:view' => array(
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
    'legacy' => array(
      'guest' => CAP_ALLOW,
      'student' => CAP_ALLOW,
      'teacher' => CAP_ALLOW,
      'editingteacher' => CAP_ALLOW,
      'coursecreator' => CAP_ALLOW,
      'manager' => CAP_ALLOW
    )
  ),
  'mod/iassign:editiassign' => array(
    'captype' => 'write',
    'contextlevel' => CONTEXT_MODULE,
    'legacy' => array(
      'editingteacher' => CAP_ALLOW,
      'coursecreator' => CAP_ALLOW,
      'manager' => CAP_ALLOW
    )
  ),
  'mod/iassign:evaluateiassign' => array(
    'riskbitmask' => RISK_XSS,
    'captype' => 'write',
    'contextlevel' => CONTEXT_MODULE,
    'legacy' => array(
      'teacher' => CAP_ALLOW,
      'editingteacher' => CAP_ALLOW,
      'coursecreator' => CAP_ALLOW,
      'manager' => CAP_ALLOW
    )
  ),
  'mod/iassign:viewiassignall' => array(
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
    'legacy' => array(
      'teacher' => CAP_ALLOW,
      'editingteacher' => CAP_ALLOW,
      'coursecreator' => CAP_ALLOW,
      'manager' => CAP_ALLOW
    )
  ),
  'mod/iassign:viewreport' => array(
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
    'legacy' => array(
      'teacher' => CAP_ALLOW,
      'editingteacher' => CAP_ALLOW,
      'coursecreator' => CAP_ALLOW,
      'manager' => CAP_ALLOW
    )
  ),
  'mod/iassign:submitiassign' => array(
    'captype' => 'write',
    'contextlevel' => CONTEXT_MODULE,
    'legacy' => array(
      'student' => CAP_ALLOW
    )
  ),
  'mod/iassign:deleteiassignnull' => array(
    'riskbitmask' => RISK_XSS,
    'captype' => 'write',
    'contextlevel' => CONTEXT_MODULE,
    'legacy' => array(
      'editingteacher' => CAP_ALLOW,
      'coursecreator' => CAP_ALLOW,
      'manager' => CAP_ALLOW
    )
  ),
  'mod/iassign:deleteiassignnotnull' => array(
    'riskbitmask' => RISK_XSS,
    'captype' => 'write',
    'contextlevel' => CONTEXT_MODULE,
    'legacy' => array(
      'manager' => CAP_ALLOW
    )
  ),
  'mod/iassign:addinstance' => array(
    'riskbitmask' => RISK_XSS,
    'captype' => 'write',
    'contextlevel' => CONTEXT_COURSE,
    'archetypes' => array(
      'editingteacher' => CAP_ALLOW,
      'manager' => CAP_ALLOW
    ),
    'clonepermissionsfrom' => 'moodle/course:manageactivities'
  ),
  'mod/iassign:runautoevaluate' => array(
    'captype' => 'read',
    'contextlevel' => CONTEXT_MODULE,
    'legacy' => array(
      'teacher' => CAP_ALLOW,
      'editingteacher' => CAP_ALLOW,
      'coursecreator' => CAP_ALLOW,
      'manager' => CAP_ALLOW
    )
  )
);
