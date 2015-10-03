$( document ).ready(function() {

});

var table = $('#limitTable').DataTable({
    "dom": '<"toolbar">',
    "aoColumnDefs": [
        {
            "bSortable": false,
            "aTargets": [1]
        },
        {
            "targets": [0],
            "visible": false
        }
    ],
    "bStateSave": true,
    "fnStateSave": function (oSettings, oData) {
        localStorage.setItem('Limits_' + window.location.pathname, JSON.stringify(oData));
    },
    "fnStateLoad": function (oSettings) {
        var data = localStorage.getItem('Limits_' + window.location.pathname);
        return JSON.parse(data);
    }
});



$('#limitTable tbody').on( 'click', 'tr', function () {

    tableRowIndex = null;

    var $tr = $(this);

    while(tableColIndex === null){
        //wait
    }

    var limit_id = $tr.find('input[type="hidden"][id="limit_id"]').val();

    tableRowIndex = limit_id;

    if(tableColIndex !== null){

        if(tableColIndex > 1)
            window.location = 'limits/' + limit_id + '/edit';
    }
} );

$('#limitTable tbody').on( 'click', 'td', function () {

    tableColIndex = null;

    var cellId = table.cell( this ).index().column;

    tableColIndex = cellId;
});



var info = table.page.info();

$("div.toolbar").html('');

if($('#tableInfo').html() === '')
    setTableInfo();

$('#_next').on( 'click', function () {

    table.page( 'next' ).draw( false );

    if((table.page.info().page + 1) === table.page.info().pages){
        $('#_next').prop('disabled', true);
    }

    if(table.page.info().page > 0){
        $('#_prev').prop('disabled', false);
    }

    $('#currentPage').html('Page ' + (table.page.info().page + 1));

    setTableInfo();
} );

$('#_prev').on( 'click', function () {

    table.page( 'previous' ).draw( false );

    if(table.page.info().page === 0)
        $('#_prev').prop('disabled', true);

    if((table.page.info().page + 1) === table.page.info().pages)
        $('#_next').prop('disabled', true);

    if(table.page.info().pages > 1)
        $('#_next').prop('disabled', false);

    $('#currentPage').html('Page ' + (table.page.info().page + 1));

    setTableInfo();
});

function selectPage(page) {

    table.page( page ).draw( false );
    $('#currentPage').html('Page ' + (page + 1));

    if(page === 0)
        $('#_prev').prop('disabled', true);

    if((page + 1) < table.page.info().pages)
        $('#_next').prop('disabled', false);

    if(page > 0)
        $('#_prev').prop('disabled', false);

    if((page + 1) === table.page.info().pages)
        $('#_next').prop('disabled', true);

    setTableInfo();
}

$( document ).ready(function() {
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    if(info) {
        for (var i = 0; i < info.pages; i++) {
            $('#tablePages').append('<li><a href="javascript:selectPage(' + i + ');">' + (i + 1) + '</a></li>')
        }

        if (info.pages > 1)
            $('#_next').prop('disabled', false);

        $('#_prev').prop('disabled', true);


        $('#limitsSearch').on( 'keyup click', function () {
            filterGlobal();
        } );

        updatePageDropdown();
        selectPage(info.page);
        $('#limitsSearch').val(table.search());

    }

    var selected = $('#server_type_select').val();

    if(selected){

        $("#server_type_select option").each(function()
        {
            var opt = $(this).val();

            if(opt !== ''){
                if(opt === selected)
                    $('#server_type_' + opt).show();
                else
                    $('#server_type_' + opt).hide();

            }
        });
    }

    $('.tooltip-wrapper').tooltip({position: "bottom"});

});


$('#refresh').click(function(){
    table.state.clear();
    localStorage.removeItem('Servers_' + window.location.pathname);
    window.location.reload();
});


function setTableInfo(){
    $('#tableInfo').html('Showing Limits ' + (table.page.info().start + 1) + ' to ' + table.page.info().end + ' of ' + table.page.info().recordsDisplay);
}


function updatePageDropdown(){

    $('#tablePages').empty();

    for(var i = 0; i < table.page.info().pages; i++){
        $('#currentPage').text('Page 1');
        $('#tablePages').append('<li><a href="javascript:selectPage(' + i + ');">' + (i + 1) + '</a></li>')
    }

    if(table.page.info().page === 0)
        $('#_prev').prop('disabled', true);

    if((table.page.info().page + 1) < table.page.info().pages)
        $('#_next').prop('disabled', false);

    if(table.page.info().page > 0)
        $('#_prev').prop('disabled', false);

    if((table.page.info().page + 1) === table.page.info().pages)
        $('#_next').prop('disabled', true);
}

function filterGlobal () {
    $('#limitTable').DataTable().search(
        $('#limitsSearch').val()
    ).draw();

    updatePageDropdown();
    setTableInfo();
}



function removeLimit(id, name) {
    if(confirm('Remove Limit "' + name + '" ?')){
        $('#single_delete_' + id).submit();
        return true;
    }
    else
        return false;
}


$('#selectedLimitsRemove').click(function(){

    var deleteArray = [];
    var deleteNames = '';

    $('input[type=checkbox]').each(function (event) {
        if(this.checked)
        {
            deleteNames += '"' + this.name + '", ';
            deleteArray.push(this.value);
        }
    });

    deleteNames = deleteNames.substring(0, deleteNames.length - 2);

    if(!deleteArray.length){
        alert('No Limit(s) Selected!');
        return true;
    }

    $('#_selected').val(deleteArray);

    if(confirm('Remove Selected Limits ' + deleteNames + ' ?')){
        $('#multi_delete').submit();
        return true;
    }
    else
        return false;
});






function cancelCreateLimit(){

    window.location = '/v1/limits';
}

function cancelEditLimit(){

    window.location = '/v1/limits';
}

