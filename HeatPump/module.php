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
        
                if (!IPS_VariableExists($variableID)) {
                    continue;
                }


            $initialHandling[] = 'handleMessage(\'' . $this->GetUpdatedValue($ident, $variableID) . '\');';
            }

            $messages = '<script>' . implode(' ', $initialHandling) . '</script>';

            // We need to include the assets directly as there is no way to load anything afterwards yet
            $assets = '<script>';
            $assets .= 'window.assets = {};' . PHP_EOL;
            $assets .= 'window.assets.img_wp_aus = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wp_aus.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_wp_heizen = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wp_heizen.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_wp_ww = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wp_ww.webp')) . '";' . PHP_EOL;
            $assets .= '</script>';

            // Add static HTML content from file to make editing easier
            $module = file_get_contents(__DIR__ . '/module.html');

            // Return everything to render our fancy tile!
            return $module . $assets . $messages;
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

            // If Min/Max are not needed you can remove those values
            // The channel
            $p = IPS_VariableProfileExists($profile) ? IPS_GetVariableProfile($profile) : null;
            return json_encode([
                'Ident' => $variableIdent,
                'Value' => $Value,  
                'ValueFormatted' => $Valueformatted, 
                'Min' => $p ? $p['MinValue'] : false,
                'Max' => $p ? $p['MaxValue'] : false,
            ]);
        }
    
    }

?>
