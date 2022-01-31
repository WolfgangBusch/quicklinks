<?php
/*
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Januar 2022
 */
#
class quicklinks_install {
#
public static function build_modules($my_package) {
   #   Creates / updates a number of modules in table rex_module.
   #   $my_package      package Id
   #   functions used:
   #      self::define_modules()
   #
   $table='rex_module';
   $modules=self::define_modules($my_package);
   for($i=1;$i<=count($modules);$i=$i+1):
      #
      # --- module sources: name, input, output, type (='input' or 'output')
      #     and string for identifying the type-part 
      $name  =$modules[$i]['name'];
      $input =$modules[$i]['input'];
      $output=$modules[$i]['output'];
      $ident =$modules[$i]['ident'];
      $part  =$modules[$i]['part'];
      #
      # --- module exists already?
      $sql=rex_sql::factory();
      $where='name LIKE \'%'.$my_package.'%\' AND '.$part.' LIKE \'%'.$ident.'%\'';
      $query='SELECT * FROM '.$table.' WHERE '.$where;
      $mod=$sql->getArray($query);
      if(!empty($mod)):
        #     existing:         update (name unchanged)
        $id=$mod[0]['id'];
        $sql->setQuery('UPDATE '.$table.' SET  input=\''.$input.'\'  WHERE id='.$id);
        $sql->setQuery('UPDATE '.$table.' SET output=\''.$output.'\' WHERE id='.$id);
        else:
        #     not yet existing: insert
        $sql->setQuery('INSERT INTO '.$table.' (name,input,output) '.
              'VALUES (\''.$name.'\',\''.$input.'\',\''.$output.'\')');
        endif;
      endfor;
   }
public static function define_modules($my_package) {
   #   Defines some module sources and returns them as a numbered array:
   #      $mod[$i]['name']    the module's name
   #      $mod[$i]['input']   source of the module's input part
   #      $mod[$i]['output']  source of the module's output part
   #      $mod[$i]['ident']   search string for indentifying a module as a
   #                          quicklinks module in order to re-write it
   #      $mod[$i]['part']    = 'input' or 'output', defines whether the search
   #                          string (see above) is to be applyed eather to the
   #                          input part or to the output part of the module
   #          ($i = 1, 2, ...)
   #   $my_package      package Id
   #
   $name =array();
   $in   =array();
   $out  =array();
   $ident=array();
   $part =array();
   #
   # --- module 1
   $name[1]='Artikel-Linkliste ('.$my_package.')';
   $in[1]='
<?php
$val=trim(REX_VALUE[11]);
?>
<h4 align="center">Auswahl einer Gruppe von Quicklinks auf Artikel</h4>
<table class="ql_inherit">
    <tr><td class="ql_nowrap">
            Name der Gruppe:</td>
        <td class="ql_indent">
            <input class="form-control" name="REX_INPUT_VALUE[11]"
                   size="20" value="<?php echo $val; ?>" /></td>
        <td class="ql_indent">
            (darf nicht leer sein)</td></tr>
    <tr valign="top">
        <td class="ql_nowrap">
            <br/>Auswahl der Links:</td>
        <td colspan="2" class="ql_indent">
            <br/>REX_LINKLIST[1 widget=1]</td></tr>
</table>';
   $out[1]='<?php
if(rex::isBackend()):
  $grp =REX_VALUE[11];
  $list=REX_LINKLIST[1];
  echo quicklinks::show_internal_quicklinks($grp,$list);
  endif;
?>';
   $ident[1]=$my_package.'::show_internal_quicklinks';
   $part[1]='output';
   #
   # --- module 2
   $name[2]='Liste externer Links ('.$my_package.')';
   $str='
<?php
$val=trim(REX_VALUE[11]);
?>
<h4 align="center">Eingabe einer Gruppe externer Quicklinks</h4>
<table class="ql_inherit">
    <tr><td class="ql_nowrap">
            Name der Gruppe:</td>
        <td class="ql_indent">
            <input class="form-control" name="REX_INPUT_VALUE[11]"
                   size="20" value="<?php echo $val; ?>" /></td>
        <td class="ql_indent">
            (darf nicht leer sein)</td></tr>
</table><br/>
<table class="ql_inherit">
    <tr><td class="ql_nowrap">
            <tt>Eingabe:</tt></td>
        <td class="ql_indent" width="200">
            <input class="form-control ql_bold"
                   value="URL ; Linktext ; Linktitel" disabled="disabled" /></td>
        <td class="ql_indent">
            (mit ";" getrennt, URL inkl. "http(s)://")</td></tr>';
for($i=1;$i<=10;$i=$i+1) $str=$str.'
    <tr><td class="ql_nowrap">
            Link Nr. '.$i.':</td>
        <td class="ql_indent" colspan="2">
            <input class="form-control" name="REX_INPUT_VALUE['.$i.']"
                   value="REX_VALUE['.$i.']" size="100" /></td></tr>';
$in[2]=$str.'
</table>';
   $out[2]='
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
   $ident[2]=$my_package.'::show_external_quicklinks';
   $part[2]='output';
   #
   # --- the modules data
   $modules=array();
   for($i=1;$i<=count($name);$i=$i+1)
      $modules[$i]=array(
         'name'  =>$name[$i],
         'input' =>str_replace('\\','\\\\',$in[$i]),
         'output'=>str_replace('\\','\\\\',$out[$i]),
         'ident' =>$ident[$i],
         'part'  =>$part[$i]);
   return $modules;
   }
}
?>
