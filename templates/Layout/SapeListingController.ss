<% require themedCSS(FacetedListingController) %>


<div id="sidebar">
	$CachedFilterForm
</div>

<div id="content" class="typography has-sidebar">
    
    
  
                     


<script type="text/javascript" src="https://www.google.com/jsapi"></script>
 <!-- Code to build the actual chart. -->
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      
      function drawChart() {
       
         var JSONObject = {

              cols: [
              
 
               {id: '$getPlotX', label: '$getPlotX', type: 'string'},
               
               <% control PlotLabels %>
                  {id: '$Name', label: '$Title', type: 'number'},
               <% end_control %>   
                    
               ],

            
              rows: [
                    {c:[{v: '1'},{v: 334}, {v: 5}] },
                    {c:[{v: '2'},{v: 5}, {v: 7}] },
                    {c:[{v: '3'},{v: 5}, {v: 9}] }
              ]


         };

      var data = new google.visualization.DataTable(JSONObject)
      var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
      chart.draw(data, {width: 600, height: 400, title: 'Plot'});
        
      }
      
      
      
    </script>
    
    <% control VisItems %>
    <p> {c:[{v: $PercentageVoyageLapsed},
       ,
    </p>
      <% control x %> 
         $PercentageVoyageLapsed
         <% control Diseases %>
            $Name, $Count
         <% end_control %>
      <% end_control %>
    <% end_control %>
                     



        <!--Div that will hold the  chart-->

    
        


                     
     
    <div id="chart_div"></div>




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
								<a href="$Link">
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
</div>