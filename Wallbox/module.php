<?php
class TileVisuWallbox extends IPSModule
{
    public function Create()
    {
        // Nie diese Zeile löschen!
        parent::Create();


        // Drei Eigenschaften für die dargestellten Zähler
        $this->RegisterPropertyInteger("Status", 0);
        $this->RegisterPropertyInteger("Ladeleistung", 0);
        $this->RegisterPropertyInteger("SOC", 0);
        $this->RegisterPropertyInteger("ZielSOC", 0);
        $this->RegisterPropertyInteger("SOCschalter", 0);
        $this->RegisterPropertyInteger("ZielSOCschalter", 0);
        $this->RegisterPropertyInteger("Verbrauchgesamt", 0);
        $this->RegisterPropertyInteger("VerbrauchTag", 0);
        $this->RegisterPropertyInteger("KostenTag", 0);
        $this->RegisterPropertyInteger("KostenGesamt", 0);
        $this->RegisterPropertyInteger("Fehler", 0);
        $this->RegisterPropertyInteger("Phasen", 0);
        $this->RegisterPropertyInteger("MaxLadeleistung", 0);
        $this->RegisterPropertyInteger("Kabel", 0);
        $this->RegisterPropertyInteger("Zugangskontrolle", 0);
        $this->RegisterPropertyInteger("Verriegelung", 0);
        $this->RegisterPropertyFloat("StatusSchriftgroesse", 1);
        $this->RegisterPropertyFloat("ProgrammSchriftgroesse", 1);
        $this->RegisterPropertyFloat("InfoSchriftgroesse", 1);
        $this->RegisterPropertyFloat("BalkenSchriftgroesse", 1);
        $this->RegisterPropertyInteger("BalkenVerlaufFarbe1", 2674091);
        $this->RegisterPropertyInteger("BalkenVerlaufFarbe2", 2132596);
        $this->RegisterPropertyInteger("BalkenVerlaufSOCFarbe1", 7257660);
        $this->RegisterPropertyInteger("BalkenVerlaufSOCFarbe2", 5281320);
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
            $this->ReadPropertyInteger('Ladeleistung'),
            $this->ReadPropertyInteger('SOC'),
            $this->ReadPropertyInteger("SOCschalter"),
            $this->ReadPropertyInteger('ZielSOC'),
            $this->ReadPropertyInteger("ZielSOCschalter"),
            $this->ReadPropertyInteger("Verbrauchgesamt"),
            $this->ReadPropertyInteger('bgImage'),
            $this->ReadPropertyInteger('Verbrauchgesamt'),
            $this->ReadPropertyInteger('VerbrauchTag'),
            $this->ReadPropertyInteger('KostenTag'),
            $this->ReadPropertyInteger('KostenGesamt'),
            $this->ReadPropertyInteger('Fehler'),
            $this->ReadPropertyInteger('Phasen'),
            $this->ReadPropertyInteger('MaxLadeleistung'),
            $this->ReadPropertyInteger('Kabel'),
            $this->ReadPropertyInteger('Zugangskontrolle'),
            $this->ReadPropertyInteger('Verriegelung')
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


        foreach (['Status', 'Ladeleistung', 'SOC', 'ZielSOC', 'SOCschalter', 'ZielSOCschalter', 'Verbrauchgesamt', 'VerbrauchTag', 'KostenTag', 'KostenGesamt', 'Fehler', 'Phasen', 'MaxLadeleistung', 'Kabel', 'Zugangskontrolle', 'Verriegelung'] as $VariableProperty)        {
            $this->RegisterMessage($this->ReadPropertyInteger($VariableProperty), VM_UPDATE);
        }

        // Schicke eine komplette Update-Nachricht an die Darstellung, da sich ja Parameter geändert haben können
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        foreach (['Status', 'Ladeleistung', 'SOC', 'ZielSOC', 'SOCschalter', 'ZielSOCschalter', 'Verbrauchgesamt', 'VerbrauchTag', 'KostenTag', 'KostenGesamt', 'Fehler', 'Phasen', 'MaxLadeleistung', 'Kabel', 'Zugangskontrolle', 'Verriegelung'] as $index => $VariableProperty)
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
            $assets .= 'window.assets.img_goe_aus = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/go_e.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_goe_an = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/go_e_kabel.webp')) . '";' . PHP_EOL;
            $assets .= '</script>';
        }
        elseif($bildauswahl == '1') {
            $assets = '<script>';
            $assets .= 'window.assets = {};' . PHP_EOL;
            $assets .= 'window.assets.img_goe_aus = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/go_e_gemini.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_goe_an = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/go_e_gemini_kabel.webp')) . '";' . PHP_EOL;
            $assets .= '</script>';
        }
        elseif($bildauswahl == '2') {
            $assets = '<script>';
            $assets .= 'window.assets = {};' . PHP_EOL;
            $assets .= 'window.assets.img_goe_aus = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/legacy.webp')) . '";' . PHP_EOL;
            $assets .= 'window.assets.img_goe_an = "data:image/webp;base64,' . base64_encode(file_get_contents(__DIR__ . '/assets/legacy_kabel.webp')) . '";' . PHP_EOL;
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
            $assets .= 'window.assets.img_goe_aus = "' . $imageContent2 . '";' . PHP_EOL;
            $assets .= 'window.assets.img_goe_an = "' . $imageContent . '";' . PHP_EOL;
            $assets .= '</script>';
        }


        // Formulardaten lesen und Statusmapping Array für Bild und Farbe erstellen
        $assoziationsArray = json_decode($this->ReadPropertyString('ProfilAssoziazionen'), true);
        $statusMappingImage = [];
        $statusMappingColor = [];
        $statusMappingAnimation = [];
        foreach ($assoziationsArray as $item) {
            $statusMappingImage[$item['AssoziationValue']] = $item['Bildauswahl'];
            $statusMappingAnimation[$item['AssoziationValue']] = $item['Animation'];
                      
            $statusMappingColor[$item['AssoziationValue']] = $item['StatusColor'] === -1 ? "" : sprintf('%06X', $item['StatusColor']);


        }

        $statusImagesJson = json_encode($statusMappingImage);
        $statusColorJson = json_encode($statusMappingColor);
        $statusAnimationJson = json_encode($statusMappingAnimation);
        $images = '<script type="text/javascript">';
        $images .= 'var statusImages = ' . $statusImagesJson . ';';
        $images .= 'var statusColor = ' . $statusColorJson . ';';
        $images .= 'var statusAnimation = ' . $statusAnimationJson . ';';
        $images .= 'var phasecount = ' . (IPS_VariableExists($this->ReadPropertyInteger('Phasen')) ? GetValue($this->ReadPropertyInteger('Phasen')) : 'null') . ';';
        $images .= 'var wallboxstatus = ' . GetValue($this->ReadPropertyInteger('Status')) . ';';
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
            $result['ladeleistung'] = IPS_VariableExists($this->ReadPropertyInteger('Ladeleistung')) ? $this->CheckAndGetValueFormatted('Ladeleistung') : null;
            $result['ladeleistungvalue'] = IPS_VariableExists($this->ReadPropertyInteger('Ladeleistung')) ? GetValue($this->ReadPropertyInteger('Ladeleistung')) : null;
            $result['maxladeleistungvalue'] = IPS_VariableExists($this->ReadPropertyInteger('MaxLadeleistung')) ? GetValue($this->ReadPropertyInteger('MaxLadeleistung')) : null;
            $result['SOC'] = IPS_VariableExists($this->ReadPropertyInteger('SOC')) ? $this->CheckAndGetValueFormatted('SOC') : null;
            $result['SOCvalue'] = IPS_VariableExists($this->ReadPropertyInteger('SOC')) ? GetValue($this->ReadPropertyInteger('SOC')) : null;
            $result['SOCschaltervalue'] = IPS_VariableExists($this->ReadPropertyInteger('SOCschalter')) ? GetValue($this->ReadPropertyInteger('SOCschalter')) : null;
            $result['ZielSOC'] = IPS_VariableExists($this->ReadPropertyInteger('ZielSOC')) ? $this->CheckAndGetValueFormatted('ZielSOC') : null;
            $result['ZielSOCschaltervalue'] = IPS_VariableExists($this->ReadPropertyInteger('ZielSOCschalter')) ? GetValue($this->ReadPropertyInteger('ZielSOCschalter')) : null;
            $result['ZielSOCvalue'] = IPS_VariableExists($this->ReadPropertyInteger('ZielSOC')) ? GetValue($this->ReadPropertyInteger('ZielSOC')) : null;
            $result['Verbrauchgesamt'] = IPS_VariableExists($this->ReadPropertyInteger('Verbrauchgesamt')) ? $this->CheckAndGetValueFormatted('Verbrauchgesamt') : null;
            $result['verbrauchtag'] = IPS_VariableExists($this->ReadPropertyInteger('VerbrauchTag')) ? $this->CheckAndGetValueFormatted('VerbrauchTag') : null;
            $result['kostentag'] = IPS_VariableExists($this->ReadPropertyInteger('KostenTag')) ? $this->CheckAndGetValueFormatted('KostenTag') : null;
            $result['kostengesamt'] = IPS_VariableExists($this->ReadPropertyInteger('KostenGesamt')) ? $this->CheckAndGetValueFormatted('KostenGesamt') : null;
            $result['Fehler'] = IPS_VariableExists($this->ReadPropertyInteger('Fehler')) ? $this->CheckAndGetValueFormatted('Fehler') : null;
            $result['Phasen'] = IPS_VariableExists($this->ReadPropertyInteger('Phasen')) ? GetValue($this->ReadPropertyInteger('Phasen')) : null;            
            $result['MaxLadeleistung'] = IPS_VariableExists($this->ReadPropertyInteger('MaxLadeleistung')) ? $this->CheckAndGetValueFormatted('MaxLadeleistung') : null;
            $result['Kabel'] = IPS_VariableExists($this->ReadPropertyInteger('Kabel')) ? $this->CheckAndGetValueFormatted('Kabel') : null;
            $result['Zugangskontrolle'] = IPS_VariableExists($this->ReadPropertyInteger('Zugangskontrolle')) ? $this->CheckAndGetValueFormatted('Zugangskontrolle') : null;
            $result['Verriegelung'] = IPS_VariableExists($this->ReadPropertyInteger('Verriegelung')) ? $this->CheckAndGetValueFormatted('Verriegelung') : null;
           
            
            
            
            $result['statusschriftgroesse'] =  $this->ReadPropertyFloat('StatusSchriftgroesse');
            $result['programmschriftgroesse'] =  $this->ReadPropertyFloat('ProgrammSchriftgroesse');
            $result['infoschriftgroesse'] =  $this->ReadPropertyFloat('InfoSchriftgroesse');
            $result['balkenschriftgroesse'] =  $this->ReadPropertyFloat('BalkenSchriftgroesse');
            $result['BalkenVerlaufFarbe1'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('BalkenVerlaufFarbe1'));
            $result['BalkenVerlaufFarbe2'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('BalkenVerlaufFarbe2'));
            $result['BalkenVerlaufSOCFarbe1'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('BalkenVerlaufSOCFarbe1'));
            $result['BalkenVerlaufSOCFarbe2'] =  '#' . sprintf('%06X', $this->ReadPropertyInteger('BalkenVerlaufSOCFarbe2'));
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
                        'Bildauswahl' => 'goe_aus',
                        'StatusColor' => '-1'
                    ];
                }
            }
        } else {
            IPS_LogMessage("TileVisuWallbox", "Die übergebene ID $id entspricht keiner existierenden Variable.");
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
