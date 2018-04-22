<?php
namespace tonysong\bulk_price\data;
class Bulk_Data{
    public $product_id;
    public $bulk_enable;
    public $bulk_type;
    public $bulk_description;
    public $bulk_values = array();

    public function __construct($product_id, $sort_minqty = 'asc')
    {
        //get data
        $this->product_id = $product_id;
        $bulk_enable = get_post_meta($product_id, '_bulk_enable', true);
        $bulk_type = get_post_meta($product_id, '_bulk_type', true);
        $bulk_description = get_post_meta($product_id, '_bulk_description', true);
        $bulk_minqty = array();
        $bulk_percent = array();
        $bulk_fixed = array();
        $bulk_flat = array();
        for ($i = 0; $i < 100; $i++) {
            $minqty = get_post_meta($product_id, '_bulk_minqty_' . $i, true);
            if (!empty($minqty)) {
                $bulk_minqty[] = $minqty;
            }
        }

        $rec_count = count($bulk_minqty);
        foreach (range(0, $rec_count - 1) as $i) {
            $bulk_percent[] = get_post_meta($product_id, '_bulk_percent_' . $i, true);
            $bulk_fixed[] = get_post_meta($product_id, '_bulk_fixed_' . $i, true);
            $bulk_flat[] = get_post_meta($product_id, '_bulk_flat_' . $i, true);
        }

        //store it
        $this->bulk_enable = $bulk_enable;
        $this->bulk_type = $bulk_type;
        $this->bulk_description = $bulk_description;

        for($i = 0;$i < $rec_count;$i++){
            $this->bulk_values[] = new Bulk_Value($bulk_minqty[$i], $bulk_percent[$i], $bulk_fixed[$i], $bulk_flat[$i]);
        }
        if($sort_minqty!='asc'){
            usort($this->bulk_values, array($this, 'cmp_minqty_desc'));
        }
        else{
            usort($this->bulk_values, array($this, 'cmp_minqty_asc'));
        }

    }

    public function data()
    {
        // TODO: Implement __get() method.

        $bulk_data = array(
            'bulk_enable' => $this->bulk_enable,
            'bulk_type' => $this->bulk_type,
            'bulk_description' => $this->bulk_description,
            'bulk_values' => $this->bulk_values
        );
        return $bulk_data;
    }

    function cmp_minqty_asc(Bulk_Value $a,Bulk_Value $b){
        return ($a->bulk_minqty > $b->bulk_minqty);
    }

    function cmp_minqty_desc(Bulk_Value $a,Bulk_Value $b){
        return ($a->bulk_minqty < $b->bulk_minqty);
    }
}