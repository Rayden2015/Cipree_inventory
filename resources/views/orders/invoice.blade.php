@extends('layouts.app2')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> --}}
        {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> --}}
        {{-- <link rel="stylesheet" href="/css/bootstrap-print.min.css" media="print">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-print-css/css/bootstrap-print.min.css" media="print"> --}}
        <style>
            @media print {
                body {
                    -webkit-print-color-adjust: exact;
                }
            }
        </style>
        {{-- <style>

@media print {
   body {
      -webkit-print-color-adjust: exact;
   }
}
        body {
            margin-top: 20px;
            color: #2e323c;
            background: #f5f6fa;
            position: relative;
            height: 70%;
            width: 70%;
            max-width: 800px;
            margin: auto;

            padding: 10px;

        }

        .content {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 10px;
        }

        .invoice-container {
            padding: 1rem;
        }

        .invoice-container .invoice-header .invoice-logo {
            margin: 0.8rem 0 0 0;
            display: inline-block;
            font-size: 1.6rem;
            font-weight: 700;
            color: black;
        }

        .invoice-container .invoice-header .invoice-logo img {
            max-width: 130px;
        }

        .invoice-container .invoice-header address {
            font-size: 0.8rem;
            color: #9fa8b9;
            margin: 0;
        }

        .invoice-container .invoice-details {
            margin: 1rem 0 0 0;
            padding: 1rem;
            line-height: 180%;
            background: #f5f6fa;
        }

        .invoice-container .invoice-details .invoice-num {
            text-align: right;
            font-size: 0.8rem;
        }

        .invoice-container .invoice-body {
            padding: 1rem 0 0 0;
        }

        .invoice-container .invoice-footer {
            text-align: center;
            font-size: 0.7rem;
            margin: 5px 0 0 0;
        }

        .invoice-status {
            text-align: center;
            padding: 1rem;
            background: #ffffff;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .invoice-status h2.status {
            margin: 0 0 0.8rem 0;
        }

        .invoice-status h5.status-title {
            margin: 0 0 0.8rem 0;
            color: #9fa8b9;
        }

        .invoice-status p.status-type {
            margin: 0.5rem 0 0 0;
            padding: 0;
            line-height: 150%;
        }

        .invoice-status i {
            font-size: 1.5rem;
            margin: 0 0 1rem 0;
            display: inline-block;
            padding: 1rem;
            background: #f5f6fa;
            -webkit-border-radius: 50px;
            -moz-border-radius: 50px;
            border-radius: 50px;
        }

        .invoice-status .badge {
            text-transform: uppercase;
        }

        @media (max-width: 767px) {
            .invoice-container {
                padding: 1rem;
            }
        }


        .custom-table {
            border: 1px solid #e0e3ec;
        }

        .custom-table thead {
            background: #3F79EB;
        }

        /* .custom-table thead th {
            border: 0;
            color: #ffffff;
        } */

        .custom-table>tbody tr:hover {
            background: #fafafa;
        }

        .custom-table>tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }

        .custom-table>tbody td {
            border: 1px solid #e6e9f0;
        }


        .card {
            background: #ffffff;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            border: 0;
            margin-bottom: 1rem;
        }

        .text-success {
            color: #00bb42 !important;
        }

        .text-muted {
            color: #9fa8b9 !important;
        }

        .custom-actions-btns {
            margin: auto;
            display: flex;
            justify-content: flex-end;
        }

        .custom-actions-btns .btn {
            margin: .3rem 0 .3rem .3rem;
        }
    </style> --}}
    </head>

    <body>
        <div class="container">
            <div class="row gutters" style="padding-left:10px;">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="invoice-container">
                                <div class="invoice-header">

                                    <!-- Row start -->
                                    {{-- @foreach ($company as $company) --}}
                                        <div class="row gutters" style="padding-left:10px;">
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                <td class="invoice-logo">
                                                    {{-- NAYTYVE --}}
                                                    <h3 style="padding-top:4%;"> <b> {{ $company->name }}</b> </h3>
                                                </td>
                                            </div>
                                            {{-- <div class="col-lg-6 col-md-6 col-sm-6">
                                        <address class="text-right">
                                            Maxwell admin Inc, 45 NorthWest Street.<br>
                                            Sunrise Blvd, San Francisco.<br>
                                            00000 00000
                                        </address>
                                    </div> --}}
                                        </div>

                                        <!-- Row end -->

                                        <!-- Row start -->



                                        <table style="width:100%">
                                            <tr>
                                                <th style="width:30%"> <span
                                                        style="padding-left:10px; font-weight: normal;">{{ $company->phone }}
                                                    </span></th>
                                                <th> <span
                                                        style="font-weight: normal; padding-left:30%;">{{ $company->address }}
                                                    </span></th>

                                            </tr>
                                            <tr>
                                                <td style="padding-left:10px;">{{ $company->email }}</td>
                                                <td> <span style="font-weight: normal; padding-left:30%;">East Legon</span>
                                                </td>
                                                <td>
                                                <th rowspan="2">
                                                    <h1 style="color:blue; padding-left:30%;"><b> INVOICE</b> </h1>
                                                </th>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left:10px;">{{ $company->website }}</td>
                                                <td> <span style="font-weight: normal; padding-left:30%;">Accra</span> </td>

                                            </tr>
                                            <tr>
                                                <td></td>


                                            </tr>
                                        </table>

                                        <br>
                                    {{-- @endforeach --}}
                                    <hr>
                                    <!-- Row end -->





                                    <table style="width:100%">
                                        <tr>
                                            <th style="width:47%; padding-left:10px;">Bill To: <span class="pl-4"
                                                    style="font-weight: normal; padding-left:4%;">
                                                    {{ $invoice->client->name }}</span>
                                            </th>
                                            <th style="width:15%"> <span style="font-weight:normal"> Invoice No:</span>
                                            </th>
                                            <th style="padding-left:8%;"> <span style="font-weight:normal">
                                                    {{ $invoice->receipt_no }}</span> </th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Date</td>
                                            <td style="padding-left:8%;">{{ date('d-m-Y (H:i)', strtotime($invoice->created_at))}}</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            {{-- <td>Terms</td>
                                        <td>None</td> --}}
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Due Date:</td>
                                            <td style="padding-left:8%;"> {{ date('d-m-Y (H:i)', strtotime($invoice->est))}}</td>
                                        </tr>
                                    </table>




                                    <div class="invoice-body">


                                        <!-- Row start -->
                                        <div class="row gutters">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="table-responsive">

                                                    <table class="table custom-table m-0">
                                                        <thead>
                                                            <tr style="background-color:#3F79EB;">
                                                                <th>Products</th>
                                                                <th>Price</th>
                                                                <th>Quantity</th>
                                                                <th>Sub Total</th>
                                                            </tr>
                                                        </thead>
                                                        @foreach ($invoices as $invs)
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        {{ $invs->product->name }}

                                                                    </td>
                                                                    <td> {{ $invs->product->price }}</td>
                                                                    <td> {{ $invs->quantity }}</td>
                                                                    <td> {{ $invs->product->price }}</td>
                                                                </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td colspan="2">
                                                                <p>
                                                                    Subtotal<br>
                                                                    Discount<br>
                                                                    Paid<br>
                                                                </p>
                                                                <h5 class="text-primary" style="background-color:#3F79EB;">
                                                                    <strong style="color: white">Balance Due</strong>
                                                                </h5>
                                                            </td>
                                                            <td>
                                                                <p>
                                                                    Ghc {{ $invoice->price }}<br>
                                                                    Ghc {{ $invoice->dis }}<br>
                                                                    Ghc {{ $paid }}<br>
                                                                </p>
                                                                <h5 class="text-primary" style="background-color:#3F79EB;">
                                                                    <strong
                                                                        style="color: white">Ghc {{ $bal }}</strong>
                                                                </h5>
                                                            </td>
                                                        </tr>
                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Row end -->
                                    </div>

                                    <div class="invoice-footer" style="text-align: center;">
                                        Thanks for purchasing.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </body>
    <script>
        window.print();
    </script>

    </html>
@endsection
