<?php

class DbHandler {
 
    private $conn;
 
    function __construct() {
        require_once dirname(__FILE__) . './DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
	function insertData($uniquid, $code, $gender, $age){
		$stmt = $this->conn->prepare("INSERT INTO data VALUES ('$uniquid','$code','$gender','$age')");
		
		$stmt->execute();
		$tasks = $stmt->get_result();
        $stmt->close();
		return $tasks;
	}
	
	function updateData($uniquid, $code, $gender, $age){
		$stmt = $this->conn->prepare("UPDATE data SET code='$code', gender='$gender',age='$age' WHERE uniquid='$uniquid'");
		
		$stmt->execute();
		$tasks = $stmt->get_result();
        $stmt->close();
		return $tasks;
	}
	/**
	* Fetching hasil liga mingguan
	*/
	public function getHasil(){
		$stmt = $this->conn->prepare("SELECT idUrut, hari, tanggal, tim1, skor, tim2 FROM hasil ORDER BY idUrut ASC");
		
		$stmt->execute();
		$tasks = $stmt->get_result();
        $stmt->close();
		
        return $tasks;		

	}
	
	public function getHasilById($idUrut){
		$stmt = $this->conn->prepare("SELECT idUrut, hari, tanggal, tim1, skor, tim2 FROM hasil where idUrut = $idUrut ORDER BY idUrut ASC");
		
		$stmt->execute();
		$tasks = $stmt->get_result();
        $stmt->close();
		
        return $tasks;		

	}
	
	public function getPekerjaan(){
		$stmt = $this->conn->prepare("SELECT NIM, NAMA_PERUSAHAAN, BIDANG_PEKERJAAN, GAJI FROM alumni_pekerjaan");
		//$stmt = $this->conn->prepare("SELECT idUrut, hari, tanggal, tim1, skor, tim2 FROM hasil ORDER BY idUrut ASC");
		
		$stmt->execute();
		$tasks = $stmt->get_result();
        $stmt->close();
		
        return $tasks;		

	}
	
	public function getKamus(){
		$stmt = $this->conn->prepare("SELECT WORD, KETERANGAN FROM kamus_it");
		//$stmt = $this->conn->prepare("SELECT idUrut, hari, tanggal, tim1, skor, tim2 FROM hasil ORDER BY idUrut ASC");
		
		$stmt->execute();
		$tasks = $stmt->get_result();
        $stmt->close();
		
        return $tasks;		

	}
 
}
 
?>