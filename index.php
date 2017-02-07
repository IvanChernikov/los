<?php
require_once('config.php'); 
session_start();
Flag::Init();
Router::Route();