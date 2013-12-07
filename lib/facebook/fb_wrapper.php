<?php
# Check if class already exits
if(!class_exists("APS_FB_Wrapper")){
	/**
	* Class : APS_FB_Wrapper
	*/
	class APS_FB_Wrapper
	{
		public function __construct()
		{
			require_once(sprintf("%s/facebook.php",dirname(__FILE__)));
			$this->FB_lib = new Facebook(array(
							  'appId'  => get_option("APS_FB_app_id"),
							  'secret' => get_option("APS_FB_secret_key")
						));
		} # END Function : __construct

		public function login_url(){
			return $this->FB_lib->getLoginUrl(array('scope' => 'read_stream,publish_stream,manage_pages',
                'redirect_uri' => get_bloginfo("url").'/wp-admin/options-general.php?page=aps-setting'));
		} # END Function : login_url

        public function get_user($id=null){
        	if($id!=null){
        		try {
                	# Proceed knowing you have a logged in user who's authenticated.
                    return $this->FB_lib->api("/".$id."/");
                } catch (FacebookApiException $e) {
                    var_dump($e);exit;
                    $user = null;
                }
        	}

            $user = $this->FB_lib->getUser();
            if ($user) {
                try {
                    	# Proceed knowing you have a logged in user who's authenticated.
                        return $this->FB_lib->api('/me');
                    } catch (FacebookApiException $e) {
                        error_log($e);exit;
                        $user = null;
                    }
            }
        } # END Function : get_user

        public function get_pages($args=array()){
        	$user_id = get_option("APS_FB_profile_data");
			try {
				$user_data =  $this->FB_lib->api('/'.$user_id['id']);
			} catch (FacebookApiException $e) {
				unregister_setting("APS_Setting-group","APS_FB_profile_ids");
				delete_option("APS_FB_profile_ids");
				unregister_setting("APS_Setting-group","APS_FB_profile_data");
				delete_option("APS_FB_profile_data");
				return false;
			}
            $pages =  $this->FB_lib->api('/'.$user_id['id'].'/accounts');
            if($args['include_user']){
                $count = count($pages['data']);
                $pages['data'][$count]['name'] = $user_id['name'];
                $pages['data'][$count]['id'] = $user_id['id'];
                $pages['data'][$count]['category'] = "profile";
            }
            return $pages['data'];
        } # END Function : get_pages

        public function post_in_wall($args=array()){
        	if($args['profile_type']=="page"){
        		try {
	            $page_info = $this->FB_lib->api("/".$args['profile_id']."?fields=access_token");
	            if( !empty($page_info['access_token']) ) {
	                $fb_args = array(
	                'access_token'  => $page_info['access_token'],
	                );
	            }
	            } catch (FacebookApiException $e) {
	                error_log($e);
	                $user = null;
	            }
        	}
            try {
                $fb_args['message']=$args['message'];
                $post_id = $this->FB_lib->api("/".$args['profile_id']."/feed","post",$fb_args);

            } catch (FacebookApiException $e) {
                error_log($e);
                $user = null;
            }
        } # END Function : post_in_wall
        
        function check_status(){
            $user_id = get_option("APS_FB_profile_data");
            try {
                $user_data =  $this->FB_lib->api('/'.$user_id['id']);
                return true;
            } catch (FacebookApiException $e) {
                unregister_setting("APS_Setting-group","APS_FB_profile_ids");
                delete_option("APS_FB_profile_ids");
                unregister_setting("APS_Setting-group","APS_FB_profile_data");
                delete_option("APS_FB_profile_data");
                return false;
            }
        } # END Function : check_status
	} # END Class : APS_FB_Wrapper
} # END : if(!class_exists("APS_FB_Wrapper"))

?>