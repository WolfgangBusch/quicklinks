<?php
/**
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Juli 2018
 */
#
# --- Beschreibung
$string='
<div><b>Erzeugung der Quicklinks:</b></div>
<div style="padding-left:20px;">
Das AddOn stellt zwei Module zur Verfügung, mit denen jeweils eine Gruppe
von Linkzeilen (&quot;Quicklinks-Gruppe&quot;) erzeugt werden kann. Der
eine Modul definiert eine Gruppe von Links auf Redaxo-Artikel (interne
Links), der andere eine Liste externer Links.<br/>
Eine Quicklinks-Gruppe wird dann durch einen Artikel gebildet, der nur
genau einen der entsprechenden Blöcke enthält. Der Artikel kann
<code>offline</code> bleiben, da er keine Frontend-Ausgaben macht.<br/>
Sinnvollerweise richtet man für für die Quicklinks-Gruppen eine eigene
Kategorie ein. Die Reihenfolge der Gruppen (von links nach rechts)
ergibt sich aus der Reihenfolge der Artikel in der Kategorie.</div>

<div><br/><b>Anzeige der Quicklinks:</b></div>
<div style="padding-left:20px;">
Die Quicklinks-Gruppen werden durch den Aufruf der PHP-Funktion
<code>quicklinks::print_quicklinks()</code> angezeigt. Dieser
Aufruf erfolgt sinnvollerweise im Seitentemplate.</div>

<div><br/><b>Konfigurierbare Styles:</b></div>
<ul>
    <li>Breite der Gruppen und Linkzeilen</li>
    <li>Textgröße der Gruppen (die der Linkzeilen ist 20% kleiner)</li>
    <li>Hintergrundfarbe, Textfarbe und Randfarbe der Gruppen</li>
    <li>Hintergrundfarbe und Textfarbe der Linkzeilen</li>
</ul>
';
echo utf8_encode($string);
?>
