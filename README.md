# quicklinks
<h3>Kompakte Linklisten (Quicklinks)</h3>

<div>Dieses AddOn gestattet die Erzeugung und Darstellung kompakter
Listen von Links, die den Zugriff auf wichtige Artikel mit nur einem
einzigen Klick gestatten.</div>

<div>Diese "Quicklinks" werden in Gruppen zusammengefasst.
Deren zugehörige Linklisten werden erst beim Überfahren mit der
Maus bzw. beim Antippen (Touchscreen) als PopUp-Menüs sichtbar.
Auf Desktop-Displays sind die Gruppen in einer Zeile nebeneinander
angeordnet. Auf Smartphones können sie über ein Menü-Icon angezeigt
werden und sind dann übereinander angeordnet.</div>

<div>Die Stylesheets zur Darstellung der Quicklinks sind
konfigurierbar.</div><br/>

<div><b>Erzeugung der Quicklinks:</b></div>

<div>Das AddOn stellt zwei Module zur Erzeugung jeweils einer
Gruppe von Linkzeilen ("Quicklinks-Gruppe") zur Verfügung.
Der eine Modul definiert eine Liste von internen Links (auf
Redaxo-Artikel), der andere eine Gruppe externer Links.<br/>
An passender Stelle wird ein Artikel angelegt. Dessen Blöcke
aus den oben genannten Modulen bilden die Quicklinks-Gruppen.
Die Reihenfolge der Blöcke im Artikel bestimmt die Reihenfolge
der Darstellung der Quicklinks-Gruppen. Der Artikel kann
<i>offline</i> bleiben, da er keine Frontend-Ausgaben macht.</div>

<div><br/><b>Anzeige der Quicklinks:</b></div>

<div>Die Quicklinks-Gruppen werden durch den Aufruf der
PHP-Funktion <code>quicklinks::print_quicklinks()</code>
angezeigt. Dieser Aufruf erfolgt sinnvollerweise im
Seitentemplate.</div>

<div><br/><b>Konfigurierbare Styles:</b></div>

<div>- Breite der Gruppen und Linkzeilen<br/>
- Textgröße der Gruppen (die der Linkzeilen ist 20% kleiner)<br/>
- Hintergrundfarbe, Textfarbe und Randfarbe der Gruppen<br/>
- Hintergrundfarbe und Textfarbe der Linkzeilen</div><br/>