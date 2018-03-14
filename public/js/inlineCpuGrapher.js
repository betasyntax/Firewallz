(function( $ ) { 
  $.fn.inlineCpuGrapher = function(options) {
    var settings = $.extend(options);
    var netter = {
      'elem':$(this),
      'procs':null,
      'max_length':100,
      // 'type':'',
      'url':'',
      'xfr':true,
      'init':function(){
        netter._setup();
      },
      'extif':settings.extif,
      '_setup':function(){
        // netter.total_bandwidth = settings.max;
        // netter.type = settings.txType;
        netter.url = settings.dataUrl;
        netter.procs = settings.totalCpus;
        // if(settings.txType=='up') {
          // title="Tx Rate";
        // } else {
        for(i=0;netter.procs>i;i++) {
          title="CPU("+i+")";
          $(netter.elem).append('<div class="cpuGrapher" id="cpu'+i+'"><div class="label">'+title+':</div><div class="meter-outer"><div class="meter"><span>0</span> %</div></div></div>');
          // $(netter.elem).append('');
        }
        var x = setInterval(function(){netter.getSpeed(netter.elem)},settings.interv);
      },
      'getSpeed':function(ele){
        if(netter.xfr) {
          netter.xfr = false;
          var xhr = $.ajax({
            'method':'GET',
            'url':netter.url,
            'success': function(data) {
              d = $.parseJSON(data);
              el1=$(netter.elem).find('.meter span');
              // u = d.tx*8/1024/1024;
              // d = d.rx*8/1024/1024;
              // if (netter.type=='up') {
              //   // length = netter.round(u/netter.total_bandwidth*100,0);
              //   $(elsel).html(netter.round(u,2));
              // } else if (netter.type=='down') {

              //loop through each cpu and update the width and percentage for each node 
              // console.log(Object.keys(d).length);
              // console.log(d);
              for(i=0;d.length>i;i++) {

                length = Math.round(d[i]);

                // $(el).html(netter.round(d,2));
                el2 = $("#cpu"+i).find('.meter');
                // console.log($("#cpu"+i+" .meter span"));
                x = $("#cpu"+i+" .meter").find("span");
                x.html(Math.round(d[i]));
                // console.log(x.html());
                // console.log(el2);
                if(length > 100) {
                  length == 100;
                }
                $(el2).css('width',length+'%');

                netter.xfr = true;
              }
              // return false;
            }
          }).done(function(){
            xhr.abort();
          });
        }
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