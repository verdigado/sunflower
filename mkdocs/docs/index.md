# Einleitung

Dies ist die Anleitung für das Sunflower-WordPress-Theme.

## Voraussetzungen
* WordPress 6
* Sunflower Theme 2.0.x: PHP 7.4 oder 8.2
* Sunflower Theme 2.1.x: PHP 8.2

## Features
* einfacher Umzug von Urwahl3000
* eine Musterstartseite erstellt man mit nur einem Knopfdruck
* eigene Terminverwaltung, Kalender können per ics importiert werden
* responsives Layout für verschiedene Bildschirmgrößen (Handys, Tablet, Desktops)

## Neu mit Version 2.1
* **Terms of Use** - Nutzende müssen bestätigen, um das Logo der Grünen benutzen zu können. [Hier ist beschrieben, wie es geht.](/setup/#theme-aktivieren)
* **Button-Highlight** - es wurde iene Klasse geschaffen, um ein Menü Item in der Hauptnavigation hervorzuheben. [Mehr dazu](/menus/#wie-setze-ich-einen-highlight-button)
* **Textbalken** - hebe Überschriften oder Schriften hevor, indem du sie mit Textblaken hinterlegst. [Wie du sie benutzt, wird hier erläutert](/typography/)
* **Logo im Header** - wir haben die beiden Header angepasst, damit die Sonnenblumme auf dem grünen Hintergrund erscheint.
* **RSS-Block überarbeitet** - damit sehen Einbindungen über einen RSS-Feed besser aus.
* **Vorlage für Kandidierende** - wir haben zu den Wahlen eine Vorlage geschaffen, die du dir in deine Seite reinladen kannst. [Die Anleitung dazu, findest du hier](/blocks/#vorlagen)
* **Terminblock Anpassungen** - der Terminblock wurde Übersetzt und eine Silbentrennung für Texte hinzugefügt.
* PT Sans ist nun auch in kursiver Schrift möglich.
* Font-Awesome Update - dadurch stehen nun auch Bluesky und Threads Icons zur Verfügung
* *Bug* TAB-Navigation - die Barrierefreiheit wurde wieder hergestellt.
* *Bug* iCal-Termin-Import - es kam zu Fehlern bei Ausnahmen von Wiederholungsterminen.

## Technik
* basiert auf [Bootstrap 5](https://getbootstrap.com/docs/5.0/getting-started/introduction/)
* Buildprozesse für SCSS, JavaScript und Übersetzungen
* basiert auf [underscores](https://underscores.me/)
* [mehr zur Technik](development.md)
