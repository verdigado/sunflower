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

Das Deployment läuft über GitHub Actions und wird durch das Erstellen eines GitHub Releases ausgelöst.

### Stabile Releases

Das Deployment läuft vollständig automatisiert über GitHub Actions:

1. **GitHub Release erstellen:**
    - Auf GitHub ein neues Release erstellen
    - Tag-Format: `v3.0.10` (mit `v`-Prefix)
    - Die GitHub Action *Build and Deploy* wird automatisch ausgelöst

2. **Automatischer Ablauf:**
    - **Version:** Die Action nutzt die Version aus `sass/style.scss` (die bereits auf main dem Release-Tag entsprechen muss).
    - **Build:** Baut CSS, RTL-CSS und JavaScript.
    - **Checks:** Führt PHPCS-Checks durch.
    - **Artifacts:** Erstellt das ZIP-Bundle und lädt es als Release-Artifact hoch.
    - **Deploy:** Deployt ZIP, Versionsdatei und Changelog auf den Updateserver und aktualisiert die Demoseite.
    - **Post-Release Bump:** Der Workflow erhöht automatisch die Versionsnummer in `sass/style.scss` auf das nächste Patch-Level und committet diese zurück auf main, sodass main immer bereit für das nächste Release ist.

**Wichtig:** Da die Action die Version in `sass/style.scss` prüft, muss für Minor- oder Major-Releases die Nummer in der Datei vor dem Release manuell auf main aktualisiert werden.

### Beta-Releases

1. **Version setzen:**
    ```
    make publishbeta
    ```
    Fragt nach der Versionsnummer (z.B. `3.0.10-beta-1`), erstellt einen `deploy-beta`-Branch.

2. **GitHub Release erstellen:**
    - Tag-Format: `v3.0.10-beta-1` (mit `-alpha`, `-beta` oder `-rc` im Tag)
    - Als Pre-Release markieren
    - Die GitHub Action *Build and Deploy Beta release* wird ausgelöst

Beta-Releases führen zusätzlich SCSS- und JS-Linting durch und werden als Pre-Release markiert.
