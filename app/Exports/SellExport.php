<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SellExport implements FromArray, WithStyles
{
    protected $data;

    // Use dependency injection for SellService
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $view_excel_data = [];
        
        foreach($this->data as $order) {
            $view_excel_data[] = ['', '', ''];
            $view_excel_data[] = [__('text.reference'), $order['reference']];
            $view_excel_data[] = [__('excel.order_date'), $order['order_date']];
            $view_excel_data[] = [__('excel.customer_name'), $order['contact_name']];
            $view_excel_data[] = [__('excel.customer_email'), $order['contact_email']];
            $view_excel_data[] = ['', '', ''];
            $view_excel_data[] = [
                __('excel.no'), 
                __('excel.product_sub_type'), 
                __('text.category'), 
                __('text.name'), 
                __('text.code'), 
                __('text.size'), 
                __('excel.carton'), 
                __('excel.packing'), 
                __('text.qty'), 
                __('text.unit'), 
                __('excel.sale_price_without_vat'), 
                __('excel.total_price_without_vat'), 
                __('text.vat'), 
                __('excel.total_price_with_vat'), 
                $order['discount_type'] == 'peritem' ? __('text.discount') : ''
            ];
            $total_carton = 0;
            $total_pair = 0;
            $total_sale_price_without_vat = 0;
            $total_sale_price_with_vat = 0;
            $total_sale_discount = $order['total_discount'];
            foreach($order['stock_items'] as $index => $item) {
                $total_carton += $item['base_unit_name'] == 'karton' ? $item['item_base_quantity'] : $item['item_converted_quantity'];
                $total_pair += $item['base_unit_name'] == 'para' ? $item['item_base_quantity'] : $item['item_converted_quantity'];
                $total_sale_price_without_vat += $item['sale_price'] * $item['item_base_quantity'];
                $total_sale_price_with_vat += $item['sale_price'] * $item['item_base_quantity'] * (1 - $item['item_vat'] / 100);
                $view_excel_data[] = [
                    $index + 1, 
                    $item['item_subtype'], 
                    $item['item_category'], 
                    $item['item_name'], 
                    $item['item_code'], 
                    $item['item_size'],
                    $item['base_unit_name'] == 'karton' ? $item['item_base_quantity'] . " " . $item['base_unit_name'] : $item['item_converted_quantity'] . " "  . $item['converted_unit_name'], 
                    $item['item_unitconverter'], 
                    $item['base_unit_name'] != 'karton' ? $item['item_base_quantity'] . " " . $item['base_unit_name'] : $item['item_converted_quantity'] . " "  . $item['converted_unit_name'], 
                    // $item['item_converted_quantity'] . " "  . $item['converted_unit_name'], 
                    $item['base_unit_name'], 
                    $item['sale_price'] .__('text.PLN'), 
                    $item['sale_price'] * $item['item_base_quantity'] .__('text.PLN'), 
                    $item['item_vat'] . "%", 
                    $item['sale_price'] * $item['item_base_quantity'] * (1 - $item['item_vat'] / 100) . __('text.PLN'),
                    $order['discount_type'] == 'peritem' ? $item['discount'] . "PLN" : '', 
                ];
            }
            $view_excel_data[] = ['', ''];
            $view_excel_data[] = ['', '', '', '', '', __('excel.total'), $total_carton . " carton", '', $total_pair . " pair", '', ''];
            $view_excel_data[] = ['', '', '', '', '', __('excel.total_price_without_vat'), $total_sale_price_without_vat . __('text.PLN')];
            $view_excel_data[] = ['', '', '', '', '', __('excel.total_vat'), $total_sale_price_without_vat - $total_sale_price_with_vat . __('text.PLN')];
            $view_excel_data[] = ['', '', '', '', '', __('excel.total_price_with_vat'), $total_sale_price_with_vat . __('text.PLN')];
            $view_excel_data[] = ['', '', '', '', '', __('excel.total_discount'), $total_sale_discount . __('text.PLN')];
            $view_excel_data[] = ['', ''];
        }
        return $view_excel_data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold and merge some header cells
            // 1 => ['font' => ['bold' => true]], // Bold the first row
            // 7 => ['font' => ['bold' => true]], // Bold the product header row
            // 13 => ['font' => ['bold' => true]], // Bold the TOTAL row
        ];
    }
}
