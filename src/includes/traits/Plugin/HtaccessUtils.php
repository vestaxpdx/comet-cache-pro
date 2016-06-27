<?php
namespace WebSharks\CometCache\Pro\Traits\Plugin;

use WebSharks\CometCache\Pro\Classes;

trait HtaccessUtils
{
    /**
     * Unique comment marker.
     *
     * @since 151220 Enhancing `.htaccess` tweaks.
     *
     * @return string Used in `.htaccess` parsing.
     */
    public $htaccess_marker = 'WmVuQ2FjaGU';

    /**
     * Plugin options that have associated htaccess rules.
     *
     * @since 160103 Improving `.htaccess` tweaks.
     *
     * @return array Plugin options that have associated htaccess rules
     *
     * @note We keep track of this to avoid the issue described here: http://git.io/vEFIH
     */
    public $options_with_htaccess_rules = ['cdn_enable', 'htaccess_browser_caching_enable', 'htaccess_gzip_enable', 'htaccess_enforce_canonical_urls',];

    /**
     * Add template blocks to `/.htaccess` file.
     *
     * @since 151114 Adding `.htaccess` tweaks.
     *
     * @return bool True if added successfully.
     *
     * @TODO Improve error reporting detail to better catch unexpected failures; see http://git.io/vEFLT
     */
    public function addWpHtaccess()
    {
        global $is_apache;

        if (!$is_apache) {
            return false; // Not running the Apache web server.
        }
        if (!$this->options['enable']) {
            return true; // Nothing to do.
        }
        if (!$this->needHtaccessRules()) {
            if ($this->findHtaccessMarker()) { // Do we need to clean up previously added rules?
                $this->removeWpHtaccess(); // Fail silently since we don't need rules in place.
            }
            return true; // Nothing to do; no options enabled that require htaccess rules.
        }
        if (!$this->removeWpHtaccess()) {
            return false; // Unable to remove.
        }
        if (!($htaccess = $this->readHtaccessFile())) {
            return false; // Failure; could not read file or invalid UTF8 encountered, file may be corrupt.
        }

        $template_blocks = ''; // Initialize.

        foreach (array('gzip-enable.txt', 'access-control-allow-origin-enable.txt', 'browser-caching-enable.txt', 'canonical-urls-ts-enable.txt', 'canonical-urls-no-ts-enable.txt') as $_template) {
            if (!is_file($_template_file = dirname(dirname(dirname(__FILE__))).'/templates/htaccess/'.$_template)) {
                continue; // Template file missing; bypass.
            } // ↑ Some files might be missing in the lite version.
            elseif (!($_template_file_contents = trim(file_get_contents($_template_file)))) {
                continue; // Template file empty; bypass.
            } // ↑ Some files might be empty in the lite version.

            switch ($_template) {
                case 'gzip-enable.txt':
                    if ($this->options['htaccess_gzip_enable']) {
                        $template_blocks .= $_template_file_contents."\n\n";
                    } // ↑ Only if GZIP is enabled at this time.
                    break;
                /*[pro strip-from="lite"]*/
                case 'access-control-allow-origin-enable.txt':
                    if ($this->options['htaccess_access_control_allow_origin']) {
                        $template_blocks .= $_template_file_contents."\n\n";
                    } // ↑ Only if Access-Control-Allow-Origin is enabled at this time.
                    break;

                case 'browser-caching-enable.txt':
                    if ($this->options['htaccess_browser_caching_enable']) {
                        $template_blocks .= $_template_file_contents."\n\n";
                    } // ↑ Only if browser caching is enabled at this time.
                    break;

                case 'canonical-urls-ts-enable.txt':
                    if ($this->options['htaccess_enforce_canonical_urls'] && $GLOBALS['wp_rewrite']->use_trailing_slashes) {
                        $template_blocks .= $_template_file_contents."\n\n";
                    } // ↑ Only if enforce canonical URLs enabled at this time.
                    break;

                case 'canonical-urls-no-ts-enable.txt':
                    if ($this->options['htaccess_enforce_canonical_urls'] && !$GLOBALS['wp_rewrite']->use_trailing_slashes) {
                        $template_blocks .= $_template_file_contents."\n\n";
                    } // ↑ Only if enforce canonical URLs enabled at this time.
                    break;
                /*[/pro]*/
            }
        }
        unset($_template_file); // Housekeeping

        if (empty($template_blocks)) { // Do we need to add anything to htaccess?
            $this->closeHtaccessFile($htaccess); // No need to write to htaccess file in this case.
            return true; // Nothing to do, but no failures either.
        }

        $template_header           = '# BEGIN '.NAME.' '.$this->htaccess_marker.' (the '.$this->htaccess_marker.' marker is required for '.NAME.'; do not remove)';
        $template_footer           = '# END '.NAME.' '.$this->htaccess_marker;
        $htaccess['file_contents'] = $template_header."\n\n".trim($template_blocks)."\n\n".$template_footer."\n\n".$htaccess['file_contents'];

        if (!$this->writeHtaccessFile($htaccess, true)) {
            return false; // Failure; could not write changes.
        }

        return true; // Added successfully.
    }

