<?php
/**
 * A class representing Student or Child details
 */
class ChildDetails {

    /**
     * Instantiates a new Child Details class
     * @param int $id Child Id
     * @param string $firstName Child's first name
     * @param string $lastName Child's last name
     * @param string $className Child's class name
     */
    function __construct($id, $firstName, $lastName, $className) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->className = $className;
    }

    /**
     * Determines if the child is valid or not.  Child is valid if first or last name is set
     * @return True if child is valid
     */
    public function isValid() {
        if ($this->firstName != NULL || $this->lastName != NULL) {
            return true;
        }

        return false;
    }

    /**
     * Returns the display name for Child to be used in selection dropdown in 
     * the following format "firstname in classname"
     * @return Child display name for selection dropdown
     */
    public function getChildSelectionName() {
        $name = $this->firstName ?: $this->lastName;
		if ($this->className != NULL) {
			$name = $name . " in " . $this->className;
        }
        
        return $name;
    }
}
?>
