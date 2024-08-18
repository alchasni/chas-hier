<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Note</title>

    <style>
        table td {
            /* font-family: Arial, Helvetica, sans-serif; */
            font-size: 14px;
        }

        table.data td,
        table.data th {
            border: 1px solid #ccc;
            padding: 5px;
        }

        table.data {
            border-collapse: collapse;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
<table width="100%">
    <tr>
        <td rowspan="4" width="60%">
            <img src="{{ public_path("/img/member.png") }}" alt="{{ "/img/member.png" }}" width="120">
            <br>
            {{ "ALAMAT" }}
            <br>
            <br>
        </td>
        <td>Tanggal</td>
        <td>: {{ to_date_string(date('Y-m-d')) }}</td>
    </tr>
    <tr>
        <td>Kode Member</td>
        <td>: {{ $transaction->guest->code ?? '' }}</td>
    </tr>
</table>

<table class="data" width="100%">
    <thead>
    <tr>
        <th>No</th>
        <th>Kode</th>
        <th>Nama</th>
        <th>Harga Satuan</th>
        <th>Jumlah</th>
        <th>Diskon</th>
        <th>Subtotal</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($detail as $key => $item)
        <tr>
            <td class="text-center">{{ $key+1 }}</td>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->product->code }}</td>
            <td class="text-right">{{ money_number_format($item->sell_price) }}</td>
            <td class="text-right">{{ money_number_format($item->quantity) }}</td>
            <td class="text-right">{{ money_number_format($item->price) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="6" class="text-right"><b>Total Harga</b></td>
        <td class="text-right"><b>{{ money_number_format($transaction->total_price) }}</b></td>
    </tr>
    <tr>
        <td colspan="6" class="text-right"><b>Total Bayar</b></td>
        <td class="text-right"><b>{{ money_number_format($transaction->final_price) }}</b></td>
    </tr>
    <tr>
        <td colspan="6" class="text-right"><b>Diterima</b></td>
        <td class="text-right"><b>{{ money_number_format($transaction->money_received) }}</b></td>
    </tr>
    <tr>
        <td colspan="6" class="text-right"><b>Change</b></td>
        <td class="text-right"><b>{{ money_number_format($transaction->money_received - $transaction->final_price) }}</b></td>
    </tr>
    </tfoot>
</table>

<table width="100%">
    <tr>
        <td><b>Terimakasih telah berbelanja dan sampai jumpa</b></td>
        <td class="text-center">
            Kasir
            <br>
            <br>
            {{ auth()->user()->name }}
        </td>
    </tr>
</table>
</body>
</html>
