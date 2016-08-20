<?php
require 'bootstrap.php';

/**
 * SocketIP.class.php
 *
 * ...
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @version 1.0
 */

class SocketIP extends StudIPPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();

        $navigation = new AutoNavigation(_('Socket.IP'));
        $navigation->setURL(PluginEngine::GetURL($this, array(), 'show'));
        $navigation->setImage($this->getPluginURL() . '/assets/socket.svg');
        Navigation::addItem('/socketip', $navigation);
    }

    public function initialize () {

    }

    public function perform($unconsumed_path)
    {
        StudipAutoloader::addAutoloadPath(__DIR__ . '/models');

        $this->addStylesheet('assets/style.less');
        PageLayout::addScript($this->getPluginURL() . '/bower_components/autobahn/autobahn.js');
        PageLayout::addScript($this->getPluginURL() . '/assets/application.js');
        
        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'show'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

    private function setupAutoload()
    {
        if (class_exists('StudipAutoloader')) {
        } else {
            spl_autoload_register(function ($class) {
                include_once __DIR__ . $class . '.php';
            });
        }
    }
}
