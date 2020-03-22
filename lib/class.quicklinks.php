<?php
/**
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version März 2020
 */
#
define ('QUICKLINKS',$this->getPackageId());
#
class quicklinks {
#
public static function get_default_data() {
   #   Returns the Quicklinks default stylesheet data as an associative array
   #   with theese keys and values:
   #   ['width']        width of the Quicklink cell (number of pixels)
   #   ['size']         size of the Quicklink link text (number of points)
   #   ['radius']       border radius of the Quicklinks cell (number of pixels)
   #                    colors each as string like 'rgb(red,green,blue)':
   #   ['quick_backgr'] Quicklink cell background color
   #   ['quick_border'] Quicklink cell border color
   #   ['quick_text']   Quicklink cell text color
   #   ['popup_backgr'] PopUp cell background color
   #   ['popup_text']   PopUp cell text color
   #
   return array(
      'width'       =>0,
      'size'        =>0,
      'radius'      =>0,
      'quick_backgr'=>'rgb(204,103, 51)',
      'quick_border'=>'rgb(255,190, 60)',
      'quick_text'  =>'rgb(255,255,255)',
      'popup_backgr'=>'rgb(240,120, 60)',
      'popup_text'  =>'rgb(255,255,255)');
   }
public static function proof_data($data) {
   #   Proofs content and structure of an array that shall be set
   #   as configuration data for the Quicklinks stylesheet.
   #   $data            given data
   #   used functions:
   #      self::get_default_data()
   #
   # --- proof the data structure
   $keys=array_keys($data);
   $defdata=self::get_default_data();
   $defkeys=array_keys($defdata);
   if(count($keys)<>count($defkeys)) return FALSE;
   #
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
   #   Sets the configuration data for the Quicklinks stylesheet.
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
      if($ida>0 or strval($da)=='0') $da=$ida;
      rex_config::set(QUICKLINKS,$key,$da);
      endfor;
   }
public static function define_css($data) {
   #   Returns a string with the contents of the stylesheets for the Quicklinks.
   #   $data            given array of data for the stylesheet
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
   $quisize='';
   $popsize='font-size:0.8em';
   if($size>0):
     $quisize='font-size:'.$size.'pt';
     $popsize='font-size:'.intval(0.8*$size).'pt';
     endif;
   $pwidth=0;
   $padli='4px 8px 4px  8px';
   $mapop='0px 0px 0px -8px';
   if($width>0):
     $pwidth=$width-2;
     $padli='4px 0px 4px 0px';
     $mapop='0px 0px 0px 0px';
     endif;
   #
   # --- generate the stylesheet instructions
   $str=
'/*   Stylesheet for Quicklinks   */
ul.quicklink {
   margin:0px; padding:0px; list-style-type:none; }
ul.quicklink li {
   float:left; box-sizing:border-box;
   margin:0px 0px 0px -1px; padding:'.$padli.';
   white-space:nowrap; text-align:center; '.$quisize.';
   background-color:'.$q_bg_c.'; color:'.$q_t_c.';
   border:solid 1px '.$q_b_c.'; border-radius:'.$borrad.'px; }
ul.quicklink li div.quicklink_popup {
   position:absolute; margin:'.$mapop.'; padding:4px 0px 4px 0px;
   text-align:left; background-color:'.$p_bg_c.';
   border-style:none; overflow:hidden; visibility:hidden; }
ul.quicklink li div.quicklink_popup a {
   padding:0px 4px 0px 4px; text-decoration:none;
   '.$popsize.'; color:'.$p_t_c.'; }';
   #
   # --- addition in case of fixed width
   if($width>0) $str=$str.'
/*   */
/*   addition in case of fixed width   */
ul.quicklink li { width:'.$width.'px; }
ul.quicklink li div.quicklink_popup { width:'.$pwidth.'px; }';
   #
   # --- for backend functions
   $str=$str.'
/*   */
/*   for backend functions   */
table.ql_inherit { background-color:inherit; }
td.ql_indent, div.ql_indent { padding-left:20px; }
td.ql_indent_r  { padding-left:20px; text-align:right; }
td.ql_nowrap    { padding-left:10px; white-space:nowrap; }
td.ql_smaller, span.ql_smaller { padding-left:10px; white-space:nowrap; font-size:smaller; }
input.ql_show   { width:140px; }
input.ql_show_h { width:140px; height:1.5em; }
input.ql_width  { width:60px; text-align:right; }';
   #
   return $str;
   }
public static function file_css($data) {
   #   Generates the stylesheet file for Quicklinks in the AddOn assets directory
   #   and copy it to /assets/addons/quicklinks/.
   #   $data            given array of data for the stylesheet
   #   used functions:
   #      self::define_css($data)
   #
   # --- generate the stylesheet
   $buffer=self::define_css($data);
   #
   # --- generate the file
   $fn=QUICKLINKS.'.css';
   $file=rex_path::addon(QUICKLINKS,'assets/'.$fn);
   $handle=fopen($file,'w');
   fwrite($handle,$buffer);
   fclose($handle);
   #
   # --- copy the file to the /assets/addons/AddOn/ directory
   $copyfile=rex_path::addonAssets(QUICKLINKS,$fn);
   copy($file,$copyfile);
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
   #   Reads configuration data values for the Quicklinks stylesheet via form
   #   and returns the entered values in an associative array.
   #   Empty input values are replaced by already configured values.
   #   The rgb colors may be restricted to integer values between
   #   0 and 255, if necessary.
   #   used functions:
   #      self::get_default_data()
   #      self::read_zero($key,$confdat)
   #
   # --- Return the actually configured data after reset
   if(!empty($_POST['reset'])) return self::get_default_data();
   #
   # --- Keys for the input data
   $confdat=rex_config::get(QUICKLINKS);
   $dkeys=array_keys($confdat);
   $keys=array();
   for($i=0;$i<count($dkeys);$i=$i+1):
      $cdat=$confdat[$dkeys[$i]];
      if(substr($cdat,0,4)!='rgb('):
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
   for($i=0;$i<count($dkeys);$i=$i+1):
      $key=$dkeys[$i];
      $cdat=$confdat[$key];
      if(substr($cdat,0,4)!='rgb('):
        #
        # --- Dimensions
        $data[$key]=self::read_zero($key,$confdat[$key]);
        $iw=$i;
        else:
        #
        # --- Colors
        $po=explode('(',$confdat[$key]);
        $po=explode(')',$po[1]);
        $po=explode(',',$po[0]);
        $dat='rgb(';
        $m=($i-$iw-1)*3+$iw;
        for($j=1;$j<=3;$j=$j+1):
           $ke=$keys[$m+$j];
           $post=self::read_zero($ke,$po[$j-1]);
           if(intval($post)>255) $post='255';
           $dat=$dat.$post.',';
           endfor;
        $dat=substr($dat,0,strlen($dat)-1).')';
        $data[$key]=$dat;
        endif;
      endfor;
   return $data;
   }
public static function print_form() {
   #   Displays the form for entering the stylesheet configuration data containing
   #   the actual data and replaces the configuration with the entered data.
   #   used functions:
   #      self::get_default_data()
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
      $datin[$i]=$val;
      if(substr($val,0,4)!='rgb('):
        $str[$i]='class="form-control ql_width" name="'.$key.'" value="'.$val.'"';
        else:
        #   $val='rgb(rrr,gg,b)';
        $arr=explode(',',$val);
        $red  =intval(substr($arr[0],4));
        $green=intval($arr[1]);
        $blue =intval(substr($arr[2],0,strlen($arr[2])-1));
        $kr=$key.'_red';
        $kg=$key.'_green';
        $kb=$key.'_blue';
        $str[$i]=array(
           'red'=>   'class="form-control ql_width" name="'.$kr.'" value="'.$red.'"',
           'green'=> 'class="form-control ql_width" name="'.$kg.'" value="'.$green.'"',
           'blue'=>  'class="form-control ql_width" name="'.$kb.'" value="'.$blue.'"');
        endif;
      endfor;
   #
   # --- The input form
   $string='
<form method="post">
<table class="ql_inherit">';
   #
   # --- Width and font size
   $string=$string.'
    <tr><td class="ql_nowrap">Breite einer Quicklinks-Gruppe <b>[*]</b></td>
        <td class="ql_smaller"><input '.$str[0].' />
        <td colspan="2" class="ql_smaller">px &nbsp;(Anzahl Pixel,</td>
        <td class="ql_smaller">0: keine feste Breite, &quot;auto&quot;)</td></tr>
    <tr><td class="ql_nowrap">Schriftgröße der Quicklinks-Texte</td>
        <td class="ql_smaller"><input '.$str[1].' />
        <td colspan="2" class="ql_smaller">pt &nbsp; (Anzahl Punkte,</td>
        <td class="ql_smaller">0: keine feste Größe, &quot;auto&quot;)</td></tr>
    <tr><td class="ql_nowrap">Eckenradius einer Quicklinks-Gruppe</td>
        <td class="ql_smaller"><input '.$str[2].' />
        <td colspan="2" class="ql_smaller">px &nbsp; (Anzahl Pixel,</td>
        <td class="ql_smaller">0: Ecken nicht abgerundet)</td></tr>';
   #
   # --- Headline colors
   $string=$string.'
    <tr><td class="ql_smaller ql_indent_r">Farben (RGB-Werte)</td>
        <td class="ql_smaller"> &nbsp; &nbsp; rot</td>
        <td class="ql_smaller">&nbsp; &nbsp;grün</td>
        <td class="ql_smaller">&nbsp; &nbsp;blau</td>
        <td class="ql_smaller">&nbsp; &nbsp; &nbsp; &nbsp;Darstellung</td></tr>';
   #
   # --- Quicklinks background color
   $string=$string.'
    <tr><td class="ql_nowrap">Quicklinks-Hintergrundfarbe</td>
        <td class="ql_nowrap"><input '.$str[3]['red'].' /></td>
        <td class="ql_nowrap"><input '.$str[3]['green'].' /></td>
        <td class="ql_nowrap"><input '.$str[3]['blue'].' /></td>
        <td class="ql_nowrap">
            <input class="form-control ql_show_h" type="text" value=""
                   style="border:solid 2px '.$datin[3].';
                          background-color:'.$datin[3].';" /></td></tr>';
   #
   # --- Quicklinks border color
   $string=$string.'
    <tr><td class="ql_nowrap">Quicklinks-Randfarbe <span class="ql_smaller">(feste Randdicke 1px)</span></td>
        <td class="ql_nowrap"><input '.$str[4]['red'].' /></td>
        <td class="ql_nowrap"><input '.$str[4]['green'].' /></td>
        <td class="ql_nowrap"><input '.$str[4]['blue'].' /></td>
        <td class="ql_nowrap">
            <input class="form-control ql_show_h" type="text" value=""
                   style="border:solid 2px '.$datin[4].'; border-radius:'.$datin[2].'px;" /></td></tr>';
   #
   # --- Quicklinks text color
   $string=$string.'
    <tr><td class="ql_nowrap">Quicklinks-Textfarbe</td>
        <td class="ql_nowrap"><input '.$str[5]['red'].' /></td>
        <td class="ql_nowrap"><input '.$str[5]['green'].' /></td>
        <td class="ql_nowrap"><input '.$str[5]['blue'].' /></td>
        <td class="ql_nowrap">
            <input class="form-control ql_show" type="text" value=" Quicklink-Text "
                   style="text-align:center;
                          border:solid 2px '.$datin[4].'; border-radius:'.$datin[2].'px;
                          background-color:'.$datin[3].';
                          color:'.$datin[5].';" /></td></tr>';
   #
   # --- PopUps background color
   $string=$string.'
    <tr><td class="ql_nowrap">PopUps-Hintergrundfarbe</td>
        <td class="ql_nowrap"><input '.$str[6]['red'].' /></td>
        <td class="ql_nowrap"><input '.$str[6]['green'].' /></td>
        <td class="ql_nowrap"><input '.$str[6]['blue'].' /></td>
        <td class="ql_nowrap">
            <input class="form-control ql_show_h" type="text" value=""
                   style="border:solid 2px '.$datin[6].';
                          background-color:'.$datin[6].';" /></td></tr>';
   #
   # --- PopUps text color
   $string=$string.'
    <tr><td class="ql_nowrap">PopUps-Textfarbe</td>
        <td class="ql_nowrap"><input '.$str[7]['red'].' /></td>
        <td class="ql_nowrap"><input '.$str[7]['green'].' /></td>
        <td class="ql_nowrap"><input '.$str[7]['blue'].' /></td>
        <td class="ql_nowrap">
            <input class="form-control ql_show" type="text" value=" PopUp-Text "
                   style="font-size:0.8em;
                          border:solid 2px '.$datin[6].';
                          background-color:'.$datin[6].';
                          color:'.$datin[7].';" /></td></tr>';
   #
   # --- Buttons
   $restit='auf Defaultwerte zurücksetzen und speichern';
   $string=$string.'
    <tr><td class="ql_nowrap"><br/>
            <button class="btn btn-save" type="submit" name="sendit" value="sendit"
                    title=" speichern "> speichern </button></td>
        <td colspan="4" class="ql_smaller"><br/>
            <button class="btn btn-update" type="submit" name="reset" value="reset"
                    title="'.$restit.'"> '.$restit.' </button></td></tr>';
   #
   # --- Notice on width
   $string=$string.'
    <tr><td colspan="5" class="ql_nowrap"><br/><b>[*] Zur Breite der Quicklinks-Gruppen:</b></td></tr>
    <tr><td colspan="5" class="ql_indent">
            <div class="ql_indent">Ein <u>Parameterwert&gt;0 definiert
            eine einheitliche feste Breite</u> der Gruppen samt allen darunter
            aufpoppenden Linkzeilen. Zu lange Texte werden dabei abgeschnitten.
            Alternativ erzeugt der <u>Parameterwert 0 flexible Breiten</u>, die nur
            durch die jeweiligen Textlängen festgelegt sind. Die Gruppenbreiten sind
            entsprechend unterschiedlich, und die Breite der aufpoppenden Linkzeilen
            einer Gruppe kann die Gruppenbreite übertreffen, weil sie durch ihren
            längsten Linktext bestimmt wird.</div></td></tr>';
   #
   # --- Completion of the form
   $string=$string.'
</table>
</form>';
   echo $string;
   }
public static function xmp_linklists() {
   #   Returns the HTML code for displaying an Quicklinks example containing
   #   3 groups of artificial links.
   #   used functions:
   #      self::get_quicklinks($links)
   #
   $url=rex_url::backend('index.php?page='.QUICKLINKS.'/introduction');
   $ind=array(1=>4, 2=>3, 3=>2);
   $groups=array(1=>'Über uns', 2=>'Aktuelles', 3=>'Externe Links');
   $ref=array();
   $tit=array();
   $ref[1]=array(1=>'Verband', 2=>'Kontakt', 3=>'Impressum',
                 4=>'Datenschutzerklärung');
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
   #
   # --- Comments
   $nz=count($links);
   $width=rex_addon::get(QUICKLINKS)->getConfig('width');
   if($width<=0):
     $zus='d.h. Gruppenüberschrift und Linkzeilen sind (unabhängig voneinander) so breit, wie ihr Inhalt es verlangt.';
     else:
     $zus='ihr Inhalt wird daher ggf. rechts abgeschnitten.';
     endif;
   $str='
<div>Im Beispiel sind die unten aufgeführten <b>'.$nz.' Quicklinks-Gruppen</b> so eingerichtet:</div>
<ul>
    <li>Alle internen Links sind identisch (mit unterschiedlichen Linktexten).</li>
    <li>Die externen Links sind real und werden in einem neuen Browserfenster angezeigt.</li>
</ul>
<div>Die äußere Form entspricht der gewählten Konfiguration. Demgemäß sind die
Quicklinks-Zellen '.$width.' Pixel breit, '.$zus.'<br/>&nbsp;</div>';
   #
   $str=$str.self::get_quicklinks($links).'
<div>&nbsp;</div>
<div>&nbsp;</div>';
   return $str;
   }
public static function get_internal_linklists() {
   #   Returns all groups of Quicklinks constructed by a special module
   #   (based on Redaxo linklist variable). They are selected from
   #   table 'rex_article_slice' as a numbered array (numbering starting at 1).
   #   Each group is represented as a numbered array (numbering starting at 1).
   #   Each Quicklink consists of an associative array with these keys and values:
   #     ['group_nr']   group number according to slice priority
   #                    number in the Quicklinks article
   #     ['group_name'] group identifier
   #     ['article_id'] Id of an Redaxo article
   #     ['url']        URL of an external link
   #     ['ref']        link text of an external link
   #     ['title']      title text of an external link
   #   used functions:
   #      self::set_internal_links($slice)
   #
   $sql=rex_sql::factory();
   #
   # --- Select the link list based rex_module
   $query='SELECT * FROM rex_module WHERE output LIKE \'%REX_LINKLIST[1]%quicklinks%\'';
   $mod=$sql->getArray($query);
   $mod_id=$mod[0]['id'];
   #
   # --- Select the Quicklinks from rex_article_slice
   $query='SELECT * FROM rex_article_slice WHERE module_id='.$mod_id.' ORDER BY createdate ASC';
   $slic=$sql->getArray($query);
   $links=array();
   for($i=0;$i<count($slic);$i=$i+1):
      $grplink=self::set_internal_links($slic[$i]);
      $links[$i+1]=$grplink;
      endfor;
   return $links;
   }
public static function set_internal_links($slice) {
   #   Returns one group of internal Quicklinks defined in an article slice.
   #   The group is represented as a numbered array (numbering starting at 1).
   #   Each Quicklink consists of an associative array with these keys and values:
   #     ['group_nr']   group number according to slice priority
   #                    number in the Quicklinks article
   #     ['group_name'] group identifier
   #     ['article_id'] Id of the Redaxo article
   #     ['url']        article URL (via rex_getUrl())
   #     ['ref']        article name as link text
   #     ['title']      article name as link text
   #   $slice           given slice (as object)
   #
   $art_id=$slice['article_id'];
   $prio=rex_article::get($art_id)->getValue('priority');
   $prio=$prio-1;  // slices start from priority 2
   $grp=$slice['value11'];
   $linklist=$slice['linklist1'];
   $grplink=array();
   if(empty($linklist)) return $grplink;
   #
   $arr=explode(',',$linklist);
   for($k=0;$k<count($arr);$k=$k+1):
      $aid=$arr[$k];
      $name=rex_article::get($aid)->getName();
      $grplink[$k+1]['group_nr']  =$prio;
      $grplink[$k+1]['group_name']=$grp;
      $grplink[$k+1]['article_id']=$aid;
      $grplink[$k+1]['url']       =rex_getUrl($aid);
      $grplink[$k+1]['ref']       =$name;
      $grplink[$k+1]['title']     =$name;
      endfor;
   return $grplink;
   }
public static function get_external_linklists() {
   #   Returns all groups of external Quicklinks constructed by a special
   #   module (based on Redaxo value variables). They are selected from database
   #   table 'rex_article_slice'. Each group is represented as a numbered array
   #   of Quicklinks (numbering starting at 1). Each Quicklink consists of an
   #   associative array with these keys and values:
   #     ['group_nr']   group number according to slice priority
   #                    number in the Quicklinks article
   #     ['group_name'] group identifier
   #     ['article_id'] ='-1'
   #     ['url']        URL of an external link
   #     ['ref']        link text of an external link
   #     ['title']      title text of an external link
   #   used functions:
   #      self::set_external_links($slice)
   #
   $sql=rex_sql::factory();
   #
   # --- Select the value based rex_module
   $query='SELECT * FROM rex_module WHERE output LIKE \'%REX_VALUE[10]%quicklinks%\'';
   $mod=$sql->getArray($query);
   $mod_id=$mod[0]['id'];
   #
   # --- Select the Quicklinks from rex_article_slice
   $query='SELECT * FROM rex_article_slice WHERE module_id='.$mod_id.' ORDER BY createdate ASC';
   $slic=$sql->getArray($query);
   $links=array();
   for($i=0;$i<count($slic);$i=$i+1)
      $links[$i+1]=self::set_external_links($slic[$i]);
   return $links;
   }
public static function set_external_links($slice) {
   #   Returns one group of external Quicklinks defined in an article slice as a
   #   numbered array (numbering starting at 1, max. 10 elements). Each Quicklink
   #   consists of an associative array with these keys and values:
   #     ['group_nr']   group number according to slice priority
   #                    number in the Quicklinks article
   #     ['group_name'] group identifier
   #     ['article_id'] ='-1'
   #     ['url']        URL of an external link
   #     ['ref']        link text of an external link
   #     ['title']      title text of an external link
   #   $slice           given slice (as object)
   #
   $art_id=$slice['article_id'];
   $prio=rex_article::get($art_id)->getValue('priority');
   $prio=$prio-1;  // slices start from priority 2
   $grp=$slice['value11'];
   $grplink=array();
   for($k=1;$k<=10;$k=$k+1):
      $value='value'.strval($k);
      $val=$slice[$value];
      if(empty($val)) break;
      $arr=explode(';',$val);
      $grplink[$k]['group_nr']=$prio;
      $grplink[$k]['group_name']=$grp;
      $grplink[$k]['article_id']='-1';
      $grplink[$k]['url']=trim($arr[0]);
      $grplink[$k]['ref']=trim($arr[1]);
      $grplink[$k]['title']=trim($arr[2]);
      endfor;
   return $grplink;
   }
public static function get_linklists() {
   #   Returns all groups of internal and external Quicklinks as a numbered
   #   array (numbering starting at 1), ordered according to the article priority
   #   in the Quicklinks category. Each group is represented as a numbered array
   #   of Quicklinks (numbering starting at 1). Each Quicklink consists of an
   #   associative array with these keys:
   #     ['group_nr']   group number according to slice priority
   #                    number in the Quicklinks article
   #     ['group_name'] group identifier
   #     ['article_id'] Id of an Redaxo article / '-1' (external link)
   #     ['url']        URL of an external link
   #     ['ref']        link text of an external link
   #     ['title']      title text of an external link
   #   used functions:
   #      self::get_internal_linklists()
   #      self::get_external_linklists()
   #
   $linksa=self::get_internal_linklists();
   $grplinks=array();
   for($m=1;$m<=count($linksa);$m=$m+1):
      $links=$linksa[$m];
      $gnr=$links[1]['group_nr'];
      $grplinks[$gnr]=$links;
      endfor;
   $linkse=self::get_external_linklists();
   for($m=1;$m<=count($linkse);$m=$m+1):
      $links=$linkse[$m];
      $gnr=$links[1]['group_nr'];
      $grplinks[$gnr]=$links;
      endfor;
   return $grplinks;
   }
public static function be_show_quicklinks($links) {
   #   Returns a html code for printing a group of Quicklinks (used in backend).
   #   $links           numbered array of Quicklinks belonging to one group
   #                    (numbering starting at 1), each array element is
   #                    an associative array of the following form:
   #     ['group_nr']   group number according to slice priority
   #                    number in the Quicklinks article
   #     ['group_name'] group identifier
   #     ['article_id'] Id of an Redaxo article
   #     ['url']        URL of an external link
   #     ['ref']        link text of an external link
   #     ['title']      title text of an external link
   #
   $grp=$links[1]['group_name'];
   $gnr=$links[1]['group_nr'];
   $str='<div>Quicklinks-Gruppe '.$gnr.' &nbsp; <b>\''.$grp.'\'</b></div>
<table>';
   for($i=1;$i<=count($links);$i=$i+1):
      $id =$links[$i]['article_id'];
      $url=$links[$i]['url'];
      $ref=$links[$i]['ref'];
      $tit=$links[$i]['title'];
      $tid=$id.':';
      if($tid<0) $tid='';
      $str=$str. '
    <tr><td class="ql_indent_r" width="50">'.$tid.'</td>
        <td class="ql_indent">
           <a href="'.$url.'" title="'.$tit.'" target="_blank">'.$ref.'</a></td></tr>';
      endfor;
   $str=$str.'
</table>';
   return $str;
   }
public static function get_quicklinks($grplinks) {
   #   Returns the HTML code of the Quicklinks as side-by-side Quicklinks groups.
   #   The links of a group pop up when crossing over with the mouse.
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
      # --- Lines of the popup menues
      $text='';
      for($k=1;$k<=count($grplink);$k=$k+1):
         $aid=$grplink[$k]['article_id'];
         $tit=$grplink[$k]['title'];
         $url=$grplink[$k]['url'];
         $ref=$grplink[$k]['ref'];
         $tar='';
         if(substr($url,0,4)=='http') $tar=' target="_blank"';
         $text=$text.'
            <a href="'.$url.'" title="'.$tit.'"'.$tar.'>'.$ref.'</a><br/>';
         endfor;
      $text=substr($text,0,strlen($text)-5);
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
public static function print_quicklinks() {
   #   Displays the defined Quicklinks groups, side-by-side as a html unordered list.
   #   The links of a group pop up when crossing over with the mouse.
   #   used functions:
   #      self::get_linklists()
   #      self::get_quicklinks($links)
   #
   $grplinks=self::get_linklists();
   echo self::get_quicklinks($grplinks);
   }
}
?>
