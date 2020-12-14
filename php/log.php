<?php
function logger($message) {
	syslog(LOG_INFO, $message);
}
