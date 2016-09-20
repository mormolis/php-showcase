//================================== FILE 3 =======================================
// Shows the available cars on a selected time span as part of the reservation 
// process. Delivery and Pick up time is not been taken into consideration due 
// to client's request, instead there are 3 different lists. One with those clearly available,
// one with those available but collected the selected collection date and one 
// with those available but delivered the same data set as delivery date.
//=================================================================================

<?php session_start(); 
include_once "db-config.php";
?>
<html lang="en">
	<head>
       <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
	   <link rel="stylesheet" type="text/css" href="dist/bootstrap-clockpicker.min.css">
	   <script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="dist/bootstrap-clockpicker.min.js"></script>
         
<script type="text/javascript">
$('.clockpicker').clockpicker()
	.find('demo-input').change(function(){
		// TODO: time changed
		console.log(this.value);
	});
$('#demo-input').clockpicker({
	autoclose: true
});
</script>
    </head>
    <body>

	<H1>Please select Pickup and Dropoff hour</H1>
	
	
<?php



    $pickup_date = $_POST['pickUpDate'];
    $dropoff_date = $_POST['dropOffDate'];
	$but = $_GET['but'];
	

    //********************************************
    //ανοιγω σεσσιον για τις μεταβλητες που πρέπει να περάσουν
    //στα επόμενα σκριπτάκια
    //********************************************
    
    $_SESSION["pickup_date"] = $pickup_date;
    $_SESSION["dropoff_date"] = $dropoff_date;

    //*********************************************
    if ($pickup_date<=$dropoff_date){
       
        
        try{
			
			//***************************opening Database
  			  $db = new PDO("mysql:host=".PHOST.";"." dbname=".DB_NAME.";", DB_USER, DB_PASS);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//****************************** end of code opening database
			// creating form //
			if ($but==0){
				echo '<form method="post" action="resrvation-details.php">';
			} else {
				echo '<form method="post" action="quickres-details.php">';
			}
			echo '<ul>';
				echo '<li>Pick up hour: <input id="input-a" value="" data-default="20:48" name="pickup_hour" required></li>';
				echo '<li>Drop off hour: <input id="input-b" value="" data-default="20:48" name="dropoff_hour" required></li>';
			echo '</ul>';
			 echo '<h1>Available Cars for the the dates '.$pickup_date .' - '.$dropoff_date.'</h1><br>';
			
				$results = $db->prepare('select avail_cars.plateID, avail_cars.model, avail_cars.cgroup  from avail_cars
						 where  avail_cars.plateID
						NOT IN 
						( select rentals.plateID
						from rentals
						where
						(rentals.pickUpDate <= :pickup_date AND  rentals.dropOffDate >= :pickup_date)
						OR
						(rentals.pickUpDate <= :dropoff_date AND rentals.dropOffDate >= :dropoff_date)
						OR
						(rentals.pickUpDate >= :pickup_date AND rentals.dropOffDate <= :dropoff_date)
						) order by avail_cars.cgroup;');
		    
                        
            $results->execute(array(	'pickup_date'=>$pickup_date,
										  'dropoff_date'=>$dropoff_date));
			
            //Δημιουργία της λίστας διαθέσημων αμαξιών
            
           
			while ($row = $results->fetch()){
                
                echo ("<input type=\"radio\" name=\"car-hire\" required value=\"$row[0]\"> $row[0] $row[1] | <strong>Group: $row[2]</strong> <br>");
            }
						            
           
            //εμφανίζουμε τα αμάξια που επιστρέφονται σήμερα
            //και δε δίδονται μέσα στο χρονικό διάστημα της επόμενης μέρας από τη μέρα κράτησης
            //και μέχρι το τέλος της κράτησης 
          
           $results2 = $db->prepare('select rentals.plateID, avail_cars.model, avail_cars.cgroup, rentals.pickUpHour, rentals.pickUpLoc
									from rentals join avail_cars on rentals.plateID = avail_cars.plateID
									where
									rentals.dropOffDate = :pickup_date
									AND rentals.plateID NOT IN
									(select rentals.plateID
									from rentals
									where 
									rentals.pickUpDate < :dropoff_date AND rentals.dropOffDate > :pickup_date);');
            
			$results2->execute(array('pickup_date' =>$pickup_date,
									 'dropoff_date'=>$dropoff_date
									 ));
            
              echo '<br><br><br><h2> '.$pickup_date .' Dropoffs available :</h2><br><br><br>';  
            while ($row1 = $results2->fetch()){
                
                echo ("<input type=\"radio\" name=\"car-hire\" required value=\"$row1[0]\"> $row1[0] $row1[1] <strong>Group: $row1[2]</strong> | Estimated pick up time: $row1[3] from $row1[4] <br>");
            }
			echo '<br><h2> must be delivered the same day of dropoff :</h2>';
			
			$results3 = $db->prepare('select rentals.plateID, avail_cars.model, avail_cars.cgroup, rentals.pickUpHour, rentals.pickUpLoc
									from rentals join avail_cars on rentals.plateID = avail_cars.plateID
									where
									rentals.pickUpDate = :dropoff_date
									AND rentals.plateID NOT IN
									(select rentals.plateID
									from rentals
									where 
									rentals.pickUpDate < :dropoff_date AND rentals.dropOffDate > :pickup_date);');
            
			$results3->execute(array('pickup_date' =>$pickup_date,
									 'dropoff_date'=>$dropoff_date
									 ));
			
			while ($row1 = $results3->fetch()){
                
                echo ("<input type=\"radio\" name=\"car-hire\" required value=\"$row1[0]\"> $row1[0] $row1[1] <strong>Group: $row1[2]</strong> | Estimated pick up time: $row1[3] from $row1[4] <br>");
            }
         
            echo '<br><br><br><button type=\"submit\" value=\"Book Selected\"> Book Selected</button>';
			$db = null;
        }   catch (PDOException $e) {
             echo $e->getMessage();
            }
        
        
    } else {
        echo 'fail!';
    }

    
    
    
    ?>


<script type="text/javascript">
var input = $('#input-a');
input.clockpicker({
    autoclose: true
});



var input = $('#input-b');
input.clockpicker({
    autoclose: true
});

</script>
        
    </body>
	
	
</html>
