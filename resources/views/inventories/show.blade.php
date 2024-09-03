@extends('layouts.admin')
@section('content')
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>View GRN</title>
    <!------ Include the above in your HEAD tag ---------->

    <head>

        <style>
            #invoice {
                padding: 30px;
            }

            .invoice {
                position: relative;
                background-color: #FFF;
                min-height: 680px;
                padding: 15px
            }

            .invoice header {
                padding: 10px 0;
                margin-bottom: 20px;
                border-bottom: 1px solid #3989c6
            }

            .invoice .company-details {
                text-align: right;
            }

            .invoice .company-details .name {
                margin-top: 0;
                margin-bottom: 0
            }

            .invoice .contacts {
                margin-bottom: 20px
            }

            .invoice .invoice-to {
                text-align: left
            }

            .invoice .invoice-to .to {
                margin-top: 0;
                margin-bottom: 0
            }

            .invoice .invoice-details {
                text-align: right
            }

            .invoice .invoice-details .invoice-id {
                margin-top: 0;
                color: #3989c6
            }

            .invoice main {
                padding-bottom: 50px
            }

            .invoice main .thanks {
                margin-top: -100px;
                font-size: 2em;
                margin-bottom: 50px
            }

            .invoice main .notices {
                padding-left: 6px;
                border-left: 6px solid #3989c6
            }

            .invoice main .notices .notice {
                font-size: 1.2em
            }

            .invoice table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                margin-bottom: 20px
            }

            .def td,
            .def th {
                padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff
            }

            .invoice table th {
                white-space: nowrap;
                font-weight: 400;
                font-size: 16px
            }

            .invoice table td h3 {
                margin: 0;
                font-weight: 400;
                color: #3989c6;
                font-size: 1.2em
            }

            .invoice table .qty,
            .invoice table .total,
            .invoice table .unit {
                text-align: right;
                font-size: 1.2em
            }

            .invoice table .no {
                color: #fff;
                font-size: 1.6em;
                background: #3989c6
            }

            .invoice table .unit {
                background: #ddd
            }

            .invoice table .total {
                background: #3989c6;
                color: #fff
            }

            .invoice table tbody tr:last-child td {
                border: none
            }

            .invoice table tfoot td {
                background: 0 0;
                border-bottom: none;
                white-space: nowrap;
                text-align: right;
                padding: 10px 20px;
                font-size: 1.2em;
                border-top: 1px solid #aaa
            }

            .invoice table tfoot tr:first-child td {
                border-top: none
            }

            .invoice table tfoot tr:last-child td {
                color: #3989c6;
                font-size: 1.4em;
                border-top: 1px solid #3989c6
            }

            .invoice table tfoot tr td:first-child {
                border: none
            }

            .invoice footer {
                width: 100%;
                text-align: center;
                color: #777;
                border-top: 1px solid #aaa;
                padding: 8px 0
            }

            @media print {
                .invoice {
                    font-size: 11px !important;
                    overflow: hidden !important
                }

                .invoice footer {
                    position: absolute;
                    bottom: 10px;
                    page-break-after: always
                }

                /* .invoice div:last-child {
                                    page-break-before: always
                                } */
            }

            @media print {

                #approvebtn,
                #backbtn,
                #printbtn {
                    display: none;
                }
            }
        </style>

    </head>
    <div id="invoice">
        <div class="card-header">
            <a href="#" id="printbtn" onclick="window.print()" class="float-left"> <img
                src="{{ asset('assets/images/icons/printer.png') }}" style="width:30px; height:30px;"></a>
            <a href="{{ route('inventories.index') }}" id="printbtn" class="btn btn-primary float-right">Back</a>
        </div>

        <div class="invoice overflow-auto">
            <div style="min-width: 600px">
                <header>
                    <div class="row">
                        <div class="col">
                            <a target="_blank">
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/images/company/company-1.png'))) }}"
                                    style="height: 80px; width: 220px;" alt="company image" data-holder-rendered="true" />
                            </a>
                            <br>
                        
                            <p style="font-size: 1.6em;">Goods Received Notes (GRN)</p>
                        </div>
                        <div class="col company-details">
                            <h2 class="name">
                                <a href="#" style="text-decoration:none;">
                                    {{ $company->name ?? '' }}
                                </a>
                            </h2>
                            <div> {{ $company->address ?? '' }}</div>
                            <div> {{ $company->phone ?? '' }}</div>
                            <div> {{ $company->email ?? '' }}</div>
                        </div>
                    </div>
                </header>
                <header>
                    <div class="row">

                        <div class="col">
                            <table border="0" cellspacing="0" cellpadding="0"
                            style="width:410px; height:10%; font-size:12px;">
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    GRN Number:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    {{ $inventory->grn_number ?? '' }}</th>
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Invoice Number:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    {{ $inventory->invoice_number ?? '' }}</th>
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Date:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    {{ date('d-m-Y H:i', strtotime($inventory->created_at)) }}</th>
                            </tr>
                            {{-- <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Supplied To:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    {{ $inventory->supplied_to ?? ' ' }}</th>
                            </tr> --}}
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Exchange Rate:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    {{ $inventory->exchange_rate ?? '' }}</th>
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Waybill:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    {{ $inventory->waybill ?? '' }}</th>
                            </tr>
                            
                        </table>

                    </div>
                    <div style="margin-right: 50px;">

                        <table style="width:450px; font-size:12px;">
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Supplier:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    {{ $inventory->supplier->name ?? 'not set' }}</th>
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Enduser:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    {{ $inventory->enduser->asset_staff_id ?? 'not set' }}</th>
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Billing Currency:</th>
                              
                                    <th
                                        style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;background-color:white;">
                                        {{ $inventory->billing_currency ?? 'Dollar' }}</th>
                               
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                  PO Number:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    {{ $inventory->po_number ?? 'not set' }}</th>
                            </tr>

                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Type of Purchase:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    {{ $inventory->trans_type ?? 'not set' }}</th>
                            </tr>
                            
                        </table>
                        </div>
                    </div>
                </header>
                <main>

                    <table border="0" cellspacing="0" cellpadding="0" class="def">
                        <thead>
                            <tr>
                                {{-- <th>#</th> --}}
                                <th>Description</th>
                                <th>UoM</th>
                                <th>Part Number</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Amount</th>
                                <th>Discount</th>
                                <th>Location</th>


                            </tr>
                        </thead>
                        @php
                        $totalAmount = 0;
                    @endphp

                        @forelse ($inventories as $ph)
                        @php
                        $amount = $ph->amount ?? 0;
                        $totalAmount += $amount;
                    @endphp
                            <tbody>
                                <tr>
                                    {{-- <td>{{ $ph->id }}</td> --}}
                                    <td>{{ $ph->item->item_description ?? '' }}</td>
                                    <td>{{ $ph->item->item_uom ?? '' }}</td>
                                    <td>{{ $ph->item->item_part_number ?? '' }}</td>
                                    <td>{{ $ph->quantity ?? '' }}</td>

                                    <td>
                                        @if ($inventory->billing_currency == 'Dollar')
                                            {{ '$ ' . $ph->unit_cost_exc_vat_gh ?? '' }}
                                        @elseif ($inventory->billing_currency == 'Cedi')
                                            {{ 'GHC ' . $ph->unit_cost_exc_vat_gh ?? '' }}
                                        @else
                                            {{ $ph->unit_cost_exc_vat_gh ?? '' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($inventory->billing_currency == 'Dollar')
                                            {{ '$ ' . $ph->amount ?? '' }}
                                        @elseif ($inventory->billing_currency == 'Cedi')
                                            {{ 'GHC ' . $ph->amount ?? '' }}
                                        @else
                                            {{ $ph->amount ?? '' }}
                                        @endif
                                    </td>

                                    <td>

                                        {{ $ph->discount ?? '' }}
                                    </td>
                                    <td>{{ $ph->location->name ?? '' }}</td>
                                @empty
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                        @endforelse

                        </tr>
                        </tbody>

                    </table>
                    <div class="float-right mt-3">
                        <strong>Total Amount: 
                          
                          
                                ${{ $totalAmount }}
                           
                        </strong>
                    </div>

                    <div class="float-left mt-9">
                        <strong>Remarks:</strong> <br>
                        {{ $inventory->manual_remarks ?? ''}}  <br> <br>
                        <strong>Received in Good Condition By:   </strong> <br>
                            {{ $inventory->user->name ?? '' }} , {{ $inventory->created_at ?? ''}}  
                      <br>   <br>  <strong> Site Name:  </strong> <br>
                          {{ $inventory->user->site->name ?? '' }}
                          
   
                      
                    </div>

                </main>
            </div>
            <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
            <div></div>
        </div>
    </div>
    <script>
        $('#printInvoice').click(function() {
            Popup($('.invoice')[0].outerHTML);

            function Popup(data) {
                window.print();
                return true;
            }
        });
    </script>
@endsection
