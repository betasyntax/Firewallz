
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
var closeModal = function(e) {
  $(".close-modal").click(function(e) {
    // console.log('test');
    $(".overlay .inner .add-record").show(); 
    $(".overlay").hide();
    $(".overlay").html('');
    $(".close-modal").hide();  
  });
};
var showModal = function(e) {
  $(".overlay").show();
  $(".overlay .inner .add-record").hide();
  if ($('.overlay').is(':empty')){
    $(".overlay").html("<div class='outer'><div class='inner'></div></div>");
  }
};