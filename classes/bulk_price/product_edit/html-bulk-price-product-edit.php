<?php
$bulk_data = new \tonysong\bulk_price\data\Bulk_Data($bulk_data->product_id);
$id = rand(1, 10000);
?>
<template id="template-bulk-price-data">
    <div id="discount-1" data-id="<?php echo $id ?>">
        <p class="form-field"><label><strong>Discount <span class="disc-num">1</span></strong></label></p>
        <?php
        woocommerce_wp_text_input(array(
            'id' => '_bulk_minqty_' . $id,
            'label' => 'Min. Quantity',
            'name' => '_bulk_minqty[]',
            'class' => '_bulk_minqty',
            'wrapper_class' => 'bulk_minqty_field',
            'type' => 'number',
            'custom_attributes' => array(
                'required' => 'required'
            )
        ));

        woocommerce_wp_text_input(array(
            'id' => '_bulk_percent_' . $id,
            'label' => 'Percentage discount',
            'name' => '_bulk_percent[]',
            'type' => 'number',
            'class' => '_bulk_percent',
            'wrapper_class' => 'bulk_percent_field',
            'custom_attributes' => array(
                'max' => '100'
            )
        ));
        woocommerce_wp_text_input(array(
            'id' => '_bulk_fixed_' . $id,
            'label' => 'Fixed discount',
            'name' => '_bulk_fixed[]',
            'class' => '_bulk_fixed',
            'wrapper_class' => 'bulk_fixed_field',
            'type' => 'number',
        ));

        woocommerce_wp_text_input(array(
            'id' => '_bulk_flat_' . $id,
            'label' => 'Flat discount',
            'type' => 'number',
            'name' => '_bulk_flat[]',
            'class' => '_bulk_flat',
            'wrapper_class' => 'bulk_flat_field',
            'data_type' => 'decimal'
        ));
        ?>
        <p class="form-field text-right">
            <button class="remove-data button">Remove</button>
        </p>
        <hr>
    </div>
</template>

