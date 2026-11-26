<?php

    
    /**
    * Compatibility     : SG_Security
    * Introduced at     : 1.6.9
    * Latest check on   : 1.6.9  
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_sgs
        {
            
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_action( 'login_head',                                array( $this, 'login_head') , -1);    
                           
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'sg-security/sg-security.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function login_head()
                {   
                    if (  ! $this->wph->functions->check_backtrace_for_caller( array ( array ( 'load_form', 'SG_Security\Sg_2fa\Sg_2fa') ) ) )
                        return;
                    
                    $new_login_url       =   $this->wph->functions->get_module_item_setting('new_wp_login_php');
                    if ( empty (  $new_login_url ) )
                        return;
                    
                    $headers    =   headers_list();
                    
                    $cookie = $this->get_set_cookie_details( $headers, 'sgs_2fa_login_nonce'); 
                    
                    if ( ! $cookie )             
                        return;
                        
                    $new_header = 'Set-Cookie: '
                        . $cookie['name'] . '=' . $cookie['value'];

                    if ($cookie['expires'] !== null) {
                        $new_header .= '; Expires=' . $cookie['expires'];
                    }

                    if ($cookie['max-age'] !== null) {
                        $new_header .= '; Max-Age=' . $cookie['max-age'];
                    }

                    // Use the new path.
                    $new_header .= '; Path=/' . $new_login_url;

                    if ($cookie['domain'] !== null) {
                        $new_header .= '; Domain=' . $cookie['domain'];
                    }

                    if ($cookie['secure']) {
                        $new_header .= '; Secure';
                    }

                    if ($cookie['httponly']) {
                        $new_header .= '; HttpOnly';
                    }

                    if ($cookie['samesite'] !== null) {
                        $new_header .= '; SameSite=' . $cookie['samesite'];
                    }

                    header($new_header, false);
                }
                
                
            /**
             * Find and parse a specific Set-Cookie header.
             *
             * @param array|string $headers Headers as an array of lines or a raw header string.
             * @param string       $cookie_name Cookie name to search for.
             *
             * @return array|null
             */
            private function get_set_cookie_details($headers, $cookie_name) 
                {

                    // Normalize headers to an array of individual header lines.
                    if (is_string($headers)) {
                        // Split on CRLF/LF, but keep each Set-Cookie header intact.
                        $headers = preg_split('/\r\n|\r|\n/', $headers);
                    }

                    if (!is_array($headers)) {
                        return null;
                    }

                    foreach ($headers as $header) {

                        $header = trim($header);

                        // Only process Set-Cookie headers.
                        if (stripos($header, 'Set-Cookie:') !== 0) {
                            continue;
                        }

                        $cookie_string = trim(substr($header, strlen('Set-Cookie:')));

                        // Split cookie into name/value + attributes.
                        $parts = preg_split('/\s*;\s*/', $cookie_string);

                        if (empty($parts[0])) {
                            continue;
                        }

                        // First part is always name=value.
                        $name_value = explode('=', $parts[0], 2);

                        if (count($name_value) !== 2) {
                            continue;
                        }

                        $name  = trim($name_value[0]);
                        $value = $name_value[1];

                        // Exact cookie-name match.
                        if ($name !== $cookie_name) {
                            continue;
                        }

                        $result = array(
                            'name'       => $name,
                            'value'      => $value,
                            'expires'    => null,
                            'expires_ts' => null,
                            'max-age'    => null,
                            'path'       => null,
                            'domain'     => null,
                            'secure'     => false,
                            'httponly'   => false,
                            'samesite'   => null,
                            'attributes' => array(),
                            'header'     => $header,
                        );

                        // Parse remaining attributes.
                        foreach (array_slice($parts, 1) as $attribute) {

                            $attribute = trim($attribute);

                            if ($attribute === '') {
                                continue;
                            }

                            // Attribute with a value.
                            if (strpos($attribute, '=') !== false) {

                                list($attr_name, $attr_value) = explode('=', $attribute, 2);

                                $attr_name  = strtolower(trim($attr_name));
                                $attr_value = trim($attr_value);

                                switch ($attr_name) {

                                    case 'expires':
                                        $result['expires'] = $attr_value;
                                        $result['expires_ts'] = strtotime($attr_value);
                                        break;

                                    case 'max-age':
                                        $result['max-age'] = $attr_value;
                                        break;

                                    case 'path':
                                        $result['path'] = $attr_value;
                                        break;

                                    case 'domain':
                                        $result['domain'] = $attr_value;
                                        break;

                                    case 'samesite':
                                        $result['samesite'] = $attr_value;
                                        break;

                                    default:
                                        $result['attributes'][$attr_name] = $attr_value;
                                        break;
                                }

                            } else {

                                // Boolean attribute.
                                $attr_name = strtolower($attribute);

                                switch ($attr_name) {
                                    case 'secure':
                                        $result['secure'] = true;
                                        break;

                                    case 'httponly':
                                        $result['httponly'] = true;
                                        break;

                                    default:
                                        $result['attributes'][$attr_name] = true;
                                        break;
                                }
                            }
                        }

                        return $result;
                    }

                    return null;
                }
       
                            
        }
        
    new WPH_conflict_handle_sgs();    
        
?>