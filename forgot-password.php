<?php
	
  require_once("function/functions.php");
  require_once("class/dao.php"); 
	

	$error="";
	$send=false;
	if ($_POST) {
    if (isset($_GET["patient"])&&$_GET["patient"]=="forgot-password") {
    $data=verifyAccountMail($dao->getPatient(),$_POST["mail"]);
    if ($data) {
			//envoi du mail 
            $send=sendMail($data['email'],$data['prenom']." ".$data['nom'],"Réinitialisation du mot de passe",str_replace("###token###",$data['id']."&patient=reset-password",file_get_contents("mail-forgot.html")));

			if ($send===true) {
				
			} else {
				$error=$send;
			}
		}
    }
    if (isset($_GET["praticien"])&&$_GET["praticien"]=="forgot-password") { 
      $data=verifyAccountMail($dao->getPractitioner(),$_POST["mail"]);
      if ($data) {
        //envoi du mail 
              $send=sendMail($data['email'],$data['prenom']." ".$data['nom'],"Réinitialisation du mot de passe",str_replace("###token###",$data['id']."&praticien=reset-password",file_get_contents("mail-forgot.html")));
  
        if ($send===true) {
          
        } else {
          $error=$send;
        }
      }
    }
		
	}
?>

<!DOCTYPE html>
<html lang="fr">
<html>
<head>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<title>Mot de passe perdu</title>
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
 <link rel="stylesheet" href="css/style.css" type="text/css">
 </head>
<body>



 <div class="form-gap"></div>
<div class="container">
<?php if ($send===true) { ?>
		<div class="row">
				<div class="col-lg-12 mb-4">
                  <div class="card bg-success text-white shadow">
                    <div class="card-body">
                      Le mail de réinitialisation de mot de passe vous a été envoyé à votre boîte mail.
                    </div>
                  </div>
                </div>
			</div>
	<?php } else { ?>
	<?php 
	/* si message d'erreur lié à l'inscription alors affichage dans une div */
	if ($error) { ?>
			<div class="row">
				<div class="col-lg-12 mb-4">
                  <div class="card bg-danger text-white shadow">
                    <div class="card-body">
                      <?php print $error;?>
                    </div>
                  </div>
                </div>
			</div>
			<?php } ?>
</nav>
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="text-center">
                  <h3><i class="fa fa-lock fa-4x"></i></h3>
                  <h2 class="text-center">Mot de passe perdu?</h2>
                  <p>Vous pouvez réinitialiser votre mot de passe ici.</p>
                  <div class="panel-body">
    
                    <form id="register-form" role="form" autocomplete="off" class="form" method="post">
    
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                          <input id="email" name="mail" placeholder="Votre adresse email..." class="form-control"  type="email">
                        </div>
                      </div>
                      <div class="form-group">
                        <input name="recover-submit" class="btn btn-lg btn-primary btn-block" value="Réinitialiser mot de passe" type="submit">
                      </div>
                      
                      <input type="hidden" class="hide" name="token" id="token" value=""> 
                    </form>
                    <span class="psw"> <a class="reference" href="index.php"><b> Accueil </b> </a> </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
    </div>
    <?php } ?>
</div>

</body>
</html>