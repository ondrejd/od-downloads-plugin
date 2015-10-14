<?php
/*
Plugin Name: ondrejd's Downloads Plugin
Plugin URI: https://github.com/ondrejd/od-downloads-plugin/
Description: WordPress plug-in that allow to manage files that you want to offer to visitors of your pages for download. Allow to use either sidebar widget or whole dowload page.
Version: 0.5
Author: Ondřej Doněk
Author URI: http://ondrejdonek.blogspot.cz/
*/

/*  ***** BEGIN LICENSE BLOCK *****
    Version: MPL 1.1
    
    The contents of this file are subject to the Mozilla Public License Version 
    1.1 (the "License"); you may not use this file except in compliance with 
    the License. You may obtain a copy of the License at 
    http://www.mozilla.org/MPL/
    
    Software distributed under the License is distributed on an "AS IS" basis,
    WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
    for the specific language governing rights and limitations under the
    License.
    
    The Original Code is WordPress Photogallery Plugin.
    
    The Initial Developer of the Original Code is
    Ondrej Donek.
    Portions created by the Initial Developer are Copyright (C) 2009
    the Initial Developer. All Rights Reserved.
    
    Contributor(s):
    
    ***** END LICENSE BLOCK ***** */

// TODO Add test if autoload exists! Otherwise show message to the user.
require_once dirname(__FILE__) . '/vendor/autoload.php';

/**
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @since 0.5
 */
class odWpDownloadsPlugin extends \odwp\SimplePlugin
{
	protected $id = 'od-downloads-plugin';
	protected $version = '0.5';
	protected $textdomain = 'od-downloads-plugin';
	protected $admin_menu_position = 11;
    protected $enable_default_options_page = true;

	/**
	 * Constructor.
	 * 
	 * @return void
	 */
	public function __construct() {
		$this->init_locales();

		$this->options = array();
		$this->options[] = new \odwp\PluginOption(
			'main_downloads_dir',
			__('Main downloads dir', $this->get_textdomain()),
			\odwp\PluginOption::TYPE_STRING,
			'wp-content/downloads/'
		);
		$this->options[] = new \odwp\PluginOption(
			'downloads_page_id',
			__('ID of page with downloads listed', $this->get_textdomain()),
			\odwp\PluginOption::TYPE_NUMBER,
			0
		);
		$this->options[] = new \odwp\PluginOption(
			'downloads_thumb_size_use_default',
			__('Use default thumbnails size', $this->get_textdomain()),
			\odwp\PluginOption::TYPE_BOOL,
			false,
			__('You can use either default WordPress thumbnail size or your own.', $this->get_textdomain())
		);
		$this->options[] = new \odwp\PluginOption(
			'downloads_thumb_size_width',
			__('Width of thumbnail images', $this->get_textdomain()),
			\odwp\PluginOption::TYPE_NUMBER,
			146
		);
		$this->options[] = new \odwp\PluginOption(
			'downloads_thumb_size_height',
			__('Height of thumbnail images', $this->get_textdomain()),
			\odwp\PluginOption::TYPE_NUMBER,
			89
		);
		$this->options[] = new \odwp\PluginOption(
			'downloads_shortlist_max_count',
			__('Count of short listing items', $this->get_textdomain()),
			\odwp\PluginOption::TYPE_NUMBER,
			2,
			__('Maximal count of downloads items in short listing.', $this->get_textdomain())
		);

		parent::__construct();
	}

    /**
     * Returns title of the plug-in.
     *
     * @since 0.5
     * @param string $suffix (Optional.)
     * @param string $sep (Optional.)
     * @return string
     */
    public function get_title($suffix = '', $sep = ' - ') {
		if (empty($suffix)) {
			return __('Downloads', $this->get_textdomain());
		}

		return sprintf(
			__('Downloads%s%s', $this->get_textdomain()),
			$sep,
			$suffix
		);
	}
} // End of odWpDownloadsPlugin

// ===========================================================================
// Plugin initialization

global $od_downloads_plugin;

$od_downloads_plugin = new odWpDownloadsPlugin();
