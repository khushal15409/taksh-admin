@php($params = $params ?? session('dash_params') ?? ['zone_id' => 'all'])
<div id="grow-sale-chart"></div>

<script>
    "use strict";
    options = {
          series: [{
          name: 'Gross Sale',
          data: [{{ implode(",",$total_sell ?? [0,0,0,0,0,0,0,0,0,0,0,0]) }}]
        },{
          name: 'Admin Comission',
          data: [{{ implode(",",$commission ?? [0,0,0,0,0,0,0,0,0,0,0,0]) }}]
        },{
          name: 'Delivery Comission',
          data: [{{ implode(",",$delivery_commission ?? [0,0,0,0,0,0,0,0,0,0,0,0]) }}]
        }],
          chart: {
          height: 350,
          type: 'area',
          toolbar: {
            show:false
        },
            colors: ['#76ffcd','#ff6d6d', '#005555'],
        },
            colors: ['#76ffcd','#ff6d6d', '#005555'],
        dataLabels: {
          enabled: false,
            colors: ['#76ffcd','#ff6d6d', '#005555'],
        },
        stroke: {
          curve: 'smooth',
          width: 2,
            colors: ['#76ffcd','#ff6d6d', '#005555'],
        },
        fill: {
            type: 'gradient',
            colors: ['#76ffcd','#ff6d6d', '#005555'],
        },
        xaxis: {
        //   type: 'datetime',
            categories: [{!! implode(",",$label ?? ['"Jan"','"Feb"','"Mar"','"Apr"','"May"','"Jun"','"Jul"','"Aug"','"Sep"','"Oct"','"Nov"','"Dec"']) !!}]
        },
        tooltip: {
          x: {
            format: 'dd/MM/yy HH:mm'
          },
        },
        };

        chart = new ApexCharts(document.querySelector("#grow-sale-chart"), options);
        chart.render();

        // INITIALIZATION OF CHARTJS
        // =======================================================
        Chart.plugins.unregister(ChartDataLabels);

        $('.js-chart').each(function () {
            $.HSCore.components.HSChartJS.init($(this));
        });

        updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));
    </script>

