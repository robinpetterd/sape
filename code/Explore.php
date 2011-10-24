<?php

class Explore extends Page {
	  
	  
	public static $db = array(
							  
	);
	
	public static $has_one = array(
	);
	
	
	  static $defaults = array(
   );
   
   function getCMSFields() {
   
   		$fields = parent::getCMSFields();
    	return $fields;

   }

}


class Explore_Controller extends Page_Controller {
	
	public function init() {
			parent::init();
			//Debug::show("in page controller"); 
                        
                            $base = Director::baseURL();
                                            
                            Director::redirect($base . 'explore');

		
	}
	
	
	
	
	
	
	
}

?>