<?php

/*
 Plugin Name: Lebendiger Adventskalender
 Plugin URI: https://github.com/sebaur/LebendigerAdventskalender
 Description: This plugin provides an advent calendar for SfC Karlsruhe.
 Author: Jeremias Trautmann
 Version: 1.0
 Author URI: https://github.com/jtrautmann
*/

$lebendiger_adventskalender = new LebendigerAdventskalender();

class LebendigerAdventskalender {

    private $controller;

    public function __construct() {
        // add administrator tool to administrator menu
        add_action('admin_menu', array($this, 'addToMenu'));
        
        // add autoload functions
        spl_autoload_register(array($this, 'autoload'));
        if ($_SERVER["SERVER_ADDR"] == '127.0.0.1') {
            require_once(ABSPATH."../../vendor/autoload.php");
        }

        // add functions collection
        include(plugin_dir_path(__FILE__)."src/functions.php");

        // register plugin activation and deactivation hook 
        register_activation_hook( __FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // add styles and scripts
        add_action('wp_loaded', array($this, 'addStylesAndScripts'));

        // instantiate controller
        $this->controller = Controller::getController();
    }

    public function activate() {
        $this->controller->activate();
    }

    public function deactivate() {
        $this->controller->deactivate();
    }

    public function addToMenu() {
        add_menu_page('Lebendiger Adventskalender', 'Lebendiger Adventskalender', 'edit_posts', 'lebendiger_adventskalender', array($this, 'printAdminPage'));
    }

    public function printAdminPage() {
        include(plugin_dir_path(__FILE__).'admin.php');
    }

    public function addStylesAndScripts() {
        // register plugin styles and enqueue plugin scripts
        wp_register_style('lebendiger_adventskalender_admin',
                            plugin_dir_url(__FILE__).'assets/admin.css');
        wp_enqueue_script('lebendiger_adventskalender_functions',
                            plugin_dir_url(__FILE__).'assets/functions.js');
        switch ($this->controller->getShowState()) {
            case ShowState::CALENDAR:
                wp_register_style('lebendiger_adventskalender_calendar',
                                    plugin_dir_url(__FILE__).'assets/calendar.css');
                break;
            case ShowState::DOOR:
                wp_register_style('lebendiger_adventskalender_door',
                                    plugin_dir_url(__FILE__).'assets/door.css');
                wp_enqueue_script('lebendiger_adventskalender_door',
                                    plugin_dir_url(__FILE__).'assets/door.js');
                wp_enqueue_script('lebendiger_adventskalender_lightbox',
                                    plugin_dir_url(__FILE__).'lightbox/lightbox-plus-jquery.min.js');
                break;
            case ShowState::RESERVATION:
                wp_register_style('lebendiger_adventskalender_reservation',
                                    plugin_dir_url(__FILE__).'assets/reservation.css');
                wp_enqueue_script('jquery');
                break;
        }

    }

    public function autoload($class) {
        $path = plugin_dir_path(__FILE__).'src/'.$class.'.php';
        if (file_exists($path)) {
            require_once($path);
        }
        else {
            return false;
        }
    }
}

?>