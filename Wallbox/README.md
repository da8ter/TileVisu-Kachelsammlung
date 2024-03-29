# Wallbox Kachel
![Wallbox-Kachel](https://github.com/da8ter/images/blob/main/wallbox.png)

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Die Anzeige umfasst alle relevanten Informationen einer Wallbox und ist speziell für Go-e Charger optimiert. Alle Informationen sind optional und nicht vorhandene Daten führen zum Ausblenden der jeweiligen Anzeigeabschnitte, wodurch die Kachel universell mit vielen anderen Wallbox-Typen kompatibel ist. 

### 2. Voraussetzungen

- IP-Symcon ab Version 7.1

### 3. Software-Installation

* Über das Module Control folgende URL hinzufügen
https://github.com/da8ter/TileVisu-Kachelsammlung.git


### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann die Wallbox-Kachel mithilfe des Schnellfilters gefunden werden. (Suchbegriff: TileVisuWallbox)  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

__Kacheldesign__
Name     | Beschreibung
-------- | ------------------
Standard-Hintergrundbild|Ein-/Ausschalten des Standard-Hintergrundbildes.
Hintergrundbild|: Auswahl eines eigenen Medienobjekts als Hintergrund.
Transparenz Bild|Einstellung der Transparenz des Hintergrundbildes, um es abzudunkeln oder farblich anzupassen. 
Kachelhintergrundfarbe|Farbe des Kachelhintergrunds (wird nur bei eingestellter Bildtransparenz sichtbar)

__Bild__
Name     | Beschreibung
-------- | ------------------
Bildauswahl|Optionen für die Bildauswahl inklusive Go-e Charger, Go-e Charger Gemini, Universell und Eigenes Bild.
Bildbreite|Breite des Wallbox-Bildes, in Prozent der Kachelbreite.
Eigenes Bild: Wallbox lädt|Auswahl eines eigenen Bildes für den Wallboxzustand "Laden".
Eigenes Bild: Wallbox aus|Auswahl eines eigenen Bildes für den Wallboxzustand "Aus".

__Wallbox-Status__
Name     | Beschreibung
-------- | ------------------
Statusvariable|Variable, die den aktuellen Status der Wallbox wiedergibt, erfordert ein Variablenprofil mit Zustandsassoziationen.
Schriftgröße|Schriftgröße des Statustextes in EM.
Zuordnung|Zuweisung von Animation, Farbe und Bild je nach Wallbox-Status.

__Ladeleistung-Anzeige__
Name     | Beschreibung
-------- | ------------------
Aktuelle Ladeleistung|Variablen für aktuelle Ladeleistung in KW
Maximale Ladeleistung|Variablen für maximale Ladeleistung in KW
Farbe Farbverlauf 1|Farbe 1 des Balken-Farbverlaufs.
Farbe Farbverlauf 2|Farbe 3 des Balken-Farbverlaufs.
Schriftgröße|Schriftgröße des Statustextes in EM.

__SOC-Anzeige__

Die Informationen für den SOC und den Ziel-SOC muss der User selbst z.B. über eine API-Anbindung des PKW bereitstellen. Diese Informationen werden leider nicht durch Typ2 Wallboxen geliefert.
Name     | Beschreibung
-------- | ------------------
SOC|Eine Variable, die den aktuellen SOC in Prozent liefert.
Ziel-SOC|Eine Variable, die den gewünschten SOC (Ladeziel) in Prozent liefert.
SOC Schalter|Eine Bool-Variable, true = SOC Anzeigen, false = SOC ausblenden.
Ziel-SOC Schalter|Eine Bool-Variable, true = Ziel-SOC Anzeigen, false = Ziel-SOC ausblenden.
Farbe Farbverlauf 1|Farbe 1 des Balken-Farbverlaufs.
Farbe Farbverlauf 2|Farbe 3 des Balken-Farbverlaufs.

Mit den beiden Schalter-Variablen kann gezielt die SOC Anzeige ein- bzw. ausgeblendet werden. Dies mach z.B. Sinn wenn mehrere Autos an der Wallbox geladen werden sollen und nicht alle Autos die erforderlichen Informationen liefern.

Die Schriftgröße kann unter Ladeleistung-Anzeige angepasst werden. (Identisch mit der Schriftgröße der Ladeleistung)

__Engergieverbrauch/Kosten__

Name     | Beschreibung
-------- | ------------------
Geladen gesamt: Eine Variable, die die gesamte geladene Energie in kWh angibt.
Geladen aktueller Ladevorgang: Eine Variable, die die während des aktuellen Ladevorgangs geladene Energie in kWh angibt.
Kosten gesamt: Eine Variable, die die gesamten Stromkosten darstellt. (Float oder Integer mit Variablenprofil "Euro")
Kosten aktueller Ladevorgang: Eine Variable, die die Stromkosten des aktuellen Ladevorgangs darstellt.(Float oder Integer mit Variablenprofil "Euro")

__Sonstiges__

Name     | Beschreibung
-------- | ------------------
Fehler|Eine Variable, die einen möglichen Fehler als String liefert.
Phasen|Eine Variable, die die Anzahl der aktuell genutzen Phasen leifert. (Integer oder Float mit den Zahlen 1-3)
Kabel|Eine Variable, die eine Information zum verwendeten Kabel liefert. (Integer mit Profil-Assoziationen).
Zugangskontrolle|Eine Variable, die eine Information zur Zugangskontrolle liefert. (Integer mit Profil-Assoziationen) 
Verriegelung|Eine Variable, die eine Informoation zum Ver- und Entriegelungsverhalten der Wallbox liefert. (Integer mit Profil-Assoziationen)

Name     | Beschreibung
-------- | ------------------
Schriftgröße Infos|Stellt die Schriftgröße von Energieverbrauch und Sonstiges ein.