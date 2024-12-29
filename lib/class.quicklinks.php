<?php
/*
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Dezember 2024
 */
#
class quicklinks {
#
#----------------------------------------- Inhaltsuebersicht
#   Boot
#      quicklinks_groups_article()
#      set_class_variables()
#   Konfiguration
#      get_config_keys()
#      get_default_data()
#      check_data($data)
#      set_config_data($data)
#      define_css($data)
#      file_css($data)
#      read_zero($key,$confdat)
#      read_config_data()
#      print_form()
#   Backend
#      xmp_linklists()
#      xmp_comments()
#      show_internal_quicklinks($grp,$list)
#      show_external_quicklinks($grp,$val)
#   Frontend
#      get_internal_links($slice_id)
#      get_external_links($slice_id)
#      get_linklists()
#      get_quicklinks($grplinks)
#      switch_icons()
#   Output
#      print_quicklinks()
#      print_xmp_quicklinks()
#
#----------------------------------------- Boot
const this_addon      ='quicklinks';
const max_width_mobile=35;               // Smartphone display size 'max-width:...em'
const icon_3points    ='icon_3points';   // Icon Id 'show Quicklinks'
const icon_cross      ='icon_cross';     // Icon Id 'hide Quicklinks'
public static $slices =array();          // array of slices of the Quicklinks groups
#
public static function quicklinks_groups_article() {
   #   Returning the articles containing internal Quicklinks groups.
   #   Exactly one such article should be returned. And this article should
   #   contain the external Quicklinks groups, too.
   #
   $sql=rex_sql::factory();
   #
   # --- Select the internal Quicklinks from rex_article_slice (unordered)
   #     internal Quicklinks module Id
   $query='SELECT * FROM rex_module WHERE output LIKE \'%quicklinks::show_internal_quicklinks%\'';
   $modint=$sql->getArray($query);
   $modint_id=$modint[0]['id'];
   #     internal Quicklinks article Id
   $query='SELECT * FROM rex_article_slice WHERE module_id='.$modint_id.' ORDER BY priority';
   $slicint=$sql->getArray($query);
   $aid=array();
   $m=0;
   for($i=0;$i<count($slicint);$i=$i+1):
      $id=$slicint[$i]['article_id'];
      $vorhanden=FALSE;
      for($k=1;$k<=$m;$k=$k+1)
         if($aid[$k]==$id):
           $vorhanden=TRUE;
           break;
           endif;
      if(!$vorhanden):
        $m=$m+1;
        $aid[$m]=$id;
        endif;
      endfor;
   return $aid;
   }
public static function set_class_variables($artid=0) {
   #   Setting the class variables.
   #      self::$slices:   Numbered array of parameters of Quicklinks slices
   #                       (numbering starting at 1). Each slice is represented
   #                       as an associate array with these keys and values:
   #                          ['id']   slice Id
   #                          ['typ']  type, values = 'intern' or  'extern'
   #   $artid           Id of the article containing the Quicklinks groups
   #                    =0: find the first article containing the groups
   #   benutzte functions:
   #      self::quicklinks_groups_article()
   #
   $art_id=$artid;
   if($art_id<=0):
     $aid=self::quicklinks_groups_article();
     if(count($aid)<=0) return;
     $art_id=$aid[1];
     endif;
   $sql=rex_sql::factory();
   #
   # --- Select the internal Quicklinks from rex_article_slice (unordered)
   $query='SELECT * FROM rex_module WHERE output LIKE \'%show_internal_quicklinks%\'';
   $modint=$sql->getArray($query);
   $modint_id=$modint[0]['id'];
   $query='SELECT * FROM rex_article_slice WHERE module_id='.$modint_id.' AND article_id='.$art_id.' ORDER BY priority';
   $slicint=$sql->getArray($query);
   #
   # --- Select the external Quicklinks from rex_article_slice (unordered)
   $query='SELECT * FROM rex_module WHERE output LIKE \'%show_external_quicklinks%\'';
   $modext=$sql->getArray($query);
   $modext_id=$modext[0]['id'];
   $query='SELECT * FROM rex_article_slice WHERE module_id='.$modext_id.' AND article_id='.$art_id.' ORDER BY priority';
   $slicext=$sql->getArray($query);
   #
   # --- Quicklinks slices (Ids and types, ordered)
   $sid=array();
   for($i=0;$i<count($slicint);$i=$i+1):
      $prio=$slicint[$i]['priority'];
      $sid[$prio]['id'] =$slicint[$i]['id'];
      $sid[$prio]['typ']='intern';
      endfor;
   for($i=0;$i<count($slicext);$i=$i+1):
      $prio=$slicext[$i]['priority'];
      $sid[$prio]['id'] =$slicext[$i]['id'];
      $sid[$prio]['typ']='extern';
      endfor;
   for($i=1;$i<=count($sid);$i=$i+1):
      self::$slices[$i]['id'] =$sid[$i]['id'];
      self::$slices[$i]['typ']=$sid[$i]['typ'];
      endfor;
   }
#
#----------------------------------------- Konfiguration
public static function get_config_keys() {
   #   Returns the keys of the configuration data for the Quicklinks stylesheet
   #   as a numbered array (numbering starting at 0).
   #
   return array(
      'width',        // width of the Quicklink cell (number of pixels)
      'size',         // size of the Quicklink link text (number of pixels)
      'radius',       // border radius of the Quicklink cell (number of pixels)
      'quick_backgr', // Quicklink cell background color: rgba(red,green,blue,1)
      'quick_border', // Quicklink cell border color:     rgba(red,green,blue,1)
      'quick_text',   // Quicklink cell text color:       rgba(red,green,blue,1)
      'popup_backgr', // PopUp cell background color:     rgba(red,green,blue,opac)
      'popup_text');  // PopUp cell text color:           rgba(red,green,blue,1)
   }
public static function get_default_data() {
   #   Returns the Quicklinks default configuration data as an associative array.
   #   used functions:
   #      get_config_keys()
   #
   $keys=self::get_config_keys();
   return array(
      $keys[0] => 0,
      $keys[1] => 0,
      $keys[2] => 0,
      $keys[3] => 'rgba(204,102, 51,1)',
      $keys[4] => 'rgba(255,190, 60,1)',
      $keys[5] => 'rgba(255,255,255,1)',
      $keys[6] => 'rgba(240,120, 60,1)',
      $keys[7] => 'rgba(255,255,255,1)');
   }
public static function check_data($data) {
   #   checks content and structure of an array that shall be set as configuration 
   #   data for the Quicklinks stylesheet. In case of wrong data an error string
   #   is returned.
   #   $data            given data
   #   used functions:
   #      self::get_config_keys()
   #
   $keys   =array_keys($data);
   $defkeys=self::get_config_keys();
   #
   # --- falsche Key-Anzahl?
   if(count($keys)<>count($defkeys)) return '+++++ Schlüsselanzahl falsch';
   #
   # --- falscher Key-Wert
   for($i=0;$i<count($keys);$i=$i+1):
      $ke=$keys[$i];
      $str=FALSE;
      for($k=0;$k<count($defkeys);$k=$k+1)
         if($defkeys[$k]==$ke):
           $str=TRUE;
           break;
           endif;
      if(!$str) return '+++++ falscher Schlüssel';           
      endfor;
   #
   # --- empty content?
   $empty=0;
   for($i=0;$i<count($defkeys);$i=$i+1)
      if(empty($data[$keys[$i]])) $empty=$empty+1;
   if($empty>=count($defkeys)) return '+++++ leere Konfigurationsdaten';
   }
public static function set_config_data($data) {
   #   Sets the configuration data for the Quicklinks stylesheet.
   #   $data            Data array to be set as configuration data.
   #                    In case of empty data or wrong array structure
   #                    the default data will be set instead.
   #   used functions:
   #      self::get_default_data()
   #      self::check_data($data)
   #   used constants:
   #      self::this_addon
   #
   $defdata=self::get_default_data();
   $defkeys=array_keys($defdata);
   #
   # --- check data
   $dat=$data;
   if(!empty(self::check_data($data))) $dat=$defdata;
   #
   # --- set the configuration data
   for($i=0;$i<count($defkeys);$i=$i+1):
      $key=$defkeys[$i];
      $da=$dat[$key];
      $ida=intval($da);
      if($ida>0 or strval($da)=='0') $da=$ida;
      rex_config::set(self::this_addon,$key,$da);
      endfor;
   }
public static function define_css($data) {
   #   Returns a string with the contents of the stylesheet for the Quicklinks.
   #   $data            given array of data for the stylesheet
   #   used functions:
   #      self::check_data()
   #      self::get_default_data()
   #   used variables:
   #      self::$slices
   #   used constants:
   #      self::this_addon
   #      self::max_width_mobile
   #      self::icon_3points
   #      self::icon_cross
   #
   # --- set css variables
   $dat=$data;
   if(!empty(self::check_data($data))) $dat=self::get_default_data();
   $keys=array_keys($dat);
   $width  =$dat[$keys[0]];
   $size   =$dat[$keys[1]];
   $borrad =$dat[$keys[2]];
   $qbgcol =$dat[$keys[3]];
   $qborcol=$dat[$keys[4]];
   $qtxtcol=$dat[$keys[5]];
   $pbgcol =$dat[$keys[6]];
   $ptxtcol=$dat[$keys[7]];
   #
   # --- number of Quicklinks groups
   $anzgrps=count(self::$slices);
   #
   # --- data dependent on font size
   if($size>0):
     $fontsize=$size.'px';
     $popsize=strval(intval(0.8*$size)).'px';
     else:
     $size=16;
     $fontsize='inherit';
     $popsize='smaller';
     endif;
   $ql_linhei=$size+3;   // line-height
   $lineheight=strval($ql_linhei).'px';
   $ql_margt=$ql_linhei+4;   // line-height + padding-top  (ul.quicklink li)
   #
   # --- data dependent on Quicklinks group width
   if($width>0):
     $ql_width=$width;
     $liwidth =$width.'px';
     $lipad   ='4px 0 4px 0';
     $st_width=$anzgrps*$ql_width.'px';   // total width of all adjacent groups
     $divwidth=intval($ql_width-2).'px';
     $mapop='1px 0 0 0';
     else:
     $ql_width=170;
     $liwidth ='auto';
     $lipad   ='4px 8px 4px 8px';
     $st_width='auto';
     $divwidth='auto';
     $mapop='1px 0 0 -8px';
     endif;
   $ql_divwidth=$ql_width-2;
   $ql_widthpl=$ql_width+7;  // QL-Breite + padding-left/right - Rand (ul.quicklink li)
   #
   # --- generate the stylesheet instructions
   $str=
'/*   S t y l e s h e e t    f o r    Q u i c k l i n k s   */
ul.quicklink {
    margin:0; padding:0; list-style-type:none; }
ul.quicklink li {
    border-collapse:collapse; box-sizing:border-box; float:left; width:'.$liwidth.';
    padding:'.$lipad.'; white-space:nowrap; text-align:center; font-size:'.$fontsize.';
    background-color:'.$qbgcol.'; color:'.$qtxtcol.';
    border:solid 1px '.$qborcol.'; border-radius:'.$borrad.'px; }
ul.quicklink li div.quicklink_popup {
    position:absolute; min-width:'.$divwidth.'; margin:'.$mapop.'; padding:4px 0 4px 0;
    line-height:1.1; text-align:left; border-style:none; visibility:hidden;
    background-color:'.$pbgcol.'; }
ul.quicklink li div.quicklink_popup a {
    padding:0 4px 0 4px; text-decoration:none; font-size:'.$popsize.'; color:'.$ptxtcol.'; }
@media screen and (max-width:'.self::max_width_mobile.'em) {
    ul.quicklink li {
        float:none; width:'.$ql_width.'px; padding-left:8px;
        line-height:'.$lineheight.'; text-align:left; }
    ul.quicklink li div.quicklink_popup {
        max-width:'.$ql_divwidth.'px; min-width:'.$ql_divwidth.'px; overflow:hidden;
        text-align:right; margin-top:-'.$ql_margt.'px; margin-left:-'.$ql_widthpl.'px; } }
    
/*   d i s p l a y e d    o r    h i d d e n   */
#'.self::this_addon.' { display:block;
    max-width:'.$st_width.'; min-width:'.$st_width.'; }
@media screen and (max-width:'.self::max_width_mobile.'em) {
    #'.self::this_addon.' { display:none; position:fixed; right:5px; top:2em;
        max-width:'.$ql_width.'px; min-width:'.$ql_width.'px; } }
    
