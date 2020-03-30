$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(".create-class-text,.create-class").click(function(e){
    $(".create-code").show();
});

$(".cancel").click(function(){
    $("#createCodeForm").trigger("reset");
    $(".error-message").html('');
    $(".create-code").hide();
})

$(".createCode").click(function(e){
    $(".black_overlay").show();
    e.preventDefault();
    var data = $('#createCodeForm').serialize();
    $.ajax({
        type: "POST",
        url: $('#createCodeForm').attr('action'),
        data: data,
        success: function(returnData){
            $(".black_overlay").hide();
            $(".error-message").html("");
            if(returnData.status == 'true'){
                $(".success_message").html('<b>Code has been created successfully</b> '+returnData.newCode);
                $("#createCodeForm").trigger("reset");
                setTimeout(function () {
                    $(".success_message").fadeOut(500);
                }, 5000);
            }
            else{
                Object.keys(returnData.errors).forEach($key=>{
                $("."+$key).html(returnData.errors[$key]);
              });
            }
        },
        error:function(){
            $(".black_overlay").hide(); 
        },
        dataType:'JSON',
    });
});

$("#fromDate").datepicker({
    format: 'yyyy-mm-dd',
    changeMonth: true,
    changeYear: true,
    todayHighlight: true,
    autoclose: true,
});
$("#toDate").datepicker({
    format: 'yyyy-mm-dd',
    changeMonth: true,
    changeYear: true,
    todayHighlight: true,
    autoclose: true,
});

$(".searchCode").click(function(e){
    $(".black_overlay").show();
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    $.ajax({
        type: "GET",
        url: 'search-code',
        data: {'fromDate':fromDate,'toDate' : toDate},
        success: function(returnData){
            $(".black_overlay").hide();
            var table = $('.dataTable').DataTable();
            table.clear().draw();
            $.each(returnData.data, function(i, item) {

                let jsonData=[
                    item.state_name.state_name,
                    item.a_type,
                    item.d_type,
                    item.user_code,
                    item.ref_no,
                    item.created_at,
                    '<span style="cursor: pointer;color: blue;text-decoration: underline;" id="edit-item" data-item-id="'+item.id+'">Edit</span>/<a href="http://localhost/payutest/public/delete-code/'+item.id+'">Delete</a>'
                ];
                var rowNode = table.row.add(jsonData).draw().node();
                $( rowNode ).addClass('data-row');
                $( rowNode ).find('td').eq(4).addClass('ref_no');
            });
        },
        error:function(){
            $(".black_overlay").hide();
        },
        dataType:'JSON',
    });
});

$(".export").click(function(){
    $(".black_overlay").show();
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();

    $.ajax({
        type: "GET",
        url: 'exportCSV',
        data: {'fromDate':fromDate,'toDate' : toDate},
        xhrFields: {
            responseType: 'blob'
        },
        success: function(response, status, xhr){
            var filename = "";                   
            var disposition = xhr.getResponseHeader('Content-Disposition');

            if (disposition) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches !== null && matches[1]) filename = matches[1].replace(/['"]/g, '');
            } 
            var linkelem = document.createElement('a');

            var blob = new Blob([response], { type: 'application/octet-stream' });
            
            var URL = window.URL || window.webkitURL;
            var downloadUrl = URL.createObjectURL(blob);

            if (filename) { 
                var a = document.createElement("a");
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.target = "_blank";
                a.click();            
            } 
            else {
                window.location = downloadUrl;
            }   
            $(".black_overlay").hide();         
        },
        error:function(){
            $(".black_overlay").hide(); 
        }
    });
});



$(document).on('click', "#edit-item", function() {
    $(this).addClass('edit-item-trigger-clicked');

    var options = {
      'backdrop': 'static'
    };
    $('#edit-modal').modal(options)
});

$('#edit-modal').on('show.bs.modal', function() {
    var el = $(".edit-item-trigger-clicked");
    var row = el.closest(".data-row");
    var id = el.data('item-id');
    var ref_no = row.children(".ref_no").text();
    $("#modal-input-ref_no").val(ref_no);
    $("#codeId").val(id);
});

  // on modal hide
$('#edit-modal').on('hide.bs.modal', function() {
    $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
    $("#edit-form").trigger("reset");
})


$(document).ready(function(){
    $(".alert").delay(3000).slideUp(300);
});