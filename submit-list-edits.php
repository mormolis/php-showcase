//================================ FILE 2 ==================================
//Takes the data from the form (see FILE 1) and updates the database
//==========================================================================

<?php
session_start();

include_once "../db-config.php";

$plateID = $_POST['plateID'];
$money = $_POST['money'];
$affiliate = $_POST['affiliate'];
for($i=0; $i<=$size; $i++){
echo "<br>{$affiliate[$i]}<br>";}

$rentalID = $_SESSION['rentalID'];


$size = count($plateID) - 1;

for ($i=0; $i<=$size; $i++){
	echo  "<br>".$i." ".$plateID[$i]." ".$rentalID[$i];
}


try{
  $db = new PDO("mysql:host=".PHOST.";"." dbname=".DB_NAME.";", DB_USER, DB_PASS);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $results = $db->prepare('UPDATE rentals
				set rentals.plateID = :plateID, rentals.moneySum= :money, rentals.affiliateName= :affiliate
				WHERE rentals.rentalID = :rentalID;');
  for ($i=0; $i<=$size; $i++){
    $results->execute(array('plateID'=>$plateID[$i],
				'money'=>$money[$i],
				'affiliate'=>$affiliate[$i],
			'rentalID'=>$rentalID[$i]));
	

  }
$db = null;

  echo "success!";
} catch (PDOException $e) {
             echo $e->getMessage();
		echo "<br><br> You probably enterd wrong Plate number. Please go back and check it again"; 	
            }



session_unset();
session_destroy(); 

?>
