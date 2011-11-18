# PHP Interlation Library

This library is used to "interlace" a number of arrays into a single array, with a unified "master key".  The unique set of "master keys" from all arrays is used as the keys for the resulting array.  If one of the input arrays is missing a value for a particular master key, it assumes the last known value.  

This has a particular use-case when you only log data when it changes, and you need to perform calculations against a range of data sets over a time period on the assumption that the value was valid for every second within a time period.  This was originally developed for Smith Electric Vehicles Corp as part, within their telematics project.  As the functionality is generic, and not specific to the project, permission was granted for this code to be open-sourced.  A good use case in this situation is current and voltage: if you log these two values only when they change, and want to calculate energy usage, you can interlace the two arrays, and simply use the time difference between array items to multiply current value * voltage value * time to next changed datapoint.

## Usage

### Example code
	<?php
	
	$interlaced = new Interlace();
	
	$array1 = array();
	$array1[] = array( 'timestamp' => '2011-11-18 00:00:00', 'value' => 5 );
	$array1[] = array( 'timestamp' => '2011-11-18 00:00:30', 'value' => 8 );
	$array1[] = array( 'timestamp' => '2011-11-18 00:01:00', 'value' => 6 );
	
	$array2 = array();
	$array2[] = array( 'timestamp' => '2011-11-18 00:00:10', 'value' => 10 );
	$array2[] = array( 'timestamp' => '2011-11-18 00:00:30', 'value' => 12 );
	$array2[] = array( 'timestamp' => '2011-11-18 00:00:35', 'value' => 15 );
	$array2[] = array( 'timestamp' => '2011-11-18 00:00:45', 'value' => 25 );
	$array2[] = array( 'timestamp' => '2011-11-18 00:00:55', 'value' => 29 );
	
	$interlaced = new Interlace();
	$interlaced->addArray( 'datapointa', $array1 )->addArray( 'datapointab', $array2 );
	$interlaced = $interlaced->generate( 'timestamp', 'value' );
	?>
	
### Expected output

	Array
	(
	    [2011-11-18 00:00:00] => Array
	        (
	            [datapointa] => 5
	        )
	
	    [2011-11-18 00:00:10] => Array
	        (
	            [datapointab] => 10
	            [datapointa] => 5
	        )
	
	    [2011-11-18 00:00:30] => Array
	        (
	            [datapointa] => 8
	            [datapointab] => 12
	        )
	
	    [2011-11-18 00:00:35] => Array
	        (
	            [datapointab] => 15
	            [datapointa] => 8
	        )
	
	    [2011-11-18 00:00:45] => Array
	        (
	            [datapointab] => 25
	            [datapointa] => 8
	        )
	
	    [2011-11-18 00:00:55] => Array
	        (
	            [datapointab] => 29
	            [datapointa] => 8
	        )
	
	    [2011-11-18 00:01:00] => Array
	        (
	            [datapointa] => 6
	            [datapointab] => 29
	        )
	
	)
	
### Alternative: Generate and fill

If you are using a timestamp, as above, and want to extrapolate for all seconds between the values, you can use:

	$interlaced = $interlaced->generateAndFill( 'timestamp', 'value' );
	
### Alternative: Day break

Again, if you are using a timestamp as the master key like above, and want to break the resulting array into an array of days, you can pass the resulting array to the dayBreak method.

	<?php
	
	$array1 = array();
	$array1[] = array( 'timestamp' => '2011-11-18 00:00:00', 'value' => 5 );
	$array1[] = array( 'timestamp' => '2011-11-19 00:00:30', 'value' => 8 );
	$array1[] = array( 'timestamp' => '2011-11-20 00:01:00', 'value' => 6 );
	
	$array2 = array();
	$array2[] = array( 'timestamp' => '2011-11-18 00:00:10', 'value' => 10 );
	$array2[] = array( 'timestamp' => '2011-11-18 00:00:30', 'value' => 12 );
	$array2[] = array( 'timestamp' => '2011-11-19 00:00:35', 'value' => 15 );
	$array2[] = array( 'timestamp' => '2011-11-19 00:00:45', 'value' => 25 );
	$array2[] = array( 'timestamp' => '2011-11-20 00:00:55', 'value' => 29 );
	
	require_once( 'interlation.class.php' );
	$interlaced = new Interlace(); 
	$interlaced->addArray( 'datapointa', $array1 )->addArray( 'datapointab', $array2 );
	$interlaced = $interlaced->dayBreak( $interlaced->generate( 'timestamp', 'value' ) );
	
	?>
	
	Array
	(
	    [2011-11-18] => Array
	        (
	            [2011-11-18 00:00:00] => Array
	                (
	                    [datapointa] => 5
	                )
	
	            [2011-11-18 00:00:10] => Array
	                (
	                    [datapointab] => 10
	                    [datapointa] => 5
	                )
	
	            [2011-11-18 00:00:30] => Array
	                (
	                    [datapointab] => 12
	                    [datapointa] => 5
	                )
	
	        )
	
	    [2011-11-19] => Array
	        (
	            [2011-11-19 00:00:30] => Array
	                (
	                    [datapointa] => 8
	                    [datapointab] => 12
	                )
	
	            [2011-11-19 00:00:35] => Array
	                (
	                    [datapointab] => 15
	                    [datapointa] => 8
	                )
	
	            [2011-11-19 00:00:45] => Array
	                (
	                    [datapointab] => 25
	                    [datapointa] => 8
	                )
	
	        )
	
	    [2011-11-20] => Array
	        (
	            [2011-11-20 00:00:55] => Array
	                (
	                    [datapointab] => 29
	                    [datapointa] => 8
	                )
	
	            [2011-11-20 00:01:00] => Array
	                (
	                    [datapointa] => 6
	                    [datapointab] => 29
	                )
	
	        )
	
	)
