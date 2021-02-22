<?php
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=pays;charset=utf8', "root");
}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : '.$e->getMessage());
}               
    
        $BddByCountry=$bdd->query('SELECT id_pays,libelle_pays from t_pays ORDER BY libelle_pays ASC');
   
?>