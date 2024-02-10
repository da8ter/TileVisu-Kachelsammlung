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
        $this->RegisterPropertyFloat("InfoSchriftgroesse", 1);
        $this->RegisterPropertyFloat("BalkenSchriftgroesse", 1);
        $this->RegisterPropertyInteger("BalkenVerlaufFarbe1", 2674091);
        $this->RegisterPropertyInteger("BalkenVerlaufFarbe2", 2132596);
        $this->RegisterPropertyInteger("Bildauswahl", 0);
        //$this->RegisterPropertyInteger("Bild", 0);
        $this->RegisterPropertyFloat("BildBreite", 20);
        $this->RegisterPropertyString('ProfilAssoziazionen', '[]');

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
                        $this->UpdateVisualizationValue(json_encode([$VariableProperty . 'Value' => GetValue($this->ReadPropertyInteger($VariableProperty))]));
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


        $bildauswahl = $this->ReadPropertyInteger('Bildauswahl');

        if($bildauswahl == '0') {
            $assets = '<script>';
            $assets .= 'window.assets = {};' . PHP_EOL;
            $assets .= 'window.assets.img_wm_aus = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wm_aus.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_wm_an = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/wm_an.webp')) . '";' . PHP_EOL;
            $assets .= '</script>';
        }
        else {
            $assets = '<script>';
            $assets .= 'window.assets = {};' . PHP_EOL;
            $assets .= 'window.assets.img_wm_aus = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/trockner_aus.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_wm_an = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/trockner_an.webp')) . '";' . PHP_EOL;
            $assets .= '</script>';
        }



        // Formulardaten lesen und Statusmapping Array für Bild und Farbe erstellen
        $assoziationsArray = json_decode($this->ReadPropertyString('ProfilAssoziazionen'), true);
        $statusMappingImage = [];
        $statusMappingColor = [];
        foreach ($assoziationsArray as $item) {
            $statusMappingImage[$item['AssoziationValue']] = $item['Bildauswahl'];
                      
            $statusMappingColor[$item['AssoziationValue']] = $item['StatusColor'] === -1 ? "" : sprintf('%06X', $item['StatusColor']);

        }

        $statusImagesJson = json_encode($statusMappingImage);
        $statusColorJson = json_encode($statusMappingColor);
        $images = '<script type="text/javascript">';
        $images .= 'var statusImages = ' . $statusImagesJson . ';';
        $images .= 'var statusColor = ' . $statusColorJson . ';';
        $images .= '</script>';




        // Füge statisches HTML aus Datei hinzu
        $module = file_get_contents(__DIR__ . '/module.html');

        // Gebe alles zurück.
        // Wichtig: $initialHandling nach hinten, da die Funktion handleMessage erst im HTML definiert wird
        return $module . $images . $assets . $initialHandling;
    }



    // Generiere eine Nachricht, die alle Elemente in der HTML-Darstellung aktualisiert
    private function GetFullUpdateMessage() {

       // $profilAssoziationen = $this->ReadPropertyString('ProfilAssoziazionen');

        // Ausgabe des Wertes zur Debugging-Zwecken
       // var_dump($profilAssoziationen);

        $result = [];
    
            //$result['status'] = $this->CheckAndGetValueFormatted('Status');
            $result['status'] = IPS_VariableExists($this->ReadPropertyInteger('Status')) ? $this->CheckAndGetValueFormatted('Status') : null;
            $result['statusvalue'] = IPS_VariableExists($this->ReadPropertyInteger('Status')) ? GetValue($this->ReadPropertyInteger('Status')) : null;
            $result['programm'] = IPS_VariableExists($this->ReadPropertyInteger('Programm')) ? $this->CheckAndGetValueFormatted('Programm') : null;
            $result['programmfortschritt'] = IPS_VariableExists($this->ReadPropertyInteger('Programmfortschritt')) ? $this->CheckAndGetValueFormatted('Programmfortschritt') : null;
            $result['programmfortschrittvalue'] = IPS_VariableExists($this->ReadPropertyInteger('Programmfortschritt')) ? GetValue($this->ReadPropertyInteger('Programmfortschritt')) : null;
            $result['restlaufzeit'] = IPS_VariableExists($this->ReadPropertyInteger('Restlaufzeit')) ? $this->CheckAndGetValueFormatted('Restlaufzeit') : null;
            $result['restlaufzeitvalue'] = IPS_VariableExists($this->ReadPropertyInteger('Restlaufzeit')) ? GetValue($this->ReadPropertyInteger('Restlaufzeit')) : null;
            $result['verbrauch'] = IPS_VariableExists($this->ReadPropertyInteger('Verbrauch')) ? $this->CheckAndGetValueFormatted('Verbrauch') : null;
            $result['verbrauchtag'] = IPS_VariableExists($this->ReadPropertyInteger('VerbrauchTag')) ? $this->CheckAndGetValueFormatted('VerbrauchTag') : null;
            $result['statusschriftgroesse'] =  $this->ReadPropertyFloat('StatusSchriftgroesse');
            $result['programmschriftgroesse'] =  $this->ReadPropertyFloat('ProgrammSchriftgroesse');
            $result['infoschriftgroesse'] =  $this->ReadPropertyFloat('InfoSchriftgroesse');
            $result['balkenschriftgroesse'] =  $this->ReadPropertyFloat('BalkenSchriftgroesse');
            $result['BalkenVerlaufFarbe1'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('BalkenVerlaufFarbe1'));
            $result['BalkenVerlaufFarbe2'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('BalkenVerlaufFarbe2'));
            $result['BildBreite'] =  $this->ReadPropertyFloat('BildBreite');
        return json_encode($result);
    }



    public function UpdateList($StatusID)
    {
        $listData = []; // Hier sammeln Sie die Daten für Ihre Liste
    
        $id = $StatusID;

        // Prüfen, ob die übergebene ID einer existierenden Variable entspricht
        if (IPS_VariableExists($id)) {
            // Auslesen des Variablenprofils
            $variable = IPS_GetVariable($id);
            $profileName = $variable['VariableCustomProfile'] ?: $variable['VariableProfile'];
            
            if ($profileName != '') {
                $profile = IPS_GetVariableProfile($profileName);
    
                // Durchlaufen der Profilassoziationen
                foreach ($profile['Associations'] as $association) {
                    $listData[] = [
                        'AssoziationName' => $association['Name'],
                        'AssoziationValue' => $association['Value'],
                        'Bildauswahl' => 'wm_aus',
                        'StatusColor' => '-1'
                    ];
                }
            }
        } else {
            IPS_LogMessage("TileVisuWashingMaschine", "Die übergebene ID $id entspricht keiner existierenden Variable.");
        }
    
        // Konvertieren Sie Ihre Liste in JSON und aktualisieren Sie das Konfigurationsformular
        $jsonListData = json_encode($listData);
        $this->UpdateFormField('ProfilAssoziazionen', 'values', $jsonListData);
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