<div id="bulk_price_data" class="panel woocommerce_options_panel">
    <div class="options_group">

        <?php
        woocommerce_wp_checkbox(array(
            'id' => '_bulk_enable',
            'label' => 'Enable Bulk Price',
            'desc_tip' => true,
            'cb_value' => 1
        ));

        woocommerce_wp_select(array(
            'id' => '_bulk_type',
            'label' => 'Type',
            'desc_tip' => true,
            'description' => 'Percent is percentage Discount, Fixed is discount to each price and Flat is one-time discount after grand total',
            'options' => array(
                '0' => 'Percent',
                '1' => 'Fixed',
                '2' => 'Flat'
            )
        ));

        ?>
        <p class="form-field">
            <label for="_bulk_description">Bulk Description:</label>
        </p>
        <div class="form-field">
            <?php
            wp_editor($bulk_data->bulk_description, '_bulk_description', array(
                'editor_class' => 'form-field',
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => '_bulk_description',
                'textarea_rows' => 10,
                'teeny' => true
            ));
            ?>
        </div>
    </div>
    <div class="options-group options-group__data">
        <?php
        for ($i = 0; $i < count($bulk_data->bulk_values);$i++) {
            $id = rand(1, 10000);
            ?>
            <div id="discount-<?php echo $id ?>">
                <p class="form-field"><label><strong>Discount <span class="disc-num">1</span></strong></label></p>
                <?php

                woocommerce_wp_text_input(array(
                    'id' => '_bulk_minqty_' . $id,
                    'label' => 'Min. Quantity',
                    'name' => '_bulk_minqty[]',
                    'class' => '_bulk_minqty',
                    'wrapper_class' => 'bulk_minqty_field',
                    'type' => 'number',
                    'custom_attributes' => array(
                        'required' => 'required'
                    ),
                    'value' => $bulk_data->bulk_values[$i]->bulk_minqty
                ));

                woocommerce_wp_text_input(array(
                    'id' => '_bulk_percent_' . $id,
                    'label' => 'Percentage discount',
                    'name' => '_bulk_percent[]',
                    'type' => 'number',
                    'class' => '_bulk_percent',
                    'wrapper_class' => 'bulk_percent_field',
                    'custom_attributes' => array(
                        'max' => '100'
                    ),
                    'value' => $bulk_data->bulk_values[$i]->bulk_percent
                ));
                woocommerce_wp_text_input(array(
                    'id' => '_bulk_fixed_' . $id,
                    'label' => 'Fixed discount',
                    'name' => '_bulk_fixed[]',
                    'class' => '_bulk_fixed',
                    'wrapper_class' => 'bulk_fixed_field',
                    'type' => 'number',
                    'value' => $bulk_data->bulk_values[$i]->bulk_fixed
                ));

                woocommerce_wp_text_input(array(
                    'id' => '_bulk_flat_' . $id,
                    'label' => 'Flat discount',
                    'type' => 'number',
                    'name' => '_bulk_flat[]',
                    'class' => '_bulk_flat',
                    'wrapper_class' => 'bulk_flat_field',
                    'data_type' => 'decimal',
                    'value' => $bulk_data->bulk_values[$i]->bulk_flat

                ));

                ?>
                <p class="form-field text-right">
                    <button class="remove-data button" data-id="<?php echo $id ?>">Remove</button>
                </p>
                <hr>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="options-group">
        <p>
            <Button class="button" id="add_bulk_discount_data">Add</Button>
        </p>
    </div>
</div>
<script>
    jQuery(function ($) {
        var $data_group = $('.options-group__data');
        var $template = $('#template-bulk-price-data');
        var $btn_add = $('#add_bulk_discount_data');
        var $bulk_type = $('#_bulk_type');
        rearrangeNumber();
        redisplayFields();
        $btn_add.on('click', function (e) {
            e.preventDefault();
            var $new = $($template.clone().html());
            var $btn_remove_data = $($new.find('.remove-data'));
            var id = rand_num(10000);
            var data_id = $new.data('id');
            var $input_percent = $new.find('._bulk_percent');
            var $input_fixed = $new.find('._bulk_fixed');
            var $input_flat = $new.find('._bulk_flat');
            var $input_minqty = $new.find('._bulk_minqty');
            $new.removeAttr('data-id');
            $new.attr('id', 'discount-' + id);

            $input_minqty.attr('id', '_bulk_minqty_' + id);
            $input_minqty.siblings('label').attr('for', '_bulk_minqty_' + id);
            $input_minqty.closest('p').removeClass('_bulk_minqty_' + data_id + '_field').addClass('_bulk_minqty_' + id + '_field');

            $input_percent.attr('id', '_bulk_percent_' + id);
            $input_percent.siblings('label').attr('for', '_bulk_percent_' + id);
            $input_percent.closest('p').removeClass('_bulk_percent_' + data_id + '_field').addClass('_bulk_percent_' + id + '_field');

            $input_fixed.attr('id', '_bulk_fixed_' + id);
            $input_fixed.siblings('label').attr('for', '_bulk_fixed_' + id);
            $input_fixed.closest('p').removeClass('_bulk_fixed_' + data_id + '_field').addClass('_bulk_fixed_' + id + '_field');

            $input_flat.attr('id', '_bulk_flat_' + id);
            $input_flat.siblings('label').attr('for', '_bulk_flat_' + id);
            $input_flat.closest('p').removeClass('_bulk_flat_' + data_id + '_field').addClass('_bulk_flat_' + id + '_field');

            $btn_remove_data.attr('data-id', id);
            $data_group.append($new);
            rearrangeNumber();
            redisplayFields();
        });

        $(document).on('click', '.remove-data', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#discount-' + id).remove();
            rearrangeNumber();
            redisplayFields();
        });

        $bulk_type.on('change', function (e) {
            e.preventDefault();
            redisplayFields();
        });

        function redisplayFields() {
            var value = $bulk_type.val();
            var $percent_field = $('.bulk_percent_field');
            var $fixed_field = $('.bulk_fixed_field');
            var $flat_field = $('.bulk_flat_field');

            if (value === "0") {
                $percent_field.removeClass('hide');
                $fixed_field.addClass('hide');
                $flat_field.addClass('hide');

                $percent_field.find('input').attr('required', 'required')
                $fixed_field.find('input').removeAttr('required');
                $flat_field.find('input').removeAttr('required')
            }
            if (value === "1") {
                $percent_field.addClass('hide');
                $fixed_field.removeClass('hide');
                $flat_field.addClass('hide');

                $percent_field.find('input').removeAttr('required');
                $fixed_field.find('input').attr('required', 'required');
                $flat_field.find('input').removeAttr('required');
            }

            if (value === "2") {
                $percent_field.addClass('hide');
                $fixed_field.addClass('hide');
                $flat_field.removeClass('hide');

                $percent_field.find('input').removeAttr('required');
                $fixed_field.find('input').removeAttr('required');
                $flat_field.find('input').attr('required', 'required');
            }
        }

        function rearrangeNumber() {
            var $data_group_children = $('.options-group__data > div');
            if ($data_group_children.length) {
                $data_group_children.each(function (index, el) {
                    $(this).find('.disc-num').text(index + 1);
                })
            }
        }
    });

    function rand_num(max) {
        return Math.floor((Math.random() * max) + 1);
    }


</script>
<style>
    div.form-field {
        padding: 5px 10px;
    }

    .text-right {
        text-align: right;
    }

    .hide {
        display: none !important;
    }
    .options-group__data{
        max-height: 520px;
        overflow-y: auto;
    }
</style>