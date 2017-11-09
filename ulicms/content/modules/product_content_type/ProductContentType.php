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
		
		$field3 = new MultilineTextField ();
		$field3->name = "teaser_text";
		$field3->title = "teaser_text";
		$field3->defaultValue = "";
		$product->customFields [] = $field3;
		
		$field4 = new EmailField ();
		$field4->name = "customer_service";
		$field4->title = "customer_service";
		$field4->defaultValue = "service@company.de";
		$product->customFields [] = $field4;
		
		$field5 = new MonthField ();
		$field5->name = "available_until";
		$field5->title = "available_until";
		$field5->defaultValue = "2019-12";
		$product->customFields [] = $field5;
		
		$types ["product"] = $product;
		return $types;
	}
}