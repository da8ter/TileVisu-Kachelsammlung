<?php
class TileVisuPVOverview extends IPSModule
{
    public function Create()
    {
        // Nie diese Zeile löschen!
        parent::Create();

        $this->RegisterPropertyInteger("ProduktionWert", 0);
        $this->RegisterPropertyString("ProduktionLabel", "Produktion");
        $this->RegisterPropertyInteger("SpeicherEntladungWert", 0);
        $this->RegisterPropertyInteger("SpeicherBeladungWert", 0);
        $this->RegisterPropertyInteger("ExportWert", 0);
        $this->RegisterPropertyString("ExportLabel", "Export");
        $this->RegisterPropertyInteger("VerbrauchWert", 0);
        $this->RegisterPropertyString("VerbrauchLabel", "Verbrauch");
        $this->RegisterPropertyInteger("ImportWert", 0);
        $this->RegisterPropertyString("ImportLabel", "Import");
        $this->RegisterPropertyString("EigenverbrauchLabel", "Eigenverbrauch");
        $this->RegisterPropertyString("EigenproduktionLabel", "Eigenproduktion");
        $this->RegisterPropertyInteger("EigenverbrauchVerlaufFarbe1", 2674091);
        $this->RegisterPropertyInteger("EigenverbrauchVerlaufFarbe2", 2132596);
        $this->RegisterPropertyInteger("EigenproduktionVerlaufFarbe1", 2674091);
        $this->RegisterPropertyInteger("EigenproduktionVerlaufFarbe2", 2132596);
        //Kachellayout
        $this->RegisterPropertyInteger("bgImage", 0);
        $this->RegisterPropertyFloat("Bildtransparenz", 0.7);
        $this->RegisterPropertyInteger("Kachelhintergrundfarbe", -1);
        $this->RegisterPropertyBoolean("BG_Off", 1);
        $this->RegisterPropertyInteger("SchriftfarbeBalken", 0xFFFFFF);
        $this->RegisterPropertyInteger("SchriftfarbeSub", 0xFFFFFF);
        $this->RegisterPropertyFloat("SchriftgroesseBalken", 1);
        $this->RegisterPropertyFloat("SchriftgroesseSub", 0.8);
        $this->RegisterPropertyFloat("Eckenradius", 6);
        $this->RegisterPropertyInteger("EinspeisungFarbe", 2598689);
        $this->RegisterPropertyInteger("ZukaufFarbe", 9830400);

        // Visualisierungstyp auf 1 setzen, da wir HTML anbieten möchten
        $this->SetVisualizationType(1);
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

        // Aktualisiere registrierte Nachrichten
        foreach ($this->GetMessageList() as $senderID => $messageIDs)
        {
            foreach ($messageIDs as $messageID)
            {
                $this->UnregisterMessage($senderID, $messageID);
            }
        }


        foreach (['ProduktionWert', 'SpeicherEntladungWert', 'SpeicherBeladungWert', 'ExportWert', 'VerbrauchWert', 'ImportWert'] as $VariableProperty)        {
            $this->RegisterMessage($this->ReadPropertyInteger($VariableProperty), VM_UPDATE);
        }

        // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        foreach (['ProduktionWert', 'SpeicherEntladungWert', 'SpeicherBeladungWert', 'ExportWert', 'VerbrauchWert', 'ImportWert'] as $index => $VariableProperty)
        {
            if ($SenderID === $this->ReadPropertyInteger($VariableProperty))
            {
                

                switch ($Message)
                {
                    case VM_UPDATE:
                        
                        // Teile der HTML-Darstellung den neuen Wert mit. Damit dieser korrekt formatiert ist, holen wir uns den von der Variablen via GetValueFormatted
                        //$this->UpdateVisualizationValue(json_encode([$VariableProperty => GetValueFormatted($this->ReadPropertyInteger($VariableProperty))]));
                        //$this->UpdateVisualizationValue(json_encode([$VariableProperty . 'Value' => GetValue($this->ReadPropertyInteger($VariableProperty))]));

                        $archivID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];


                        $SpeicherEntladungID = $this->ReadPropertyInteger('SpeicherEntladungWert');
                        $speicherentladung = 1; // Standardwert setzen 

                        if (IPS_VariableExists($SpeicherEntladungID) && AC_GetLoggingStatus($archivID, $SpeicherEntladungID)) {
                            $SpeicherEntladung_heute_archiv = AC_GetAggregatedValues($archivID, $SpeicherEntladungID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                            if (!empty($SpeicherEntladung_heute_archiv)) {
                                $speicherentladung = round($SpeicherEntladung_heute_archiv[0]['Avg'], 2);
                                if ($speicherentladung <= 0) {
                                    $speicherentladung = 0.0;
                                }
                            }
                        }

                        $SpeicherBeladungID = $this->ReadPropertyInteger('SpeicherBeladungWert');
                        $speicherbeladung = 1; // Standardwert setzen 

                        if (IPS_VariableExists($SpeicherBeladungID) && AC_GetLoggingStatus($archivID, $SpeicherBeladungID)) {
                            $SpeicherBeladung_heute_archiv = AC_GetAggregatedValues($archivID, $SpeicherBeladungID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                            if (!empty($SpeicherBeladung_heute_archiv)) {
                                $speicherbeladung = round($SpeicherBeladung_heute_archiv[0]['Avg'], 2);
                                if ($speicherbeladung <= 0) {
                                    $speicherbeladung = 0.0;
                                }
                            }
                        }


                        $produktionsID = $this->ReadPropertyInteger('ProduktionWert');
                        $produktion = 1; // Standardwert setzen
                        
                        if (IPS_VariableExists($produktionsID) && AC_GetLoggingStatus($archivID, $produktionsID)) {
                            $produktion_heute_archiv = AC_GetAggregatedValues($archivID, $produktionsID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                            if (!empty($produktion_heute_archiv)) {
                                $produktion = round($speicherbeladung + $produktion_heute_archiv[0]['Avg'], 2);
                                if ($produktion <= 0) {
                                    $produktion = 0.01;
                                }
                            }
                        }



                        $importID = $this->ReadPropertyInteger('ImportWert');
                        $import = 1; // Standardwert setzen
                        
                        if (IPS_VariableExists($importID) && AC_GetLoggingStatus($archivID, $importID)) {
                            $import_heute_archiv = AC_GetAggregatedValues($archivID, $importID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                            if (!empty($import_heute_archiv)) {
                                $import = round($import_heute_archiv[0]['Avg'], 2);
                                if ($import <= 0) {
                                    $import = 0.01;
                                }
                            }
                        }
            
                        $verbrauchID = $this->ReadPropertyInteger('VerbrauchWert');
                        $verbrauch = 1; // Standardwert setzen
                        
                        if (IPS_VariableExists($verbrauchID) && AC_GetLoggingStatus($archivID, $verbrauchID)) {
                            $verbrauch_heute_archiv = AC_GetAggregatedValues($archivID, $verbrauchID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                            if (!empty($verbrauch_heute_archiv)) {
                                $verbrauch = round($verbrauch_heute_archiv[0]['Avg'], 2);
                                if ($verbrauch <= 0) {
                                    $verbrauch = 0.01;
                                }
                            }
                                                        
                        }


            
                        $exportID = $this->ReadPropertyInteger('ExportWert');
                        $export = 1; // Standardwert setzen
                        $export_prozent = 0;
                        
                        if (IPS_VariableExists($exportID) && AC_GetLoggingStatus($archivID, $exportID)) {
                            $export_heute_archiv = AC_GetAggregatedValues($archivID, $exportID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                            if (!empty($export_heute_archiv)) {
                                $export = round($export_heute_archiv[0]['Avg'], 2);
                                if ($export <= 0) {
                                    $export = 0.01;
                                    $export_prozent = 0;
                                }
                                else {
                                    $export_prozent = round($export / $produktion * 100, 0);
        
                                }
                            }
                        }


                        $eigenverbrauch_prozent = round(100 - $export_prozent, 0);
                        $eigenverbrauch = round($produktion - $export,2);
                        $eigenproduktion = $eigenverbrauch;
                        //$verbrauch = $import + $eigenproduktion;
                   
                      
                        $eigenproduktion_prozent = round(($eigenproduktion / $verbrauch) * 100, 0);


                        if ($import <= 0.1) {
                            $import_prozent = 0;
                            $import = 0;
                        }
                        else {
                            $import_prozent = round(100 - $eigenproduktion_prozent, 0);
                        }

                        $this->UpdateVisualizationValue(json_encode(['produktionwert' => $produktion]));
                        $this->UpdateVisualizationValue(json_encode(['speicherentladungwert' => $speicherentladung]));
                        $this->UpdateVisualizationValue(json_encode(['speicherbeladungwert' => $speicherbeladung]));
                        $this->UpdateVisualizationValue(json_encode(['import' => $import]));
                        $this->UpdateVisualizationValue(json_encode(['verbrauch' => $verbrauch]));
                        $this->UpdateVisualizationValue(json_encode(['export' => $export]));
                        $this->UpdateVisualizationValue(json_encode(['export_prozent' => $export_prozent]));
                        $this->UpdateVisualizationValue(json_encode(['import_prozent' => $import_prozent]));
                        $this->UpdateVisualizationValue(json_encode(['eigenverbrauch_prozent' => $eigenverbrauch_prozent]));
                        $this->UpdateVisualizationValue(json_encode(['eigenproduktion_prozent' => $eigenproduktion_prozent]));
                        $this->UpdateVisualizationValue(json_encode(['eigenverbrauch' => $eigenverbrauch]));
                        $this->UpdateVisualizationValue(json_encode(['eigenproduktion' => $eigenproduktion]));
                        break; // Beende die Schleife, da der passende Wert gefunden wurde

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
        //SetValue($variableID, !$currentValue);
        RequestAction($variableID, !$currentValue);
    }


    public function GetVisualizationTile()
    {
        // Füge ein Skript hinzu, um beim Laden, analog zu Änderungen bei Laufzeit, die Werte zu setzen
        $initialHandling = '<script>handleMessage(' . json_encode($this->GetFullUpdateMessage()) . ')</script>';

        // Füge statisches HTML aus Datei hinzu
        $module = file_get_contents(__DIR__ . '/module.html');

        // Gebe alles zurück.
        // Wichtig: $initialHandling nach hinten, da die Funktion handleMessage erst im HTML definiert wird
        return $module . $initialHandling;
    }



    private function GetFullUpdateMessage() {

        $result = [];
            $result['eigenverbrauchverlauffarbe1'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('EigenverbrauchVerlaufFarbe1'));
            $result['eigenverbrauchverlauffarbe2'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('EigenverbrauchVerlaufFarbe2'));
            $result['eigenproduktionverlauffarbe1'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('EigenproduktionVerlaufFarbe1'));
            $result['eigenproduktionverlauffarbe2'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('EigenproduktionVerlaufFarbe2'));
            $result['bildtransparenz'] =  $this->ReadPropertyFloat('Bildtransparenz');
            $result['kachelhintergrundfarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('Kachelhintergrundfarbe'));
            $result['schriftfarbebalken'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('SchriftfarbeBalken'));
            $result['schriftfarbesub'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('SchriftfarbeSub'));
            $result['schriftgroessebalken'] =  $this->ReadPropertyFloat('SchriftgroesseBalken');
            $result['schriftgroessesub'] =  $this->ReadPropertyFloat('SchriftgroesseSub');
            $result['eckenradius'] =  $this->ReadPropertyFloat('Eckenradius');
            $result['einspeisungfarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('EinspeisungFarbe'));
            $result['zukauffarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('ZukaufFarbe'));
            $result['produktionlabel'] =  $this->ReadPropertyString('ProduktionLabel');
            $result['exportlabel'] =  $this->ReadPropertyString('ExportLabel');
            $result['importlabel'] =  $this->ReadPropertyString('ImportLabel');
            $result['verbrauchlabel'] =  $this->ReadPropertyString('VerbrauchLabel');
            $result['eigenverbrauchlabel'] =  $this->ReadPropertyString('EigenverbrauchLabel');
            $result['eigenproduktionlabel'] =  $this->ReadPropertyString('EigenproduktionLabel');

            
            
            $archivID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];



            $SpeicherEntladungID = $this->ReadPropertyInteger('SpeicherEntladungWert');
            $speicherentladung = 1; // Standardwert setzen 

            if (IPS_VariableExists($SpeicherEntladungID) && AC_GetLoggingStatus($archivID, $SpeicherEntladungID)) {
                $SpeicherEntladung_heute_archiv = AC_GetAggregatedValues($archivID, $SpeicherEntladungID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                if (!empty($SpeicherEntladung_heute_archiv)) {
                    $speicherentladung = round($SpeicherEntladung_heute_archiv[0]['Avg'], 2);
                    if ($speicherentladung <= 0) {
                        $speicherentladung = 0.0;
                    }
                }
            }

            $SpeicherBeladungID = $this->ReadPropertyInteger('SpeicherBeladungWert');
            $speicherbeladung = 1; // Standardwert setzen 

            if (IPS_VariableExists($SpeicherBeladungID) && AC_GetLoggingStatus($archivID, $SpeicherBeladungID)) {
                $SpeicherBeladung_heute_archiv = AC_GetAggregatedValues($archivID, $SpeicherBeladungID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                if (!empty($SpeicherBeladung_heute_archiv)) {
                    $speicherbeladung = round($SpeicherBeladung_heute_archiv[0]['Avg'], 2);
                    if ($speicherbeladung <= 0) {
                        $speicherbeladung = 0.0;
                    }
                }
            }


            $produktionsID = $this->ReadPropertyInteger('ProduktionWert');
            $produktion = 1; // Standardwert setzen
            
            if (IPS_VariableExists($produktionsID) && AC_GetLoggingStatus($archivID, $produktionsID)) {
                $produktion_heute_archiv = AC_GetAggregatedValues($archivID, $produktionsID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                if (!empty($produktion_heute_archiv)) {
                    $produktion = round($speicherbeladung + $produktion_heute_archiv[0]['Avg'], 2);
                    if ($produktion <= 0) {
                        $produktion = 0.01;
                    }
                }
            }

          
            $importID = $this->ReadPropertyInteger('ImportWert');
            $import = 1; // Standardwert setzen
            
            if (IPS_VariableExists($importID) && AC_GetLoggingStatus($archivID, $importID)) {
                $import_heute_archiv = AC_GetAggregatedValues($archivID, $importID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                if (!empty($import_heute_archiv)) {
                    $import = round($import_heute_archiv[0]['Avg'], 2);
                    if ($import <= 0) {
                        $import = 0.01;
                    }
                }
            }

            $verbrauchID = $this->ReadPropertyInteger('VerbrauchWert');
            $verbrauch = 1; // Standardwert setzen
            
            if (IPS_VariableExists($verbrauchID) && AC_GetLoggingStatus($archivID, $verbrauchID)) {
                $verbrauch_heute_archiv = AC_GetAggregatedValues($archivID, $verbrauchID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                if (!empty($verbrauch_heute_archiv)) {
                    $verbrauch = round($verbrauch_heute_archiv[0]['Avg'], 2);
                    if ($verbrauch <= 0) {
                        $verbrauch = 0.01;
                    }
                }
                                            
            }



            $exportID = $this->ReadPropertyInteger('ExportWert');
            $export = 1; // Standardwert setzen
            $export_prozent = 0;
            
            if (IPS_VariableExists($exportID) && AC_GetLoggingStatus($archivID, $exportID)) {
                $export_heute_archiv = AC_GetAggregatedValues($archivID, $exportID, 1 /* Täglich */, strtotime("today 00:00"), time(), 0);
                if (!empty($export_heute_archiv)) {
                    $export = round($export_heute_archiv[0]['Avg'], 2);
                    if ($export <= 0) {
                        $export = 0.01;
                        $export_prozent = 0;
                    }
                    else {
                        $export_prozent = round($export / $produktion * 100, 0);

                    }
                }
            }
            $eigenverbrauch_prozent = round(100 - $export_prozent, 0);
            $eigenverbrauch = round($produktion - $export,2);
            $eigenproduktion = $eigenverbrauch;

         
            $eigenproduktion_prozent = round(($eigenproduktion / $verbrauch) * 100, 0);
            if ($import <= 0.1) {
                $import_prozent = 0;
                $import = 0;
            }
            else {
                $import_prozent = round(100 - $eigenproduktion_prozent, 0);
            }          
            
            $result['produktion'] = $produktion;
            $result['speicherentladungwert'] = $speicherentladung;
            $result['speicherbeladungwert'] = $speicherbeladung;
            $result['export'] = $export;
            $result['import'] = $import; 
            $result['verbrauch'] = $verbrauch; 

            $result['export_prozent'] = $export_prozent;
            $result['import_prozent'] = $import_prozent;
            $result['eigenverbrauch_prozent'] = $eigenverbrauch_prozent;
            $result['eigenproduktion_prozent'] = $eigenproduktion_prozent;
            $result['eigenverbrauch'] =  $eigenverbrauch;
            $result['eigenproduktion'] =  $eigenproduktion;



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
                $imageContent .= base64_encode(file_get_contents(__DIR__ . '/../imgs/kachelhintergrund1.png'));


                if ($this->ReadPropertyBoolean('BG_Off')) {
                    $result['image1'] = $imageContent;
                }
            }     







        return json_encode($result);
    }



    private function CheckAndGetValueFormatted($property) {
        $id = $this->ReadPropertyInteger($property);
        if (IPS_VariableExists($id)) {
            return GetValueFormatted($id);
        }
        return false;
    }


    private function GetColor($id) {
        $variable = IPS_GetVariable($id);
        $Value = GetValue($id);
        $profile = $variable['VariableCustomProfile'] ?: $variable['VariableProfile'];

        if ($profile && IPS_VariableProfileExists($profile)) {
            $p = IPS_GetVariableProfile($profile);
            
            foreach ($p['Associations'] as $association) {
                if (isset($association['Value'], $association['Color']) && $association['Value'] == $Value) {
                    return $association['Color'] === -1 ? "" : sprintf('%06X', $association['Color']);
                    
                }
            }
        }
        return "";
    }


    private function GetColorRGB($hexcolor) {
        $transparenz = $this->ReadPropertyFloat('InfoMenueTransparenz');
        if($hexcolor != "-1")
        {
                $hexColor = sprintf('%06X', $hexcolor);
                // Prüft, ob der Hex-Farbwert gültig ist
                if (strlen($hexColor) == 6) {
                    $r = hexdec(substr($hexColor, 0, 2));
                    $g = hexdec(substr($hexColor, 2, 2));
                    $b = hexdec(substr($hexColor, 4, 2));
                    return "rgba($r, $g, $b, $transparenz)";
                } else {
                    // Fallback für ungültige Eingaben
                    return $hexColor;
                }
        }
        else {
            return "";
        }
    }

    private function GetIcon($id, $varicon) {
        $variable = IPS_GetVariable($id);
        $Value = GetValue($id);
        $icon = "";
        //Abfragen ob das Variablen-Icon oder das Profil-Icon verwendet werden soll
        if($varicon == true){
        $icon = IPS_GetObject($id);
            if($icon['ObjectIcon'] != ""){
                $icon = $icon['ObjectIcon'];
            }
            else {
                $icon = "Transparent";
            }
        }
        else {
        // Profil-Icon abrufen
        $profile = $variable['VariableCustomProfile'] ?: $variable['VariableProfile'];
        $icon = "";

        if ($profile && IPS_VariableProfileExists($profile)) {
            $p = IPS_GetVariableProfile($profile);

            foreach ($p['Associations'] as $association) {
                if (isset($association['Value']) && $association['Icon'] != "" && $association['Value'] == $Value) {
                    $icon = $association['Icon'];
                    break;
                }
            }

            if ($icon == "" && isset($p['Icon']) && $p['Icon'] != "") {
                $icon = $p['Icon'];
            }

            if ($icon == "") {
                $icon = "Transparent";
            }
        }
        else {
            $icon = "Transparent";
        }
        
        }
        return $icon;
    }

}
?>
