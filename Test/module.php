<?php

    class TileVisuTest extends IPSModule
    {
        
        public function Create() {
            //Never delete this line!
            parent::Create();

            // Drei Eigenschaften für die dargestellten Zähler
            // Visualisierungstyp auf 1 setzen, da wir HTML anbieten möchten
            $this->SetVisualizationType(1);
        }

        public function ApplyChanges() {
            parent::ApplyChanges();

      

        }

    
        public function GetVisualizationTile() {
            // Füge ein Skript hinzu, um beim laden, analog zu Änderungen bei Laufzeit, die Werte zu setzen
            // Obwohl die Rückgabe von GetFullUpdateMessage ja schon JSON-codiert ist wird dennoch ein weiteres mal json_encode ausgeführt
            // Damit wird dem String Anführungszeichen hinzugefügt und eventuelle Anführungszeichen innerhalb werden korrekt escaped
            $initialHandling = '<script>handleMessage(' . json_encode($this->GetFullUpdateMessage()) . ');</script>';

            // Füge statisches HTML aus Datei hinzu
            $module = file_get_contents(__DIR__ . '/module.html');

            // Gebe alles zurück. 
            // Wichtig: $initialHandling nach hinten, da die Funktion handleMessage ja erst im HTML definiert wird
            return $module . $initialHandling;
        }

            // Generiere eine Nachricht, die alle Elemente in der HTML-Darstellung aktualisiert
            private function GetFullUpdateMessage() {
                $result = []; // Initialisiere das Ergebnis-Array

                                
                    
                
                
                return json_encode($result);
                
            }

    
    }
 
?>
