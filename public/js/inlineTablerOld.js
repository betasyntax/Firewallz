(function( $ ) { 
  $.fn.inlineTabler = function(options) {

    var settings = $.extend(options);
    var fieldTypes = settings.fieldTypes;
    var lock2= 0;
    var value = '';
    var newValue = '';
    var id = null;
    var data = null;

    $(this).append('<thead><tr></tr></thead><tbody></tbody>');
    for (i=0;i<settings.header.length;i++)
        $('#data thead tr').append('<td>'+settings.header[i]+'</td>');
    $('#data thead tr').append('<td>Options</td>');
    elem = this;
    $.ajax({
      method: settings.methodType,
      url: settings.dataUrl,
      success: function(data) {
        window.datat = $.parseJSON(data);
        window.masterdata = $.parseJSON(data);
        window.totalPages = 0;
        window.start = 0;

        // $(elem).after( '<div id="pager">Total Records: '+window.datat.length+'</div>' );
        $(elem).after( '<div id="pager"></div>' );
        console.log('Total rows: '+window.datat.length);
        console.log('Rows per page: '+settings.rowsPerPage);
        if(window.datat.length > settings.rowsPerPage) {
          tPages = window.datat.length / settings.rowsPerPage;
          if (tPages==window.datat.length) {
            window.totalPages = window.datat.length / settings.rowsPerPage;
            console.log(totalPages);
            console.log(parseInt(totalPages));
          } else {
            window.lastPage = (parseInt(window.totalPages) * settings.rowsPerPage+1 );
            window.totalPages = window.datat.length / settings.rowsPerPage;
            console.log(totalPages);
            console.log(parseInt(totalPages));
          }
          if((settings.rowsPerPage % window.datat.length)<0)
            // window.totalPages = window.totalPages + 1;
            console.log(settings.rowsPerPage % window.datat.length);
            console.log('lastPage = '+window.lastPage);
            window.totalPages = window.totalPages + window.lastPage;
        } else {
          window.totalPages = 1
        }
        console.log('Total pages:' +parseInt(window.totalPages));
        console.log('Start:' +window.start);
        pager = '';
        for (i=1;i<window.totalPages;i++) {
          pager += '<a href="javascript:;" class="pager">'+i+'</a> ';
        }
        console.log(pager);
        $('#pager').html(pager);

        getRows();

        // pager clicks
        $('#pager').on('click','a', function(e) {
          e.preventDefault();
          e.stopPropagation();
          x = $(e.currentTarget).html();
          window.start = x * settings.rowsPerPage - settings.rowsPerPage;
          $('#data tbody tr').remove();
          getRows();
        });

        // click on an element to bring up the inline editor
        if(settings.editor) {
          $('tbody tr td').on('click','span',function(e) {
            var elem = $(this);
            if(lock2 != 1 && id != value){
              lock2 = 1;
              value = elem.html();
              column = $(e.currentTarget).next().val();
              id = $(this).parent().parent().find('td:first-child em').html();
              tdelem = elem.parent();
              h = '';
              if(fieldTypes.length >= 0) {
                if(fieldTypes[0][0] == column) {
                  if(fieldTypes[0][1]=='option') {
                    h = selectField(column, value);
                  } 
                }else {
                  h = '<input id="field-value" type="text" value="'+ value +'" />';
                }
              } 
              h += '<input id="update-data" type="button" value="" title="Save this change"/>';
              h += '<input id="cancel-data" type="button" value="" title="Cancel the change"/>';
              h += '<input class="column" type="hidden" value="'+column+'"/>';
              h += '<input class="column2" type="hidden" value="'+settings.columns[column]+'"/>';
              h += '<input id="columnid" type="hidden" value="'+id+'" />';
              elem.parent().empty();
              tdelem.html(h);
            }
            return false;
          });      
        }

        // mouseover and mouseout for the delete button
        $('td').on('mouseover','.delete',function(e) {
          $(e.currentTarget).css('background','url(/img/edit.png) -32px 0px no-repeat');
        }).on('mouseout','.delete',function(e) {
          $(e.currentTarget).css('background','url(/img/edit.png) -48px 0px no-repeat');
        }).on('click','.delete',function(e) {
          x = $(e.currentTarget);
          id = x.attr('id');
        });

        $('td').on('click','#cancel-data',function(e) {
          column = $(e.currentTarget).parent().find('input.column').val();
          restoreField(e,1,value);
        });

        function getRows() {
          console.log(settings.rowsPerPage); 
          console.log('start' +window.start);
          cont = '';
          cnt = 0;
          for (i=window.start;cnt<settings.rowsPerPage;i++) {
            cnt++;
            if(i<window.datat.length) {
              cont += '<tr>'
              console.log('i = '+i);
              if(isset(window.datat[i])) {
                for (j=0;j<window.datat[i].length;j++) {
                  if (settings.protected[0] == j) {
                    cont += '<td><em>'+window.datat[i][j]+'</em><input type="hidden" class="column" value="'+j+'"/></td>'
                  } else {
                    cont += '<td><span>'+window.datat[i][j]+'</span><input type="hidden" class="column" value="'+j+'"/></td>'
                  }
                }
              }
              cont += '<td><input type="button" class="delete" id="'+window.datat[i][0]+'" value="" title="Delete"></td></tr>';
            }
          }

            $('#data tbody').html(cont);
          // window.start = 0;
        }

        function flash(type,msg) {
            $('.flash.'+type).html(msg);
            $('.flash.'+type).slideDown(function() {
              setTimeout(function() {
                $('.flash.'+type).slideUp();
              }, 5000);
            });
        }
        function isset ( strVariableName ) { 

          try { 
              eval( strVariableName );
          } catch( err ) { 
              if ( err instanceof ReferenceError ) 
                 return false;
          }

          return true;

       } 
        function selectField(column,value) {
          cont='';
          if(fieldTypes.length >= 0) {
          console.log(column);
            if(fieldTypes[0][0] == column) {
          console.log(2);
              if(fieldTypes[0][1]=='option') {
                console.log(3);
                cont += '<select id="field-value">';
                for (s=0;s<fieldTypes[0][2].length;s++){
                  t2='';
                  if (fieldTypes[0][2][s][1]==value) {
                    t2 += ' selected'
                  }
                  cont += '<option value="'+fieldTypes[0][2][s][1]+'"'+t2+'>'+fieldTypes[0][2][s][0]+'</option> ';
                }
                cont += '</select>';
              }
            } 
          }
                console.log(cont);
          return cont;
        }

        $('td').on('keypress','#field-value',function(e) {
          if(e.which == 13) {
            updateData(e);
          }
        });
        // click on the checkmark to update our data array 
        $('td').on('click','#update-data',function(e) {
          updateData(e);
        });
        $('td').on('keyup','#field-value',function(e) {
          if(e.which == 27) {
            restoreField(e,1,value);
          }
        });

        function updateData(e) {      
          data = window.datat;
          master = window.masterdata;
          id = $('input#columnid').val();
          column = $(e.currentTarget).parent().find('input.column').val();
          newValue = $(e.currentTarget).parent().find('input#field-value').val();
          for(p=0;p<data.length;p++) {
            if ((data[p][0] == id) && (data[p][column]!= newValue)) {
              data[p][column] = newValue;
              t = e;
              $.ajax({
                method: 'POST',
                url: settings.updateUrl,
                data: { id:id,field:settings.columns[column],value:newValue },
                success: function(data) {
                  if (data == 'true') {
                    flash('success','<p>Record updated. To update the server click the Update button.</p>');
                    $('#update-dnsmasq').attr('class','');
                    value = newValue;
                    restoreField(e,lock2,newValue);
                  } else {
                    flash('error',data);
                    restoreField(e,lock2,value);
                  }
                }
              });
            } 
          }
        }

        function restoreField(e,lock,nvalue) {
          if(lock == 1) {
            lock2 = 0;
            e= $(e.currentTarget);
            t = nvalue;
            x = $(e).parent();
            z = $(e).parent().find('*');
            y = $(e).parent();
            for (index = 0; index < z.length; ++index) {
              $(z[index]).remove();
            }
            b = '<span>'+t+'</span><input type="hidden" id="column" value="'+column+'"/>';
            $(y).append(b);
          }
        }
      }

    });

  }; 
}( jQuery ));