/*   3 - p o i n t - / c r o s s - I c o n   */
#'.self::icon_3points.' { display:none; }
#'.self::icon_cross.'   { display:none; }
@media screen and (max-width:'.self::max_width_mobile.'em) {
    #'.self::icon_3points.' { display:block;
        cursor:pointer; position:fixed; right:0.5em; top:0.5em; width:28px; }
    #'.self::icon_cross.'   { display:none;
        cursor:pointer; position:fixed; right:0.5em; top:0.5em; width:28px; }
    div.'.self::icon_3points.' { margin:2px 4px 4px 12px; width:3px; height:3px;
        background-color:'.$pbgcol.'; }
    div.'.self::icon_cross.'1  { margin:4px; height:2px; background-color:'.$pbgcol.';
        transform:translateY(6px) rotate(45deg); }
    div.'.self::icon_cross.'2  { margin:4px; height:2px; background-color:transparent; }
    div.'.self::icon_cross.'3  { margin:4px; height:2px; background-color:'.$pbgcol.';
        transform:translateY(-6px) rotate(-45deg); } }';
   #
   # --- for backend functions
   $str=$str.'

/*   for backend functions   */
table.ql_inherit   { background-color:inherit; }
td.ql_indent, div.ql_indent { padding-left:20px; }
td.ql_right, input.ql_right { text-align:right; }
td.ql_nowrap       { white-space:nowrap; }
td.ql_center       { text-align:center; }
td.ql_smaller, span.ql_smaller { width:80px; font-size:smaller; }
input.ql_width     { width:60px; }
input.ql_height    { height:1.5em; }
input.ql_bold      { font-weight:bold; }
p.ql_error         { color:red; }';
   #
   return $str;
   }
