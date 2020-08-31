<?php
session_start();

require('../lib/app.lib.php');

userIsConnected();


$view = 'index';
$pageTitle = 'Dashboard';

require('tpl/layout.phtml');

