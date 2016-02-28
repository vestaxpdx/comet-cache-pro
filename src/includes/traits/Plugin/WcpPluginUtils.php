<?php
namespace WebSharks\CometCache\Pro\Traits\Plugin;

use WebSharks\CometCache\Pro\Classes;

trait WcpPluginUtils {
    /**
    * Automatically wipes/clears on plugin activation/deactivation.
    *
    * @since 151220 Adding auto-wipe|clear on plugin activations/deactivations.
    *
    * @attaches-to `activated_plugin` hook.
    * @attaches-to `deactivated_plugin` hook.
    *
    * @param string $plugin Plugin basename.
    * @param bool True if activating|deactivating network-wide. Defaults to boolean `FALSE` in case parameter is not passed to hook.
    *
    * @return int Total files wiped|cleared by this routine (if any).
    */
    public function autoClearOnPluginActivationDeactivation($plugin, $network_wide = false)
    {
        if (!$this->applyWpFilters(GLOBAL_NS.'_auto_clear_on_plugin_activation_deactivation', true)) {
            return 0; // Nothing to do here.
        }
        return $this->{($network_wide ? 'autoWipeCache' : 'autoClearCache')}();
    }
}
