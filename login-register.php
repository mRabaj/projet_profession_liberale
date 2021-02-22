<?php
   session_start();

require_once("class/libelle_pays.php");     
require_once("function/functions.php");
require_once("class/dao.php"); 

  $find=false;
$message="";
if (isset($_GET["disconnect"])&&$_GET["disconnect"]=="true") {
    unset($_SESSION["id"]);
    if (isset($_COOKIE["connect"])) {
      
      setcookie("connect", NULL, 0, null, null, null, null);
    }
  }

    /* si processus d'inscription tout juste finalisé message de confirmation */
    if (isset($_GET["register"])&&$_GET["register"]=="ok") {
      $message="Votre inscription a bien été enregistrer. Veuillez maintenant vous connecter.";
    }
    
  /* si processus de mot de passe perdu tout juste finalisé message de confirmation */
  if (isset($_GET["resetpwd"])&&$_GET["resetpwd"]=="ok") {
    $message="La modification de votre mot de passe a bien été effectuée. Veuillez vous connecter.";
  }

  $verify=false;
  /* si le compte est vérifié après saisie d'identifiant et mot de passe, on crée la session pour ce compte et on redirige vers la page d'accueil
    Si la case restez connecté est coché on crée le cookie pour authentifier automatiquement à la prochaine ouverture de la page dans le navigateur */
  if (isset($_POST['to-log-in'])){
      

      if (isset($_GET["user"])&&$_GET["user"]=="login") {
       
          $verify=verifyAccount($dao->getPatient(),$_POST["mail-login"],$_POST["pwd-login"]); 
          if ($verify) {
            if (isset($_POST["remember"])&&$_POST["remember"]) {
              setcookie("connect", $verify, time() + (86400),null, null, false, true); // 86400 = 1 day	
            }
          header("Location:patient_home.php");
          } else {
            $error="Identifiants incorrects";
          }
      }

      if (isset($_GET["practitioner"])&&$_GET["practitioner"]=="login") {
       
        $verify=verifyAccount($dao->getPractitioner(),$_POST["mail-login"],$_POST["pwd-login"]);
        if ($verify) {
          if (isset($_POST["remember"])&&$_POST["remember"]) {
            setcookie("connect", $verify, time() + (86400),null, null, false, true); // 86400 = 1 day	
          }
        header("Location:praticien_home.php");
        } else {
          $error="Identifiants incorrects";
        }
      }    
}

