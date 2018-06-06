<?php
/**
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Juli 2018
 */
#
class quicklinks {
#
public static function get_default_data() {
   #   Quicklinks default stylesheet data as associative array:
   #     [width]          width of the Quicklink cell (number of pixels)
   #     [size]           size of the Quicklink link text (number of points)
   #     [radius]         border radius of the Quicklinks cell (number of pixels)
   #                      colors each as string like "rgb(red,green,blue)":
   #     [quick_backgr]   Quicklink cell background color
   #     [quick_border]   Quicklink cell border color
   #     [quick_text]     Quicklink cell text color
   #     [popup_backgr]   PopUp cell background color
   #     [popup_text]     PopUp cell text color
   #
   return array(
      "width"       =>0,
      "size"        =>0,
      "radius"      =>0,
      "quick_backgr"=>"rgb(204,103, 51)",
      "quick_border"=>"rgb(255,190, 60)",
      "quick_text"  =>"rgb(255,255,255)",
      "popup_backgr"=>"rgb(240,120, 60)",
      "popup_text"  =>"rgb(255,255,255)");
   }
public static function proof_data($data) {
   #   proof content and structure of an array that shall be set
   #   as configuration data for the Quicklinks stylesheet
   #   $data            given data
   #   used functions:
   #      self::get_default_data()
   #
   # --- proof the data structure
   $keys=array_keys($data);
   $defdata=self::get_default_data();
   $defkeys=array_keys($defdata);
   $struc=TRUE;
   for($i=0;$i<count($defkeys);$i=$i+1):
      if($keys[$i]!=$defkeys[$i]):
        $struc=FALSE;
        break;
        endif;
      endfor;
   if(count($keys)!=count($defkeys)) $struc=FALSE;
   if(!$struc) return FALSE;
   #
   # --- empty content?
   $empty=0;
   for($i=0;$i<count($defkeys);$i=$i+1)
      if(empty($data[$keys[$i]])) $empty=$empty+1;
   if($empty>=count($defkeys)) return FALSE;
   return TRUE;
   }
public static function set_config_data($data) {
   #   Set the configuration data for the Quicklinks stylesheet
   #   $data            Data array to be set as configuration data.
   #                    In case of empty data or wrong array structure
   #                    the default data will be set instead.
   #   used functions:
   #      self::get_default_data()
   #      self::proof_data($data)
   #
   $keys=array_keys($data);
   $defdata=self::get_default_data();
   $defkeys=array_keys($defdata);
   #
   # --- proof the data
   $correct=self::proof_data($data);
   #
   # --- set the configuration data
   $dat=$data;
   if(!$correct):
     $dat=$defdata;
     $keys=$defkeys;
     endif;
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $da=$dat[$key];
      $ida=intval($da);
      if($ida>0 or strval($da)=="0") $da=$ida;
      rex_addon::get("quicklinks")->setConfig($key,$da);
      endfor;
   }
public static function get_config_data() {
   #   return the configured data for the Quicklinks stylesheet;
   #   if they are not yet set the default data will be return instead
   #   used functions:
   #      self::get_default_data()
   #
   $defdata=self::get_default_data();
   $keys=array_keys($defdata);
   for($i=0;$i<count($keys);$i=$i+1)
      $data[$keys[$i]]=rex_addon::get("quicklinks")->getConfig($keys[$i]);
   return $data;
   }
public static function define_css($data) {
   #   Return a string with the contents of the stylesheets for the Quicklinks
   #   $data             given array of data for the stylesheet
   #   used functions:
   #      self::proof_data()
   #      self::get_default_data()
   #
   # --- proof data
   $correct=self::proof_data($data);
   #
   # --- set css variables
   $dat=$data;
   if(!$correct) $dat=self::get_default_data();
   $keys=array_keys($dat);
   $width =$dat[$keys[0]];
   $size  =$dat[$keys[1]];
   $borrad=$dat[$keys[2]];
   $q_bg_c=$dat[$keys[3]];
   $q_b_c =$dat[$keys[4]];
   $q_t_c =$dat[$keys[5]];
   $p_bg_c=$dat[$keys[6]];
   $p_t_c =$dat[$keys[7]];
   $quicksize="";
   $popsize="font-size:0.8em";
   if($size>0):
     $quicksize="font-size:".$size."pt";
     $popsize="font-size:".intval(0.8*$size)."pt";
     endif;
   #
   # --- generate the stylesheet instructions
   $str=
'/*   Stylesheet for Quicklinks   */
ul.quicklink {
   margin:0px; padding:0px; list-style-type:none; }
ul.quicklink li {
   display:inline; float:left;
   margin:0px 0px 0px -1px; padding:4px 8px 4px 8px;
   white-space:nowrap; text-align:center; '.$quicksize.';
   background-color:'.$q_bg_c.';
   border:solid 1px '.$q_b_c.'; border-radius:'.$borrad.'px;
   color:'.$q_t_c.'; }
ul.quicklink li div.quicklink_popup {
   position:absolute;
   margin:0px 0px 0px -8px; padding:4px 0px 4px 0px;
   text-align:left;
   background-color:'.$p_bg_c.';
   border-style:none; }
ul.quicklink li div.quicklink_popup a {
   padding:0px 8px 0px 8px; text-decoration:none; '.$popsize.';
   color:'.$p_t_c.'; }';
   #
   # --- addition in case of fixed width
   if($width>0):
     $pwidth=$width+16;
     $str=$str.'
/*   addition in case of fixed width   */
ul.quicklink li {
   width:'.$width.'px; }
ul.quicklink li div.quicklink_popup {
   width:'.$pwidth.'px; }';
     endif;
   #
   return $str;
   }
public static function file_css($data) {
   #   Generate the stylesheet file for Quicklinks in the AddOn assets directory
   #   $data             given array of data for the stylesheet
   #   used functions:
   #      self::define_css($data)
   #
   # --- generate the stylesheet
   $buffer=self::define_css($data);
   #
   # --- generate the file
   $dir=rex_path::addon("quicklinks")."assets";
   $file=$dir."/"."quicklinks".".css";
   $handle=fopen($file,"w");
   fwrite($handle,$buffer);
   fclose($handle);
   }
public static function read_config_data() {
   #   Read configuration data values for the Quicklinks stylesheet via form.
   #   Empty input values are replaced by already configured values.
   #   The rgb colors may be restricted to integer values between
   #   0 and 255, if necessary.
   #   Return the entered values in an associative array.
   #   used functions:
   #      self::_get_default_data()
   #      self::_get_config_data()
   #
   # --- Return the actually configured data after reset
   if(!empty($_POST['reset'])) return self::get_default_data();
   #
   # --- Keys for the input data
   $confdat=self::get_config_data();
   $dkeys=array_keys($confdat);
   for($i=0;$i<count($dkeys);$i=$i+1):
      $cdat=$confdat[$dkeys[$i]];
      if(substr($cdat,0,4)!="rgb("):
        $keys[$i]=$dkeys[$i];
        $iw=$i;
        else:
        $m=($i-$iw-1)*3+$iw;
        $keys[$m+1]=$dkeys[$i]."_red";
        $keys[$m+2]=$dkeys[$i]."_green";
        $keys[$m+3]=$dkeys[$i]."_blue";
        endif;
      endfor;
   #
   # --- Read input values
   for($i=0;$i<count($dkeys);$i=$i+1):
      $key=$dkeys[$i];
      $cdat=$confdat[$key];
      if(substr($cdat,0,4)!="rgb("):
        #
        # --- dimensions
        $post=$_POST["$key"];
        if(intval($post)<0) $post="0";
        # --- Replace empty values by already configured values
        if(empty($post) and strval($post)!="0") $post=$confdat[$key];
        $data[$dkeys[$i]]=$post;
        $iw=$i;
        else:
        #
        # --- colors
        $dat="rgb(";
        $m=($i-$iw-1)*3+$iw;
        for($j=1;$j<=3;$j=$j+1):
           $key=$keys[$m+$j];
           $post=$_POST["$key"];
           if(intval($post)<0) $post="0";
           if(empty($post) and strval($post)!="0"):
             # --- Replace empty values by already configured values
             $arr=explode(",",$confdat[$dkeys[$i]]);
             if($j==1) $po=substr($arr[0],4);
             if($j==2) $po=$arr[1];
             if($j==3) $po=substr($arr[2],0,strlen($arr[2])-1);
             $post=$po;
             endif;
           if(intval($post)>255) $post="255";
           $dat=$dat.$post.",";
           endfor;
        $dat=substr($dat,0,strlen($dat)-1).")";
        $data[$dkeys[$i]]=$dat;
        endif;
      endfor;
   return $data;
   }
public static function print_form() {
   #   Display the form for entering the stylesheet configuration data
   #   containing the actual data and replace the configuration with
   #   the entered data.
   #   used functions:
   #      self::get_default_data()
   #      self::read_config_data()
   #      self::set_config_data($data)
   #      self::file_css($data)
   #
   $lhp="line-height:2.8; padding-left:10px;";
   $sta="style=\"$lhp white-space:nowrap;\"";
   $stb="style=\"width:50px; text-align:right;\" class=\"form-control\"";
   $stc="style=\"padding-left:10px;\"";
   $std="style=\"padding-left:10px; font-size:smaller;\"";
   $stx="style=\"$lhp\"";
   $sty="style=\"$lhp font-size:smaller; white-space:nowrap;\"";
   $stz="style=\"$lhp font-size:smaller;\"";
   $width=140;
   #
   # --- Data entered via the form or default data after reset, respectively
   $data=self::read_config_data();
   #
   # --- Set data for the input form
   $keys=array_keys($data);
   for($i=0;$i<count($keys);$i=$i+1):
      $key=$keys[$i];
      $val=$data[$key];
      $datin[$i]=$val;
      if(substr($val,0,4)!="rgb("):
        $str[$i]=$stb." name=\"$key\" value=\"$val\"";
        else:
        #   $val="rgb(rrr,gg,b)"
        $arr=explode(",",$val);
        $red  =intval(substr($arr[0],4));
        $green=intval($arr[1]);
        $blue =intval(substr($arr[2],0,strlen($arr[2])-1));
        $kr=$key."_red";
        $kg=$key."_green";
        $kb=$key."_blue";
        $str[$i]=array(
           "red"=>   $stb." name=\"$kr\" value=\"$red\"",
           "green"=> $stb." name=\"$kg\" value=\"$green\"",
           "blue"=>  $stb." name=\"$kb\" value=\"$blue\"");
        endif;
      endfor;
   #
   # --- The input form
   $string='
<form method="post">
<table style="background-color:inherit;">';
   #
   # --- width and font size
   $string=$string.'
    <tr><td '.$sta.'>Breite einer Quicklinks-Gruppe <b>[*]</b></td>
        <td '.$sty.'>(Anzahl Pixel, px)</td>
        <td '.$sty.'><input '.$str[0].' />
        <td colspan="3" '.$sty.'>(0: keine feste Breite, &quot;auto&quot;)</td></tr>
    <tr><td '.$sta.'>Schriftgröße der Quicklinks-Texte</td>
        <td '.$sty.'>(Anzahl Punkte, pt)</td>
        <td '.$sty.'><input '.$str[1].' />
        <td colspan="3" '.$sty.'>(0: keine feste Größe, &quot;auto&quot;)</td></tr>
    <tr><td '.$sta.'>Eckenradius der Quicklinks-Gruppe</td>
        <td '.$sty.'>(Anzahl Pixel, px)</td>
        <td '.$sty.'><input '.$str[2].' />
        <td colspan="3" '.$sty.'>(0: Ecken nicht abgerundet)</td></tr>';
   #
   # --- Headline colors
   $string=$string.'
    <tr><td '.$std.'></td>
        <td '.$std.'>Farben (RGB-Werte)</td>
        <td '.$std.'> &nbsp; &nbsp; rot</td>
        <td '.$std.'>&nbsp; &nbsp;grün</td>
        <td '.$std.'>&nbsp; &nbsp;blau</td>
        <td '.$std.'>&nbsp; &nbsp; &nbsp; &nbsp;Darstellung</td></tr>';
   #
   # --- Quicklinks background color
   $string=$string.'
    <tr><td '.$sta.'>Quicklinks-Hintergrundfarbe</td>
        <td '.$stx.'></td>
        <td '.$stx.'><input '.$str[3][red].' /></td>
        <td '.$stx.'><input '.$str[3][green].' /></td>
        <td '.$stx.'><input '.$str[3][blue].' /></td>
        <td '.$stx.'>
            <input class="form-control" type="text" value=""
                   style="width:'.$width.'px;
                          border:solid 2px '.$datin[3].';
                          background-color:'.$datin[3].';" /></td></tr>';
   #
   # --- Quicklinks border color
   $string=$string.'
    <tr><td '.$sta.'>Quicklinks-Randfarbe</td>
        <td '.$sty.'>(feste Randdicke 1px)</td>
        <td '.$stx.'><input '.$str[4][red].' /></td>
        <td '.$stx.'><input '.$str[4][green].' /></td>
        <td '.$stx.'><input '.$str[4][blue].' /></td>
        <td '.$stx.'>
            <input class="form-control" type="text" value=""
                   style="width:'.$width.'px;
                          border:solid 2px '.$datin[4].';" /></td></tr>';
   #
   # --- Quicklinks text color
   $string=$string.'
    <tr><td '.$sta.' colspan="2">Quicklinks-Textfarbe
            <span '.$stz.'>(hier inkl. Rand und Hintergrund)</span></td>
        <td '.$stx.'><input '.$str[5][red].' /></td>
        <td '.$stx.'><input '.$str[5][green].' /></td>
        <td '.$stx.'><input '.$str[5][blue].' /></td>
        <td '.$stx.'>
            <input class="form-control" type="text" value=" Quicklink-Text "
                   style="width:'.$width.'px; text-align:center;
                          border:solid 2px '.$datin[4].'; border-radius:'.$datin[2].'px;
                          background-color:'.$datin[3].';
                          color:'.$datin[4].';" /></td></tr>';
   #
   # --- PopUps background color
   $string=$string.'
    <tr><td '.$sta.'>PopUps-Hintergrundfarbe</td>
        <td '.$stx.'></td>
        <td '.$stx.'><input '.$str[6][red].' /></td>
        <td '.$stx.'><input '.$str[6][green].' /></td>
        <td '.$stx.'><input '.$str[6][blue].' /></td>
        <td '.$stx.'>
            <input class="form-control" type="text" value=""
                   style="width:'.$width.'px;
                          border:solid 2px '.$datin[6].';
                          background-color:'.$datin[6].';" /></td></tr>';
   #
   # --- PopUps text color
   $string=$string.'
    <tr><td '.$sta.' colspan="2">PopUps-Textfarbe
            <span '.$stz.'>(hier inkl. Hintergrund)</span></td>
        <td '.$stx.'><input '.$str[7][red].' /></td>
        <td '.$stx.'><input '.$str[7][green].' /></td>
        <td '.$stx.'><input '.$str[7][blue].' /></td>
        <td '.$stx.'>
            <input class="form-control" type="text" value=" PopUp-Text "
                   style="width:'.$width.'px; font-size:0.8em;
                          border:solid 2px '.$datin[6].';
                          background-color:'.$datin[6].';
                          color:'.$datin[7].';" /></td></tr>';
   #
   # --- Buttons
   $restit="auf Defaultwerte zurücksetzen und speichern";
   $string=$string.'
    <tr><td '.$sta.'>
            <button class="btn btn-save" type="submit" name="sendit" value="sendit"
                    title=" speichern "> speichern </button></td>
        <td colspan="4" '.$sty.'>
            <button class="btn btn-update" type="submit" name="reset" value="reset"
                    title="'.$restit.'"> '.$restit.' </button></td></tr>';
   #
   # --- Save data and re-write stylesheet file
   if(!empty($_POST['reset']) or !empty($_POST['sendit'])):
     if(!empty($_POST['sendit'])):
       $msg="gespeichert";
       else:
       $data=self::get_default_data();
       $msg="zurückgesetzt und gespeichert";
       endif;
     self::set_config_data($data);
     self::file_css($data);
     $string=$string.'
    <tr><td colspan="5" '.$sta.'><span style="color:blue;">'.$msg.'</span></td></tr>';
     echo rex_view::info(utf8_encode("Nun noch das <u>AddOn re-installieren</u>, ".
        "damit das neue Stylesheet zur Wirkung kommt!"));
     endif;
   #
   # --- Completion of the form
   $string=$string.'
</table>
</form>';
   #
   # --- notice on width
   $string=$string.'
<div '.$stc.'><br/><b>[*] Zur Breite der Quicklinks-Gruppen:</b><br/>
Der Parameter legt eine <u>einheitliche feste Breite</u> der Gruppen samt allen
darunter aufpoppenden Linkzeilen fest. Zu lange Texte werden dabei abgeschnitten.
Alternativ erzeugt der <u>Parameterwert 0 flexible Breiten</u>, die nur durch die
jeweiligen Textlängen festgelegt sind. Die Gruppenbreiten sind entsprechend
unterschiedlich, und die Breite der aufpoppenden Linkzeilen einer Gruppe kann die
Gruppenbreite übertreffen, weil sie durch ihren längsten Linktext bestimmt wird.</div>';
   echo utf8_encode($string);
   }
public static function xmp_linklists() {
   #   Returns the HTML code for displaying an Quicklinks example
   #   containing three groups of artificial links.
   #   For the array structure see get_linklist().
   #   used functions:
   #      self::get_quicklinks($links)
   #
   $url="/redaxo/index.php?page=quicklinks/introduction";
   $ind=array(1=>4, 2=>3, 3=>2);
   $groups=array(1=>"Über uns", 2=>"Aktuelles", 3=>"Externe Links");
   $ref[1]=array(1=>"Verband", 2=>"Kontakt", 3=>"Impressum",
                 4=>"Datenschutzerklärung");
   $tit[1]=array(1=>"Unser Verband",
                 2=>"Telefon und E-Mail-Adressen der Vorstandsmitglieder",
                 3=>"Impressum",
                 4=>"Datenschutzerklärung");
   $ref[2]=array(1=>"Terminkalender", 2=>"neuer Vorstand", 3=>"Herbstausflug");
   $tit[2]=array(1=>"Alle Termine auf einen Blick",
                 2=>"neuer Vorstand gewählt auf der Verbandsversammlung",
                 3=>"Einladung zum alljährlichen Herbstausflug");
   $ref[3]=array(1=>"TTVN", 2=>"Süddeutsche Zeitung");
   $tit[3]=array(1=>"Tischtennisverband Niedersachsen",
                 2=>"Süddeutscher Verlag (Süddeutsche Zeitung)");
   for($m=1;$m<=count($ind);$m=$m+1):
      $grp=$groups[$m];
      $nr=$m;
      for($i=1;$i<=$ind[$m];$i=$i+1):
         $uri=$url;
         if($m==3 and $i==1) $uri="http://ttvn.de/";
         if($m==3 and $i==2) $uri="http://www.sueddeutsche.de/";
         $links[$m][$i][group_nr]  =$nr;
         $links[$m][$i][group_name]=utf8_encode($grp);
         $links[$m][$i][article_id]="";
         $links[$m][$i][url]       =$uri;
         $links[$m][$i][ref]       =utf8_encode($ref[$m][$i]);
         $links[$m][$i][title]     =utf8_encode($tit[$m][$i]);
         endfor;
      endfor;
   #
   # --- comments
   $nz=count($links);
   $width=rex_addon::get("quicklinks")->getConfig("width");
   $zus='';
   if($width<=0)
     $zus='<br/>D.h. die Quicklinks-Zellen sind so breit wie ihr Inhalt es verlangt.';
   $str='
<div>Im Beispiel sind die unten aufgeführten <b>'.$nz.' Quicklinks-Gruppen</b> so eingerichtet:</div>
<ul>
    <li>Alle internen Links sind identisch (mit unterschiedlichen Linktexten).</li>
    <li>Die externen Links sind real.</li>
    <li>Jeder Klick auf einen der Links erzeugt ein neues Browserfenster.</li>
    <li>Gemäß Konfiguration sind die Quicklinks-Zellen '.$width.' Pixel breit.
        '.$zus.'</li>
</ul>
<div><br/>&nbsp;</div>';
   $str=utf8_encode($str);
   #
   $str=$str.self::get_quicklinks($links).'
<div>&nbsp;</div>
<div>&nbsp;</div>';
   return $str;
   }
public static function get_linklist() {
   #   Returns all groups of quicklinks constructed by a special module
   #   (based on Redaxo linklist variable). They are selected from
   #   table rex_article_slice as an indexed array (numbered from 1).
   #   Each group is an indexed array (numbered from 1) of quicklinks.
   #   Each quicklink is an associative array with these keys:
   #       [group_nr]      group number according to slice priority
   #                       number in the quicklinks article
   #       [group_name]    group identifier
   #       [article_id]    Id of an Redaxo article
   #       [url]           URL of an external link
   #       [ref]           link text of an external link
   #       [title]         title text of an external link
   #   used functions:
   #      self::set_links($slice)
   #
   $sql=rex_sql::factory();
   #
   # --- select the link list based rex_module
   $query="SELECT * FROM rex_module WHERE output LIKE '%REX_LINKLIST[1]%quicklinks%'";
   $mod=$sql->getArray($query);
   $mod_id=$mod[0][id];
   #
   # --- select the quicklinks from rex_article_slice
   $query="SELECT * FROM rex_article_slice WHERE module_id=$mod_id ORDER BY createdate ASC";
   $slic=$sql->getArray($query);
   for($i=0;$i<count($slic);$i=$i+1):
      $link=self::set_links($slic[$i]);
      if(empty($link)) break;
      $links[$i+1]=$link;
      endfor;
   return $links;
   }
public static function set_links($slice) {
   #   Return one group of internal links defined in an article slice
   #   as an indexed array (numbered from 1). Each array element is an
   #   associative array with the following keys:
   #       [group_nr]      group number according to slice priority
   #                       number in the quicklinks article
   #       [group_name]    group identifier
   #       [article_id]    Id of the Redaxo article
   #       [url]           article URL (via rex_getUrl())
   #       [ref]           article name as link text
   #       [title]         article name as link text
   #   $slice              given slice (as object)
   #
   $art_id=$slice[article_id];
   $prio=rex_article::get($art_id)->getValue("priority");
   $prio=$prio-1;  // slices start from priority 2
   $grp=$slice[value11];
   $linklist=$slice[linklist1];
   if(empty($linklist)) return;
   $arr=explode(",",$linklist);
   for($k=0;$k<count($arr);$k=$k+1):
      $aid=$arr[$k];
      $name=rex_article::get($aid)->getName();
      $link[$k+1][group_nr]=$prio;
      $link[$k+1][group_name]=$grp;
      $link[$k+1][article_id]=$aid;
      $link[$k+1][url]=rex_getUrl($aid);
      $link[$k+1][ref]=$name;
      $link[$k+1][title]=$name;
      endfor;
   return $link;
   }
public static function get_external_linklist() {
   #   Returns all groups of quicklinks constructed by a special module
   #   (based on Redaxo value variables).
   #   They are selected from database table rex_article_slice as an
   #   indexed array (numbered from 1).
   #   Each group is an indexed array (numbered from 1) of quicklinks.
   #   Each quicklink is an associative array with these keys:
   #       [group_nr]      group number according to slice priority
   #                       number in the quicklinks article
   #       [group_name]    group identifier
   #       [article_id]    Id of an Redaxo article
   #       [url]           URL of an external link
   #       [ref]           link text of an external link
   #       [title]         title text of an external link
   #   used functions:
   #      self::set_external_links($slice)
   #
   $sql=rex_sql::factory();
   #
   # --- select the value based rex_module
   $query="SELECT * FROM rex_module WHERE output LIKE '%REX_VALUE[10]%quicklinks%'";
   $mod=$sql->getArray($query);
   $mod_id=$mod[0][id];
   #
   # --- select the quicklinks from rex_article_slice
   $query="SELECT * FROM rex_article_slice WHERE module_id=$mod_id ORDER BY createdate ASC";
   $slic=$sql->getArray($query);
   for($i=0;$i<count($slic);$i=$i+1)
      $links[$i+1]=self::set_external_links($slic[$i]);
   return $links;
   }
public static function set_external_links($slice) {
   #   Return one group of external links defined in an article slice
   #   as an indexed array (numbered from 1). Each array element is an
   #   associative array with the following keys:
   #       [group_nr]      group number according to slice priority
   #                       number in the quicklinks article
   #       [group_name]    group identifier
   #       [article_id]    (empty)
   #       [url]           URL of an external link
   #       [ref]           link text of an external link
   #       [title]         title text of an external link
   #   $slice              given slice (as object)
   #
   $art_id=$slice[article_id];
   $prio=rex_article::get($art_id)->getValue("priority");
   $prio=$prio-1;  // slices start from priority 2
   $grp=$slice[value11];
   for($k=1;$k<=10;$k=$k+1):
      $value="value".strval($k);
      $val=$slice[$value];
      if(empty($val)) break;
      $arr=explode(";",$val);
      $link[$k][group_nr]=$prio;
      $link[$k][group_name]=$grp;
      $link[$k][article_id]=0;
      $link[$k][url]=trim($arr[0]);
      $link[$k][ref]=trim($arr[1]);
      $link[$k][title]=trim($arr[2]);
      endfor;
   return $link;
   }
public static function get_linklists() {
   #   Returns all groups of quicklinks ordered by the article priority
   #   in the quicklinks category
   #   used functions:
   #      self::get_linklist()
   #      self::get_external_linklist()
   #
   $linksa=self::get_linklist();
   for($m=1;$m<=count($linksa);$m=$m+1):
      $link=$linksa[$m];
      $gnr=$link[1][group_nr];
      $links[$gnr]=$link;
      endfor;
   $linkse=self::get_external_linklist();
   for($m=1;$m<=count($linkse);$m=$m+1):
      $link=$linkse[$m];
      $gnr=$link[1][group_nr];
      $links[$gnr]=$link;
      endfor;
   return $links;
   }
public static function be_show_quicklinks($links) {
   #   returns a html code for printing a group of quicklinks (used in backend)
   #   $links              array of quicklinks belonging to one group
   #                       numbering from 1, each array element is
   #                       an associative array of the following form
   #       [group_nr]      group number according to slice priority
   #                       number in the quicklinks article
   #       [group_name]    group identifier
   #       [article_id]    Id of an Redaxo article
   #       [url]           URL of an external link
   #       [ref]           link text of an external link
   #       [title]         title text of an external link
   #
   $stx="style=\"padding-left:20px; text-align:right;\"";
   $sty="style=\"padding-left:20px;\"";
   $grp=$links[1][group_name];
   $gnr=$links[1][group_nr];
   $str="<div>Quicklinks-Gruppe $gnr <b>'$grp'</b>:</div>\n".
      "<table>\n";
   for($i=1;$i<=count($links);$i=$i+1):
      $id=$links[$i][article_id];
      $url=$links[$i][url];
      $ref=$links[$i][ref];
      $tit=$links[$i][title];
      $tid="";
      if($id>0) $tid="$id:";
      $str=$str. "    <tr><td $stx>$tid</td>\n".
         "        <td $sty>".
         "<a href=\"$url\" title=\"$tit\" target=\"_blank\">$ref</a></td></tr>\n";
      endfor;
   $str=$str."</table>\n";
   return $str;
   }
public static function get_quicklinks($links) {
   #   Return the HTML code of the quicklinks as side-by-side quicklinks groups.
   #   The links of a group pop up when crossing over with the mouse.
   #   $links           Numbered array of defined group of quicklinks
   #                    (numbering starting at 1).
   #                    Each group off quicklinks is a numbered array of
   #                    related links (numbering starting at 1).
   #                    Each quicklink is an associative array with
   #                    such elements:
   #      [group_name]  Identifier (headline) of the group
   #      [article_id]  Article-Id of the page to which the link points
   #                    (internal links)
   #      [url]         URL of the page to which the link points
   #      [ref]         link text
   #      [title]       title text (displayed when crossing over with the mouse)
   #
   # --- string for Javascript functions
   $box="box";   // will be extended to: box1, box2, ...
   $div="pop";   // will be extended to: pop1, pop2, ...
   #
   # --- cells of the quicklinks groups
   $anz=count($links);
   for($i=1;$i<=$anz;$i=$i+1):
      $link=$links[$i];
      #
      # --- Ueberschrifts-Box
      $title=$link[1][group_name];
      $ev="onmouseover=\"quickNewStyle($i); quickMenu($i,$anz);\"".
         " onmouseout=\"quickStdStyle($i); quickCleanDelay($anz);\"" ;
      $titms="title=\"$title\" $ev";
      #
      # --- Zeilen des Popup-Menues
      $text="";
      for($k=1;$k<=count($link);$k=$k+1):
         $aid=$link[$k][article_id];
         $tit=$link[$k][title];
         $url=$link[$k][url];
         $ref=$link[$k][ref];
         $tar="";
         if(substr($url,0,4)=="http") $tar=" target=\"_blank\"";
         $text=$text."        <a href=\"$url\" title=\"$tit\"$tar>$ref</a><br/>\n";
         endfor;
      $text=substr($text,0,strlen($text)-6)."\n";
      #
      # --- adding of headline and lines
      $string=$title."\n".
         "        <div class=\"quicklink_popup\" id=\"$div$i\" ".
         "style=\"visibility:hidden;\">\n".
         $text.
         "        </div>";
      $li[$i]="    <li id=\"box$i\" $titms>\n".
         "        $string</li>\n";
      endfor;
   #
   # --- Assembly of the quicklinks groups
   $str="<ul class=\"quicklink\">\n";
   for($i=1;$i<=$anz;$i=$i+1) $str=$str.$li[$i];
   $str=$str."</ul>\n";
   return $str;
   }
public static function print_quicklinks() {
   #   Display the defined quicklinks groups, side-by-side in a div-container.
   #   The links of a group pop up when crossing over with the mouse.
   #   used functions:
   #      self::get_linklists()
   #      self::get_quicklinks($links)
   #
   $links=self::get_linklists();
   echo "<div>".
      self::get_quicklinks($links).
      "</div>\n";
   }
}
?>