public static function file_css($data) {
   #   Writes the Quicklinks stylesheet file into the assets directory
   #   /assets/addons/quicklinks/.
   #   $data            given array of data for the stylesheet
   #   used functions:
   #      self::define_css($data)
   #   used constants:
   #      self::this_addon
   #
   # --- generate the stylesheet
   $buffer=self::define_css($data);
   #
   # --- write the stylesheet file
   $dir=rex_path::addonAssets(self::this_addon);
   #     after installation, the AddOn assets folder is not set up immediately
   if(!file_exists($dir)) mkdir($dir);
   $file=$dir.self::this_addon.'.css';
   $handle=fopen($file,'w');
   fwrite($handle,$buffer);
   fclose($handle);
   }
public static function read_zero($key,$confdat) {
   #   Reads and returns $_POST-parameter in order to determine new configuration values.
   #   In the case of entering empty or '0' the value '0' is returned (instead of empty).
   #   $key             given $_POST-key
   #   $confdat         corresponding actual configured value
   #
   $post='';
   if(!empty($_POST["$key"])) $post=trim($_POST["$key"]);
   if(empty($post)):
     if(empty($_POST['sendit']) and empty($_POST['reset'])):
       # --- Replace empty values by already configured values
       $post=$confdat;
       else:
       # --- Value 0
       $post='0';
       endif;
     endif;
   return $post;
   }
