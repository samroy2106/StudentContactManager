<?php
require_once('../../php/startsession.php');
require_once('../../../config.php');
require_once('../../php/constants_config.php');
require_once('../../php/log.php');

$start = microtime(true);

$html_header = "<!DOCTYPE html>\n<html>\n<head>\n<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>\n<title>Log Table</title>
<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css'>
<link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>
<link rel='stylesheet' type='text/css' href='css/log_table.css'>
<script src='https://kit.fontawesome.com/8b75e01689.js' crossorigin='anonymous'></script>
<script src='https://code.jquery.com/jquery-3.5.1.js'></script>
<script src='https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js'></script>
<script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'></script>
<script src='js/log_table.js'></script>
</head>\n<body>\n";

$table_header = "<div id='log_table'>";

$table = "<table id='logs' class='display' style='width:100%'>\n<thead>\n<tr>\n<th>ID</th>\n<th>Action Time</th>
<th>Requester</th>\n<th>IP Address</th>\n<th>Action</th><th>Details</th></tr>\n</thead>\n";

$table_footer = "</table>\n";

$html_footer = "</body>\n</html>";

$html = $html_header.$table_header.$table.$table_footer.$html_footer;
echo $html;

$end = microtime(true);
$render_time = ($end-$start);
//printf("Page rendered in %f seconds.", $render_time);