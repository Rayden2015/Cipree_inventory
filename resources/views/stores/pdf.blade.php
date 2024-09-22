@extends('layouts.app2')
@section('content')
 
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
      
                .invoice>div:last-child {
                    page-break-before: always
                }
            }
      
            @media print {
                #printPageButton {
                    display: none;
                }
            }
            @page {
  size: A3 Landscape;
}
.topright {
  position: absolute;
  top: 8px;
  right: 16px;
  font-size: 18px;
}

        </style>

    </head>
    <div id="invoice">

        {{-- <div class="toolbar hidden-print">
        <div class="text-right">
            <button id="printInvoice" class="btn btn-info"><i class="fa fa-print"></i> Print</button>
            <button class="btn btn-info"><i class="fa fa-file-pdf-o"></i> Export as PDF</button>
        </div>
        <hr>
    </div> --}}

        <div class="invoice overflow-auto">
            
            <div style="min-width: 600px">
                <header>
                    <div class="row">
                        {{-- <div class="col"> --}}
                            <a target="_blank">
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/images/company/logo.jpg'))) }}"
                                    style="height: 80px; width: 290px;" alt="company image" data-holder-rendered="true" />
                                    <br>
                                    <span style="font-size:2em;"> Stock Requisition (SR)</span>
                                   
                                    {{-- <p style="font-size: 1.6em;">Stock Requisition (SR)</p> --}}
                            </a>
                            {{-- <br>
                            <br> --}}
                            

                            <div class="topright">
                                {{-- <h2 class=""> --}}
                                    <div  style="text-decoration:none; color:blue; font-size:1.875rem">
                                        {{ $company->name ?? '' }}
                                    </div>
                                {{-- </h2> --}}
                                <div> {{ $company->address ?? '' }}</div>
                                <div> {{ $company->phone ?? '' }}</div>
                                <div> {{ $company->email ?? '' }}</div>
                            </div>
                        {{-- </div> --}}
                       
                    </div>
                </header>
                <main>

                    <header>
                        <div class="row">

                            <div class="col">
                                <table border="0" cellspacing="0" cellpadding="0"
                                    style="width:50%; height:10%; font-size:18px; margin-left:25%; margin-right:25%;">
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;text-align:left; padding-left:20px;">
                                            SR Number:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white; text-align:left;padding-left:20px;">
                                            {{ $sorder->request_number ?? ' ' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;text-align:left; padding-left:20px;">
                                            Request Date:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white; text-align:left;padding-left:20px;">
                                            {{ date('d-m-Y H:i:s', strtotime($sorder->request_date)) }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;text-align:left; padding-left:20px;">
                                            Request By:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white; text-align:left;padding-left:20px;">
                                            {{ $sorder->request_by->name ?? ' ' }} </th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;text-align:left; padding-left:20px;">
                                            Supplied To:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white; text-align:left;padding-left:20px;">
                                            {{ $sorder->supplied_to ?? ' ' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue; text-align:left; padding-left:20px;">
                                            End User:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white; text-align:left;padding-left:20px;">
                                            {{ $sorder->enduser->asset_staff_id ?? ' ' }}</th>
                                    </tr>
                                {{-- </table>

                            </div>
                            <div style="margin-right: 50px;">

                                <table style="width:450px; font-size:12px;"> --}}
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue; text-align:left; padding-left:20px;">
                                            Approval Status:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white; text-align:left;padding-left:20px;">
                                            {{ $sorder->approval_status ?? 'Pending' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue; text-align:left;padding-left:20px;">
                                            Approved By:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white; text-align:left;padding-left:20px;">
                                            {{ $sorder->approve_by->name ?? '' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue; text-align:left;padding-left:20px;">
                                            Approved On:</th>
                                        @if ($sorder->approved_on !== null)
                                            <th
                                                style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;background-color:white; text-align:left;padding-left:20px;">
                                                {{ date('d-m-Y H:i:s', strtotime($sorder->approved_on ?? '')) }}</th>
                                        @else
                                            <th
                                                style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;background-color:white; text-align:left;padding-left:20px;">
                                            </th>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue; text-align:left;padding-left:20px;">
                                            Supplied By:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white; text-align:left;padding-left:20px;">
                                            {{ $sorder->user->name ?? ' ' }}</th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue; text-align:left;padding-left:20px;">
                                            SR Status:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white; text-align:left;padding-left:20px;">
                                            {{ $sorder->status ?? ' ' }}</th>
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
                                <th>Qty Requested</th>
                                <th>Qty Supplied</th>
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
                    {{-- <a href="#" id="printbtn" onclick="window.print()" class="btn btn-primary float-right">Print</a> --}}
                    @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('authoriser'))
                        {{-- <a href="{{ URL::previous() }}" class="btn btn-primary float-left">Approve</a> --}}
                        @if ($sorder->approval_status == '')
                            {{-- {{ 'not yet ' }} --}}
                            {{-- <button    class="btn btn-secondary">Approved</button> --}}
                            <a href="{{ route('stores.approved_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-success float-right">Approve</a>
                        @elseif ($sorder->approval_status == 'Approved')
                            {{-- {{ 'not yet ' }} --}}
                            {{-- <button    class="btn btn-secondary">Approved</button> --}}
                            <a href="{{ route('stores.denied_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-danger float-right">Deny</a>
                        @elseif ($sorder->approval_status == 'Denied')
                            {{-- {{ 'not yet ' }} --}}
                            {{-- <button    class="btn btn-secondary">Approved</button> --}}
                            <a href="{{ route('stores.approved_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-success float-right">Approve</a>
                        @else
                            {{-- {{ 'another time' }} --}}
                            {{-- <button class="btn btn-warning">Assigned</button> --}}
                            <a href="{{ route('stores.denied_status', $sorder->id) }}" id="approvebtn"
                                class="btn btn-danger float-right">Deny</a>
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