public static function read_config_data() {
   #   Returns the input values for the configuration data in an associative array.
   #   Empty input values are replaced by already configured values. If there are
   #   no configured data empty values are replaced by default values. The rgb color
   #   parameter are restricted to integer values between 0 and 255, if necessary.
   #   used functions:
   #      self::get_default_data()
   #      self::read_zero($key,$confdat)
   #   used constants:
   #      self::this_addon
   #
   $defdat=self::get_default_data();
   $dkeys=array_keys($defdat);
   #
   # --- Return the actually configured data after reset
   if(!empty($_POST['reset'])) return $defdat;
   #
   # --- actual configuration data
   $confdat=rex_config::get(self::this_addon);
   #     no configuration data: take the default data
   if(count($confdat)<=0):
     $confdat=$defdat;
     echo rex_view::warning('Noch keine Konfigurationsdaten erfasst. Angezeigt werden Defaultwerte.');
     endif;
   #     data from former AddOn versions: change 'rgb(r,g,b)' to 'rgba(r,g,b,1)'
   for($i=0;$i<count($dkeys);$i=$i+1):
      $key=$dkeys[$i];
      $val=$confdat[$key];
      if(substr($val,0,4)!='rgb(') continue;
      $val=str_replace('rgb(','rgba(',$val);
      $val=str_replace(')',',1)',$val);
      $confdat[$key]=$val;
      endfor;
   #
   # --- Keys for the input data
   $keys=array();
   $anzset=0;
   for($i=0;$i<count($dkeys);$i=$i+1):
      $key=$dkeys[$i];
      $cdat='';
      if(isset($confdat[$key])):
        $cdat=$confdat[$key];
        $anzset=$anzset+1;
        endif;
      if(substr($cdat,0,5)!='rgba('):
        $keys[$i]=$dkeys[$i];
        $iw=$i;
        else:
        $m=($i-$iw-1)*3+$iw;
        $keys[$m+1]=$dkeys[$i].'_red';
        $keys[$m+2]=$dkeys[$i].'_green';
        $keys[$m+3]=$dkeys[$i].'_blue';
        endif;
      endfor;
   #
   # --- Read input values
   $data=array();
   for($i=0;$i<count($dkeys);$i=$i+1):
      $key=$dkeys[$i];
      $cdat='';
      if(isset($confdat[$key])) $cdat=$confdat[$key];
      if(substr($cdat,0,5)!='rgba('):
        #
        # --- Dimensions
        $data[$key]=self::read_zero($key,$cdat);
        $iw=$i;
        else:
        #
        # --- Colors
        $po=explode('(',$cdat);
        $po=explode(')',$po[1]);
        $po=explode(',',$po[0]);
        $dat='rgba(';
        $m=($i-$iw-1)*3+$iw;
        for($j=1;$j<=3;$j=$j+1):
           $ke=$keys[$m+$j];
           $post=self::read_zero($ke,$po[$j-1]);
           if(intval($post)>255) $post='255';
           $dat=$dat.$post.',';
           endfor;
        $opacval=$po[3];
        #     opacity of PopUps background color
        if($i==6 and isset($_POST['opac'])) $opacval=$_POST['opac'];
        $data[$key]=substr($dat,0,strlen($dat)-1).','.$opacval.')';
        endif;
      endfor;
   return $data;
   }
