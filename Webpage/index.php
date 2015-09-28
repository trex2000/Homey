<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Barnikanak a Homey-a">
	<meta name="keywords" content="Barni, Barnika, Barney, Homey">
	<meta name="author" content="Csakis Barney">

	<title>Homey</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="style.css">

  </head>
  <body>
  <div><img src="/img/homey.png" alt="Homey"></div>

  <?php
require_once('config.inc.php');
error_reporting(E_ALL);
//number of seconds to wait between 2 consecutive commands
define("SECONDS_MIN", 10); 


function write_to_log($priority, $str, $echoed)
{
	$curr_time= gmdate("Y-m-d H:i:s ",time());
	syslog($priority, $str);
	if ($echoed)
	{
		$str='<font color="#E0E0D1">['.$curr_time.']:</font> '.$str;
		echo '<br>'.$str;
	}
	else
	{
		//do nothing
	}
	
	
}

function notify_homey_app_about_DB_update()
{
	//----- SHARED MEMORY CONFIGURATION -----
	$SEMAPHORE_KEY = 291623581;   			//Semaphore unique key
	$SHARED_MEMORY_KEY = 672213396;   	//Shared memory unique key

	//Create the semaphore
	//BUG-os a kibaszott szemafor a PHP-ben, emiatt ez a szar nem fog menni. More info:
	//bugs.php.net/bug.php?id=61608
/*	$semaphore_id = sem_get($SEMAPHORE_KEY);		//Creates, or gets if already present, a semaphore
	if ($semaphore_id === false)
	{
		echo "<br>Failed to create semaphore.  Reason: $php_errormsg<br />";
		return;		
	}

	//Acquire the semaphore
	if (!sem_acquire($semaphore_id))						//If not available this will stall until the semaphore is released by the other process
	{
		echo "<br>Failed to acquire semaphore $semaphore_id<br />";
		sem_remove($semaphore_id);						//Use even if we didn't create the semaphore as something has gone wrong and its usually debugging so lets no lock up this semaphore key
		return;
	}

	//We have exclusive access to the shared memory (the other process is unable to aquire the semaphore until we release it)
*/
	//Setup access to the shared memory
	$shared_memory_id = shmop_open($SHARED_MEMORY_KEY, "c", 0666, 1);	//Shared memory key, flags, permissions, size (permissions & size are 0 to open an existing memory segment)
																																	//flags: "a" open an existing shared memory segment for read only, "w" read and write to a shared memory segment, c to create or open if already exists
	if (empty($shared_memory_id))
	{
		echo "<br>Failed to open shared memory.<br />";			//<<<< THIS WILL HAPPEN IF THE C APPLICATION HASN'T CREATED THE SHARED MEMORY OR IF IT HAS BEEN SHUTDOWN AND DELETED THE SHARED MEMORY
	}
	else
	{
		//echo "<br>Shared memory size: ".shmop_size($shared_memory_id)." bytes<br />";

			//Convert the array of byte values to a byte string
			$shared_memory_data = '1';
			//echo "<br>Writing bytes:".$shared_memory_data."<br />";
			$bytes_written=shmop_write($shared_memory_id, $shared_memory_data, 0);			//Shared memory id, string to write, Index to start writing from
			write_to_log(LOG_INFO, "C app notified:".$bytes_written."<br />", 1);
			echo 
		//Detach from the shared memory
		shmop_close($shared_memory_id);
	}
/*
	//Release the semaphore
	if (!sem_release($semaphore_id))				//Must be called after sem_acquire() so that another process can acquire the semaphore
		echo "<br>Failed to release $semaphore_id semaphore<br />";
*/
	//Delete the shared memory (only do this if we created it and its no longer being used by another process)
	//shmop_delete($shared_memory_id);

	//Delete the semaphore (use only if none of your processes require the semaphore anymore)
	//sem_remove($semaphore_id);				//Destroy the semaphore for all processes
}


