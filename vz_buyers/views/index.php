<?php

$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
    lang('col_order_no'),
    lang('col_qty'),
    lang('col_name'),
    lang('col_date'),
    lang('col_details')
);

// Print out each buyer on a separate row
$total_qty = 0;
foreach ($orders as $order)
{
    $total_qty += $order['item_qty'];
    $this->table->add_row(
        $order['order_id'],
        $order['item_qty'],
        "<a href='mailto:{$order['order_email']}'>{$order['billing_name']}</a>",
        $this->localize->set_human_time($order['order_date']),
        '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=store'.AMP.'method=orders'.AMP.'order_id='.$order['order_id'].'">'.lang('col_details').'</a>'
    );
}

// Total quantity
$this->table->add_row(
    '<strong>'.lang('total').'</strong>',
    '<strong>'.$total_qty.'</strong>',
    '',
    '',
    ''
);

echo $this->table->generate();
$this->table->clear();

?>