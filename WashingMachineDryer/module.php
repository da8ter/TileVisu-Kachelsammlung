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
        $this->RegisterPropertyInteger("KostenTag", 0);
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
        $this->RegisterPropertyInteger("Bild_An", 0);
        $this->RegisterPropertyInteger("Bild_Aus", 0);
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
            $this->ReadPropertyInteger('Status'),
            $this->ReadPropertyInteger('Programm'),
            $this->ReadPropertyInteger('Programmfortschritt'),
            $this->ReadPropertyInteger('Restlaufzeit'),
            $this->ReadPropertyInteger('Verbrauch'),
            $this->ReadPropertyInteger('VerbrauchTag'),
            $this->ReadPropertyInteger('KostenTag'),
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


        foreach (['Status', 'Programm', 'Programmfortschritt', 'Restlaufzeit', 'Verbrauch', 'VerbrauchTag', 'KostenTag'] as $VariableProperty)        {
            $this->RegisterMessage($this->ReadPropertyInteger($VariableProperty), VM_UPDATE);
        }

        // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        foreach (['Status', 'Programm', 'Programmfortschritt', 'Restlaufzeit', 'Verbrauch', 'VerbrauchTag', 'KostenTag'] as $index => $VariableProperty)
        {
            if ($SenderID === $this->ReadPropertyInteger($VariableProperty))
            {
                switch ($Message)
                {
                    case VM_UPDATE:
                        // Teile der HTML-Darstellung den neuen Wert mit. Damit dieser korrekt formatiert ist, holen wir uns den von der Variablen via GetValueFormatted
       
                        // Zusätzliche if-Abfrage für Restlaufzeit
                        if ($VariableProperty === 'Restlaufzeit') {
                            $restlaufzeitValue = GetValue($this->ReadPropertyInteger('Restlaufzeit'));
        
                            // Führe hier die spezifische Logik für Restlaufzeit aus
                            // Zum Beispiel eine Umwandlung von HH:MM:SS in Sekunden
                            if (is_string($restlaufzeitValue) && preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $restlaufzeitValue, $matches)) {
                                $hours = (int)$matches[1];
                                $minutes = (int)$matches[2];
                                $seconds = (int)$matches[3];
                                $restlaufzeitInSeconds = $hours * 3600 + $minutes * 60 + $seconds;
                                
                                // Aktualisiere die Visualisierung oder verarbeite den Wert weiter, falls nötig
                                $this->UpdateVisualizationValue(json_encode(['restlaufzeitvalue' => $restlaufzeitInSeconds]));
                            }
                            else {
                                $this->UpdateVisualizationValue(json_encode(['restlaufzeitvalue' => $restlaufzeitValue]));
                            }
                            
                        }
                        else {
                            $this->UpdateVisualizationValue(json_encode([$VariableProperty => GetValueFormatted($this->ReadPropertyInteger($VariableProperty))]));
                            $this->UpdateVisualizationValue(json_encode([$VariableProperty . 'Value' => GetValue($this->ReadPropertyInteger($VariableProperty))]));
                        }
        
                       
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
        elseif($bildauswahl == '1') {
            $assets = '<script>';
            $assets .= 'window.assets = {};' . PHP_EOL;
            $assets .= 'window.assets.img_wm_aus = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/trockner_aus.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_wm_an = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/trockner_an.webp')) . '";' . PHP_EOL;
            $assets .= '</script>';
        }
        else {

        // Prüfe vorweg, ob ein Bild ausgewählt wurde
        $imageID_Bild_An = $this->ReadPropertyInteger('Bild_An');
        if (IPS_MediaExists($imageID_Bild_An)) {
            $image = IPS_GetMedia($imageID_Bild_An);
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
                    $imageContent .= IPS_GetMediaContent($imageID_Bild_An);
                }

            }
        }
        else {
            $imageContent = 'data:image/png;base64,';

            $imageContent .= base64_encode(file_get_contents(__DIR__ . '/../imgs/transparent.webp'));

            
        } 

                // Prüfe vorweg, ob ein Bild ausgewählt wurde
                $imageID_Bild_Aus = $this->ReadPropertyInteger('Bild_Aus');
                if (IPS_MediaExists($imageID_Bild_Aus)) {
                    $image2 = IPS_GetMedia($imageID_Bild_Aus);
                    if ($image2['MediaType'] === MEDIATYPE_IMAGE) {
                        $imageFile2 = explode('.', $image2['MediaFile']);
                        $imageContent2 = '';
                        // Falls ja, ermittle den Anfang der src basierend auf dem Dateitypen
                        switch (end($imageFile2)) {
                            case 'bmp':
                                $imageContent2 = 'data:image/bmp;base64,';
                                break;
        
                            case 'jpg':
                            case 'jpeg':
                                $imageContent2 = 'data:image/jpeg;base64,';
                                break;
        
                            case 'gif':
                                $imageContent2 = 'data:image/gif;base64,';
                                break;
        
                            case 'png':
                                $imageContent2 = 'data:image/png;base64,';
                                break;
        
                            case 'ico':
                                $imageContent2 = 'data:image/x-icon;base64,';
                                break;
                        }
        
                        // Nur fortfahren, falls Inhalt gesetzt wurde. Ansonsten ist das Bild kein unterstützter Dateityp
                        if ($imageContent2) {
                            // Hänge base64-codierten Inhalt des Bildes an
                            $imageContent2 .= IPS_GetMediaContent($imageID_Bild_Aus);
                        }
        
                    }
                }
                else {
                    $imageContent2 = 'data:image/png;base64,';

                    $imageContent2 .= base64_encode(file_get_contents(__DIR__ . '/../imgs/transparent.webp'));

                    
                }  

            $assets = '<script>';
            $assets .= 'window.assets = {};' . PHP_EOL;
            $assets .= 'window.assets.img_wm_aus = "' . $imageContent2 . '";' . PHP_EOL;
            $assets .= 'window.assets.img_wm_an = "' . $imageContent . '";' . PHP_EOL;
            $assets .= '</script>';
        }


        // Formulardaten lesen und Statusmapping Array für Bild und Farbe erstellen
        $assoziationsArray = json_decode($this->ReadPropertyString('ProfilAssoziazionen'), true);
        $statusMappingImage = [];
        $statusMappingColor = [];
        $statusMappingBalken = [];
        foreach ($assoziationsArray as $item) {
            $statusMappingImage[$item['AssoziationValue']] = $item['Bildauswahl'];
                      
            $statusMappingColor[$item['AssoziationValue']] = $item['StatusColor'] === -1 ? "" : sprintf('%06X', $item['StatusColor']);

            $statusMappingBalken[$item['AssoziationValue']] = $item['StatusBalken'];

        }

        $statusImagesJson = json_encode($statusMappingImage);
        $statusColorJson = json_encode($statusMappingColor);
        $statusStatusBalkenJson = json_encode($statusMappingBalken);
        $images = '<script type="text/javascript">';
        $images .= 'var statusImages = ' . $statusImagesJson . ';';
        $images .= 'var statusColor = ' . $statusColorJson . ';';
        $images .= 'var statusBalken = ' . $statusStatusBalkenJson . ';';
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
            //$result['restlaufzeitvalue'] = IPS_VariableExists($this->ReadPropertyInteger('Restlaufzeit')) ? GetValue($this->ReadPropertyInteger('Restlaufzeit')) : null;
            


            // Wert der Restlaufzeit abrufen
            $restlaufzeitValue = IPS_VariableExists($this->ReadPropertyInteger('Restlaufzeit')) ? GetValue($this->ReadPropertyInteger('Restlaufzeit')) : null;

            // Überprüfen, ob der Wert im Format HH:MM:SS vorliegt
            if (is_string($restlaufzeitValue) && preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $restlaufzeitValue, $matches)) {
                // Wert ist im Format HH:MM:SS, also konvertieren in Sekunden
                $hours = (int)$matches[1];
                $minutes = (int)$matches[2];
                $seconds = (int)$matches[3];
                $restlaufzeitValueInSeconds = $hours * 3600 + $minutes * 60 + $seconds;
            } else {
                // Wert ist bereits in Sekunden oder nicht im erwarteten Format
                $restlaufzeitValueInSeconds = (int)$restlaufzeitValue;
            }

            // Ergebnis setzen
            $result['restlaufzeitvalue'] = $restlaufzeitValueInSeconds;


            
            $result['verbrauch'] = IPS_VariableExists($this->ReadPropertyInteger('Verbrauch')) ? $this->CheckAndGetValueFormatted('Verbrauch') : null;
            $result['verbrauchtag'] = IPS_VariableExists($this->ReadPropertyInteger('VerbrauchTag')) ? $this->CheckAndGetValueFormatted('VerbrauchTag') : null;
            $result['kostentag'] = IPS_VariableExists($this->ReadPropertyInteger('KostenTag')) ? $this->CheckAndGetValueFormatted('KostenTag') : null;
            $result['statusschriftgroesse'] =  $this->ReadPropertyFloat('StatusSchriftgroesse');
            $result['programmschriftgroesse'] =  $this->ReadPropertyFloat('ProgrammSchriftgroesse');
            $result['infoschriftgroesse'] =  $this->ReadPropertyFloat('InfoSchriftgroesse');
            $result['balkenschriftgroesse'] =  $this->ReadPropertyFloat('BalkenSchriftgroesse');
            $result['BalkenVerlaufFarbe1'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('BalkenVerlaufFarbe1'));
            $result['BalkenVerlaufFarbe2'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('BalkenVerlaufFarbe2'));
            $result['BildBreite'] =  $this->ReadPropertyFloat('BildBreite');
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
