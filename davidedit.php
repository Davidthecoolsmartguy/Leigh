<?php

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );


class W3ExDAVIDEDITView{
	
	private static $ins = null;
	private $attributes      = array();
	private $attributes_asoc = array();
	private $variations_fields = array();
	private $categories = array();
	private $cat_asoc = array();
	
	public static function lang_category_id($id){
	  if(function_exists('icl_object_id')) {
	    return icl_object_id($id,'category',true);
	  } else {
	    return $id;
	  }
	}
	
    public static function init()
    {
       self::instance()->_main();
    }

    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }
	
	public function mb_ucfirst($p_str)
	{
		if (function_exists('mb_substr') && function_exists('mb_strtoupper') && function_exists('mb_strlen')) 
		{
			$string = $p_str;
			if(mb_strlen($p_str) > 0)
			{
			    $string = mb_strtoupper(mb_substr($p_str, 0, 1)) . mb_substr($p_str, 1);
			}
		    return $string;
		}else
		{
			return ucfirst($p_str);
		}
	}
	
	public function loadAttributes()
	{
		//categories
		$args = array(
		    'number'     => 99999,
		    'orderby'    => 'slug',
		    'order'      => 'ASC',
		    'hide_empty' => false,
		    'include'    => '',
			'fields'     => 'all'
		);

		$woo_categories = get_terms( 'product_cat', $args );

		foreach($woo_categories as $category){
		   if(!is_object($category)) continue;
		   if(!property_exists($category,'term_taxonomy_id')) continue;
		    if(!property_exists($category,'term_id')) continue;
		   $cat = new stdClass();
		   $cat->category_id     = $category->term_taxonomy_id;
//		   $id = self::lang_category_id($cat->category_id);
		   $cat->term_id         = $category->term_id;
		   $cat->category_name   = $category->name;
		   $cat->category_slug   = urldecode($category->slug);
		   $cat->category_parent = $category->parent;
		   $this->categories[] = $cat;   
		   $this->cat_asoc[$cat->category_id] = $cat;
		};
		
		$curr_settings = get_option('w3exabe_settings');
		if(is_array($curr_settings))
		{
			if(isset($curr_settings['disattributes']))
			{
				if($curr_settings['disattributes'] == 1)
					return;
			}
		}
	    global $wpdb;
		
		$woo_attrs = $wpdb->get_results("select * from " . $wpdb->prefix . "woocommerce_attribute_taxonomies",ARRAY_A);
		$counter = 0;
//		foreach($woo_attrs as $attr){
			
		foreach($woo_attrs as $attr){
//			if($counter > 15)
//				return;
			$counter++;
			$att         = new stdClass();
			$att->id     = $attr['attribute_id'];
			$att->name   = $attr['attribute_name'];  
			$att->label  = $attr['attribute_label']; 
			if(!$att->label)
				$att->label = ucfirst($att->name);
			$att->type   = $attr['attribute_type'];

		  
			$att->values = array();
			$values     = get_terms( 'pa_' . $att->name, array('hide_empty' => false));
			foreach($values as $val){
				if(!is_object($val)) continue;
				if(!property_exists($val,'term_taxonomy_id')) continue;
				$value          = new stdClass();
				$value->id      = $val->term_taxonomy_id;
				$value->term_id      = $val->term_id;
				$value->slug    = $val->slug;
				$value->name    = $val->name;
				$value->parent  = $val->parent;
				$att->values[]  = $value;
			}
			
		 	if(count($att->values) > 0)
			{
				$this->attributes[]                = $att;
				$this->attributes_asoc[$att->name] = $att;
				$this->variations_fields[] = 'pattribute_'.$att->id;
			}
		}
	}

	

					<td class="tdbulkvalue">
						<div class="imgButton sm mapto">
					    </div>
						<textarea id="bulkpost_namevalue" rows="1" cols="15" data-id="post_name" class="bulkvalue" placeholder="Skipped (empty)"></textarea>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <textarea class="inputwithvalue" rows="1" cols="15"></textarea></div>
					</td>
				</tr>
				<tr data-id="_sku">
					<td>
						<?php echo $arrTranslated['_sku']; ?>
					</td>
					<td>
						 <select id="bulk_sku" class="bulkselect">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td class="tdbulkvalue">
						<div class="imgButton sm mapto">
					    </div>
						<input id="bulk_skuvalue" type="text" data-id="_sku" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <input class="inputwithvalue" type="text"></div>
					</td>
				</tr>
				<tr data-id="product_cat">
					<td>
						<input id="setproduct_cat" type="checkbox" class="bulkset" data-id="product_cat" data-type="customtaxh"><label for="setproduct_cat"><?php echo $arrTranslated['product_cat']; ?></label>
					</td>
					<td>
						
						</select>
					</td>
									<tr data-id="product_shipping_class">
					<td>
						<input id="setproduct_shipping_class" type="checkbox" class="bulkset" data-id="product_shipping_class" data-type="customtaxh"><label for="setproduct_shipping_class"><?php echo $arrTranslated['product_shipping_class']; ?></label>
					</td>
					<td>
						
					</td>
					<td class="nontextnumbertd">
						 <select id="bulkproduct_shipping_class" class="makechosen catselset" style="width:250px;" data-placeholder="select">
						 <option value="">none</option>
						<?php
							//shipping class
						$args = array(
						    'number'     => 99999,
						    'orderby'    => 'slug',
						    'order'      => 'ASC',
						    'hide_empty' => false,
						    'include'    => '',
							'fields'     => 'all'
						);

						$woo_categories = get_terms( 'product_shipping_class', $args );
						foreach($woo_categories as $category){
						    if(!is_object($category)) continue;
						    if(!property_exists($category,'name')) continue;
						    if(!property_exists($category,'term_id')) continue;
						   	echo '<option value="'.$category->term_id.'" >'.$category->name.'</option>';
						};
						?>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
						<input id="ddownfiles" class="dsettings" data-id="_downloadable_files" type="checkbox"><label for="ddownfiles"> <?php echo $arrTranslated['_downloadable_files']; ?></label>
					</td>
					<td>
						<div>
						 <img id="ddownfiles_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="ddowntype" class="dsettings" data-id="_download_type" type="checkbox"><label for="ddowntype"> <?php echo $arrTranslated['_download_type']; ?></label>
					</td>
					<td>
						<div>
						 <img id="ddowntype_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="d_product_url" class="dsettings" data-id="_product_url" type="checkbox"><label for="d_product_url"> <?php echo $arrTranslated['_product_url']; ?></label>
					</td>
					<td>
						<div>
						 <img id="d_product_url_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="d_button_text" class="dsettings" data-id="_button_text" type="checkbox"><label for="d_button_text"> <?php echo $arrTranslated['_button_text']; ?></label>
					</td>
					<td>
						<div>
						 <img id="d_button_text_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dproduct_type" class="dsettings" data-id="product_type" type="checkbox"><label for="dproduct_type"> <?php echo $arrTranslated['product_type']; ?></label>
					</td>
					<td>
						<div>
						 <img id="dproduct_type_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				
			?>
			</table><br />
			 <button id="addcustomfield" class="button"><?php _e( 'Add Custom Field', 'woocommerce-DAVIDEDIT'); ?></button>
		</div>
		<div id="findcustomfieldsdialog">
			 <br /><br />
			 <?php _e('Find custom fields by product ID','woocommerce-DAVIDEDIT'); ?>:<input id="productid" type="text"/><button id="findcustomfield" class="button"><?php _e('Find','woocommerce-DAVIDEDIT'); ?></button> &nbsp;&nbsp;<?php _e('OR','woocommerce-DAVIDEDIT'); ?>&nbsp;&nbsp; 
			 <button id="findcustomtaxonomies" class="button"><?php _e('Find Taxonomies','woocommerce-DAVIDEDIT'); ?></button>
			 <br /><br /><br />
			 <table cellpadding="25" cellspacing="0" class="tablecustomfields">
			</table>
		</div>
		<div id="debuginfo"></div>
			<iframe id="exportiframe" width="0" height="0">
  			</iframe>
		<div id="editorcontainer">
			 <?php
				 $settings = array( 'textarea_name' => 'post_text' );//,'wpautop' => false,'tinymce' => array('forced_root_block' => true,'convert_newlines_to_brs' => false));
				 wp_editor("", "editorid",$settings );
			 ?>
			<textarea style="display:none;" name="post_text" id="editorid" rows="3"></textarea>
			<DIV style='text-align:right' id="savewordpeditor"><BUTTON>Save</BUTTON><BUTTON id="cancelwordpeditor">Cancel</BUTTON></DIV>
			</div>
		</div>
		<?php
	}
	
	
    public function _main()
    {
		$this->showMainPage();
    }
}

W3ExDAVIDEDITView::init();
