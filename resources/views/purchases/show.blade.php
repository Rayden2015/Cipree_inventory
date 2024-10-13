    @extends('layouts.admin')
    @section('content')

        {{-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
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
                    text-align: right
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

                .invoice table td,
                .invoice table th {
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

                    .invoice>div:last-child {
                        page-break-before: always
                    }

                }

                @media print {
                    #printhide {
                        display: none;
                    }
                }

                .container1 {
                    position: relative;
                    text-align: center;
                    color: white;
                }

                /* Bottom left text */
                .bottom-left {
                    position: absolute;
                    bottom: 11px;
                    left: 16px;
                }

                .bottom-right {
                    position: absolute;
                    bottom: 8px;
                    right: 16px;
                }

                #rcorners2 {
                    border-radius: 25px;
                    border: 2px solid black;
                    padding: 20px;
                    width: 420px;
                    height: 100px;
                    background-color: #eee;
                }
            </style>

        </head>

        <div id="invoice">

            <?php $image_path = '/assets/images/icons/company.png'; ?>
            {{-- <div class="toolbar hidden-print">
            <div class="text-right">
                <button id="printInvoice" class="btn btn-info"><i class="fa fa-print"></i> Print</button>
                <button class="btn btn-info"><i class="fa fa-file-pdf-o"></i> Export as PDF</button>
            </div>
            <hr>
        </div> --}}



            <div class="invoice overflow-auto">

                <div style="min-width: 600px">

                    <header style="border:none;">
                        <div class="col">
                            <a href="#" onclick="window.print()" id="printhide" class="float-left"> <img
                                    src="{{ asset('assets/images/icons/printer.png') }}"
                                    style="width:40px; height:40px;"></a>

                            {{-- @if (Auth::user()->role->name == 'purchasing_officer') --}}
                            @if (Auth::user()->hasRole('purchasing_officer'))
                                <td>
                                    <a href="{{ route('purchases.generate_order', $purchase->id) }}"
                                        style="margin-left: 10px" class="btn btn-primary float-right">Generate PO</a>

                                </td>
                            @endif
                            {{-- @if (Auth::user()->role->name == 'requester') --}}
                            @if (Auth::user()->hasRole('requester'))
                                <a href="{{ route('purchases.req_all') }}"id="printhide" class="btn btn-primary float-right"
                                    style="float: right;">Back</a>
                            @else
                                <a href="{{ route('authorise.all_requests') }}" id="printhide"
                                    class="btn btn-primary float-right" style="float: right;">Back</a>
                            @endif

                        </div>
                    </header>
                    <br>
                    <header>
                        <div class="row">
                            <div class="col">
                                <a target="_blank">
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/assets/images/icons/company.png'))) }}"
                                        style="height: 100px; width: 230px;" alt="company image"
                                        data-holder-rendered="true" />
                                </a>
                            </div>

                            <div class="col company-details" style="margin-right:5%">

                                <p id="rcorners2" style="text-align:center; font-size:25px;">REQUEST FOR QUOTE (RFQ)
                                </p>

                            </div>

                            <div class="col company-details">

                                <h2 class="name">
                                    <a target="_blank" href="#">
                                        {{ $company->name ?? '' }}
                                    </a>
                                </h2>
                                <div> {{ $company->address ?? '' }}</div>
                                <div> {{ $company->phone ?? '' }}</div>
                                <div> {{ $company->email ?? '' }}</div>
                            </div>
                        </div>
                    </header>
                    <p class="float-left" style="font-weight: bold; font-size:20px;">RFQ: Ref#:
                        {{ $purchase->purchasing_order_number ?? '' }}</p>
                    <p class="float-right" style="font-weight: bold; font-size:20px;">Date Created:
                        {{ date('d-m-Y (H:i)', strtotime($purchase->request_date ?? '')) }}</p>
                    <main>
                        {{-- <div class="container1">
                            <img src="{{ asset('assets/images/icons/dpr.png') }}" alt="" style="width: 100%;">
                            <div class="bottom-left" style="color: black"><h4>REF#: {{ $purchase->request_number ?? '' }}</h4>  </div>
                            <div class="bottom-right" style="color: black"><h4>DATE:  {{ date('d-m-Y (H:i)', strtotime($purchase->request_date)) }}</h4>  </div>
                        </div> --}}



                        <table style="border: 1px solid black;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid black;">Enduser</th>
                                    <th style="border: 1px solid black;">Description / Name</th>
                                    <th style="border: 1px solid black;">WO Number</th>
                                    <th style="border: 1px solid black;">Requested By</th>
                                    <th style="border: 1px solid black;">Approved By</th>
                                    <th style="border: 1px solid black;">Approved Date</th>
                                    <th style="border: 1px solid black;">Site / Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="background-color:white;border: 1px solid black;">
                                        {{ $purchase->enduser->asset_staff_id ?? 'Not Set' }}</td>
                                    <td style="background-color:white;border: 1px solid black;">
                                        {{ $purchase->enduser->name_description ?? 'Not Set' }}</td>
                                    <td style="background-color:white;border: 1px solid black;">
                                        {{ $purchase->work_order_ref ?? 'Not Set' }}</td>
                                    <td style="background-color:white;border: 1px solid black;">
                                        {{ $purchase->requested->name ?? 'Not Set' }}</td>
                                    <td style="background-color:white;border: 1px solid black;">
                                        {{ $purchase->approvedby->name ?? 'Not Set' }}</td>
                                    <td style="background-color:white;border: 1px solid black;">
                                        {{ $purchase->approved_on ?? ''}}
                                    </td>
                                    <td style="background-color:white;border: 1px solid black;">
                                        {{ $purchase->enduser->site->name ?? 'Not Set' }}</td>
                                </tr>

                            </tbody>
                        </table>
                        <br> <br>
                        <table border="0" cellspacing="0" cellpadding="0" style="border: 1px solid black;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid black;">LN#</th>
                                    <th style="border: 1px solid black;">Description</th>
                                    <th style="border: 1px solid black;">Part Number</th>
                                    <th style="border: 1px solid black;">UoM</th>
                                    
                                    <th style="border: 1px solid black;">Quantity</th>
                                    <th style="border: 1px solid black;">Priority</th>
                                    <th style="border: 1px solid black;">Remarks</th>

                                </tr>
                            </thead>

                            @forelse ($order_parts as $ph)
                                <tbody>
                                    <tr>
                                        <td style="background-color:white;border: 1px solid black;">{{ $loop->iteration }}
                                        </td>
                                        <td style="background-color:white;border: 1px solid black;">
                                            {{ $ph->description ?? '' }}</td>
                                            <td style="background-color:white;border: 1px solid black;">
                                                {{ $ph->part_number ?? '' }}</td>
                                        <td style="background-color:white;border: 1px solid black;">{{ $ph->uom ?? '' }}
                                        </td>
                                     
                                        <td style="background-color:white;border: 1px solid black;">
                                            {{ $ph->quantity ?? '' }}
                                        </td>
                                        <td style="background-color:white;border: 1px solid black;">
                                            {{ $ph->priority ?? '' }}
                                        </td>
                                        <td style="background-color:white;border: 1px solid black;">
                                            {{ $ph->remarks ?? '' }}
                                        </td>
                                    @empty
                                    <tr>
                                        <td class="text-center" colspan="12">Data Not Found!</td>
                                    </tr>
                            @endforelse

                            </tr>
                            </tbody>


                        </table>
                        {{-- @if ((Auth::user()->role->name == 'store_officer' && $purchase->approval_status == 'Approved') || (Auth::user()->role->name == 'store_assistant' && $purchase->approval_status == 'Approved'))
                        <a href="{{ route('stores.store_officer_edit', $purchase->id) }}" class="float-right"
                            style="padding-right:10px;" id="approvebtn"><img src="{{ asset('assets/images/icons/edit.png') }}"
                                style="width:30px; height:30px;"></a>
                    @else
                    @endif --}}


                        {{-- @if (Auth::user()->role->name == 'purchasing_officer') --}}
                        @if (Auth::user()->hasRole('purchasing_officer'))
                            <td>
                                <a href="{{ route('purchases.generate_order', $purchase->id) }}"
                                    class="btn btn-info float-left">Generate PO</a>

                            </td>
                        @endif


                        {{-- @if (Auth::user()->role->name == 'Super Authoriser') --}}
                        @if (Auth::user()->hasRole('Super Authoriser'))
                            @if ($purchase->approval_status == '')
                                {{-- {{ 'not yet ' }} --}}
                                {{-- <button    class="btn btn-secondary">Approved</button> --}}
                                <a href="{{ route('authorise.approved_status', $purchase->id) }}"
                                    class="btn btn-success float-right" id="printhide">Approve</a>
                            @elseif ($purchase->approval_status == 'Approved')
                                {{-- {{ 'not yet ' }} --}}
                                {{-- <button    class="btn btn-secondary">Approved</button> --}}
                                <a href="{{ route('authorise.denied_status', $purchase->id) }}"
                                    class="btn btn-danger float-right" id="printhide">Deny</a>
                            @elseif ($purchase->approval_status == 'Denied')
                                {{-- {{ 'not yet ' }} --}}
                                {{-- <button    class="btn btn-secondary">Approved</button> --}}
                                <a href="{{ route('authorise.approved_status', $purchase->id) }}"
                                    class="btn btn-success float-right" id="printhide">Approve</a>
                            @else
                                {{-- {{ 'another time' }} --}}
                                {{-- <button class="btn btn-warning">Assigned</button> --}}
                                <a href="{{ route('authorise.denied_status', $purchase->id) }}"
                                    class="btn btn-danger float-right" id="printhide">Deny</a>
                            @endif
                        @endif
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
