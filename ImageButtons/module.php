<?php
class TileVisuImageButtons extends IPSModule
{
    public function Create()
    {
        // Nie diese Zeile löschen!
        parent::Create();


        // Drei Eigenschaften für die dargestellten Zähler
        $this->RegisterPropertyInteger("bgImage", 0);
        $this->RegisterPropertyFloat('Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Bildtransparenz', 0.7);
        $this->RegisterPropertyInteger('Kachelhintergrundfarbe', 0x000000);
        $this->RegisterPropertyInteger('InfoMenueSchriftfarbe', 0xFFFFFF);
        $this->RegisterPropertyInteger('Schalter1', 0);
        $this->RegisterPropertyFloat('Schalter1Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter1Breite', 100);
        $this->RegisterPropertyString('Schalter1AltName', '');
        $this->RegisterPropertyInteger('Schalter2', 0);
        $this->RegisterPropertyFloat('Schalter2Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter2Breite', 100);
        $this->RegisterPropertyString('Schalter2AltName', '');
        $this->RegisterPropertyInteger('Schalter3', 0);
        $this->RegisterPropertyFloat('Schalter3Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter3Breite', 100);
        $this->RegisterPropertyString('Schalter3AltName', '');
        $this->RegisterPropertyInteger('Schalter4', 0);
        $this->RegisterPropertyFloat('Schalter4Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter4Breite', 100);
        $this->RegisterPropertyString('Schalter4AltName', '');
        $this->RegisterPropertyInteger('Schalter5', 0);
        $this->RegisterPropertyFloat('Schalter5Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter5Breite', 100);
        $this->RegisterPropertyString('Schalter5AltName', '');
        $this->RegisterPropertyBoolean('Schalter1NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter2NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter3NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter4NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter5NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter1IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter2IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter3IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter4IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter5IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter1VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter2VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter3VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter4VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter5VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter1AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter2AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter3AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter4AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter5AssoSwitch', 1);
        // Visualisierungstyp auf 1 setzen, da wir HTML anbieten möchten
        $this->SetVisualizationType(1);
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();


        //Referenzen Registrieren
        $ids = [
            $this->ReadPropertyInteger('bgImage'),
            $this->ReadPropertyInteger('Schalter1'),
            $this->ReadPropertyInteger('Schalter2'),
            $this->ReadPropertyInteger('Schalter3'),
            $this->ReadPropertyInteger('Schalter4'),
            $this->ReadPropertyInteger('Schalter5'),
        ];
        $refs = $this->GetReferenceList();
            foreach($refs as $ref) {
                $this->UnregisterReference($ref);
            } 
            foreach ($ids as $id) {
                if ($id !== '') {
                    $this->RegisterReference($id);
                }
            }

        
        // Aktualisiere registrierte Nachrichten
        foreach ($this->GetMessageList() as $senderID => $messageIDs)
        {
            foreach ($messageIDs as $messageID)
            {
                $this->UnregisterMessage($senderID, $messageID);
            }
        }


        foreach (['bgImage', 'Schalter1', 'Schalter2', 'Schalter3', 'Schalter4', 'Schalter5'] as $VariableProperty)        {
            $this->RegisterMessage($this->ReadPropertyInteger($VariableProperty), VM_UPDATE);
        }

        // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        foreach (['bgImage', 'Schalter1', 'Schalter2', 'Schalter3', 'Schalter4', 'Schalter5'] as $index => $VariableProperty)
        {
            if ($SenderID === $this->ReadPropertyInteger($VariableProperty))
            {
                

                switch ($Message)
                {
                    case VM_UPDATE:
                        
                        // Teile der HTML-Darstellung den neuen Wert mit. Damit dieser korrekt formatiert ist, holen wir uns den von der Variablen via GetValueFormatted
                        $this->UpdateVisualizationValue(json_encode([$VariableProperty => GetValueFormatted($this->ReadPropertyInteger($VariableProperty))]));
                        
                        //Icon und Farbe abrufen
                            //Farbe abrufen
                            $result[$VariableProperty . 'Color'] = $this->GetColor($this->ReadPropertyInteger($VariableProperty));

                            if($VariableProperty != 'bgImage')
                            {
                                if ($this->ReadPropertyBoolean($VariableProperty . 'NameSwitch')) $result[$VariableProperty . 'name'] = IPS_GetName($this->ReadPropertyInteger($VariableProperty));
                                if ($this->ReadPropertyBoolean($VariableProperty . 'IconSwitch') && $this->GetIcon($this->ReadPropertyInteger($VariableProperty), $this->ReadPropertyBoolean($VariableProperty . 'VarIconSwitch')) !== "Transparent") {
                                   $result[$VariableProperty .'icon'] = $this->GetIcon($this->ReadPropertyInteger($VariableProperty), $this->ReadPropertyBoolean($VariableProperty . 'VarIconSwitch'));
                                }
                                if ($this->ReadPropertyBoolean($VariableProperty . 'AssoSwitch')) $result[$VariableProperty . 'asso'] = $this->CheckAndGetValueFormatted($VariableProperty);
                                $result[$VariableProperty .'AltName'] =  $this->ReadPropertyString($VariableProperty .'AltName');
                            }

                            $this->UpdateVisualizationValue(json_encode($result));

                            
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



    // Generiere eine Nachricht, die alle Elemente in der HTML-Darstellung aktualisiert
    private function GetFullUpdateMessage() {

        $result = [];
    
        if (IPS_VariableExists($this->ReadPropertyInteger('Schalter1'))) {
            $result['schalter1'] = $this->CheckAndGetValueFormatted('Schalter1');
            $result['schalter1breite'] =  $this->ReadPropertyFloat('Schalter1Breite');
            $result['schalter1color'] =  $this->GetColor($this->ReadPropertyInteger('Schalter1'));
            if ($this->ReadPropertyBoolean('Schalter1NameSwitch')) $result['schalter1name'] = IPS_GetName($this->ReadPropertyInteger('Schalter1'));
            if ($this->ReadPropertyBoolean('Schalter1IconSwitch') && $this->GetIcon($this->ReadPropertyInteger('Schalter1'), $this->ReadPropertyBoolean('Schalter1VarIconSwitch')) !== "Transparent") {
                $result['schalter1icon'] = $this->GetIcon($this->ReadPropertyInteger('Schalter1'), $this->ReadPropertyBoolean('Schalter1VarIconSwitch'));
            }
            if ($this->ReadPropertyBoolean('Schalter1AssoSwitch')) $result['schalter1asso'] = $this->CheckAndGetValueFormatted('Schalter1');
        }
        if (IPS_VariableExists($this->ReadPropertyInteger('Schalter2'))) {
            $result['Schalter2'] = $this->CheckAndGetValueFormatted('Schalter2');
            $result['schalter2breite'] =  $this->ReadPropertyFloat('Schalter2Breite');
            $result['schalter2color'] =  $this->GetColor($this->ReadPropertyInteger('Schalter2'));
            if ($this->ReadPropertyBoolean('Schalter2NameSwitch')) $result['schalter2name'] = IPS_GetName($this->ReadPropertyInteger('Schalter2'));
            if ($this->ReadPropertyBoolean('Schalter2IconSwitch') && $this->GetIcon($this->ReadPropertyInteger('Schalter2'), $this->ReadPropertyBoolean('Schalter2VarIconSwitch')) !== "Transparent") {
                $result['schalter2icon'] = $this->GetIcon($this->ReadPropertyInteger('Schalter2'), $this->ReadPropertyBoolean('Schalter2VarIconSwitch'));
            }
            if ($this->ReadPropertyBoolean('Schalter2AssoSwitch')) $result['schalter2asso'] = $this->CheckAndGetValueFormatted('Schalter2');
        }
        if (IPS_VariableExists($this->ReadPropertyInteger('Schalter3'))) {
            $result['Schalter3'] = $this->CheckAndGetValueFormatted('Schalter3');
            $result['schalter3breite'] =  $this->ReadPropertyFloat('Schalter3Breite');
            $result['schalter3color'] =  $this->GetColor($this->ReadPropertyInteger('Schalter3'));
            if ($this->ReadPropertyBoolean('Schalter3NameSwitch')) $result['schalter3name'] = IPS_GetName($this->ReadPropertyInteger('Schalter3'));
            if ($this->ReadPropertyBoolean('Schalter3IconSwitch') && $this->GetIcon($this->ReadPropertyInteger('Schalter3'), $this->ReadPropertyBoolean('Schalter3VarIconSwitch')) !== "Transparent") {
                $result['schalter3icon'] = $this->GetIcon($this->ReadPropertyInteger('Schalter3'), $this->ReadPropertyBoolean('Schalter3VarIconSwitch'));
            }
            if ($this->ReadPropertyBoolean('Schalter3AssoSwitch')) $result['schalter3asso'] = $this->CheckAndGetValueFormatted('Schalter3');
        }
        if (IPS_VariableExists($this->ReadPropertyInteger('Schalter4'))) {
            $result['Schalter4'] = $this->CheckAndGetValueFormatted('Schalter4');
            $result['schalter4breite'] =  $this->ReadPropertyFloat('Schalter4Breite');
            $result['schalter4color'] =  $this->GetColor($this->ReadPropertyInteger('Schalter4'));
            if ($this->ReadPropertyBoolean('Schalter4NameSwitch')) $result['schalter4name'] = IPS_GetName($this->ReadPropertyInteger('Schalter4'));
            if ($this->ReadPropertyBoolean('Schalter4IconSwitch') && $this->GetIcon($this->ReadPropertyInteger('Schalter4'), $this->ReadPropertyBoolean('Schalter4VarIconSwitch')) !== "Transparent") {
                $result['schalter4icon'] = $this->GetIcon($this->ReadPropertyInteger('Schalter4'), $this->ReadPropertyBoolean('Schalter4VarIconSwitch'));
            }
            if ($this->ReadPropertyBoolean('Schalter4AssoSwitch')) $result['schalter4asso'] = $this->CheckAndGetValueFormatted('Schalter4');
        }
        if (IPS_VariableExists($this->ReadPropertyInteger('Schalter5'))) {
            $result['Schalter5'] = $this->CheckAndGetValueFormatted('Schalter5');
            $result['schalter5breite'] =  $this->ReadPropertyFloat('Schalter5Breite');
            $result['schalter5color'] =  $this->GetColor($this->ReadPropertyInteger('Schalter5'));
            if ($this->ReadPropertyBoolean('Schalter5NameSwitch')) $result['schalter5name'] = IPS_GetName($this->ReadPropertyInteger('Schalter5'));
            if ($this->ReadPropertyBoolean('Schalter5IconSwitch') && $this->GetIcon($this->ReadPropertyInteger('Schalter5'), $this->ReadPropertyBoolean('Schalter5VarIconSwitch')) !== "Transparent") {
                $result['schalter5icon'] = $this->GetIcon($this->ReadPropertyInteger('Schalter5'), $this->ReadPropertyBoolean('Schalter5VarIconSwitch'));
            }
            if ($this->ReadPropertyBoolean('Schalter5AssoSwitch')) $result['schalter5asso'] = $this->CheckAndGetValueFormatted('Schalter5');
        }

            $result['hintergrundfarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('Kachelhintergrundfarbe'));
            $result['infomenueschriftfarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('InfoMenueSchriftfarbe'));
            $result['schriftgroesse'] =  $this->ReadPropertyFloat('Schriftgroesse');
            $result['transparenz'] =  $this->ReadPropertyFloat('Bildtransparenz');
            $result['schalter1altname'] =  $this->ReadPropertyString('Schalter1AltName');
            $result['schalter2altname'] =  $this->ReadPropertyString('Schalter2AltName');
            $result['schalter3altname'] =  $this->ReadPropertyString('Schalter3AltName');
            $result['schalter4altname'] =  $this->ReadPropertyString('Schalter4AltName');
            $result['schalter5altname'] =  $this->ReadPropertyString('Schalter5AltName');
            $result['info1altname'] =  $this->ReadPropertyString('Info1AltName');
            $result['info2altname'] =  $this->ReadPropertyString('Info2AltName');
            $result['info3altname'] =  $this->ReadPropertyString('Info3AltName');
            $result['info4altname'] =  $this->ReadPropertyString('Info4AltName');
            $result['info5altname'] =  $this->ReadPropertyString('Info5AltName');         
            $result['infolinksaltname'] =  $this->ReadPropertyString('InfoLinksAltName');
            $result['inforechtsaltname'] =  $this->ReadPropertyString('InfoRechtsAltName');    
            $result['infolinks2altname'] =  $this->ReadPropertyString('InfoLinks2AltName');
            $result['inforechts2altname'] =  $this->ReadPropertyString('InfoRechts2AltName');    
            $result['infomenueswitch'] =  $this->ReadPropertyBoolean('InfoMenueSwitch');   
            
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
