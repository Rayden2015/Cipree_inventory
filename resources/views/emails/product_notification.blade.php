<!DOCTYPE html>
<html>

<head>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>

<body>

    <h2>HTML Table</h2>

    <table>
        <tr>
            <th>Product</th>
            <th>Description</th>

        </tr>
        @foreach ($products as $detail)
            <tr>
              
                <td>{{ $detail->id ?? '' }}</td>
                <td>{{ $detail->description ?? ''}}</td>

            </tr>
        @endforeach

    </table>
   
    <p>Thank you</p>

</body>

</html>
