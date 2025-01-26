<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CheckinExport implements FromArray, WithStyles
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
            $view_excel_data[] = [__('excel.purchase_date'), $order['checkin_date']];
            $view_excel_data[] = [__('excel.supplier_name'), $order['contact_name']];
            $view_excel_data[] = [__('excel.supplier_email'), $order['contact_email']];
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
                __('text.price'), 
                __('text.total_price'), 
            ];
            $total_carton = 0;
            $total_pair = 0;
            $total_price = 0;
            foreach($order['stock_items'] as $index => $item) {
                $total_carton += $item['base_unit_name'] == 'karton' ? $item['item_base_quantity'] : $item['item_converted_quantity'];
                $total_pair += $item['base_unit_name'] == 'para' ? $item['item_base_quantity'] : $item['item_converted_quantity'];
                $total_price += $item['price'] * $item['item_base_quantity'];
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
                    $item['price'] . __('text.PLN'), 
                    $item['price'] * $item['item_base_quantity'] . __('text.PLN'), 
                ];
            }
            $view_excel_data[] = ['', ''];
            $view_excel_data[] = ['', '', '', '', '', __('excel.total'), $total_carton . " carton", '', $total_pair . " pair", '', ''];
            $view_excel_data[] = ['', '', '', '', '', __('excel.total_price'), $total_price . __('text.PLN')];
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
