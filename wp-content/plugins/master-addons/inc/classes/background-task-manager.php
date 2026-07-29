<?php

namespace MasterAddons\Inc\Classes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Background Task Manager
 * Wraps Action Scheduler (preferred) with WP-Cron fallback for scheduling
 * recurring, single, and async tasks. Provides retry logic, logging,
 * and image optimization queue support.
 */
class Background_Task_Manager
{
    private static $instance = null;

    /** Action Scheduler group for all MA actions */
    const AS_GROUP = 'jltma_background';

    /** Option key for task log */
    const LOG_OPTION = 'jltma_background_task_log';

    public function __construct()
    {
        add_action('init', [$this, 'init'], 20);
    }

    public function init()
    {
        // Image optimization background handler
        add_action('jltma_optimize_image_background', [$this, 'handle_image_optimization'], 10, 1);

        // Admin AJAX status endpoint
        add_action('wp_ajax_jltma_get_background_task_status', [$this, 'ajax_get_status']);
    }

    /**
     * Check if Action Scheduler is available
     */
    public function is_action_scheduler_available()
    {
        return function_exists('as_schedule_recurring_action');
    }

    /**
     * Schedule a recurring action
     *
     * @param string $hook     Action hook name
     * @param int    $interval Interval in seconds
     * @param array  $args     Optional arguments
     * @param bool   $unique   Whether to skip if already scheduled
     */
    public function schedule_recurring($hook, $interval, $args = [], $unique = true)
    {
        if ($this->is_action_scheduler_available()) {
            if ($unique && as_has_scheduled_action($hook, $args, self::AS_GROUP)) {
                return;
            }
            as_schedule_recurring_action(time(), $interval, $hook, $args, self::AS_GROUP);
        } else {
            // WP-Cron fallback
            if ($unique && wp_next_scheduled($hook, $args)) {
                return;
            }
            $recurrence = $this->interval_to_recurrence($interval);
            wp_schedule_event(time(), $recurrence, $hook, $args);
        }
    }

    /**
     * Schedule a single (one-time) action
     *
     * @param string $hook  Action hook name
     * @param array  $args  Optional arguments
     * @param int    $delay Delay in seconds from now (default 0)
     */
    public function schedule_single($hook, $args = [], $delay = 0)
    {
        $timestamp = time() + $delay;

        if ($this->is_action_scheduler_available()) {
            as_schedule_single_action($timestamp, $hook, $args, self::AS_GROUP);
        } else {
            wp_schedule_single_event($timestamp, $hook, $args);
        }
    }

    /**
     * Enqueue an async action (runs ASAP in background)
     *
     * @param string $hook Action hook name
     * @param array  $args Optional arguments
     */
    public function enqueue_async($hook, $args = [])
    {
        if ($this->is_action_scheduler_available()) {
            as_enqueue_async_action($hook, $args, self::AS_GROUP);
        } else {
            // WP-Cron fallback: schedule 1 second from now
            wp_schedule_single_event(time() + 1, $hook, $args);
        }
    }

    /**
     * Unschedule all instances of an action
     *
     * @param string $hook Action hook name
     * @param array  $args Optional arguments (must match scheduled args)
     */
    public function unschedule($hook, $args = [])
    {
        if ($this->is_action_scheduler_available()) {
            as_unschedule_all_actions($hook, $args, self::AS_GROUP);
        }

        // Always clear from WP-Cron too (in case of migration)
        $timestamp = wp_next_scheduled($hook, $args);
        while ($timestamp) {
            wp_unschedule_event($timestamp, $hook, $args);
            $timestamp = wp_next_scheduled($hook, $args);
        }
    }

    /**
     * Execute a callback with exponential backoff retry
     *
     * @param callable $callback    The function to execute
     * @param int      $max_retries Maximum retry attempts
     * @param string   $task_name   Human-readable task name for logging
     * @return mixed Result of the callback on success
     * @throws \Exception Re-throws after all retries exhausted
     */
    public function execute_with_retry($callback, $max_retries = 3, $task_name = '')
    {
        $attempt = 0;
        $last_error = null;

        while ($attempt < $max_retries) {
            try {
                $result = call_user_func($callback);
                if ($task_name) {
                    $this->log_success($task_name);
                }
                return $result;
            } catch (\Exception $e) {
                $last_error = $e;
                $attempt++;

                if ($attempt < $max_retries) {
                    // Exponential backoff: 2s, 4s, 8s...
                    $wait = pow(2, $attempt);
                    sleep($wait);
                }
            }
        }

        if ($task_name && $last_error) {
            $this->log_failure($task_name, $last_error->getMessage());
        }

        throw $last_error;
    }

