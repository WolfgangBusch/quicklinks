<?php
/**
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Dezember 2021
 */
#
# --- generate the Quicklinks modules
require_once __DIR__.'/lib/class.quicklinks_install.php';
require_once __DIR__.'/lib/class.quicklinks.php';
quicklinks_install::update_modules();
#
# --- generate the Quicklinks stylesheet
$data=quicklinks::read_config_data();
quicklinks::file_css($data);
?>
