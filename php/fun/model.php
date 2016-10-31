<?php
class Model{
    private $_attributes = array();

    /**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames(){
        return array();
    }

   	/**
	 * Returns the validation rules for attributes.
	 * @see scenario
	 */
	public function rules()	{
		return array();
	}

    /**
	 * Returns all attribute values.
	 * @param array $names list of attributes whose value needs to be returned.
	 * Defaults to null, meaning all attributes as listed in {@link attributeNames} will be returned.
	 * If it is an array, only the attributes in the array will be returned.
	 * @return array attribute values (name=>value).
	 */
	public function getAttributes( $names = null ) {
		$values = array();
		foreach( $this->attributeNames() as $name => $label ) {
	        $values[ $name ] = $this->$name;
        }	

		if(is_array( $names ) ) {
			$values2 = array();
			foreach( $names as $name ) {
	            $values2[ $name ] = isset( $values[ $name ] ) ? $values[ $name ] : null;
            }
			
			return $values2;
		} else {
	        return $values;
        }
	}

	/**
	 * Sets the attribute values in a massive way.
	 * @param array $values attribute values (name=>value) to be set.
	 * @see attributeNames
	 */
	public function setAttributes( $values ) {
		if( !is_array( $values ) )
			return;

		$attributes = $this->attributeNames();

		foreach( $values as $name => $value ) {
			if( isset( $attributes[ $name ] ) ) {
	            $this->_attributes[ $name ] = $value;
            }
		}
	}

    /**
	 * Returns a property value
	 * @param string $name the property name or event name
	 * @throws Exception if the property or event is not defined
	 * @see __set
	 */
	public function __get( $name ) {
		$getter = 'get' . $name;

		$attributes = $this->attributeNames();

        if( isset( $this->_attributes[ $name ] ) ) {
            return $this->_attributes[ $name ];
        }		
		else if( isset( $attributes[ $name ] ) ) {
			return NULL;
		}		 
        else if( method_exists( $this, $getter ) ) {
	        return $this->$getter();
        }	
            
		throw new Exception( 'Property "' . get_class( $this ) . '.' . $name . '" is not defined.' );
	}

	/**
	 * Sets value of a component property.
	 * @param string $name the property name or the event name
	 * @param mixed $value the property value
	 * @throws Exception if the property is not defined or the property is read only.
	 * @see __get
	 */
	public function __set( $name, $value ) {
		$setter = 'set' . $name;
		$attributes = $this->attributeNames();

		if( isset( $attributes[ $name ] ) ) {
			return $this->_attributes[ $name ] = $value;
		} else if( method_exists( $this, $setter ) ) {
	        return $this->$setter( $value );
        }

		throw new Exception( 'Property "' . get_class( $this ) . '.' . $name . '" is not defined.' );		
	}
}
?>
