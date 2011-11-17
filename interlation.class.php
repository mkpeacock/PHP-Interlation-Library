<?php
/**
 * Interlace multiple arrays, based on a common key, a common key for the data value, and extrapolate previous values where no new values recorded
 * - Special thanks to Smith Electric Vehicles Corp, my employer, for allowing me to open-source this code
 * 
 * @author Michael Peacock
 * @version 1.0
 */
class Interlace {
	
	/**
	 * The resulting, interlaced array
	 * @var array
	 */
	private $interlaced = array();
	
	/**
	 * The arrays that we wish to interlace
	 * @var array
	 */
	private $arrays = array();
	
	private $first;
	private $last;
		
	public function __construct(){}
	
	/**
	 * Add an array to the list of arrays to interlace
	 * @param String $name the name of the array
	 * @param array the array itself
	 * @return Object this
	 */
	public function addArray( $name, $array )
	{
		$this->arrays[ $name ] = $array;
		return $this;
	}
	
	public function getFirst( $field )
	{
		$first = null;
		$arrays = array_values( $this->arrays );
		foreach( $arrays as $array )
		{
			$comp = $array[0][ $field ];
			if( is_null( $first ) || $comp < $first )
			{
				$first = $comp;
			}
		}
		return $first;
	}
	
	public function getLast( $field )
	{
		$last = null;
		$arrays = array_values( $this->arrays );
		foreach( $arrays as $array )
		{
			$count = count( $array );
			$comp = $array[ $count-1 ][ $field ];
			if( is_null ( $last ) || $comp > $last )
			{
				$last = $comp;
			}
		}
		return $last;
	}
	
	public function populateKeysFromField( $field, $valueField=null )
	{
		$populated = array();
		foreach( $this->arrays as $name => $array )
		{
			$new = array();
			foreach( $array as $arr )
			{
				$val = ( ! is_null( $valueField ) ) ? $arr[ $valueField ] : $arr;
				$new[ $arr[ $field ] ] = $val;
			}
			$populated[ $name ] = $new;
			
		}
		$this->arrays = $populated;
	}
	
	/**
	 * Split the interlaced array into multiple arrays, one per day
	 * @param array $interlationArray
	 * @return array
	 */
	public function dayBreak( $interlationArray )
	{
		$tor = array();
		foreach( $interlationArray as $timestamp => $array )
		{
			$date = substr( $timestamp, 0, -9 );
			if( ! array_key_exists( $date, $tor ) )
			{
				$tor[ $date ] = array();
			}
			$tor[ $date ][ $timestamp ] = $array;
		}
		return $tor;
	}
	
	
	/**
	 * Generate the interlaced array
	 * @param String $keyField 
	 * @param String $valueField
	 * @return array
	 */
	public function generate( $keyField, $valueField )
	{
		$this->first = $this->getFirst( $keyField );
		$this->last = $this->getLast( $keyField );
		$this->populateKeysFromField( $keyField );
		
		$previousValues = array_keys( $this->arrays );
		
		$interlation = array();
		foreach( $this->arrays as $key => $array )
		{
			foreach( $array as $arr )
			{
				if( ! array_key_exists( $arr[ $keyField ], $interlation ) )
				{
					$interlation[ $arr[ $keyField ] ] = array();
				}
				
				$interlation[ $arr[ $keyField ] ][ $key ] = $arr[ $valueField ]; 
			}
			
		}
		
		$keys = array_keys( $this->arrays );
		
		// extrapolate array based off previous values
		$newInterlation = array();
		ksort( $interlation );
		$previousKey = null; 
		foreach( $interlation as $key => $array )
		{
			if( ! is_null( $previousKey ) )
			{
				foreach( $keys as $requiredKey )
				{
					if( ! array_key_exists( $requiredKey, $array ) )
					{
						$array[ $requiredKey ] = $newInterlation[ $previousKey ][ $requiredKey ];
					}
				}
			}
			$newInterlation[ $key ] = $array;
			$previousKey = $key;
		}
		ksort( $newInterlation );
		return $newInterlation;
	}
	
	
	/**
	 * Generate an interlaced array and fill for all timestamps within the range of _first_ to _last_
	 * @param String $keyField
	 * @param String $valueField
	 * @return array
	 */
	public function generateAndFill( $keyField, $valueField )
	{
		$this->first = $this->getFirst( $keyField );
		$this->last = $this->getLast( $keyField );
		$this->populateKeysFromField( $keyField );
		
		$previousValues = array_keys( $this->arrays );
		for( $i = $this->first; $i <= $this->last; $i++ )
		{
			$interlation = array();
			
			foreach( $this->arrays as $key => $array )
			{
				
				if( isset( $array[ $i ] ) )
				{
					$previousValues[ $key ] = $array[ $i ][ $valueField ];
				}
				elseif( ! isset( $previousValues[ $key ] ) )
				{
					$previousValues[ $key ] = 0;
				}
				
				$interlation[ $key ] = $previousValues[ $key ];
			}
			
	
			$this->interlaced[ $i ] = $interlation;
		}
		
		
		return $this->interlaced;
		
	}
	
	
	
	
	
}


?>