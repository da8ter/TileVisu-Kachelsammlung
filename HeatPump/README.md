# Wärmepumpen-Kachel
Eine Kachel für die IPSymcon Tile Visualisation um den aktuellen Betriebsstatus einer Wärmepumpe zu visualisieren.
![Wärmepumpen-Kachel](https://github.com/da8ter/images/blob/main/heatpump_kachel.png)

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang
Anzeige von aktuellen Werten einer Wärempumpe.

![Variablenprofil für die Balkendarstellung](https://github.com/da8ter/images/blob/main/heatpump_balken_profil.png)

### 2. Voraussetzungen

- IP-Symcon ab Version 7.1

### 3. Software-Installation

* Über das Module Control folgende URL hinzufügen
https://github.com/da8ter/TileVisu-Kachelsammlung.git

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'TileVisuHeatPump'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:



### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Name   | Typ     | Beschreibung
------ | ------- | ------------
       |         |
       |         |

#### Profile

Name   | Typ
------ | -------
       |
       |

### 6. WebFront

Die Funktionalität, die das Modul im WebFront bietet.

### 7. PHP-Befehlsreferenz

`boolean BWO_BeispielFunktion(integer $InstanzID);`
Erklärung der Funktion.

Beispiel:
`BWO_BeispielFunktion(12345);`