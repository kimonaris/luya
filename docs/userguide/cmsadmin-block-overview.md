CMS INHALTSBLÖCKE
=================

Überblick über alle standardmässig verfügbaren CMS Blöcke.


| Block Name          | Typ      |  Beschreibung
| ------------------- | ---------| -------------
| Überschrift    	  | Einfach  | Titel
| Text		          | Einfach  | Text Block
| Texteditor		  | Einfach  | Text mit Formatierung über einen Editor
| Zitat               | Einfach  | Ein besonders hervorgehobenes Zitat
| Bild		          | Einfach  | Einfaches Bild
| Karte	              | Einfach  | Google Map Kartenausschnitt mit Anzeige einer definierten Adresse
| Auflistung          | Einfach  | Liste
| Aufzählung          | Einfach  | Durchnummerierte Liste
| Code                | Einfach  | HTML Code Block
| Link                | Einfach  | HTML Link zu einer URL
|                     |          |
| Trenner             | Struktur | Trennlinie
| Abstandhalter       | Struktur | Variable Leerfläche


Block-Überschrift
-----------------

### User-Info:

Setzt eine Überschrift in einer bestimmten Grösse.

Beispiel:
Bei Auswahl der Grösse "Mittel" wird die Überschrift mit dem \<H2\> HTML-Tag im Frontend gesetzt:

`<h2>Überschrift</h2>`

### Entwickler-Info:

| Gruppe        | Variable      | Label-Name      | Typ             | Optionen
|---------------|---------------|-----------------|-----------------|---------------
| vars          | content       | Titel           | zaa-text        | -
| vars          | headingType   | Grösse          | zaa-select      | h1/Gross, h2/Mittel, h3/Klein (value/label)


