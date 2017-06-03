<?php
/**
 * WooCommerce Predictive Search Functions
 *
 * Class Function into woocommerce plugin
 *
 * Table Of Contents
 *
 * woops_limit_words()
 * create_page()
 * create_page_wpml()
 * auto_create_page_for_wpml()
 * strip_shortcodes()
 */
class WC_Predictive_Search_Functions
{

	public static function symbol_entities() {
		$symbol_entities = array(
			"_" => "_",
			"(" => "&lpar;",
			")" => "&rpar;",
			"{" => "&lcub;",
			"}" => "&rcub;",
			"<" => "&lt;",
			">" => "&gt;",
			"«" => "&laquo;",
			"»" => "&raquo;",
			"‘" => "&lsquo;",
			"’" => "&rsquo;",
			"“" => "&ldquo;",
			"”" => "&rdquo;",
			"‐" => "&dash;",
			"-" => "-",
			"–" => "&ndash;",
			"—" => "&mdash;",
			"←" => "&larr;",
			"→" => "&rarr;",
			"↑" => "&uarr;",
			"↓" => "&darr;",
			"©" => "&copy;",
			"®" => "&reg;",
			"™" => "&trade;",
			"€" => "&euro;",
			"£" => "&pound;",
			"¥" => "&yen;",
			"¢" => "&cent;",
			"§" => "&sect;",
			"∑" => "&sum;",
			"µ" => "&micro;",
			"¶" => "&para;",
			"¿" => "&iquest;",
			"¡" => "&iexcl;",

		);

		return apply_filters( 'wc_ps_symbol_entities', $symbol_entities );
	}

	public static function get_argument_vars() {
		$argument_vars = array( 'keyword' , 'search-in', 'cat-in', 'search-other' );
		return $argument_vars;
	}

	public static function special_characters_list() {
		$special_characters = array();
		foreach ( self::symbol_entities() as $symbol => $entity ) {
			$special_characters[$symbol] = $symbol;
		}

		return apply_filters( 'wc_ps_special_characters', $special_characters );
	}

	public static function is_enable_special_characters () {
		$enable_special_characters = true;

		$woocommerce_search_remove_special_character = get_option( 'woocommerce_search_remove_special_character', 'no' );
		if ( 'no' == $woocommerce_search_remove_special_character ) {
			$enable_special_characters = false;
		}

		$woocommerce_search_special_characters = get_option( 'woocommerce_search_special_characters', array() );
		if ( !is_array( $woocommerce_search_special_characters ) || count( $woocommerce_search_special_characters ) < 1 ) {
			$enable_special_characters = false;
		}

		return $enable_special_characters;
	}

	public static function replace_mysql_command( $field_name, $special_symbol, $replace_special_character = 'ignore' ) {
		if ( 'ignore' == $replace_special_character ) {
			$field_name = 'REPLACE( '.$field_name.', " '.$special_symbol.' ", "")';
			$field_name = 'REPLACE( '.$field_name.', " '.$special_symbol.'", "")';
			$field_name = 'REPLACE( '.$field_name.', "'.$special_symbol.' ", "")';
			$field_name = 'REPLACE( '.$field_name.', "'.$special_symbol.'", "")';
		} else {
			$field_name = 'REPLACE( '.$field_name.', " '.$special_symbol.' ", " ")';
			$field_name = 'REPLACE( '.$field_name.', " '.$special_symbol.'", " ")';
			$field_name = 'REPLACE( '.$field_name.', "'.$special_symbol.' ", " ")';
			$field_name = 'REPLACE( '.$field_name.', "'.$special_symbol.'", " ")';
		}

		return $field_name;
	}

