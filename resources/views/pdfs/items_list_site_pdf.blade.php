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
                <th>Amount</th>
                <th>Location</th>
                <th>Purchase Type</th>
                <th>GRN Number</th>
                <th>Age</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $rq)
                <tr>
                 
                    <td>{{ $rq->item->item_description ?? '' }}</td>
                    <td>{{ $rq->item->item_part_number ?? '' }}</td>
                    <td>{{ $rq->item->item_stock_code ?? '' }}</td>
                    <td>{{ $rq->item->stock_quantity ?? '' }}</td>
                    <td>{{ $rq->amount ?? '' }}</td>
                    <td>{{ $rq->location->name ?? '' }}</td>
                    <td>{{ $rq->inventory->trans_type ?? '' }}</td>
                    <td>{{ $rq->grn_number ?? '' }}</td>
                    <td>
                        @if ($rq->created_at)
                            {{ \Carbon\Carbon::parse($rq->created_at)->diffInDays(now()) }} days
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Data Not Found!</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
