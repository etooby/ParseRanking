
<?php
header("Content-Type: text/html; charset=utf-8");

require_once __DIR__.('/RCRanking/ParsePDF.php');
require_once __DIR__.('/RCRanking/ParseRanking.php');

?>

<form action="." method="post" enctype="multipart/form-data">
  <p>Ranglisten PDF auswählen</p>
  <input name="file" type="file" accept="application/pdf">

  <button type="submit">Parsen</button>
</form>


<?php

  

if (isset($_FILES['file'])){

  $pdf = new \RCRanking\ParsePDF();

  $pdf->UploadFile($_FILES["file"]["tmp_name"]);
  $pdf->ExtractText();

  $text = $pdf->GetText();

  if (!empty($text)){

    $Ranking = new \RCRanking\ParseRanking();
    $Ranking->SetPdfText($text);
    $Ranking->RunParser();

    $json      = $Ranking->GetJson();
    $rangliste = $Ranking->GetObject();

    // ausgabe
    foreach ($rangliste as $ranglisteKey => $ranglisteValue) {
      echo "<p>Klasse :".$ranglisteValue->klasse. "</p>";
      echo "<ol>";
      foreach ($ranglisteValue->fahrer as $fahrerKey => $fahrerValue) {
        echo "<li>".$fahrerValue. "</li>";


      }
      echo "</ol>";
    }
  }

}

?>