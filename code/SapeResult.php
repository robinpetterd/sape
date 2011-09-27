<?php 

class SapeResult extends DataObject {
 

  
    static $db = array(
          "Count" => "Int",
	  'Name' => 'Text'
	
   );


   static $has_one = array(
	
   );
  
 
   static $has_many = array(

   );	  
   	
    static $belongs_many_many = array(
       
   );
    
    

		

  static $searchable_fields = array(
       'Name'
  );
 
   
									
  static $summary_fields = array(
       'Name'
  );
	
	
 
function onBeforeWrite() {
   		
	  
    	parent::onBeforeWrite();
}
  




	
 function getCMSFields() {
		   
			
			 $fields = parent::getCMSFields();
		 	
			  
			  return $fields;
   }
   
   
function removeFields( $fields) {
		  
			 
			 $fields = parent::getCMSFields();
		 
	 		//$fields->removeByName('Event'); //We remove this event at this stage because in actual data it's harded wired. 
			//$fields->removeByName('Relationships');
			//$fields->removeByName('Groups');
		        //$fields->removeByName('SourceFile');
				
			  return $fields;
   }
		

 
};

?>