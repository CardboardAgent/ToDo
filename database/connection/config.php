<?php
/*
 * Database configuration:
 * These Configurations might get repositioned into another file, if i need any other configuration-files
 */

$dbsettings = array();
$dbsettings['host']   = '127.0.0.1';
$dbsettings['user']   = 'root';
$dbsettings['passwd'] = '$IhNBub17Ja!';
$dbsettings['dbname'] = 'todo';
$dbsettings['dbtype'] = 'mysql';
$dbsettings['dsn'] = $dbsettings['dbtype'] . ':dbname=' . $dbsettings['dbname'] . ';host=' . $dbsettings['host'];