    /**
     * Remove template blocks from `/.htaccess` file.
     *
     * @since 151114 Adding `.htaccess` tweaks.
     *
     * @return bool True if removed successfully.
     *
     * @TODO Improve error reporting detail to better catch unexpected failures; see http://git.io/vEFLT
     */
    public function removeWpHtaccess()
    {
        global $is_apache;

        if (!$is_apache) {
            return false; // Not running the Apache web server.
        }
        if (!($htaccess_file = $this->findHtaccessFile())) {
            return true; // File does not exist.
        }
        if (!$this->findHtaccessMarker()) {
            return true; // Template blocks are already gone.
        }
        if (!($htaccess = $this->readHtaccessFile())) {
            return false; // Failure; could not read file, create file, or invalid UTF8 encountered, file may be corrupt.
        }

        $regex                     = '/#\s*BEGIN\s+'.preg_quote(NAME, '/').'\s+'.$this->htaccess_marker.'.*?#\s*END\s+'.preg_quote(NAME, '/').'\s+'.$this->htaccess_marker.'\s*/uis';
        $htaccess['file_contents'] = preg_replace($regex, '', $htaccess['file_contents']);

        if (!$this->writeHtaccessFile($htaccess, false)) {
            return false; // Failure; could not write changes.
        }

        return true; // Removed successfully.
    }

    /**
     * Finds absolute server path to `/.htaccess` file.
     *
     * @since 151114 Adding `.htaccess` tweaks.
     *
     * @return string Absolute server path to `/.htaccess` file;
     *                else an empty string if unable to locate the file.
     */
    public function findHtaccessFile()
    {
        $file      = ''; // Initialize.
        $home_path = $this->wpHomePath();

        if (is_file($htaccess_file = $home_path.'.htaccess')) {
            $file = $htaccess_file;
        }
        return $file;
    }

    /**
     * Determines if there are any plugin options enabled that require htaccess rules to be added.
     *
     * @since 160103 Improving `.htaccess` tweaks.
     *
     * @return bool True when an option is enabled that requires htaccess rules, false otherwise.
     */
    public function needHtaccessRules()
    {
        if (!is_array($this->options_with_htaccess_rules)) {
            return false; // Nothing to do.
        }
        foreach ($this->options_with_htaccess_rules as $option) {
            if ($this->options[$option]) {
                return true; // Yes, there are options enabled that require htaccess rules.
            }
        }
        return false; // No, there are no options enabled that require htaccess rules.
    }

    /**
     * Utility method used to check if htaccess file contains $htaccess_marker.
     *
     * @since 151114 Adding `.htaccess` tweaks.
     *
     * @param string $htaccess_marker Unique comment marker used to identify rules added by this plugin.
     *
     * @return bool False on failure or when marker does not exist in htaccess, true otherwise.
     */
    public function findHtaccessMarker($htaccess_marker = '')
    {
        if (!($htaccess_file = $this->findHtaccessFile())) {
            return false; // File does not exist.
        }
        if (!is_readable($htaccess_file)) {
            return false; // Not possible.
        }
        if (($htaccess_file_contents = file_get_contents($htaccess_file)) === false) {
            return false; // Failure; could not read file.
        }
        if (empty($htaccess_marker)) {
            $htaccess_marker = $this->htaccess_marker;
        }
        if (mb_stripos($htaccess_file_contents, $htaccess_marker) === false) {
            return false; // Htaccess marker is missing
        }

        return true; // Htaccess has the marker
    }

