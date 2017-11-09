<?php
class ProductContentType extends Controller {
	public function contentTypesFilter($types) {
		$product = clone $types ["page"];
		$product->customFieldTabTitle = "product_properties";
		$field = new TextField ();
		$field->name = "product_price";
		$field->htmlAttributes = [ 
				"style" => "border:green 1px solid" 
		];
		$field->required = true;
		$field->title = "product_price";
		$field->helpText = "excluding_vat";
		$field->defaultValue = "0.00";
		$product->customFields [] = $field;
		
		$field2 = new TextField ();
		$field2->name = "special_tax";
		$field2->title = "special_tax";
		$field2->defaultValue = "";
		
		$product->customFields [] = $field2;
		$types ["product"] = $product;
		return $types;
	}
}