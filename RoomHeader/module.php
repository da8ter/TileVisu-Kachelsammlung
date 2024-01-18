<?php
class TileVisuRoomHeader extends IPSModule
{
    public function Create()
    {
        // Nie diese Zeile löschen!
        parent::Create();

        // Drei Eigenschaften für die dargestellten Zähler
        $this->RegisterPropertyInteger("bgImage", 0);
        $this->RegisterPropertyInteger("Variable", 0);
        $this->RegisterPropertyFloat('Schriftgroesse', 1);
        $this->RegisterPropertyFloat('Bildtransparenz', 0.7);
        $this->RegisterPropertyInteger('Kachelhintergrundfarbe', 0x000000);
        $this->RegisterPropertyInteger('Schriftfarbe', 0xFFFFFF);
        $this->RegisterPropertyString('Raumname', 'Raumname');
        $this->RegisterPropertyFloat('RaumnameSchriftgroesse', 1);
        $this->RegisterPropertyInteger('RaumnameSchriftfarbe', 0xFFFFFF);
        $this->RegisterPropertyInteger('Schalter', 0);
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


        foreach (['bgImage', 'Variable', 'Schalter'] as $VariableProperty)        {
            $this->RegisterMessage($this->ReadPropertyInteger($VariableProperty), VM_UPDATE);
        }

        // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        foreach (['bgImage', 'Variable', 'Schalter'] as $index => $VariableProperty)
        {
            if ($SenderID === $this->ReadPropertyInteger($VariableProperty))
            {
                switch ($Message)
                {
                    case VM_UPDATE:
                        
                        // Teile der HTML-Darstellung den neuen Wert mit. Damit dieser korrekt formatiert ist, holen wir uns den von der Variablen via GetValueFormatted
                        $this->UpdateVisualizationValue(json_encode([$VariableProperty => GetValueFormatted($this->ReadPropertyInteger($VariableProperty))]));
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
    private function GetFullUpdateMessage()
    {
        $VariableID = $this->ReadPropertyInteger('Variable');
        $VariableExists = IPS_VariableExists($VariableID);
        $SchalterID = $this->ReadPropertyInteger('Schalter');
        $SchalterExists = IPS_VariableExists($SchalterID);
        $result = [
            'VariableExist' => $VariableExists,
            'SchalterExist' => $SchalterExists
        ];
        $result['fontsize'] = $this->ReadPropertyFloat('Schriftgroesse');
        $result['hintergrundfarbe'] = '#' . sprintf('%06X', $this->ReadPropertyInteger('Kachelhintergrundfarbe'));
        $result['schriftfarbe'] = '#' . sprintf('%06X', $this->ReadPropertyInteger('Schriftfarbe'));
        $result['transparenz'] = $this->ReadPropertyFloat('Bildtransparenz');
        $result['raumname'] = $this->ReadPropertyString('Raumname');
        $result['raumnameschriftgroesse'] = $this->ReadPropertyFloat('RaumnameSchriftgroesse');
        $result['raumnameschriftfarbe'] = '#' . sprintf('%06X', $this->ReadPropertyInteger('RaumnameSchriftfarbe'));
        if ($VariableExists)
        {
            $result['variable'] = GetValueFormatted($VariableID);
        }
        if ($SchalterExists)
        {
            $result['Schalter'] = GetValueFormatted($SchalterID);

            $variable = IPS_GetVariable($SchalterID);
                        
            $Value = GetValue($SchalterID);
            //$ValueFormatted = GetValueFormatted($variableID);
            $profile = $variable['VariableCustomProfile'];
            if ($profile === '') {
                $profile = $variable['VariableProfile'];
            }

            $p = IPS_VariableProfileExists($profile) ? IPS_GetVariableProfile($profile) : null;

            if (IPS_VariableProfileExists($profile)) {
                $p = IPS_GetVariableProfile($profile);
            
                $colorhexWert = "";
                $result['schaltercolor'] = $colorhexWert;
            
                if (!empty($p['Associations']) && is_array($p['Associations'])) {
                    foreach ($p['Associations'] as $association) {
                        // Prüfe, ob der aktuelle Wert dem gesuchten Wert entspricht
                        if (isset($association['Value'], $association['Color']) && $association['Value'] == $Value) {
                            // Überprüfe, ob $color -1 ist und setze $colorhexWert entsprechend
                            $colorhexWert = $association['Color'] === -1 ? "" : sprintf('%06X', $association['Color']);
                            $result['schaltercolor'] = $colorhexWert;
                            break; // Beende die Schleife, da der passende Wert gefunden wurde
                        }
                    }
                }
            } else {
                $colorhexWert = "";
                $result['schaltercolor'] = $colorhexWert;
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
}
?>
