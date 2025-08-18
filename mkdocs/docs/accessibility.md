# Barrierefreiheit nach WCAG 2.1

Das Sunflower Theme erfüllt die Kriterien nach dem Web Content Accessibility Guidelines (WCAG) des World Wide Web Consortiums (W3C).

## Test der Barrierefreiheit

Wir testen hier auf das Level AA. Referenz ist die [Sunflower Demo-Seite](https://sunflower-theme.de/demo).

Für die Tests nutzen wir verschiedene Tools. Z.B.:

* Firefox Accessibility Tool in der Entwickler-Konsole
* [WAVE-Browser Plugin](https://wave.webaim.org/extension/)
* Das Command-Line-Tool [Pa11y](https://pa11y.org)

Mit  [Pa11y](https://pa11y.org) erfolgt der Test wie folgt:

```bash
pa11y https://sunflower-theme.de/demo --standard WCAG2AA

  > Running Pa11y on URL https://sunflower-theme.de/demo

No issues found!
```

## Hinweise für Redakteure


!!! Info
	Barrierefreiheit ist auch eine redaktionelle Aufgabe!

Die Strukturierung von Texten, die Beschreibung von Bildern und die Einbindung weiterer Plugins kann zu neuen Barrieren führen, die durch das Theme nicht korrigiert werden können.

Für Redakteur*innen haben [wir hier paar Hinweise](https://gcms-intern.de/aktuelles/barrierearmut-fuer-gcms-redakteurinnen) aufgeschrieben. Diese gelten sowohl für TYPO3 als auch WordPress mit Sunflower.

Allgemeines zur Barrierefreiheit auf Webseiten gibt es hier zum Nachlesen: [https://de.wikipedia.org/wiki/Barrierefreies_Internet](https://de.wikipedia.org/wiki/Barrierefreies_Internet).
