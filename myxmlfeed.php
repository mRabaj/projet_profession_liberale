<?php
require_once("class/dao.php");

	print json_encode($dao->getRdv());

if ($dao->getError()) {
		print "Une erreur s'est produite";
	}


?>