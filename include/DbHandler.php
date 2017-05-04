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

	public function addNewUser($username,$email,$hashedPassword){
		$stmt = "INSERT INTO USERS VALUES('','$username','$email','$hashedPassword')";
		$query = mysqli_query($this->conn,$stmt);
	    mysqli_close ($this->conn);
	    unset($this->conn);
	    return $query;
	}

	public function login($username,$hashedPassword){
		$statement = "SELECT * FROM USERS WHERE USERNAME = '$username' AND PASSWORD = '$hashedPassword'";
		$query = mysqli_query($this->conn,$statement);
	    mysqli_close ($this->conn);
	    unset($this->conn);

	    return mysqli_fetch_array($query);
	}

	function getCurrentBalance($user_id, $account_type){
		$statement = "SELECT * FROM REKENING WHERE USER_ID = '$user_id' AND TYPE = '$account_type'";
		$query = mysqli_query($this->conn,$statement);
	    return mysqli_fetch_array($query);
	}

	function getCashBalance($user_id){
		$statement = "SELECT * FROM REKENING WHERE USER_ID = '$user_id' AND TYPE = 'CASH'";
		$query = mysqli_query($this->conn,$statement);
	    return mysqli_fetch_array($query);
	}

	function setCashBalance($user_id, $currentBalance){
		$stmt = "UPDATE REKENING SET BALANCE = '$currentBalance' WHERE USER_ID = '$user_id' AND TYPE = 'CASH'";
		$query = mysqli_query($this->conn,$stmt);
	    return $query;	
	}

	function getSavingBalance($user_id){
		$statement = "SELECT * FROM REKENING WHERE USER_ID = '$user_id' AND TYPE = 'SAVING'";
		$query = mysqli_query($this->conn,$statement);
	    return mysqli_fetch_array($query);
	}

	function setSavingBalance($user_id, $currentBalance){
		$stmt = "UPDATE REKENING SET BALANCE = '$currentBalance' WHERE USER_ID = '$user_id' AND TYPE = 'SAVING'";
		$query = mysqli_query($this->conn,$stmt);
	    return $query;	
	}

	function setCurrentBalance($user_id, $account_type, $currentBalance){
		$stmt = "UPDATE REKENING SET BALANCE = '$currentBalance' WHERE USER_ID = '$user_id' AND TYPE = '$account_type'";
		$query = mysqli_query($this->conn,$stmt);
	    return $query;	
	}

	function setEMoneyBalance($user_id, $currentBalance){
		$stmt = "UPDATE REKENING SET BALANCE = '$currentBalance' WHERE USER_ID = '$user_id' AND TYPE = 'ELECTRONIC_MONEY'";
		$query = mysqli_query($this->conn,$stmt);
	    return $query;	
	}

	function getEMoneyBalance($user_id){
		$statement = "SELECT * FROM REKENING WHERE USER_ID = '$user_id' AND TYPE = 'ELECTRONIC_MONEY'";
		$query = mysqli_query($this->conn,$statement);
	    return mysqli_fetch_array($query);
	}

	public function addNewTransaction($user_id, $account_type, $type, $amount,$information){
		$currentBalanceQuery = $this->getCurrentBalance($user_id,$account_type);
		if($currentBalanceQuery != null){
			$currentBalance = $currentBalanceQuery['balance'];
			$account_id = $currentBalanceQuery['id'];
			if("EXPENSE" == $type){
				$currentBalance = $currentBalance - $amount;
				if($currentBalance < 0){
					return null;
				}
			}else if("INCOME" == $type){
				$currentBalance = $currentBalance + $amount;
			}else if("WITHDRAW" == $type){
				$currentBalance = $currentBalance - $amount;
				if($currentBalance < 0){
					return null;
				}
				$cashBalanceQuery  = $this->getCashBalance($user_id);
				if($cashBalanceQuery != null){
					$cashBalance = $cashBalanceQuery['balance'];
					$cashBalance = $cashBalance + $amount;
					$this->setCashBalance($user_id,$cashBalance);
				}else{
					return null;
				}
			}else if("SAVING" == $type){
				$currentBalance = $currentBalance - $amount;
				if($currentBalance < 0){
					return null;
				}
				$savinBalanceQuery  = $this->getSavingBalance($user_id);
				if($savinBalanceQuery != null){
					$savingBalance = $savinBalanceQuery['balance'];
					$savingBalance = $savingBalance + $amount;
					$this->setSavingBalance($user_id,$savingBalance);
				}else{
					return null;
				}
			}else if("TRANSFER" == $type){
				$currentBalance = $currentBalance - $amount;
				if($currentBalance < 0){
					return null;
				}
				$eMoneyBalanceQuery  = $this->getEMoneyBalance($user_id);
				if($eMoneyBalanceQuery != null){
					$eMoneyBalance = $eMoneyBalanceQuery['balance'];
					$eMoneyBalance = $eMoneyBalance + $amount;
					$this->setEMoneyBalance($user_id,$eMoneyBalance);
				}else{
					return null;
				}
			}

			if($this->setCurrentBalance($user_id, $account_type, $currentBalance)){
				$date = new DateTime();
				$timeStamp = $date->format('Y-m-d H:i:s');
				$stmt = "INSERT INTO TRANSAKSI VALUES('','$user_id','$account_id','$type','$amount','$timeStamp','$information')";
				$query = mysqli_query($this->conn,$stmt);
	    		mysqli_close ($this->conn);
	    		unset($this->conn);
	    		return $query;
			}else{
				return null;
			}
		}else{
			return null;
		}	
	}

	public function addNewAccount($user_id, $type,$balance){
		$stmt = "INSERT INTO REKENING VALUES('','$user_id','$type','$balance')";
		$query = mysqli_query($this->conn,$stmt);
	    mysqli_close ($this->conn);
	    unset($this->conn);
	    return $query;
	}
 
}
 
?>