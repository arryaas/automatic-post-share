<?php
# Check if class already exits
if(!class_exists('APS_Custom_Fields'))
{
	class APS_Custom_Fields
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct($args=array())
		{
            #Access Facebook Function
            $this->facebook= $args['facebook'];

            # Create your custom meta box
            add_action( 'add_meta_boxes', array(&$this,'APS_add_custom_box') );

            # Save your meta box content
            add_action( 'save_post', array(&$this,'APS_save_custom_meta_box') );

		} # END Function : __construct

        /**
        * Add a custom meta box to a post
        */
        function APS_add_custom_box( $post ) {

            $post_types = get_option( 'APS_post_types' );
            if(!$post_types)
                return false;
            foreach ($post_types as $key => $post_type) {
                add_meta_box(
                    'Meta Box', 
                    'Automatic Post Share',
                    array(&$this,'APS_custom_meta_box_content'), 
                    $post_type, 
                    'side',
                    'high'
                );
            }
       } # END Function : APS_add_custom_box

       /**
       * Content for the custom meta box
       */
        public function APS_custom_meta_box_content( $post ) {
            if(!$this->facebook->check_status()){
                echo "<a href='options-general.php?page=aps-setting'>Error: Some settings are missing.</a>";
                return false;
            }
           #Get post meta value using the key from our save function in the second paramater.
            $screen = get_current_screen();
            if("add"!=$screen->action){
                $_APS_social_fb_it = get_post_meta($post->ID, '_APS_social_fb_it', true);
                $APS_social_fb_it_to = get_post_meta($post->ID, 'APS_social_fb_it_to', true);
                echo '<div class="misc-pub-section misc-pub-post-status"><span id="post-status-display">'.(($_APS_social_fb_it=="1")?"Posted":"Not Posted")." on : </span>  Facebook.</div>";
                
                # For New Updates
                // if($_APS_social_fb_it=="1")
                //     echo '<div class="misc-pub-section misc-pub-post-status"><span id="post-status-display">Posted On : </span><a href="">View Logs</a></div>';
                return;
            }

            echo '<input type="checkbox" name="APS_social_fb_it" id="APS_social-fb-it" class="post-format" value="1" checked/>';
            echo '<label for="APS_social-fb-it"> Post in Facebook</label><br/>';   

            $fb_pages = $this->facebook->get_pages(array("include_user"=>1));
            if($fb_pages){
                echo "<select multiple name='APS_social_fb_it_to[]' id='APS_social_fb_it_to'>";
                foreach ($fb_pages as $key=>$val) {
                echo '<option value="'.$val['id'].'" selected>'.ucfirst($val['name']).'</option>';
                }  
                echo "</select>";
            }
        } # END Function : APS_custom_meta_box_content

        /**
        *  Save Meta and post in facebook
        */
        public function APS_save_custom_meta_box($post_id){
            global $post;
            # Get our form field
            if( $_POST ) {
                 $_APS_social_fb_it = @esc_attr( $_POST['APS_social_fb_it'] );
                 $APS_social_fb_it_to = @$_POST['APS_social_fb_it_to'] ;
                 # Update post meta
                 update_post_meta($post->ID, '_APS_social_fb_it', $_APS_social_fb_it);
                 if($_APS_social_fb_it=="1"){
                    update_post_meta($post->ID, 'APS_social_fb_it_to', $APS_social_fb_it_to);
                    $post_data = get_post($post_id);
                    if($post_data->post_content!=""){
                        $post_org_id = (($post_data->post_parent)?$post_data->post_parent:$post_data->ID);
                        $fb_message = $post_data->post_title." ".get_permalink($post_org_id);
                        $fb_users = $_POST['APS_social_fb_it_to'];
                        if($fb_users){
                            $get_profile_data = get_option("APS_FB_profile_data");
                            foreach ($fb_users as $fb_user) {
                                $arguments = array("profile_id"=>$fb_user,"message"=>$fb_message,"profile_type"=>(($fb_user==$get_profile_data['id'])?"user":"page"));
                                $this->facebook->post_in_wall($arguments);
                            }
                        }                    
                    }
                    
                 }
            }
        } # END Function : APS_save_custom_meta_box
    } # END Class : APS_Custom_Fields
} # END if(!class_exists('APS_Custom_Fields'))