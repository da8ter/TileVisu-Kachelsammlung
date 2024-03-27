# Bewohnerstatus
![Bewohnerstatus-Kachel](https://github.com/da8ter/images/blob/main/bewohner_status.jpg)

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Bildet den Anwesenheitsstatus von bis zu 5 Bewohnern ab. Der Status eines Bewohners wird über eine Bool-Variable gesteuert. Ein umschalten des Status kann zusätzlich über das Bild erfolgen (kann in der Konfiguration deaktiviert werden) Wenn ein Bewohner anwesend ist wird das Bild in Farbe abgebildet, bei Abwesenheit in Graustufen. Es kann je Bewohner ein eigenes Bild verwendet werden. 

### 2. Voraussetzungen

- IP-Symcon ab Version 7.1

### 3. Software-Installation

* Über das Module Control folgende URL hinzufügen
https://github.com/da8ter/TileVisu-Kachelsammlung.git


### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann die Bewohnerstatus-Kachel mithilfe des Schnellfilters gefunden werden. (Suchbegriff: TileVisuBewohnerstatus)  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
Standard-Hintergrundbild|Schaltet das Standard-Hintergrundbild aus oder an.
Hintergrundbild|Hier kann ein eigenes Medienobjekt als Hintergrundbild ausgewählt werden.
Transparenz Bild|Tranzparenz des Hintergrundbildes. Ermöglicht zusammen mit "Kachelhintergrundfarbe" das Bild abzudunkeln oder einen farbigen Touch zu geben. 
Kachelhintergrundfarbe|Hintergrundfarbe der Kachel (Nur sichtbar wenn Bildtransparenz eingestellt ist).
Standard-Hintergrundbild|Schaltet das Standard-Hintergrundbild aus oder an.

Name     | Beschreibung
-------- | ------------------
Bewohner 1-5
Status|Erwartet eine Bool-Variable. true = anwesend, false = abwesend.
Foto|Auswahl eines eigenen Bildes als Medienobjekt.

Name     | Beschreibung
-------- | ------------------
Pptionen
Schriftgröße|Schriftgröße Bewohnername
Eckenradius|Eckenradius Bildanzeige. 50% = rundes Foto.
Name nazeigen|Name aus- oder einblenden
Bedienung sperren|Verhindet manuelles steuern des Bewohnerstatus über die Kachel

