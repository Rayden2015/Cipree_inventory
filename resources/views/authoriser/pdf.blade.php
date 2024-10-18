@extends('layouts.app2')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <style>
            #purchase {
                font-family: Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            #foot {
                float: right;
                padding-right: 100px;
            }

            #purchase td,
            #purchase th {
                border: 1px solid #ddd;
                padding: 8px;
            }

            #purchase tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            #purchase tr:hover {
                background-color: #ddd;
            }

            #purchase th {
                padding-top: 12px;
                padding-bottom: 12px;
                text-align: left;
                background-color: rgb(54, 56, 54);
                color: white;
            }
        </style>
    </head>

    <body>

        <h1 style=" text-align:center;">Purchase Details</h1>

        <table id="purchase">

            <tr>
                <th>Label</th>
                <th>Value</th>
            </tr>
            {{--  --}}
            <tr>
                <td style="font-weight:bold;">Supplier </td>
                <td> {{ $purchase->supplier->name ?? ' ' }} </td>
            </tr>
            {{--  --}}



            {{--  --}}
            <tr>
                <td style="font-weight:bold;">Type of Purchase </td>
                <td> {{ $purchase->type_of_purchase ?? ' ' }} </td>
            </tr>
            {{--  --}}

            {{--  --}}
            <tr>
                <td style="font-weight:bold;">Enduser </td>
                <td> {{ $purchase->enduser->asset_staff_id ?? ' ' }} </td>
            </tr>
            {{--  --}}

            {{--  --}}
            <tr>
                <td style="font-weight:bold;">Status </td>
                <td> {{ $purchase->status ?? ' ' }} </td>
            </tr>
        </table>
        <br><br>
        {{--  --}}
        <table id="purchase" border="0" cellspacing="0" cellpadding="0" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>Pre-fix</th>
                    <th>Part Number</th>
                    <th>Quantity</th>
                    <th>Priority</th>
                    <th>Remarks</th>

                </tr>
            </thead>

            @forelse ($order_parts as $ph)
                <tbody>
                    <tr>
                        <td>{{ $ph->id }}</td>
                        <td>{{ $ph->description ?? '' }}</td>
                        <td>{{ $ph->prefix ?? '' }}</td>
                        <td>{{ $ph->part_number ?? '' }}</td>
                        <td>{{ $ph->quantity ?? '' }}</td>
                        <td>{{ $ph->priority ?? '' }}</td>
                        <td>{{ $ph->remarks ?? '' }}</td>
                    @empty
                    <tr>
                        <td class="text-center" colspan="12">Data Not Found!</td>
                    </tr>
            @endforelse

            </tr>
            </tbody>

        </table>



    </body>

    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
