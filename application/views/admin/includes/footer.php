 <script src="//cdn.jsdelivr.net/webshim/1.14.5/polyfiller.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
  webshims.setOptions('forms-ext', {
    types: 'date'
  });
  webshims.setOptions('forms-ext', {
    types: 'month'
  });
  webshims.polyfill('forms forms-ext');
  //reset telko
  $(document).ready(function() {
    $("#divResults").hide();
    $('.order_reset_btn').on('click', function() {
      var order_code = $(this).attr('data-order-id');
      var order_type = $(this).attr('data-reset-order-type');
      $.ajax({
        type: "POST",
        url: "/admin/reset_telkom_stats",
        data: {
          request_code: order_code,
          order_type: order_type
        },
        dataType: "json",
        success: function(response) {
          $("#prev_total_cap").empty();
          $("#prev_night_cap").empty();
          $("#prev_anytime_cap").empty();
          $("#divResults").show();
          $("#divResults").empty().append(response.msg);
          setTimeout(function() {
            location.reload();
          }, 2000);
        }
      });

    });
    $('.order_delete_btn').on('click', function() {
      var order_code = $(this).attr('data-order-id');
      var order_type = $(this).attr('data-reset-order-type');
      $.ajax({
        type: "POST",
        url: "/admin/delete_status_request",
        data: {
          request_code: order_code,
          order_type: order_type
        },
        dataType: "json",
        success: function(response) {
          $("#divResults").show();
          $("#divResults").empty().append(response.msg);
          setTimeout(function() {
            location.reload();
          }, 2000);
        }
      });

    });
    var ref_order_code = '';
    $('.send_telkom_stats_btn').on('click', function() {
      var ref_order_code = $(this).attr('data-send-order-id');
      var ref_email = $(this).attr('data-send-email-id');
      var ref_name = $(this).attr('data-send-name-id');
      //prev caps
      var ref_total_cap = $(this).attr('data-prev-total-cap');
      var ref_night_cap = $(this).attr('data-prev-night-cap');
      var ref_anytime_cap = $(this).attr('data-prev-anytime-cap');
      var order_type = $(this).attr('data-order-type');
      var simnumber = $(this).attr('data-prev-simnumber');

      $("#prev_total_cap").empty().append(ref_total_cap + ' GB');
      $("#prev_night_cap").empty().append(ref_night_cap + ' GB');
      $("#prev_anytime_cap").empty().append(ref_anytime_cap + ' GB');


      ///mobile
      var prev_minutes = $(this).attr('data-prev-minutes');
      var prev_data = $(this).attr('data-prev-data');
      var prev_sms = $(this).attr('data-prev-sms');

      if (prev_data == '....') {
        prev_data = 'x';
      }
      if (prev_minutes == '....') {
        prev_minutes = 'x';
      }
      if (prev_sms == '....') {
        prev_sms = 'x';
      }

      $("#prev_minutes").empty().append(prev_minutes + ' Min');
      $("#prev_data").empty().append(prev_data + ' GB');
      $("#prev_sms").empty().append(prev_sms + ' SMS');


      ///general
      $('#hidden_order_id').attr('value', ref_order_code);
      $('#hidden_user_email').attr('value', ref_email);
      $('#hidden_user_name').attr('value', ref_name);
      $('#hidden_order_type_name').attr('value', order_type);
      $('#hidden_simnumber').attr('value', simnumber)
    });

    $('#telkomSaveBtn').on('click', function() {
      //values
      var order_code = $('#hidden_order_id').val();
      var total_cap = $('input[name=total_cap]').val();
      var night_cap = $('input[name=night_cap]').val();
      var anytime_cap = $('input[name=anytime_cap]').val();
      var user_email = $('input[name=user_email]').val();
      var user_name = $('input[name=user_name]').val();
      var order_type = $('input[name=order_type]').val();
      var simnumber = $('input[name=simnumber]').val();
      $.ajax({
        type: 'POST',
        url: '/admin/send_telkom_request_stats',
        data: {
          order_code: order_code,
          total_cap: total_cap,
          night_cap: night_cap,
          anytime_cap: anytime_cap,
          user_email: user_email,
          user_name: user_name,
          order_type: order_type,
          simnumber: simnumber
        },
        dataType: 'json',
        success: function(response) {
          $('#myModal').modal('toggle')
          // $('#hidden_order_id').val('');
          // $('input[name=total_cap]').val('');
          // $('input[name=night_cap]').val('');
          // $('input[name=anytime_cap]').val('');
          // $('input[name=user_email]').val('');

          $("#prev_total_cap").empty();
          $("#prev_night_cap").empty();
          $("#prev_anytime_cap").empty();
          $("#divResults").show();
          $("#divResults").empty().append(response.msg);
          setTimeout(function() {
            $("#divResults").hide();
            location.reload();
          }, 3000);

        }
      });
    });




    //Topup request btn
    $('.order_topup_request_view_btn').on('click', function() {
      var topup_name = $(this).attr('data-brought-topup');
      var topup_price = $(this).attr('data-brought-topup-price');
      var topup_date = $(this).attr('data-brought-topup-date');
      var topup_id = $(this).attr('data-brought-topup-id');
      var topup_buyer_name = $(this).attr('data-brought-send-name-id');
      var topup_buyer_email = $(this).attr('data-brought-send-email-id');
      var topup_order_id = $(this).attr('data-brought-order-id');

      setTimeout(function() {
        $('#table_topup_name').text(topup_name)
        $('#table_topup_price').text('R' + topup_price)
        $('#table_transaction_date').text(topup_date)
        //form values 
        $('input[name=topuploaded_id]').val(topup_id);
        $('input[name=topuploaded_name]').val(topup_name);
        $('input[name=topuploaded_price]').val(topup_price);
        $('input[name=topuploaded_buyer_name]').val(topup_buyer_name);
        $('input[name=topuploaded_buyer_email]').val(topup_buyer_email);
        $('input[name=topuploaded_order_id]').val(topup_order_id);

      }, 100);

    })
    $('#telkomTopupSuccessBtn').on('click', function() {
      $("#myModalTopupRequest").modal('toggle')
      var topup_id = $('input[name=topuploaded_id]').val();
      var topup_name = $('input[name=topuploaded_name]').val();
      var topup_price = $('input[name=topuploaded_price]').val();
      var topuploaded_buyer_name = $('input[name=topuploaded_buyer_name]').val();
      var topuploaded_buyer_email = $('input[name=topuploaded_buyer_email]').val();
      var topuploaded_order_id = $('input[name=topuploaded_order_id]').val();
      $.ajax({
        type: "POST",
        url: "/admin/telkom_topup_loaded",
        data: {
          l_topup_id: topup_id,
          l_topup_name: topup_name,
          l_topup_price: topup_price,
          topup_buyer_name: topuploaded_buyer_name,
          topup_buyer_email: topuploaded_buyer_email,
          topup_order_id: topuploaded_order_id
        },
        dataType: "json",
        success: function(response) {
          $("#divResults").show();
          $("#divResults").empty().append(response.msg);
          setTimeout(function() {
            $("#divResults").hide();
            location.reload();
          }, 2000);
        }
      });
    });
    $('#mtnTopupSuccessBtn').on('click', function() {
      $("#myModalTopupRequest").modal('toggle')
      var topup_id = $('input[name=topuploaded_id]').val();
      var topup_name = $('input[name=topuploaded_name]').val();
      var topup_price = $('input[name=topuploaded_price]').val();
      var topuploaded_buyer_name = $('input[name=topuploaded_buyer_name]').val();
      var topuploaded_buyer_email = $('input[name=topuploaded_buyer_email]').val();
      var topuploaded_order_id = $('input[name=topuploaded_order_id]').val();
      $.ajax({
        type: "POST",
        url: "/admin/mtn_topup_loaded",
        data: {
          l_topup_id: topup_id,
          l_topup_name: topup_name,
          l_topup_price: topup_price,
          topup_buyer_name: topuploaded_buyer_name,
          topup_buyer_email: topuploaded_buyer_email,
          topup_order_id: topuploaded_order_id
        },
        dataType: "json",
        success: function(response) {
          $("#divResults").show();
          $("#divResults").empty().append(response.msg);
          setTimeout(function() {
            $("#divResults").hide();
            location.reload();
          }, 2000);
        }
      });
    });
    $('#telkomClearBtn').on('click', function(e) {
      if (confirm('Are you sure you want to clear records ?')) {
        var order_type = $(this).attr('data-order-clear-all');
        $.ajax({
          type: "POST",
          url: "/admin/telkom_topup_temp_removed",
          data: {
            confirmed: true,
            order_type: order_type
          },
          dataType: "json",
          success: function(response) {
            $("#divResults").show();
            $("#divResults").empty().append(response.msg);
            setTimeout(function() {
              $("#divResults").hide();
              location.reload();
            }, 2000);
          }
        });
      }
    })
  });

  $(function() {
    $('#toggle-event').change(function() {
      var toggleVal = $(this).prop('checked')

      $.ajax({
        url: "/admin/toggleLteMail",
        dataType: "json",
        data: {
          toggle: toggleVal
        },
        success: function(data) {
          $('#console-event').text(data.msg)
          setTimeout(function() {
            $('#console-event').text('');
          }, 2000);
        }
      });
    })
  })
  $(document).ready(function() {
    $.ajax({
      url: "/admin/getToogle",
      dataType: "json",
      success: function(data) {
        setTimeout(function() {

          if (data === 'true') {
            console.log(data)
            $('#toggle-event').prop('checked', data).change()
          }

        }, 2000);

      }
    });
  })
  $(document).ready(function() {
    $('#locationSelector').on('change', function() {
      var value = $(this).val();
      if (value == 'LocationUnlock') {
        $('#locationBox').show()
        setTimeout(function() {
          $("#lat-f").prop('required', true);
          $("#long-f").prop('required', true);
        }, 100)
      } else {
        $('#locationBox').hide()
        $("#lat-f").prop('required', false);
        $("#long-f").prop('required', false);
      }
    })
  })


  ///data tables
  $(document).ready(function() {
    $('#example').DataTable();
  });

  $("#mobile-stats-form").submit(function(event) {
    event.preventDefault();
    event.stopPropagation();
    /* Get from elements values */
    var values = $(this).serialize();

    $.ajax({
      url: '/admin/send_mobile_request_stats',
      type: "post",
      data: values,
      success: function(response) {
        let res = JSON.parse(response);
        $("#divResults").show();
        $("#divResults").empty().append(res.msg);
        setTimeout(function() {
          $("#divResults").hide();
          location.reload();
        }, 3000);

        // You will get response from your PHP page (what you echo or print)
      },

    });

  })
  $("#mobile-topuploaded-form").submit(function(event) {
    event.preventDefault();
    event.stopPropagation();
    /* Get from elements values */
    var values = $(this).serialize();

    $.ajax({
      url: '/admin/mobile_topup_loaded',
      type: "post",
      data: values,
      success: function(response) {
        let res = JSON.parse(response);
        $("#divResults").show();
        $("#divResults").empty().append(res.msg);
        setTimeout(function() {
          $("#divResults").hide();
          location.reload();
        }, 3000);

        // You will get response from your PHP page (what you echo or print)
      },

    });

  })
</script>

</div>
</div>
</div>
<div class="footer col-lg-12">
  <div class="container footer-inner">
    <p class="copyright">Copyright &copy; OpenWeb. All rights reserved.</p>
    <ul class="footerNav">
      <li><a target="_new" href="<?php echo TERMS_OF_SERVICE_LINK; ?>">Privacy policy</a></li>
      <li><a target="_new" href="<?php echo SUPPORT_LINK; ?>">Contact us</a></li>
    </ul>
  </div>
</div>
</body>

</html>