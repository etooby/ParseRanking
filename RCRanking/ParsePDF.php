<?php
namespace RCRanking;

class ParsePDF
{
  
  private $filenamePDF = null;
  private $textPDF = null;
  private $pdfbox = null;
  
  function __construct()
  {
    $this->pdfbox = "./lib/pdfbox-app-1.8.7.jar";
  }
  
  public function UploadFile($filenameSrc)
  {
    $filenameDst = null;
    
    if (isset($filenameSrc)) {
      // create md5 has as filename 
      $filenameDst = 'upload/' . md5_file($filenameSrc) . '.pdf';
      // move file to upload folder
      move_uploaded_file($filenameSrc, $filenameDst);
    }
    
    return $this->filenamePDF = $filenameDst;
  }
  
  public function ExtractText()
  {
    if (file_exists($this->filenamePDF) && file_exists($this->pdfbox)) {
      
      // create tmp file ... -console parameter do not work with uft8 ???
      $tmpFilename = "/tmp/pdfoutput.txt";
      shell_exec("java -jar " . $this->pdfbox . " ExtractText -encoding UTF-8 " . $this->filenamePDF . " " . $tmpFilename);
      
      // read text from tmp file
      if (file_exists($tmpFilename)) {
        $retval = null;
        $file   = fopen($tmpFilename, "r");
        while (!feof($file) && $file) {
          $retval .= fgets($file, 1024);
          
        }
        fclose($file);
        
        $this->textPDF = $retval;
      }
      // delete tmp file
      unlink($tmpFilename);
    }
    
    return $this->textPDF;
  }
  
  public function GetText()
  {
    return $this->textPDF;
  }
}

?>