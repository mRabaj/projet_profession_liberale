<?php
require_once("dao.php");

$find=false;
if($_GET){
    foreach($dao->getRdv2() as $item){
        $dateTime=strtotime($item['start']);
        $date_heure_debut=date('Y-m-d H:i',$dateTime);
        if($_GET['date_heure_debut']==$date_heure_debut){
          
                 $find=true;
                 print "}vous devez chisoir une autre date ";
          
        }
    }
}
    $messages=array();
if($_GET&&!$find){
    $idPatient=$_GET['idPatient'];
    $idPraticien=$_GET['idPraticien'];
    $date_heure_debut=$_GET['date_heure_debut'];
    $date_start=strtotime($date_heure_debut);
    $today=date("Y-m-d H:i");
    $today_begin=strtotime($today);
   
    if ($date_start>=$today_begin){

    print json_encode($dao->insertAppoitment($idPatient,$idPraticien,$date_heure_debut));
     print "Rendez-vous a bien été enregistrer";
    }else{
        print "}vous devez chisoir une date futur ";
    }

}

?>