<?php
session_start();

require_once("class/dao.php"); 
require_once("class/libelle_pays.php"); 
require_once("function/functions.php");

 
    $item=$dao->getNomPraticien("nom, prenom, sexe",$_SESSION['id']);   
    //print_r($item);
    $infoPatient=$dao->getInfoPatient("patient.idPatient as idPatient,patient.sexe, patient.nom, prenom, date_naissance, telephone_portable, telephone_fixe, patient.email, adresse1, adresse2, patient.code_postal, patient.ville, pays, numero_securite_sociale, mutuelle.nom as mutuelle",$_SESSION['id']);
    // print_r($infoPatient);
    $infoUpload=$dao->getInfoUpload("patient.nom, patient.prenom, patient.sexe, titre, documents.date, documents.idDocument as id, file_blob as file, extension",$_SESSION['id']);
    // print_r($infoUpload);
    $find=false;
    $message="";

    if(isset($_POST['register'])&&$_POST['register']){

                foreach($dao->getPatient() as $value){
                    if ($value['email']==strtolower($_POST['mail']))
                        {
                            $find=true;
                            print '<script>window.alert("Il y a déjà un compte enregistrer avec cette adresse mail !\n Veuillez-vous connecter !.");
                            </script>';
                        }else{

                        }
                }
                if (!$find) {
                    $nom=ucwords($_POST['lastName']);
                    $prenom=ucwords(strtolower($_POST['firstName']));
                    $sexe=$_POST['sexe'];
                    $nom_naissance=ucwords(strtolower($_POST['nom-naissance']));
                    $date_naissance=$_POST['date-de-naissance'];
                    $portable=$_POST['numero-de-telephone-portable'];
                    $fixe=$_POST['numero-de-telephone-fixe'];
                    $email=strtolower($_POST['mail']);
                    $adresse1=ucwords(strtolower($_POST['adresse-1']));
                    $adresse2=ucwords(strtolower($_POST['adresse-2']));
                    $code_postal=$_POST['code-postal'];
                    $ville=ucwords(strtolower($_POST['ville']));
                    $pays=$_POST['country'];
                     
                    $mot_de_passe1 = Genere_Password(10);
                    echo $mot_de_passe1;
                    $mot_de_passe=password_hash($mot_de_passe1, PASSWORD_ARGON2I);
                    $mutual=$_POST['mutual'];
                    $praticien=$_SESSION["id"];
                    
                    $verif=valideNir($_POST['numero-securite-sociale']);
                            if ($verif==true) {
                                $numero_sociale=$_POST['numero-securite-sociale'];
                                $dao->insertPatient($nom,$prenom,$sexe,$nom_naissance,$date_naissance,$portable,$fixe,$email,$adresse1,$adresse2,$code_postal,$ville,$pays,$numero_sociale,$mot_de_passe,$mutual,$praticien);
                                if ($dao->getError()) {
                                    print $dao->getError();
                                }else{
                                    $nomination="";
                                    if($_POST['sexe']=="M"){
                                        $nomination="M. ";
                                    }else{
                                        $nomination="Mme. ";
                                    }
                                   
                                     $send=sendMail($_POST['mail'],$_POST["firstName"]." ".$_POST["lastName"],"Confirmation d'inscription",str_replace("##token##","patient=1",file_get_contents("mail-register.html")));
                                    if ($send===true) {
                                        print '<script>window.alert("Le patient '.$nomination.$_POST["lastName"]." ".$_POST["firstName"].' a été enregistrer avec succés.");
                                        </script>';
                                        header("location:?action=home");
                                    }
                                }
                            } else {                               
                                print '<script>window.alert("Veuillez saisir un muméro de sécurité sociale valide");
                                </script>';
                            }
                }      
        }

        if(isset($_POST['contact'])) {

            foreach($dao->getPatient() as $value){
                if($value['id']==$_POST['patients']){
                    
            $send=sendMail($value['email'],$value['nom']." ".$value['prenom'],$_POST["sujet"],$_POST['message']);
            if ($send===true) {
                print '<script>window.alert("Le message a été envoyé avec succés.");
                </script>';

                }else{
                    print '<script>window.alert("Le message n\'a pas été envoyé.");
                    </script>';
            }

            }
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
    <link rel="stylesheet" href="css/praticien_home.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    
    <link href='https://cdn.jsdelivr.net/combine/npm/fullcalendar-scheduler@5.4.0/main.min.css,npm/fullcalendar-scheduler@5.4.0/main.min.css' rel='stylesheet' />
          <script src='https://cdn.jsdelivr.net/combine/npm/fullcalendar@5.4.0,npm/fullcalendar-scheduler@5.4.0,npm/fullcalendar-scheduler@5.4.0/locales-all.min.js,npm/fullcalendar-scheduler@5.4.0/locales-all.min.js,npm/fullcalendar-scheduler@5.4.0/main.min.js'></script>
          <link href='https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.css' rel='stylesheet' />
          <link href='https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.css' rel='stylesheet'>
    <title>Home praticien</title>
    <script type="text/javascript">
          /**
          mettre l'event dans var event sinon, sa cré un nouveau calendrier pour chaque nouvel evenement recupéré dans json
          */
          document.addEventListener('DOMContentLoaded', function() {
            var xhttp = new XMLHttpRequest();
                      //on lui affecte une fonction quand HTTPREQUEST reçoit des informations
                        xhttp.onreadystatechange = function() {
                          //vérification que la requête HTTP est effectuée (readyState 4) et qu'elle s'est bien passée (status 200)
                      
                          if (this.readyState == 4 && this.status == 200) {
                          // Typical action to be performed when the document is ready:
                                var obj=JSON.parse(xhttp.responseText);
                                var events=new Array();      
                                for (var i=0;i<obj.length;i++) {	
                                var event= {
                                title: obj[i].nom +" "+ obj[i].prenom,
                                start: obj[i].date_heure_debut,
                                end: obj[i].date_heure_fin,                          
                                /*extendedProps: {
                                  department: 'BioChemistry'
                                },*/
                                description: 'appointment'
                            }
                            events.push(event);	
                                //console.log(obj[i]);
                                }
                                
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
                    
                    events:events,
                            
                    dateClick: function(info) {
                              console.log('ok');
                            $('#exampleModal').modal('show');
                        }            
                  });
                  
                  calendar.render(); 
                            }                    
                          }
                        xhttp.open("GET","myxmlfeed.php", true);
                        xhttp.send();
                    });
              </script>
