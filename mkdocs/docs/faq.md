# Häufige Fragen

## Wie komme ich an schöne Bilder?
Kostenlose schöne Bilder gibt es bspw. auf
- [Pixabay](https://pixabay.com)
- [Unsplash](https://unspalsh.com)
- [Pexels](https://pexels.com)

Du kannst auch das [WordPress-Plugin Free Images](https://wordpress.org/plugins/free-images/) installieren und dann innerhalb von WordPress Bilder von Pixabay herunterladen und in deiner Mediathek hinterlegen.

1. Installiere das [WordPress-Plugin Free Images](https://wordpress.org/plugins/free-images/)
2. Gehe im Menü auf *Medien > Download Images*
3. Suche ein Bild aus und lade es in Deine Mediathek
4. Nutze das Bild an jeder beliebigen Stelle in WordPress

## Wie kann ich Soziale Plattformen wie Instagram, Twitter und Facebook verlinken?
Gehe dazu in die Sunflower-Eintellungen und trage die kompletten Webadressen Deiner Profile ein. 
Sie erscheinen dann automatisch in der Fußzeile.

## Ich wünsche mir ein neues Feature, das es nicht gibt
Neue Features kannst Du auf der

* [Projektseite bei gitbub](https://github.com/codeispoetry/sunflower/issues) oder 
* im Chatkanal auf [Chatbegrünung](https://chatbegruenung.de/channel/sunflower)

melden.

## Wie sieht es mit dem Datenschutz aus?
[Mehr zum Datenschutz gibt es auf einer Unterseite.](privacy.md)

## Manche Begriffe sind auf Handys zu lang
Manche Begriffe sind auf Handys zu lang. Sunflower bricht mitten im Wort in eine neue Zeile um, damit das Layout nicht zerschossen wird. Diese Bruchstelle kannst Du selbst definieren, indem Du an die gewünschte Sollbruchstelle ein `&shy;`einträgst. Das ist das ein bedingtes oder weiches Trennzeichen. Wenn nötig, wird an dieser Stelle umgebrochen. Wenn nicht, erscheint an dieser Stelle nichts. 

Auf manchen System kann man den bedingten Trennstrich auch mit der Tastenkombination AltGr + Minus erzeugen.

## Die E-Mails werden verschleiert dargestellt. Wie kann ich das Abschalten
Trage in der *wp-config.php* bitte die Zeile

``define('SUNFLOWER_EMAIL_SCRAMBLE_NO', true);``

ein.