	public static function remove_special_characters_in_mysql( $field_name, $search_keyword = '' ) {
		global $wpdb;

		$sql_after = '';

		if ( '' == trim( $field_name ) || '' == trim( $search_keyword ) ) {
			return $sql_after;
		}

		global $predictive_search_mode;

		$multi_keywords = explode( ' ', trim( $search_keyword ) );

		// This is original query
		if ( 'broad' != $predictive_search_mode ) {

			$sql_after .= " ( ";
			$combine = '';
			foreach ( $multi_keywords as $single_keyword ) {
				$sql_after .= $combine . " ( " . $wpdb->prepare( $field_name . " LIKE %s OR " . $field_name . " LIKE %s ", $single_keyword.'%', '% '.$single_keyword.'%' ) . " ) ";
				$combine = " AND ";
			}
			$sql_after .= " ) ";

		} else {

			$sql_after .= " ( ";
			$combine = '';
			foreach ( $multi_keywords as $single_keyword ) {
				$sql_after .= $combine . $wpdb->prepare( $field_name . " LIKE %s ", '%'.$single_keyword.'%' );
				$combine = " AND ";
			}
			$sql_after .= " ) ";

		}

		if ( ! self::is_enable_special_characters() ) {
			return $sql_after;
		}

		$replace_special_character             = get_option( 'woocommerce_search_replace_special_character', 'remove' );
		$woocommerce_search_special_characters = get_option( 'woocommerce_search_special_characters', array() );

		foreach ( $woocommerce_search_special_characters as $special_symbol ) {

			if ( 'both' == $replace_special_character ) {
				if ( 'broad' != $predictive_search_mode ) {

					$sql_after .= " OR ( ";
					$combine = '';
					foreach ( $multi_keywords as $single_keyword ) {
						$sql_after .= $combine . " ( " .  $wpdb->prepare( self::replace_mysql_command( $field_name, $special_symbol, 'ignore' ) . " LIKE %s OR " . self::replace_mysql_command( $field_name, $special_symbol, 'ignore' ) . " LIKE %s ", $single_keyword.'%', '% '.$single_keyword.'%' ) . " ) ";

						$combine = " AND ";
					}
					$sql_after .= " ) ";

					$sql_after .= " OR ( ";
					$combine = '';
					foreach ( $multi_keywords as $single_keyword ) {
						$sql_after .= $combine . " ( " . $wpdb->prepare( self::replace_mysql_command( $field_name, $special_symbol, 'remove' ) . " LIKE %s OR " . self::replace_mysql_command( $field_name, $special_symbol, 'remove' ) . " LIKE %s ", $single_keyword.'%', '% '.$single_keyword.'%' ) . " ) ";

						$combine = " AND ";
					}
					$sql_after .= " ) ";

				} else {

					$sql_after .= " OR ( ";
					$combine = '';
					foreach ( $multi_keywords as $single_keyword ) {
						$sql_after .= $combine . $wpdb->prepare( self::replace_mysql_command( $field_name, $special_symbol, 'ignore' ) . " LIKE %s ", '%'.$single_keyword.'%' );

						$combine = " AND ";
					}
					$sql_after .= " ) ";

					$sql_after .= " OR ( ";
					$combine = '';
					foreach ( $multi_keywords as $single_keyword ) {
						$sql_after .= $combine . $wpdb->prepare( self::replace_mysql_command( $field_name, $special_symbol, 'remove' ) . " LIKE %s ", '%'.$single_keyword.'%' );

						$combine = " AND ";
					}
					$sql_after .= " ) ";
				}
			} else {
				if ( 'broad' != $predictive_search_mode ) {

					$sql_after .= " OR ( ";
					$combine = '';
					foreach ( $multi_keywords as $single_keyword ) {
						$sql_after .= $combine . " ( " . $wpdb->prepare( self::replace_mysql_command( $field_name, $special_symbol, $replace_special_character ) . " LIKE %s OR " . self::replace_mysql_command( $field_name, $special_symbol, $replace_special_character ) . " LIKE %s ", $single_keyword.'%', '% '.$single_keyword.'%' ). " ) ";

						$combine = " AND ";
					}
					$sql_after .= " ) ";

				} else {

					$sql_after .= " OR ( ";
					$combine = '';
					foreach ( $multi_keywords as $single_keyword ) {
						$sql_after .= $combine . $wpdb->prepare( self::replace_mysql_command( $field_name, $special_symbol, $replace_special_character ) . " LIKE %s ", '%'.$single_keyword.'%' );

						$combine = " AND ";
					}
					$sql_after .= " ) ";
				}
			}

		}

		return $sql_after;
	}

	public static function remove_s_letter_at_end_word( $search_keyword ) {
		$search_keyword_new = '';
		$search_keyword_new_a = array();
		$search_keyword_split = explode( " ", trim( $search_keyword ) );
		if ( is_array( $search_keyword_split ) && count( $search_keyword_split ) > 0 ) {
			foreach ( $search_keyword_split as $search_keyword_element ) {
				if ( strlen( $search_keyword_element ) > 2 ) {
					$search_keyword_new_a[] = rtrim( $search_keyword_element, 's' );
				} else {
					$search_keyword_new_a[] = $search_keyword_element;
				}
			}
			$search_keyword_new = implode(" ", $search_keyword_new_a);
		}

		if ( '' != $search_keyword && $search_keyword_new != $search_keyword ) {
			return $search_keyword_new;
		} else {
			return false;
		}
	}

