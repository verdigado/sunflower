# Themeentwicklung

## Sprachdateien
- Erzeuge mit `make make-pot` ein neues Template-File
- Öffne `languages/de_DE.po` mit PoEdit
- Gehe dort auf *Katalog -> Aus POT-Datei aktualisieren*
- Erstelle die Übersetzungen
- Klicke auf *Speichern*

## Dokumentation
- Starte `make mkdocs-serve` *mkdocs*
- Die Dokumentation siehst Du unter localhost:
- Bearbeite die Dokumentation unter *mkdocs/docs*
- Baue die Dokumentation mit `make mkdocs-build`

## CSS
- ``npm run watch``, um css zu kompilieren und eine Source-Map zu erhalten
- Dateien befinden sich im Ordner *sass*

## Blöcke
- ``npm run start``, um den Watcher für JS-Files zu starten
- Dateien befinden sich im Ordner *src*

### Neue Block-Vorlage hinzufügen
Lege dazu eine neue Datei im Verzeichnis *functions/block-patterns/seiten* an.
Fertig.

## Lehrvideo erstellen
- Anzeigeeinstellungen auf 1280x720 (16:9)
- ggf. primären Bildschirm ändern (um die Topbar zu verstecken)
- ggf. unter Darstellung das Dock verschieben
- Kazam, Vollbild
- Bearbeitung mit Kdenlive
- resize mit ``ffmpeg -i input.mp4 -vf scale=960:540,setsar=1:1 output.mp4``
- thumbnail mit ``ffmpeg -i input.mp4 -ss 00:00:01.000 -vframes 1 output.png``

## Publishing
``make publish``

or
1. edit version in style.scss
1. git add . && git commit
1. git tag -a v0.1.9 -m "message"
1. edit release_notes.html
1 .git push --follow-tags
4. increment version in style.scss and add beta, nightly-build, etc.