if ( (isset($_POST['action'])) && ($_POST['action'] == 'submitted') && (!isset($_POST['Back'])) )
{
    //echo "Page submitted<BR>";
	$con=mysqli_connect($config['host'],$config['username'],$config['password'],$config['dbname']);
	// Check connection
	if (mysqli_connect_errno())
	{
		write_to_log(LOG_ERR, "<br>Failed to connect to MySQL: " . mysqli_connect_error(), 1);
		die;
	}
	else
	{
		//var_dump($_POST);
		$result = mysqli_query($con,"SELECT * FROM devices");
		if (!$result)
		{
			write_to_log(LOG_ERR, "<br>Mysql query failed:".mysqli_error($con), 1);
			die;
		}
		else
		{
			//go on
		}
	
		echo "<br>";
		if($_POST['force_update']=="1")
		{
			//must update all devices regardless of last state or last update time
			write_to_log(LOG_INFO, "<font color='#CC3300'> Sending command to all devices regardless of last state or update time!</font>", 1);
			$index=0;
			while($row = mysqli_fetch_array($result))
			{
				$testval="Status".$row['id'];
				write_to_log(LOG_INFO, 'Modified '.$row['Name'].' from '.$row['LastState'].' to '.$_POST[$testval].' <font color="#33CC33">-ok </font>', 1);
				$modified[$index]['id']=$row['id'];
				$modified[$index]['newstatus']= $_POST[$testval];
				$index++;
			}
		}
		else
		{
			//finding device which was modified and update only that one
			//array index of all of the modified status
			$index=0;
			while($row = mysqli_fetch_array($result))
			{
				
				//test if the value was modified
				$testval="Status".$row['id'];
				if ($_POST[$testval]!=$row['LastState'])
				{
					write_to_log(LOG_INFO, 'Modified '.$row['Name'].' from '.$row['LastState'].' to '.$_POST[$testval].' <font color="#33CC33">-ok </font>', 1);
					$diff = time() - $row['LastModified'];
					//echo "Diff:".$diff;
					if (($diff<SECONDS_MIN) || $diff<0)
					{
						/*wait at least SECONDS_MIN before issuing 2 consecutive commands to the relays*/
						if ($diff>=0)
						{
							write_to_log(LOG_INFO, " - ignored, you must still wait ".(SECONDS_MIN-$diff)." seconds",1);
						}
						else
						{
							write_to_log(LOG_INFO, " - ignored, last saved date is in the future", 1);
						}
					}
					else
					{
					   write_to_log (LOG_INFO, "Command accepted",1); 
					   $modified[$index]['id']=$row['id'];
					   $modified[$index]['newstatus']= $_POST[$testval];
					   $index++;
					}
				}
			}//end while
		}
		
		
	//var_dump($modified);
	//update the Database with the new values
	for ($i=0; $i<$index; $i++)
	{
		$query_string =" UPDATE devices SET  LastState=".$modified[$i]['newstatus'].", LastModified=".time()." WHERE id=".$modified[$i]['id'];
		//echo "Update query string: ".$query_string;
		//echo "<BR>";
		$result = mysqli_query($con,$query_string);
		if (!$result)
		{
			write_to_log(LOG_ERR, "Query failed. Error:".mysqli_error($con),1);
		}
		else
		{
			//no thing
		}
		//insert into log
		$query_string =" INSERT INTO log (Deviceid, LastState, LastModified) VALUES (".$modified[$i]['id'].",".$modified[$i]['newstatus'].",".time().")";
		//echo "Update query string for log: ".$query_string;
		//echo "<BR>";
		$result = mysqli_query($con,$query_string);
		if (!$result)
		{
			write_to_log(LOG_ERR, "Query failed. Error:".mysqli_error($con),1);
		}
		else
		{
			//no thing
		}
		
		
	}
	
	//	Issue the commands to the X10 network
	//build up the query string to select only the devices which were modified	
	$query_string = "SELECT * FROM devices WHERE id in (";
	$i=0;
	while ($i<$index)
	{
		$query_string=$query_string.$modified[$i]['id'];
		if ($i!=$index-1)
		{
			//separate values with ,
			$query_string=$query_string.",";
		}
		else
		{
			// end the query string with )
			$query_string=$query_string.")";
		}
		$i++;
	}//end while
	
	//echo "Query string for devices that must be commanded: ".$query_string;
	$result = mysqli_query($con,$query_string);
	$x10command="";
	$nr_of_results=0;
	while($row = mysqli_fetch_array($result))
	{
		//building up the string
		$x10command = $row['Housecode'].$row['Unitcode'];
		write_to_log(LOG_INFO,'Setting '.$row['Name'].' ('.$x10command.')'.' relay to '.$row['LastState'],1);
		//call shell app
		if ($row['LastState'])
		{
			$shell_exec = "/var/www/html/execheyu.sh on ".$x10command;
		}
		else
		{
			$shell_exec = "/var/www/html/execheyu.sh off ".$x10command;
		}
		write_to_log (LOG_INFO, $shell_exec,0);
		$resp = shell_exec($shell_exec);
		write_to_log( LOG_INFO, $resp,0);
		if ($row['SupportsStatus'])
		{
			write_to_log (LOG_INFO, "Reading back the status:",1);
			$shell_exec = "/var/www/html/heyustatus.sh ".$x10command;
			write_to_log(LOG_INFO, $shell_exec,1);
			//TODO:  modify back the lsat status in case the status command is		 incorrect. 
			//TODO:  check how does the heyu status response command look like			
		}
		else
		{
			//do nothing
		}
		$nr_of_results++;
	}//end while
	if ($nr_of_results==0)
	{
		write_to_log(LOG_INFO,  "No status change was performed", 1);
	}
	else
	{
		write_to_log (LOG_INFO,  'Summary:   - number of status changes: '.($nr_of_results),1)	;
	}
	}//mysql connect ok
	
	//set the shared memory to '1'
	notify_homey_app_about_DB_update();
	
	mysqli_close($con);
	echo "<form method='post' action='$PHP_SELF'>";
	echo "<input type='submit' name='Back' value='Back'>"; 
	echo "</FORM>";
  //END!!
} 
else 
{
	unset($_POST['Back']);
	$con=mysqli_connect($config['host'],$config['username'],$config['password'],$config['dbname']);
	// Check connection
	if (mysqli_connect_errno())
	{
		write_to_log(LOG_ERR, "Failed to connect to MySQL: " . mysqli_connect_error(),1);
	}
	else
	{
	}
	$result = mysqli_query($con,"SELECT * FROM devices");
	
	echo "<form method='post' action='$PHP_SELF'>";
	echo "<font color='#1199CC'><h3>Configured devices:</h3></font>";
	echo "<br>";
	echo '<div class="container-fluid">';
	echo '<div class="row">';
	while($row = mysqli_fetch_array($result))
	{
		echo '<div class="col-md-4">';
		echo "<h4>".$row['Name']."</h4>";
		echo '<p class="text-primary">';
		echo "- device id: ".$row['id'];
		echo '</p>';
		echo '<p class="text-primary">';
		echo "- house/unit Code: ".$row['Housecode'].$row['Unitcode'];
		echo "</p>";
		echo '<p class="text-primary">- last known state: ';
		if ($row['LastState'])
		{
			echo '<span class="text-success">';
			echo "On";
			echo '</span></p>';
		}
		else
		{
			echo '<span class="text-danger">';
			echo "Off";
			echo '</span></p>';
		}
		echo '<p class="text-primary">';
		echo "- last modified: ";
		echo gmdate("Y-m-d H:i:s ", $row['LastModified']);
		echo " (GMT)";
		echo '</p>';
		
		echo '<p class="text-primary">- supports status query: ';
		if ($row['SupportsStatus'])
		{
			echo '<span class="text-success">';
			echo "Yes";
			echo '</span></p>';
		}
		else
		{
			echo '<span class="text-danger">';
			echo "no";
			echo '</span></p>';
		}
		// populate radio button
		if ($row['LastState'])
		{
			$button_checked_on="checked";
			$button_checked_off="";
		}
		else
		{
			$button_checked_off="checked";//nothing
			$button_checked_on="";
		}
		echo "<label class='radio-inline'>";
			echo "<input type='radio' name='Status".$row['id']."' value='1' ".$button_checked_on."> On";
		echo "</label>";
		echo "<label class='radio-inline'>";
			echo "<input type='radio' name='Status".$row['id']."' value='0' ".$button_checked_off."> Off";
		echo "</label>";
		echo "<br>";
		echo "<button type='submit' class='btn btn-primary' name='Submit".$row['id']."'>Modify</button>";
		echo "</div>";
	}
	echo "</div>";
	mysqli_close($con);
	echo "</div>";
	echo "<label class='checkbox-inline'>";
		echo "<input type='checkbox' name='force_update' value='1'> force status update regardless of last state or last time of update";
	echo "</label>";
	echo "<input type='hidden' name='action' value='submitted'>";
	echo "</FORM>";
}
?>
  </body>
</html>