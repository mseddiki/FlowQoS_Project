<!DOCTYPE HTML>
<html>

<?php
$jsonfile = 'outgoing.cfg';
$json = file_get_contents($jsonfile);
$data = json_decode($json, true);

?>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>FlowQos</title>
  <link rel="stylesheet" href="style/style.css" type="text/css" media="screen" />
  <!--script type="text/javascript" src="style/accordian.pack.js"></script-->
</head>

<body>
<div id="name">
    <h1>FlowQoS</h1>
    <h2>Providing Per-Flow QoS for Broadband Access Networks</h2>
</div>
  <div id="basic-container" >
    <div id="test-header" class="container_headings header_highlight"><a href="index.php">Incoming Internet Traffic</a> | Outgoing Internet Traffic </div>
    <div id="test-content">
      <div class="container_child">
        <h1>Please indicate the percentage of bandwidth you wish to allocate for each application.</h1>
        <?php
        $updated=false;
        if($_SERVER["REQUEST_METHOD"] == "POST"){
			$total = 0;
			$isnumeric = true;
			//check whether the values sum up to 90
			foreach($_POST['application_limit'] as $item)
			{
				if(is_numeric($item)){
					$total = $total + $item;
				}else{
					if($item != null){
						$isnumeric = false;
					}
				}
			}
			if ($isnumeric == false){
				echo '<h2>ALLOCATION FIELD MUST BE A NUMBER. PLEASE CHECK NEW ALLOCATION FIELDS AGAIN.</h2>';
			}else if ($total > 100){
				echo '<h2>TOTAL ALLOCATION CANNOT EXCEED 100%. PLEASE CHECK NEW ALLOCATION FIELDS AGAIN.</h2>';
			}else if ($total < 100) {
				echo '<h2>TOTAL ALLOCATION IS LESS THAN 100%. PLEASE CHECK NEW ALLOCATION FIELDS AGAIN.</h2>';
			}else{
				//update
				$arr_rate = array();
				$temp_arr = array();
				$n=0;
				foreach($data as $key => $jsons){
					foreach($jsons as $key => $value){
						if($data['application_limit'][$n]['user_defined']){
							//just in case user changed the name
							if($_POST['user_defined_type'][$n-6] != null){
								$temp_arr['type']=$_POST['user_defined_type'][$n-6]; // minus six since there's six default types
								$temp_arr['rate']=$_POST['application_limit'][$n];
								$temp_arr['user_defined']=$data['application_limit'][$n]['user_defined'];
							}
						}else{
							if($data['application_limit'][$n]['type']=='Others (fixed)'){ //don't change rate for this field
								$temp_arr['type']=$data['application_limit'][$n]['type'];
								$temp_arr['rate']=$data['application_limit'][$n]['rate'];
								$temp_arr['user_defined']=$data['application_limit'][$n]['user_defined'];
							}else{
								$temp_arr['type']=$data['application_limit'][$n]['type'];
								$temp_arr['rate']=$_POST['application_limit'][$n];
								$temp_arr['user_defined']=$data['application_limit'][$n]['user_defined'];
							}
						}
						if($temp_arr != null){
							array_push($arr_rate, $temp_arr);
						}
						$n=$n+1;
						$temp_arr = array();
					}
				}
				if($_POST['new_application_type']!=null){
					$temp_arr['type']=$_POST['new_application_type'];
					$temp_arr['rate']=$_POST['application_limit'][$n];
					$temp_arr['user_defined']=true;
					array_push($arr_rate, $temp_arr);
				}
				$applimit = array("application_limit" => $arr_rate);
				$encodedValue = json_encode($applimit);
				
				//write to config file
				$fp = fopen($jsonfile, "w");
				fwrite($fp, $encodedValue);
				fclose($fp);
				
				//get new data to be used to create table
				$json = file_get_contents($jsonfile);
				$data = json_decode($json, true);
				$updated=true;
			}
		}
        ?>
        <form action="" method="POST">
            <div class="form_settings">
                <table style="width:100%; border-spacing:0;"
                	<tr><th>Application Type</th><th>Current Allocation (%)</th><th>New Allocation (%)</th></tr>
                    <?php
                    $n=0;
                    $total=0;
                    foreach ($data as $key => $jsons) {
                        foreach ($jsons as $key => $value) {
                        	if($data['application_limit'][$n]['user_defined']){
                        		echo '<td><input class="application_limit" type="text" name="user_defined_type[]" value="'.$data['application_limit'][$n]['type'].'"/></td>';
                        	}else{
                        		echo '<tr><td>'.$data['application_limit'][$n]['type'].'</td>';
                        	}
                            echo '<td>'.$data['application_limit'][$n]['rate'].'</td>';
                            if($data['application_limit'][$n]['type']=='Others (fixed)'){
                            	//echo '<td>'.$data['application_limit'][$n]['rate'].'</td></tr>';
                            	echo '<td><input class="application_limit" type="number" name="application_limit[]" min="0" max="100" value="'.$data['application_limit'][$n]['rate'].'" readonly /></td></tr>';
                        	}else{
                        		if($_SERVER["REQUEST_METHOD"] == "POST" && false==$updated){
                        			echo '<td><input class="application_limit" type="number" name="application_limit[]" min="0" max="100" value="'.$_POST['application_limit'][$n].'" /></td></tr>';
                        		}else{
                            		echo '<td><input class="application_limit" type="number" name="application_limit[]" min="0" max="100" value="'.$data['application_limit'][$n]['rate'].'" /></td></tr>';
                            	}
                            }
                            $total = $total + $data['application_limit'][$n]['rate'];
                            $n = $n+1;
                        }
                    }
                    echo '<tr><td><input class="application_limit" type="text" name="new_application_type" value="" /></td><td>0</td>';
                    echo '<td><input class="application_limit" type="number" name="application_limit[]" min="0" max="100" value="'.$_POST['application_limit'][$n].'" /></td></tr>';
                    echo '<tr><th>Total</th><th>'.$total.'</th><th><input class="submit" type="submit" name="rate_submitted" value="submit" /></th></tr>';
                    ?>
                </table>
            </div>
        </form>
        
      </div>
    </div>
  </div>
  <div id="footer">
    <p>FlowQoS-2014</p>
  </div>
</body>
</html>
