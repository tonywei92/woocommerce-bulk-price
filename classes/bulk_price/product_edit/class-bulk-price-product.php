<?php
namespace tonysong\bulk_price\product_edit;
use tonysong\bulk_price\data\Bulk_Data;
class Bulk_Price_Product
{
    public function __construct()
    {
        add_filter('woocommerce_product_data_tabs', array($this, 'add_tabs'));
        add_action('woocommerce_product_data_panels', array($this, 'add_bulk_price_page'));
    }

    function add_tabs($tabs)
    {
        $tabs['bulk_price'] = array(
            'label' => __('Bulk Price', 'tonysong'),
            'target' => 'bulk_price_data',
            'class' => array('show_if_simple', 'show_if_variable'),
            'priority' => 80,
        );
        return $tabs;
    }

    function add_bulk_price_page()
    {
        global $post;
        $id = $post->ID;
        $bulk_data = new Bulk_Data($id);
        include('html-bulk-price-product-edit.php');
    }
}

return new Bulk_Price_Product();