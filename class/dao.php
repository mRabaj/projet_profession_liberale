<?php
class DAO {
	private $host="localhost";
	private $db="professionliberal";
	private $user="root"; 
	private $password='';
	private $userWrite="root"; 
	private $passwordWrite='';
	private $bdd;
	private $error="";
	
	 function __construct()
	{ 
	}
	public function getError() {
			return $this->error;
		}
	
	private function connect($write=false){
			
		try
		{
			if ($write) {
					$this->bdd = new PDO('mysql:host='.$this->host.';dbname='.$this->db.';charset=utf8', $this->userWrite, $this->passwordWrite);
				} else {
					$this->bdd= new PDO('mysql:host='.$this->host.';dbname='.$this->db.';charset=utf8',$this->user,$this->password);
				}
			
			
		}
		catch(Exception $e)
		{
			print "Error !:".$e->getMessage()."<br/>";
			die();
		}
	
	}
	
	private function executeQuery($sql) {
			$this->connect();
			$reponse=$this->bdd->query($sql);
			
			if (!$reponse) {
				$this->error=$this->bdd->errorInfo()[2];
			}
			return $reponse->fetchAll(PDO::FETCH_ASSOC);
		}
		
		private function executeNonQuery($sql) {
			$this->connect(true);
			$reponse=$this->bdd->query($sql);
			if (!$reponse) {
				$this->error=$this->bdd->errorInfo()[2];
			}
			return $reponse;
		}
		
	   /**
	   * @param integer $patient
	   * @param integer $practitioner
	   * @returns array
	   */
		public function getPatient() {
			$sql="SELECT `idPatient` AS 'id', `email`,`mot_de_passe` AS 'pwd',nom, prenom  FROM `patient` ORDER BY nom ASC";
			
			return $this->executeQuery($sql);
			}
		
		public function getPractitioner(){
			$sql="SELECT `idPraticien` AS 'id' , `nom`, `prenom`, `email`, `mot_de_passe` AS 'pwd', `code_rpps` FROM `praticien` WHERE 1";
			
			return $this->executeQuery($sql);
		}
		public function getRdv(){
			$sql="SELECT date_heure_debut, date_heure_fin, nom, prenom FROM rendez_vous INNER JOIN patient ON (rendez_vous.idPatient=patient.idPatient)";
			return $this->executeQuery($sql);
		}
		public function getNameMutuelle(){
			$sql="SELECT idMutuelle,nom FROM `mutuelle` ORDER BY nom ASC";
			return $this->executeQuery($sql);
		}

		public function insertPatient($nom,$prenom,$sexe,$nom_naissance,$date_naissance,$portable,$fixe,$email,$adresse1,$adresse2,$code_postal,$ville,$pays,$numero_sociale,$mot_de_passe,$mutual,$praticien) {
			$sql='INSERT INTO `patient`( `nom`, `prenom`, `sexe`, `nom_naissance`, `date_naissance`, `telephone_portable`, `telephone_fixe`, `email`, `adresse1`, `adresse2`, `code_postal`, `ville`, `pays`, `numero_securite_sociale`, `mot_de_passe`,`idMutuelle`, `idPraticien`) VALUES ("'.$nom.'","'.$prenom.'","'.$sexe.'","'.$nom_naissance.'","'.$date_naissance.'","'.$portable.'","'.$fixe.'","'.$email.'","'.$adresse1.'","'.$adresse2.'","'.$code_postal.'","'.$ville.'","'.$pays.'",'.$numero_sociale.',"'.$mot_de_passe.'",'.$mutual.','.$praticien.')';
			return $this->executeNonQuery($sql);
		}
		public function insertPraticien($nom,$prenom,$sexe,$portable,$fixe,$email,$adresse1,$adresse2,$code_postal,$ville,$pays,$mot_de_passe,$code_rpps) {
			$sql='INSERT INTO `praticien`( `nom`, `prenom`, `sexe`, `telephone_portable`, `telephone_fixe`, `email`, `adresse1`, `adresse2`, `code_postal`, `ville`, `pays`, `mot_de_passe`, `code_rpps`) VALUES ("'.$nom.'","'.$prenom.'","'.$sexe.'","'.$portable.'","'.$fixe.'","'.$email.'","'.$adresse1.'","'.$adresse2.'","'.$code_postal.'","'.$ville.'","'.$pays.'","'.$mot_de_passe.'",'.$code_rpps.')';
			return $this->executeNonQuery($sql);
		}
		public function insertAppoitment($idPatient,$idPraticien,$date_heure_debut,$date_heure_fin){
			$sql='INSERT INTO `rendez_vous`( `idPatient`, `idPraticien`, `date_heure_debut`,`date_heure_fin`) VALUES ('.$idPatient.','.$idPraticien.',"'.$date_heure_debut.'","'.$date_heure_fin.'")';
		
		return $this->executeNonQuery($sql);
		}
		public function updatePwdPatient($mot_de_passe,$idPatient){
			$sql='UPDATE `patient` SET `mot_de_passe`="'.$mot_de_passe.'" WHERE `idPatient`='.$idPatient;
			return $this->executeNonQuery($sql);
		}
		public function updatePwdPraticien($mot_de_passe,$idPraticien){
			$sql='UPDATE `praticien` SET `mot_de_passe`="'.$mot_de_passe.'" WHERE `idPraticien`='.$idPraticien;
			return $this->executeNonQuery($sql);
		}

// esteban
		public function getNomPrenom($select="",$id=""){
            $sql="SELECT ".$select." FROM patient LEFT JOIN mutuelle ON (mutuelle.idMutuelle=patient.idMutuelle) LEFT JOIN praticien ON (praticien.idPraticien=patient.idPraticien) WHERE idPatient=".$id ;
            return $this->executeQuery($sql);
        }

        public function insertdocuments($name,$documents,$id) {    
            $extension = substr($name, strpos($name, ".")+1);    
            $sql="INSERT INTO documents VALUES ('',".$id.",'".$name."',NOW(),'','".$documents."','".$extension."')";
            // print $sql;
			return $this->executeNonQuery($sql);
        }

        public function getDocuments($id="") { 
            $sql="SELECT documents.titre as titre, documents.date as dateE, documents.file_blob as hex, documents.extension as extension FROM documents LEFT JOIN patient ON (documents.idPatient=patient.idPatient) WHERE patient.idPatient=".$id ;
			return $this->executeQuery($sql);
        }


        public function getNomPraticien($select="",$id=""){
            $sql="SELECT ".$select." FROM praticien WHERE idPraticien=".$id ;
            return $this->executeQuery($sql);
        }

        public function getInfoPatient($select="",$id=""){
            $sql="SELECT ".$select." FROM patient LEFT JOIN mutuelle ON (patient.idMutuelle=mutuelle.idMutuelle) WHERE idPraticien=".$id;
            // print $sql;
            return $this->executeQuery($sql);
        }

        public function getInfoUpload($select="",$id=""){
            $sql="SELECT ".$select." FROM documents LEFT JOIN patient ON (patient.idPatient=documents.idPatient) LEFT JOIN praticien ON (praticien.idPraticien=patient.idPraticien) WHERE praticien.idPraticien=".$id;
            return $this->executeQuery($sql);
		}
		
		public function getInfoDocument($select="",$id=""){
            $sql="SELECT ".$select." FROM documents WHERE documents.idDocument=".$id;
            return $this->executeQuery($sql);
        }
		
}
	
$dao=new DAO();
	if ($dao->getError()) {
		print "Une erreur s'est produite";
	}



?>