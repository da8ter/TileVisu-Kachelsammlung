<?php
class TileVisuWashingMaschine extends IPSModule
{
    public function Create()
    {
        // Nie diese Zeile löschen!
        parent::Create();


        // Drei Eigenschaften für die dargestellten Zähler
        $this->RegisterPropertyInteger("Status", 0);
        $this->RegisterPropertyInteger("Programm", 0);
        $this->RegisterPropertyInteger("Programmfortschritt", 0);
        $this->RegisterPropertyInteger("Restlaufzeit", 0);
        $this->RegisterPropertyInteger("Verbrauch", 0);
        $this->RegisterPropertyInteger("VerbrauchTag", 0);
        $this->RegisterPropertyFloat("StatusSchriftgroesse", 1);
        $this->RegisterPropertyFloat("ProgrammSchriftgroesse", 1);
        $this->RegisterPropertyFloat("BalkenSchriftgroesse", 1);
        $this->RegisterPropertyInteger("BalkenVerlaufFarbe1", 2674091);
        $this->RegisterPropertyInteger("BalkenVerlaufFarbe2", 2132596);
        //$this->RegisterPropertyInteger("Bild", 0);
        $this->RegisterPropertyFloat("BildBreite", 0);

        $this->RegisterPropertyString('Statusimage0', 'wm_aus');
        $this->RegisterPropertyString('Statusimage1', 'wm_aus');
        $this->RegisterPropertyString('Statusimage2', 'wm_aus');
        $this->RegisterPropertyString('Statusimage3', 'wm_aus');
        $this->RegisterPropertyString('Statusimage4', 'wm_aus');
        $this->RegisterPropertyString('Statusimage5', 'wm_aus');
        $this->RegisterPropertyString('Statusimage6', 'wm_aus');
        $this->RegisterPropertyString('Statusimage7', 'wm_aus');
        $this->RegisterPropertyString('Statusimage8', 'wm_aus');
        $this->RegisterPropertyString('Statusimage9', 'wm_aus');


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


        foreach (['Status', 'Programm', 'Programmfortschritt', 'Restlaufzeit', 'Verbrauch', 'VerbrauchTag'] as $VariableProperty)        {
            $this->RegisterMessage($this->ReadPropertyInteger($VariableProperty), VM_UPDATE);
        }

        // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        foreach (['Status', 'Programm', 'Programmfortschritt', 'Restlaufzeit', 'Verbrauch', 'VerbrauchTag'] as $index => $VariableProperty)
        {
            if ($SenderID === $this->ReadPropertyInteger($VariableProperty))
            {
                

                switch ($Message)
                {
                    case VM_UPDATE:
                        
                        // Teile der HTML-Darstellung den neuen Wert mit. Damit dieser korrekt formatiert ist, holen wir uns den von der Variablen via GetValueFormatted
                        $this->UpdateVisualizationValue(json_encode([$VariableProperty => GetValueFormatted($this->ReadPropertyInteger($VariableProperty))]));
                             
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

        $assets = '<script>';
        $assets .= 'window.assets = {};' . PHP_EOL;
        $assets .= 'window.assets.img_wm_aus = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wm_aus.webp')) . '";' . PHP_EOL;
        $assets .= 'window.assets.img_wm_an = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wm_an.webp')) . '";' . PHP_EOL;
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

        $statusImagesJson = json_encode($statusMapping);
        $images = '<script type="text/javascript">';
        $images .= 'var statusImages = ' . $statusImagesJson . ';';
        $images .= '</script>';


        // Füge statisches HTML aus Datei hinzu
        $module = file_get_contents(__DIR__ . '/module.html');

        // Gebe alles zurück.
        // Wichtig: $initialHandling nach hinten, da die Funktion handleMessage erst im HTML definiert wird
        return $module . $images . $assets . $initialHandling;
    }



    // Generiere eine Nachricht, die alle Elemente in der HTML-Darstellung aktualisiert
    private function GetFullUpdateMessage() {

        $result = [];
    
            $result['status'] = $this->CheckAndGetValueFormatted('Status');
            $result['programm'] = $this->CheckAndGetValueFormatted('Programm');
            $result['programmfortschritt'] = $this->CheckAndGetValueFormatted('Programmfortschritt');
            $result['programmfortschrittvalue'] = IPS_VariableExists($this->ReadPropertyInteger('Programmfortschritt')) ? GetValue($this->ReadPropertyInteger('Programmfortschritt')) : null;
            $result['restlaufzeit'] = $this->CheckAndGetValueFormatted('Restlaufzeit');
            $result['verbrauch'] = $this->CheckAndGetValueFormatted('Verbrauch');
            $result['verbrauchtag'] = $this->CheckAndGetValueFormatted('VerbrauchTag');
            $result['StatusSchriftgroesse'] =  $this->ReadPropertyFloat('StatusSchriftgroesse');
            $result['ProgrammSchriftgroesse'] =  $this->ReadPropertyFloat('ProgrammSchriftgroesse');
            $result['BalkenSchriftgroesse'] =  $this->ReadPropertyFloat('BalkenSchriftgroesse');
            $result['BalkenVerlaufFarbe1'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('BalkenVerlaufFarbe1'));
            $result['BalkenVerlaufFarbe2'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('BalkenVerlaufFarbe2'));
            //$result['Bild'] =  $this->GetColor($this->ReadPropertyInteger('Bild'));
            $result['BildBreite'] =  $this->ReadPropertyFloat('BildBreite');
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
