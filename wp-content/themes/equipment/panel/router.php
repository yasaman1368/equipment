<?php



class Router
{
    public function __construct()
    {
        add_action('init', [$this, 'routes_handler']);
    }

    public function routes_handler()
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        $this->dispatch_uri($request_uri);
    }

    private function dispatch_uri($request_uri)
    {
        // Check if the request URI contains 'panel'
        if (strpos($request_uri, 'panel') === false) {
            return; // Do nothing if 'panel' is not in the URI
        }

        // Parse the URI to get the controller name
        $controller = $this->pars_uri($request_uri);
        $controller_name = $this->format_controller_file($controller);
        $controller_path = $this->get_controller_file($controller_name);

        // Check if the controller file is valid
        if ($this->is_valid_controller($controller_path)) {
            require_once $controller_path;
            $controllerInstance = new $controller_name;
            $controllerInstance->index();
        } else {
            // Instead of loading 404.php, you can handle it here
            // For example, you could log an error or display a message
            $this->show_404($controller);
        }
    }

    private function pars_uri($request_uri): string
    {
        // Use basename to ensure correct parsing of URI
        return basename(parse_url($request_uri, PHP_URL_PATH));
    }

    private function format_controller_file($controller)
    {
        return ucfirst($controller) . 'Controller';
    }

    private function get_controller_file($controller_name)
    {
        return get_template_directory() . '/panel/Controller/' . $controller_name . '.php';
    }

    private function is_valid_controller($controller_file_path)
    {
        return file_exists($controller_file_path) && is_readable($controller_file_path);
    }

    private function show_404($controller)
    {
        // Handle the not found case here
        // You can log it, display a custom message, or redirect
        echo 'The requested controller "' . htmlspecialchars($controller) . '" was not found.';
    }
}

// Initialize the Router
new Router();
