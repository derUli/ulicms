<?php

// this module demonstrates the new "Custom Fields" feature of UliCMS 2018.1
class CustomTypesExample extends Controller {

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

        $field6 = new DatetimeField ();
        $field6->name = "date";
        $field6->title = "date";
        $field6->defaultValue = date("Y-m-d\TH:i:s");
        $product->customFields [] = $field6;

        $field7 = new NumberField ();
        $field7->name = "storage_amount";
        $field7->title = "storage_amount";
        $field7->defaultValue = 159;
        $product->customFields [] = $field7;

        $field8 = new ColorField ();
        $field8->name = "product_color";
        $field8->title = "product_color";
        $field8->defaultValue = "#69B9FF";
        $product->customFields [] = $field8;

        $field9 = new HtmlField ();
        $field9->name = "html_field";
        $field9->title = "html_editor";
        $product->customFields [] = $field9;

        $field10 = new SelectField ();
        $field10->name = "product_available";
        $field10->title = "product_available";
        $field10->translateOptions = false;
        $field10->options = array(
            "enabled" => get_translation("enabled"),
            "disabled" => get_translation("disabled")
        );

        $product->customFields [] = $field10;

        $field11 = new MultiSelectField ();
        $field11->name = "zip_codes";
        $field11->title = "zip_codes";
        $field11->helpText = "hold_ctrl_to_select_multiple";
        $field11->translateOptions = false;
        $field11->options = array(
            "38102" => "Braunschweig",
            "38104" => "Gliesmarode",
            "38124" => "Heidburg",
            "38100" => "Innenstadt",
            "38116" => "Kanzlerfeld"
        );
        $product->customFields [] = $field11;
        $field12 = new CheckboxField ();
        $field12->name = "product_locked";
        $field12->title = "locked";
        $product->customFields [] = $field12;

        $field13 = new FileImage ();
        $field13->name = "my_image";
        $field13->title = "my_image";
        $field13->defaultValue = "";
        $product->customFields [] = $field13;

        $field14 = new FileFile ();
        $field14->name = "my_file";
        $field14->title = "my_file";
        $field14->defaultValue = "";
        $product->customFields [] = $field14;

        $types ["product"] = $product;
        return $types;
    }

}
