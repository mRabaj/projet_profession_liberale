<?php

try
{
	// On se connecte à MySQL
	$bdd = new PDO('mysql:host=localhost;dbname=professionliberal;charset=utf8', 'liberal_write', 'liberal_write');
}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : '.$e->getMessage());
}



	$idPatient=$_GET['idPatient'];
	$idPraticien=$_GET['idPraticien'];
	$date_heure_debut=$_GET['date_heure_debut'];

	$bdd->query('INSERT INTO `rendez_vous`( `idPatient`, `idPraticien`, `date_heure_debut`,`date_heure_fin`) VALUES ('.$idPatient.','.$idPraticien.',"'.$date_heure_debut.'",NULL)');

		print '<script>window.alert("Le rendez-vous pour a été enregistrer avec succés.");</script>';    

