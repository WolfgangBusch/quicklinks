# quicklinks
<h3>Kompakte Linklisten (Quicklinks)</h3>

<div>Dieses AddOn bietet ein System zur Erzeugung und Darstellung
kompakter Listen von Links. Entsprechende Listen können im Seitentemplate
eingebunden werden und so auf jeder Seite den Zugriff auf wichtige
Artikel mit nur einem einzigen Klick gestatten ("Quicklinks").</div>

<div>Um den Platzbedarf für die Quicklinks möglichst gering zu halten,
sind sie in Gruppen nebeneinander angeordnet. Die Linkliste einer
Gruppe ist zeilenweise angeordnet. Sie poppt erst beim Überfahren
mit der Maus auf. Dafür stehen entsprechende Javascript-Funktionen
zur Verfügung. Die Stylesheets zur Darstellung der Quicklinks sind
konfigurierbar.</div>

<div><br/><b>Erzeugung der Quicklinks:</b></div>
<div>Das AddOn stellt zwei Module zur Verfügung, mit denen jeweils
eine Gruppe von Linkzeilen ("Quicklinks-Gruppe") erzeugt werden kann.
Der eine Modul definiert eine Gruppe von Links auf Redaxo-Artikel
(interne Links), der andere eine Liste externer Links.<br/>
Eine Quicklinks-Gruppe wird dann durch einen Artikel gebildet, der
nur genau einen der entsprechenden Blöcke enthält. Der Artikel
kann offline bleiben, da er keine Frontend-Ausgaben macht.</div>

<div><br/><b>Anzeige der Quicklinks:</b></div>
<div>Die Quicklinks-Gruppen werden durch den Aufruf der PHP-Funktion
quicklinks::print_quicklinks() angezeigt. Dieser Aufruf erfolgt
sinnvollerweise im Seitentemplate.</div>






