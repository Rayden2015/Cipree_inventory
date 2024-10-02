<!DOCTYPE html>
<html>
<head>
    <title>Items List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Items List</h1>
    <table>
        <thead>
            <tr>
              
                <th>Description</th>
                <th>Part Number</th>
                <th>Stock Code</th>
                <th>Qty in Stock</th>
                <th>Unit Cost</th>
                <th>Amount</th>
                <th>Location</th>
                <th>Purchase Type</th>
                <th>GRN Number</th>
                <th>PO Number</th>
                <th>Age</th>
                <th>Supplier</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $rq)
                <tr>
                    <td>{{ $rq->item_description ?? '' }}</td>
                    <td>{{ $rq->item_part_number ?? '' }}</td>
                    <td>{{ $rq->item_stock_code ?? '' }}</td>
                    <td>{{ $rq->stock_quantity ?? '' }}</td>
                    <td>{{ $rq->unit_cost_exc_vat_gh ?? '' }}</td>
                    <td>{{ $rq->amount ?? '' }}</td>
                    <td>{{ $rq->name ?? '' }}</td> <!-- Access location name directly -->
                    <td>{{ $rq->trans_type ?? '' }}</td>
                    <td>{{ $rq->grn_number ?? '' }}</td>
                    <td>{{ $rq->po_number ?? '' }}</td>
                    <td>
                        @if ($rq->created_at)
                            {{ \Carbon\Carbon::parse($rq->created_at)->diffInDays(now()) }} days
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $rq->supplier_name ?? 'N/A' }}</td> <!-- Directly access supplier_name from the query -->
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">Data Not Found!</td> <!-- Adjust colspan based on number of columns -->
                </tr>
            @endforelse
        </tbody>
        
    </table>
</body>
</html>