	public static function woops_limit_words($str='',$len=100,$more) {
		if (trim($len) == '' || $len < 0) $len = 100;
	   if ( $str=="" || $str==NULL ) return $str;
	   if ( is_array($str) ) return $str;
	   $str = trim($str);
	   $str = strip_tags(str_replace("\r\n", "", $str));
	   if ( strlen($str) <= $len ) return $str;
	   $str = substr($str,0,$len);
	   if ( $str != "" ) {
			if ( !substr_count($str," ") ) {
					  if ( $more ) $str .= " ...";
					return $str;
			}
			while( strlen($str) && ($str[strlen($str)-1] != " ") ) {
					$str = substr($str,0,-1);
			}
			$str = substr($str,0,-1);
			if ( $more ) $str .= " ...";
			}
			return $str;
	}

	public static function create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;

		$option_value = get_option($option);

		if ( $option_value > 0 && get_post( $option_value ) )
			return $option_value;

		$page_id = $wpdb->get_var( "SELECT ID FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%$page_content%'  AND `post_type` = 'page' AND post_status = 'publish' ORDER BY ID ASC LIMIT 1" );

		if ( $page_id != NULL ) :
			if ( ! $option_value )
				update_option( $option, $page_id );
			return $page_id;
		endif;

		$page_data = array(
			'post_status' 		=> 'publish',
			'post_type' 		=> 'page',
			'post_author' 		=> 1,
			'post_name' 		=> $slug,
			'post_title' 		=> $page_title,
			'post_content' 		=> $page_content,
			'post_parent' 		=> $post_parent,
			'comment_status' 	=> 'closed'
		);
		$page_id = wp_insert_post( $page_data );

		if ( class_exists('SitePress') ) {
			global $sitepress;
			$source_lang_code = $sitepress->get_default_language();
			$trid = $sitepress->get_element_trid( $page_id, 'post_page' );
			if ( ! $trid ) {
				$wpdb->query( "UPDATE ".$wpdb->prefix . "icl_translations SET trid=".$page_id." WHERE element_id=".$page_id." AND language_code='".$source_lang_code."' AND element_type='post_page' " );
			}
		}

		update_option( $option, $page_id );

