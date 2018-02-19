<?php 
	function rollup($conn, $new_day, $limit_time, $id)
	{
		$output = [
			'success' => false,
			'message' => ''
		];

		$today = date("Ymd");
	    $time = date("H:i");

	    if ($time < $new_day) $today = date('Ymd',strtotime("yesterday"));

	    $query = "SELECT * FROM rollup WHERE user_id = '{$id}' AND roll_day = {$today}";

	    $result = mysqli_query($conn, $query);

		//first time
	    if ($result->num_rows === 0) {

	        if ($time > $limit_time) { //late
	            $output['warning'] = "The latest time is {$limit_time}";
	        }

	        $query = "INSERT INTO rollup (user_id, roll_day, first) VALUES ('{$id}', {$today}, '{$time}')";

	        $check = mysqli_query($conn, $query);

	        if (!$check) {
	            $output['message'] = 'Cannot roll up now, try again later';

	            return $output;
	        }
	        $output['success'] = true;
	        $output['message'] = 'Roll up successfully';

	        return $output;
	    }

		//last time
	    $query = "UPDATE rollup SET last = '{$time}' WHERE user_id = '{$id}' AND roll_day = {$today}";

	    $check = mysqli_query($conn, $query);

	    if (!$check) {
	        $output['error'] = 'Cannot roll up now, try again later';

	        return $output;
	    }

	    $output['success'] = true;
	    $output['message'] = 'Roll up successfully';

	    return $output;
	}