<?php
class Model{
    private $_attributes = array();	
    private $_errors = array();	// attribute name => array of errors    
	private $_validators;  		// validators

    /**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
	public function attributeNames(){
        return array();
    }

   	/**
	 * Returns the validation rules for attributes.
	 * @return array validation rules to be applied when {@link validate()} is called.
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

		foreach( $this->attributeNames() as $name ) {
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
	 * A safe attribute is one that is associated with a validation rule in the current {@link scenario}.
	 * @see getSafeAttributeNames
	 * @see attributeNames
	 */
	public function setAttributes( $values ) {
		if( !is_array( $values ) )
			return;

		$attributes = array_flip( $this->attributeNames() );

		foreach( $values as $name => $value ) {
			if( isset( $attributes[ $name ] ) ) {
	            $this->$name = $value;
            }
		}
	}

    /**
	 * Returns the named attribute value.
	 * If this is a new record and the attribute is not set before,
	 * the default column value will be returned.
	 * If this record is the result of a query and the attribute is not loaded,
	 * null will be returned.
	 * You may also use $this->AttributeName to obtain the attribute value.
	 * @param string $name the attribute name
	 * @return mixed the attribute value. Null if the attribute is not set or does not exist.
	 * @see hasAttribute
	 */
	public function getAttribute( $name ) {
		if( property_exists( $this, $name ) )
			return $this->$name;
		elseif( isset( $this->_attributes[ $name ] ) )
			return $this->_attributes[ $name ];
	}

	/**
	 * Sets the named attribute value.
	 * You may also use $this->AttributeName to set the attribute value.
	 * @param string $name the attribute name
	 * @param mixed $value the attribute value.
	 * @return boolean whether the attribute exists and the assignment is conducted successfully
	 * @see hasAttribute
	 */
	public function setAttribute( $name, $value ) {
        $attributes = array_flip( $this->attributeNames() );

		if( property_exists( $this, $name ) )
			$this->$name = $value;
		elseif( isset( $attributes[ $name ] ) )
			$this->_attributes[ $name ] = $value;
		else
			return false;

		return true;
	}

    /**
	 * Returns a property value
	 * @param string $name the property name or event name
	 * @return mixed the property value, event handlers attached to the event, or the named behavior
	 * @throws Exception if the property or event is not defined
	 * @see __set
	 */
	public function __get( $name ) {
		$getter = 'get' . $name;

        if( isset( $this->_attributes[ $name ] ) ) {
            return $this->_attributes[$name];
        } 
        else if( method_exists( $this, $getter ) ) {
	        return $this->$getter();
        }		
            
		throw new Exception( 'Property "' . get_class( $this ) . '.' . $name . '" is not defined.' );
	}

	/**
	 * Sets value of a component property.
	 * @param string $name the property name or the event name
	 * @param mixed $value the property value or callback
	 * @return mixed
	 * @throws Exception if the property/event is not defined or the property is read only.
	 * @see __get
	 */
	public function __set( $name, $value ) {
		$setter = 'set' . $name;

		if( ( $this->setAttribute( $name, $value ) === false ) && method_exists( $this, $setter ) ) {
	        return $this->$setter( $value );
        }

		if( method_exists( $this, 'get' . $name ) ) {
            throw new Exception( 'Property "' . get_class( $this ) . '.' . $name . '" is read only.');
        } else {
            throw new Exception( 'Property "' . get_class( $this ) . '.' . $name . '" is not defined.' );
        }			
	}
}
?>
