<div class="wrap">
    <h2>Automatic Post Share</h2>
    <form method="post" action="options.php"> 
        <?php settings_fields('APS_Setting-group'); ?>
        <?php // do_settings_fields('aps-setting','APS_Setting-section'); ?>

        <?php do_settings_sections('aps-setting'); ?>

        <?php @submit_button(); ?>
    </form>
</div>