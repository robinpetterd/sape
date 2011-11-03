
<div id="dashboard_div">

<div id="filter">
	$CachedFilterForm
</div>

<div id="content" class="typography has-sidebar">
                    
<% if  FilteredStatus != 0 %>

        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
         <!-- Code to build the actual chart. -->
            <script type="text/javascript">
                
              google.load('visualization', '1.0', {'packages':['controls']});
              
              google.setOnLoadCallback(drawChart);

              function drawChart() {

                  var data = new google.visualization.DataTable();
                  var columnCount = 2; 
                  var columnArray = [];
                  columnArray.push(0);
                  //columnArray.push(1);

                  
                  data.addColumn('number', 'day');
                  data.addColumn('number', 'Lat');
                  data.addColumn('number', 'Long');

                 <% control VisItems %>
                  <% if First %>
                         <% control x %>
                             <% control Diseases %>
                                <% if Name != Diseases %>
                                 data.addColumn('number', '$Name');
                                 columnCount++;
                                 columnArray.push(columnCount);
                                <% end_if %>
                             <% end_control %>
                       <% end_control %>
                   <% end_if %>
                  data.addRow([
                            
                        <% control x %>
                            <% if PercentageVoyageLapsed == 0 %>
                                $PercentageVoyageLapsed,
                            <% end_if %>
                            <% if PercentageVoyageLapsed %>
                                $PercentageVoyageLapsed,
                            <% end_if %>
                        <% end_control %>
                        
                      -42.492352591428194, 147.6947021484375,
                    
                      <% control x %>
                             <% control Diseases %> <% if Name != Diseases %>$Count,<% end_if %> <% end_control %>
                        <% end_control %>
                  
                        ]);
                    
               <% end_control %>
           
            //  console.log(columnArray);
   

            //chart.draw(data, options);
            var lineChartWidth = $('#chart_div').width();
            var lineChartHeight = $('#filter').height()+120;
            //console.log(lineChartWidth);
          
           //alert(columnCount);
           
           
          
            var lineOne = new google.visualization.ChartWrapper({
                'chartType': 'LineChart',
                'containerId': 'chart_div',
                    
                'options': {
                    
                    'width': lineChartWidth,
                    'height': lineChartHeight,
                    'pointSize': 1,
                    'title': 'Diseases and % of the vogage',
                    'hAxis':  {showTextEvery:5,title: '% of vogage',  titleTextStyle: {color: 'Black'}},                    
                    'vAxis':  {maxValue: 1,title: 'Disease Count',  titleTextStyle: {color: 'Black'}},
                    'chartArea': {width: '70%',height: '90%',left: 45, top: 10,bottom: 10 },
                    "lineWidth":2,
                    "hasLabelsColumn":true,
                    'legendTextStyle':  {fontSize: 11}




               },
                   'view': {'columns': columnArray}
              })

             //lineOne.draw();
              var mapChartWidth = $('#map_div').width();

                          
              var mapOne = new google.visualization.ChartWrapper({
                    'chartType': 'Map',
                    'containerId': 'map_div',
                    'dataTable': data,
                    'options': {
                        'width': mapChartWidth,
                        'height': 390,
                        'mapType' : 'normal',
                        'zoomLevel':0 
                    },
                    'view': { 'columns': [1, 2]}
              });
                
             mapOne.draw();
       
       // Create a range slider, passing some options
        var dayRangeSlider = new google.visualization.ControlWrapper({
          'controlType': 'NumberRangeFilter',
          'containerId': 'filter_div',
          'options': {
            'filterColumnLabel': 'day'

          }
        });
        
        
      // Create a dashboard.
        var dashboard = new google.visualization.Dashboard( document.getElementById('dashboard_div'));
        dashboard.bind(dayRangeSlider, lineOne);
        dashboard.draw(data);   
      }

            </script>


                         <div id="chart_div"></div>
                         <div class="clear"></div>

                          <div id="filter_div"></div>

                </div>

               
               <div class="clear"></div>

               <div id="map_div"></div>
               <div class="clear"></div>

                <table id="listing-items">
                        <thead>
                                <tr>
                                        <% control HeaderRow %>
                                                <th class="$Name.HTMLATT">
                                                        <% if Sortable %>
                                                                <a href="$SortLink" class="$SortClass">$Title</a>
                                                        <% else %>
                                                                $Title
                                                        <% end_if %>
                                                </th>
                                        <% end_control %>
                                <tr>
                        </thead>
                        <tbody>
                                <% if TableItems %>
                                        <% control TableItems %>
                                                <tr class="$EvenOdd">
                                                        <% control Me %>
                                                                <td class="$Name.HTMLATT">
                                                                        <a href="view/$ID">
                                                                                <% if Value %>$Value<% end_if %>
                                                                        </a>
                                                                </td>
                                                        <% end_control %>
                                                </tr>
                                        <% end_control %>
                                <% else %>
                                        <tr id="listing-no-items">
                                                <td colspan="$HeaderRow.Count">No $PluralName found.</td>
                                        </tr>
                                <% end_if %>
                        </tbody>
                </table>

                <div id="listing-pagination">
                        <div id="listing-per-page">
                                <% control PerPageSummary %>
                                        <% if Current %>
                                                $Num
                                        <% else %>
                                                <a href="$Link">$Num</a>
                                        <% end_if %>
                                <% end_control %>
                                $PluralName per page
                        </div>

                        <div id="listing-items-num">
                                Displaying $TableItems.Count of $TableItems.TotalItems results
                        </div>

                        <% if TableItems.MoreThanOnePage %>
                                <div id="listing-items-pagination-controls">
                                        <% if TableItems.NotFirstPage %>
                                                <a class="prev" href="$TableItems.PrevLink">&laquo; Previous</a>
                                        <% end_if %>
                                        <% control TableItems.PaginationSummary(4) %>
                                                <% if CurrentBool %>
                                                        $PageNum
                                                <% else %>
                                                        <% if Link %>
                                                                <a href="$Link">$PageNum</a>
                                                        <% else %>
                                                                &hellip;
                                                        <% end_if %>
                                                <% end_if %>
                                        <% end_control %>
                                        <% if TableItems.NotLastPage %>
                                                <a class="next" href="$TableItems.NextLink">Next &raquo;</a>
                                        <% end_if %>
                                </div>
                        <% end_if %>
                </div>


        <% end_if %>

</div>