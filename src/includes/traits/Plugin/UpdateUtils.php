<?php
namespace WebSharks\CometCache\Pro\Traits\Plugin;

use WebSharks\CometCache\Pro\Classes;

trait UpdateUtils
{
    /**
     * Checks for a new lite release.
     *
     * @since 151220 Show version number in plugin options.
     *
     * @attaches-to `admin_init` hook.
     */
    public function maybeCheckLatestLiteVersion()
    {
        if (IS_PRO) {
            return; // Not applicable.
        }
        if (!$this->options['lite_update_check']) {
            return; // Nothing to do.
        }
        if (!current_user_can($this->update_cap)) {
            return; // Nothing to do.
        }
        if (is_multisite() && !current_user_can($this->network_cap)) {
            return; // Nothing to do.
        }
        if ($this->options['last_lite_update_check'] >= strtotime('-1 hour')) {
            return; // No reason to keep checking on this.
        }
        $this->updateOptions(['last_lite_update_check' => time()]);

        $product_api_url        = 'https://'.urlencode(DOMAIN).'/';
        $product_api_input_vars = ['product_api' => ['action' => 'latest_lite_version']];

        $product_api_response = wp_remote_post($product_api_url, ['body' => $product_api_input_vars]);
        $product_api_response = json_decode(wp_remote_retrieve_body($product_api_response));

        if (is_object($product_api_response) && !empty($product_api_response->lite_version)) {
            $this->updateOptions(['latest_lite_version' => $product_api_response->lite_version]);
        }
        // Disabling the notice for now. We only run this check to collect the latest version number.
        #if ($this->options['latest_lite_version'] && version_compare(VERSION, $this->options['latest_lite_version'], '<')) {
        #    $this->dismissMainNotice('new-lite-version-available'); // Dismiss any existing notices like this.
        #    $lite_updater_page = network_admin_url('/plugins.php'); // In a network this points to the master plugins list.
        #    $this->enqueueMainNotice(sprintf(__('<strong>%1$s:</strong> a new version is now available. Please <a href="%2$s">upgrade to v%3$s</a>.', SLUG_TD), esc_html(NAME), esc_attr($lite_updater_page), esc_html($this->options['latest_lite_version'])), array('persistent_key' => 'new-lite-version-available'));
        #}
    }

    /*[pro strip-from="lite"]*/
    /**
     * Checks for a new pro release.
     *
     * @since 150422 Rewrite.
     *
     * @attaches-to `admin_init` hook.
     *
     * @see pre_site_transient_update_plugins()
     */
    public function maybeCheckLatestProVersion()
    {
        if (!$this->options['pro_update_check']) {
            return; // Nothing to do.
        }
        if (!current_user_can($this->update_cap)) {
            return; // Nothing to do.
        }
        if (is_multisite() && !current_user_can($this->network_cap)) {
            return; // Nothing to do.
        }
        if ($this->options['last_pro_update_check'] >= strtotime('-1 hour')) {
            return; // No reason to keep checking on this.
        }
        $this->updateOptions(['last_pro_update_check' => time()]);

        $product_api_url        = 'https://'.urlencode(DOMAIN).'/';
        $product_api_input_vars = ['product_api' => ['action' => 'latest_pro_version']];

        $product_api_response = wp_remote_post($product_api_url, ['body' => $product_api_input_vars]);
        $product_api_response = json_decode(wp_remote_retrieve_body($product_api_response));

        if (is_object($product_api_response) && !empty($product_api_response->pro_version)) {
            $this->updateOptions(['latest_pro_version' => $product_api_response->pro_version]);
        } else { // Let's try the proxy server
            $product_api_url      = 'http://proxy.websharks-inc.net/'.urlencode(SLUG_TD).'/';
            $product_api_response = wp_remote_post($product_api_url, ['body' => $product_api_input_vars, 'timeout' => 15]);
            $product_api_response = json_decode(wp_remote_retrieve_body($product_api_response));

            if (is_object($product_api_response) && !empty($product_api_response->pro_version)) {
                $this->updateOptions(['latest_pro_version' => $product_api_response->pro_version]);
            }
        }
        if ($this->options['latest_pro_version'] && version_compare(VERSION, $this->options['latest_pro_version'], '<')) {
            $this->dismissMainNotice('new-pro-version-available'); // Dismiss any existing notices like this.
            $pro_updater_page = add_query_arg(urlencode_deep(['page' => GLOBAL_NS.'-pro-updater']), network_admin_url('/admin.php'));
            $this->enqueueMainNotice(sprintf(__('<strong>%1$s Pro:</strong> a new version is now available. Please <a href="%2$s">upgrade to v%3$s</a>.', SLUG_TD), esc_html(NAME), esc_attr($pro_updater_page), esc_html($this->options['latest_pro_version'])), ['persistent_key' => 'new-pro-version-available']);
        }
    }
    /*[/pro]*/
}
