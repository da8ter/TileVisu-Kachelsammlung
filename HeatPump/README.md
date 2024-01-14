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

### 2. Voraussetzungen

- IP-Symcon ab Version 7.1

### 3. Software-Installation

* Über das Module Control folgende URL hinzufügen
https://github.com/da8ter/TileVisu-Kachelsammlung.git

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'TileVisuHeatPump'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Über das Konfigurationsformular können alle Variablen konfiguriert werden. Nicht konfigurierte Werte werden in der Kachel ausgeblendet.

Gültig für alle Variablen:
Alle Variablen (außer Bool) benötigen ein Variablenprofil mit entsprechenden Suffix wie z.B. °C oder Watt und je nach belieben die Anzal der Nachkommastellen. Alle Werte werden in der Kachel so abgebildet wie das Variablenprofil es vorgibt.

Status:
Benötigt eine Integer-Variable mit maximal 10 Werten. Text und Farben werden aus dem Variablenprofil ausgelesen.

Beispiel:
![Variablenprofil für den Status](https://github.com/da8ter/images/blob/main/heatpump_status_profil.png)

Status Bildkonfiguration:
Hier kann jedem Status ein entsprechendes Wärmepumpenbild zugewiesen werden. Verfügbar sind drei Bilder: 1. Wärmepumpe aus, 2. Wärmepumpe heizen, 3. Wärmepumpe Warmwasser.

Modus: (wie Status nur ohne Bilder)

Die Werte Leistung, Kompressor, Lüfter und Durchfluss werden in einem Balken dargestellt. Diese 4 Variablen benötigen in ihrem Variablenprofil zwingend einen Min- und Maximalwert für die Berechnung der prozentualen Balkendarstellung.
![Variablenprofil für den Status](https://github.com/da8ter/images/blob/main/heatpump_balken_profil.png)


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