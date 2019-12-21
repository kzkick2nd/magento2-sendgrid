function init_sendgrid_settings($)
{
  if ($('#send-method').find('option:selected').val() == 'api' ) {
    $('#sg-smtp-port').hide();
  } else if ($('#send-method').find('option:selected').val() == 'smtp' ) {
    $('#sg-smtp-port').show();
  }

  $('#send-method').change(function () {
    var sendMethod = $(this).find('option:selected').val();
    if (sendMethod == 'api' ) {
      $('#sg-smtp-port').hide();
    } else {
      $('#sg-smtp-port').show();
    }
  });
}