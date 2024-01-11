<?php

    class HTMLVisuHeatPump extends IPSModule
    {
        
        public function Create() {
            //Never delete this line!
            parent::Create();

            $this->RegisterPropertyInteger('Status', 0);
            $this->RegisterPropertyInteger('Mode', 0);
            $this->RegisterPropertyInteger('OutdoorTemperature', 0);
            $this->RegisterPropertyInteger('WaterTemperature', 0);
            $this->RegisterPropertyInteger('FlowTemperature', 0);
            $this->RegisterPropertyInteger('ReturnTemperature', 0);
            $this->RegisterPropertyInteger('HeaterRodBackupStatus', 0);
            $this->RegisterPropertyInteger('HeaterRodPhase1', 0);
            $this->RegisterPropertyInteger('HeaterRodPhase2', 0);
            $this->RegisterPropertyInteger('HeaterRodPhase3', 0);
            $this->RegisterPropertyInteger('Flow', 0);
            $this->RegisterPropertyInteger('FanRotations', 0);
            $this->RegisterPropertyInteger('CompressorPower', 0);        
            $this->RegisterPropertyInteger('COP', 0);
            $this->RegisterPropertyInteger('SPF', 0);
            $this->RegisterPropertyInteger('SPFHeating', 0);
            $this->RegisterPropertyInteger('SPFWater', 0);
            $this->RegisterPropertyInteger('Power', 0);
            $this->RegisterPropertyInteger('Consumption', 0);
            $this->RegisterPropertyInteger('ConsumptionToday', 0);
            $this->RegisterPropertyString('Statusimage0', 'wp_aus');
            $this->RegisterPropertyString('Statusimage1', 'wp_aus');
            $this->RegisterPropertyString('Statusimage2', 'wp_aus');
            $this->RegisterPropertyString('Statusimage3', 'wp_aus');
            $this->RegisterPropertyString('Statusimage4', 'wp_aus');
            $this->RegisterPropertyString('Statusimage5', 'wp_aus');
            $this->RegisterPropertyString('Statusimage6', 'wp_aus');
            $this->RegisterPropertyString('Statusimage7', 'wp_aus');
            $this->RegisterPropertyString('Statusimage8', 'wp_aus');
            $this->RegisterPropertyString('Statusimage9', 'wp_aus');
            $this->SetVisualizationType(1);
        }
        public function ApplyChanges() {
            parent::ApplyChanges();

            // Aktualisiere registrierte Nachrichten
            foreach ($this->GetMessageList() as $senderID => $messageIDs) {
                foreach($messageIDs as $messageID) {
                    $this->UnregisterMessage($senderID, $messageID);
                }
            }

            foreach(['Status', 'Mode', 'OutdoorTemperature', 'WaterTemperature', 'FlowTemperature', 'ReturnTemperature', 'HeaterRodBackupStatus', 'HeaterRodPhase1', 'HeaterRodPhase2', 'HeaterRodPhase3', 'Flow', 'FanRotations', 'CompressorPower', 'COP', 'SPF', 'SPFHeating', 'SPFWater', 'Power', 'Consumption', 'ConsumptionToday'] as $HeatPumpProperty) {
                $this->RegisterMessage($this->ReadPropertyInteger($HeatPumpProperty), VM_UPDATE);
            }
        }


        public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
            foreach(['Status', 'Mode', 'OutdoorTemperature', 'WaterTemperature', 'FlowTemperature', 'ReturnTemperature', 'HeaterRodBackupStatus', 'HeaterRodPhase1', 'HeaterRodPhase2', 'HeaterRodPhase3', 'Flow', 'FanRotations', 'CompressorPower', 'COP', 'SPF', 'SPFHeating', 'SPFWater', 'Power', 'Consumption', 'ConsumptionToday'] as $index => $HeatPumpProperty) {
                if ($SenderID === $this->ReadPropertyInteger($HeatPumpProperty)) {
                    switch ($Message) {
                        case VM_UPDATE:
                            // Holen Sie sich das aktualisierte Wert-Array für das HeatPumpProperty
                            $updatedValue = $this->GetUpdatedValue($HeatPumpProperty, $this->ReadPropertyInteger($HeatPumpProperty));
                            
                            // Aktualisieren Sie die Visualisierung mit dem neuen Wert
                            $this->UpdateVisualizationValue($updatedValue);
                            break;
                    }
                }
            }
        }





        public function RequestAction($Ident, $Value) {
            $variableID = $this->ReadPropertyInteger($Ident);
            SetValue($variableID, $Value);

            // Holen Sie sich das aktualisierte Wert-Array für das HeatPumpProperty
            $updatedValue = $this->GetUpdatedValue($Ident, $this->ReadPropertyInteger($Ident));

            // Aktualisieren Sie die Visualisierung mit dem neuen Wert
            //$this->UpdateVisualizationValue($updatedValue);
        }
        
        public function GetVisualizationTile() {
            // Inject current values using the message handling function
            $initialHandling = [];
            $statusArray = [];
            $childs = array(
                array('Name' => 'Status', 'VariableID' => $this->ReadPropertyInteger('Status')),
                array('Name' => 'Mode', 'VariableID' => $this->ReadPropertyInteger('Mode')),
                array('Name' => 'OutdoorTemperature', 'VariableID' => $this->ReadPropertyInteger('OutdoorTemperature')),
                array('Name' => 'WaterTemperature', 'VariableID' => $this->ReadPropertyInteger('WaterTemperature')),
                array('Name' => 'FlowTemperature', 'VariableID' => $this->ReadPropertyInteger('FlowTemperature')),
                array('Name' => 'ReturnTemperature', 'VariableID' => $this->ReadPropertyInteger('ReturnTemperature')),
                array('Name' => 'HeaterRodBackupStatus', 'VariableID' => $this->ReadPropertyInteger('HeaterRodBackupStatus')),
                array('Name' => 'HeaterRodPhase1', 'VariableID' => $this->ReadPropertyInteger('HeaterRodPhase1')),
                array('Name' => 'HeaterRodPhase2', 'VariableID' => $this->ReadPropertyInteger('HeaterRodPhase2')),
                array('Name' => 'HeaterRodPhase3', 'VariableID' => $this->ReadPropertyInteger('HeaterRodPhase3')),
                array('Name' => 'Flow', 'VariableID' => $this->ReadPropertyInteger('Flow')),
                array('Name' => 'FanRotations', 'VariableID' => $this->ReadPropertyInteger('FanRotations')),
                array('Name' => 'CompressorPower', 'VariableID' => $this->ReadPropertyInteger('CompressorPower')),
                array('Name' => 'COP', 'VariableID' => $this->ReadPropertyInteger('COP')),
                array('Name' => 'SPF', 'VariableID' => $this->ReadPropertyInteger('SPF')),
                array('Name' => 'SPFHeating', 'VariableID' => $this->ReadPropertyInteger('SPFHeating')),
                array('Name' => 'SPFWater', 'VariableID' => $this->ReadPropertyInteger('SPFWater')),
                array('Name' => 'Power', 'VariableID' => $this->ReadPropertyInteger('Power')),
                array('Name' => 'Consumption', 'VariableID' => $this->ReadPropertyInteger('Consumption')),
                array('Name' => 'ConsumptionToday', 'VariableID' => $this->ReadPropertyInteger('ConsumptionToday'))
            );
            
            foreach ($childs as $child) {
                $variableID = $child['VariableID'];  // Holt die VariableID aus dem Kind-Array
                $ident = $child['Name'];  // Holt den Namen als Ident
            
                $variableExists = IPS_VariableExists($variableID);  // Überprüft, ob die Variable existiert
                if ($variableExists) {
                    $initialHandling[] = 'handleMessage(\'' . $this->GetUpdatedValue($ident, $variableID) . '\');';
                }
            
                // Füge den Namen und den Status der Existenz der Variable in das neue Array ein
                $statusArray[] = [
                    'Name' => $ident,
                    'Status' => $variableExists
                ];
            }

            $messages = '<script>' . implode(' ', $initialHandling) . '</script>';

            // We need to include the assets directly as there is no way to load anything afterwards yet
            $assets = '<script>';
            $assets .= 'window.assets = {};' . PHP_EOL;
            $assets .= 'window.assets.img_wp_aus = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wp_aus.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_wp_heizen = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wp_heizen.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_wp_ww = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wp_ww.webp')) . '";' . PHP_EOL;
            $assets .= '</script>';

          
            $statusMapping = array(
                0 => $this->ReadPropertyString('Statusimage0'),
                1 => $this->ReadPropertyString('Statusimage1'),
                2 => $this->ReadPropertyString('Statusimage2'),
                4 => $this->ReadPropertyString('Statusimage4'),
                5 => $this->ReadPropertyString('Statusimage5'),
                3 => $this->ReadPropertyString('Statusimage3'),
                6 => $this->ReadPropertyString('Statusimage6'),
                7 => $this->ReadPropertyString('Statusimage7'),
                8 => $this->ReadPropertyString('Statusimage8'),
                9 => $this->ReadPropertyString('Statusimage9'),

            );


            // Konvertiere das PHP-Array in einen JSON-String
            $statusImagesJson = json_encode($statusMapping);
            // Füge den JSON-String in ein <script>-Tag ein
            $images = '<script type="text/javascript">';
            $images .= 'var statusImages = ' . $statusImagesJson . ';';
            $images .= '</script>';
            
            $statusArrayJson = json_encode($statusArray);
            $varexist = '<script type="text/javascript">';
            $varexist .= 'var varexist = ' . $statusArrayJson . ';';
            $varexist .= 'console.log(varexist);';
            $varexist .= '</script>';

            // Add static HTML content from file to make editing easier
            $module = file_get_contents(__DIR__ . '/module.html');

            // Return everything to render our fancy tile!
            return $module . $varexist . $images . $assets . $messages;
        }

        private function GetUpdatedValue($variableIdent, $variableID) {
         
            if (($variableID === false) || (!IPS_VariableExists($variableID))) {
                return false;
            }

            $variable = IPS_GetVariable($variableID);
                        
            $Value = GetValue($variableID);
            $ValueFormatted = GetValueFormatted($variableID);
            $profile = $variable['VariableCustomProfile'];
            if ($profile === '') {
                $profile = $variable['VariableProfile'];
            }

            $p = IPS_VariableProfileExists($profile) ? IPS_GetVariableProfile($profile) : null;

            if (IPS_VariableProfileExists($profile)) {
                $p = IPS_GetVariableProfile($profile);
            
                // Setze $colorhexWert auf einen leeren String als Standardwert
                $colorhexWert = "";
            
                if (!empty($p['Associations']) && is_array($p['Associations'])) {
                    foreach ($p['Associations'] as $association) {
                        // Prüfe, ob der aktuelle Wert dem gesuchten Wert entspricht
                        if (isset($association['Value'], $association['Color']) && $association['Value'] == $Value) {
                            // Überprüfe, ob $color -1 ist und setze $colorhexWert entsprechend
                            $colorhexWert = $association['Color'] === -1 ? "" : sprintf('%06X', $association['Color']);
                            break; // Beende die Schleife, da der passende Wert gefunden wurde
                        }
                    }
                }
            } else {
                $colorhexWert = "";
            }

            return json_encode([
                'Ident' => $variableIdent,
                'Value' => $Value,  
                'ValueFormatted' => $ValueFormatted, 
                'Min' => $p ? $p['MinValue'] : false,
                'Max' => $p ? $p['MaxValue'] : false,
                'Color' => '#' . $colorhexWert,
            ]);
            
        }
    
    }

?>
