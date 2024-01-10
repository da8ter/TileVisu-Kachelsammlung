<?php

    class TileVisuImage extends IPSModule
    {
        
        public function Create() {
            //Never delete this line!
            parent::Create();

            // Drei Eigenschaften für die dargestellten Zähler
            $this->RegisterPropertyInteger("bgImage", 0);
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


                    $imageID = $this->ReadPropertyInteger('bgImage');
                    if (IPS_MediaExists($imageID)) {
                        $image = IPS_GetMedia($imageID);
                        if ($image['MediaType'] === MEDIATYPE_IMAGE) {
                            $imageFile = explode('.', $image['MediaFile']);
                            $imageContent = '';
                            // Falls ja, ermittle den Anfang der src basierend auf dem Dateitypen
                            switch (end($imageFile)) {
                                case 'bmp':
                                    $imageContent = 'data:image/bmp;base64,';
                                    break;
            
                                case 'jpg':
                                case 'jpeg':
                                    $imageContent = 'data:image/jpeg;base64,';
                                    break;
            
                                case 'gif':
                                    $imageContent = 'data:image/gif;base64,';
                                    break;
            
                                case 'png':
                                    $imageContent = 'data:image/png;base64,';
                                    break;
            
                                case 'ico':
                                    $imageContent = 'data:image/x-icon;base64,';
                                    break;
                            }

                            // Nur fortfahren, falls Inhalt gesetzt wurde. Ansonsten ist das Bild kein unterstützter Dateityp
                            if ($imageContent) {
                                // Hänge base64-codierten Inhalt des Bildes an
                                $imageContent .= IPS_GetMediaContent($imageID);
                                $result['image1'] = $imageContent;
                            }

                        }
                    }
                    else{
                        $imageContent = 'data:image/png;base64,';
                        $imageContent .= base64_encode(file_get_contents(__DIR__ . '/assets/placeholder.png'));
                        $result['image1'] = $imageContent;
                    }                    
                    
                
                
                return json_encode($result);
                
            }

    
    }
 
?>
