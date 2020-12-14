var csrf_token;

$(document).ready(function () {

    $.getJSON("php/get_token.php", function (token) {
        csrf_token = token;
    });

    table = $('#logs').DataTable({
        "serverSide": true,
        "processing": true,
        "ajax": {
            "url": "admin_panel/student_action_log/log_json.php",
            "data": {
                "token": csrf_token
            }
        },
        "rowId": "id",
        "lengthMenu": [15, 30, 50, 70],
        "columns": [
            {"data": "id"},
            {"data": "action_time"},
            {"data": "requester"},
            {"data": "ip_addr"},
            {"data": "action"},
            {"data": "details"}
        ]
    });

    setInterval(function () {
       table.ajax.reload();
    }, 300000);

});