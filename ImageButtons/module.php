<?php
class TileVisuImageButtons extends IPSModule
{
    public function Create()
    {
        // Nie diese Zeile löschen!
        parent::Create();


        // Drei Eigenschaften für die dargestellten Zähler
        $this->RegisterPropertyInteger("bgImage", 0);
        $this->RegisterPropertyBoolean('BG_Off', 1);
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
        $this->RegisterPropertyInteger('Schalter6', 0);
        $this->RegisterPropertyFloat('Schalter6Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter6Breite', 100);
        $this->RegisterPropertyString('Schalter6AltName', '');
        $this->RegisterPropertyInteger('Schalter7', 0);
        $this->RegisterPropertyFloat('Schalter7Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter7Breite', 100);
        $this->RegisterPropertyString('Schalter7AltName', '');
        $this->RegisterPropertyInteger('Schalter8', 0);
        $this->RegisterPropertyFloat('Schalter8Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter8Breite', 100);
        $this->RegisterPropertyString('Schalter8AltName', '');
        $this->RegisterPropertyInteger('Schalter9', 0);
        $this->RegisterPropertyFloat('Schalter9Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter9Breite', 100);
        $this->RegisterPropertyString('Schalter9AltName', '');
        $this->RegisterPropertyInteger('Schalter10', 0);
        $this->RegisterPropertyFloat('Schalter10Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Schalter10Breite', 100);
        $this->RegisterPropertyString('Schalter10AltName', '');
        $this->RegisterPropertyBoolean('Schalter1NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter2NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter3NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter4NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter5NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter6NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter7NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter8NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter9NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter10NameSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter1IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter2IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter3IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter4IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter5IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter6IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter7IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter8IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter9IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter10IconSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter1VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter2VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter3VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter4VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter5VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter6VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter7VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter8VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter9VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter10VarIconSwitch', 0);
        $this->RegisterPropertyBoolean('Schalter1AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter2AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter3AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter4AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter5AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter6AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter7AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter8AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter9AssoSwitch', 1);
        $this->RegisterPropertyBoolean('Schalter10AssoSwitch', 1);
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
            $this->ReadPropertyInteger('Schalter6'),
            $this->ReadPropertyInteger('Schalter7'),
            $this->ReadPropertyInteger('Schalter8'),
            $this->ReadPropertyInteger('Schalter9'),
            $this->ReadPropertyInteger('Schalter10'),
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


        foreach (['bgImage', 'Schalter1', 'Schalter2', 'Schalter3', 'Schalter4', 'Schalter5', 'Schalter6', 'Schalter7', 'Schalter8', 'Schalter9', 'Schalter10'] as $VariableProperty)        {
            $this->RegisterMessage($this->ReadPropertyInteger($VariableProperty), VM_UPDATE);
        }

        // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        foreach (['bgImage', 'Schalter1', 'Schalter2', 'Schalter3', 'Schalter4', 'Schalter5', 'Schalter6', 'Schalter7', 'Schalter8', 'Schalter9', 'Schalter10'] as $index => $VariableProperty)
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
    
        for ($i = 1; $i <= 10; $i++) {
            $schalterID = $this->ReadPropertyInteger("Schalter$i");
            if (IPS_VariableExists($schalterID)) {
                $prefix = "schalter$i";
                $result[$prefix] = $this->CheckAndGetValueFormatted("Schalter$i");
                $result[$prefix . 'breite'] = $this->ReadPropertyFloat("Schalter{$i}Breite");
                $result[$prefix . 'color'] = $this->GetColor($schalterID);
        
                if ($this->ReadPropertyBoolean("Schalter{$i}NameSwitch")) {
                    $result[$prefix . 'name'] = IPS_GetName($schalterID);
                }
        
                $iconSwitch = $this->ReadPropertyBoolean("Schalter{$i}IconSwitch");
                $varIconSwitch = $this->ReadPropertyBoolean("Schalter{$i}VarIconSwitch");
                if ($iconSwitch) {
                    $icon = $this->GetIcon($schalterID, $varIconSwitch);
                    if ($icon !== "Transparent") {
                        $result[$prefix . 'icon'] = $icon;
                    }
                }
        
                if ($this->ReadPropertyBoolean("Schalter{$i}AssoSwitch")) {
                    $result[$prefix . 'asso'] = $this->CheckAndGetValueFormatted("Schalter$i");
                }
            }
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
            $result['schalter6altname'] =  $this->ReadPropertyString('Schalter6AltName');
            $result['schalter7altname'] =  $this->ReadPropertyString('Schalter7AltName');
            $result['schalter8altname'] =  $this->ReadPropertyString('Schalter8AltName');
            $result['schalter9altname'] =  $this->ReadPropertyString('Schalter9AltName');
            $result['schalter10altname'] =  $this->ReadPropertyString('Schalter10AltName');
            
            // Prüfe vorweg, ob ein Bild ausgewählt wurde
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
                        $result['bgimage'] = $imageContent;
                    }

                }
            }
            else{
                $imageContent = 'data:image/png;base64,';
                $imageContent .= base64_encode(file_get_contents(__DIR__ . '/../imgs/kachelhintergrund1.png'));

                if ($this->ReadPropertyBoolean('BG_Off')) {
                    $result['bgimage'] = $imageContent;
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
