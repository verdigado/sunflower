# Theme-Entwicklung

## Sprachdateien
- Erzeuge mit `make make-pot` ein neues Template-File
- Öffne `languages/de_DE.po` mit PoEdit
- Gehe dort auf `Übersetzung -> aus POT-Datei aktualisieren`
- Erstelle die Übersetzungen
- Klicke auf *Speichern*

### Sprachdateien für eigene Blöcke

Um für einen eigenen Block eine Sprachdatei zu erstellen bzw. zu aktualisieren muss man wie folgt vorgehen.

Beispiel Block `sunflower-accordion`:

1. im Sunflower-Root-Verzeichnis eine neue POT-Datei erstellen:

```
wp i18n make-pot . languages/sunflower-accordion.pot --slug=sunflower-accordion --domain=sunflower-accordion --exclude=node_modules,src
```

2. Die vorhandene Datei `languages/sunflower-accordion-de_DE.po` öffnet und mit der erstellten POT-Datei aktualisieren (`Übersetzung -> aus POT-Datei aktualisieren`).
3. Aus den PO-Dateien JSON-Dateien erstellen für die Texte in JavaScript-Dateien:

```
wp i18n make-json languages/ --no-purge
```

Für die Nutzung im Editor wird für die JavaScript-Komponente eine Datei `sunflower-accordion-de_DE-31a766b993e67ee3f8daefdd7b73b26d.json` erstellt. Der Hash im Dateinamen wird aus dem Dateinamen und Pfad der JavaScript-Datei gebildet.

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

Das Deployment läuft über GitHub Actions. Beim Erstellen eines Releases wird das CSS und JavaScript gebaut und das ZIP-Archiv gepackt und auf dem Updateserver https://sunflower-theme.de kopiert.

Wichtig: Vor dem Release die Versions-Nummer in `sass/style.scss` anpassen!

Dazu kann man den Schritt `publish` des Makefiles nutzen:

`make publish`

Dadurch wird ein neuer Branch `deploy` angelegt, die Versions-Nummer in `sass/style.scss` gesetzt, das Changelog aktualisiert und der Branch gepusht.
