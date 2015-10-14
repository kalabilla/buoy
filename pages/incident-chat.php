<?php
$options = get_option($this->prefix . 'settings');
$alert_post = $this->getAlert(urldecode($_GET[$this->prefix . 'incident_hash']));
$alerter = get_userdata($alert_post->post_author);
$curr_user = wp_get_current_user();
$auto_show_modal = ($curr_user->ID === $alerter->ID) ? 'auto-show-modal' : '';
$responders = $this->getIncidentResponders($alert_post);
$responder_info = array();
foreach ($responders as $responder_id) {
    $responder_data = get_userdata($responder_id);
    $responder_info[] = array(
        'id' => $responder_id,
        'display_name' => $responder_data->display_name,
        'avatar_url' => get_avatar_url($responder_id, array('size' => 32)),
        'geo' => $this->getResponderGeoLocation($alert_post, $responder_id)
    );
}
?>
<div id="alert-map" role="alert" class="alert alert-warning alert-dismissible fade in">
    <button id="toggle-incident-map-btn" class="btn btn-default" type="button"><?php esc_html_e('Show Map', 'better-angels');?></button>
</div>
<div id="map-container" class="container-fluid"
    data-alerter="<?php print esc_attr($alerter->display_name);?>"
    data-latitude="<?php print esc_attr(get_post_meta($alert_post->ID, 'geo_latitude', true));?>"
    data-longitude="<?php print esc_attr(get_post_meta($alert_post->ID, 'geo_longitude', true));?>"
    data-icon="<?php print esc_attr(get_avatar_url($alerter->ID, array('size' => 32)));?>"
    data-info-window-text="<?php print sprintf(esc_attr__("%s's last known location", 'better-angels'), $alerter->display_name);?>"
    data-responder-info='<?php print esc_attr(json_encode($responder_info));?>'
    >
    <div id="map"></div>
</div>
<div id="tlkio" data-channel="<?php print esc_attr(get_post_meta($alert_post->ID, $this->prefix . 'chat_room_name', true));?>" data-nickname="<?php esc_attr_e($curr_user->display_name);?>" style="height:100%;"></div><script async src="https://tlk.io/embed.js" type="text/javascript"></script>

<div id="safety-information-modal" class="modal fade <?php esc_attr_e($auto_show_modal);?>" role="dialog" aria-labelledby="safety-information-modal-label">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php esc_attr_e('Close', 'better-angels');?>"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="safety-information-modal-label"><?php esc_html_e('Safety information', 'better-angels');?></h4>
            </div>
            <div class="modal-body">
                <?php print $options['safety_info']; // TODO: Can we harden against XSS here? ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', 'better-angels');?></button>
            </div>
        </div><!-- .modal-content -->
    </div><!-- .modal-dialog -->
</div><!-- .modal.fade -->
