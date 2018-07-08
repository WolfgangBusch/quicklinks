<?php
/**
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Juli 2018
 */
#
class quicklinks_install {
#
public static function sql_action($sql,$query) {
   #   performing an SQL action using setQuery()
   #   including error message if fails
   #   $sql               SQL handle
   #   $query             SQL action
   #
   try {
        $sql->setQuery($query);
        $error="";
         } catch(rex_sql_exception $e) {
        $error=$e->getMessage();
        }
   if(!empty($error)) echo rex_view::error($error);
   }
public static function update_module($my_package,$name,$funin,$funout) {
   #   Insert or update of a module
   #   $name              Part 1 of the module's name
   #   $my_package        AddOn identifier (= Part 2 of the module's name)
   #   $funin             public static function returning the code of the module's input section
   #   $funout            public static function returning the code of the module's output section
   #   used functions:
   #      self::sql_action($sql,$query)
   #
   # --- Read the codes of the module's sections
   $input=self::$funin();
   $output=self::$funout();
   #
   # --- Insert/update the module in table rex_module
   $table="rex_module";
   $fullname=$name." (".$my_package.")";
   $sql=rex_sql::factory();
   $query="SELECT * FROM ".$table." WHERE name LIKE '%".$name."%'";
   $mod=$sql->getArray($query);
   if(count($mod[0])>0):
     # --- Module exists: update
     self::sql_action($sql,"UPDATE ".$table." SET input='".$input."' ".
        "WHERE id=".$mod[0][id]);
     self::sql_action($sql,"UPDATE ".$table." SET output='".$output."' ".
        "WHERE id=".$mod[0][id]);
     else:
     # --- Module does not exist: insert
     self::sql_action($sql,"INSERT INTO ".$table." (name,input,output) ".
        "VALUES ('".$fullname."','".$input."','".$output."')");
     endif;
   }
public static function mod_linklist_in() {
   #   Code of the input section of the article linklist module
   #
   $str='
<p><b>Auswahl einer Gruppe von Quicklinks auf Artikel</b></p>
<p>Name der Gruppe: &nbsp;
<input name="REX_INPUT_VALUE[11]" value="REX_VALUE[11]" />
&nbsp; (darf nicht leer sein)</p>
<p>Auswahl der Links: &nbsp; REX_LINKLIST[1 widget=1]</p>';
   return str_replace("\\","\\\\",utf8_encode($str));
   }
public static function mod_linklist_out() {
   #   Code of the output section of the article linklist module
   #
   $str='
<?php
if(rex::isBackend()):
  $grp=REX_VALUE[11];
  $list=REX_LINKLIST[1];
  if(empty($grp)):
    echo "<p style=\"color:red;\">Gruppenname ist leer!</p>\n";
    else:
    #
    # --- show list of links
    $prio=rex_article::getCurrent()->getValue("priority")-1;
    $arr=explode(",",$list);
    for($i=0;$i<count($arr);$i=$i+1):
       $id=$arr[$i];
       $name=rex_article::get($id)->getName();
       $k=$i+1;
       $links[$k]["group_nr"]=$prio;
       $links[$k]["group_name"]=$grp;
       $links[$k]["article_id"]=$id;
       $links[$k]["url"]=rex_getUrl($id);
       $links[$k]["ref"]=$name;
       $links[$k]["title"]=$name;
       endfor;
    echo quicklinks::be_show_quicklinks($links);
    endif;
  endif;
?>';
   return str_replace("\\","\\\\",utf8_encode($str));
   }
public static function mod_ext_linklist_in() {
   #   Code of the input section of the external linklist module
   #
   $str='
<p><b>Auswahl einer Liste externer Quicklinks:</b></p>
<table style="background-color:inherit;">
    <tr><td>Name&nbsp;der&nbsp;Gruppe:&nbsp;&nbsp;</td>
        <td><input name="REX_INPUT_VALUE[11]" value="<?php echo trim(REX_VALUE[11]); ?>" /></td></tr>
    <tr><td colspan="2" style="color:blue;"><br/>
            Für jeden Link jeweils mit ";" getrennt eingeben:  <b>URL (inkl. "http://")</b> ; <b>Linktext</b> ; <b>Linktitel (als Tooltip)</b></td></tr>
    <tr><td>Link Nr. 1:</td>
        <td><input style="width:600px;" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]" /></td></tr>
    <tr><td>Link Nr. 2:</td>
        <td><input style="width:600px;" name="REX_INPUT_VALUE[2]" value="REX_VALUE[2]" /></td></tr>
    <tr><td>Link Nr. 3:</td>
        <td><input style="width:600px;" name="REX_INPUT_VALUE[3]" value="REX_VALUE[3]" /></td></tr>
    <tr><td>Link Nr. 4:</td>
        <td><input style="width:600px;" name="REX_INPUT_VALUE[4]" value="REX_VALUE[4]" /></td></tr>
    <tr><td>Link Nr. 5:</td>
        <td><input style="width:600px;" name="REX_INPUT_VALUE[5]" value="REX_VALUE[5]" /></td></tr>
    <tr><td>Link Nr. 6:</td>
        <td><input style="width:600px;" name="REX_INPUT_VALUE[6]" value="REX_VALUE[6]" /></td></tr>
    <tr><td>Link Nr. 7:</td>
        <td><input style="width:600px;" name="REX_INPUT_VALUE[7]" value="REX_VALUE[7]" /></td></tr>
    <tr><td>Link Nr. 8:</td>
        <td><input style="width:600px;" name="REX_INPUT_VALUE[8]" value="REX_VALUE[8]" /></td></tr>
    <tr><td>Link Nr. 9:</td>
        <td><input style="width:600px;" name="REX_INPUT_VALUE[9]" value="REX_VALUE[9]" /></td></tr>
    <tr><td>Link Nr. 10</td>
        <td><input style="width:600px;" name="REX_INPUT_VALUE[10]" value="REX_VALUE[10]" /></td></tr>
</table>';
   return str_replace("\\","\\\\",utf8_encode($str));
   }
public static function mod_ext_linklist_out() {
   #   Code of the output section of the external linklist module
   #
   $str='
<?php
if(rex::isBackend()):
  $grp=REX_VALUE[11];
  $val[1]=REX_VALUE[1];
  $val[2]=REX_VALUE[2];
  $val[3]=REX_VALUE[3];
  $val[4]=REX_VALUE[4];
  $val[5]=REX_VALUE[5];
  $val[6]=REX_VALUE[6];
  $val[7]=REX_VALUE[7];
  $val[8]=REX_VALUE[8];
  $val[9]=REX_VALUE[9];
  $val[10]=REX_VALUE[10];
  if(empty($grp)):
    echo "<p style=\"color:red;\">Gruppenname ist leer!</p>\n";
    else:
    #
    # --- show list of links
    $prio=rex_article::getCurrent()->getValue("priority")-1;
    for($i=1;$i<=count($val);$i=$i+1):
       if(empty($val[$i])) break;
       $brr=explode(";",$val[$i]);
       $links[$i]["group_nr"]=$prio;
       $links[$i]["group_name"]=$grp;
       $links[$i]["article_id"]=0;
       $links[$i]["url"]=trim($brr[0]);
       $links[$i]["ref"]=trim($brr[1]);
       $links[$i]["title"]=trim($brr[2]);
       endfor;
    echo quicklinks::be_show_quicklinks($links);
    endif;
  endif;
?>';
   return str_replace("\\","\\\\",utf8_encode($str));
   }
}
?>
