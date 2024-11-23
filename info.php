<?php 

if(isset($_GET['checkInfo']) && !empty($_GET['checkInfo']) && $_GET['checkInfo'] == 'server'){
    phpinfo();
}