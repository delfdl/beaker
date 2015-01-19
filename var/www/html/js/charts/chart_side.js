	$(function () {
    var sin = [], cos = [];
    for (var i = 0; i < 25; i++) {
        sin.push([i, Math.sin(i)]); // define min data
        cos.push([i, Math.cos(i)]); // define max data
    }

    var plot = $.plot($(".chartS"),
           [ { data: sin, label: "min (mb/s)"}, { data: cos, label: "max (mb/s)" } ], {
               series: {
                   lines: { show: true },
                   points: { show: true }
               },
               grid: { hoverable: true, clickable: true },
               yaxis: { min: 0, max: 30 },
			   xaxis: { min: 0, max: 24 }
             });
             
        //     <php echo ('// fred'); ?>

    function showTooltip(x, y, contents) {
        $('<div id="tooltip" class="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 5,
			'z-index': '9999',
			'color': '#fff',
			'font-size': '11px',
            opacity: 0.8
        }).appendTo("body").fadeIn(200);
    }

    var previousPoint = null;
    $(".chartS").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        if ($(".chartS").length > 0) {
            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    
                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);
                    
                    showTooltip(item.pageX, item.pageY,
                                item.series.label + " "+y);
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
        }
    });

    $(".chartS").bind("plotclick", function (event, pos, item) {
        if (item) {
            $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
            plot.highlight(item.series, item.datapoint);
        }
    });
});
