<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Barcode</title>
    <style>
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
<table width="100%">
    <tr>
        @php $counter = 0; @endphp
        @foreach ($productData as $product)
            <td class="text-center" style="border: 1px solid #333;">
                <p>{{ $product->name }} - Rp. {{ money_number_format($product->sell_price) }}</p>
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($product->code, 'C128B') }}"
                     alt="{{ $product->code }}"
                     width="180"
                     height="60">
                <br>
                {{ $product->code }}
            </td>
            @php $counter++; @endphp
            @if ($counter % 3 == 0)
    </tr>
    <tr>
        @endif
        @endforeach
    </tr>
</table>
</body>
</html>
