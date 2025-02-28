<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Print</title>
    <link rel="stylesheet" href="{{ asset('public/css/bootstrap.min.css') }}" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        @media print {
            @page {
                margin: 10mm;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 95%;
                margin: 0 auto;
                padding: 10px;
                page-break-after: always; /* New page for each order */
            }
            .row {
                margin: 0;
            }
            .order {
                page-break-after: always; /* Ensures each order starts on a new page */
            }

            /* Optional: Hide elements that shouldn't appear in print */
            .no-print {
                display: none;
            }
            .noline {
                white-space: nowrap;
            }
            td{
                text-align: center;
            }

        }
    </style>
</head>
<body>
    @foreach($data as $order)
    <div class="container order" style="margin-top: 10px; padding: 10px 0px;max-width: 100%;">
        {{-- {{ $data }} --}}
        <div class="row">
            <div class="col-6">
                <div class="logo-wrapper">
                    <img width="100" src="{{ asset('public/storage/settings/').'/'.$setting->logo }}" class="img-fluid" />
                </div>
            </div>
            <div class="col-3" >
                <h5 class="">{{__('excel.order_date')}}</h5>
                <h5 class="">{{__('excel.order_time')}}</h5>
                <h5 class="">{{__('excel.sale_person')}}</h5>
            </div>
            <div class="col-3" >
                <h5 class="">{{date('d/m/Y')}}</h5>
                <h5 class="">{{date('H:i:s')}}</h5>
                <h5 class="">{{$order['creator']}}</h5>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-9">
                <h5 class="">{{__('excel.email')}} {{$setting->company_email}}</h5>
            </div>
            <div class="col-3 " >
                @if($order['description'] != '')
                <span >Uwagi:{{ $order['description'] }}</span>
                @endif
            </div>
        </div>
        <div class="row mt-3" style="padding:auto 0px">
            <div class="col-12">
                <h2 class="text-center">{{__('excel.order_number')}} {{$order['show_reference']}}</h2>
            </div>
            <div class="col-12" style="padding:auto 0px">
                <table class="table table-bordered" style="padding:auto 0px">
                    <thead>
                        <tr style="font-size: 16px;">
                            <th>{{__('excel.no')}}</th>
                            <th>{{__('excel.product_sub_type')}}</th>
                            <th>k</th>
                            {{-- <th>{{__('text.category')}}</th> --}}
                            <th>Name{{--{{__('text.code')}}--}}</th>
                            <th>{{__('text.size')}}</th>
                            <th>{{__('excel.carton')}}</th>
                            <th>{{__('excel.packing')}}</th>
                            <th>{{__('text.qty')}}</th>
                            <th>{{__('text.unit_short')}}</th>
                            <th>{{__('excel.sale_price_without_vat')}}</th>
                            <th>{{__('excel.total_price_without_vat')}}</th>
                            <th>{{__('text.discount')}}</th>
                            <!-- <th>{{__('excel.sale_price_without_vat')}}</th> -->

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $total_carton = 0;
                            $total_pair = 0;
                            $total_sale_price_without_vat = 0;
                            $total_sale_price_with_vat = 0;
                            $total_sale_discount = $order['total_discount'];
                        ?>
                        @foreach($order['stock_items'] as $index => $item)

                            <?php
                                $total_carton += $item['base_unit_name'] == 'karton' ? $item['item_base_quantity'] : $item['item_converted_quantity'];
                                $total_pair += $item['base_unit_name'] == 'para' ? $item['item_base_quantity'] : $item['item_converted_quantity'];
                                $item_price = $item['sale_price'] + ($order['discount_type'] == 'peritem' ?$item['discount'] : 0);
                                $item_price_with_total = $item['sale_price'];
                                $total_sale_price_without_vat += $item_price_with_total * $item['item_converted_quantity'];
                                $total_sale_price_with_vat += $item_price_with_total * $item['item_converted_quantity'] * (1 + $item['item_vat'] / 100);
                            ?>
                            <tr style="font-size: 16px">
                                <td>{{$index + 1}}</td>
                                <td>{{$item['item_subtype']}}</td>
                                <td>{{ $item['stock_qty'] == 0 ? 'K':'' }}</td>
                                {{-- <td>{{$item['item_category']}}</td> --}}
                                <td>{{$item['item_name']}}</td>
                                <td class="noline">{{$item['item_size']}}</td>
                                <td class="text-center">{{$item['base_unit_name'] == 'karton' ? $item['item_base_quantity'] : $item['item_converted_quantity']}}</td>
                                <td class="text-center">{{$item['item_unitconverter']}}</td>
                                <td class="text-center">{{$item['base_unit_name'] != 'karton' ? $item['item_base_quantity'] : $item['item_converted_quantity']}}</td>
                                <td >Par</td>
                                {{-- <td >{{$item['base_unit_name']}}</td> --}}
                                <td class="text-center">{{ $item_price }}</td>
                                <td class="text-center">{{ ($item_price * $item['item_converted_quantity']  )}}</td>
                                <td class="text-center">{{ $order['discount_type'] == 'peritem' ?format_price($item['discount']) : ''}}</td>
                            </tr>
                        @endforeach
                        <?php
                            $total_sale_price_without_vat += $total_sale_discount;
                            // $total_sale_price_with_vat += $total_sale_discount;
                            $vat_test = $total_sale_price_without_vat*0.23;
                            $total_sale_price_with_vat = $vat_test + $total_sale_price_without_vat;
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="font-size: 16px">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Razem</th>
                            <th class="text-right">{{$total_carton}}</th>
                            <th>Opk</th>
                            <th class="text-right">{{$total_pair}}</th>
                            <th>par</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-6"></div>
            <div class="col-6">
                <table class="table table-bordered">
                    <tbody style="font-size: 16px">
                        @if($order['discount_type'] == 'peritem')
                            <tr>
                                <th>{{__('excel.total_price_with_vat_after_discount')}} </th>
                                <td class="text-right noline">{{ format_price($total_sale_price_without_vat) }} {{ __('text.PLN') }}</td>
                            </tr>
                        @else
                            <tr>
                                <th>{{__('excel.total_price_without_vat')}}</th>
                                <td class="text-right noline">{{ format_price($total_sale_price_without_vat - $total_sale_discount) }} {{ __('text.PLN') }}</td>
                            </tr>
                            <tr>
                                <th>{{__('excel.total_discount')}}</th>
                                <td class="text-right noline">{{ format_price($total_sale_discount) }} {{ __('text.PLN') }}</td>
                            </tr>
                            <tr>
                                <th>{{__('excel.total_price_with_vat_after_discount')}} </th>
                                <td class="text-right noline">{{ format_price($total_sale_price_without_vat) }} {{ __('text.PLN') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>{{__('excel.total_vat')}}</th>
                            {{-- <td class="text-right noline">{{ format_price($total_sale_price_with_vat - $total_sale_price_without_vat) }} {{ __('text.PLN') }}</td> --}}
                            <td class="text-right noline">{{ format_price($vat_test) }} {{ __('text.PLN') }}</td>
                        </tr>
                        <tr>
                            <th>{{__('excel.total_price_with_vat')}}</th>
                            <td class="text-right noline">{{ format_price($total_sale_price_with_vat) }} {{ __('text.PLN') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</body>
</html>
