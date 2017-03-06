<?php

/*
 * Router class | Templates Included
 * Class for nginx servers ( php-fpm without htaccess file).
 */
 
class Router {
    private $_route = array(); 

    public function setRoute($dir, $file) {
        $this->_route[trim($dir, '/')] = $file;
    }

    public function route() {
        if (!isset($_SERVER['PATH_INFO'])) { 
            Core::InitController('builders');
            include_once 'templates/index.phtml'; 
        } elseif (isset($this->_route[trim($_SERVER['PATH_INFO'], '/')])) { 
            include_once $this->_route[trim($_SERVER['PATH_INFO'], '/')]; 
        }
        else return false; 
        return true;
    }
}


?>
