<link rel="stylesheet" href="/assets/css/Chart.min.css">
<script src="/assets/js/Chart.min.js"></script>
<?php
  $this->load->view('meeting/register_graph.css.1280.php');
?>
<div class="wrapper-graph" data-id="<?php echo $meeting_info['meeting_id']; ?>">
  <div class="section-left">
    <div class="register-total">
      <div class="title">ผู้เข้าร่วมประชุม</div>
      <div class="num">0</div>
    </div>
    <?php
      $bg_color = [
        '#D8D8D8',
        '#2AA8A6',
        '#007912',
        '#FFAB1F',
        '#004A6B',
        '#FA4858',
        '#6B4CDB',
        '#9D1E9C',
        '#0189C9',
        '#F347BD',
        '#9D1E9C'
      ];
      $index = 0;
      foreach($group_info as $i => $row) {
        echo sprintf('
          <div class="box-coop item-%02d%s" data-ref="%s" data-bgcolor="%s">
            <div class="title">%s</div>
            <div class="num" data-num="0">0</div>
          </div>
          <style>
            .wrapper-graph .section-left .box-coop.item-%02d::before {
              background-color: %s;
            }
          </style>
          ',
          $row['id'],
          ($i == (count($group_info) - 1)) ? ' last-child' : '',
          $row['id'],
          $bg_color[$index],
          $row['name'],
          $row['id'],
          $bg_color[$index++]
        );
      }
    ?>
  </div>
  <div class="section-right">
    <div class="title">
      <!-- <h1>สถิติผู้เข้าร่วมงาน</h1> -->
      <h4><?php echo $meeting_info['meeting_name']; ?></h4>
    </div>
    <div class="current-time"></div>
    <div id="canvas-holder" style="width:100%;position: relative;margin-top: 90px;">
      <canvas id="chart-area"></canvas>
      <div class="chart-inner">0</div>
    </div>
  </div>
</div>
<script>
      $(function() {
        "use strict";
        Number.prototype.format = function(n, x) {
          var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
          return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
        };

        let $register_time;
        let serverdate = new Date('<?php print date("F d, Y H:i:s", time()); ?>');
        function set_servertime() {
          serverdate.setSeconds( serverdate.getSeconds() + 1 );
        }
        setInterval(set_servertime, 1000);
        time_get();
        setInterval(time_get, 1000);
        function time_get() {
          let $days = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
          var $months = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤษจิกายน", "ธันวาคม"];
          let $day =  $days[serverdate.getDay()];
          let $date =  String(serverdate.getDate()).padStart(2, "0");
          let $month =  $months[serverdate.getMonth()];
          let $year =  serverdate.getFullYear() + 543;
          let $hour =  String(serverdate.getHours()).padStart(2, "0");
          let $minute =  String(serverdate.getMinutes()).padStart(2, "0");
          let $second =  String(serverdate.getSeconds()).padStart(2, "0");
          let $timestring = `${$day}ที่ ${$date} ${$month} ${$year} เวลา ${$hour}:${$minute}:${$second} น.`;
          $('.wrapper-graph .section-right .current-time').html($timestring);
        }

        setInterval(data_get, (1000 * 60) );
        data_get();
        let $global_json;
        function data_get() {
          var $data = Array();
          $data.push({ name: "id", value: $('.wrapper-graph').data('id') });
          $data.push({ name: "_t", value: Math.random() });
          $data = jQuery.param($data);
          $.ajax({
            type: "POST"
            , url: '/meeting/register_graph_info'
            , dataType: 'json'
            , data: $data
            , async: true
            , success: function( $json ) {
              console.log($json);
              $global_json = $json;
              $('.wrapper-graph .section-left .register-total .num').html( $json.total.format(0, 3) ).data('num', $json.total);
              $('.wrapper-graph .section-left [class*="item-"]').each(function() {
                if( typeof $json.data[$(this).data('ref')] !== 'undefined' ) {
                $(this).find('.num').html( $json.data[$(this).data('ref')].format(0, 3) ).data('num', $json.data[$(this).data('ref')]);
                }
              });

              update_chart();
            }
          });
        }

        function update_chart() {
          $('#canvas-holder .chart-inner').html($global_json.total.format(0, 3));
          var $no = 0;
          $('.wrapper-graph .section-left [class*="item-"]').each(function() {
            let $bgcolor = $(this).data('bgcolor');
            config.data.datasets[0].data[$no] = parseInt($(this).find('.num').data('num'));
            config.data.datasets[0].backgroundColor[$no] = $bgcolor;
            if( typeof window.myDoughnut !== 'undefined' ) {
              window.myDoughnut.data.labels[$no] = $(this).find('.title').html().toString();
            }
            $no++;
          });
          window.myDoughnut.update();
        }
        var config = {
          type: 'doughnut',
          data: {
            datasets: [{
              data: [0, 0, 0, 0, 0, 0, 0, 0, 0],
              backgroundColor: [
                '#D8D8D8',
                '#2AA8A6',
                '#007912',
                '#FFAB1F',
                '#004A6B',
                '#FA4858',
                '#6B4CDB',
                '#9D1E9C',
                '#0189C9',
                '#F347BD',
                '#9D1E9C',
              ],
              label: 'Dataset 1'
            }],
            labels: ['', '', '', '', '', '', '', '', '']
          },
          options: {
            segmentShowStroke: false,
            responsive: true,
            legend: {
              display: false
            },
            title: {
              display: false,
              text: ''
            },
            animation: {
              animateScale: true,
              animateRotate: true
            },
            elements: {
              arc: {
                borderWidth: 0
              }
            }
          }
        };
        let ctx;

          ctx = document.getElementById('chart-area').getContext('2d');
          window.myDoughnut = new Chart(ctx, config);


      });
    </script>