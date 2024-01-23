<?php
class TileVisuRoomHeader extends IPSModule
{
    public function Create()
    {
        // Nie diese Zeile löschen!
        parent::Create();

        // Drei Eigenschaften für die dargestellten Zähler
        $this->RegisterPropertyInteger("bgImage", 0);
        $this->RegisterPropertyInteger("InfoLinks", 0);
        $this->RegisterPropertyInteger("InfoRechts", 0);
        $this->RegisterPropertyFloat('Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Bildtransparenz', 0.7);
        $this->RegisterPropertyInteger('Kachelhintergrundfarbe', 0x000000);
        $this->RegisterPropertyInteger('Schriftfarbe', 0xFFFFFF);
        $this->RegisterPropertyString('Raumname', 'Raumname');
        $this->RegisterPropertyFloat('RaumnameSchriftgroesse', 1);
        $this->RegisterPropertyInteger('RaumnameSchriftfarbe', 0xFFFFFF);
        $this->RegisterPropertyInteger('Schalter1', 0);
        $this->RegisterPropertyFloat('Schalter1Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter1Breite', 100);
        $this->RegisterPropertyInteger('Schalter2', 0);
        $this->RegisterPropertyFloat('Schalter2Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter2Breite', 100);
        $this->RegisterPropertyInteger('Schalter3', 0);
        $this->RegisterPropertyFloat('Schalter3Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter3Breite', 100);
        $this->RegisterPropertyInteger('Schalter4', 0);
        $this->RegisterPropertyFloat('Schalter4Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter4Breite', 100);
        $this->RegisterPropertyInteger('Schalter5', 0);
        $this->RegisterPropertyFloat('Schalter5Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter5Breite', 100);
        $this->RegisterPropertyInteger('Info1', 0);
        $this->RegisterPropertyInteger('Info2', 0);
        $this->RegisterPropertyInteger('Info3', 0);
        $this->RegisterPropertyInteger('Info4', 0);
        $this->RegisterPropertyInteger('Info5', 0);
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


        foreach (['bgImage', 'InfoLinks', 'InfoRechts', 'Schalter1', 'Schalter2', 'Schalter3', 'Schalter4', 'Schalter5', 'Info1', 'Info2', 'Info3', 'Info4', 'Info5'] as $VariableProperty)        {
            $this->RegisterMessage($this->ReadPropertyInteger($VariableProperty), VM_UPDATE);
        }

        // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        foreach (['bgImage', 'InfoLinks', 'InfoRechts', 'Schalter1', 'Schalter2', 'Schalter3', 'Schalter4', 'Schalter5', 'Info1', 'Info2', 'Info3', 'Info4', 'Info5'] as $index => $VariableProperty)
        {
            if ($SenderID === $this->ReadPropertyInteger($VariableProperty))
            {
                

                switch ($Message)
                {
                    case VM_UPDATE:
                        
                        // Teile der HTML-Darstellung den neuen Wert mit. Damit dieser korrekt formatiert ist, holen wir uns den von der Variablen via GetValueFormatted
                        $this->UpdateVisualizationValue(json_encode([$VariableProperty => GetValueFormatted($this->ReadPropertyInteger($VariableProperty))]));
                        
                        //Icon und Farbe abrufen
                        $propertiesToCheck = ["Schalter1", "Schalter2", "Schalter3", "Schalter4", "Schalter5", "InfoLinks", "InfoRechts", "Info1", "Info2", "Info3", "Info4", "Info5"];
                        if (in_array($VariableProperty, $propertiesToCheck)) {
                                        
                            $variable = IPS_GetVariable($this->ReadPropertyInteger($VariableProperty));
                            $variableID = $this->ReadPropertyInteger($VariableProperty);

                            //Farbe abrufen
                            $result[$VariableProperty . 'Color'] = $this->GetColor($this->ReadPropertyInteger($variableID));

                            //Icon abrufen
                            $icon = $this->GetIcon($this->ReadPropertyInteger($VariableProperty));
                            $result[$VariableProperty .'Icon'] = $icon;
                        break; // Beende die Schleife, da der passende Wert gefunden wurde

                            
                            $this->UpdateVisualizationValue(json_encode($result));
                
                        }
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



    // Generiere eine Nachricht, die alle Elemente in der HTML-Darstellung aktualisiert
    private function GetFullUpdateMessage() {

        $result = [];
    
        if (IPS_VariableExists($this->ReadPropertyInteger('InfoLinks'))) {
            $result['infolinks'] = $this->CheckAndGetValueFormatted('InfoLinks');
            $result['infolinksicon'] = $this->GetIcon($this->ReadPropertyInteger('InfoLinks'));
        }
    
        if (IPS_VariableExists($this->ReadPropertyInteger('InfoRechts'))) {
            $result['inforechts'] = $this->CheckAndGetValueFormatted('InfoRechts');
            $result['inforechtsicon'] = $this->GetIcon($this->ReadPropertyInteger('InfoRechts'));
        }
        if (IPS_VariableExists($this->ReadPropertyInteger('Info1'))) {
            $result['info1'] = $this->CheckAndGetValueFormatted('Info1');
            $result['info1icon'] = $this->GetIcon($this->ReadPropertyInteger('Info1'));
        }
        if (IPS_VariableExists($this->ReadPropertyInteger('Info2'))) {
            $result['info2'] = $this->CheckAndGetValueFormatted('Info2');
            $result['info2icon'] = $this->GetIcon($this->ReadPropertyInteger('Info2'));
        }
        if (IPS_VariableExists($this->ReadPropertyInteger('Info3'))) {
            $result['info3'] = $this->CheckAndGetValueFormatted('Info3');
            $result['info3icon'] = $this->GetIcon($this->ReadPropertyInteger('Info3'));
        }
        if (IPS_VariableExists($this->ReadPropertyInteger('Info4'))) {
            $result['info4'] = $this->CheckAndGetValueFormatted('Info4');
            $result['info4icon'] = $this->GetIcon($this->ReadPropertyInteger('Info4'));
        }
        if (IPS_VariableExists($this->ReadPropertyInteger('Info5'))) {
            $result['info5'] = $this->CheckAndGetValueFormatted('Info5');
            $result['info5icon'] = $this->GetIcon($this->ReadPropertyInteger('Info5'));
        }

            $result['fontsize'] = $this->ReadPropertyFloat('Schriftgroesse');


            $result['fontsize'] =  $this->ReadPropertyFloat('Schriftgroesse');
            $result['hintergrundfarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('Kachelhintergrundfarbe'));
            $result['schriftfarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('Schriftfarbe'));
            $result['transparenz'] =  $this->ReadPropertyFloat('Bildtransparenz');
            $result['raumname'] =  $this->ReadPropertyString('Raumname');
            $result['raumnameschriftgroesse'] =  $this->ReadPropertyFloat('RaumnameSchriftgroesse');
            $result['raumnameschriftfarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('RaumnameSchriftfarbe'));
            $result['schalter1breite'] =  $this->ReadPropertyFloat('Schalter1Breite');
            $result['schalter2breite'] =  $this->ReadPropertyFloat('Schalter2Breite');
            $result['schalter3breite'] =  $this->ReadPropertyFloat('Schalter3Breite');
            $result['schalter4breite'] =  $this->ReadPropertyFloat('Schalter4Breite');
            $result['schalter5breite'] =  $this->ReadPropertyFloat('Schalter5Breite');
 

        $schalterIDs = ['Schalter1', 'Schalter2', 'Schalter3', 'Schalter4', 'Schalter5'];

        foreach ($schalterIDs as $schalter) {
            $schalterValue = $this->CheckAndGetValueFormatted($schalter);
            
            if ($schalterValue) {
                $result[$schalter] = $schalterValue;
                $result[$schalter . 'Color'] = $this->GetColor($this->ReadPropertyInteger($schalter));
            }
        }

            // Prüfe vorweg, ob ein Bild ausgewählt wurde
            $imageID = $this->ReadPropertyInteger('bgImage');
            if (IPS_MediaExists($imageID))
            {
                $image = IPS_GetMedia($imageID);
                if ($image['MediaType'] === MEDIATYPE_IMAGE)
                {
                    $imageFile = explode('.', $image['MediaFile']);
                    $imageContent = '';
                    // Falls ja, ermittle den Anfang der src basierend auf dem Dateitypen
                    switch (end($imageFile))
                    {
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
                    if ($imageContent)
                    {
                        // Hänge base64-codierten Inhalt des Bildes an
                        $imageContent .= IPS_GetMediaContent($imageID);
                        $result['image1'] = $imageContent;
                    }
                }
            }
            else
            {
                $imageContent = 'data:image/png;base64,';
                $imageContent .= base64_encode(file_get_contents(__DIR__ . '/assets/placeholder.png'));
                $result['image1'] = $imageContent;
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

    private function GetIcon($id) {
        $variable = IPS_GetVariable($id);
        $Value = GetValue($id);
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
        return $icon;
    }

}
?>