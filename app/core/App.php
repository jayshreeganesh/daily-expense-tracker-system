<?php
class App {
    protected $currentController = 'Dashboard';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        // Check if first part of url exists as a controller
        if (isset($url[0]) && file_exists('../app/controllers/' . ucwords($url[0]) . 'Controller.php')) {
            $this->currentController = ucwords($url[0]) . 'Controller';
            unset($url[0]);
        } else {
            // Default to AuthController if not logged in (logic will be handled in controllers)
            $this->currentController = $this->currentController . 'Controller';
        }

        // Require the controller
        require_once '../app/controllers/' . $this->currentController . '.php';
        $this->currentController = new $this->currentController;

        // Check for second part of url (method)
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        // Get parameters
        $this->params = $url ? array_values($url) : [];

        // Call a callback with array of params
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
