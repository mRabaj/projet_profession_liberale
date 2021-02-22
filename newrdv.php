<?php
 session_start();
require_once("class/dao.php");

$infoPatient=$dao->getInfoPatient("patient.idPatient AS id,patient.sexe, patient.nom, prenom, date_naissance, telephone_portable, telephone_fixe, patient.email, adresse1, adresse2, patient.code_postal, patient.ville, pays, numero_securite_sociale, mutuelle.nom as mutuelle",$_SESSION['id']);

$today=date("Y-m-d H:i");
$date=strtotime($today);

if(isset($_POST['ajoutrdv'])){

              $idPatient=$_POST['patients'];
              $idPraticien=$_SESSION['id'];
              $date_heure=strtotime($_POST['date-of-appointment']);
              $date_heure_debut=date('Y-m-d H:i:s',$date_heure);
              $time=strtotime($date_heure_debut);
                  if($_POST['ajoutminutes']==20){
                      $time =strtotime($date_heure_debut." +20 minute");
                      $date_heure_fin=date('Y-m-d H:i:s',$time);
                  } elseif($_POST['ajoutminutes']==30){
                      $time =strtotime($date_heure_debut." +30 minute");
                      $date_heure_fin=date('Y-m-d H:i:s',$time);
                  }elseif($_POST['ajoutminutes']==40){
                      $time =strtotime($date_heure_debut." +40 minute");
                      $date_heure_fin=date('Y-m-d H:i:s',$time);
                  }
                    $dao->insertAppoitment($idPatient,$idPraticien,$date_heure_debut,$date_heure_fin);
                                          if ($dao->getError()) {
                                              print $dao->getError();
                                          }else{                                    
                 print '<script>window.alert("Le rendez-vous pour a été enregistrer avec succés.");</script>';     
                                  }
  }
  if (!isset($_SESSION["id"]))  {
    header("Location:index.php");
  } else {
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

    <title>New RDV</title>
    <script type=text/javascript>

    </script>
  </head>
  <body>
            <div class="container" >
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
          <a class="navbar-brand" href="index.php">Accueil</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">   
              <li class="nav-item">
                <a class="nav-link" href="index.php?disconnect=true"  tabindex="-1" >Déconnexion </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
                <br>
                <br>
                <div class="card">
                <div class="card-body">               
        <form class="row g-3 needs-validation" method="post" novalidate >

            
              <div class="col-md-7">
              <label for="validationCustom04" class="form-label">Patients</label>
                          <select name="patients" id="idpatient" class="form-control " required>
                              <?php 
                            for ($i=0;$i<count($infoPatient);$i++) { 
                                
                              if ($infoPatient[$i]["sexe"]=="M") {
                                  $nominationPatient="M. ";
                              }else{
                                  $nominationPatient="Mme. ";
                              }
                              echo "<option value=".$infoPatient[$i]["id"].">".$nominationPatient.$infoPatient[$i]["nom"]." ".$infoPatient[$i]["prenom"]."</option>";

                              } 
                              ?>
                          </select>
              </div>
              <div class="col-md-7 position-relative">
                  <label for="validationTooltip01" class="form-label">Date de rendez-vous</label>
                  <input type="datetime-local" class="form-control" name="date-of-appointment" id="validationTooltip01" value="" required>
              
              </div>
                      <!-- <div class="col-md-7 position-relative">
                          <label for="validationTooltip02" class="form-label">Heure de rendez-vous</label>
                  <input type="time" class="form-control" name="hour-of-appointment" id="validationTooltip02" value="" required>        </div>
                      </div> -->
                  <div class="col-7">
                  <label for="addMinutes">* Nombre de minute souhaiter :</label>
                  <select class="form-select" name="ajoutminutes" id="addMinutes">
                    <option value="20">+ 20mins </option>
                    <option value="30">+ 30mins</option>
                    <option value="40">+ 40mins</option>                  
                  </select>
                  </div>
                  <div class="col-6">
                  <button class="btn btn-primary" name="ajoutrdv" type="submit">Enregistrer</button>
              </div>
              <div class="col-6">
              <a  href="praticien_home.php"  class="btn btn-outline-success"> retour </a>
              </div>
          </form>
          </div>
       </div>
          <script type=text/javascript>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
                  (function () {
                  'use strict'

                  // Fetch all the forms we want to apply custom Bootstrap validation styles to
                  var forms = document.querySelectorAll('.needs-validation')

                  // Loop over them and prevent submission
                  Array.prototype.slice.call(forms)
                      .forEach(function (form) {
                      form.addEventListener('submit', function (event) {
                          if (!form.checkValidity()) {
                          event.preventDefault()
                          event.stopPropagation()
                          }
                          form.classList.add('was-validated')
                      }, false)
                      })
                  })()
          </script>

          <!-- Option 1: Bootstrap Bundle with Popper -->
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

  </body>
</html>
<?php }