public static function print_form() {
   #   Displays the form for entering the stylesheet configuration data containing
   #   the actual data and replaces the configuration with the entered data.
   #   used functions:
   #      self::read_config_data()
   #      self::set_config_data($data)
   #      self::file_css($data)
   #
   # --- Data entered via the form or default data after reset, respectively
   $data=self::read_config_data();
   #
   # --- Save data and re-write stylesheet file
   if(!empty($_POST['reset']) or !empty($_POST['sendit'])):
     self::set_config_data($data);
     self::file_css($data);
     endif;
   #
   # --- Set data for the input form
   $str=array();
   $keys=array_keys($data);
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=$data[$key];
      if($i==0):   // box width = defined group width
        if($val>0):
          $boxwidth=$val;
          $txtwidth='&lt;- '.$boxwidth.'px -&gt;';
          else:
          $boxwidth=150;
          $txtwidth='';
          endif;
        endif;
      if($i==1):   // take the defined font size
        if($val>0):
          $fontsize=$val.'px';
          $popsize=intval(0.8*$fontsize).'px';
          else:
          $fontsize='inherit';
          $popsize='smaller';
          endif;
        endif;
      $datin[$i]=$val;
      if(substr($val,0,5)!='rgba('):
        $str[$i]='class="form-control ql_right ql_width" name="'.$key.'" value="'.$val.'"';
        else:
        #   $val='rgba(rrr,ggg,bbb,1)';
        $arr=explode(',',$val);
        $red  =intval(substr($arr[0],5));
        $green=intval($arr[1]);
        $blue =intval($arr[2]);
        $kr=$key.'_red';
        $kg=$key.'_green';
        $kb=$key.'_blue';
        $str[$i]=array(
           'red'=>   'class="form-control ql_right ql_width" name="'.$kr.'" value="'.$red.'"',
           'green'=> 'class="form-control ql_right ql_width" name="'.$kg.'" value="'.$green.'"',
           'blue'=>  'class="form-control ql_right ql_width" name="'.$kb.'" value="'.$blue.'"');
        endif;
      if($i==6):   // opacity from PopUps background color
        $arr=explode(',',$val);
        $opac=trim(substr($arr[3],0,strlen($arr[3])-1));
        if($opac<=0 or $opac>1):
          echo rex_view::warning('Falscher Wert für die Deckkraft: <code>'.$opac.'</code>. Korrigiert auf <code>1</code>.');
          $opac=1;
          endif;
        $str[8]='class="form-control ql_right ql_width" name="opac" value="'.$opac.'"';
        endif;
      endfor;
   #
   # --- The input form
   $html='
<form method="post">
<table class="ql_inherit">';
   #
   # --- Width and font size
   $html=$html.'
    <tr><td class="ql_indent ql_nowrap">
            Breite einer Quicklinks-Gruppe <b>(*)</b></td>
        <td class="ql_indent ql_smaller">
            <input '.$str[0].' />
        <td colspan="3" class="ql_indent ql_smaller ql_nowrap">
            px &nbsp;(Anzahl Pixel) &nbsp;
            0:&nbsp; keine feste Breite, &quot;auto&quot;</td></tr>
    <tr><td class="ql_indent ql_nowrap">
            Schriftgröße der Quicklinks-Texte</td>
        <td class="ql_indent ql_smaller">
            <input '.$str[1].' />
        <td colspan="3" class="ql_indent ql_smaller ql_nowrap">
            px &nbsp; (Anzahl Pixel) &nbsp;
            0:&nbsp; keine feste Größe, &quot;auto&quot;</td></tr>
    <tr><td class="ql_indent ql_nowrap">
            Eckenradius einer Quicklinks-Gruppe</td>
        <td class="ql_indent ql_smaller">
            <input '.$str[2].' />
        <td colspan="3" class="ql_indent ql_smaller ql_nowrap">
            px &nbsp; (Anzahl Pixel) &nbsp;
            0:&nbsp; Ecken nicht abgerundet</td></tr>';
   #
   # --- Headline colors
   $html=$html.'
    <tr><td class="ql_indent ql_smaller ql_right ql_indent">Farben (RGB-Werte)</td>
        <td class="ql_indent ql_smaller ql_center">rot</td>
        <td class="ql_indent ql_smaller ql_center">grün</td>
        <td class="ql_indent ql_smaller ql_center">blau</td>
        <td class="ql_indent ql_smaller ql_center">Darstellung</td></tr>';
   #
   # --- Quicklinks background color
   $html=$html.'
    <tr><td class="ql_indent ql_nowrap">Quicklinks-Hintergrundfarbe</td>
        <td class="ql_indent ql_nowrap"><input '.$str[3]['red'].' /></td>
        <td class="ql_indent ql_nowrap"><input '.$str[3]['green'].' /></td>
        <td class="ql_indent ql_nowrap"><input '.$str[3]['blue'].' /></td>
        <td class="ql_indent ql_nowrap">
            <input class="form-control ql_height" type="text" value="'.$txtwidth.'"
                   style="width:'.$boxwidth.'px;
                          text-align:center; font-size:smaller;
                          border:solid 3px '.$datin[3].';
                          background-color:'.$datin[3].';
                          color:'.$datin[5].';" /></td></tr>';
   #
   # --- Quicklinks border color
   $html=$html.'
    <tr><td class="ql_indent ql_nowrap">
            Quicklinks-Randfarbe <span class="ql_indent ql_smaller">(feste Randdicke 1px)</span></td>
        <td class="ql_indent ql_nowrap"><input '.$str[4]['red'].' /></td>
        <td class="ql_indent ql_nowrap"><input '.$str[4]['green'].' /></td>
        <td class="ql_indent ql_nowrap"><input '.$str[4]['blue'].' /></td>
        <td class="ql_indent ql_nowrap">
            <input class="form-control ql_height" type="text" value=""
                   style="width:'.$boxwidth.'px;
                          background-color:white;
                          border:solid 3px '.$datin[4].';
                          border-radius:'.$datin[2].'px;" /></td></tr>';
   #
   # --- Quicklinks text color
   $html=$html.'
    <tr><td class="ql_indent ql_nowrap">Quicklinks-Textfarbe</td>
        <td class="ql_indent ql_nowrap"><input '.$str[5]['red'].' /></td>
        <td class="ql_indent ql_nowrap"><input '.$str[5]['green'].' /></td>
        <td class="ql_indent ql_nowrap"><input '.$str[5]['blue'].' /></td>
        <td class="ql_indent ql_nowrap">
            <input class="form-control" type="text" value="Quicklink"
                   style="width:'.$boxwidth.'px;
                          text-align:center;
                          font-size:'.$fontsize.';
                          border:solid 3px '.$datin[4].';
                          border-radius:'.$datin[2].'px;
                          background-color:'.$datin[3].';
                          color:'.$datin[5].';" /></td></tr>';
   #
   # --- PopUps background color
   $html=$html.'
    <tr><td class="ql_indent ql_nowrap">PopUps-Hintergrundfarbe</td>
        <td class="ql_indent ql_nowrap"><input '.$str[6]['red'].' /></td>
        <td class="ql_indent ql_nowrap"><input '.$str[6]['green'].' /></td>
        <td class="ql_indent ql_nowrap"><input '.$str[6]['blue'].' /></td>
        <td class="ql_indent ql_nowrap">
            <input class="form-control ql_height" type="text" value=""
                   style="width:'.$boxwidth.'px;
                          border:none;
                          background-color:'.$datin[6].';" /></td></tr>';
   #
   # --- PopUps text color
   $html=$html.'
    <tr><td class="ql_indent ql_nowrap">PopUps-Textfarbe</td>
        <td class="ql_indent ql_nowrap"><input '.$str[7]['red'].' /></td>
        <td class="ql_indent ql_nowrap"><input '.$str[7]['green'].' /></td>
        <td class="ql_indent ql_nowrap"><input '.$str[7]['blue'].' /></td>
        <td class="ql_indent ql_nowrap">
            <input class="form-control" type="text" value="PopUp"
                   style="width:'.$boxwidth.'px;
                          font-size:'.$popsize.';
                          border:none;
                          background-color:'.$datin[6].';
                          color:'.$datin[7].';" /></td></tr>';
   #
   # --- Opacity of PopUps background color
   $html=$html.'
    <tr><td class="ql_indent ql_nowrap">
            Deckkraft der PopUps-Hintergrundfarbe</td>
        <td class="ql_indent ql_smaller">
            <input '.$str[8].' />
        <td colspan="3" class="ql_indent ql_smaller ql_nowrap">
            (transparent [0] ... bis ... undurchsichtig [1])</td></tr>';
   #
   # --- Buttons
   $restit='auf Defaultwerte zurücksetzen und speichern';
   $html=$html.'
    <tr><td class="ql_indent ql_nowrap"><br>
            <button class="btn btn-save" type="submit" name="sendit" value="sendit"
                    title=" speichern "> speichern </button></td>
        <td colspan="4" class="ql_indent ql_smaller"><br>
            <button class="btn btn-update" type="submit" name="reset" value="reset"
                    title="'.$restit.'"> '.$restit.' </button></td></tr>';
   #
   # --- Completion of the form
   $html=$html.'
</table>
</form>';
   #
   # --- Notice on width
   $html=$html.'
<div class="ql_indent"><br><b>(*) Zur Breite der Quicklinks-Gruppen:</b><br>
<div class="ql_indent">Ein <u>Parameterwert&gt;0 definiert eine einheitliche feste
Breite</u> der Gruppen. Eine zu lange Gruppenbezeichnung kann daher ggf. abgeschnitten
werden. Alternativ erzeugt der <u>Parameterwert 0 eine flexible Breite</u> der Gruppen,
festgelegt durch die jeweilige Textlänge der Gruppenbezeichnung. Die Breite des
PopUp-Menüs der Linkzeilen unter der Gruppe wird unabhängig von der Gruppenbreite
durch die Textlänge der längsten Zeile bestimmt.</div></div>';
   echo $html;
   }
