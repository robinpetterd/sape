    <?php    

/**
 * The SAPE exenstion for the FacetedListingController
 *  *
 * @package 
 */

abstract class SapeListingController extends FacetedListingController {
        
        private $data;
        private $form;
                
   
       public function init() {
		parent::init();
                                
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-metadata/jquery.metadata.js');
		//CHANGE so that we use the local .js so that Google DataTables can be made. 
                Requirements::javascript('http://www.google.com/jsapi');
                
               // Session::set('Filtered', false);
                
                                       
                //Requirements::javascript('sape/javascript/SapeListing.js');
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
                
                
                

		foreach ($fields as $name => $title) {
				$columns->push(new ArrayData(array(
				'Name'      => $name,
				'Title'     => $title,
			)));
		}

		return $columns;
                
         
     }
     
        
       public function FilteredStatus() {
             
           $filterStatus = Session::get('Filtered');
           return $filterStatus; 
        }
     
        
        
        public function change() {
             Session::set('Filtered', false);

        }
        
        /*
         * Does query and then formats that CVS. 
         * 
         */
        
     	public function doExport($data, $form) {
                 
            
                Session::set('Filtered', true);

                
		$query  = $this->generateFullQuery($data, $form);
		$result = $query->execute();

		$this->sourceItems = singleton('DataObject')->buildDataObjectSet($result);

		if ($this->sourceItems) {
			$this->sourceItems->parseQueryLimit($query);
		} else {
			$this->sourceItems = new DataObjectSet();
		}
                
		// Add faceting data to the page.
		$metadata = sprintf(
			'<script id="listing-facets" type="data">%s</script>',
			$this->generateFacetJson($data)
		);
		Requirements::insertHeadTags($metadata, 'listing-facets');

		$controller = $this->customise(array(
			'Title'            => $this->PluralName(),
			'CachedFilterForm' => $form
		));
		
               // return $this->getViewer('list')->process($controller);
                
                $now = Date("d-m-Y-H-i");
		$fileName = "export-$now.csv";
                
                if($fileData = $this->generateExportFileData()){

			return SS_HTTPRequest::send_file($fileData, $fileName,'text/csv');
		}else{
			user_error("No records found", E_USER_ERROR);
		}
                
                
	}
        
        
	/*
         * Formats the sourceItemas as cvs string
         * 
         */
	function generateExportFileData() {
		
		$cvs = '';
		
                $items = $this->sourceItems;
                //build the columns 
                
                $first = $items->First();
                
                $fields = $first->custom_database_fields($first->ClassName);
                
                foreach($fields as $field => $fieldTitle) {
                    //echo $field->fieldTitle;
                    //Debug::show($field);
                    $cvs .= $field . ',';	
                   
                }
                
                 //get rid the last, 
                  $cvs =  substr_replace($cvs ,"",-1);
                  $cvs .= "\n"; 
                           
                           
                
                //out the actual items  
		if($items) {
			foreach($items as $item) {
				
                            //debug::Show($item->custom_database_fields($item->ClassName));
                                
				$fields = $item->custom_database_fields($item->ClassName);
                                
				if($fields) foreach($fields as  $field => $fieldTitle) {
                                        //debug::Show($field);
					$value = $item->getField($field);
					
                                        //echo $value;
                                        $cvs .= $value . ',';					
                            }
                           //get rid the last, 
                           $cvs =  substr_replace($cvs ,"",-1);
                           $cvs .= "\n"; 
                            
                        }
			
                        
                        //echo $cvs;
                        return $cvs;
		} else {
			return null;
		}
	}
	
       
         
        /**
	 * @param  array $data
	 * @param  Form $form
	 * @return SQLQuery
	 */
	protected function generateFullQuery($data, $form) {
		$context = new SearchContext($this->getItemClass());

		foreach (array_keys($this->getFacetableFields()) as $name) {
			$context->addFilter($this->getFacetFilter($name));
		}

		$query = $context->getQuery($data);
		$query->orderby($this->getSqlSort());
                
		/*$query->limit(array(
			'start' => $this->getPaginationStart(),
			'limit' => $this->getItemsPerPage()
		));*/

		if ($indexes = $this->getFulltextFields()) {
			if (isset($data['Keywords']) && strlen($data['Keywords'])) {
				$query->where(sprintf(
					'MATCH(%s) AGAINST (\'%s\')',
					'"' . implode('", "', $indexes) . '"',
					Convert::raw2sql($data['Keywords'])
				));
			}
		}

		return $query;
	}
        
        
        
        
         
