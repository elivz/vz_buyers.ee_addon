<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Buyers Class
 *
 * @author    Eli Van Zoeren <eli@elivz.com>
 * @copyright Copyright (c) 2012 Eli Van Zoeren
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */
 
class Vz_buyers_ft extends EE_Fieldtype {

    public $info = array(
        'name'      => 'VZ Buyers',
        'version'   => '1.0',
    );
    
	/**
	 * Fieldtype Constructor
	 */
	function Vz_buyers_ft()
	{
        parent::EE_Fieldtype();
	}

	// --------------------------------------------------------------------
	    
    /**
     * Display Field
     */
    function display_field($field_data)
    {
        $this->EE->load->library('table');
		$this->EE->lang->loadfile('vz_buyers');

        // Make sure the entry has been saved
        if ( empty($_GET['entry_id']) ) {
            return '<p>'.lang('not_saved').'</p>';
        }

        // Get everything we need from the database
        $this->EE->db->select('store_order_items.order_id, store_order_items.item_qty, store_orders.order_date, store_orders.order_email, store_orders.billing_name');
        $this->EE->db->from('store_order_items');
        $this->EE->db->where('store_order_items.entry_id', $_GET['entry_id']);
        $this->EE->db->join('store_orders', 'store_orders.order_id = store_order_items.order_id');
        $orders = $this->EE->db->get()->result_array();

        $data = array(
            'orders' => $orders
        );
		
        return $this->EE->load->view('index', $data, true);
    }
	
	// --------------------------------------------------------------------

    /**
     * Display Tag
     */
    function replace_tag($address, $params=array(), $tagdata=FALSE)
    {
        /* TODO: Output a list of buyers */
    }
	
}

/* End of file ft.vz_buyers.php */