    /**
     * Log a successful task execution
     */
    public function log_success($task)
    {
        $log = get_option(self::LOG_OPTION, []);
        $log[$task] = array_merge($log[$task] ?? [], [
            'last_success' => current_time('mysql'),
            'last_error'   => null,
        ]);
        update_option(self::LOG_OPTION, $log, false);
    }

    /**
     * Log a failed task execution
     */
    public function log_failure($task, $error)
    {
        $log = get_option(self::LOG_OPTION, []);
        $log[$task] = array_merge($log[$task] ?? [], [
            'last_failure' => current_time('mysql'),
            'last_error'   => $error,
        ]);
        update_option(self::LOG_OPTION, $log, false);
    }

    /**
     * Get status for a single task
     */
    public function get_task_status($task)
    {
        $log = get_option(self::LOG_OPTION, []);
        return $log[$task] ?? null;
    }

    /**
     * Get status for all tracked tasks
     */
    public function get_all_statuses()
    {
        return get_option(self::LOG_OPTION, []);
    }

    /**
     * Queue a single image for background optimization
     *
     * @param int $attachment_id WordPress attachment ID
     */
    public function queue_image_optimization($attachment_id)
    {
        $this->enqueue_async('jltma_optimize_image_background', [$attachment_id]);
    }

    /**
     * Queue multiple images for background optimization
     *
     * @param array $attachment_ids Array of attachment IDs
     */
    public function queue_bulk_optimization($attachment_ids)
    {
        foreach ($attachment_ids as $id) {
            $this->enqueue_async('jltma_optimize_image_background', [(int) $id]);
        }
    }

    /**
     * Handle background image optimization (action callback)
     *
     * @param int $attachment_id
     */
    public function handle_image_optimization($attachment_id)
    {
        $attachment_id = absint($attachment_id);
        if (!$attachment_id || !wp_attachment_is_image($attachment_id)) {
            return;
        }

        // Check if already optimized
        $existing = get_post_meta($attachment_id, '_jltma_optimization_data', true);
        if (!empty($existing) && isset($existing['percentage'])) {
            return;
        }

        // Delegate to the Image Optimizer if available
        $optimizer_class = '\\MasterAddons\\Pro\\Admin\\Image_Optimizer\\Image_Optimizer';
        if (class_exists($optimizer_class)) {
            $optimizer = $optimizer_class::get_instance();
            if (method_exists($optimizer, 'process_background_optimization')) {
                $optimizer->process_background_optimization($attachment_id);
            }
        }
    }

    /**
     * Add custom cron intervals
     *
     * @param array $schedules Existing WP-Cron schedules
     * @return array Modified schedules
     */
    public function add_cron_intervals($schedules)
    {
        if (!isset($schedules['jltma_six_hourly'])) {
            $schedules['jltma_six_hourly'] = [
                'interval' => 6 * HOUR_IN_SECONDS,
                'display'  => __('Every 6 Hours (MA)', 'master-addons'),
            ];
        }
        return $schedules;
    }

    /**
     * Map seconds to WP-Cron recurrence name
     *
     * @param int $seconds Interval in seconds
     * @return string WP-Cron recurrence identifier
     */
    public function interval_to_recurrence($seconds)
    {
        if ($seconds <= 6 * HOUR_IN_SECONDS) {
            return 'jltma_six_hourly';
        }
        if ($seconds <= 12 * HOUR_IN_SECONDS) {
            return 'twicedaily';
        }
        return 'daily';
    }

    /**
     * AJAX handler: return background task statuses
     */
    public function ajax_get_status()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied', 'master-addons')]);
        }

        check_ajax_referer('jltma_background_task_status', '_wpnonce', true);

        $statuses = $this->get_all_statuses();
        $result = [];

        foreach ($statuses as $task => $info) {
            $entry = [
                'last_success' => $info['last_success'] ?? null,
                'last_failure' => $info['last_failure'] ?? null,
                'last_error'   => $info['last_error'] ?? null,
                'next_run'     => null,
                'queued_count' => 0,
            ];

            // Try to get next scheduled run
            if ($this->is_action_scheduler_available()) {
                $next = as_next_scheduled_action($task, [], self::AS_GROUP);
                if ($next) {
                    $entry['next_run'] = gmdate('Y-m-d H:i:s', $next);
                }
            } else {
                $next = wp_next_scheduled($task);
                if ($next) {
                    $entry['next_run'] = gmdate('Y-m-d H:i:s', $next);
                }
            }

            $result[$task] = $entry;
        }

        wp_send_json_success($result);
    }

    /**
     * Get singleton instance
     */
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
