# Änderungsprotokoll

Hier findest du eine Liste, über Änderungen, die mit den verschiedenen Versionen erschienen sind.

## Version 2.2

Release: [*03. September 2024*](https://github.com/verdigado/sunflower/releases/tag/v2.2.0)

* **3-spaltige Beiträge** - Der Block "Neueste Beiträge" kann jetzt auch mit 3 Spalten genutzt werden.
* **Bestätigungsmail an Absender** - Im Sunflower Formular wurde ergänzt, dass der Absender eine Bestätigungsmail erhalten kann.
* **Anpassbare Empfänger im Kontaktformular** - Es können nun Empfänger im Kontaktformular individuell hinterlegt werden.
* **Vorlage Linkseite** - Nutzende können sich eine Linkseite als Vorlage in die Hompage integrieren und anpassen. Damit bieten wir eine Alternative zu Linktree und Wonderlink. [Die Anleitung dazu, findest du hier](blocks.md#vorlagen)
* **Pixabay Stockfotos** - Pixabay hat die Nutzung ihrer Bilder für politische Inhalte verboten - diese wurden deshalb komplett ausgetauscht.
* **SEO Verbesserung** - In den Metadaten wird der [WebSite Name nach schema.org](https://developers.google.com/search/docs/appearance/site-names?hl=de#website) standardmäßig ausgegeben.

## Version 2.1

Release: [*07. Mai 2024*](https://github.com/verdigado/sunflower/releases/tag/v2.1.0)

* **Nutzungsbedingungen** - Nutzende müssen bestätigen, um das Logo der Grünen benutzen zu können. [Hier ist beschrieben, wie es geht.](setup.md#theme-aktivieren)
* **Button-Highlight** - es wurde eine Klasse geschaffen, um ein Menü Item in der Hauptnavigation hervorzuheben. [Mehr dazu](menus.md#wie-setze-ich-einen-highlight-button)
* **Textbalken** - hebe Überschriften oder Schriften hervor, indem du sie mit Textbalken hinterlegst. [Wie du sie benutzt, wird hier erläutert](typography.md)
* **Logo im Header** - wir haben die beiden Header angepasst, damit die Sonnenblume auf dem grünen Hintergrund erscheint.
* **RSS-Block überarbeitet** - damit sehen Einbindungen über einen RSS-Feed besser aus.
* **Vorlage für Kandidierende** - wir haben zu den Wahlen eine Vorlage geschaffen, die du dir in deine Seite reinladen kannst. [Die Anleitung dazu, findest du hier](blocks.md#vorlagen)
* **Neuste Beiträge (Sunflower)** - du kannst nun bequem zwischen einer Kachel- und Listenansicht auswählen. [Wie das geht, haben wir hier beschrieben.](blocks.md#neuste-beitrage-sunflower)
* **Terminblock Anpassungen** - der Terminblock wurde Übersetzt und eine Silbentrennung für Texte hinzugefügt.
* **kursive Schriften** - PT Sans ist nun auch in kursiver Schrift möglich.
* **Font-Awesome Update** - dadurch stehen nun auch Bluesky und Threads Icons zur Verfügung
* **Refactor von Blöcken** - die Handhabung wird intuitiver, da z.B. direkt im Backend angezeigt wird, was man einstellt. Zudem sind bspw. Listenansichten möglich.
* **Wordpress Coding und Security Standards** - wir haben erste Schritte unternommen den Codingstandards von Wordpress zu entsprechen, um langfristig gesehen das Theme in den Wordpress Theme download Bereich zu bekommen.
* *Bug* TAB-Navigation - die Barrierefreiheit wurde wieder hergestellt.
* *Bug* iCal-Termin-Import - es kam zu Fehlern bei Ausnahmen von Wiederholungsterminen.
* *Bug* ICS-Parser - der ICS Parser wurde im backend gegen einen aktuelleren Parser ausgetauscht.
