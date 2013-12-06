<?php
/*
Plugin Name: Automatic Post Share
Plugin URI: http://racase.com/automatic-post-share
Description: A simple wordpress plugin to share post automatically while saving new post.
Version: 1.0
Author: Racase Lawaju
Author URI: http://www.racase.com
License: GPL2
*/
/*
Copyright 2013  Racase Lawaju  (email : arryaas@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

# Check if class already exits
if(!class_exists("Automatic_Post_Share")){
	/**
	* Class : Automatic_Post_Share
	*/
	class Automatic_Post_Share
	{
		
		public function __construct()
		{
			#Load Facebook Wrapper
			require_once(sprintf("%s/lib/facebook/fb_wrapper.php",dirname(__FILE__)));
			$facebook = new APS_FB_Wrapper();

			require_once(sprintf("%s/APS_Settings.php",dirname(__FILE__)));
			$APS_Settings = new APS_Settings(array("facebook"=>$facebook));

			require_once(sprintf("%s/APS_Custom_Fields.php",dirname(__FILE__)));
			$APS_Custom_Fields = new APS_Custom_Fields(array("facebook"=>$facebook));

		} # END Function : __construct

		/**
		* Activate plugin
		*/
		public static function activate(){
			# DO Nothing
		} # END Function : activate

		/**
		* Deactivate plugin
		*/
		public static function deactivate(){
			// unregister_setting();
			unregister_setting("APS_Setting-group","APS_post_types");
			unregister_setting("APS_Setting-group","APS_FB_app_id");
			unregister_setting("APS_Setting-group","APS_FB_secret_key");
			unregister_setting("APS_Setting-group","APS_FB_profile_ids");
			unregister_setting("APS_Setting-group","APS_FB_profile_data");

			delete_option("APS_post_types");
			delete_option("APS_FB_app_id");
			delete_option("APS_FB_secret_key");
			delete_option("APS_FB_profile_ids");
			delete_option("APS_FB_profile_data");

		} # END of function : deactivate
	} # END Class : Automatic_Post_Share
} # END : if(!class_exits("Automatic_Post_Share"))


if(class_exists("Automatic_Post_Share")){

	# Installation and uninstallation Hooks
	register_activation_hook(__FILE__,array("Automatic_Post_Share","activate"));
	register_deactivation_hook(__FILE__,array("Automatic_Post_Share","deactivate"));

	# instantiate Plugin Class 
	$Automatic_Post_Share = new Automatic_Post_Share();

	if(isset($Automatic_Post_Share)){
		# ADD Setting link in plugin page
		function plugin_setting_link($links){
			$settings_link = '<a href="options-general.php?page=aps-setting">Settings</a>'; 
			array_unshift($links, $settings_link); 
			return $links; 
		} #END Function : plugin_setting_link

		$plugin = plugin_basename(__FILE__); 
        add_filter("plugin_action_links_$plugin", 'plugin_setting_link');

	} #END : if(isset($Automatic_Post_Share))

} #END : if(class_exists("Automatic_Post_Share"))