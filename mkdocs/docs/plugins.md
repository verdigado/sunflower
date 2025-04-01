# Empfohlene Plugins

Es gibt eine unübersehbare Menge an [WordPress Plugins](https://de.wordpress.org/plugins/) um die Funktionen zu erweitern. Hier möchten wir einige wenige vorstellen.

## ActivityPub - Fediverse ohne Aufwand

Mit dem Plugin [Activitypub](https://de.wordpress.org/plugins/activitypub/) wird deine Webseite Teil des Fediverse. Über Mastodon kann man folgen, Beiträge favorisieren und teilen.

Das Plugin muss einmalig unter `Einstellungen -> ActivityPub` konfiguriert werden. Danach brauchst Du Dich nicht mehr drum zu kümmern.

### Tipp

1. Unter Einstellungen -> Profile wähle "Nur Blog-Profil"
2. Unter Blog-Profil wähle
  	* einen "Avatar". Die Sonnenblume aus dem Sunflower-Theme wird nicht automatisch übernommen. Nimm z.B. [sunflower.png](https://sunflower-theme.de/wp-content/themes/sunflower/assets/img/sunflower.png), wenn es die Sonnenblume sein soll.
	* ein Header-Bild

## Embed Privacy

Das Plugin [Embed Privacy](https://de.wordpress.org/plugins/embed-privacy/) verhindert das Laden externer Inhalte (CSS, Fonts, Bilder, Videos) von Social Media und Video-Platformen ohne, dass der\*die Besucher\*innen der Seite zugestimmt hat.

Es ist nichts weiter zu machen, als das Plugin zu installieren.

!!! Note "Twingle-Spendenformular mit Embed Privacy nutzen"
	Wenn du das Embed des [twingle-Spendenformulars](donationform.md) einstellen oder deaktivieren möchtest, kannst du unter `Einstellungen > Embed Privacy > Embeds verwalten > Neuen Beitrag erstellen` twingle als Embed hinzufügen. Verwende dazu das Regex-Muster `/twingle\.de/`.
	
	Da twingle keine Cookies setzt, kann das Overlay von Embed Privacy für diesen Embed-Anbieter deaktiviert werden.
  	
  	Embed Privacy funktioniert nur mit dem Code-Snippet ("Optimiert für Homepagebaukästen") von twingle und nicht mit dem twingle-Plugin.
