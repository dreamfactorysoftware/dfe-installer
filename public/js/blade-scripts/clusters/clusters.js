

var table = $('#clusterTable').DataTable({
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
        localStorage.setItem('Clusters_' + window.location.pathname, JSON.stringify(oData));
    },
    "fnStateLoad": function (oSettings) {
        var data = localStorage.getItem('Clusters_' + window.location.pathname);
        return JSON.parse(data);
    }
});



$('#clusterTable tbody').on( 'click', 'tr', function () {

    tableRowIndex = null;

    var $tr = $(this);

    while(tableColIndex === null){
        //wait
    }

    var cluster_id = $tr.find('input[type="hidden"][id="cluster_id"]').val();

    tableRowIndex = cluster_id;


    if(tableColIndex !== null){


        if(tableColIndex > 1)
            window.location = 'clusters/' + cluster_id + '/edit';
    }
} );

$('#clusterTable tbody').on( 'click', 'td', function () {

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


$('#refresh').click(function(){
    table.state.clear();
    localStorage.removeItem('Clusters_' + window.location.pathname);
    window.location.reload();
});


function setTableInfo(){
    $('#tableInfo').html('Showing Clusters ' + (table.page.info().start + 1) + ' to ' + table.page.info().end + ' of ' + table.page.info().recordsDisplay);
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
    $('#clusterTable').DataTable().search(
        $('#clusterSearch').val()
    ).draw();

    updatePageDropdown();
    setTableInfo();
}











function removeCluster(id, name) {
    if(confirm('Remove Cluster "' + name + '" ?')){
        $('#single_delete_' + id).submit();
        return true;
    }
    else
        return false;
}

$('#selectedClustersRemove').click(function(){

    var deleteArray = [];
    var deleteNames = '';

    $('input[type=checkbox]').each(function () {

        if(this.checked)
        {
            deleteNames += '"' + this.name + '", ';
            deleteArray.push(this.value);
        }
    });

    deleteNames = deleteNames.substring(0, deleteNames.length - 2);

    if(!deleteArray.length){
        alert('No Cluster(s) Selected!');
        return true;
    }

    $('#_selected').val(deleteArray);

    if(confirm('Remove Selected Clusters ' + deleteNames + ' ?')){
        $('#multi_delete').submit();
        return true;
    }
    else
        return false;
});


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


        $('#clusterSearch').on('keyup click', function () {
            filterGlobal();
        });

        updatePageDropdown();
        selectPage(info.page);
        $('#clusterSearch').val(table.search());
    }

    $('.tooltip-wrapper').tooltip({position: "bottom"});
});

