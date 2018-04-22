<?php
namespace tonysong\bulk_price\data;
class Bulk_Value{
    public $bulk_minqty;
    public $bulk_percent;
    public $bulk_fixed;
    public $bulk_flat;
    function __construct($bulk_minqty, $bulk_percent, $bulk_fixed, $bulk_flat)
    {
        $this->bulk_minqty = $bulk_minqty;
        $this->bulk_percent = $bulk_percent;
        $this->bulk_fixed = $bulk_fixed;
        $this->bulk_flat = $bulk_flat;
    }
}