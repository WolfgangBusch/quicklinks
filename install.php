<?php
/**
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version März 2020
 */
#
# --- generate the Quicklinks modules
require_once __DIR__.'/lib/class.quicklinks_install.php';
$my_package=$this->getPackageId();
quicklinks_install::update_module($my_package,'Artikel-Linkliste',
   'mod_linklist_in','mod_linklist_out');
quicklinks_install::update_module($my_package,'Liste externer Links',
   'mod_ext_linklist_in','mod_ext_linklist_out');
?>
