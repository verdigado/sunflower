# Individuelles CSS

## Vergebene Klassennamen

Es sind alle Klassennamen von Bootstrap 5 möglich.

## Mindesthöhe für die Kästen in Beitragsübersichten

dadurch werden die meisten Kästen gleich hoch
```
archive-loop article {
    min-height: 525px;
}
```

## Aktuellen Menüpunkt hervorheben

```
.current-menu-item {
    background: #FFEE00;
    color: black;
}
```


## Ganz obere Menüleiste ausblenden

```
.topmenu {
    display: none;
}
```

## Hintergrundfarbe für Logo im Menü anpassen

Die folgende Einstellung setzt die Hintergrundfarbe für das eigene Logo im Sticky-Menü (angezeigtes Menü beim Scrollen):

```
.wp-custom-logo .navbar-main.stuck .navbar-brand {
	background-color: #005437;
}
```
Die folgende Einstellung setzt die Hintergrundfarbe für das eigene Logo im Personal-Header:
```
.header-personal .img-container {
    background-color: #005437;
}
```
Die Beispiele setzen die Hintergrundfarbe auf "Tanne", wie es mit der Sonnenblume der Fall ist.

## Suche in der oberen Menüleiste ausblenden

```
#topmenu-container .search, #topmenu-container .show-search {
    display: none !important;
}
```

## Hintergrundbild im Block "Nächste Termine" ändern

- Lade ein neues Bild in die Mediathek hoch
- Öffne das Bild in der Mediathek und klicke auf den Button "URL in die Zwischenablage kopieren"
- füge diese URL (siehe unten) ein

```
.next-events {
    background-image: url(assets/img/sunflower1.jpg);
}
```

## Sehr lange Menüs scrollbar machen

```
.navbar-main.stuck {
    position: relative;
}
```

!!! Danger "Menü bleibt nicht mehr oben angeheftet!"
    Mit der o.a. Änderung bleibt das Menü nicht mehr angeheftet und scrollt mit.
	Gerne würden wir beides unter einen Hut bekommen. Wer hier eine Lösung findet, kann sich gerne
	auf [GitHub](https://github.com/verdigado/sunflower) melden.

## Eigene Klassennamen

| CSS Klasse | Funktion |
|---------------|--------------|
|`.has-shadow`  | erzeugt Schatten |
|`.no-link`     | Keine Linkfarbe |
|`.same-height` | damit in Spalten alle Bilder gleich hoch sind |
|`.max-800`     | erwingt Maximalbreite |
|`.no-gap`      | löscht den Abstand zwischen 2 Spalten |
