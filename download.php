<?php
    $id=$_GET['id'];
    
    require_once("class/dao.php");
    $dao= new DAO();
    if ($dao->getError()) {
        print "erreur: ".$dao->getError();
    }

    $infoUpload=$dao->getInfoDocument(" titre, file_blob as file, extension",$id);
    

    $titre = str_replace(' ', '_', $infoUpload[0]["titre"]);

    // echo '<script>window.alert("Le fichier '.$titre.' a été envoyé avec succés.");</script>';
    
    $base64 = $infoUpload[0]["file"];
    $base64 = str_replace('data:image/'.$infoUpload[0]["extension"].';base64,', '', $base64);
    $base64 = str_replace(' ', '+', $base64);
    $data = base64_decode($base64);   

    header('Content-Description: File Transfer');
    header('Content-Type: image/'.$infoUpload[0]["extension"]); 
    header('Content-Disposition: attachment; filename="'.$titre.'"');
    
    header('Connection: Keep-Alive');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . strlen($data));
    flush(); // Flush system output buffer
    echo $data;
    die();

?>