#
#----------------------------------------- Backend
public static function xmp_linklists() {
   #   Returns 3 groups of artificial internal and external Quicklinks to be
   #   displayed as an Quicklinks example, represented as a numbered array
   #   (numbering starting at 1). Each group contains a numbered array of
   #   Quicklinks (numbering starting at 1). Each Quicklink consists of an
   #   associative array with these keys and values:
   #     ['group_nr']   group number according to slice priority
   #                    number in the Quicklinks article
   #     ['group_name'] group identifier
   #     ['article_id'] Id of an Redaxo article or '-1' (external link)
   #     ['url']        URL of an external link
   #     ['ref']        link text of an external link
   #     ['title']      title text of an external link
   #   used constants:
   #      self::this_addon
   #
   $url=rex_url::backend('index.php?page='.self::this_addon.'/introduction');
   $ind=array(1=>4, 2=>3, 3=>2);
   $groups=array(1=>'Über uns', 2=>'Aktuelles', 3=>'Externe Links');
   $ref=array();
   $tit=array();
   $ref[1]=array(1=>'Verband', 2=>'Kontakt', 3=>'Impressum', 4=>'Datenschutzerklärung');
   $tit[1]=array(1=>'Unser Verband',
                 2=>'Telefon und E-Mail-Adressen der Vorstandsmitglieder',
                 3=>'Impressum',
                 4=>'Datenschutzerklärung');
   $ref[2]=array(1=>'Terminkalender', 2=>'neuer Vorstand', 3=>'Herbstausflug');
   $tit[2]=array(1=>'Alle Termine auf einen Blick',
                 2=>'neuer Vorstand gewählt auf der Verbandsversammlung',
                 3=>'Einladung zum alljährlichen Herbstausflug');
   $ref[3]=array(1=>'TTVN', 2=>'Süddeutsche Zeitung');
   $tit[3]=array(1=>'Tischtennisverband Niedersachsen',
                 2=>'Süddeutscher Verlag (Süddeutsche Zeitung)');
   $links=array();
   for($m=1;$m<=count($ind);$m=$m+1):
      $grp=$groups[$m];
      $nr=$m;
      for($i=1;$i<=$ind[$m];$i=$i+1):
         $uri=$url;
         if($m==3 and $i==1) $uri='http://ttvn.de/';
         if($m==3 and $i==2) $uri='http://www.sueddeutsche.de/';
         $links[$m][$i]['group_nr']  =$nr;
         $links[$m][$i]['group_name']=$grp;
         $links[$m][$i]['article_id']='-1';
         $links[$m][$i]['url']       =$uri;
         $links[$m][$i]['ref']       =$ref[$m][$i];
         $links[$m][$i]['title']     =$tit[$m][$i];
         endfor;
      endfor;
   return $links;
   }
public static function xmp_comments() {
   #   Returns the HTML code for the desription of the Quicklinks example groups.
   #   used constants:
   #      self::this_addon
   #
   $width=rex_addon::get(self::this_addon)->getConfig('width');
   if($width<=0):
     $width='0';
     $zus='Daher sind Gruppenüberschrift und Linkzeilen - unabhängig voneinander - '.
          'so breit, wie ihre Texte es verlangen.';
     else:
     $zus='Daher wird die Gruppenüberschrift ggf. rechts abgeschnitten, '.
          'während die Linkzeilen mindestens so breit sind, wie ihre Texte es verlangen.';
     endif;
   $str='
<div>Im Beispiel sind die unten aufgeführten <b>Quicklinks-Gruppen</b> so eingerichtet:</div>
<ul>
    <li>Alle internen Links sind identisch (mit unterschiedlichen Linktexten).</li>
    <li>Die externen Links sind real und werden in einem neuen Browserfenster angezeigt.</li>
</ul>
<div>Die äußere Form entspricht der gewählten Konfiguration '.
'(Breite der Quicklinks-Gruppen: '.$width.' Pixel). '.$zus.'<br>&nbsp;</div>';
   return $str;
   }
public static function show_internal_quicklinks($grp,$list) {
   #   Returns the html code for printing some parameters of a group of internal
   #   Quicklinks (used in backend).
   #   $grp                name of the Quicklinks group
   #   $list               comma-separated string of links (linklist)
   #
   if(empty($grp)) return '
<p class="ql_error">Gruppenname ist leer!</p>';
   #
   $str='
<div>Quicklinks-Gruppe <b>"'.trim($grp).'"</b></div>
<table>';
   #
   # --- link list
   if(!empty($list)):
     $links=explode(',',$list);
     for($i=0;$i<count($links);$i=$i+1):
        $art_id=$links[$i];
        $url=rex_getUrl($art_id);
        $art=rex_article::get($art_id);
        $name=$art->getName();
        $ref=$name;
        $tit=$name;
        $nurl='';
        if(!empty($url)) $nurl='('.$url.')';
        $str=$str.'
    <tr><td class="ql_indent">
            <a href="'.$url.'" title="'.$tit.'" target="_blank">
           '.$ref.'</a></td>
        <td class="ql_indent">
            <small>'.$nurl.'</small></td></tr>';
        endfor;
     else:
     $str=$str.'
    <tr><td class="ql_indent">
            ---</td>
        <td class="ql_indent">
            <small>(Gruppe ist leer)</small></td></tr>';
     endif;
   $str=$str.'
</table>';
   return $str;
   }
