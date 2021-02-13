<?php

/**
 * Form to add and edit instance of iAssign.
 * 
 * @author Patricia Alves Rodrigues
 * @author Leônidas O. Brandão
 * @version v 1.0 2012/10/15
 * @package mod_iassign
 * @since 2010/09/27
 * @copyright iMatica (<a href="http://www.matematica.br">iMath</a>) - Computer Science Dep. of IME-USP (Brazil)
 * 
 * <b>License</b> 
 *  - http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This class renderer the iassign module.
 * @see plugin_renderer_base
 */
class mod_iassign_renderer extends plugin_renderer_base {

  /**
   * Renderer iassign files.
   * @return string Return an string with a tag html.
   */
  public function iassign_files ($context, $itemid, $filearea = 'iassign') {
    return $this->render(new iassign_files($context, $itemid, $filearea));
    }

  }
