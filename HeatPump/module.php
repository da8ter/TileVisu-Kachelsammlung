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
                $this->RegisterMessage($this->ReadPropertyInteger($HeatPumpProperty), OM_CHANGENAME);
                $this->RegisterMessage($this->ReadPropertyInteger($HeatPumpProperty), VM_UPDATE);
            }

            // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
            $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
        }

        public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
            foreach(['Status', 'Mode', 'OutdoorTemperature', 'WaterTemperature', 'FlowTemperature', 'ReturnTemperature', 'HeaterRodBackupStatus', 'HeaterRodPhase1', 'HeaterRodPhase2', 'HeaterRodPhase3', 'Flow', 'FanRotations', 'CompressorPower', 'COP', 'SPF', 'SPFHeating', 'SPFWater', 'Power', 'Consumption', 'ConsumptionToday'] as $index => $HeatPumpProperty) {
                if ($SenderID === $this->ReadPropertyInteger($HeatPumpProperty)) {
                    switch ($Message) {
                        case OM_CHANGENAME:
                            // Teile der HTML-Darstellung den neuen Namen mit
                            $this->UpdateVisualizationValue(json_encode([
                                'name' . ($index + 1) => $Data[0]
                            ]));
                            break;

                        case VM_UPDATE:
                            // Teile der HTML-Darstellung den neuen Wert mit. Damit dieser korrekt formatiert ist, holen wir uns den von der Variablen via GetValueFormatted
                            $this->UpdateVisualizationValue(json_encode(['value' . ($index + 1) => GetValue($this->ReadPropertyInteger($HeatPumpProperty))]));
                            break;
                    }
                }
            }
        }

        public function RequestAction($Ident, $value) {
            // Nachrichten von der HTML-Darstellung schicken immer den Ident passend zur Eigenschaft und im Wert die Differenz, welche auf die Variable gerechnet werden soll
            $variableID = $this->ReadPropertyInteger($Ident);
            if (!IPS_VariableExists($variableID)) {
                $this->SendDebug('Error in RequestAction', 'Variable to be updated does not exist', 0);
                return;
            }
                // Umschalten des Werts der Variable
            $currentValue = GetValue($variableID);
            SetValue($variableID, !$currentValue);
        }
        
        public function GetVisualizationTile() {
            // Füge ein Skript hinzu, um beim laden, analog zu Änderungen bei Laufzeit, die Werte zu setzen
            // Obwohl die Rückgabe von GetFullUpdateMessage ja schon JSON-codiert ist wird dennoch ein weiteres mal json_encode ausgeführt
            // Damit wird dem String Anführungszeichen hinzugefügt und eventuelle Anführungszeichen innerhalb werden korrekt escaped
            $initialHandling = '<script>handleMessage(' . json_encode($this->GetFullUpdateMessage()) . ');</script>';

            $assets = '<script>';
            $assets .= 'window.assets = {};' . PHP_EOL;
            $assets .= 'window.assets.img_wp_aus = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wp_aus.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_wp_heizen = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wp_heizen.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_wp_ww = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wp_ww.webp')) . '";' . PHP_EOL;
            $assets .= '</script>';

            // Füge statisches HTML aus Datei hinzu
            $module = file_get_contents(__DIR__ . '/module.html');

            // Gebe alles zurück. 
            // Wichtig: $initialHandling nach hinten, da die Funktion handleMessage ja erst im HTML definiert wird
            return $module . $initialHandling;
        }

            // Generiere eine Nachricht, die alle Elemente in der HTML-Darstellung aktualisiert
            private function GetFullUpdateMessage() {
                $StatusID = $this->ReadPropertyInteger('Status');
                $ModeID = $this->ReadPropertyInteger('Mode');
                $OutdoorTemperatureID = $this->ReadPropertyInteger('OutdoorTemperature');
                $WaterTemperatureID = $this->ReadPropertyInteger('WaterTemperature');
                $FlowTemperatureID = $this->ReadPropertyInteger('FlowTemperature');
                $ReturnTemperatureID = $this->ReadPropertyInteger('ReturnTemperature');
                $HeaterRodBackupStatusID = $this->ReadPropertyInteger('HeaterRodBackupStatus');
                $HeaterRodPhase1ID = $this->ReadPropertyInteger('HeaterRodPhase1');
                $HeaterRodPhase2ID$this->ReadPropertyInteger('HeaterRodPhase2');
                $HeaterRodPhase3ID = $this->ReadPropertyInteger('HeaterRodPhase3');
                $FlowID = $this->ReadPropertyInteger('Flow');
                $FanRotationsID = $this->ReadPropertyInteger('FanRotations');
                $CompressorPowerID = $this->ReadPropertyInteger('CompressorPower');        
                $COPID = $this->ReadPropertyInteger('COP');
                $SPFID = $this->ReadPropertyInteger('SPF');
                $SPFHeatingID = $this->ReadPropertyInteger('SPFHeating');
                $SPFWaterID = $this->ReadPropertyInteger('SPFWater');
                $PowerID = $this->ReadPropertyInteger('Power');
                $ConsumptionID = $this->ReadPropertyInteger('Consumption');
                $ConsumptionTodayID = $this->ReadPropertyInteger('ConsumptionToday');

                $StatusExists = IPS_VariableExists($StatusID);
                $ModeExists = IPS_VariableExists($ModeID);
                $OutdoorTemperatureExists = IPS_VariableExists($OutdoorTemperatureID);
                $WaterTemperatureExists = IPS_VariableExists($WaterTemperatureID);
                $FlowTemperatureExists = IPS_VariableExists($FlowTemperatureID);
                $ReturnTemperatureExists = IPS_VariableExists($ReturnTemperatureID);
                $HeaterRodBackupStatusExists = IPS_VariableExists($HeaterRodBackupStatusID);
                $HeaterRodPhase1Exists = IPS_VariableExists($HeaterRodPhase1ID);
                $HeaterRodPhase2Exists = IPS_VariableExists($HeaterRodPhase2ID);
                $HeaterRodPhase3Exists = IPS_VariableExists($HeaterRodPhase3ID);
                $FlowExists = IPS_VariableExists($FlowID);
                $FanRotationsExists = IPS_VariableExists($FanRotationsID);
                $CompressorPowerExists = IPS_VariableExists($CompressorPowerID);
                $COPExists = IPS_VariableExists($COPID);
                $SPFExists = IPS_VariableExists($SPFID);
                $SPFHeatingExists = IPS_VariableExists($SPFHeatingID);
                $SPFWaterExists = IPS_VariableExists($SPFWaterID);
                $PowerExists = IPS_VariableExists($PowerID);
                $ConsumptionExists = IPS_VariableExists($ConsumptionID);
                $ConsumptionTodayExists = IPS_VariableExists($ConsumptionTodayID);

                $result = [
                    'Status' => $StatusExists,
                    'Mode' => $ModeExists,
                    'OutdoorTemperature' => $OutdoorTemperatureExists,
                    'WaterTemperature' => $WaterTemperatureExists,
                    'FlowTemperature' => $FlowTemperatureExists,
                    'ReturnTemperature' => $ReturnTemperatureExists,
                    'HeaterRodBackupStatus' => $HeaterRodBackupStatusExists,
                    'HeaterRodPhase1' => $HeaterRodPhase1Exists,
                    'HeaterRodPhase2' => $HeaterRodPhase2Exists,
                    'HeaterRodPhase3' => $HeaterRodPhase3Exists,
                    'Flow' => $FlowExists,
                    'FanRotations' => $FanRotationsExists,
                    'CompressorPower' => $CompressorPowerExists,
                    'COP' => $COPExists,
                    'SPF' => $SPFExists,
                    'SPFHeating' => $SPFHeatingExists,
                    'SPFWater' => $SPFWaterExists,
                    'Power' => $PowerExists,
                    'Consumption' => $ConsumptionExists,
                    'ConsumptionToday' => $ConsumptionTodayExists
                ];

                if ($StatusExists) {
                    $result['Status'] = GetValueInteger($StatusID);
                }
                if ($ModeExists) {
                    $result['Mode'] = GetValueInteger($ModeID);
                }
                    if ($OutdoorTemperatureExists) {
                    $result['OutdoorTemperature'] = GetValueFloat($OutdoorTemperatureID);
                }
                    if ($WaterTemperatureExists) {
                    $result['WaterTemperature'] = GetValueFloat($WaterTemperatureID);
                }
                    if ($FlowTemperatureExists) {
                    $result['FlowTemperature'] = GetValueFloat($FlowTemperatureID);
                }
                    if ($ReturnTemperatureExists) {
                    $result['ReturnTemperature'] = GetValueFloat($ReturnTemperatureID);
                }
                    if ($HeaterRodBackupStatusExists) {
                    $result['HeaterRodBackupStatus'] = GetValueBoolean($HeaterRodBackupStatusID);
                }
                    if ($HeaterRodPhase1Exists) {
                    $result['HeaterRodPhase1'] = GetValueBoolean($HeaterRodPhase1ID);
                }
                    if ($HeaterRodPhase2Exists) {
                    $result['HeaterRodPhase2'] = GetValueBoolean($HeaterRodPhase2ID);
                }
                    if ($HeaterRodPhase3Exists) {
                    $result['HeaterRodPhase3'] = GetValueBoolean($HeaterRodPhase3ID);
                }
                    if ($FlowExists) {
                    $result['Flow'] = GetValueFloat($FlowID);
                }
                    if ($FanRotationsExists) {
                    $result['FanRotations'] = GetValueFloat($FanRotationsID);
                }
                    if ($CompressorPowerExists) {
                    $result['CompressorPower'] = GetValueFloat($CompressorPowerID);
                }
                    if ($COPExists) {
                    $result['COP'] = GetValueFloat($COPID);
                }
                    if ($SPFExists) {
                    $result['SPF'] = GetValueFloat($SPFID);
                }
                    if ($SPFHeatingExists) {
                    $result['SPFHeating'] = GetValueFloat($SPFHeatingID);
                }
                    if ($SPFWaterExists) {
                    $result['SPFWater'] = GetValueFloat($SPFWaterID);
                }
                    if ($PowerExists) {
                    $result['Power'] = GetValueFloat($PowerID);
                }
                    if ($ConsumptionExists) {
                    $result['Consumption'] = GetValueFloat($ConsumptionID);
                }
                    if ($ConsumptionTodayExists) {
                    $result['ConsumptionToday'] = GetValueFloat($ConsumptionTodayID);
                }

                return json_encode($result);
                
            }

    }
 
?>