</head>
<body> 
                    <?php $nomination="";
                    if($item[0]["sexe"]=="M"){
                        $nomination="M. ";
                    }else{
                        $nomination="Mme. ";
                    }
                    ?>
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

                    <div class="container"><h3><?=$nomination.$item[0]["nom"]." ".$item[0]["prenom"]?></h3></div>
                    <div id="exTab1" class="container">	
                        <ul class="nav nav-pills">
                            <li class="active"><a  href="#1a" data-toggle="tab">Liste des patients</a>
                            </li>
                            <li><a href="#2a" data-toggle="tab">Documents envoyés</a>
                            </li>
                            <li><a href="#3a" data-toggle="tab">Nouveau patient</a>
                            </li>
                            <li><a href="#4a" data-toggle="tab">Emplois du temps</a>
                            </li> 
                            <li><a href="#5a" data-toggle="tab">Contact</a>
                            </li>    
                        </ul>
                        <div class="tab-content clearfix">

                <!-- debut liste des patients -->

                            <div class="tab-pane active" id="1a">
                                <h7>yo</h7>
                                <div class="white_background_conteneur">

                                    <table id="table_id" class="display">
                                        <thead>
                                            <tr>
                                                <th>Patient</th>
                                                <th>Date de naissance</th>
                                                <th>Téléphone</th>
                                                <th>Email</th>
                                                <th>Adresse</th>
                                                <th>Code postal</th>
                                                <th>Ville</th>
                                                <th>Pays</th>
                                                <th>n° sécuriter social</th>
                                                <th>mutuelle</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            <?php 
                                                $nominationPatient="";
                                                for ($i=0;$i<count($infoPatient);$i++) { 

                                                    if ($infoPatient[$i]["sexe"]=="M") {
                                                        $nominationPatient="M.";
                                                    }else{
                                                        $nominationPatient="Mme.";
                                                    }
                                                    echo "<tr>";
                                                        echo "<th>".$nominationPatient.$infoPatient[$i]["nom"]." ".$infoPatient[$i]["prenom"]."</th>";
                                                        echo "<th>".date("d/m/Y", strtotime($infoPatient[$i]["date_naissance"]))."</th>";
                                                        echo "<th>".$infoPatient[$i]["telephone_portable"]."".$infoPatient[$i]["telephone_fixe"]."</th>";
                                                        echo "<th>".$infoPatient[$i]["email"]."</th>";
                                                        echo "<th>".$infoPatient[$i]["adresse1"]." ".$infoPatient[$i]["adresse2"]."</th>";
                                                        echo "<th>".$infoPatient[$i]["code_postal"]."</th>";
                                                        echo "<th>".$infoPatient[$i]["ville"]."</th>";
                                                        echo "<th>".$infoPatient[$i]["pays"]."</th>";
                                                        echo "<th> n°".$infoPatient[$i]["numero_securite_sociale"]."</th>";
                                                        echo "<th>".$infoPatient[$i]["mutuelle"]."</th>";
                                                    echo "</tr>";
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                
                                </div>
                                test
                            </div>

                <!-- debut liste documents envoyés -->

                            <div class="tab-pane" id="2a">
                                <h7>ca va</h7>
                                <div class="white_background_conteneur">

                                    <table id="table_id2" class="display">
                                        <thead>
                                            <tr>
                                                <th>Patient</th>
                                                <th>Documents</th>
                                                <th>Date d'envoi</th>
                                                <th>Téléchargement</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            <?php $nominationPatient="";
                                            for ($i=0;$i<count($infoUpload);$i++) { 
                                                if ($infoUpload[$i]["sexe"]=="M") {
                                                    $nominationPatient="M.";
                                                }else{
                                                    $nominationPatient="Mme.";
                                                }
                                                echo '<tr value='.$infoUpload[$i]["file"].' value2='.$infoUpload[$i]["extension"].'>';
                                                echo "<th>".$nominationPatient.$infoUpload[$i]["nom"]." ".$infoUpload[$i]["prenom"]."</th>";
                                                echo "<th>".$infoUpload[$i]["titre"]."</th>";
                                                echo "<th>".date("d/m/Y H:i", strtotime($infoUpload[$i]["date"]))."</th>";
                                                echo "<th><a href='download.php?id=".$infoUpload[$i]["id"]."'>télécharger</a></th>";
                                                echo "</tr>";
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                            </div>
                                <img id="img_historique" src="">
                            </div>

                <!-- debut nouveau patient -->

                            <div class="tab-pane" id="3a">
                                <h7>ca va ?</h7>
                                <div class="white_background_conteneur">
                                <br> 
                                <div class="card">
                                <div class="card-body">
                                <form method="post" class="row g-3 needs-validation" novalidate>
                                <div class="col-md-12">
                                    <div>
                                        <input type="radio" class="form-check-input" id="women" name="sexe" value="F" required>
                                        <label for="women">* Femme</label>                                        
                                    </div> 
                                    <div>
                                        <input type="radio" class="form-check-input" id="man" name="sexe" value="M">    
                                        <label for="man">* Homme</label>                           
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <label for="lastName" >* Nom </label>
                                    <input type="text" name="lastName" id="lastName" class="form-control" required> 
                                </div>
                                <div class="col-md-4">
                                    <label for="firstName">* Prénom </label>
                                    <input type="text" name="firstName" id="firstName" class="form-control" required> 
                                </div>
                                <div class="col-md-4">
                                    <label for="nom-naissance">* Nom de naissance </label>
                                    <input type="text" name="nom-naissance" id="nom-naissance" class="form-control" required> 
                                </div>
                                <div class="col-md-4">
                                    <label for="numero-securite-sociale">* Numéro de sécurité sociale :</label>
                                    <input type="number" class="form-control" name="numero-securite-sociale" id="numero-securite-sociale"  required> 
                                </div>
                                <div class="col-md-4">
                                    <label for="birthdate">* Date de naissance :</label>
                                    <input type="date" class="form-control" name="date-de-naissance" class="form-control" id="birthdate"  required>
                                </div>
                                <div class="col-md-4">
                                <label for="port">* Téléphone portable :
                                <input type="tel" pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$"  name="numero-de-telephone-portable" class="form-control" id="port"  required>
                                </label> 
                                </div>
                                <div class="col-md-4">
                                <label for="fixe">Téléphone fixe :</label>
                                <input type="tel" pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$"  name="numero-de-telephone-fixe" class="form-control " id="fixe"  placeholder="" >
                                </div>
                                <div class="col-md-4">
                                <label for="email">* Mail</label>
                                <input type="text" class="form-control" placeholder="Recipient's username" name="mail" id="email" onChange="checkMailPatient()" aria-label="Recipient's username" aria-describedby="basic-addon2"  required>
                                </div>                    
                                <div class="col-md-6">
                                <label for="adresse1">* Adresse 1 :</label>                    
                                <input type="text" name="adresse-1" class="form-control " id="adresse1" required >      
                                </div>
                                <div class="col-md-6">
                                    <label for="adresse2">Adresse 2:</label>
                                    <input type="text" name="adresse-2" class="form-control " id="adresse2"  value="">
                                </div>
                                <div class="col-md-3">
                                    <label for="cp">* Code Postal :</label>
                                    <input type="text" name="code-postal" class="form-control" id="cp" required >
                                </div>
                                <div class="col-md-4">
                                    <label for="ville">* Ville :</label>
                                    <input type="text" name="ville" class="form-control " id="ville" required >
                                </div>
                                <div class="col-md-4">
                                    <label for="country" class="form-label">* Pays :</label>
                                    <select name="country" id="country" class="form-control" required>
                                        <option value="">Sélectionner un pays : </option>
                                        <?php
                                            while ($country = $BddByCountry->fetch(PDO::FETCH_ASSOC))
                                            {
                                        ?>
                                        <option value="<?= $country["libelle_pays"] ?>" <?php  if($country["libelle_pays"]=="France (métropolitaine)") {print "selected";} ?>><?= $country["libelle_pays"] ?></option>

                                        <?php
                                            }
                                        $BddByCountry->closeCursor();
                                        ?>
                                    </select>
                                </div>   
                                <div class="col-6">
                                    <label for="mutual1" class="form-label">* Mutuelle :</label>
                                    <select name="mutual" id="mutual1" class="form-control"  required>
                                        <option value="NULL">Pas de mutuelle</option>
                                        <?php 
                                        foreach($dao->getNameMutuelle() as $item){
                                            ?>
                                            <option value="<?= $item["idMutuelle"] ?>"><?= $item["nom"] ?></option>
                                        <?php
                                        } ?>
                                    </select>
                                </div>
                               
                                <div class="col-12">
                                    <input class="btn btn-primary" type="submit" value="Valider"  name="register">
                                </div>  

                            </form>

                                </div>
                                </div>          
                            
                        </div>
                        </div>

                <!-- debut emplois du temps -->

                                <div class="tab-pane" id="4a">
                                    <h7>grave</h7> 
                                    <button onclick="window.location.href = 'newrdv.php';">ajouter un rdv</button>
                                    <div class="white_background_conteneur" style="width:auto;height:1020px;">
                                        <div id='calendar' style="width:100%;heighth:100%;"></div>   
                                    </div>

                                     
                                    
                                </div>

                <!-- page contact -->
         <div class="tab-pane" id="5a">
                 <h7>grave</h7> 
                                    
                 <div class="white_background_conteneur">
                    <form method="post" class="row g-3 need-validation" novalidate>
            
                    <div class="col-md-7">
                                <label for="patient" >Les patients </label>
                                <select name="patients" id="patient" class="form-control " required>
                                <option value="">Faite votre choix</option>
                                    <?php 
                       for ($i=0;$i<count($infoPatient);$i++) { 
                        if ($infoPatient[$i]["sexe"]=="M") {
                            $nominationPatient="M.";
                        }else{
                            $nominationPatient="Mme.";
                        }
                            ?>
                            <option value="<?= $infoPatient[$i]["idPatient"]?>" ><?= $nominationPatient.$infoPatient[$i]["nom"]." ".$infoPatient[$i]["prenom"] ?></option>
                        <?php
                        } 
                            ?>                        
                        </select>
                    </div>

                         <div class="input-group flex-nowrap">                        
                        <input value="" type="text" name="sujet" class="form-control" placeholder="Sujet" aria-label="Username" aria-describedby="addon-wrapping" required>
                        </div>

                        <div class="col-12">
                        <label for="exampleFormControlTextarea1" class="form-label">Votre message</label>
                        <textarea value="" name="message" class="form-control" id="exampleFormControlTextarea1" rows="3" required></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" name="contact" type="submit">Envoyer</button>
                        </div>
                 </form>
            </div>                                  
    </div>

                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
                    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
                    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
                    <script>
                        $(document).ready( function () {
                            $('#table_id').DataTable();
                            $('#table_id2').DataTable();
                            $('#img_historique').hide();

                            $('#table_id2 tbody').on('mouseover', 'tr', function () {
                                var hex_data=$(this).attr('value');

                                extencion=$(this).attr('value2');

                                $('#img_historique').attr('src', 'data:image/'+extencion+';base64,'+hex_data);
                                $('#img_historique').show();
                            });

                            $( "#table_id2 tbody" ).mouseleave(function() {
                                $('#img_historique').hide();
                            });

                        } );

                    </script>
                        <script type=text/javascript>
                            function addPatient(){

                                if (document.getElementById('women')==F){
                                    alert(document.getElementById('women').value." ".document.getElementById('man').value." ".document.getElementById('').value )
                                } 
                            }
                            
                            </script>
                    <script>
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

</body>
</html>

<?php }