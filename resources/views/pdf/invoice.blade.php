<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            padding: 30px;
        }

        .header {
            margin-bottom: 10px;
        }

        .member-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        tbody td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .total-section {
            background-color: #f2f2f2;
            margin-top: 20px;
            padding: 15px;
        }

        .total-section table {
            width: 100%;
        }

        .total-section td {
            padding: 8px;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
        }

    </style>
</head>

<body>

    <div class="header">
        <strong>Indo April</strong>
    </div>

    <div class="member-info">
        <p>Member Status : {{ $purchase->member ? 'Member' : 'Non Member' }}</p>
        <p>No. HP : {{ $purchase->member ? $purchase->member->phone_number : '-' }}</p>
        <p>Bergabung Sejak :
            {{ $purchase->member ? \Carbon\Carbon::parse($purchase->member->joined_at)->translatedFormat('d F Y') : '-' }}
        </p>
        <p>Poin Member : {{ $purchase->member ? floor($purchase->member->points) : '-' }}</p>

    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>QTY</th>
                <th>Harga</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp. {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="right">Rp. {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="total-section">
        <tr>
            <td class="right bold" colspan="3">Total Harga</td>
            <td class="bold right">Rp.
                {{ number_format(max(0, $purchase->total_price + $purchase->used_point), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Poin Digunakan</td>
            <td class="col-3">{{ $purchase->used_point ?? 0 }}</td>
            <td class="right bold">Harga Setelah Poin</td>
            <td class="bold right">Rp. {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="right bold" colspan="3">Total Kembalian</td>
            <td class="bold right">Rp.
                {{ number_format(max(0, $purchase->pay_change), 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>{{ $purchase->created_at }} | {{ $purchase->user->name }}</p>
        <p><strong>Terima kasih atas pembelian Anda!</strong></p>
    </div>

</body>

</html>
