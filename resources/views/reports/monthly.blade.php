@extends('layouts.admin')
@section('content')
    {{-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->

    <head>

        <style>
            th {
                border: 1px solid black;
                background-color: rgb(203, 199, 199);
            }

            td,
            th {
                text-align: left;
                padding: 8px;
            }

            td,
            th {
                border-right: 1px solid black;
            }

            table {
                border: 1px solid #dddddd;
                border-collapse: collapse;

            }

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


            .invoice table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                margin-bottom: 20px
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

            #rcorners2 {
                border-radius: 25px;
                border: 2px solid black;
                padding: 20px;
                width: 380px;
                height: 90px;
                color: black;
                font-weight: 100px;

            }
        </style>
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
        
            th, td {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }
        
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
        </style>

    </head>
    <div id="invoice">
        <div class="card-header">
            <a href="" onclick="window.print()" id="printPageButton" class="btn btn-primary float-left">Print</a>
            <a href="{{ route('inventories.index') }}" id="printPageButton" class="btn btn-primary float-right">Back</a>
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

                {{-- <header>
                    <div class="row">
                    <div class="col-sm-4" style="background-color:lavender;"><hr style="width:100%; border:3px solid black"></div>
                    <div class="col-sm-4" style="background-color:lavenderblush;"><p id="rcorners2">Rounded corners!</p></div>
                    <div class="col-sm-4" style="background-color:lavender;"><hr style="width:100%; border:3px solid black"></div>
                  </div>
                </header> --}}


                <main>

                    <table border="0" cellspacing="0" cellpadding="0" class="" id="rp" style="width:100%">
                        <!-- report.blade.php -->


                        <thead>
                            <tr>
                                <th>LN#</th>
                                <th>Month / Year</th>
                                <th>Opening Bal.($)</th>
                                <th>Receiving Items ($)</th>
                                <th>Supplied Items ($)</th>
                                <th>Closing Bal.</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($openingBalances as $month => $openingBalance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $month }}</td>
                                    <td>{{ $openingBalance }}</td>
                                    <td>{{ $result_received[$loop->index]['received'] }}</td>
                                    <td>{{ $result_supplied[$loop->index]['totalSupplied'] }}</td>
                                    <td>{{ $closingBalances[$month] }}</td>
                                    <td></td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>


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
