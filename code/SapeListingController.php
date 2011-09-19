<?php    

/**
 * The SAPE DataObjectDecorator for the FacetedListingController
 * 
 * Add replace the javascrits 
 *
 * @package 
 */


abstract class SapeListingController extends FacetedListingController {
        
    public function init() {
		parent::init();
                                
		 Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		 Requirements::javascript(THIRDPARTY_DIR . '/jquery-metadata/jquery.metadata.js');
		//CHANGE so that we use the local .js so that Google DataTables can be made. 
                 Requirements::javascript('http://www.google.com/jsapi');
                 Requirements::javascript('sape/javascript/SapeListing.js');
	}

      
        
        
    /*Function the should overwrite in actual use */
        
    
    /** Returns a list the class that are to be used in the chart 
     *
     * @return array
     * 
     */
       
        
     public function getPlotClasses() {
            return array(
            );
     }
     
     
     public function PlotLabels() {
         
                $columns  = new DataObjectSet();
		$fields   = $this->getPlotClasses();
		$sortable = $this->getSortableFields();

		foreach ($fields as $name => $title) {
				$columns->push(new ArrayData(array(
				'Name'      => $name,
				'Title'     => $title,
			)));
		}

		return $columns;
                
         
     }
         
}