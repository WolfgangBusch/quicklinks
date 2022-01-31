<?php
/*
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Januar 2022
 */
#
# --- generate the Quicklinks modules
require_once __DIR__.'/lib/class.quicklinks_install.php';
require_once __DIR__.'/lib/class.quicklinks.php';
quicklinks_install::build_modules($this->getPackageId());
#
# --- generate the Quicklinks stylesheet file
$data=quicklinks::read_config_data();
quicklinks::file_css($data);
?>
