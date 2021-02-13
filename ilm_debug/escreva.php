<?php
// Para depuracao: escrever no arquivo '/var/www/html/saw/mod/iassign/'



// Write each personal student file in './<turma>/<N>_<name>.txt'
// Param: $filetype1 in "student", "log", "sendemail"
// Return 1 in case of success; -1 in case do not overwrite
function writeContent ($filetype1, $pathbase, $outputFile, $msgToRegister) {
  global $GROUP, $WRITEMSG, $OVERWRITE;

  $pathbase = "/var/www/html/saw/mod/iassign/ilm_debug/"; // "/mod/iassign/ilm_debug/";
  $outputFile = $pathbase . $outputFile;

  if (!is_writable($pathbase)) { // TRUE se arquivo existe e pode ser escrito
     // $file_debug .= "Error: '" . $completfilepath . "' could not be registered! Perhaps the directory or the file has permission problem?<br/>\n";
     //D echo  "$outputFile, $filetype1<br/>";
     print "Erro! Problema de acesso ao servidor! Por favor, avise ao administrador (<tt>$pathbase</tt> nao acessivel para escrita).<br/>"; //  . $file_debug . "
     exit(0);
     }

   // $result = writeContent("file", $pathbase, $filename, $msgToRegister);
   // print "escreva.php: result=$result<br/>";

   // write personal email file
   // To write: verify if the file does not exists or have permission to overwrite
   if (is_file($outputFile)) {  // already exist this file
     // $outputFile .= $outputFile . '_' . date('Y_m_d_h_m');
     } // if (is_file($outputFile))

   if (1==1) { // write/overwrite the file
     $fpointer = fopen($outputFile, "w"); // write - if executed, it clear the previou content at this file
     if (!$fpointer) {
        $file_debug .= "Erro: nao foi possivel abrir o roteiro ($outputFile)!<br/>\n";
        // it was not possible to open the file '$completfilepath" . $file_name . "'!<br/>\n";
        print "<br/>" . $file_debug . "<br/>\n";
        //D echo "writeContent: $filetype1, outputFile=$outputFile<br/>\n";
        return 0;
        }
     fwrite($fpointer, $msgToRegister . "\n");
     // echo " - outputFile=$outputFile : WRITEMSG=$WRITEMSG, OVERWRITE=$OVERWRITE, gerado com sucesso!<br/>\n";
     fclose($fpointer);
     }
   else {
     // print "Nao gera os arquivos personalizados para email aos alunos ('$outputFile')<br/>\n"; // admin/<turma>/<N>_<name>.txt
     }

  return 1;
  } // function writeContent($outputFile, $msgToRegister)


?>