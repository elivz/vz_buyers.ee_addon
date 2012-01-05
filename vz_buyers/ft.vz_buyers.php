<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Buyers Class
 *
 * @author    Eli Van Zoeren <eli@elivz.com>
 * @copyright Copyright (c) 2011 Eli Van Zoeren
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

        if (!isset($this->EE->session->cache['vz_buyers']))
        {
            $this->EE->session->cache['vz_buyers'] = array('css' => FALSE, 'countries' => array());
        }
        $this->cache =& $this->EE->session->cache['vz_buyers'];
		
        // Cache the array of country names
		$this->EE->lang->loadfile('vz_buyers');
        foreach ($this->country_codes as $country)
        {
            $this->cache['countries'][$country] = $this->EE->lang->line($country);
        }
	}
	
	/**
	 * Include the CSS styles, but only once
	 */
	private function _include_css()
	{
        if ( !$this->cache['css'] )
        {
            $this->EE->cp->add_to_head('<style type="text/css">
                .vz_buyers { padding-bottom: 0.5em; }
                .vz_buyers label { display:block; }
                .vz_buyers input { width:99%; padding:4px; }
                .vz_buyers_street_field, .vz_buyers_street_2_field, .vz_buyers_city_field { float:left; width:48%; padding-right:2%; }
                .vz_buyers_region_field, .vz_buyers_postal_code_field { float:left; width:23%; padding-right:2%; }
                .vz_buyers_region_cell, .vz_buyers_postal_code_cell { float:left; width:48%; padding-right:2%; }
            </style>');
        	
        	$this->cache['css'] = TRUE;
        }
    }


	// --------------------------------------------------------------------
	
	
	/**
     * Generate the publish page UI
     */
    private function _address_form($name, $data, $is_cell=FALSE)
    {
		$this->EE->load->helper('form');
		$this->EE->lang->loadfile('vz_buyers');
		
        $this->_include_css();
		
        $form = "";
        $fields = array(
            'street' => '',
            'street_2' => '',
            'city' => '',
            'region' => '',
            'postal_code' => '',
            'country' => 'US'
        );
        
        // Set default values
        if (!is_array($data)) $data = unserialize(htmlspecialchars_decode($data));
        if (!is_array($data)) $data = array();
        $data = array_merge($fields, $data);
        
        foreach(array_keys($fields) as $field)
        {
            $form .= '<div class="vz_buyers vz_buyers_'.$field.($is_cell ? '_cell' : '_field').'">';
            $form .= form_label($this->EE->lang->line($field), $name.'_'.$field);
            
            if ($field == 'country')
            {
                // Output a select box for the country
                $form .= form_dropdown($name.'['.$field.']', $this->cache['countries'], $data[$field], 'id="'.$name.'_'.$field.'"');
            }
            else
            {
                // All other fields are just text inputs
                $form .= form_input($name.'['.$field.']', $data[$field], 'id="'.$name.'_'.$field.'" class="vz_buyers_'.$field.'"');
            }
            $form .= '</div>';
        }
        
        return $form;
    }
    
    /**
     * Display Field
     */
    function display_field($field_data)
    {
        return $this->_address_form($this->field_name, $field_data);
    }
    
    /**
     * Display Cell
     */
    function display_cell($cell_data)
    {
        return $this->_address_form($this->cell_name, $cell_data, TRUE);
    }
	
    /**
     * Display for Low Variables
     */
    function display_var_field($field_data)
    {
        return $this->_address_form($this->field_name, $field_data);
    }

	
	// --------------------------------------------------------------------
    
    /**
     * Save Field
     */
    function save($data)
    {
    	return serialize($data);
    }
    
    /**
     * Save Cell
     */
    function save_cell($data)
    {
        return serialize($data);
    }
	
    /**
     * Save Low Variable
     */
    function save_var_field($data)
    {
        return serialize($data);
    }

	
	// --------------------------------------------------------------------

    /**
     * Unserialize the data
     */
    function pre_process($data)
    {
        return unserialize($data);
    }

    /**
     * Display Tag
     */
    function replace_tag($address, $params=array(), $tagdata=FALSE)
    {
        $wrapper_attr = isset($params['wrapper_attr']) ? $params['wrapper_attr'] : FALSE;
        $style = isset($params['style']) ? $params['style'] : 'microformat';
        
        if (!$tagdata) // Single tag
        {
            switch ($style)
            {
                case 'inline' :
                    $output = "{$address['street']}, ".($address['street_2'] ? $address['street_2'].', ' : '')."{$address['city']}, {$address['region']} {$address['postal_code']}, {$this->EE->lang->line($address['country'])}";
                    break;
                case 'plain' :
                    $output = "
                        {$address['street']}
                        {$address['street_2']}
                        {$address['city']}, {$address['region']} {$address['postal_code']}
                        {$this->EE->lang->line($address['country'])}";
                    break;
                case 'rdfa' :
                    $output = "
                        <div xmlns:v='http://rdf.data-vocabulary.org/#' typeof='v:Address' class='adr' {$wrapper_attr}>
                            <div property='v:street-address'>
                                <div class='street-address'>{$address['street']}</div>
                                <div class='extended-address'>{$address['street_2']}</div>
                            </div>
                            <div>
                                <span property='v:locality' class='locality'>{$address['city']}</span>,
                                <span property='v:region' class='region'>{$address['region']}</span>
                                <span property='v:postal-code' class='postal-code'>{$address['postal_code']}</span>
                            </div>
                            <div property='v:contry-name' class='country'>{$this->EE->lang->line($address['country'])}</div>
                        </div>";
                    break;
                case 'schema' :
                    $output = "
                        <div itemprop='address' itemscope itemtype='http://schema.org/PostalAddress' class='adr' {$wrapper_attr}>
                            <div itemprop='streetAddress'>
                                <div class='street-address'>{$address['street']}</div>
                                <div class='extended-address'>{$address['street_2']}</div>
                            </div>
                            <div>
                                <span itemprop='addressLocality' class='locality'>{$address['city']}</span>,
                                <span itemprop='addressRegion' class='region'>{$address['region']}</span>
                                <span itemprop='postalCode' class='postal-code'>{$address['postal_code']}</span>
                            </div>
                            <div itemprop='addressCountry' class='country'>{$this->EE->lang->line($address['country'])}</div>
                        </div>";
                    break;
                case 'microformat' : default :
                    $output = "
                        <div class='adr' {$wrapper_attr}>
                            <div class='street-address'>{$address['street']}</div>
                            <div class='extended-address'>{$address['street_2']}</div>
                            <div>
                                <span class='locality'>{$address['city']}</span>,
                                <span class='region'>{$address['region']}</span>
                                <span class='postal-code'>{$address['postal_code']}</span>
                            </div>
                            <div class='country'>{$this->EE->lang->line($address['country'])}</div>
                        </div>";
            }
    	}
    	else // Tag pair
    	{
            $address['country'] = $this->EE->lang->line($address['country']);
            
            // Replace the variables            
            $output = $this->EE->TMPL->parse_variables($tagdata, array($address));
    	}
            
        return $output;
    }
	
	/**
     * Display Low Variables tag
	 */
    function display_var_tag($var_data, $tagparams, $tagdata) 
    {
        $data = unserialize(htmlspecialchars_decode($var_data));
        return $this->replace_tag($data, $tagparams, $tagdata);
    }
    
    /*
     * Individual address pieces
     */
    function replace_street($address, $params=array(), $tagdata=FALSE)
    {
        return $address['street'];
    }
    function replace_street_2($address, $params=array(), $tagdata=FALSE)
    {
        return $address['street_2'];
    }
    function replace_city($address, $params=array(), $tagdata=FALSE)
    {
        return $address['city'];
    }
    function replace_region($address, $params=array(), $tagdata=FALSE)
    {
        return $address['region'];
    }
    function replace_postal_code($address, $params=array(), $tagdata=FALSE)
    {
        return $address['postal_code'];
    }
    function replace_country($address, $params=array(), $tagdata=FALSE)
    {
        if (isset($params['code']) && $params['code'] == 'yes')
        {
            return $address['country'];
        }
        else
        {
            return $this->EE->lang->line($address['country']);
        }
    }
}

/* End of file ft.vz_buyers.php */