<?php
require_once('startsession.php');
require_once('log.php');

echo(json_encode($_SESSION['token']));
logger("error: log msg: ".json_encode($_SESSION['token']));