if (isset($_POST['create-an-account']))
{      
      if ($_POST['pwd-register']==$_POST['rpwd-register'])
        {   
            if (isset($_GET["patient"])&&$_GET["patient"]=="register") {
                    foreach($dao->getPatient() as $item){
                        if ($item['email']==strtolower($_POST['mail-register']))
                            {
                                $find=true;
                                $message="Il y a déjà un compte enregistrer avec cette adresse mail !<br> Veuillez-vous connecter !.";
                            }
                    }
                }
            
            if (isset($_GET["praticien"])&&$_GET["praticien"]=="register") {
                    foreach($dao->getPractitioner() as $item){
                        if ($item['email']==strtolower($_POST['mail-register']))
                            {
                                $find=true;
                                $message="Il y a déjà un compte enregistrer avec cette adresse mail !<br> Veuillez-vous connecter !.";
                            }
                    }
                }

            if (!$find) {
                        $nom=ucwords($_POST['lastName']);
                        $prenom=ucwords(strtolower($_POST['firstName']));
                        $sexe=$_POST['sexe'];
                        $portable=$_POST['numero-de-telephone-portable'];
                        $fixe=$_POST['numero-de-telephone-fixe'];
                        $email=strtolower($_POST['mail-register']);
                        $adresse1=ucwords(strtolower($_POST['adresse-1']));
                        $adresse2=ucwords(strtolower($_POST['adresse-2']));
                        $code_postal=$_POST['code-postal'];
                        $ville=ucwords(strtolower($_POST['ville']));
                        $pays=$_POST['country'];
                        $mot_de_passe=password_hash($_POST["pwd-register"], PASSWORD_ARGON2I);            
                                   
                                if (isset($_GET["patient"])&&$_GET["patient"]=="register") {
                                    $nom_naissance=ucwords(strtolower($_POST['nom-naissance']));
                                    $date_naissance=$_POST['date-de-naissance'];
                                    $mutual=$_POST['mutual'];
                                    $praticien=$_POST['practitioner'];
                                   
                                    $verif=valideNir($_POST['numero-securite-sociale']);
                                    if($verif==true){
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

                                            $send=sendMail($_POST['mail-register'],$_POST["name"],"Confirmation d'inscription",str_replace("##token##","patient=register&user=login",file_get_contents("mail-register.html")));
                                            if ($send===true) {
                                            /* redirection vers la page d'identification si inscription réussie */
                                            print '<script>window.alert('.$nomination." ".$_POST["lastName"]." ".$_POST["firstName"]. ' votre compte a été enregistrer avec succés.\n Maintenant vous pouvez vous connecter.");
                                            </script>';
                                            }
                                        }
                                    }                                 
                                  
                                }

                                if (isset($_GET["praticien"])&&$_GET["praticien"]=="register") {
                                    $code_rpps=$_POST['rpps'];
                                    $dao->insertPraticien($nom,$prenom,$sexe,$portable,$fixe,$email,$adresse1,$adresse2,$code_postal,$ville,$pays,$mot_de_passe,$code_rpps);
                                    if ($dao->getError()) {
                                        print $dao->getError();
                                    }
                                    $send=sendMail($_POST['mail-register'],$_POST["firstName"]." ".$_POST["lastName"],"Confirmation d'inscription",str_replace("##token##","praticien=register&practitioner=login",file_get_contents("mail-register.html")));
                                    if ($send===true) {
                                    /* redirection vers la page d'identification si inscription réussie */
                                    print '<script>window.alert("Le patient '.$nomination.$_POST["lastName"]." ".$_POST["firstName"].' votre compte a été enregistrer avec succés.\n Maintenant vous pouvez vous connecter.");
                                    </script>';
                              
                                    } else {
                                        $error=$send;
                                    }
                                }                            
                        
                    }
                        
        }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" 
    integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<title></title>
	<script type="text/javascript">
		function checkMailPatient(){
		var xhttp = new XMLHttpRequest();
			//on lui affecte une fonction quand HTTPREQUEST reçoit des informations
				xhttp.onreadystatechange = function() {
					//vérification que la requête HTTP est effectuée (readyState 4) et qu'elle s'est bien passée (status 200)
					if (this.readyState == 4 && this.status == 200) {
					// Typical action to be performed when the document is ready:
                        var obj=JSON.parse(xhttp.responseText);
						for (var i=0;i<obj.length;i++) {								
							if (obj[i].email==document.getElementById('email-register').value){
                           
							    alert("Il y a déjà un compte enregistrer avec cette adresse mail !\n Veuillez-vous connecter !.");
							}
						}
						
					}
				}
				xhttp.open("GET","checkAccountPatient.php", true);
				xhttp.send();			
        }
        /** il vérifie aussi les mails pour les comptes praticiens, il faut trouver une autre solution */
        // function checkMailPraticien(){
		// var xhttp = new XMLHttpRequest();
		// 	//on lui affecte une fonction quand HTTPREQUEST reçoit des informations
		// 		xhttp.onreadystatechange = function() {
		// 			//vérification que la requête HTTP est effectuée (readyState 4) et qu'elle s'est bien passée (status 200)
		// 			if (this.readyState == 4 && this.status == 200) {
		// 			// Typical action to be performed when the document is ready:
        //                 var obj=JSON.parse(xhttp.responseText);
		// 				for (var i=0;i<obj.length;i++) {								
		// 					if (obj[i].email==document.getElementById('email-register').value){                           
		// 					    alert("Il y a déjà un compte enregistrer avec cette adresse mail !\n Veuillez-vous connecter !.");
		// 					}
		// 				}
						
		// 			}
		// 		}
		// 		xhttp.open("GET","checkAccountPraticien.php", true);
		// 		xhttp.send();
		// }
		
		function checkPwd(){
			if(document.getElementById('pwd-register').value!==document.getElementById('rpwd-register').value){
				alert("les mots de passe ne correspondent pas !");
			   }
        }   
        /**reste à faire, vérifier le bon format rpps */
        // function sizeRpps(){
        //     if(document.getElementById('rpps').value>=11){
        //         console.log("ok");
        //         //alert("Veuillez entrer un numéro RPPS à chiffres.")
        //     }
        // }
       
	</script>
