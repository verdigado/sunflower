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

## Aktuellen Menüpunkt hervorhen
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

## Eigene Klassennamen

| CSS Klasse | Funktion |
|---------------|--------------|
|`.has-shadow`  | erzeugt Schatten |
|`.no-link`     | Keine Linkfarbe |
|`.same-height` | damit in Spalten alle Bilder gleich hoch sind |
|`.max-800`     | erwingt Maximalbreite |
