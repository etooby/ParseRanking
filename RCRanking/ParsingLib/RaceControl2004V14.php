<?php
namespace RCRanking\ParsingLib;

  /**
   * Finde alle Klassen und Fahrer in einer PDF
   *
   * * Finde alle Klassen anhand der "Klassen:"  Zeichenkette
   * * Finde alle Fahrer innerhalb zweier Klassen, diese werden anhand von
   *    einerm Regulären ausdruck "Nachname, Vorname" ermittelt. 
   * 
   * Klasse : 'Klasse'
   * Fahrer : '/\d{1,2}:\d{2},\d{3}/'
   *
   * Beispiel PDF Output
   * ...
   * Klasse: Tourenwagen Sport
   * best. Vorlauf Ges1. A-Finale 2. A-Finale 3. A-Finale
   * Reg# Flagge OrtsclubPl. Name Rn. Zeit Pos Rn. Zeit Pkt Rn. Zeit Pkt Rn. Zeit Pkt Pkt
   * 5036 sac RCC Dresden 34 5:05,984Kroh, Sebastian 11 49 7:06,325 1 49 7:08,557 1 48 7:08,766 2 2
   * 5207 msc MSC Höckendorf e.V. 34 5:08,787Dietrich, Tobias 22 48 7:07,026 2 48 7:02,321 2 48 7:07,934 1 3
   * 5063 sac Leipzig 33 5:00,841Wernicke, Tobias 33 47 7:08,365 4 47 7:06,226 3 47 7:00,685 3 6
   * 5203 sac RCC Dresden 33 5:09,369Kroh, Oliver 44 47 7:06,791 3 46 7:01,355 4 47 7:12,822 4 7
   * Klasse: M- Cassis
   * ...
   *
   **/
  function ParseRaceControl2004V14($pdfText)
  {
    $delimiterKlasse   = 'Klasse';
    // pdf text in array zerlegen 
    $arrayKlassenTeile = explode($delimiterKlasse, $pdfText);
    // index 0 entfernen -> sinnvoller teil fängt erst nach dem erste "klassen:" an
    array_splice($arrayKlassenTeile, 0, 1);
    
    // alle klassen filtern -> wichtig bei einer klasse, welche über mehrere seiten geht
    
    $klassen_string = null;
    
    foreach ($arrayKlassenTeile as $klasseKey => $klasseValue) {
      // erste zeile verwenden, da in dieser die klassenbezeichnung steht
      $zeilen           = explode("\n", $klasseValue);
      // klasse an array anfügen und den anfang ": " entfernen
      $klassen_string[] = $zeilen[0];
    }
    // doppelte einträge filter, zb bei seiten umbrüchen
    $klassen_string = array_unique($klassen_string);
    
    // rangliste definieren und initialisieren
    $rangliste = null;
    
    // alle klassen teile filtern
    foreach ($arrayKlassenTeile as $klasseKey => $klasseValue) {
      
      // zeilenweise nach fahrern filtern
      $zeilenKlasse = explode("\n", $klasseValue);
      
      // index für die jeweilige klasse suchen
      $ranglisteKey                     = array_search($zeilenKlasse[0], $klassen_string);
      // klasse setzen (TopStock)
      $rangliste[$ranglisteKey]->klasse = substr($zeilenKlasse[0], 2);
      
      
      //Bsp.: "5008 msc MSC Höckendorf e.V. 28 10:29,969 0Dietrich, Tobias 11 14 5:03,258 1 14 5:09,344 1 11 2"
      //$zeichenkette = $zeilenKlasse[3];
      //$suchmuster = '/\d{1,2}:\d{2},\d{3}/';
      //preg_match_all($suchmuster, $zeichenkette, $treffer);
      
      $j      = 0;
      $fahrer = null;
      foreach ($zeilenKlasse as $zeilenKey => $zeilenValue) {
        
        // regulärer ausdruck für "Nachname, Vorname"
        $suchmusterName = '/\D{1,}, \D{1,}/';
        // regulärer ausdruck für "05:03,258"
        $suchmusterZeit = '/\d{1,2}:\d{2},\d{3}/';
        
        // alle namen in der klasse finden
        preg_match($suchmusterName, $zeilenValue, $trefferName);
        // zeit ist wichtig, da auch Rennleiten und Co gefunden wurden .. somit nur fahrer mit zeit
        preg_match($suchmusterZeit, $zeilenValue, $trefferZeit);
        
        // alle fahrer finden
        if ($trefferName && $trefferZeit) {
          //echo "<li>" . $trefferName[0]. "</li>";
          $fahrer[$j] = $trefferName[0];
          $j++;
        }
      }
      
      // bestehende Fahrer in der Klasse auslesen
      @$driver = $rangliste[$ranglisteKey]->fahrer;
      // bestehende Fahrer und neue Fahrer zusammenführen
      if ($driver) {
        $result = array_merge($driver, $fahrer);
      } else {
        $result = $fahrer;
      }
      // Alle Fahrer der jewiligen Klasse dieser zuordnen
      $rangliste[$ranglisteKey]->fahrer = $result;
    }
    
    // Rangliste zurückgeben
    return $rangliste;
  }

?>