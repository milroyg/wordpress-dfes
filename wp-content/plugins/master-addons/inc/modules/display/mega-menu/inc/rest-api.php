<?php

namespace MasterAddons\Modules\Display\MegaMenu;

defined('ABSPATH') || exit;

trait Rest_API
{
    public $prefix = '';
    public $param = '';
    public $request = null;

    /**
     * Explicit allowlist of dispatchable REST handler method names.
     * Only methods listed here may be invoked by jltma_rest_api_action().
     *
     * @var string[]
     */
    public $rest_actions = array();


    public function config($prefix, $param, $rest_actions = array())
    {
        $this->prefix       = $prefix;
        $this->param        = $param;
        $this->rest_actions = $rest_actions;
    }

    public function init()
    {
        add_action('rest_api_init', function () {

            register_rest_route(untrailingslashit('masteraddons/v2' . $this->prefix), '/(?P<action>\w+)/' . ltrim($this->param, '/'), array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'jltma_rest_api_action'],
                'permission_callback' => function() {
                    return current_user_can('edit_posts');
                }
            ));
        });

    }

    public function jltma_rest_api_action($request)
    {
        $this->request = $request;
        $action_class = strtolower($this->request->get_method()) . '_' . sanitize_key($this->request['action']);

        // Dispatch only to handlers on the explicit allowlist. Without this,
        // method_exists() alone would let a request invoke any get_*/post_*
        // method on the class (e.g. get_instance), creating a dynamic-call surface.
        if (in_array($action_class, $this->rest_actions, true) && method_exists($this, $action_class)) {
            return $this->{$action_class}();
        }

        return new \WP_Error('jltma_invalid_action', __('Invalid action', 'master-addons'), array('status' => 400));
    }
}
