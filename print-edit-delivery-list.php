//=============================== FILE 1 =============================================
// It displays an editable form of the cars that should be delivered the selected date.
// The form is editable so that the changes can be submitted to the connected database
//====================================================================================

<?php
session_start();
include_once "../db-config.php";
  $pickUpDate = $_SESSION['dateToCheck'];

 try{
  $db = new PDO("mysql:host=".PHOST.";"." dbname=".DB_NAME.";", DB_USER, DB_PASS);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $results = $db->prepare('Select rentals.pickUpHour, avail_cars.cgroup, datediff(rentals.dropOffDate, rentals.pickUpDate) AS days, rentals.pickUpLoc, clients.clSurname, rentals.plateID, rentals.contractNu, rentals.moneySum, rentals.affiliateName, rentals.rentalID
                          FROM 
                          avail_cars join rentals join clients 
                          ON
                          avail_cars.plateID=rentals.plateID AND rentals.clientID = clients.clientID
                          WHERE
                          rentals.pickUpDate = :pickUpDate ORDER BY rentals.pickUpHour; ');
  $results->execute(array('pickUpDate'=>$pickUpDate));
  
  $newDate = date("d/m", strtotime($pickUpDate));
  $day = date('l', $pickUpDate);

  $affiliatesDB = $db->prepare('select affiliates.affiliateName from affiliates;');
  $affiliatesDB->execute();
 } catch (PDOException $e) {
             echo $e->getMessage();
            }
?>

<html>
  <head>
    
    <style media="screen">
  .noPrint{ display: block; }
  .yesPrint{ display: block !important; }
</style>

<style media="print">
  .noPrint{ display: none; }
  .yesPrint{ display: block !important; }
</style>
  </head>  
    <body>
        
<div class= "yesPrint">
 <table border="1">
  <tr><th colspan="9"><h1><?php echo $day." ". $newDate ?> - Delivery</h1></th></tr>
 <tr>
 <th>Time</th>
 <th>Car type</th>
  <th>Days</th>
   <th>Deliver to</th>
    <th>Client name</th>
     <th>Car reg.</th>
      <th>Contract no</th>
       <th>Payment</th>
        <th>Booked by</th>
 </tr>
<form method="post" action="edit.php">
 <?php
$strToEcho = "";
while($row1 = $affiliatesDB->fetch()){ 
			$strToEcho .= "<option>{$row1[0]}</option>  <br>"; }

$i=0;
    while($row = $results->fetch()){
      echo "<tr>
            <td>{$row[0]}</td>
            <td>{$row[1]}</td>
            <td>{$row[2]}</td>
            <td>{$row[3]}</td>
            <td>{$row[4]}</td>
            <td>  <input type=\"text\" name=\"plateID[]\" value=\"$row[5]\"}</td>
            <td>{$row[6]}</td>
            <td><input type=\"text\" name=\"money[]\" value=\"$row[7]\"}</td>
            <td><select id=\"affiliate\" name=\"affiliate[]\">";

		echo $strToEcho;
		echo "<option selected>{$row[8]}</option></td> </tr>";
           
	$rentalID[$i] = $row[9];
	$i++;
    }
	
	$_SESSION['rentalID'] = $rentalID;
	
    
    $db = null;
  ?>
 

 </table>
 </div>
        <br />
<input type="submit" value="Edit"></form>
        <input TYPE="button" value="PRINT" onClick="window.print()">
    </body>
    
    
</html>



