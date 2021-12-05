
    <style>
      html, body {
        margin: 0px;
        padding: 0px;
        font-family: 'Kanit';
        color: #FFF;
        background-color: #000;
      }
      .wrapper-graph {
        background-image: url('/assets/images/graph.background.1280x800.png');
        width: 1280px;
        height: 800px;
        display: block;
        position: relative;
        overflow: hidden;
      }

      .wrapper-graph .section-right .title {
        margin-top: 10px;
      }
      .wrapper-graph .title h1,
      .wrapper-graph .section-right .title h4 {
        margin: 0px;
        text-align: center;
      }
      .wrapper-graph .section-right .title h1 {
        font-weight: 500;
      }
      .wrapper-graph .section-right .title h4 {
        font-weight: 300;
        font-size: 24px;
        font-size: 38px;
        margin-top: 70px;
      }

      .wrapper-graph .section-left {
        position: absolute;
        top: 131px;
        left: 0px;
        right: calc(1280px - 356px);
        bottom: 0px;
        /* background-color: #995CBF; */
      }
      .wrapper-graph .section-right {
        position: absolute;
        top: 0px;
        left: 356px;
        right: 0px;
        bottom: 0px;
        /* background-color: #4B1080; */
      }
      .wrapper-graph .section-right .current-time {
        position: absolute;
        left: 10px;
        bottom: 14px;
        right: 0px;
        font-weight: 300;
        font-size: 16px;
        text-align: left;
      }

      .wrapper-graph .section-left .register-total {
        padding-left: 10px;
        height: 109px;
        display: block;
        position: relative;
      }
      .wrapper-graph .section-left .register-total .title {
        font-weight: 500;
        font-size: 16px;
        margin-top: 15px;
      }
      .wrapper-graph .section-left .register-total .num {
        font-weight: 400;
        font-size: 55px;
        position: absolute;
        left: 10px;
        right: 0px;
        bottom: 0px;
      }
      .wrapper-graph .section-left .box-coop {
        border-top: 1px solid rgba(255, 255, 255, 0.5);
        height: 55px;
        line-height: 55px;
        display: block;
        position: relative;
        padding-left: 20px;
      }
      .wrapper-graph .section-left .box-coop::before {
        position: absolute;
        left: 0px;
        top: 0px;
        bottom: 0px;
        display: block;
        width: 15px;
        content: ' ';
      }
      .wrapper-graph .section-left .box-coop.last-child {
        border-bottom: 1px solid rgba(255, 255, 255, 0.5);
      }
      /*
      .wrapper-graph .section-left .box-coop.item-01::before { background-color: #D8D8D8; }
      .wrapper-graph .section-left .box-coop.item-02::before { background-color: #2AA8A6; }
      .wrapper-graph .section-left .box-coop.item-03::before { background-color: #007912; }
      .wrapper-graph .section-left .box-coop.item-04::before { background-color: #FFAB1F; }
      .wrapper-graph .section-left .box-coop.item-05::before { background-color: #004A6B; }
      .wrapper-graph .section-left .box-coop.item-06::before { background-color: #FA4858; }
      .wrapper-graph .section-left .box-coop.item-07::before { background-color: #6B4CDB; }
      .wrapper-graph .section-left .box-coop.item-08::before { background-color: #9D1E9C; }
      .wrapper-graph .section-left .box-coop.item-09::before { background-color: #0189C9; }
      .wrapper-graph .section-left .box-coop.item-10::before { background-color: #F347BD; }
      .wrapper-graph .section-left .box-coop.item-11::before { background-color: #9D1E9C; }
      */

      .wrapper-graph .section-left .box-coop .title {
        font-size: 18px;
        font-weight: 300;
        text-align: left;
        height: 54px;
        overflow: hidden;
      }
      .wrapper-graph .section-left .box-coop .num {
        position: absolute;
        text-align: right;
        left: 0px;
        right: 10px;
        top: 0px;
        bottom: 0px;
        line-height: 55px;
        font-size: 30px;
      }

      #canvas-holder .chart-inner {
        position: absolute;
        left: 0px;
        top: calc((100% / 2) - (68px / 2));
        font-size: 45px;
        line-height: 68px;
        right: 0px;
        text-align: center;
      }

    </style>