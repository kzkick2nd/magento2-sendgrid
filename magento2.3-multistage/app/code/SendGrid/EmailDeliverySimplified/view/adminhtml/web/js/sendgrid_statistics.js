var SGStatistics = {
  // Chart configuration
  sg_stats_config : {
    type: 'line',
    data: {
      labels: [],
      datasets: []
    },
    options: {
      responsive: true,
      title: {
        display: true,
        fontSize: 14,
        text: 'SendGrid Statistics'
      },
      tooltips: {
        mode: 'label',
        callbacks: { }
      },
      hover: {
        mode: 'dataset'
      },
      legend: {
        position: 'bottom',
        labels: {
          boxWidth: 10
        },
      },
      scales: {
        xAxes: [{
          display: true,
          scaleLabel: {
            show: true,
            labelString: 'Date'
          }
        }],
        yAxes: [{
          display: true,
          scaleLabel: {
            show: true,
            labelString: 'Events'
          },
          ticks: {
            suggestedMin: 0,
            suggestedMax: 50,
          }
        }]
      }
    }
  },

  // Chart context
  sg_stats_ctx : null,

  // Chart colors
  sg_colors : {
    "unique_opens":       "#fbe500",
    "opens":              "#028690",
    "unique_clicks":      "#bcd0d1",
    "clicks":             "#59c1ca",
    "delivered":          "#bcd514",
    "bounces":            "#c042be",
    "unsubscribes":       "#3e44c0",
    "requests":           "#246201",
    "spam_reports":       "#e04427",
    "spam_report_drops":  "#d59f7f",
    "blocks":             "#aa0202",
    "unsubscribe_drops":  "#6dc2f9",
    "invalid_emails":     "#6b6b6b",
    "bounce_drops":       "#ff748c"
  },

  // get stats base url
  base_url              : null,
  // token invalidate url
  invalidate_token_url  : null,
  // generated token for this page
  token                 : null,
  // local jquery reference
  my_jquery             : null,
  // start date for stats
  start_date            : null,
  // end date for stats
  end_date              : null,
  // selected category
  category              : null,
  // default days to display
  default_days_before   : 7,

  // Initialization method
  init : function ( $ ) {
    // copy jquery reference (might not exist in window)
    this.my_jquery = $;

    // default category
    this.category = "magento2_sendgrid_plugin";

    // init date pickers
    this._init_dates();

    // get data from page
    this.base_url = this.my_jquery("#statistics-base-url").val();
    this.invalidate_token_url = this.my_jquery("#statistics-invalidate-token-url").val();
    this.token = this.my_jquery("#statistics-token").val();

    // register invalidation logic
    this.my_jquery(window).unload(this.my_jquery.proxy(function () {
      //call do request with reset token url
      var invalidate_url = this.invalidate_token_url + this.token;
      this.do_request(invalidate_url, function () { }, false);
    }, this));

    // init chart
    this.sg_stats_ctx = document.getElementById("sg_stats_canvas").getContext("2d");
    window.SGStatsChart = new Chart(this.sg_stats_ctx, this.sg_stats_config);
    
    // add stats refresh action to apply button
    this.my_jquery("#sendgrid-stats-apply").click(
        this.my_jquery.proxy(
            function () {
            this.start_date = this.my_jquery("#sendgrid-start-date").val();
            this.end_date = this.my_jquery("#sendgrid-end-date").val();
            this.category = this.my_jquery("#sendgrid-category").val();

            this.refresh_stats();
            },
            this
        )
    );

    // update the stats
    this.refresh_stats();
  },

  // Statistics refresh method
  refresh_stats : function () {
    var stats_url = this.base_url + this.category + "/" + this.start_date + "/" + this.end_date;
    this.do_request(stats_url, this.update_stats, true);
  },

  // request method
  do_request: function ( stats_url, callback, do_async ) {
    // display the loading animation
    this.my_jquery("#sendgrid_error").hide();
    this.my_jquery("#sendgrid_stats_load").show();

    // send the stats request
    this.my_jquery.ajax({
      url: stats_url,
      context: this,
      async: do_async,
      method: "GET",
      headers: {
        "Authorization": "Bearer " + this.token
      },
      data: {form_key: window.FORM_KEY},
      success: callback,
      error: this.my_jquery.proxy(function () {
        this._display_error();
      }, this)
    });
  },

  // update statistics chart method
  update_stats: function ( stats ) {
    var stats = JSON.parse(stats);

    if (! stats.dates.length ) {
      this._display_error();
    }

    this.sg_stats_config.data.labels = stats.dates;
    this.sg_stats_config.data.datasets = [];
    for (var key in stats.metrics) {
      if (typeof this.sg_colors[key] != 'undefined' ) {
        var set = {
          label: stats.metrics[key].label,
          data: stats.metrics[key].values,
          lineTension: 0,
          fill: false,
          borderColor: this.sg_colors[key],
          backgroundColor: this.sg_colors[key],
          pointBorderColor: this.sg_colors[key],
          pointBackgroundColor: this.sg_colors[key],
          pointBorderWidth: 1
        };

        this.sg_stats_config.data.datasets.push(set);
      }
    }

    window.SGStatsChart.update();
    this.my_jquery("#sendgrid_stats_load").hide();
  },

  // convert js date to required format method
  _date_to_ymd: function ( date ) {
    var d = date.getDate();
    var m = date.getMonth() + 1;
    var y = date.getFullYear();
    
    return "" + y + "-" + (m <= 9 ? "0" + m : m) + "-" + (d <= 9 ? "0" + d : d);
  },

  // initialize datepickers method
  _init_dates: function () {
    // init date picker
    var date = new Date();

    this.my_jquery("#sendgrid-start-date").datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      maxDate: this._date_to_ymd(new Date()),
      onClose: this.my_jquery.proxy(function ( selected_date ) {
        this.my_jquery("#sendgrid-end-date").datepicker("option", "minDate", selected_date);
      }, this)
    });
    initial_start_date = new Date(date.getFullYear(), date.getMonth(), date.getDate() - this.default_days_before);
    this.my_jquery("#sendgrid-start-date").datepicker("setDate", initial_start_date);
    this.start_date = this._date_to_ymd(initial_start_date);

    this.my_jquery("#sendgrid-end-date").datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      maxDate: this._date_to_ymd(new Date()),
      onClose: this.my_jquery.proxy(function ( selected_date ) {
        this.my_jquery("#sendgrid-start-date").datepicker("option", "maxDate", selected_date);
      }, this)
    });
    initial_end_date = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    this.my_jquery("#sendgrid-end-date").datepicker("setDate", initial_end_date);
    this.end_date = this._date_to_ymd(initial_end_date);
  },

  // display error on page method
  _display_error: function () {
    this.my_jquery("#sendgrid_stats_load").hide();
    this.my_jquery("#sendgrid_error").show();
  }
}