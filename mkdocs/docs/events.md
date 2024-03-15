# Veranstaltungen / Termine

## Termin manuell anlegen
Termine sind ein eigener Inhaltstyp, ähnlich wie Beiträge und Seiten. Du findest sie im Backend-Menü als extra Menüpunkt *Termine*. Dort kannst Du auf *Erstellen* klicken.

Ein Termin besteht aus einer normalen Überschrift und normalem Inhalt, genauso wie Seiten und Beiträge. Zusätzlich gibt es in der Seitenleiste den Bereich *Termin*. Hier kannst Du folgendes einstellen:

- Start- und ggf. Enddatum, mit Uhrzeit
- ganztägiger Termin oder nicht
- Veranstaltungsort, Straße und Stadt

Außerdem kannst Du eine Markierung auf einer Landkarte [OpenStreetMap](https://www.openstreetmap.de/) setzen. Diese Landkarte wird nach einem Opt-In-Klick durch die Besucherin im Frontend angezeigt.

## Übersichtsseite im Menü einfügen
- Design > Menüs
- Termine > alle anzeigen
- Den obersten Eintrag zum Menü hinzufügen

Wenn Du keine "Termine" siehst, klicke rechts oben auf das Zahnrad und blende Termine ein.

### In der Menüansicht werden keine Termine angezeigt
- Klicke auf *Ansicht anpassen* und
- setze den Haken bei Termine

### Die Terminseite wird im Frontend nicht gefunden
Wenn die Seite nicht gefunden wird, musst Du die die Permalink-Struktur neu einlesen.
Das kannst Du direkt in den Sunflower-Einstellungen machen unter Einstellungen -> Permalinks.

Alternativ kannst Du folgendes probieren:
- Einstellungen Permalinks
- Wähle eine beliebiege andere Einstellung aus und speichere sie
- Wähle Deine bisherige Einstellung wieder aus und speichere sie
- Fertig. Jetzt muss der Link im Menü funktionieren

## Termine über ical importieren

*Sunflower* kann Termine aus mehreren externen Kalendern per iCal-URL importieren.

Bitte trage diese iCal-URLs in den Sunflower-Einstellungen ein. Die Termine werden regelmäßig automatisch aktualisiert. Solche Termine darfst Du nicht mehr im WordPress-Backend bearbeiten, weil Änderungen wieder überschrieben würden.

Du kannst das Importieren von Termine auch gewollt auslösen (zusätzlich zum automatischen Import alle paar Stunden), indem Du in den Sunflower-Einstellungen auf *Kalender importieren* klickst.

Importiert werden

- Titel
- Beschreibung
- Start- und Enddatum
- Kategorien (technisch sind das WordPress-Schlagwörter)
- Ort (dazu holt sich *Sunflower* selbstständig Geodaten, siehe unten)#
- Auf Wunsch bekommen Termine eines Kalenders eine automatische Kategorie in WordPress zugewiesen. Trage dazu in den Einstellungen nach der KalenderURL einen Strichpunkt ; ein und dann den beliebigen Namen der automatisch zugewiesenenen Kategorie.

### Externe Kalender (z.B. Wolke) importieren

Du kannst jeden Kalender importieren, der eine öffentliche URL im iCal-Format anbietet. In der grünen Wolke gibt es auch einen Service für grüne Mitglieder und Gliederungen. Unter
[wolke.netzbegruenung.de -> Kalender](https://wolke.netzbegruenung.de/apps/calendar/dayGridMonth/now)
findest Du Deine eigenen Kalender. Dort kanst Du mehrere Kalender anlegen, z.B. einen nur für die Webseite.

Klicke auf

1. *+ Neuer Kalender* und vergebe einen beliebigen Namen
2. Neben dem Kalendername gibt es ein Kettensymbol, damit öffnet sich ein kleine Menü
3. hierin gibt es ein Untermenü, dass Du mit den drei Punkten öffnen kannst
4. anschließend klickst Du auf *Abonnement-Link kopieren*
5. diesen trägst Du in den *Sunflower-Einstellungen* ein

Eine bebilderte Anleitung findest du auch [hier](https://gcms-intern.de/anleitungen/single/termine-anlegen#c892817).

### Import-Häufigkeit festlegen
Standardmäßig holt sich *Sunflower* alle drei Stunden neue Daten vom externen Kalender. Um diese Zeit zu ändern, kannst Du in der wp-config.php folgendes eintragen:

``define('SUNFLOWER_EVENT_IMPORT_EVERY_N_HOUR', 1);``

wobei die 1 hier im Beispiel für jede Stunde steht.

### Anzahl der zu importierenden Termine festlegen
Standardmäßig holt sich *Sunflower* alle Termie der nächsten sechs Monate vom externen Kalender. Um dies zu ändern, kannst Du in der wp-config.php folgendes eintragen:

``define('SUNFLOWER_EVENT_TIME_RANGE', '3 months');``

Von Haus aus werden vergangene Termine nicht mehr importiert. Um das zu ändern, kann Du in der wp-config.php folgendes eintragen:

``define('SUNFLOWER_EVENT_TIME_RANGE_BACK', '3 months');``

Dabei kannst Du bspw. folgendes nutzen

- 4 weeks
- 9 months
- 1 year

### Anzahl der sich wiederholenden Termine beim Import festlegen
Sich wiederholende Termine werden bis längstens in einem Jahr importiert. Du kannst die Höchstzahl der zu
importierenden Termine bei sich wiederholenden Terminen so festlegen:
``define('SUNFLOWER_EVENT_RECURRING_EVENTS', 10);``

### Geodaten
Die Information zum Veranstaltungsort werden über [Nominatim](https://nominatim.openstreetmap.org/ui/) auf einer Landkarte gesucht. Die Geokoordinaten werden lokal gespeichert. Pro Import-Lauf werden höchstens drei neue Orte über Nominatim gesucht, damit die Import-Performance hoch bleibt.

Fehlerhafte Geodaten können in den Sunflower-Einstellungen unter Termine korrigiert werden. Sie werden beim nächsten Import-Lauf wirksam.

### Zoom-Level für importierte Termine festlegen
Importierte Termine haben standardmäßig das Zoom-Level 12. Wenn Du davon abweichen möchtest, trage in der *wp-config.php* bitte die Zeile

``define('SUNFLOWER_EVENT_IMPORTED_ZOOM', 10);``

ein, wobei die *10* das neue Zoomlevel angibt. Es wird werst beim nächsten Import verwendet, diesen Import kannst Du aber manuell triggern (wie, siehe oben).
