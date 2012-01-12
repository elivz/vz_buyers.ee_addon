<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * VZ Buyers Module Front End File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Eli Van Zoeren
 * @link		http://elivz.com
 * @license     http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */

class Vz_buyers {
	
	public $return_data;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	public function print_csv()
	{
		$this->EE->lang->loadfile('vz_buyers');
		
        // Get everything we need from the database
        $orders = $this->EE->db->select('store_order_items.order_id, store_order_items.item_qty, store_orders.order_date, store_orders.order_email, store_orders.billing_name')
                    ->from('store_order_items')
                    ->where('store_order_items.entry_id', $_GET['entry_id'])
                    ->join('store_orders', 'store_orders.order_id = store_order_items.order_id')
                    ->get()->result_array();
        
        $csv = implode(',', array(
            lang('col_order_no'),
            lang('col_qty'),
            lang('col_name'),
            lang('col_email'),
            lang('col_date')
        ));
        
        // Print out each buyer on a separate row
        foreach ($orders as $order)
        {
            $csv .= NL . implode(',', array(
                $order['order_id'],
                $order['item_qty'],
                '"'.str_replace('"', '""', $order['billing_name']).'"',
                '"'.str_replace('"', '""', $order['order_email']).'"',
                $this->EE->localize->set_human_time($order['order_date'])
            ));
        }
        
        // Get the entry URL Title
        $entry = $this->EE->db->select('url_title')
                    ->where('entry_id', $_GET['entry_id'])
                    ->get('channel_titles')->row();
        
        // Send the CSV to the browser as an attachemnt
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename={$entry->url_title}.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $csv;
        die();
	}
	
}
/* End of file mod.vz_buyers.php */
/* Location: /system/expressionengine/third_party/vz_buyers/mod.vz_buyers.php */