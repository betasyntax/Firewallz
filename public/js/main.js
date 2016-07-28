$( window ).load(function() {
  $( document ).ready(function() {
    // $('ul li').hover(function() {
    //   d=$(this).find('ul');
    //   $(d).show();
    // },
    // function() {
    //   $(this).find('ul').first().hide();
    // });

    // $('.setup-wizard a.edit.wan').click(function(e){
    //   if($('form#edit-wan').css('display')=='none') {
    //     $('form#edit-wan').show();
    //     $('#wan').hide();
    //   } else {
    //     $('form#edit-wan').hide();
    //     $('#wan').show();
    //   }
    // });
    // $('#external_iface_type').change(function(e) {
    //   if($(e.currentTarget).find(":selected").val()=='static') {
    //     $('#wan-form').show();
    //   } else {
    //     $('#wan-form').hide();
    //   }
    // });
    clammer($('#wan'),$('#edit-wan'),$('.edit.wan'))
    clammer($('#lan'),$('#edit-lan'),$('.edit.lan'))
    clammer($('#dns'),$('#edit-dns'),$('.edit.dns'))
    clammerType($('#lan-form'),$('#internal_iface_type'));
    clammerType($('#wan-form'),$('#external_iface_type'));

    if($('#external_iface_type').find(":selected").val()=='dhcp') {
      $('#wan-form').hide();
    }
    if($('#internal_iface_type').find(":selected").val()=='dhcp') {
      $('#lan-form').hide();
    }

    if($('.alert.alert-danger').text().length>0){
      $('.alert.alert-danger').css('display','block');
    }
    if($('.alert.alert-success').text().length>0){
      $('.alert.alert-success').css('display','block');
    }
    setTimeout(function() {
      $('.alert').slideUp();
    }, 5000);
    $('#update-dnsmasq').click(function(e) {
      console.log(e);
      $.ajax({
        method:'GET',
        url:'/dhcp/update-hosts',
        success:function(msg){
          $('#update-dnsmasq').attr('class','disabled');
          console.log(msg);
        }
      });
    });
    $('#update-sites').click(function(e) {
      console.log(e);
      $.ajax({
        method:'GET',
        url:'/proxy/update-proxy',
        success:function(msg){
          console.log(msg);
          // $('#update-sites').attr('class','disabled');
        }
      });
    });

  });
});

function clammerType(elem,clicker) {
  $(clicker).on('change',function(e) {
    e.preventDefault;
    console.log($(this).find(":selected").val());
    if($(this).find(":selected").val()=='static') {
      $(elem).show();
    } else {
      $(elem).hide();
    }
  });
}

function clammer(elem,elem2,clicker) {
  $(clicker).click(function(e){
    e.preventDefault;
    if($(elem2).css('display')=='none') {
      $(elem2).show();
      $(elem).hide();
    } else {
      $(elem2).hide();
      $(elem).show();
    }
  });
}

function getHeight(elem) {
  var amount = $(elem).attr('data-amount'),
  height = amount * 80/100 + 20;
  console.log(height);
  return height;
}

  $('.outer').each(function(i,e) {
    amount = $(e).attr('data-amount');
    console.log(amount)
    console.log($(e).find('.inner'));
    x =$(e).find('.inner');

    // console.log()
    height = amount;
    $(x).css('width',height+'%');
    console.log(x)
    $(e).find('.used').html(parseFloat(Math.round(amount * 100) / 100).toFixed(2) + '% used');
    $(x).html('&nbsp;');
  });

// (function( $ ){
//     // var getStringForElement = function (el) {
//     //     var string = el.tagName.toLowerCase();

//     //     if (el.id) {
//     //         string += "#" + el.id;
//     //     }
//     //     if (el.className) {
//     //         string += "." + el.className.replace(/ /g, '.');
//     //     }

//     //     return string;
//     // };

//     // $.fn.getDomPath = function(string) {
//     //     if (typeof(string) == "undefined") {
//     //         string = true;
//     //     }

//     //     var p = [],
//     //         el = $(this).first();
//     //     el.parents().not('html').each(function() {
//     //         p.push(getStringForElement(this));
//     //     });
//     //     p.reverse();
//     //     p.push(getStringForElement(el[0]));
//     //     return string ? p.join(" > ") : p;
//     // };
// })( jQuery );
