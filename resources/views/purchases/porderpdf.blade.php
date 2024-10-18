
@extends('layouts.app2')
@section('content')
{{-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
<!------ Include the above in your HEAD tag ---------->
<html>
    
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
<body> 
<div id="invoice">

  
    <div class="invoice overflow-auto" style="padding-top:1px;">
        <div style="min-width: 600px">
            <header>
                <div class="row">
                    {{-- <div class="col"> --}}
                        <a target="_blank">
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/images/company/logo.jpg'))) }}"
                                style="height: 80px; width: 290px;" alt="company image" data-holder-rendered="true" />
                                <br>
                                <span style="font-size:2em;"> </span>
                               
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
                    {{-- <div class="col company-details">
                        <h2 class="name">
                            <a href="#" style="text-decoration:none;">
                                {{ $company->name ?? '' }}
                            </a>
                        </h2>
                        <div> {{ $company->address ?? '' }}</div>
                        <div> {{ $company->phone ?? '' }}</div>
                        <div> {{ $company->email ?? '' }}</div>
                    </div> --}}
                    <br>
                </div>
            </header>
            <main>
                <div class="row contacts">
                    <div class="col invoice-to">
                        <div class="text-gray-light">PURCHASING ORDER TO:</div>
                        <h2 class="to">{{ $purchase->supplier->name}}</h2>
                        <div class="address">{{ $purchase->supplier->address ?? ' ' }}</div>
                        <div class="email"><a
                                href="mailto:john@example.com">{{ $purchase->supplier->email ?? ' ' }}</a></div>
                    </div>
                    <div class="col invoice-details">
                        <h1 class="invoice-id">{{ $purchase->purchasing_order_number ?? '' }}</h1>
                        <div class="date">{{ date('d-m-Y (H:i)', strtotime($purchase->request_date))}}</div>
                        {{-- <div class="date">Due Date: 30/10/2018</div> --}}
                    </div>
                </div>
                <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Pre-fix</th>
                            <th>Part Number</th>
                            <th>Quantity</th>
                            <th>Priority</th>
                            <th>Remarks</th>
                            <th>Unit Price</th>
                            <th>Sub Total</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order_parts as $ph)
                            <tr>
                                <td>{{ $ph->id }}</td>
                                <td>{{ $ph->description ?? '' }}</td>
                                <td>{{ $ph->prefix ?? '' }}</td>
                                <td>{{ $ph->part_number ?? '' }}</td>
                                <td>{{ $ph->quantity ?? '' }}</td>
                                <td>{{ $ph->priority ?? '' }}</td>
                                <td>{{ $ph->remarks ?? '' }}</td>
                                <td>{{ $ph->unit_price ?? '' }}</td>
                                <td>{{ $ph->sub_total ?? '' }}</td>
                            </tr>
                        @endforeach

                    </tbody>

                                
            
                </table>
                <div style="float:right;">
                    <p>Tax: GHC 200</p><br>
                    <p>GRANDTOTAL: {{ $grandtotal }}</p>
                </div>
              
               
                {{-- <div class="thanks">Thank you!</div> --}}
                {{-- <div class="notices">
                    <div>NOTICE:</div>
                    <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
                </div> --}}
            </main>
            {{-- <footer>
                Invoice was created on a computer and is valid without the signature and seal.
            </footer> --}}
        </div>
        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
        <div></div>
    </div>
</div>
</body>
</html>
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

