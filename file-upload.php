<?php 
session_start();
include 'class/dao.php';

$statusMsg = '';
$db=new DAO();
// File upload path
$targetDir = "uploads/";
$fileName = basename($_FILES["fileUpload"]["name"]);
$targetFilePath = $targetDir . $fileName;
$fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
$file_contents= file_get_contents ($_FILES['fileUpload']['tmp_name']);
$file_hex="";

$handle = @fopen($_FILES['fileUpload']['tmp_name'], "rb"); 
if ($handle) {        
    $file_base64= base64_encode(fread ($handle , filesize($_FILES['fileUpload']['tmp_name']) ));  
    fclose($handle);  
} 

if(isset($_POST["submit"]) && !empty($_FILES["fileUpload"]["name"])){
    // Allow certain file formats
    $allowTypes = array('jpg','png','jpeg','gif','pdf');
    if(in_array($fileType, $allowTypes)){
        // Upload file to server
        if(move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $targetFilePath)){
            // Insert image file name into database
            
            $insert = $db->insertdocuments($fileName,$file_base64,$_SESSION["id"]);
            if($insert){
                unlink("uploads/".$fileName); // delete le fichier
                // $statusMsg = "The file ".$fileName." has been uploaded successfully.";
                echo '<script>window.alert("Le fichier '.$fileName.' a été envoyé avec succés.");
                window.location.replace("patient_home.php");</script>';

            }else{
                echo '<script>window.alert("Envoi de fichier échouer, ressayer dans quelques instants");
                window.location.replace("patient_home.php");</script>';
            } 
        }else{
            echo '<script>window.alert("Une erreur est survenue lors de l'.'envois du fichier");
            window.location.replace("patient_home.php");</script>';
        }
    }else{
        echo '<script>window.alert("Seulements des fichiers JPG, JPEG, PNG et PDF peuve t'.'être envoiller");
        window.location.replace("patient_home.php");</script>';
    }
}else{
    echo '<script>window.alert("Veuillez choisir un fichier à envoyer");
    window.location.replace("patient_home.php");</script>';
}

// Display status message
echo $statusMsg;

?>