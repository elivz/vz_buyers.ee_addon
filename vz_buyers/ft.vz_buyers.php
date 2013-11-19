<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * VZ Buyers Fieldtype
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Eli Van Zoeren
 * @link		http://elivz.com
 * @license     http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */

 
class Vz_buyers_ft extends EE_Fieldtype {

    public $info = array(
        'name'      => 'VZ Buyers',
        'version'   => '1.0.1',
    );

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
        $orders = $this->EE->db->select('store_order_items.order_id, store_order_items.item_qty, store_orders.order_date, store_orders.order_email, store_orders.billing_name')
                    ->from('store_order_items')
                    ->where('store_order_items.entry_id', $_GET['entry_id'])
                    ->where('store_order.order_completed_date >', 0)
                    ->join('store_orders', 'store_orders.order_id = store_order_items.order_id')
                    ->get()->result_array();

        // Create the url for the CSV download
        $csv_action = $this->EE->cp->fetch_action_id('Vz_buyers', 'print_csv');
        $csv_url = $csv_action ? '<a href="'.$this->EE->functions->fetch_site_index(0,0).QUERY_MARKER.'ACT='.$csv_action.AMP.'entry_id='.$_GET['entry_id'].'">Download CSV</a>' : '';
        
        $data = array(
            'orders' => $orders,
            'csv_url' => $csv_url
        );
		
        return $this->EE->load->view('index', $data, true);
    }
	
	// --------------------------------------------------------------------

    /**
     * Display Tag
     */
    function replace_tag($field_data, $params=array(), $tagdata=FALSE)
    {
        /* TODO: Output a list of buyers */
    }
	
}

/* End of file ft.vz_buyers.php */
