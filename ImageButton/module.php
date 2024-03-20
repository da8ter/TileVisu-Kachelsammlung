<?php
class TileVisuImageButton extends IPSModule
{
    public function Create()
    {
        // Nie diese Zeile löschen!
        parent::Create();


        // Drei Eigenschaften für die dargestellten Zähler
        $this->RegisterPropertyInteger("Variable", 0);
        $this->RegisterPropertyFloat("Schriftgroesse", 1);
        $this->RegisterPropertyInteger("Bildauswahl", 0);
        $this->RegisterPropertyBoolean('BG_Off', 1);
        $this->RegisterPropertyInteger("bgImage", 0);
        $this->RegisterPropertyFloat('Bildtransparenz', 0.7);
        $this->RegisterPropertyInteger('Kachelhintergrundfarbe', -1);

        // Visualisierungstyp auf 1 setzen, da wir HTML anbieten möchten
        $this->SetVisualizationType(1);
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();

        
        //Referenzen Registrieren
        $ids = [
            $this->ReadPropertyInteger('Variable'),
            $this->ReadPropertyInteger('bgImage')
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


        foreach (['Variable', 'bgImage'] as $VariableProperty)        {
            $this->RegisterMessage($this->ReadPropertyInteger($VariableProperty), VM_UPDATE);
        }

        // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        foreach (['Variable', 'bgImage'] as $index => $VariableProperty)
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

       // $profilAssoziationen = $this->ReadPropertyString('ProfilAssoziazionen');

        // Ausgabe des Wertes zur Debugging-Zwecken
       // var_dump($profilAssoziationen);

        $result = [];
    
            //$result['status'] = $this->CheckAndGetValueFormatted('Status');
            if (IPS_VariableExists($this->ReadPropertyInteger('Variable'))) {
                $result['variable'] = $this->CheckAndGetValueFormatted('Variable');
                if ($this->ReadPropertyBoolean('VariableNameSwitch')) $result['variablename'] = IPS_GetName($this->ReadPropertyInteger('Variable'));
                if ($this->ReadPropertyBoolean('VariableIconSwitch') && $this->GetIcon($this->ReadPropertyInteger('Variable'), $this->ReadPropertyBoolean('VariableVarIconSwitch')) !== "Transparent") {
                    $result['variableicon'] = $this->GetIcon($this->ReadPropertyInteger('Variable'), $this->ReadPropertyBoolean('VariableVarIconSwitch'));
                }
                if ($this->ReadPropertyBoolean('VariableAssoSwitch')) $result['variablesasso'] = $this->CheckAndGetValueFormatted('Variable');
            }
            $result['schriftgroesse'] =  $this->ReadPropertyFloat('Schriftgroesse');
            $result['bildtransparenz'] =  $this->ReadPropertyFloat('Bildtransparenz');
            $result['kachelhintergrundfarbe'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('Kachelhintergrundfarbe'));

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
                    return "rgba($r, $g, $b, '1')";
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
