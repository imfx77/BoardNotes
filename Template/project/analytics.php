<style type="text/css">
.c3 svg{font:10px sans-serif;-webkit-tap-highlight-color:transparent}.c3 line,.c3 path{fill:none;stroke:#000}.c3 text{-webkit-user-select:none;-moz-user-select:none;user-select:none}.c3-bars path,.c3-event-rect,.c3-legend-item-tile,.c3-xgrid-focus,.c3-ygrid{shape-rendering:crispEdges}.c3-chart-arc path{stroke:#fff}.c3-chart-arc text{fill:#fff;font-size:13px}.c3-grid line{stroke:#aaa}.c3-grid text{fill:#aaa}.c3-xgrid,.c3-ygrid{stroke-dasharray:3 3}.c3-text.c3-empty{fill:gray;font-size:2em}.c3-line{stroke-width:1px}.c3-circle._expanded_{stroke-width:1px;stroke:#fff}.c3-selected-circle{fill:#fff;stroke-width:2px}.c3-bar{stroke-width:0}.c3-bar._expanded_{fill-opacity:.75}.c3-target.c3-focused{opacity:1}.c3-target.c3-focused path.c3-line,.c3-target.c3-focused path.c3-step{stroke-width:2px}.c3-target.c3-defocused{opacity:.3!important}.c3-region{fill:#4682b4;fill-opacity:.1}.c3-brush .extent{fill-opacity:.1}.c3-legend-item{font-size:12px}.c3-legend-item-hidden{opacity:.15}.c3-legend-background{opacity:.75;fill:#fff;stroke:#d3d3d3;stroke-width:1}.c3-title{font:14px sans-serif}.c3-tooltip-container{z-index:10}.c3-tooltip{border-collapse:collapse;border-spacing:0;background-color:#fff;empty-cells:show;-webkit-box-shadow:7px 7px 12px -9px #777;-moz-box-shadow:7px 7px 12px -9px #777;box-shadow:7px 7px 12px -9px #777;opacity:.9}.c3-tooltip tr{border:1px solid #CCC}.c3-tooltip th{background-color:#aaa;font-size:14px;padding:2px 5px;text-align:left;color:#FFF}.c3-tooltip td{font-size:13px;padding:3px 6px;background-color:#fff;border-left:1px dotted #999}.c3-tooltip td>span{display:inline-block;width:10px;height:10px;margin-right:6px}.c3-tooltip td.value{text-align:right}.c3-area{stroke-width:0;opacity:.2}.c3-chart-arcs-title{dominant-baseline:middle;font-size:1.3em}.c3-chart-arcs .c3-chart-arcs-background{fill:#e0e0e0;stroke:none}.c3-chart-arcs .c3-chart-arcs-gauge-unit{fill:#000;font-size:16px}.c3-chart-arcs .c3-chart-arcs-gauge-max,.c3-chart-arcs .c3-chart-arcs-gauge-min{fill:#777}.c3-chart-arc .c3-gauge-value{fill:#000}
</style>
<script>
$( document ).ready(function() {

  // Copy paste from kanboard app.js
  for (var t = $("#chart").data("metrics"), e = [], a = 0; a < t.length; a++) e.push([t[a].column_title, t[a].nb_tasks]);
    c3.generate({
        data: {
            columns: e,
            type: "donut"
        }
    })
});
</script>

<?php
// Working the data from $analyticsData before visualizing
// Note to self - change bad php loop to sql in model


// Data - open and closed notes
$ana_active0 = "0";
$ana_active1 = "0";
$ana_total = "0";
foreach ($analyticsData as $qq) {
  if ($qq['is_active'] == "0") {
    $ana_active0++;
  } else {
    $ana_active1++;
  }
  $ana_total++;
}

$ana_active0_per = (($ana_active0/$ana_total)*100);
$ana_active0_per = number_format((float)$ana_active0_per, 2, '.', '');
$ana_active1_per = (($ana_active1/$ana_total)*100);
$ana_active1_per = number_format((float)$ana_active1_per, 2, '.', '');

?>
<p><strong>Open: <?php print $ana_active1; ?></strong></p>
<p><strong>Done: <?php print $ana_active0; ?></strong></p>
<section class="analytic-task-repartition">
<div id="chart" class="c3" data-metrics='[{"column_title":"Open","nb_tasks":<?php print $ana_active1; ?>,"percentage":<?php print $ana_active1_per; ?>},{"column_title":"Done","nb_tasks":<?php print $ana_active0; ?>,"percentage":<?php print $ana_active0_per; ?>}]'></div>


</section>
