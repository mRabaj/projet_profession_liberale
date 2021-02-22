<?php
require_once("class/dao.php");

	print json_encode($dao->getRdvPatientByPraticien($_GET["idPraticien"]));

if ($dao->getError()) {
		print "Une erreur s'est produite";
	}


?>