        /**
	 * @param  array $data
	 * @param  Form $form
	 * @return SQLQuery
	 */
	protected function generatePagedQuery($data, $form) {
		$context = new SearchContext($this->getItemClass());

		foreach (array_keys($this->getFacetableFields()) as $name) {
			$context->addFilter($this->getFacetFilter($name));
		}

		$query = $context->getQuery($data);
		$query->orderby($this->getSqlSort());
                
		$query->limit(array(
			'start' => $this->getPaginationStart(),
			'limit' => $this->getItemsPerPage()
		));

		if ($indexes = $this->getFulltextFields()) {
			if (isset($data['Keywords']) && strlen($data['Keywords'])) {
				$query->where(sprintf(
					'MATCH(%s) AGAINST (\'%s\')',
					'"' . implode('", "', $indexes) . '"',
					Convert::raw2sql($data['Keywords'])
				));
			}
		}

		return $query;
	}
        
        
        
        
        
        
        
        public function doFilter($data, $form) {
                
                Session::set('Filtered', true);
                
		$query  = $this->generateFullQuery($data, $form);
                
		$result = $query->execute();
                
                $this->data = $data;
                $this->form = $form;
                
		$this->sourceItems = singleton('DataObject')->buildDataObjectSet($result);

		if ($this->sourceItems) {
			$this->sourceItems->parseQueryLimit($query);
		} else {
			$this->sourceItems = new DataObjectSet();
		}
                
		// Add faceting data to the page.
		$metadata = sprintf(
			'<script id="listing-facets" type="data">%s</script>',
			$this->generateFacetJson($data)
		);
		Requirements::insertHeadTags($metadata, 'listing-facets');

		$controller = $this->customise(array(
			'Title'            => $this->PluralName(),
			'CachedFilterForm' => $form
		));
		return $this->getViewer('list')->process($controller);
	}
        
        
     // -------------------------------------------------------------------------

	/**
	 * @return Form
	 */
	public function FilterForm() {
		$fields = new FieldSet();

		if ($this->getFulltextFields()) {
			$fields->push(new TextField('Keywords'));
		}

		foreach ($this->getFacetableFields() as $name => $title) {
			$fieldName = $this->convertNameToRelationID($name);
			$fieldName = str_replace('.', '__', $fieldName);

			$fields->push(new DropdownField(
				$fieldName, $title, $this->getFacetMap($name), null, null, '(any)'
			));
		}

		$form = new Form($this, 'FilterForm', $fields, new FieldSet(
			new FormAction('doFilter', 'Filter'),
                        new FormAction('doExport', 'Export'),
			$reset = new ResetFormAction('reset', 'Reset')
		));
		$reset->useButtonTag = true;

		// Pass along all the useful form GET params
		$fields->push(new HiddenField('sort', '', $this->request->getVar('sort')));
		$fields->push(new HiddenField('dir', '', $this->request->getVar('dir')));
		$fields->push(new HiddenField('perpage', '', $this->request->getVar('perpage')));

		$form->setFormMethod('GET');
		$form->disableSecurityToken();
		$form->addExtraClass(sprintf("{facetsLink: '%s'}", Convert::raw2js(
			Controller::join_links($this->Link(), 'facets')
		)));

		return $form;
	}

        

        
      
        public function TableItems() {
            
                
           $filterStatus = Session::get('Filtered');
                
            if($filterStatus == 0) { 
                    return;  // just give up                  
              } else {
                
                $query  = $this->generatePagedQuery($this->data,$this->form );
                
		$result = $query->execute();             
		$this->sourceItems = singleton('DataObject')->buildDataObjectSet($result);

                $result = new DataObjectSet();
		$items  = $this->getSourceItems();
		$fields = $this->getListingFields();
                

		if ($items) foreach ($items as $item) {
			$result->push($row = new DataObjectSet());

			foreach ($fields as $name => $title) {
				$row->push(new ArrayData(array(
					'Name'  => $name,
					'Link'  => $item->ID,
					'Value' => $this->getValueFromItem($name, $item)
				)));
			}
		}

		$limits = $items->getPageLimits();
		$result->setPageLimits(
			$limits['pageStart'], $limits['pageLength'], $limits['totalSize']
		);

		return $result;   
                
                
                
                };
        }

                
     	/** Returns the data for use in the JSON google chart.
	 * @return DataObjectSet
	 */
        
        
        
