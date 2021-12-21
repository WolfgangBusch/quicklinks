<?php
/**
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Dezember 2021
 */
require_once __DIR__.'/lib/class.quicklinks.php';
#
# --- include CSS file and JS file in backend
$my_package=$this->getPackageId();
$file=rex_url::addonAssets($my_package).$my_package.'.css';
rex_view::addCssFile($file);
$file=rex_url::addonAssets($my_package).$my_package.'.js';
rex_view::addJsFile($file);
?>
