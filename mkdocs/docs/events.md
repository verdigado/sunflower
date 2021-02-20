# Veranstaltunge / Termine

## Termin anlegen
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

## In der Menüansicht werden keine Termine angezeigt
- Klicke auf *Ansicht anpassen* und
- setze den Hake bei Termine

## Die Terminseite wird im Frontend nicht gefunden
Wenn die Seite nicht gefunden wird, musst Du die die Permalink-Struktur neu einlesen.
Das machst Du folgendermaßen:
- Einstellungen Permalinks
- Wähle eine beliebiege andere Einstellung aus und speichere sie
- Wähle Deine bisherige Einstellung wieder aus und speichere sie
- Fertig. Jetzt muss der Link im Menü funktionieren

## Termine über ical importieren
Sunflower importiert Termine aus mehreren externen Kalendern, auf die per iCal-Datei zugeriffen wird. Voraussetzung ist, dass der Kalender eine iCal-Datei (Endung meist .ics) per URL zur Verfügung stellt.

Bitte trage diese iCal-URLs in den Sunflower-Einstellungen ein. Die Termine werden automatisch alle drei Stunden importiert. Beachte bitte, dass Du solche Termine im WordPress-Backend nur vorsichtig editierst. Du darfst nur solche Felder editieren, die nicht importiert wurden. Denn diese werden beim nächsten Import überschrieben. Zu den Feldern, die überschrieben werden, gehören:

- Titel
- Inhalt
- Startdatum
- Enddatum

Du kannst das Importieren von Termine auch gewollt auslösen (zusätzlich zum automatischen Import alle paar Stunden), indem Du in den Sunflower-Einstellungen auf *Kalender importieren* klickst.

Importiert werden alle Termine der nächsten 6 Monate.