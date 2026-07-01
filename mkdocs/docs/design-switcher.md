# Design-Umschalter

Sunflower 26 bietet verschiedene Designvarianten an: abgerundete oder eckige Formen, helles oder dunkles Farbschema und verschiedene Footer-Farben. Der Design-Umschalter ermöglicht es, diese Varianten im Frontend live zu wechseln.

## Einstellungen im Backend

Unter **Sunflower > Einstellungen > Designvariante** gibt es zwei Checkboxen:

| Einstellung | Beschreibung |
|---|---|
| **Design-Umschalter** | Aktiviert die Umschalt-Funktionalität. Buttons mit den entsprechenden CSS-Klassen können das Design wechseln. Die Auswahl wird im Browser (localStorage) gespeichert. |
| **Design-Umschalter-Menü in der Seitenleiste** | Zeigt zusätzlich ein Menü-Panel an der Seite (Pinsel-Icon unten rechts), mit dem alle Varianten per Dropdown umgeschaltet werden können. Diese Option erscheint nur, wenn der Design-Umschalter aktiviert ist. |

!!! note "Hinweis"
    Das Menü-Panel ist zum Testen und Auswählen des gewünschten Designs gedacht. Für den Dauerbetrieb auf der Website empfiehlt es sich, nur die Button-Variante zu nutzen bzw. den Design-Umschalter generell zu deaktivieren.

## Designvarianten

### Formstil

| Variante | Beschreibung |
|---|---|
| `rounded` | Abgerundete Ecken bei Buttons, Karten und Bildern |
| `sharp` | Eckige, kantige Darstellung |

### Farbschema

| Variante | Beschreibung |
|---|---|
| `light` | Helles Design |
| `green` | Dunkles Design mit grünem Grundton |
| `auto` | Folgt der Systemeinstellung des Browsers (hell/dunkel) |

### Footer-Farbe

| Variante | Beschreibung |
|---|---|
| `sand` | Sandfarbener Footer |
| `green` | Hellgrüner Footer |

Die Footer-Farbe kann nur über das Menü-Panel oder die Backend-Einstellungen geändert werden, nicht über Buttons.

## Buttons mit CSS-Klassen

Du kannst an beliebiger Stelle in Deinem Inhalt Buttons oder andere Elemente platzieren, die das Design beim Klick umschalten. Dafür müssen die Elemente in einen Container mit den passenden CSS-Klassen eingebettet werden.

### Verfügbare CSS-Klassen

Jeder Button benötigt die Basisklasse `design-switcher-trigger` plus eine Variantenklasse:

**Formstil:**

| CSS-Klasse | Wirkung |
|---|---|
| `design-switcher-trigger ds-fs-rounded` | Schaltet auf abgerundetes Design um |
| `design-switcher-trigger ds-fs-sharp` | Schaltet auf eckiges Design um |

**Farbschema:**

| CSS-Klasse | Wirkung |
|---|---|
| `design-switcher-trigger ds-cs-light` | Schaltet auf helles Design um |
| `design-switcher-trigger ds-cs-green` | Schaltet auf dunkles Design um |
| `design-switcher-trigger ds-cs-auto` | Schaltet auf automatische Erkennung um |

Der aktive Button erhält automatisch die Klasse `is-active`.

### Beispiel im Block-Editor

Erstelle einen **Buttons-Block** oder eine **Gruppe** und füge unter "Erweitert > Zusätzliche CSS-Klasse(n)" die gewünschten Klassen ein:

```
design-switcher-trigger ds-fs-rounded
```

Beispiel mit zwei Buttons nebeneinander:

1. Button "Abgerundet" mit den CSS-Klassen: `design-switcher-trigger ds-fs-rounded`
2. Button "Eckig" mit den CSS-Klassen: `design-switcher-trigger ds-fs-sharp`

Die Buttons können beliebig gestaltet werden. Beim Klick wird das Design sofort umgeschaltet und die Auswahl gespeichert.

## Speicherung

Die gewählten Einstellungen werden im **localStorage** des Browsers unter dem Schlüssel `sunflower_design` gespeichert. Beim nächsten Seitenaufruf werden die gespeicherten Einstellungen automatisch angewendet.

Wird der Design-Umschalter im Backend komplett deaktiviert, wird der localStorage-Eintrag beim nächsten Seitenaufruf automatisch gelöscht und die Standardeinstellungen aus dem Backend greifen wieder.
