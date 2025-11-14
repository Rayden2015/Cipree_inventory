@extends('layouts.admin')
@section('content')
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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

        <div class="invoice overflow-auto">
            <a href="#" id="printbtn" onclick="window.print()" class="btn btn-primary float-lef">Print</a>
            <a href="{{ route('dashboard.pending_stock_approvals') }}" id="backbtn"
                class="btn btn-primary float-right">Back</a>

            <br>
            <div style="min-width: 600px">
                <header>

                    <div class="row">
                        <div class="col">
                            <a target="_blank">
                                <img src="{{ asset('images/company/' . $company->image) }}"
                                    style="height: 80px; width: 250px;" alt="company image" data-holder-rendered="true" />
                            </a>
                            <br>

                            {{-- <p style="font-size: 1.6em;">Stock Requisition (SR)</p> --}}
                        </div>

                        <div class="col">


                            <p style="font-size: 1.9em; font-weight: bold;">STOCK REQUISITION (SR)</p>
                        </div>

                        <div class="col company-details">
                            <h2 class="name">
                                <a target="#" href="#" style="text-decoration: none; pointer-events: none;">
                                    {{ $company->name ?? '' }}
                                </a>
                            </h2>
                            <div> {{ $company->address ?? '' }}</div>
                            <div> {{ $company->phone ?? '' }}</div>
                            <div> {{ $company->email ?? '' }}</div>
                        </div>
                    </div>
                </header>
                <main>

                    <header>
                        <div class="row">

                            <div class="col">
                                <table border="0" cellspacing="0" cellpadding="0"
                                    style="width:410px; height:10%; font-size:12px;">
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            SR Number:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->request_number ?? ' ' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Work Order #:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->work_order_number ?? 'N/A' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Request Date:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ date('d-m-Y H:i:s', strtotime($sorder->request_date)) }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Request By:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->request_by->name ?? ' ' }} </th>
                                    </tr>
                                    {{-- <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Supplied To:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->supplied_to ?? ' ' }}</th>
                                    </tr> --}}
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            End User:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->enduser->asset_staff_id ?? ' ' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Department:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->enduser->department ?? ' ' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Name/Description:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->enduser->name_description ?? ' ' }}</th>
                                    </tr>
                                </table>

                            </div>
                            <div style="margin-right: 50px;">

                                <table style="width:450px; font-size:12px;">
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Department Approval Status:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->depart_auth_approval_status ? ucfirst(strtolower($sorder->depart_auth_approval_status)) : 'Pending' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Department Authorised By:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->depart_auth_name->name ?? '--' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Department Approved On:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->depart_auth_approved_on ? \Carbon\Carbon::parse($sorder->depart_auth_approved_on)->format('d-m-Y (H:i)') : '--' }}</th>
                                    </tr>
                                    @if($sorder->depart_auth_approval_status === 'Denied')
                                        <tr>
                                            <th
                                                style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                                Department Denied By:</th>
                                            <th
                                                style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                                {{ $sorder->depart_auth_denied_name->name ?? '--' }}</th>
                                        </tr>
                                        <tr>
                                            <th
                                                style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                                Department Denied On:</th>
                                            <th
                                                style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                                {{ $sorder->depart_auth_denied_on ? \Carbon\Carbon::parse($sorder->depart_auth_denied_on)->format('d-m-Y (H:i)') : '--' }}</th>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Supply Chain Approval Status:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->approval_status ? ucfirst(strtolower($sorder->approval_status)) : 'Pending' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Supply Chain Authorised By:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->approve_by->name ?? '--' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Supply Chain Approved On:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->approved_on ? \Carbon\Carbon::parse($sorder->approved_on)->format('d-m-Y (H:i)') : '--' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Stores Processed By:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->user->name ?? '--' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Stores Processed On:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->delivered_on ? \Carbon\Carbon::parse($sorder->delivered_on)->format('d-m-Y (H:i)') : '--' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            SR Status:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            {{ $sorder->status ?? '--' }}</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </header>
                    <table border="0" cellspacing="0" cellpadding="0" class="def">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>Part Number</th>
                                <th>UoM</th>
                                <th>Qty Req.</th>
                                <th>Qty Sup.</th>
                                <th>Remarks</th>
                                <th>Unit Cost</th>
                                <th>Amount</th>


                            </tr>
                        </thead>

                        @forelse ($sorder_parts as $ph)
                            <tbody>
                                <tr>
                                    <td
                                        style="
                background: #eee;
                border-bottom: 1px solid #fff; text-align:left;">
                                        {{ $ph->id }}</td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        {{ $ph->item_details->item_description ?? '' }}</td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        {{ $ph->item_details->item_part_number ?? '' }}</td>
                                    {{-- <td>{{ $ph-> }}</td> --}}
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        {{ $ph->item_details->item_uom ?? '' }}</td>

                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        {{ $ph->quantity ?? '' }}</td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        {{ $ph->qty_supplied ?? '' }}</td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        {{ $ph->remarks ?? '' }}</td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        {{ $ph->item_parts->unit_cost_exc_vat_gh ?? '' }}</td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        {{ $ph->sub_total ?? '' }}</td>
                                @empty
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                        @endforelse

                        </tr>

                        </tbody>




                    </table>
                    <label for="" class="float-right">Total (USD): {{ $sorder->total ?? '' }} </label> <br>
                    <div style=" padding: 10px; width:40%;">
                        <p>Received in Good Condition by: {{ $sorder->supplied_to ?? ' ' }}</p> <br>
                        <p>Signed by:</p> <br>
                    </div>

                    <br>
                    {{-- <a href="#" id="printbtn" onclick="window.print()" class="btn btn-primary float-right">Print</a> --}}
                    @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Authoriser'))
                        @if ($sorder->approval_status == '')
                            <a href="{{ route('stores.approved_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-success float-right">Approve (Final)</a>
                        @elseif ($sorder->approval_status == 'Approved')
                            <a href="{{ route('stores.denied_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-danger float-right">Deny (Final)</a>
                        @elseif ($sorder->approval_status == 'Denied')
                            <a href="{{ route('stores.approved_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-success float-right">Approve (Final)</a>
                        @else
                            <a href="{{ route('stores.denied_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-danger float-right">Deny (Final)</a>
                        @endif
                    @elseif (Auth::user()->hasRole('Department Authoriser'))
                        @if ($sorder->depart_auth_approval_status == '')
                            <a href="{{ route('stores.depart_auth_approved_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-success float-right">Approve (Department)</a>
                        @elseif ($sorder->depart_auth_approval_status == 'Approved')
                            <a href="{{ route('stores.depart_auth_denied_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-danger float-right">Deny (Department)</a>
                        @elseif ($sorder->depart_auth_approval_status == 'Denied')
                            <a href="{{ route('stores.depart_auth_approved_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-success float-right">Approve (Department)</a>
                        @else
                            <a href="{{ route('stores.depart_auth_denied_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-danger float-right">Deny (Department)</a>
                        @endif
                    @endif
                    {{-- end of approval actions --}}
                    @if (
                        (Auth::user()->hasRole('store_officer') && $sorder->approval_status == 'Approved') ||
                            (Auth::user()->hasRole('store_assistant') && $sorder->approval_status == 'Approved'))
                        <td><a href="{{ route('stores.store_officer_edit', $sorder->id) }}"
                                class="btn btn-success float-right" id="approvebtn">Process</a></td>
                    @else
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