    /**
     * Gets contents of `/.htaccess` file with exclusive lock to read+write. If file doesn't exist, we attempt to create it.
     *
     * @since 151220 Improving `.htaccess` utils.
     *
     * @param string $htaccess_file Absolute path to the htaccess file. Optional.
     *                              If not provided, we attempt to find it or create it if it doesn't exist.
     *
     * @return array|bool Returns an array with data necessary to call $this->writeHtaccessFile():
     *                    `fp` a file pointer resource, `file_contents` a string. Returns `false` on failure.
     *
     * @note If a call to this method is not followed by a call to $this->writeHtaccessFile(),
     *       you must make sure that you unlock and close the `fp` resource yourself.
     */
    public function readHtaccessFile($htaccess_file = '')
    {
        if (empty($htaccess_file) && !($htaccess_file = $this->findHtaccessFile())) {
            if (!is_writable($this->wpHomePath()) || file_put_contents($htaccess_file = $this->wpHomePath().'.htaccess', '') === false) {
                return false; // Unable to find and/or create `.htaccess`.
            } // If it doesn't exist, we create the `.htaccess` file here.
        }
        if (!is_readable($htaccess_file) || !is_writable($htaccess_file) || (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)) {
            return false; // Not possible.
        }
        if (!($fp = fopen($htaccess_file, 'rb+')) || !flock($fp, LOCK_EX)) {
            fclose($fp); // Just in case we opened it before failing to obtain a lock.
            return false; // Failure; could not open file and obtain an exclusive lock.
        }
        if (($file_contents = fread($fp, filesize($htaccess_file))) && ($file_contents === wp_check_invalid_utf8($file_contents))) {
            rewind($fp); // Rewind pointer to beginning of file.
            return compact('fp', 'file_contents');
        } else { // Failure; could not read file or invalid UTF8 encountered, file may be corrupt.
            flock($fp, LOCK_UN);
            fclose($fp);
            return false;
        }
    }

    /**
     * Writes to `/.htaccess` file using provided file pointer.
     *
     * @since 151220 Improving `.htaccess` utils.
     *
     * @param array  $htaccess        Array containing `fp` file resource pointing to htaccess file and `file_contents` to write to file.
     * @param bool   $require_marker  Whether or not to require the marker be present in contents before writing.
     * @param string $htaccess_marker Unique comment marker used to identify rules added by this plugin.
     *
     * @return bool True on success, false on failure.
     */
    public function writeHtaccessFile(array $htaccess, $require_marker = true, $htaccess_marker = '')
    {
        if (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS) {
            return false; // Not possible.
        }
        if (!is_resource($htaccess['fp'])) {
            return false;
        }
        $htaccess_marker = $htaccess_marker ?: $this->htaccess_marker;

        $_have_marker = mb_stripos($htaccess['file_contents'], $htaccess_marker);

        // Note: rewind() necessary here because we fread() above.
        if (($require_marker && $_have_marker === false) || !rewind($htaccess['fp']) || !ftruncate($htaccess['fp'], 0) || !fwrite($htaccess['fp'], $htaccess['file_contents'])) {
            flock($htaccess['fp'], LOCK_UN);
            fclose($htaccess['fp']);
            return false; // Failure; could not write changes.
        }
        fflush($htaccess['fp']);
        flock($htaccess['fp'], LOCK_UN);
        fclose($htaccess['fp']);

        return true;
    }

    /**
     * Utility method used to unlock and close htaccess file resource.
     *
     * @since 151114 Adding `.htaccess` tweaks.
     *
     * @param array $htaccess Array containing at least an `fp` file resource pointing to htaccess file.
     *
     * @return bool False on failure, true otherwise.
     */
    public function closeHtaccessFile(array $htaccess)
    {
        if (!is_resource($htaccess['fp'])) {
            return false; // Failure; requires a valid file resource.
        }
        flock($htaccess['fp'], LOCK_UN);
        fclose($htaccess['fp']);

        return true;
    }
}
