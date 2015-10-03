
function cancelCreateUser(){

    window.location = '/v1/users';
}


function checkPasswordMatch() {
    var password = $("#new_password").val();
    var confirmPassword = $("#retype_new_password").val();

    if (password != confirmPassword) {
        $("#btnSubmitForm").prop("disabled",true);
    }
    else {
        $("#btnSubmitForm").prop("disabled",false);
    }
}


function cancelEditUser(){
    window.location = '/v1/users';
}


function initUserEditSet(status){

    if(status)
        $("#advancedUserOptions").show();
    else
        $("#advancedUserOptions").hide();
}


function removeUser(id, name, type) {

    if(confirm('Remove User "' + name + '" ? ')){
        $('#single_delete_' + id + '_' + type).submit();
        return true;
    }
    else
        return false;

}


$('#selectedUsersRemove').click(function(){

    var deleteArrayIds = [];
    var deleteArrayTypes = [];
    var deleteNames = '';

    $('input[type=checkbox]').each(function () {

        var val = this.value.split(',');

        if(this.checked){
            deleteNames += '"' + this.name + '", ';
            deleteArrayIds.push(val[0]);
            deleteArrayTypes.push(val[1]);
        }
    });

    deleteNames = deleteNames.substring(0, deleteNames.length - 2);

    if(!deleteArrayIds.length){
        alert('No User(s) Selected!');
        return true;
    }

    $('#_selectedIds').val(deleteArrayIds);
    $('#_selectedTypes').val(deleteArrayTypes);

    if(confirm('Remove Selected Users ' + deleteNames + ' ?')){
        $('#multi_delete').submit();
        return true;
    }
    else
        return false;
});


$('#refresh').click(function(){
    table.state.clear();
    localStorage.removeItem('Users_' + window.location.pathname);
    window.location.reload();
});


var tableRowIndex = null;
var tableColIndex = null;

var table = $('#userTable').DataTable({
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
        localStorage.setItem('Users_' + window.location.pathname, JSON.stringify(oData));
    },
    "fnStateLoad": function (oSettings) {
        var data = localStorage.getItem('Users_' + window.location.pathname);
        return JSON.parse(data);
    }
});


$('#userTable tbody').on( 'click', 'tr', function () {

    tableRowIndex = null;

    var $tr = $(this);

    while(tableColIndex === null){
        //wait
    }

    var user_id = $tr.find('input[type="hidden"][id="user_id"]').val();
    var user_admin = $tr.find('input[type="hidden"][id="user_type"]').val();

    tableRowIndex = user_id;

    var user_type = null;

    if(tableColIndex !== null){

        if(user_admin)
            user_type = 'admin';
        else
            user_type = 'user';

         if(tableColIndex > 1)
             window.location = 'users/' + user_id + '/edit?user_type=' + user_type;
    }
} );


$('#userTable tbody').on( 'click', 'td', function () {

    tableColIndex = null;

    var cellId = table.cell( this ).index().column;

    tableColIndex = cellId;
});



function _nextPage(){
    table.page( 'next' ).draw( false );

    if((table.page.info().page + 1) === table.page.info().pages){
        $('#_next').prop('disabled', true);
    }

    if(table.page.info().page > 0){
        $('#_prev').prop('disabled', false);
    }

    $('#currentPage').html('Page ' + (table.page.info().page + 1));

    setTableInfo();
}

function _prevPage(){
    table.page( 'previous' ).draw( false );

    if(table.page.info().page === 0)
        $('#_prev').prop('disabled', true);

    if((table.page.info().page + 1) === table.page.info().pages)
        $('#_next').prop('disabled', true);

    if(table.page.info().pages > 1)
        $('#_next').prop('disabled', false);

    $('#currentPage').html('Page ' + (table.page.info().page + 1));

    setTableInfo();
}

function _gotoPage(page){
    selectPage(page);
}


var table = $('#userTable').DataTable();
var info = table.page.info();

$("div.toolbar").html('');

if($('#tableInfo').html() === '')
    setTableInfo();


$('#_next').on( 'click', function () {
    _nextPage();
} );

$('#_prev').on( 'click', function () {
    _prevPage();
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



function setTableInfo(){
    $('#tableInfo').html('Showing Users ' + (table.page.info().start + 1) + ' to ' + table.page.info().end + ' of ' + table.page.info().recordsDisplay);
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
    $('#userTable').DataTable().search(
        $('#userSearch').val()
    ).draw();

    updatePageDropdown();
    setTableInfo();
}

$( document ).ready(function() {
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $("#new_password").keyup(checkPasswordMatch);
    $("#retype_new_password").keyup(checkPasswordMatch);

    if(info){
        for(var i = 0; i < info.pages; i++){
            $('#tablePages').append('<li><a href="javascript:selectPage(' + i + ');">' + (i + 1) + '</a></li>')
        }

        if(info.pages > 1)
            $('#_next').prop('disabled', false);

        $('#_prev').prop('disabled', true);

        $('#userSearch').on( 'keyup click', function () {
            filterGlobal();
        } );

        updatePageDropdown();
        selectPage(info.page);
        $('#userSearch').val(table.search());
    }
});

