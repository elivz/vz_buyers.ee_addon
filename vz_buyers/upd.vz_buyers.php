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
 * VZ Buyers Module Install/Update File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Eli Van Zoeren
 * @link		http://elivz.com
 * @license     http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */

class Vz_buyers_upd {
	
	public $version = '1.0.1';
	
	private $EE;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Installation Method
	 *
	 * @return 	boolean 	TRUE
	 */
	public function install()
	{
		$mod_data = array(
			'module_name'			=> 'Vz_buyers',
			'module_version'		=> $this->version,
			'has_cp_backend'		=> "n",
			'has_publish_fields'	=> "n"
		);
		$this->EE->db->insert('modules', $mod_data);

        $data = array(
            'class'     => 'Vz_buyers' ,
            'method'    => 'print_csv'
        );
        $this->EE->db->insert('actions', $data);
		
		return TRUE;
	}

	// ----------------------------------------------------------------
	
	/**
	 * Uninstall
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function uninstall()
	{
		$mod_id = $this->EE->db->select('module_id')
								->get_where('modules', array(
									'module_name'	=> 'Vz_buyers'
								))->row('module_id');
		
		$this->EE->db->where('module_id', $mod_id)
					 ->delete('module_member_groups');
		
		$this->EE->db->where('module_name', 'Vz_buyers')
					 ->delete('modules');
		
		$this->EE->db->where('class', 'Vz_buyers')
					 ->delete('actions');
		
		return TRUE;
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Module Updater
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function update($current = '')
	{
		return FALSE;
	}
	
}
/* End of file upd.vz_buyers.php */
/* Location: /system/expressionengine/third_party/vz_buyers/upd.vz_buyers.php */
