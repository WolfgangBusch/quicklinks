# quicklinks
<h4>Version 2.3</h4>
    <li>Bisher war es nicht möglich, eine Quicklinks-Gruppe anzulegen
        und dazu keine Linkzeile zu definieren. In solchen Fällen
        war der Artikel mit den Quicklinks-Gruppen aufgrund eines
        Fehlers nicht mehr editierbar. Jetzt sind auch solche leeren
        Quicklinks-Gruppen möglich.</li>
</ul>
<h4>Version 2.2</h4>
    <li>Jetzt werden alle Blöcke der Quicklinks-Gruppen in einem einzigen
        Artikel definiert. Die Reihenfolge der Gruppen (von links nach
        rechts) ergibt sich aus der Reihenfolge der Blöcke in diesem
        Artikel. Bisher wurde jede Quicklinks-Gruppe als einzelner Block
        in je einem eigenen Artikeln angelegt, sodass für alle Gruppen
        zusammen eine eigene Kategorie mit entsprechenden Artikeln nötig
        war. - Bestehende Quicklinks-Gruppen müssen entsprechend neu
        angelegt werden!!!</li>
    <li>Auf Desktop-Displays wird die Leiste der nebeneinander liegenden
        Quicklinks-Gruppen nicht mehr umgebrochen. Außerdem werden auf
        Desktop-Displays bei fester Breite der Quicklinks-Gruppen die
        Inhalte der PopUp-Zeilen nicht mehr rechts abgeschnitten.</li>
    <li>Auf Smartphone-Displays sind die Quicklinks jetzt normalerweise
        ausgeblendet, können aber über einen Schalter eingeblendet (und
        wieder ausgeblendet) werden (responsives Design).</li>
    <li>Für die Hintergrundfarbe der PopUp-Menüs kann jetzt auch eine
        Transparenz konfiguriert werden. Sie wird daher - wie auch alle
        anderen Konfigurationsfarben - im RGBA-Format abgelegt.</li>
    <li>Die Stylesheet-Datei wird nicht mehr in zwei Ordnern abgelegt,
        sondern nur noch in rex_path::addonAssets('quicklinks').</li>
    <li>Konstanten werden nicht mehr per 'define(...)' vereinbart,
        sondern als Klassen-Konstanten definiert.</li>
<ul>
</ul>
<h4>Version 2.1.2</h4>
<ul>
    <li>Der Programmcode ist überarbeitet. Dadurch sind die Codes der
        Modul-Ausgabeteile verkürzt.</li>
    <li>Das Stylesheet ist überarbeitet. U.a. beträgt die Schriftgröße
        der Quicklinks-Zeilen jetzt tatsächlich 80% der Schriftgröße der
        Quicklinks-Gruppe. Außerdem wird das Stylesheet bereits bei der
        (Re-)Installation erzeugt bzw. überschrieben.</li>
</ul>
<h4>Version 2.1.1</h4>
<ul>
    <li>Lesen und Setzen der Konfigurationsdaten funktioniert jetzt auch
        unabhängig von deren Speicherreihenfolge in der Tabelle rex_config.</li>
</ul>
<h4>Version 2.1.0</h4>
<ul>
    <li>Kleinere Korrekturen am Programmcode zur Vermeidung von PHP-Warnungen.</li>
    <li>Die Konstruktion des Stylesheet ist verbessert. Außerdem sind jetzt
        nahezu alle Stilangaben in das Stylesheet verlegt.</li>
    <li>Änderungen an der Konfiguration werden jetzt ohne re-install sofort
        wirksam.</li>
    <li>Der gesamte Source-Code ist auf UTF-8 umgestellt.</li>
</ul>
<h4>Version 2.0.1</h4>
<ul>
    <li>Die Keys der assoziativen Arrays in den Modulen sind jetzt
        vorschriftsmäßig mit Gänsefüßchen versehen.</li>
</ul>
<h4>Version 2.0.0</h4>
<ul>
    <li>Diese Version ist komplett überarbeitet und auf Redaxo 5 angepasst.</li>
</ul>
