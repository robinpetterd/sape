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
                Requirements::javascript('sape/javascript/SapeListing.js');
	}

       
        
}