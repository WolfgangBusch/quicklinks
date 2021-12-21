<?php
/**
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Dezember 2021
 */
#
class quicklinks_install {
#
public static function update_modules() {
   #   Insert or update all quicklinks modules
   #   used functions:
   #      update_module($my_package,$name,$funin,$funout)
   #
   self::update_module('quicklinks','Artikel-Linkliste',
      'mod_linklist_in','mod_linklist_out');
   self::update_module('quicklinks','Liste externer Links',
      'mod_ext_linklist_in','mod_ext_linklist_out');
   }
public static function update_module($my_package,$name,$funin,$funout) {
   #   Insert or update of a module
   #   $name              Part 1 of the module's name
   #   $my_package        AddOn identifier (= Part 2 of the module's name)
   #   $funin             public static function returning the code of the module's input section
   #   $funout            public static function returning the code of the module's output section
   #
   # --- Read the codes of the module's sections
   $input=self::$funin();
   $output=self::$funout();
   #
   # --- Insert/update the module in table rex_module
   $table='rex_module';
   $fullname=$name.' ('.$my_package.')';
   $sql=rex_sql::factory();
   $query='SELECT * FROM '.$table.' WHERE name LIKE \'%'.$name.'%\'';
   $mod=$sql->getArray($query);
   if(count($mod[0])>0):
     # --- Module exists: update
     $sql->setQuery('UPDATE '.$table.' SET input=\''.$input.'\' WHERE id='.$mod[0][id]);
     $sql->setQuery('UPDATE '.$table.' SET output=\''.$output.'\' WHERE id='.$mod[0][id]);
     else:
     # --- Module does not exist: insert
     $sql->setQuery('INSERT INTO '.$table.' (name,input,output) '.
        'VALUES (\''.$fullname.'\',\''.$input.'\',\''.$output.'\')');
     endif;
   }
public static function mod_linklist_in() {
   #   Code of the input section of the article linklist module
   #
   $str='
<?php
$val=trim(REX_VALUE[11]);
?>
<h4 align="center">Auswahl einer Gruppe von Quicklinks auf Artikel</h4>
<p>Name der Gruppe: &nbsp;
<input name="REX_INPUT_VALUE[11]" value="<?php echo $val; ?>" />
&nbsp; (darf nicht leer sein)</p>
<p>Auswahl der Links: &nbsp; REX_LINKLIST[1 widget=1]</p>';
   return str_replace('\\','\\\\',$str);
   }
public static function mod_linklist_out() {
   #   Code of the output section of the article linklist module
   #
   $str='
<?php
if(rex::isBackend()):
  $grp =REX_VALUE[11];
  $list=REX_LINKLIST[1];
  echo quicklinks::show_internal_quicklinks($grp,$list);
  endif;
?>';
   return str_replace('\\','\\\\',$str);
   }
public static function mod_ext_linklist_in() {
   #   Code of the input section of the external linklist module
   #
   $group=trim(REX_VALUE[11]);
   $str='
<?php
$val=trim(REX_VALUE[11]);
?>
<h4 align="center">Eingabe einer Gruppe externer Quicklinks</h4>
<table class="ql_inherit">
    <tr><td>Name&nbsp;der&nbsp;Gruppe:</td>
        <td class="ql_indent">
            <input name="REX_INPUT_VALUE[11]" value="<?php echo $val; ?>" />
            &nbsp; (darf nicht leer sein)</td></tr>
</table>
<table class="ql_inherit">
    <tr><td><br><u>Eingabe:</u></td>
        <td class="ql_indent"><br/> &nbsp;
            <b>URL ; Linktext ; Linktitel</b> &nbsp; &nbsp;
            (mit ";" getrennt, URL inkl. "http(s)://")</td></tr>';
for($i=1;$i<=10;$i=$i+1) $str=$str.'
    <tr><td>Link Nr. '.$i.':</td>
        <td class="ql_indent">
            <input name="REX_INPUT_VALUE['.$i.']" value="REX_VALUE['.$i.']"
                   class="ql_extwidth" /></td></tr>';
$str=$str.'
</table>';
   return str_replace('\\','\\\\',$str);
   }
public static function mod_ext_linklist_out() {
   #   Code of the output section of the external linklist module
   #
   $str='
<?php
if(rex::isBackend()):
  $grp    =REX_VALUE[11];
  $val[1] =REX_VALUE[1];
  $val[2] =REX_VALUE[2];
  $val[3] =REX_VALUE[3];
  $val[4] =REX_VALUE[4];
  $val[5] =REX_VALUE[5];
  $val[6] =REX_VALUE[6];
  $val[7] =REX_VALUE[7];
  $val[8] =REX_VALUE[8];
  $val[9] =REX_VALUE[9];
  $val[10]=REX_VALUE[10];
  echo quicklinks::show_external_quicklinks($grp,$val);
  endif;
?>';
   return str_replace('\\','\\\\',$str);
   }
}
?>