</head>
<body>
    <div class="container">
                <nav class="navbar navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand" href="index.php?disconnect=true">Accueil</a>
               
                
                <?php if (isset($_GET["user"])&&$_GET["user"]=="login") { ?>
                <h3>Connexion <span style="color:red;">patient</span> </h3>              
                   <?php } ?>
                   <?php if (isset($_GET["practitioner"])&&$_GET["practitioner"]=="login") { ?>
                    <h3 style="text-align:center;">Connexion <span style="color:red;">praticien</span> </h3>
                    <?php } ?>
                 </div>
                </nav>
          
            <?php if (isset($_GET["practitioner"])&&$_GET["practitioner"]=="login") { ?>
                <div class="info" tabindex="0">
                                    <span class="infoicon">
                                    <i class="fa fa-info"></i>
                                    </span>
                                    <h1 class="titre_id">Identifiant :</h1>
                                    <p class="description">Si vous ne souvenez pas de votre code RPPS, vous pouvez vous procurer ce numéro sur <a class="reference" href="https://annuaire.sante.fr/">annuaire.sante.fr</a>.</p>                      
                </div>
    <?php } ?>
                
        <form method="post" class="row g-3 needs-validation" novalidate>
           <div class="col-md-7"><h4 class=''>Vous avez déjà un compte</h4></div>
            <br/>               
                         <?php if (isset($_GET["user"])&&$_GET["user"]=="login") { ?>
                            <div class="alert alert-primary" role="alert">
                                Pour accéder à la démo, il vous faut les informations de connexion. Celles-ci sont les suivantes : login « patient@example.com » et mot de passe « 123456 »
                                </div>
                           <?php } ?>

                            <?php if (isset($_GET["practitioner"])&&$_GET["practitioner"]=="login") { ?>
                                <div class="alert alert-primary" role="alert">
                                Pour accéder à la démo, il vous faut les informations de connexion. Celles-ci sont les suivantes : code RPPS « 1234 » Adresse mail « admin@example.com » et mot de passe « 123456 »
                                </div>
                            <div class="col-md-7">
                                <label for="rppsconnexion">Code RPPS </label>
                              <input type="number" name="code-rpps" placeholder="Entrez votre code RPPS" class="form-control" onChange="" id="rppsconnexion" value="<?php if (isset($_POST['code-rpps'])&& $_POST['code-rpps']){print $_POST['code-rpps'];} ?>"  required > 
                            </div>
                              <?php } ?>
                            <div class="col-md-7">
                                <label for="email">* Adresse mail </label>
                                <input type="email" name="mail-login" id="email"  class="form-control"  value="<?php if (isset($_POST['mail-login'])&& $_POST['mail-login']){print $_POST['mail-login'];} ?>" placeholder="nom.prenom@example.com" required> 
                             </div>

                             <div class="col-md-7">
                                <label for="pwd-login">* Mot de passe </label>
                                <input type="password" name="pwd-login" id="pwd-login" minlength="" class="form-control" required> 
                            </div>
                            <label class="col-12">
                                <input type="checkbox" checked="checked" name="remember"> Restez connectés
                              </label>
                            <div class="col-md-3">
                            <button class="btn btn-primary" type="submit" name="to-log-in">Connexion</button>
                            </div> 
                            <?php if (isset($_GET["user"])&&$_GET["user"]=="login") { ?>
                                <div class="col-md-3">
                              <span class="psw"> Mot de passe <a class="reference" href="forgot-password.php?patient=forgot-password">oublié </a> ?</span>
                            </div>
                           <?php } ?>

                              <?php if (isset($_GET["practitioner"])&&$_GET["practitioner"]=="login") { ?>
                                <div class="col-md-3">
                                <span class="psw"> Mot de passe <a class="reference" href="forgot-password.php?praticien=forgot-password">oublié </a> ?</span>
                              </div>
                              <?php } ?>
                             
   </form>
   <br>
   <br>
    <form method="post" class="row g-3 needs-validation" novalidate>   
            <legend><h4 class="">Vous n'avez pas encore de compte</h4></legend>
                <br/>
                
                <div class="col-12">
                <input type="radio" id="women" class="form-check-input" name="sexe" value="F" required >
                <label for="women">* Femme</label>
                </div> 
                <div class="col-12">
                <input type="radio" id="man" class="form-check-input"  name="sexe" value="M">
                <label for="man">* Homme</label>
                </div> 
               <h4 id='result'></h4>
                <div class="col-md-6"> 
                    <label for="email-register">* Adresse mail </label>
                    <input type="email" name="mail-register" id="email-register"  class="form-control mail-register" onChange="checkMailPatient()" value="<?php if (isset($_POST['mail-register'])&& $_POST['mail-register']){print $_POST['mail-register'];}  ?>" placeholder="nom.prenom@example.com" required> 
                </div>
                
                <div class="col-md-6">
                    <label for="repeat-email-register">* Répétez votre adresse email </label>
                    <input type="email" name="repeat-mail-register" id="repeat-email-register"  class="form-control" onChange="checkMail()" value="<?php if (isset($_POST['mail-register'])&& $_POST['repeat-mail-register']){print $_POST['repeat-mail-register'];}  ?>" placeholder="nom.prenom@example.com" required> 
                </div>
                <div class="col-md-6">
                    <label for="pwd">* Mot de passe </label>
                    <input type="password" name="pwd-register" id="pwd-register" minlength="6" class="form-control " required> 
                </div>

                <div class="col-md-6">
                    <label for="rpwd">* Répétez votre mot de passe</label>
                    <input type="password" name="rpwd-register" id="rpwd-register" minlength="6" onChange="checkPwd()" class="form-control" required> 
                </div>
                <div class="col-md-4">
                    <label for="lastName">* Nom </label>
                    <input type="text" name="lastName" id="lastName" class="form-control " value="<?php if (isset($_POST['lastName'])&& $_POST['lastName']){print $_POST['lastName'];}?>" required> 
                </div>
                
                <div class="col-md-4">
                    <label for="firstName">* Prénom </label>
                    <input type="text" name="firstName" id="firstName" class="form-control" value="<?php if (isset($_POST['firstName'])&& $_POST['firstName']){print $_POST['firstName']; }?>" required> 
                </div>

                <?php if (isset($_GET["patient"])&&$_GET["patient"]=="register") {                  
                    ?>
                <div class="col-md-4">
                    <label for="nom-naissance">* Nom de naissance </label>
                    <input type="text" name="nom-naissance" id="nom-naissance" class="form-control" value="<?php if (isset($_POST['nom-naissance'])&& $_POST['nom-naissance']){print $_POST['nom-naissance'];} ?>" required> 
                </div>
                <div class="col-md-6">
                    <b><?php $message;?></b>
                    <label for="numero-securite-sociale">* Numéro de sécurité sociale </label>
                    <input type="number" name="numero-securite-sociale" id="numero-securite-sociale" class="form-control" value="<?php if (isset($_POST['numero-securite-sociale'])&& $_POST['numero-securite-sociale']){print $_POST['numero-securite-sociale'];} ?>" required> 
                </div>
                <div class="col-md-4">
                    <label for="birthdate">* Date de naissance </label>
                    <input type="date" name="date-de-naissance" class="form-control" id="birthdate" class="form-control " value="<?php if (isset($_POST['date-de-naissance'])&& $_POST['date-de-naissance']){print $_POST['date-de-naissance'];}?>" placeholder="" required>
                </div>

                <?php } ?>

                <?php if (isset($_GET["praticien"])&&$_GET["praticien"]=="register") {                     
                    ?>
                    <div class="col-md-4">
                    <label for="rpps">Votre Code RPPS (11 chiffres)
                    <input type="number" name="rpps" minlength="11" onChange="" class="form-control " id="rpps" required>
                    </label> 
                    </div>
               <?php }               
               ?>

                <div class="col-md-6"> 
                <label for="port">* Téléphone portable </label> 
                <input type="tel" pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$"  name="numero-de-telephone-portable" class="form-control " id="port" value="<?php if (isset($_POST['numero-de-telephone-portable'])&& $_POST['numero-de-telephone-portable']){print $_POST['numero-de-telephone-portable'];}?>" placeholder="0600000000"  required>
              
               </div>
                <div class="col-md-6">
                <label for="fixe">Téléphone fixe :</label>
                <input type="tel" pattern="^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}$"  name="numero-de-telephone-fixe" class="form-control" id="fixe" value="<?php if (isset($_POST['numero-de-telephone-fixe'])&& $_POST['numero-de-telephone-fixe']){print $_POST['numero-de-telephone-fixe'];}?>" placeholder="0900000000" >
                </div>

                <div class="col-md-6">
                <label for="adresse1">* Adresse 1 </label>                    
                <input type="text" name="adresse-1" class="form-control " id="adresse1" value="<?php if (isset($_POST['adresse-1'])&& $_POST['adresse-1']){print $_POST['adresse-1'];}?>" placeholder="" required >      
                </div>
                <div class="col-md-6">
                <label for="adresse2">Adresse 2</label>
                <input type="text" name="adresse-2" class="form-control" id="adresse2" value="<?php if (isset($_POST['adresse-2'])&& $_POST['adresse-2']){print $_POST['adresse-2'];}?>" placeholder="" no-required>
                </div>
                <div class="col-md-6">
                    <label for="country" class="form-label">* Pays </label>
                    <select name="country" id="country" class="form-control " required>
                        <option value="" selected>Sélectionner un pays : </option>
                            <?php
                            while ($country = $BddByCountry->fetch(PDO::FETCH_ASSOC))
                            {
                            ?>
                        <option value="<?= $country["libelle_pays"] ?>" <?php if($country["libelle_pays"]=="France (métropolitaine)") {print "selected";} ?>><?= $country["libelle_pays"] ?></option>
                            <?php
                            }
                            $BddByCountry->closeCursor();
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ville">* Ville :</label>
                    <input type="text" name="ville" class="form-control  " id="ville" value="<?php if (isset($_POST['ville'])&& $_POST['ville']){print $_POST['ville'];}?>" placeholder="" required>
                </div>
                <div class="col-md-3">
                    <label for="cp">* Code Postal :</label>
                    <input type="text" name="code-postal" class="form-control  " id="cp" value="<?php if (isset($_POST['code-postal'])&& $_POST['code-postal']){print $_POST['code-postal'];}?>" placeholder="" required>
                </div>
                
                <?php if (isset($_GET["patient"])&&$_GET["patient"]=="register") { ?>
                <div class="col-md-6">
                    <label for="mutual" >* Mutuelle :</label>
                    <select name="mutual" id="mutual" class="form-control need-valid" required>
                        <option value="NULL">Pas de mutuelle</option>
                        <?php 
                        foreach($dao->getNameMutuelle() as $item){
                            ?>
                            <option value="<?= $item["idMutuelle"] ?>"><?= $item["nom"] ?></option>
                        <?php
                        } 
                        ?>                        
                    </select>
                </div>
                <div class="col-md-6">     
                    <label for="practitioner" >* Votre praticien </label>
                    <select name="practitioner" id="practitioner" class="form-control " required>
                        <option value="NULL"> Pas encore de praticien</option>
                        <?php 
                        foreach($dao->getPractitioner() as $item){
                            ?>
                            <option value="<?= $item['id'] ?>"><?= $item["nom"]." ".$item["prenom"]  ?></option>
                        <?php
                        } 
                        ?>
                    </select>
                </div>
                 <?php } ?>  
                 <div class="col-12">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="invalidCheck" required>
                    <label class="form-check-label" for="invalidCheck">
                        Agree to terms and conditions
                    </label>
                    <div class="invalid-feedback">
                        Rester connecter
                    </div>
                    </div>
                </div>  
                    <div class="col-12">
                       <button class="btn btn-primary" type="submit" name="create-an-account">Créer un compte</button>
                    </div>         
             </form>

                <script type="text/javascript">
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
                <script type="text/javascript">
                     
                    function validateEmail(email) {
                        const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                        return re.test(email);
                    }
                    var mailValide=false;
                 
                        function validate() {
                            const $result = $("#result");
                            const email = $("#email-register").val();
                            $result.text("");
                            if (validateEmail(email)) {
                            mailValide=true;
                           
                            } else {
                              $result.text("Attention : " +email + ",  ne respecte pas le bon format d'une adresse mail valide.");
                                $result.css("color", "orange");
                              
                            }
                           return mailValide;
                        }
                        $(".mail-register").on("change", validate);

                    function compareMail(){
                        if(mailValide===true){
                        if($('#email-register').val()!==$('#repeat-email-register').val()){
                                alert("les adresses mail ne correspondent pas !");
                            }                            
                        }else{
                            alert("Vérifier le bon format de l'adresse mail ")
                        }
                    }
                    $('#repeat-email-register').on("change",compareMail);
                   
                                
                    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</body>
</html>

