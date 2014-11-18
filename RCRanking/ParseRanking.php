<?php
namespace RCRanking;

require_once __DIR__.('/ParsingLib/RaceControl2004V14.php');

class ParseRanking
{
  private $pdfText = null;
  private $ranking = null;
    
  public function __construct() {}
  
  function SetPdfText($pdfText)
  {
    $this->pdfText = $pdfText;
    
  }
  
  public function RunParser($version = "Race-Control 2004 V14")
  {
    //TODO auto detect
    switch ($version) {
      case "Race-Control 2004 V14":
        $retval = \RCRanking\ParsingLib\ParseRaceControl2004V14($this->pdfText);
        break;
      default:
        echo "default";
    }
    $this->ranking = $retval;
  }
  
  public function GetJson()
  {
    return json_encode($this->ranking);
  }
  
  public function GetObject()
  {
    return $this->ranking;
  }
  
  
}

?>