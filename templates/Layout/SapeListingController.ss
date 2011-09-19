<% require themedCSS(FacetedListingController) %>


<div id="sidebar">
	$CachedFilterForm
</div>

<div id="content" class="typography has-sidebar">
    

    
<script src="http://multimedia.journalism.berkeley.edu/static/js/protovis/protovis-r3.2.js" type="text/javascript"></script>

<script type="text/javascript+protovis">

   
/* edit/input your data */
var data = [
	{"Date":"Midnight","lineData":18}, // edit values that follow the ":"
	{"Date":1,"lineData":32},
	{"Date":2,"lineData":17},
	{"Date":3,"lineData":3},
	{"Date":4,"lineData":2},
	{"Date":5,"lineData":7},
	{"Date":6,"lineData":13},
	{"Date":7,"lineData":41},
	{"Date":8,"lineData":89},
	{"Date":9,"lineData":76},
	{"Date":10,"lineData":59},
	{"Date":11,"lineData":43},
	{"Date":"Noon","lineData":68},
	{"Date":13,"lineData":69},
	{"Date":14,"lineData":75},
	{"Date":15,"lineData":72},
	{"Date":16,"lineData":83},
	{"Date":17,"lineData":81},
	{"Date":18,"lineData":108},
	{"Date":19,"lineData":60},
	{"Date":20,"lineData":38},
	{"Date":21,"lineData":41},
	{"Date":22,"lineData":37},
	{"Date":23,"lineData":39}  // the last value does not need a trailing comma
	];
	

/* edit these settings freely */
var w = 620, // width of the panel
	h = 200, // height of the panel
	topMargin = 10, // these four settings position your chart in the panel
	bottomMargin = 20,
	leftMargin = 30,
	rightMargin = 5,
        dataLineWidth = 3,
	dataLineColor = "#a14538",// use hexadecimal colors or web colors e.g. "black"
	baseDotColor = "transparent", 
	highlightDotColor = "#a14538",
	tickLineColor = "#d6d8da",	
	dataRange = 112, // change to just above the upper extent of your data
	increments = 10, // change to adjust spacing in tick lines
	dataText = "bold 8pt Arial,sans-serif", // adjust font settings of labels
	tickText = "bold 6pt Arial,sans-serif",
	dateText = "7pt Arial,sans-serif",
	dataTextSpace = 3; // adjusts distance between the label and the data point 
	
/* edit with care */
var chartHeight = h-bottomMargin-topMargin,
	chartWidth = w-leftMargin-rightMargin,
	xScale = pv.Scale.linear(0, data.length).range(0, chartWidth),
	total = pv.Scale.linear(0, dataRange).range(0, chartHeight),
	dataWidth = (chartWidth)/data.length,
	commas = pv.Format.number(),
	activeBar = -1;	
	
/* main panel */	
var vis = new pv.Panel().width(w).height(h)
	.event("mousemove", pv.Behavior.point(Infinity).collapse("y"));

	/* tick marks */
   	vis.add(pv.Rule)
		.data(pv.range(0, dataRange, increments))
		.bottom(function(d) (dataRange > chartHeight) ? (d / (dataRange/chartHeight)) + bottomMargin : (d * (chartHeight)/dataRange) + bottomMargin)
		.left(leftMargin)
		.right(rightMargin)
		.strokeStyle(tickLineColor)
		.add(pv.Label)
		.textAlign("right")
		.textBaseline("center")
		.font(tickText)
		.textStyle("gray");
	 		  
	/* line and data labels	 */
	vis.add(pv.Line)                         
		.data(data)
		.left(function() (dataWidth/2) + xScale(this.index)+leftMargin)         
		.height(function(d) total(d.lineData))  
		.width(dataWidth)                          
		.bottom(function(d) total(d.lineData) + bottomMargin)
		.lineWidth(dataLineWidth)
		.strokeStyle(dataLineColor)
		.add(pv.Dot)
		.strokeStyle(null)
		.event("point", function() {activeBar = this.index; return vis;})
		.fillStyle(function() (activeBar == this.index) ? highlightDotColor : baseDotColor)
		.size(10)
		.anchor("top")
		.add(pv.Label)
		.textBaseline("bottom")
		.textMargin(dataTextSpace)
		.text(function(d) d.lineData)
		.font(dataText)
		.textStyle(function() (activeBar == this.index) ? "black" : "transparent");

   /* date labels */
	vis.add(pv.Bar)
		.data(data) 
		.bottom(0) 
		.left(function() xScale(this.index) + leftMargin)
		.width(dataWidth)
		.fillStyle(null)
		.anchor("bottom")                 
		.add(pv.Label)                                     
		.text(function(d) d.Date)
		.textAlign("center")
		.font(dateText)
		.textStyle("#333333"); 
					
	/* animation panel */
	vis.add(pv.Panel)
		.events("all") 
		.event("point", function() {activeBar = this.index; return vis;});
	 
	vis.render();
        

</script>






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