public static function show_external_quicklinks($grp,$val) {
   #   Returns the html code for printing some parameters of a group of internal
   #   Quicklinks (used in backend).
   #   $val                array of Redaxo variables REX_VALUE[1], ..., REX_VALUE[11]
   #
   if(empty($grp)) return '
<p class="ql_error">Gruppenname ist leer!</p>';
   #
   $str='
<div>Quicklinks-Gruppe <b>"'.trim($grp).'"</b></div>
<table>';
   #
   # --- link list
   $list='';
   for($i=1;$i<=10;$i=$i+1):
      $arr=explode(';',$val[$i]);
      $url=trim($arr[0]);
      $ref=trim($arr[1]);
      $tit=trim($arr[2]);
      if(empty($url) and empty($ref) and empty($tit)) continue;
      $list=$list.'
    <tr><td class="ql_indent">
            <a href="'.$url.'" title="'.$tit.'" target="_blank">
            '.$ref.'</a></td>
        <td class="ql_indent">
            <small>('.$url.')</small></td></tr>';
      endfor;
   if(empty($list)) $list='
    <tr><td class="ql_indent">
            ---</td>
        <td class="ql_indent">
            <small>(Gruppe ist leer)</small></td></tr>';
   $str=$str.$list.'
</table>';
   return $str;
   }
#
#----------------------------------------- Frontend
public static function get_internal_links($slice_id) {
   #   Returns the parameters of a group of internal Quicklinks defined in an article
   #   slice. The group is represented as a numbered array (numbering starting at 1).
   #   Each Quicklink is represented as an associative array with these keys and values:
   #     ['group_nr']   group number = slice priority number 
   #     ['group_name'] group identifier
   #     ['article_id'] Id of the Redaxo article
   #     ['url']        article URL (via rex_getUrl())
   #     ['ref']        article name as link text
   #     ['title']      article name as link title
   #   $slice_id        Id of the given slice group
   #
   $slice=rex_article_slice::getArticleSliceById($slice_id);
   $art_id=$slice->getArticleId();
   $grp_name=$slice->getValue(11);
   $grp_nr  =$slice->getValue('priority');
   $linklist=$slice->getLinkList(1);
   $grplink=array();
   #
   if(!empty($linklist)):
     $arr=explode(',',$linklist);
     for($k=0;$k<count($arr);$k=$k+1):
        $aid=$arr[$k];
        $name=rex_article::get($aid)->getName();
        $grplink[$k+1]['group_nr']  =$grp_nr;
        $grplink[$k+1]['group_name']=$grp_name;
        $grplink[$k+1]['article_id']=$aid;
        $grplink[$k+1]['url']       =rex_getUrl($aid);
        $grplink[$k+1]['ref']       =$name;
        $grplink[$k+1]['title']     =$name;
        endfor;
     else:
     $grplink[1]['group_nr']  =$grp_nr;
     $grplink[1]['group_name']=$grp_name;
     $grplink[1]['article_id']=0;
     endif;
   return $grplink;
   }
public static function get_external_links($slice_id) {
   #   Returns the parameters of a group of external Quicklinks defined in an article
   #   slice as a numbered array (numbering starting at 1, max. 10 elements).
   #   Each Quicklink consists of an associative array with these keys and values:
   #     ['group_nr']   group number = slice priority number 
   #     ['group_name'] group identifier
   #     ['article_id'] ='-1'
   #     ['url']        URL of an external link
   #     ['ref']        link text of an external link
   #     ['title']      title text of an external link
   #   $slice_id        Id of the given slice group
   #
   $slice=rex_article_slice::getArticleSliceById($slice_id);
   $art_id=$slice->getArticleId();
   $grp_name=$slice->getValue(11);
   $grp_nr=$slice->getValue('priority');
   #
   $grplink=array();
   for($k=1;$k<=10;$k=$k+1):
      $val=$slice->getValue($k);
      if(empty($val)) continue;
      $arr=explode(';',$val);
      $grplink[$k]['group_nr']  =$grp_nr;
      $grplink[$k]['group_name']=$grp_name;
      $grplink[$k]['article_id']='-1';
      $grplink[$k]['url']       =trim($arr[0]);
      $grplink[$k]['ref']       =trim($arr[1]);
      $grplink[$k]['title']     =trim($arr[2]);
      endfor;
   if(count($grplink)<=0):
     $grplink[1]['group_nr']  =$grp_nr;
     $grplink[1]['group_name']=$grp_name;
     $grplink[1]['article_id']='-1';
     endif;
   return $grplink;
   }
