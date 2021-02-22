<?php
session_start();

if (!isset($_SESSION["id"]))  {
	header("Location:login-register.php?patient=register&user=login");
} else {
    require_once("class/dao.php");
    require_once("function/functions.php");

  
    $item=$dao->getNomPrenom("mutuelle.nom as mutuelle,praticien.email as emailPraticien,praticien.nom as praticienN,praticien.prenom as praticienP,praticien.sexe as praticienS,patient.nom,patient.prenom,patient.sexe,patient.nom_naissance,patient.date_naissance,patient.telephone_portable,patient.telephone_fixe,patient.email,patient.adresse1,patient.adresse2,patient.code_postal,patient.ville,patient.pays,patient.numero_securite_sociale",$_SESSION["id"]);   
    // print_r($item);
    $doc=$dao->getDocuments($_SESSION["id"]);

    if(isset($_POST['contact'])) {
 
            $send=sendMail($item[0]['emailPraticien'],$item[0]["nom"]." ".$item[0]["prenom"],$_POST["sujet"],$_POST['message']);
            if ($send===true) {
                print '<script>window.alert("Le message a été envoyé avec succés.");
                </script>';
            }
            else{
                print '<script>window.alert("Le message n\'a pas été envoyé.");
                </script>';
            }
        }
        if (!isset($_SESSION["id"]))  {
            header("Location:index.php");
        } else {
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href='https://cdn.jsdelivr.net/combine/npm/fullcalendar-scheduler@5.4.0/main.min.css,npm/fullcalendar-scheduler@5.4.0/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="css/patient_home.css">

          <script src='https://cdn.jsdelivr.net/combine/npm/fullcalendar@5.4.0,npm/fullcalendar-scheduler@5.4.0,npm/fullcalendar-scheduler@5.4.0/locales-all.min.js,npm/fullcalendar-scheduler@5.4.0/locales-all.min.js,npm/fullcalendar-scheduler@5.4.0/main.min.js'></script>
          <link href='https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.css' rel='stylesheet' />
          <link href='https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.css' rel='stylesheet'>
    <title>Profession libérale</title>
    <script type="text/javascript">
          document.addEventListener('DOMContentLoaded', function() {           
                        var calendarEl = document.getElementById('calendar');
                        var calendar = new FullCalendar.Calendar(calendarEl, {    
                          initialView: 'timeGridWeek',
                          locale:'fr',
                          hiddenDays: [ 0 ],
                          slotDuration: '00:20:00',
                          slotMinTime: '8:00', 
                          slotMaxTime: '18:00',
                          // String, default: 'standard'
                          selectable:true,
                          editable:true,
                          themeSystem: 'bootstrap',
                          buttonText:{
                            today:'aujourd\'hui'                       
                          },                              
                          headerToolbar: {
                          left: 'prev next',
                          center: 'title',
                          right: 'today'
                          },
                  });
                  
                  calendar.render(); 
                });
              </script>
</head>
<body>
<header>
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
</header>
<br>
<div class="container"><h3><?= $item[0]["sexe"]?>. <?= $item[0]["nom"]?> <?= $item[0]["prenom"]?></h3></div>
<div id="exTab1" class="container">	
    <ul class="nav nav-pills">
        <li class="active"><a  href="#1a" data-toggle="tab">Vos informations personnelle</a>
        </li>
        <li><a href="#2a" data-toggle="tab">Prendre rendez-vous</a>
        </li>
        <li><a href="#3a" data-toggle="tab">Envoi de document</a>
        </li>
        <li><a href="#4a" data-toggle="tab">Contact</a>
        </li>
    </ul>
    <div class="tab-content clearfix">
        <div class="tab-pane active" id="1a">
            <h7>yo</h7>
            <div class="white_background_conteneur">
                <div id="conteneur_type_information">
                    <br>
                    <br>
                    <br>Sexe <br>
                    <br>Nom <br>
                    <br>Prénom <br>
                    <br>Nom de naissance <br>
                    <br>Date de naissance <br>
                    <br>Téléphone portable <br>
                    <br>Téléphone fixe<br>
                    <br>Email<br>
                    <br>Adresse1 <br>
                    <br>Adresse2 <br>
                    <br>Code postal <br>
                    <br>Ville <br>
                    <br>Pays <br>
                    <br>
                    <hr>
                    <br>
                    <br>Numéros de securité social <br>
                    <br>Praticien <br>
                    <br>Mutuelle <br>
                    <br>
                </div>
                <div id="conteneur_information">
                    <br>
                    <br>
                    <br><?= $item[0]["sexe"]?><br>
                    <br><?= $item[0]["nom"]?><br>
                    <br><?= $item[0]["prenom"]?><br>
                    <br><?= $item[0]["nom_naissance"]?><br>
                    <br><?= date("d/m/Y", strtotime($item[0]["date_naissance"]));?><br>
                    <br><?= $item[0]["telephone_portable"]?><br>
                    <br><?= $item[0]["telephone_fixe"]?><br>
                    <br><?= $item[0]["email"]?><br>
                    <br><?= $item[0]["adresse1"]?><br>
                    <br><?= $item[0]["adresse2"]?><br>
                    <br><?= $item[0]["code_postal"]?><br>
                    <br><?= $item[0]["ville"]?><br>
                    <br><?= $item[0]["pays"]?><br>
                    <br>
                    <hr>
                    <br>
                    <br><?= $item[0]["numero_securite_sociale"]?><br>
                    <?php $nomination="";
                    if ($item[0]["praticienS"]=="M"){
                        $nomination="M.";
                    }else{
                        $nomination="Mme. ";
                    }?>
                    <br><?=$nomination.$item[0]["praticienN"]." ".$item[0]["praticienP"]?><br>
                    <br><?= $item[0]["mutuelle"]?><br>
                    <br>
                </div>
            </div>

        </div>

    <!-- prendre rendez-vous  -->

        <div class="tab-pane" id="2a">
            <h7>grave</h7>
            <div class="white_background_conteneur" style="width:auto;height:1020px;">
            <div id='calendar' style="width:100%;heighth:100%;"></div>  
            </div>
        </div>
    
    <!-- envoi de document -->

        <div class="tab-pane" id="3a">
            <h7>grave</h7>
            <div class="white_background_conteneur">
                
                <form action="file-upload.php" method="post" enctype="multipart/form-data" class="mb-3" id="form_envoi">
                    <h3 class="text-center mb-5">Envoiller un fichier</h3>

                    <div class="user-image mb-3 text-center">
                        <div style="width: 100px; height: 100px; overflow: hidden; background: #cccccc; margin: 0 auto">
                            <img src="" class="figure-img img-fluid rounded" id="imgPlaceholder" alt="">
                        </div>
                    </div>
                    <span id="texte_fichier_uniquement">Uniquement des fichier jpg, png et jpeg</span>
                    <div class="custom-file" id="div_file_upload">
                        <input type="file" name="fileUpload" class="custom-file-input" id="chooseFile">
                        <label class="custom-file-label" for="chooseFile" id="choisirFichier">Choisir le fichier</label>
                    </div>
                    
                    <button type="submit" name="submit" class="btn btn-primary btn-block mt-4">
                        envoie
                    </button>
                    <!-- echo '<script type="text/javascript">window.alert("'.$documents.'");</script>'; -->
                </form>

                <div id="table_historique">
                    <div id="titre_historique">Historique des envois de fichiers :</div>
                    <table id="table_id" class="display">
                        <thead>
                            <tr>
                                <th>Nom du fichier</th>
                                <th>Date d'envoi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i=0;$i<count($doc) ;$i++) { 
                                echo '<tr value='.$doc[$i]["hex"].' value2='.$doc[$i]["extension"].'>'; 
                                echo "<th>".$doc[$i]["titre"]."</th>";
                                echo "<th>".date("d/m/Y H:i", strtotime($doc[$i]["dateE"]))."</th>";  
                                echo "</tr>";
                            }?>
                        </tbody>
                    </table>
                </div>
                <img id="img_historique" src="" alt="">

            </div>
        </div>


    <!-- Contact -->

                    <div class="tab-pane" id="4a">
                            <h7>grave</h7>
                            <div class="white_background_conteneur">
                                <div class="card">
                                <div class="card-body">
                                    <form method="post" class="row g-3 needs-validation" novalidate >
                                    
                                        <div class="col-md-7"> 
                                        <label for="sujet">* Votre sujet</label>                       
                                        <input value="" id="sujet" type="text" name="sujet" class="form-control"  required>
                                        </div>
                                        <div class="col-md-7">
                                        <label for="exampleFormControlTextarea1" class="form-label">* Votre message</label>
                                        <textarea value="" name="message" class="form-control" id="exampleFormControlTextarea1" rows="3" required></textarea>
                                        </div>
                                        <hr>
                                        <div class="col-md-7">
                                            <button class="btn btn-primary" name="contact" type="submit">Envoyer</button>
                                        </div>
                                    </form>
                                </div>
                                </div>
                                    
                            </div>
                    </div>
        </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>

<script>
    $(document).ready( function () {
        $('#table_id').DataTable();
        $('#img_historique').hide();

        $('#table_id tbody').on('mouseover', 'tr', function () {
            var hex_data=$(this).attr('value');

            extencion=$(this).attr('value2');

            $('#img_historique').attr('src', 'data:image/'+extencion+';base64,'+hex_data);
            $('#img_historique').show();
        });

        $( "#table_id tbody" ).mouseleave(function() {
            $('#img_historique').hide();
        });
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#imgPlaceholder').attr('src', e.target.result);
                document.getElementById("choisirFichier").innerHTML = input.files[0].name;
            }

            // base64 string conversion
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#chooseFile").change(function () {
        readURL(this);
    });
</script>
<?php } ?>
</body>
</html>
<?php }