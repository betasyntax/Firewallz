(function( $ ) { 
  $.fn.inlineNetter = function(options) {
    var settings = $.extend(options);
    var netter = {
      'elem':$(this),
      'width':0,
      'total_bandwidth':null,
      'type':'',
      'url':'',
      'init':function(){
        netter._setup();
      },
      'extif':settings.extif,
      '_setup':function(){
        netter.total_bandwidth = settings.max;
        netter.type = settings.txType;
        netter.url = settings.dataUrl;
        if(settings.txType=='up') {
          title="Tx Rate";
        } else {
          title="Rx Rate";
        }
        $(netter.elem).append('<div class="label">'+title+':</div>')
        $(netter.elem).append('<div class="meter-outer"><div class="meter"><span>0</span> Mbits/s</div></div>');
        var x = setInterval(function(){netter.getSpeed(netter.elem)},settings.interv);
      },
      'getSpeed':function(ele){
        var xhr = $.ajax({
          'method':'GET',
          'url':netter.url,
          'success': function(data) {
            d = $.parseJSON(data);
            el=$(netter.elem).find('.meter span');
            u = d.tx*8/1024/1024;
            d = d.rx*8/1024/1024;
            if (netter.type=='up') {
              length = netter.round(u/netter.total_bandwidth*100,0);
              $(el).html(netter.round(u,2));
            } else if (netter.type=='down') {
              length = netter.round(d/netter.total_bandwidth*100,0);
              $(el).html(netter.round(d,2));
            }
            el = $(netter.elem).find('.meter')
            $(el).css('width',length+'%');
            return false;
          }
        }).done(function(){
          xhr.abort();
        });
      },
      'round':function(value, exp){
          if (typeof exp === 'undefined' || +exp === 0)
            return Math.round(value);
          value = +value;
          exp = +exp;
          if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0))
            return NaN;
          // Shift
          value = value.toString().split('e');
          value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));
          // Shift back
          value = value.toString().split('e');
          return +(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp));
      }
    };
    netter._setup();
  }
}( jQuery ));