public static function get_linklists() {
   #   Returns all groups of internal and external Quicklinks as a numbered
   #   array (numbering starting at 1), ordered according to the slice priority
   #   in the Quicklinks article. Each group is represented as a numbered array
   #   of Quicklinks (numbering starting at 1). Each Quicklink consists of an
   #   associative array with these keys and values:
   #     ['group_nr']   group number according to slice priority
   #                    number in the Quicklinks article
   #     ['group_name'] group identifier
   #     ['article_id'] Id of an Redaxo article or '-1' (external link)
   #     ['url']        URL of an external link
   #     ['ref']        link text of an external link
   #     ['title']      title text of an external link
   #   used functions:
   #      self::get_internal_links($slice_id)
   #      self::get_external_links($slice_id)
   #   used variables:
   #      self::$slices
   #
   $slic=self::$slices;
   $grplinks=array();
   for($i=1;$i<=count($slic);$i=$i+1):
      if($slic[$i]['typ']=='intern'):
        $grplinks[$i]=self::get_internal_links($slic[$i]['id']);
        else:
        $grplinks[$i]=self::get_external_links($slic[$i]['id']);
        endif;
      endfor;
   return $grplinks;
   }
public static function switch_icons() {
   #   Returns the HTML Code of the Icons showing or hiding the Quicklinks
   #   based on a javascript function.
   #   used constants:
   #      self::this_addon
   #      self::icon_3points
   #      self::icon_cross
   #
   $title1='Quicklinks anzeigen';
   $title2='Quicklinks verbergen';
   return '
<div id="'.self::icon_3points.'">
    <a href="#" title="'.$title1.'"
       onClick="ql_show_hide(\''.self::this_addon.'\',\''.self::icon_3points.'\',\''.self::icon_cross.'\');">
    <div class="'.self::icon_3points.'"></div>
    <div class="'.self::icon_3points.'"></div>
    <div class="'.self::icon_3points.'"></div></a>
</div>
<div id="'.self::icon_cross.'">
    <a href="#" title="'.$title2.'"
       onClick="ql_show_hide(\''.self::this_addon.'\',\''.self::icon_3points.'\',\''.self::icon_cross.'\');">
    <div class="'.self::icon_cross.'1"></div>
    <div class="'.self::icon_cross.'2"></div>
    <div class="'.self::icon_cross.'3"></div></a>
</div>
';
   }
public static function get_quicklinks($grplinks) {
   #   Returns the HTML code of the Quicklinks as side-by-side Quicklinks groups.
   #   The links of a group PopUp menu when crossing over with the mouse.
   #   $grplinks        Numbered array of defined group of Quicklinks
   #                    (numbering starting at 1). Each group of Quicklinks is
   #                    represented as a numbered array of related Quicklinks
   #                    (numbering starting at 1). Each Quicklink consists of
   #                    an associative array with these keys and elements:
   #     ['group_name'] Identifier (headline) of the group
   #     ['article_id'] Article-Id of the page to which the link points
   #                    (internal links)
   #     ['url']        URL of the page to which the link points
   #     ['ref']        link text
   #     ['title']      title text (displayed when crossing over with the mouse)
   #
   # --- String for Javascript functions
   $box='box';   // will be extended to: box1, box2, ...
   $div='pop';   // will be extended to: pop1, pop2, ...
   #
   # --- Cells of the Quicklinks groups
   $anz=count($grplinks);
   $li=array();
   for($i=1;$i<=$anz;$i=$i+1):
      $grplink=$grplinks[$i];
      #
      # --- Headline Box
      $title=$grplink[1]['group_name'];
      $ev='onmouseover="quickNewStyle('.$i.'); quickMenu('.$i.','.$anz.');" '.
          'onmouseout="quickStdStyle('.$i.'); quickCleanDelay('.$anz.');"';
      $titms='title="'.$title.'" '.$ev;
      #
      # --- Lines of the PopUp menus
      $text='';
      for($k=1;$k<=count($grplink);$k=$k+1):
         $url=$grplink[$k]['url'];
         $ref=$grplink[$k]['ref'];
         $tit=$grplink[$k]['title'];
         if(!empty($url) and !empty($ref)):
           $tar='';
           if(substr($url,0,4)=='http') $tar=' target="_blank"';
           $text=$text.'
            <a href="'.$url.'" title="'.$tit.'"'.$tar.'>'.$ref.'</a><br>';
           else:
           $text='&nbsp;<br>';
           endif;
         endfor;
      $text=substr($text,0,strlen($text)-4);
      #
      # --- Adding of headline and lines
      $li[$i]='
    <li id="box'.$i.'" '.$titms.'>
        '.$title.'
        <div class="quicklink_popup" id="'.$div.$i.'">'.
            $text.'
        </div></li>';
      endfor;
   #
   # --- Assembly of the Quicklinks groups
   $str='
<ul class="quicklink">';
   for($i=1;$i<=$anz;$i=$i+1) $str=$str.$li[$i];
   $str=$str.'
</ul>
';
   return $str;
   }
#----------------------------------------- Output
public static function print_quicklinks() {
   #   Displays the defined Quicklinks groups, side-by-side as a html unordered list.
   #   The links of a group PopUp menu when crossing over with the mouse.
   #   used functions:
   #      self::get_linklists()
   #      self::switch_icons()
   #      self::get_quicklinks($links)
   #
   $grplinks=self::get_linklists();
   echo self::switch_icons().'
<div id="'.self::this_addon.'">'.self::get_quicklinks($grplinks).'</div>';
   }
public static function print_xmp_quicklinks() {
   #   Displays 3 Quicklinks example groups, side-by-side as a html unordered list.
   #   The links of a group PopUp menu when crossing over with the mouse.
   #   used functions:
   #      self::xmp_linklists()
   #      self::xmp_comments();
   #      self::get_quicklinks($links)
   #   used constants:
   #      self::this_addon
   #
   $links=self::xmp_linklists();
   if(count(rex_config::get(self::this_addon))<=0)
     echo rex_view::warning('Noch keine Konfigurationsdaten erfasst. Die Darstellung entspricht Defaultwerten.');
   echo self::xmp_comments().self::get_quicklinks($links);
   }
}
?>
