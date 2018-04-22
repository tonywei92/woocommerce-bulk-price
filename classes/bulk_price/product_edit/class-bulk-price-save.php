<?php
namespace tonysong\bulk_price\product_edit;

class Bulk_Price_Save{
    function __construct()
    {
        add_action('save_post', array($this, 'save'));
    }

    function save($post_id){
        if(get_post_type($post_id)!=='product'){
            return;
        }
        $bulk_enable = isset($_POST['_bulk_enable']) ? $_POST['_bulk_enable'] : 0;
        $bulk_type = isset($_POST['_bulk_type']) ? $_POST['_bulk_type'] : 0;

        $bulk_description = isset($_POST['_bulk_description']) ? $_POST['_bulk_description'] : '';
        update_post_meta($post_id, '_bulk_enable', $bulk_enable);
        update_post_meta($post_id, '_bulk_type', $bulk_type);
        update_post_meta($post_id, '_bulk_description', $bulk_description);

        $bulk_flat = isset($_POST['_bulk_flat']) ? $_POST['_bulk_flat'] : array();
        $bulk_minqty = isset($_POST['_bulk_minqty']) ? $_POST['_bulk_minqty'] : array();
        $bulk_percent = isset($_POST['_bulk_percent']) ? $_POST['_bulk_percent'] : array();
        $bulk_fixed = isset($_POST['_bulk_fixed']) ? $_POST['_bulk_fixed'] : array();

        $data_count = count($bulk_flat);

        for($i = 0; $i < 100; $i++){
            delete_post_meta($post_id,'_bulk_flat_' . $i);
            delete_post_meta($post_id,'_bulk_minqty_' . $i);
            delete_post_meta($post_id,'_bulk_percent_' . $i);
            delete_post_meta($post_id,'_bulk_fixed_' . $i);
        }

        for($i = 0; $i < $data_count; $i++){
            update_post_meta($post_id, '_bulk_flat_' . $i, $bulk_flat[$i]);
            update_post_meta($post_id, '_bulk_minqty_' . $i, $bulk_minqty[$i]);
            update_post_meta($post_id, '_bulk_percent_' . $i, $bulk_percent[$i]);
            update_post_meta($post_id, '_bulk_fixed_' . $i, $bulk_fixed[$i]);
        }

    }

}

return new Bulk_Price_Save();