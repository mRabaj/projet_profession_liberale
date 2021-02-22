<?php

require 'vendor/autoload.php';

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	
	  /* envoi de l'email de confirmation d'inscription ou de mot de passe oublié */
	function sendMail($recipient,$recipient_name,$subject,$body) {
		$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
		try {
			//Server settings
			$mail->SMTPDebug = 0;                                 // Enable verbose debug output
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = '';                 // SMTP username
			$mail->Password = '';                           // SMTP password
			$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 465;                                    // TCP port to connect to
			$mail->CharSet = 'UTF-8';
			//Recipients
			$mail->setFrom('noreply@professionliberal.com', 'profession libérale');
			$mail->addAddress($recipient, $recipient_name);     // Add a recipient


			//Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			return true;
		} catch (Exception $e) {
			//return 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
			return "Le message n'a pas pu être envoyé.";
		}
	}
	
	function verifyAccount($datas,$login,$pwd) {
		$valid=false;
				
		foreach($datas as $value) {
	
			if ($login==$value['email']) {
				if (password_verify($pwd,$value['pwd'])) {	
					$valid=md5($value['prenom']."-".$value['nom']."-".$value['email']);
					$_SESSION['id']=$value['id']; 
				}
				else{
					print "mauvais mot de passe !";
				}
				
			}
		}
		return $valid;
	}

	function verifyAccountMail($datas,$login) {
		$valid=false;
		foreach($datas as $value) {
			if ($login==$value['email']) {
				$valid=$value;
			}
		}
		
		return $valid;
	}

	
function valideNir($nir){
 
	/*
	 * ####################################################################################################################
	 * Par GV le 21 / 01 / 2014. Dans la REGEX, chaque ligne représente un groupe de règles ci-dessous.
	 * ####################################################################################################################
	 * Position 1 : 1 pour un homme, 2 pour une femme, 3 pour les personnes étrangères en cours d’immatriculation,
	 * 7 et 8 pour les numéros provisoires
	 * ####################################################################################################################
	 * Position 2 et 3 : Les deux derniers chiffres de l'année de naissance, de 00 à 99
	 * ####################################################################################################################
	 * Position 4 et 5 : Mois de naissance, de 01 (janvier) à 12 (décembre), de 20 à 30 et de 50 à 99 pour les
	 * personnes dont la pièce d'état civil ne précise pas le mois de naissance, de 31 à 42 pour celle dont la pièce
	 * d'état civile est incomplète mais précise quand même le mois de naissance
	 * ####################################################################################################################
	 * Position 6 à 10 : Trois cas de figures
	 * CAS 1 :
	 * Position 6 et 7 : Département de naissance métropolitain, de 01 à 95 (plus 2A ou 2B pour la Corse)
	 * Dans des cas exceptionnels, il est possible de trouver le numéro 96 qui correspondait à la Tunisie avant 1956.
	 * Position 8, 9 et 10 : Numéro d'ordre de naissance dans le département, de 001 à 989 ou 990
	 * CAS 2 :
	 * Position 6, 7 et 8 : Département de naissance Outre-mer, de 970 à 989
	 * Position 9 et 10 : Numéro d'ordre de naissance dans le département, de 01 à 89, ou 90
	 * CAS 3 :
	 * Position 6 et 7 : Naissance hors de France, une seule valeur : 99
	 * Position 8, 9 et 10 : Identifiant du pays de naissance, de 001 à 989, ou 990
	 * ####################################################################################################################
	 * Position 11, 12 et 13 : Numéro d'ordre de l'acte de naissance dans le mois et la commune (ou pays) de 001 à 999
	 * ####################################################################################################################
	 * Position 14 et 15 : Clé de contrôle, de 01 à 97 (Non contrôlé dans ce cas)
	 * ####################################################################################################################
	 */
	$regexp = '/^
	([1-37-8])
	([0-9]{2})
	(0[0-9]|[2-35-9][0-9]|[14][0-2])
	((0[1-9]|[1-8][0-9]|9[0-69]|2[abAB])(00[1-9]|0[1-9][0-9]|[1-8][0-9]{2}|9[0-8][0-9]|990)|(9[78][0-9])(0[1-9]|[1-8][0-9]|90))
	(00[1-9]|0[1-9][0-9]|[1-9][0-9]{2})
	(0[1-9]|[1-8][0-9]|9[0-7])
	$/x';

	return preg_match($regexp, $nir) > 0;
}
	

	// $size : longueur du mot passe voulue
	function Genere_Password($size)
	{
		$password="";
		// Initialisation des caractères utilisables
		$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

		for($i=0;$i<$size;$i++)
		{
			$password.= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
		}

		return $password;
	}





    ?>