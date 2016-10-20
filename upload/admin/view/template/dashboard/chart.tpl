<div class="panel panel-default">
  <div class="panel-heading">
    <div class="pull-right"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-calendar"></i> <i class="caret"></i></a>
      <ul id="range" class="dropdown-menu dropdown-menu-right">
        <li><a href="day"><?php echo $text_day; ?></a></li>
        <li><a href="sevendays"><?php echo $text_7days; ?></a></li>
        <li><a href="week"><?php echo $text_week; ?></a></li>
        <li><a href="thirtydays" class="thirty"><?php echo $text_30days; ?></a></li>
        <li class="active"><a href="month"><?php echo $text_month; ?></a></li>
        <li><a href="year"><?php echo $text_year; ?></a></li>
      </ul>
    </div>
    <h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> <?php echo $heading_title; ?></h3>
  </div>
  <div class="panel-body">
  <div id="results"></div>
    <div id="chart-sale" style="width: 100%; height: 260px;"></div>
  </div>
</div>
<script src="view/javascript/jquery/flot/jquery.flot.js"></script>
<script src="view/javascript/jquery/flot/jquery.flot.time.js"></script>
<script src="view/javascript/jquery/flot/jquery.flot.resize.min.js"></script>
<script>
var chart_sale_xaxis_links = [];

$('#range a').on('click', function(e) {
	e.preventDefault();

	$(this).parent().parent().find('li').removeClass('active');

	$(this).parent().addClass('active');

	$.ajax({
		type: 'get',
		url: 'index.php?route=dashboard/chart/chart&token=<?php echo $token; ?>&range=' + $(this).attr('href'),
		dataType: 'json',
		success: function(json) {
			var rangeType = $('#range .active a').attr('href');

			if (rangeType == 'thirtydays' || rangeType == 'sevendays') {
				xax = {
					mode: 'time',
					minTickSize: [1, 'day'],
					ticks: json['xaxis']
				};
			} else {
				xax = {
					show: true,
					ticks: json['xaxis']
				};
			}

			$.plot('#chart-sale',
				[{
					data: json['total_order'],
					label: '<?php echo $text_total_orders; ?>',
					lines: { show: true, fill: true },
				},{
					data: json['unknown_order'],
					label: '<?php echo $text_unknown_orders; ?>',
				},{
					data: json['affiliate_order'],
					label: '<?php echo $text_affiliate_orders; ?>',
				},{
					data: json['ext_aff_id_order'],
					label: '<?php echo $text_ext_aff_id_orders; ?>',
				}],
				{
					series: {
						lines: {
							show: true
						},
						points: {
							show: true
						}
					},
					grid: {
						hoverable: true,
						clickable: true
					},
					yaxis: {
						show: true,
						minTickSize: 1,
						tickDecimals: 0,
						min: 0
					},
					xaxis: xax
				}
			);

			chart_sale_xaxis_links = json['xaxis_links'];

			$('<div id="tooltip"></div>').css({
				position:           'absolute',
				display:            'none',
				color:              '#fff',
				border:             '1px solid #fdd',
				padding:            '4px',
				'background-color': '#3DA9E3',
				opacity:            0.70
			}).appendTo('body');
			
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#chart-sale').on('plothover', function(event, pos, item) {
	if (item) {
		var x = item.datapoint[0].toFixed(0),
				y = item.datapoint[1].toFixed(0);

		$('#tooltip').html(item.series.label + ' : ' + y)
			.css({top: item.pageY+5, left: item.pageX+5})
			.fadeIn(200);
	} else {
		$('#tooltip').hide();
	}
});

$('#chart-sale').on('plotclick', function (event, pos, item) {
	if (item) {
		if (item.series.label == '<?php echo $text_total_orders; ?>') {
			window.location = chart_sale_xaxis_links[item.datapoint[0]];
		}

		if (item.series.label == '<?php echo $text_unknown_orders; ?>') {
			window.location = chart_sale_xaxis_links[item.datapoint[0]] + '&filter_has_affiliate=0&filter_has_ext_aff_id=0';
		}

		if (item.series.label == '<?php echo $text_affiliate_orders; ?>') {
			window.location = chart_sale_xaxis_links[item.datapoint[0]] + '&filter_has_affiliate=1';
		}

		if (item.series.label == '<?php echo $text_ext_aff_id_orders; ?>') {
			window.location = chart_sale_xaxis_links[item.datapoint[0]] + '&filter_has_affiliate=0&filter_has_ext_aff_id=1';
		}
	}
});

$('#range .active a').trigger('click');
</script>