	public function VisItems() {
                
  
                //only do this if the search has been filter already
                $filterStatus = Session::get('Filtered');
                
                
                if($filterStatus == 0) { 
                    return;  // just give up 
                    
                 
                };
                
		$result = new DataObjectSet();
                $yFound = array();
                
		$items  = $this->getSourceItems();
                //Debug::show($items);
                $items->sort('PercentageVoyageLapsed');
                               
                // getPlotClasses()
                $xAxis =  $this->getPlotX();
               
                                
                $completelyPlots = array (); //a new of tracking which one's have been looked at is the past
                
                $row = new DataObjectSet();;//list the diease that have been found 

		if ($items) foreach ($items as $item) {
                    
                    //echo '<br> day is ' . $item->$xAxis;
                    
                    //Debug::show($item->$xAxis );
                    //
                    // check to see if have done this already
                    $done = FALSE;
                    //Debug::show($completelyPlots);
                    foreach ($completelyPlots as $completed) {  
                        if($completed == $item->$xAxis ) {
                           $done = TRUE; 
                           //echo "<p>Already done " . $item->$xAxis;
                           break;
                        } else {
                           $done = FALSE;
                        }
                        
                    }
                    
                   // Only do this the xAxis hasn't been loked at before. 
                    if ($done == TRUE) {
                        //echo "<p>Done this already " . $item->$xAxis  ;
                    } else {
                        
                       //add to what has already been done 
                        array_push($completelyPlots,$item->$xAxis);
                       //Now we do the actual core bit 
                        
                        //Find the other items with the same $item->$xAxis
                                      
                       //Check to see if there any other items with same x Axis
                       $xFound = array();
                       
                       foreach ($items as $SearchItem) {                           
                           if($SearchItem->$xAxis == $item->$xAxis ) {
                                
                               /* if($SearchItem->$xAxis == 77) {
                                    echo '<p> found another enter with the same day' . $SearchItem->$xAxis ;

                                }*/
                             //Debug::Show($SearchItem);
                             //$xFound->push($SearchItem->$xAxis);
                             array_push($xFound,$SearchItem);
                               
                           }

                       }

                     
                       
                       //Make the list of y stuff that has been found 
                       $count = 0;// how many times we have found the yPlot  
                       
                       $yCounts = array(); // where the Disease for this day are being stored .
                       //Debug::Show($xFound);
                       
                       foreach ($xFound as $Found) {
                           //lookat the disease that can be found on each
                             //if($Found->$xAxis == 77) {echo '<p>------------- <p>';};
                             
                           foreach ($Found->Diseases() as $Disease) {
                        
                                array_push($yCounts,$Disease->Name);
                                
                                //debug::Show($Disease->Name);
                                
                                //See if this in existing set of found stuff 
                                 if (in_array($Disease->Name,$yFound)) {
                                        //yes there arealad
                                   } else {
                                        array_push($yFound,$Disease->Name);//$yFound is all disease that have been found for this day
                                  }
                           }
                       }
                       
                       ///Debug::show($yCounts);
                      
                       //Count what is in that  
                       $yCounted = array_count_values($yCounts);
                      // echo '<p>-------- counts are -  ------- <p>'; 
                      //Debug::show($yCounted);
                       
                       //now make that in nice value and count pair. 
                       
                       //look a each of what found build a result object and name and counts on the 
                       //also add the that to the list object as   
                      
                       $list = new DataObjectSet();
                       
                       foreach ($yCounted as $key => $value) {
                           //print "$key == $value <p>"; 
                           $newResult = new SapeResult();
                          
                           $newResult->setField('Count',$value);
                           $newResult->setField('Name',$key);
                           //sDebug::show($newResult);
                           $list->push($newResult);
                           
                       } 
                   
                      /* for sending back out the templates 
                      $row->push(new ArrayData(array( 
                         'x' => new DataObjectSet(array( 
                            array( $xAxis  => $Found->$xAxis), 
                            array('Diseases' => $list) 
                         )) 
                      )));


                      */
                       
                      $row->push(array( 
                            array( $xAxis  => $Found->$xAxis), 
                            array('Diseases' => $list) 
                         ) 
                      );                                          
         
                    }
                    //Debug::Show($row);
                    
                  // $result->push($row);
                  //make sure if   
                    
			
		}
                
                //return $row;
                
                ///Debug::show($result);
                //
               
                
                //reformat the row's so that eall them have the same dieseases 
                
                                                
                 foreach ($row as $r) {
                     
                     $list = new DataObjectSet();  
                     //print_r($r[0][$xAxis]);
                     
                    //print_r('<p>--------------<p>');
                  
                     
                     $currentXaxis = $r[0][$xAxis];
                     
                     //print_r($currentXaxis);
                     
                     $d = $r[1]; // d is the current Diseases for this day
                     
                     //print_r('<p>--- New Day ----------- '.  $currentXaxis . '<p>');
                        
                             $alreadyFound = array(); 
                      
                             foreach ($yFound as $y){
                                    
                                  $foundStatus = FALSE;
                                    
                                    foreach($d as $Diseases) {
                                           
                                          // if($r[0][$xAxis] == 59 ){Debug::Show($Diseases);};

                                           foreach($Diseases as $Result) {
                                           //if($r[0][$xAxis] == 59 ){Debug::Show($Result);};
                                           //print_r($Result);
                                           //print_r('<p> ------- <p>');
                                           //printd_r($currentXaxis);
                                           //print_r('<p> ------- <p>');

                                            
                                            if ($Result->Name == $y ) {
                                                          // echo 'got it <p>';
                                                           $newResult = new SapeResult();

                                                           $newResult->setField('Count',$Result->Count);
                                                           $newResult->setField('Name',$Result->Name);
                                                           //Debug::show($newResult);
                                                           $list->push($newResult);
                                                           $foundStatus = TRUE;
                                                           array_push($alreadyFound,$Result->Name);
                                                           
                                                        } else {
                                                            //echo 'didnt get it  <p>';
                                                           $foundStatus = FALSE;

                                            }
                                            
                                     }
                                
                                 
                                  if($foundStatus == FALSE) {
                                                                          
                                      /* if(($r[0][$xAxis] == 59) && ($y == 'Respiratory tuberculosis') ){
                                           Debug::Show($alreadyFound);
    
                                        }*/
                                      
                                       
                                       if (in_array($y, $alreadyFound)) {
                                            // echo "RT <p>";
                                       } else  {
                                         
                                       $newResult = new SapeResult();

                                       $newResult->setField('Count','0');
                                       $newResult->setField('Name',$y);
                                       //Debug::show($newResult);
                                       $list->push($newResult);
                                       }
                                 

                                   } else {
                                       
                                   }
                                  
                                  // Debug::Show('in the loop');

                               }
                           
                                
                                       
                             }
                                                    
                
                     
                        $result->push(new ArrayData(array( 
                         'x' => new DataObjectSet(array( 
                            array( 'lat'  => $this->getLat($currentXaxis)),
                            array( $xAxis  => $currentXaxis), //need to get the write day
                            array('Diseases' => $list), 
                            array( 'long'  =>  $this->getLong($currentXaxis)),  
                         )),
                            
                            
                      )));                  
                     
                     
                
                }                         
         
                        
                       //foreach ($result as $r) {

                       //       print_r($r->x);
                       // }
                        
                //print_r($result);
                
		return $result;
                    
             

	}
        
        function getLat($percentage) {
           
            if ($percentage < 0 ) {
                $percentage = 0; 
            } else if ($percentage > 100) {
                $$percentage = 100;
            }
            
            //$Waypoint = $record = DataObject::get_one('WayPoint', 'Name = $percentage');
            //return $Waypoint->Latitude;
            return rand(1,42);
        }

       
        function getLong($percentage) {
           
            if ($percentage < 0 ) {
                $percentage = 0; 
            } else if ($percentage > 100) {
                $$percentage = 100;
            }
            
            //$Long = $record = DataObject::get_one('WayPoint', 'Name = $percentage');
            //return $WayPoint->Longitude;
            return rand(1,147);
        }      
         
}