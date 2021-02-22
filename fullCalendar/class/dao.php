<?php
class DAO {
	private $host="localhost";
	private $db="professionliberal";
	private $user="liberal_select"; 
	private $password='liberal_select';
	private $userWrite="liberal_write"; 
	private $passwordWrite='liberal_write';
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
        public function getPatient() {
			$sql="SELECT `idPatient` AS 'id', `email`,`mot_de_passe` AS 'pwd',nom, prenom  FROM `patient` ORDER BY nom ASC";
			return $this->executeQuery($sql);
			}
        public function getRdv(){
			$sql="SELECT date_heure_debut AS 'start', date_heure_fin AS 'end',CONCAT(nom,' ',prenom) AS 'title' FROM rendez_vous INNER JOIN patient ON (rendez_vous.idPatient=patient.idPatient)";
			return $this->executeQuery($sql);
		}
		public function getRdv2(){
			$sql="SELECT idPatient as 'id',date_heure_debut AS 'start' FROM rendez_vous";
			return $this->executeQuery($sql);
		}
		
		public function insertAppoitment($idPatient,$idPraticien,$date_heure_debut){
				$sql='INSERT INTO `rendez_vous`( `idPatient`, `idPraticien`, `date_heure_debut`,`date_heure_fin`) VALUES ('.$idPatient.','.$idPraticien.',"'.$date_heure_debut.'",NULL)';
		return $this->executeNonQuery($sql);
			
			
		}
		
        
}

$dao=new DAO();
	if ($dao->getError()) {
		print "Une erreur s'est produite";
	}

