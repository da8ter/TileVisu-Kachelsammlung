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
        $this->RegisterPropertyInteger("BalkenVerlaufFarbe2", 0x208a74);
        //$this->RegisterPropertyInteger("Bild", 0);
        $this->RegisterPropertyFloat("BildBreite", 0);


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

        // Füge statisches HTML aus Datei hinzu
        $module = file_get_contents(__DIR__ . '/module.html');

        // Gebe alles zurück.
        // Wichtig: $initialHandling nach hinten, da die Funktion handleMessage erst im HTML definiert wird
        return $module . $initialHandling;
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
