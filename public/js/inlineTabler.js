(function( $ ) { 
  $.fn.inlineTabler = function(options) {

    function _is_function(is_function) {
      var getType = {};
      return is_function && getType.toString.call(is_function) === '[object Function]';
    }

    var settings = $.extend({
        updateAfter:function(){}
      },
      options
    );
    var tabler = {
      'value':'', //protected
      'newValue':'', //protected
      'elem': $(this), //protected
      'lock':0, //protected
      'data': null, //protected
      'newRecord':new Array(), //protected

      'fieldTypes':settings.fieldTypes, //public
      'options':settings.options, //public
      'tableName':settings.tableName, //public
      'editor':settings.editor,//public
      'header':settings.header,//public
      'protected':settings.protected,//public
      'methodType':settings.methodType,//public
      'dataUrl':settings.dataUrl,//public
      'updateUrl':settings.updateUrl,//public
      'addUrl':settings.addUrl,//public
      'delUrl':settings.delUrl,//public
      'columns':settings.columns,//public
      'tester': null,
      'updateAfter':settings.updateAfter,
      'addAfter':settings.addAfter,
      'id':null,

      'pager':{
        'totalPages':0,
        'startPage':0,
        'rowsPerPage':settings.rowsPerPage, //public
        'lastPage':0,
        'currentPage':0,
      },
      'render':function(){
        tabler._initVars();
        tabler._get_data();
      },
      '_renderInner':function(){

        tabler._get_table_header();
        tabler._get_pager();
        tabler._add_record();
        tabler._register_events();

        $("#loader").hide();
      },
      '_initVars':function(){
        if(undefined===settings.fieldTypes)
          tabler.fieldTypes = null;
        if(undefined===settings.options)
          tabler.options = null;
        if(undefined===settings.tableName)
          tabler.tableName = null;
        if(undefined===settings.editor)
          tabler.editor = null;
        if(undefined===settings.option)
          tabler.option = null;
        if(undefined===settings.option)
          tabler.option = null;
        if(undefined===settings.header)
          tabler.header = null;
        if(undefined===settings.methodType)
          tabler.methodType = null;
        if(undefined===settings.dataUrl)
          tabler.dataUrl = null;
        if(undefined===settings.updateUrl)
          tabler.updateUrl = null;
        if(undefined===settings.addUrl)
          tabler.addUrl = null;
        if(undefined===settings.delUrl)
          tabler.delUrl = null;
        if(undefined===settings.columns)
          tabler.columns = null;
        if(undefined===settings.rowsPerPage)
          tabler.rowsPerPage = null;
        if(undefined===settings.protected)
          tabler.protected = null;
        if(undefined===settings.updateAfter)
          tabler.updateAfter = null;
        if(undefined===settings.addAfter)
          tabler.addAfter = null;
      },
      '_reset':function() {
        $(tabler.elem).html('');
        $('#pager').remove();
        $('#add-record').remove();
        $('.alert').remove();
        tabler.render();
      },
      '_add_record':function() {
        if(tabler.editor) {
          $('#add-record').remove();
          $(tabler.elem).wrap('<div id="data-container"></div>');
          $(tabler.elem).before('<div id="add-record"></div>');            
          elem = $('#add-record');
          elem.html('<a class="add-record" href="javascript:">Add Record</a>')
          elem2 = $('.overlay');
          elem2.append('<div class="outer"><div class="inner"><div class="add-record"><h4>Add New Record</h4><form class="add-record" id="'+tabler.tableName+'"></form></div></div></div>');
          elem = $('#'+tabler.tableName);
          for(i=0;i<tabler.columns.length;i++) {
            if(tabler.columns[i]!='id') {
              if(tabler.fieldTypes.length > 0) {
                for(z=0;z<tabler.fieldTypes.length;z++) {

                  console.log('test '+tabler.fieldTypes[z]);
                  console.log('test '+tabler.header[i]);
                  console.log('test '+tabler.columns[i]);
                  if(tabler.fieldTypes[z][1]=='option' && tabler.fieldTypes[z][0] == i) {
                    h = tabler.selectField(i, '',tabler.columns[i]);
                  }else {
                    h = '<input name="" id="'+tabler.columns[i]+'" type="text" value="" placeholder="'+tabler.header[i]+'" />';
                    h += '<label>'+tabler.header[i]+'</label>';
                  }
                }
              } else {
                h = '<input name="" id="'+tabler.columns[i]+'" type="text" value="" placeholder="'+tabler.header[i]+'" />';
                h += '<label>'+tabler.header[i]+'</label>';
              }
              elem.append('<div>'+h+'</div>');
            }
          }
          elem.append('<div class="options"><input type="submit" value="Add" class="record-add"/><input class="cancel-add" type="button" value="Cancel" /></div>');
          $('.overlay form input.record-add').click(function(e) {
            e.preventDefault();
            f=$('#add-record form');
            var data3 = new Array();
            cnt = '{';
            for(i=0;i<tabler.columns.length;i++) {
              if(tabler.columns[i]!='id') {
                data3[i] = new Array();
                data3[i][0] = tabler.columns[i];
                if(tabler.fieldTypes.length >= 0) {
                  comma = ',';
                  if(tabler.columns.length==(i+1))
                    comma = '';
                   cnt += '"'+tabler.columns[i]+'":"'+$('#'+tabler.columns[i]).val()+'"'+comma+' ';
                } 
              }
            }
            cnt += '}';
            $('#add-record a.add-record').show();
            $('form.add-record').hide();
            tabler.remove_overlay();
            $.ajax({
              method:'POST',
              data:{data:cnt},
              url:tabler.addUrl,
              success:function(data) {
                if (data == 'true') {
                  tabler.remove_overlay();
                  tabler._reset();
                  tabler.updateAfter();
                  tabler.flash('success','<p>New record created.</p>');
                } else {
                  tabler.flash('error',data);
                }
              }
            });
          });
        }
      },
      'capitalize':function(str){
        return str && str[0].toUpperCase() + str.slice(1);
      },
      'selectField':function(column,value,id=''){
         console.log('selectField '+column+" "+value+" "+id);
        cont='';
        sel='';
        idval='';

        if(id==''){
          idval='field-value';
        } else {
          idval=id;
        }

        // console.log(cont+" "+sel+" "+idval);
        if(tabler.fieldTypes.length > 0) {
          // console.log(tabler.fieldTypes.length);
          for(z=0;z<tabler.fieldTypes.length;z++) {
            if(tabler.fieldTypes[z][0] == column) {
              // console.log(tabler.fieldTypes[z][0]+' '+column);
              if(tabler.fieldTypes[z][1]=='option') {
                // console.log('is_option');
                cont += '<select id="'+idval+'">';
                // console.log(tabler.fieldTypes[z][2]);
                for (s=0;s<tabler.fieldTypes[z][2].length;s++){
                  console.log('value1 = '+tabler.fieldTypes[z][2][s][0]);
                  console.log('value2 = '+value);
                  if (tabler.fieldTypes[z][2][s][0]==value) {
                    sel += ' selected'
                    console.log('sel ' + sel);
                  }
                  cont += '<option value="'+tabler.fieldTypes[z][2][s][1]+'"'+sel+'>'+tabler.fieldTypes[z][2][s][0]+'</option> ';
                  // console.log(cont);
                }
                cont += '</select>';
                // break;
              }
            }
          }
        }
        return cont;
      },
      'updateData':function(e){    
        data = tabler.data;
        id = $('input#columnid').val();
        column = $(e.currentTarget).parent().find('input.column').val();
        tabler.newValue = $('#field-value').val();
        for(p=0;p<data.length;p++) {
          if ((data[p][0] == id) && (data[p][column]!= tabler.newValue)) {
            data[p][column] = tabler.newValue;
            t = e;
            $.ajax({
              method: 'POST',
              url: tabler.updateUrl,
              data: { id:id,field:tabler.columns[column],value:tabler.newValue },
              success: function(msg) {
                if (msg == 'true') {
                  tabler.flash('success','<p>Record updated.</p>');
                  tabler.value = tabler.newValue;
                  tabler.restoreField(e,tabler.lock,tabler.newValue);
                  tabler.updateAfter();
                } else {
                  tabler.flash('error',msg);
                  tabler.restoreField(e,tabler.lock,tabler.value);
                }
              }
            });
          } 
        }
      },
      '_get_table_header':function(){
        $(tabler.elem).before('<div class="alert error"></div><div class="alert success"></div>');
        $('body').append('<div class="overlay"></div>');
        $(tabler.elem).append('<thead><tr></tr></thead><tbody></tbody>');
        for (i=0;i<tabler.header.length;i++)
            $('#'+$(tabler.elem).attr('id')+' thead tr').append('<td scope="col"><strong>'+tabler.header[i]+'</strong></td>');
        if(tabler.editor || tabler.option)
          console.log('tabler.option');
          $('#'+$(tabler.elem).attr('id')+' thead tr').append('<td scope="col" class="options"><strong>Options</strong></td>');
      },
      '_get_pager':function(currentPage){
        if(typeof currentPage=='undefined')
          currentPage=1;
        tabler.pager.totalPages = 0;
        $(tabler.elem).after( '<div id="pager"></div>' );
        if(tabler.data != null && tabler.data.length > tabler.pager.rowsPerPage) {
          tPages = tabler.data.length / tabler.pager.rowsPerPage;
          if (tPages==tabler.data.length) {
            tabler.pager.totalPages = tabler.data.length / tabler.pager.rowsPerPage;
          } else {
            tabler.pager.lastPage = (parseInt(tabler.pager.totalPages) * tabler.pager.rowsPerPage+1 );
            tabler.pager.totalPages = tabler.data.length / tabler.pager.rowsPerPage;
          }
          if((tabler.pager.rowsPerPage % tabler.data.length)<0)
            tabler.pager.rowsPerPage % tabler.data.length;
            tabler.pager.totalPages = tabler.pager.totalPages + tabler.pager.lastPage;
        } else {
          tabler.pager.totalPages = 1
        }
        pager = '';
        for (i=1;i<tabler.pager.totalPages;i++) {
          if(i==currentPage) {
            pager += '<span>'+i+'</span> ';
          } else {
            pager += '<a href="javascript:;" class="pager">'+i+'</a> ';
          }
        }
        $('#pager').append(pager);
        this._show_data();
      },
      'show_overlay':function(){
        $('.overlay').slideDown('slow');
      },
      'remove_overlay':function(){
        $('div.overlay').html('');
        $('div.overlay').fadeOut('slow');
      },
      'flash':function(type,msg){
        $('#add-record').hide();
        $('.alert.'+type).html(msg);
        $('.alert.'+type).slideDown(function() {
          setTimeout(function() {
            $('.alert.'+type).slideUp();
            $('#add-record').fadeIn().show();
          }, 5000);
        });
      },
      '_show_data':function(){
        cont = '';
        cnt = 0;
        row = '';
        if(tabler.pager.rowsPerPage==0)
          tabler.pager.rowsPerPage = tabler.data.length;
        for (i=tabler.pager.startPage;cnt<tabler.pager.rowsPerPage;i++) {
          cnt++;
          if(tabler.data != null && i<tabler.data.length) {
            cont += '<tr>'
              for (j=0;j<tabler.data[i].length;j++) {
                  x = parseInt(tabler.data[i].length);
                  l = 0;
                if(null!=tabler.protected) {
                  for(x=0;x<tabler.protected.length;x++) {
                    if(x==0) {
                      row = ' scope="row"';
                    }
                    console.log(tabler.header);
                    if (tabler.protected[x] == j) {
                      cont += '<td'+row+' data-label="'+tabler.header[j]+'"><em>'+tabler.data[i][j]+'</em><input type="hidden" class="column" value="'+j+'"/></td>';
                      break;
                    } else {
                      if(tabler.header.length != j) {
                        cont += '<td'+row+'  data-label="'+tabler.header[j]+'"><span>'+tabler.data[i][j]+'</span><input type="hidden" class="column" value="'+j+'"/></td>';
                      } else {
                        cont += '<td'+row+'  data-label="'+tabler.header[j]+'"><a href="#">Create a static host</a><input type="hidden" class="column" value="'+j+'"/></td>';
                      }
                      break;
                    }
                  }
                } else {
                  cont += '<td'+row+'  data-label="'+tabler.header[x]+'"><span>'+tabler.data[i][j]+'</span><input type="hidden" class="column" value="'+j+'"/></td>';

                }
              }
            if(tabler.editor || tabler.option)
              // cont += '<td class="options"><input type="button" class="delete" id="'+tabler.data[i][0]+'" value="" title="Delete"></td></tr>';
              cont += '<td class="options"><div class="delete fa fa-trash" id="'+tabler.data[i][0]+'"></div></td></tr>';
          }
        }
        $('#'+$(tabler.elem).attr('id')).addClass('tabler');
        $('#'+$(tabler.elem).attr('id')+' tbody').html(cont);
      },
      '_get_data': function() {
        $.get( tabler.dataUrl).done(function( data ) {
          if(data != '') {
            tabler.data = $.parseJSON(data);
            tabler._renderInner();
          } else {
            tabler.flash('error','<p>There was an issue getting your data.</p>'+data);
          }
        });
      },
      'restoreField':function(e,lock,nvalue){
        if(lock == 1) {
          tabler.lock = 0;
          e = $(e.currentTarget);
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
      },
      '_register_events':function(){
        if(tabler.editor) {
          c = $('#'+$(tabler.elem).attr('id')+' tbody tr td');
          $(c).on('click','span',function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            var elem = $(this);
            if(tabler.lock != 1 && tabler.id != tabler.value){
              tabler.lock = 1;
              tabler.value = elem.html();
              column = $(e.currentTarget).next().val();
              tabler.id = $(this).parent().parent().find('td:first-child em').html();
              tabler.selected = $(this).html();
              console.log('tabler.selected '+tabler.selected);
              tdelem = elem.parent();
              h = '';
              if(null!==tabler.fieldTypes && undefined!==tabler.fieldTypes[0]) {
                if(tabler.fieldTypes.length > 0) {
                  for(z=0;z<tabler.fieldTypes.length;z++) {
                    if(tabler.fieldTypes[z][0] == column) {
                      if(tabler.fieldTypes[z][1]=='option') {
                        console.log('tabler.id '+tabler.id);
                        h = tabler.selectField(column, tabler.value, tabler.selected);
                        break;
                      } 
                    } else {
                      h = '<input id="field-value" type="text" value="'+ tabler.value +'" />';
                    }
                  }
                }  
              } else {
                h = '<input id="field-value" type="text" value="'+ tabler.value +'" />';
              }
              // h += '<input id="update-data" class="fa fa-check" type="button" value="" title="Save this change"/>';
              h += '<div id="update-data"  class="fa fa-check"></div>';
              // h += '<input id="cancel-data" class="fa fa-times" type="button" value="" title="Cancel the change"/>';
              h += '<div id="cancel-data" class="fa fa-times"></div>';
              h += '<input class="column" type="hidden" value="'+column+'"/>';
              h += '<input class="column2" type="hidden" value="'+tabler.columns[column]+'"/>';
              h += '<input id="columnid" type="hidden" value="'+tabler.id+'" />';
              elem.parent().empty();
              tdelem.html(h);

              $('#field-value').select();
            }
            tabler.id = null;
            return false;
          });
          // mouseover and mouseout for the delete button
          $('td').on('mouseover','.delete',function(e) {
            if(tabler.lock!=1) {
              $(e.currentTarget).removeClass('delete');
              $(e.currentTarget).addClass('delete-hover');
            }
          }).on('mouseout','.delete-hover',function(e) {
            if(tabler.lock!=1) {
              $(e.currentTarget).removeClass('delete-hover');
              $(e.currentTarget).addClass('delete');
            }
          }).on('click','.delete',function(e) {
            if(tabler.lock!=1) {
              x = $(e.currentTarget);
              id = x.attr('id');
            }
          });
          $('.delete').click(function(e) {
            if(tabler.lock!=1) {
              e.preventDefault();
              cnt++;
              at = e.currentTarget;
              var id = $(e.currentTarget).attr('id');
              tabler.remove_overlay();
              tabler.show_overlay();

              $('.overlay').append('<div class="outer"><div class="inner"><span>Are you sure you want to delete this record?</span><a href="#" class="delete yes">Yes</a> | <a href="#" class="delete no">No</a></div>');
              $('.delete.yes').click(function(e) {
                tabler.remove_overlay();
                $.ajax({
                  method:"GET",
                  url: tabler.delUrl + '/' + id,
                  success: function(msg) {
                    if(msg=='true') {
                      tabler._reset();
                      tabler.flash('success','<p>Record deleted successfully.</p>');
                    } else {
                      tabler.flash('error','<p>There was an issue deleting the record.</p>'+msg);
                    }
                  }
                });
                tabler.remove_overlay();
              });
              $('.delete.no').click(function(e) {
                tabler.remove_overlay();
              });
            }
          });
          $('a.add-record').click(function(e) {
            //get form#host display status
            if($('.overlay').html()=='') {
              tabler._add_record();
              tabler._register_events();
            }
            x = $('form.add-record').css('display');
            if(x == 'none') {
              $('form.add-record').show();
              tabler.show_overlay();
              $(this).hide();
            } else {
              $('.add-record').hide();
              tabler.remove_overlay();
            }
          });
          $('form.add-record input.cancel-add').click(function(e) {
            $('form.add-record').hide();
            $('#add-record a.add-record').show();
            tabler.remove_overlay();
          });            
        }
        $('td').on('click','#cancel-data',function(e) {
          column = $(e.currentTarget).parent().find('input.column').val();
          tabler.restoreField(e,1,tabler.value);
        });
        // update feild on enter key
        $('td').on('keypress','#field-value',function(e) {
          if(e.which == 13) {
            tabler.updateData(e);
          }
        });
        // click on the checkmark to update our data array 
        $('td').on('click','#update-data',function(e) {
          tabler.updateData(e);
        });
        // cancel field update on escape key
        $('td').on('keyup','#field-value',function(e) {
          if(e.which == 27) {
            tabler.restoreField(e,1,tabler.value);
          }
        });
        // pager clicks
        $('#pager').on('click','a', function(e) {
          if(tabler.lock != 1) {
            x = $(e.currentTarget).html();
            tabler.pager.startPage = x * tabler.pager.rowsPerPage - tabler.pager.rowsPerPage;
            $('#pager').remove();
            $('#data tbody tr').remove();
            c = $('#'+$(tabler.elem).attr('id')+' tbody tr td');
            $(c).click();
            tabler._get_pager(x);
            tabler._show_data();
            tabler._register_events();
          }
        });
      }
    };
    if(settings.init) {
        $("#loader").show();
      tabler.render();
    } else {
      return tabler;
    }
  }
}( jQuery ));