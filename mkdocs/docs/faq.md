# Häufige Fragen

## Wie komme ich an schöne Bilder?

Kostenlose schöne Bilder gibt es z.B. auf

- [Unsplash](https://unsplash.com)
- [pixelio.de](https://www.pixelio.de/) (Registrierung notwendig)
- [Wikimedia Commons](https://commons.wikimedia.org/w/index.php?search=sonnenblumen&title=Special:MediaSearch&type=image&haslicense=unrestricted) (gute Suche, nur freie Lizenzen)

Siehe auch die Empfehlungen des Bundesverbands unter [Das grüne Grundlagen-Design -> Hilfreiche Links](https://www.gruene.de/service/grundlagendesign-2023).

### Lizenz- und Nutzungsbedingungen prüfen!

Prüft unbedingt die Lizenz- und Nutzungsbedingungen der Bilder, die ihr verwenden möchtet. "Kostenlos" heißt nicht, dass man sie in jedem Fall verwenden darf. Manchmal ist es auch notwendig, die Quelle oder die Lizenz mit anzugeben wie z.B. bei [Creative Commons (CC)-Lizenzen](https://de.creativecommons.net/was-ist-cc/).

## Wie kann ich Soziale Plattformen wie Instagram, X (Twitter) und Facebook verlinken?
Gehe dazu in die Sunflower-Einstellungen und trage die kompletten Webadressen Deiner Profile ein.
Sie erscheinen dann automatisch in der Fußzeile und optional in der Kopfleiste. Siehe dazu [Icons in der Fußleiste](/setup/#teile-in-sozialen-medien)

## Ich wünsche mir ein neues Feature, das es nicht gibt
Neue Features kannst Du auf der

* [Projektseite bei GitHub](https://github.com/verdigado/sunflower/issues) oder
* im Chatkanal auf [Chatbegrünung](https://chatbegruenung.de/channel/sunflower-wordpress)

melden.

## Wie sieht es mit dem Datenschutz aus?
[Mehr zum Datenschutz gibt es auf einer Unterseite.](privacy.md)

## Manche Begriffe sind auf Handys zu lang
Manche Begriffe sind auf Handys zu lang. Sunflower bricht mitten im Wort in eine neue Zeile um, damit das Layout nicht zerschossen wird. Diese Bruchstelle kannst Du selbst definieren, indem Du an die gewünschte Sollbruchstelle ein `&shy;` einträgst. Das ist das ein bedingtes oder weiches Trennzeichen. Wenn nötig, wird an dieser Stelle umgebrochen. Wenn nicht, erscheint an dieser Stelle nichts.

Um `&shy;` einzutragen, gehe wie folgt vor:
Variante 1:

- füge über die Tastenkombination ALT+0173 das Trennzeichen ein

oder Variante 2:

- gehe unter Optionen auf 'in HTML bearbeiten'
- füge es dann ein
- und wechsle danach wieder in den visuelle Editor


Auf manchen System kann man den bedingten Trennstrich auch mit der Tastenkombination AltGr + Minus erzeugen.

## Die E-Mails werden verschleiert dargestellt. Wie kann ich das Abschalten
Trage in der *wp-config.php* bitte die Zeile

``define('SUNFLOWER_EMAIL_SCRAMBLE_NO', true);``

ein.
