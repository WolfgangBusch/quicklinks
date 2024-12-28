<?php
/*
 * Quicklinks AddOn
 * @author wolfgang[at]busch-dettum[dot]de Wolfgang Busch
 * @package redaxo5
 * @version Dezember 2024
 */
#
# --- Beschreibung
echo '
<div><b>Erzeugung der Quicklinks:</b></div>

<div>Das AddOn stellt zwei Module zur Erzeugung jeweils einer
Gruppe von Linkzeilen ("Quicklinks-Gruppe") zur Verfügung.
Der eine Modul definiert eine Liste von internen Links, d.h.
Links auf Redaxo-Artikel, der andere eine Gruppe externer
Links. An passender Stelle wird ein (in der Regel nur ein
einziger) Artikel angelegt, dessen Blöcke aus den oben
genannten Modulen erzeugt werden. Jeder dieser Blöcke
definiert eine Quicklinks-Gruppe. Die Reihenfolge der Blöcke
im Artikel bestimmt die Reihenfolge der Darstellung der
Quicklinks-Gruppen. Der Artikel kann <i>offline</i> bleiben,
da er keine Frontend-Ausgaben macht.</div>

<div><br><b>Anzeige der Quicklinks:</b></div>

<div>Die Quicklinks-Gruppen werden durch den Aufruf der
PHP-Funktion <code>quicklinks::print_quicklinks()</code>
angezeigt. Dieser Aufruf erfolgt sinnvollerweise im
Seitentemplate.</div>

<div><br><b>Konfigurierbare Styles:</b></div>

<div>- Breite der Gruppen und Linkzeilen<br>
- Textgröße der Gruppen (die der Linkzeilen ist 20% kleiner)<br>
- Hintergrundfarbe, Textfarbe und Randfarbe der Gruppen<br>
- Hintergrundfarbe und Textfarbe der Linkzeilen</div><br>
';
?>
