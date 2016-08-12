$( window ).load(function() {
  $( document ).ready(function() {
    if($('.alert.alert-danger').text().length>0){
      $('.alert.alert-danger').css('display','block');
    }
    if($('.alert.alert-success').text().length>0){
      $('.alert.alert-success').css('display','block');
    }
    setTimeout(function() {
      $('.alert').slideUp();
    }, 5000);
  });
});
