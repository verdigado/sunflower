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

Das Deployment läuft über GitHub Actions (`.github/workflows/build-deploy.yml`).
Ausgelöst wird es **ausschließlich durch das Veröffentlichen eines GitHub-Releases**
mit einem Tag, der mit `v` beginnt (z. B. `v3.0.9`). Beim Veröffentlichen wird
das CSS und JavaScript gebaut, das ZIP-Archiv gepackt und auf den Updateserver
https://sunflower-theme.de kopiert.

### Ablauf

1. **Versions-Nummer setzen** in `sass/style.scss` – **ohne** führendes `v`,
   also z. B. `Version: 3.0.9`.

    !!! warning "Kein `v` voranstellen!"
        Die CI prüft, ob `v` + Versionsstring aus `style.scss` exakt dem Release-Tag
        entspricht (`v3.0.9`). Steht in `style.scss` bereits `v3.0.9`, ergibt die
        Prüfung `vv3.0.9` und der Build **schlägt fehl**.

2. **Changelog aktualisieren** mit `make changelog` (bzw. `php create-changelog.php`).

3. **Release veröffentlichen** – das ist der eigentliche Auslöser des Deployments.
   Der zugehörige Tag wird dabei angelegt und muss auf den aktuellen `main`-Stand zeigen:

    ```
    gh release create v3.0.9 --title v3.0.9 --generate-notes --latest
    ```

    Alternativ über die GitHub-Weboberfläche: *Releases → Draft a new release →*
    neues Tag `v3.0.9` auf `main`, *Set as latest release → Publish release*.

### Der Makefile-Schritt `make publish`

`make publish` **allein veröffentlicht keinen Release** und löst somit **kein**
Deployment aus. Der Schritt setzt lediglich die Versions-Nummer in
`sass/style.scss`, aktualisiert das Changelog und legt einen Branch `deploy` an
bzw. pusht ihn. Das Erstellen des Tags und des Releases (Schritt 3 oben) ist im
Makefile auskommentiert und muss manuell erfolgen.