		return $page_id;
	}

	public static function create_page_wpml( $trid, $lang_code, $source_lang_code, $slug, $page_title = '', $page_content = '' ) {
		global $wpdb;

		$element_id = $wpdb->get_var( "SELECT ID FROM " . $wpdb->posts . " AS p INNER JOIN " . $wpdb->prefix . "icl_translations AS ic ON p.ID = ic.element_id WHERE p.post_content LIKE '%$page_content%' AND p.post_type = 'page' AND p.post_status = 'publish' AND ic.trid=".$trid." AND ic.language_code = '".$lang_code."' AND ic.element_type = 'post_page' ORDER BY p.ID ASC LIMIT 1" );

		if ( $element_id != NULL ) :
			return $element_id;
		endif;

		$page_data = array(
			'post_date'			=> gmdate( 'Y-m-d H:i:s' ),
			'post_modified'		=> gmdate( 'Y-m-d H:i:s' ),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'page',
			'post_author' 		=> 1,
			'post_name' 		=> $slug,
			'post_title' 		=> $page_title,
			'post_content' 		=> $page_content,
			'comment_status' 	=> 'closed'
		);
		$wpdb->insert( $wpdb->posts , $page_data);
		$element_id = $wpdb->insert_id;

		//$element_id = wp_insert_post( $page_data );

		$wpdb->insert( $wpdb->prefix . "icl_translations", array(
				'element_type'			=> 'post_page',
				'element_id'			=> $element_id,
				'trid'					=> $trid,
				'language_code'			=> $lang_code,
				'source_language_code'	=> $source_lang_code,
			) );

		return $element_id;
	}

	public static function auto_create_page_for_wpml(  $original_id, $slug, $page_title = '', $page_content = '' ) {
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$active_languages = $sitepress->get_active_languages();
			if ( is_array($active_languages)  && count($active_languages) > 0 ) {
				$source_lang_code = $sitepress->get_default_language();
				$trid = $sitepress->get_element_trid( $original_id, 'post_page' );
				foreach ( $active_languages as $language ) {
					if ( $language['code'] == $source_lang_code ) continue;
					WC_Predictive_Search_Functions::create_page_wpml( $trid, $language['code'], $source_lang_code, $slug.'-'.$language['code'], $page_title.' '.$language['display_name'], $page_content );
				}
			}
		}
	}

	public static function get_page_id_from_option( $shortcode, $option ) {
		global $wpdb;
		global $wp_version;
		$page_id = get_option($option);

		if ( version_compare( $wp_version, '4.0', '<' ) ) {
			$shortcode = esc_sql( like_escape( $shortcode ) );
		} else {
			$shortcode = esc_sql( $wpdb->esc_like( $shortcode ) );
		}

		$page_data = null;
		if ( $page_id ) {
			$page_data = $wpdb->get_row( "SELECT ID FROM " . $wpdb->posts . " WHERE post_content LIKE '%[{$shortcode}]%' AND ID = '".$page_id."' AND post_type = 'page' LIMIT 1" );
		}
		if ( $page_data == null ) {
			$page_data = $wpdb->get_row( "SELECT ID FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%[{$shortcode}]%' AND `post_type` = 'page' ORDER BY post_date DESC LIMIT 1" );
		}

		$page_id = $page_data->ID;

		return $page_id;
	}

	public static function get_page_id_from_shortcode( $shortcode, $option ) {
		global $wpdb;

		$page_id = self::get_page_id_from_option( $shortcode, $option );

		// For WPML
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$translation_page_data = null;
			$trid = $sitepress->get_element_trid( $page_id, 'post_page' );
			if ( $trid ) {
				$translation_page_data = $wpdb->get_row( $wpdb->prepare( "SELECT element_id FROM " . $wpdb->prefix . "icl_translations WHERE trid = %d AND element_type='post_page' AND language_code = %s LIMIT 1", $trid , $sitepress->get_current_language() ) );
				if ( $translation_page_data != null )
					$page_id = $translation_page_data->element_id;
			}
		}

		return $page_id;
	}

	public static function add_query_vars( $aVars ) {
		$argument_vars = self::get_argument_vars();
		foreach ( $argument_vars as $avar ) {
			$aVars[] = $avar;
		}

		return $aVars;
	}

	public static function add_page_rewrite_rules( $aRules, $page_id ) {
		$search_page = get_page( $page_id );

		if ( ! empty( $search_page ) ) {

			$search_page_slug = $search_page->post_name;
			$argument_vars    = self::get_argument_vars();

			$rewrite_rule   = '';
			$original_url   = '';
			$number_matches = 0;
			foreach ( $argument_vars as $avar ) {
				$number_matches++;
				$rewrite_rule .= $avar.'/([^/]*)/';
				$original_url .= '&'.$avar.'=$matches['.$number_matches.']';
			}

			$aNewRules = array($search_page_slug.'/'.$rewrite_rule.'?$' => 'index.php?pagename='.$search_page_slug.$original_url);
			$aRules = $aNewRules + $aRules;

		}

		return $aRules;
	}

	public static function add_rewrite_rules( $aRules ) {
		global $wpdb;
		global $woocommerce_search_page_id;

		$shortcode   = 'woocommerce_search';
		$option_name = 'woocommerce_search_page_id';

		$page_id = $woocommerce_search_page_id;
		if ( empty( $page_id ) ) {
			$page_id = self::get_page_id_from_option( $shortcode, $option_name );
		}

		$aRules      = self::add_page_rewrite_rules( $aRules, $page_id );

		// For WPML
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$translation_page_data = null;
			$trid = $sitepress->get_element_trid( $page_id, 'post_page' );
			if ( $trid ) {
				$translation_page_data = $wpdb->get_results( $wpdb->prepare( "SELECT element_id FROM " . $wpdb->prefix . "icl_translations WHERE trid = %d AND element_type='post_page' AND element_id != %d", $trid , $page_id ) );
				if ( is_array( $translation_page_data ) && count( $translation_page_data ) > 0 ) {
					foreach( $translation_page_data as $translation_page ) {
						$aRules = self::add_page_rewrite_rules( $aRules, $translation_page->element_id );
					}
				}
			}
		}

		return $aRules;
	}

	public static function strip_shortcodes ($content='') {
		$content = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $content);

		return $content;
	}

	/**
	 * Get product price
	 */
	public static function get_product_price( $product ) {

		if ( ! is_object( $product ) ) {
			$product_id = absint( $product );

			$current_db_version = get_option( 'woocommerce_db_version', null );
			if ( version_compare( $current_db_version, '2.0', '<' ) && null !== $current_db_version ) {
				$product = new WC_Product( $product_id );
			} elseif ( version_compare( WC()->version, '2.2.0', '<' ) ) {
				$product = get_product( $product_id );
			} else {
				$product = wc_get_product( $product_id );
			}
		}

		$product_price_output = '';

		$product_price_output = $product->get_price_html();

		return $product_price_output;
	}

	/**
	 * Get product add to cart
	 */
	public static function get_product_addtocart( $product ) {

		if ( ! is_object( $product ) ) {
			$product_id = absint( $product );

			$current_db_version = get_option( 'woocommerce_db_version', null );
			if ( version_compare( $current_db_version, '2.0', '<' ) && null !== $current_db_version ) {
				$product = new WC_Product( $product_id );
			} elseif ( version_compare( WC()->version, '2.2.0', '<' ) ) {
				$product = get_product( $product_id );
			} else {
				$product = wc_get_product( $product_id );
			}
		}

		$product_addtocart_output = '';

		ob_start();
		if (function_exists('woocommerce_template_loop_add_to_cart') ) {
			add_filter( 'woocommerce_product_add_to_cart_url', array( 'WC_Predictive_Search_Functions', 'change_add_to_cart_url' ), 10, 2 );
			woocommerce_template_loop_add_to_cart();
			remove_filter( 'woocommerce_product_add_to_cart_url', array( 'WC_Predictive_Search_Functions', 'change_add_to_cart_url' ), 10, 2 );
		}
		$product_addtocart_output = ob_get_clean();

		return $product_addtocart_output;
	}

	/**
	 * Get product variation name
	 */
	public static function get_product_variation_name( $variation ) {

		if ( ! is_object( $variation ) ) {
			$variation_id = absint( $variation );

			$current_db_version = get_option( 'woocommerce_db_version', null );
			if ( version_compare( $current_db_version, '2.0', '<' ) && null !== $current_db_version ) {
				$variation = new WC_Product_Variation( $variation_id );
			} elseif ( version_compare( WC()->version, '2.2.0', '<' ) ) {
				$variation = get_product( $variation_id );
			} else {
				$variation = wc_get_product( $variation_id );
			}
		}

		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			$variation_data = $variation->get_variation_attributes();
			$description    = array();

			$attributes = $variation->parent->get_attributes();
			$return     = $variation->parent->get_title();

			if ( is_array( $variation_data ) ) {

				foreach ( $attributes as $attribute ) {

					// Only deal with attributes that are variations
					if ( ! $attribute[ 'is_variation' ] ) {
						continue;
					}

					$variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute[ 'name' ] ) ] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute[ 'name' ] ) ] : '';
					$description_name         = esc_html( wc_attribute_label( $attribute[ 'name' ] ) );
					$description_value        = '';

					// Get terms for attribute taxonomy or value if its a custom attribute
					if ( $attribute[ 'is_taxonomy' ] ) {

						$variation_id = $variation->id;

						$post_terms = wp_get_post_terms( $variation_id, $attribute[ 'name' ] );

						foreach ( $post_terms as $term ) {
							if ( $variation_selected_value === $term->slug ) {
								$description_value = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
							}
						}

					} else {

						$options = wc_get_text_attributes( $attribute[ 'value' ] );

						foreach ( $options as $option ) {

							if ( sanitize_title( $variation_selected_value ) === $variation_selected_value ) {
								if ( $variation_selected_value !== sanitize_title( $option ) ) {
									continue;
								}
							} else {
								if ( $variation_selected_value !== $option ) {
									continue;
								}
							}

							$description_value = esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) );
						}
					}

					if ( '' != $description_value ) {
						$description[] = rawurldecode( $description_value );
					}
				}

				if ( count( $description ) > 0 ) {
					$return .= ' ' . implode( ' ', $description );
				}
			}
		} else {
			$return = $variation->get_name();
		}

		return $return;
	}

	/**
	 * Get product add to cart
	 */
	public static function get_terms_object( $object_id, $taxonomy = 'product_cat', $post_parent = 0 ) {
		$terms_list = array();

		if ( (int) $post_parent > 0 ) {
			$object_id = (int) $post_parent;
		}

		$terms = get_the_terms( $object_id, $taxonomy );

		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $terms ) {
				$terms_list[] = array(
					'name'	=> $terms->name,
					'url'	=> get_term_link($terms->slug, $taxonomy )
				);
			}
		}

		return $terms_list;
	}

	/**
	 * Get product thumbnail url
	 */
	public static function get_product_thumbnail_url( $post_id, $post_parent = 0, $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0  ) {
		global $woocommerce;
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		$shop_catalog = ( ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) ? $woocommerce->get_image_size( 'shop_catalog' ) : wc_get_image_size( 'shop_catalog' ) );
		if ( is_array( $shop_catalog ) && isset( $shop_catalog['width'] ) && $placeholder_width == 0 ) {
			$placeholder_width = $shop_catalog['width'];
		}
		if ( is_array( $shop_catalog ) && isset( $shop_catalog['height'] ) && $placeholder_height == 0 ) {
			$placeholder_height = $shop_catalog['height'];
		}

		$mediumSRC = '';

		// Return Feature Image URL
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbid = get_post_thumbnail_id( $post_id );
			$attachmentArray = wp_get_attachment_image_src( $thumbid, $size, false );
			if ( $attachmentArray ) {
				$mediumSRC = $attachmentArray[0];
				if ( trim( $mediumSRC ) != '' ) {
					return $mediumSRC;
				}
			}
		}

		// Return First Image URL in gallery of this product
		if ( $post_parent == 0 && trim( $mediumSRC ) == '' ) {
			$args = array( 'post_parent' => $post_id , 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => null );
			$attachments = get_posts( $args );
			if ( $attachments ) {
				foreach ( $attachments as $attachment ) {
					$attachmentArray = wp_get_attachment_image_src( $attachment->ID, $size, false );
					if ( $attachmentArray ) {
						$mediumSRC = $attachmentArray[0];
						if ( trim( $mediumSRC ) != '' ) {
							return $mediumSRC;
						}
					}
				}
			}
		}

		// Ger Image URL of parent product
		if ( $post_parent > 0 && trim( $mediumSRC ) == '' ) {

			// Set ID of parent product if one exists
			$post_id = $post_parent;

			if ( has_post_thumbnail( $post_id ) ) {
				$thumbid = get_post_thumbnail_id( $post_id );
				$attachmentArray = wp_get_attachment_image_src( $thumbid, $size, false );
				if ( $attachmentArray ) {
					$mediumSRC = $attachmentArray[0];
					if ( trim( $mediumSRC ) != '' ) {
						return $mediumSRC;
					}
				}
			}

			if ( trim( $mediumSRC ) == '' ) {
				$args = array( 'post_parent' => $post_id , 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => null );
				$attachments = get_posts( $args );
				if ( $attachments ) {
					foreach ( $attachments as $attachment ) {
						$attachmentArray = wp_get_attachment_image_src( $attachment->ID, $size, false );
						if ( $attachmentArray ) {
							$mediumSRC = $attachmentArray[0];
							if ( trim( $mediumSRC ) != '' ) {
								return $mediumSRC;
							}
						}
					}
				}
			}
		}

		// Use place holder image of Woo
		if ( trim( $mediumSRC ) == '' ) {
			$mediumSRC = ( ( version_compare( $woocommerce_db_version, '2.1', '<' ) && null !== $woocommerce_db_version ) ? woocommerce_placeholder_img_src() : wc_placeholder_img_src() );
		}

		return $mediumSRC;
	}

	public static function change_add_to_cart_url( $url, $product ) {
		
		if ( $product->is_type( 'simple' ) || $product->is_type( 'subscription' ) ) {
			$url = $product->is_purchasable() && $product->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $product->get_id(), get_permalink( $product->get_id() ) ) ) : get_permalink( $product->get_id() );
		} elseif ( $product->is_type( 'variation' ) ) {
			$variation_id = $product->get_id();
			if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
				$parent_id = $product->id;
			} else {
				$parent_id = $product->get_parent_id();
			}
			$variation_data = array_map( 'urlencode', $product->get_variation_attributes() );
			$url            = $product->is_purchasable() && $product->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( array_merge( array( 'variation_id' => $variation_id, 'add-to-cart' => $parent_id ), $variation_data ), get_permalink( $parent_id ) ) ) : get_permalink( $parent_id );
		}

		return $url;
	}
}
?>