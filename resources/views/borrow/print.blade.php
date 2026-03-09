<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota - {{ $borrow->code }}</title>
    <style>
        /* Thermal printer optimized style */
        @page {
            margin: 0;
            size: auto;
        }
        body {
            width: 100%;
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            color: #000;
            line-height: 1.4;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .header { margin-bottom: 5mm; }
        .logo { 
            width: 40mm; 
            max-width: 80%;
            margin: 0 auto 3mm; 
            display: block; 
            filter: grayscale(100%) contrast(150%); 
        }
        .company-name { font-size: 18px; font-weight: 900; margin: 0; line-height: 1.2; }
        .company-info { font-size: 11px; margin-top: 1mm; border-bottom: 2px solid #000; padding-bottom: 3mm; }
        
        .divider { border-top: 1px dashed #000; margin: 3mm 0; }
        .nota-info { font-size: 12px; margin-bottom: 4mm; text-align: center; }
        .nota-info div { margin-bottom: 1mm; }
        
        table { width: 100%; border-collapse: collapse; margin: 4mm 0; }
        th { text-align: center; font-size: 11px; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 2mm 0; }
        td { padding: 2mm 0; vertical-align: top; border-bottom: 1px dotted #ccc; }
        
        .item-name { display: block; font-weight: bold; margin-bottom: 0.5mm; text-align: center; }
        .item-qty { display: block; font-weight: 900; text-align: center; font-size: 14px; }
        
        .footer { margin-top: 5mm; font-size: 11px; }
        .qr-section { margin: 6mm 0; }
        .qr-code { 
            width: 45mm; 
            height: 45mm; 
            margin: 0 auto; 
            padding: 5px;
            background: #fff;
            border: 1px solid #000;
        }
        
        .no-print { display: none; }
        
        @media screen {
            body { 
                background: #f8f9fa; 
                padding: 40px; 
                box-shadow: 0 0 20px rgba(0,0,0,0.1); 
                margin: 20px auto;
                border-radius: 4px;
            }
            .no-print { 
                display: flex; 
                justify-content: center; 
                gap: 10px; 
                margin-bottom: 30px; 
                position: fixed;
                top: 20px;
                left: 0;
                right: 0;
                z-index: 1000;
            }
            .btn-print { 
                padding: 12px 24px; 
                background: #2563eb; 
                color: #fff; 
                border: none; 
                border-radius: 8px;
                cursor: pointer; 
                font-weight: bold; 
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            .btn-back {
                padding: 12px 24px;
                background: #fff;
                color: #374151;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                text-decoration: none;
                font-weight: bold;
            }
        }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 5mm; width: 100%; box-shadow: none; border: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print">
        <a href="{{ route('borrow.show', $borrow) }}" class="btn-back">KEMBALI</a>
        <button class="btn-print" onclick="window.print()">CETAK STRUK</button>
    </div>

    @php
        $appName = \App\Models\SystemSetting::get('app_name', 'SMART INVENTORY');
        $ptAddress = \App\Models\SystemSetting::get('company_address', 'Office Building, Jakarta');
        $ptPhone = \App\Models\SystemSetting::get('company_phone', '+62 21 0000 0000');
        $ptLogo = \App\Models\SystemSetting::get('app_logo');
    @endphp

    <div class="header text-center">
        @if($ptLogo)
            <img src="{{ asset('storage/' . $ptLogo) }}" class="logo">
        @endif
        <h1 class="company-name uppercase">{{ $appName }}</h1>
        <div class="company-info uppercase">
            {{ $ptAddress }}<br>
            {{ $ptPhone }}
        </div>
    </div>

    <div class="nota-info text-center">
        <div class="bold" style="font-size: 16px; border: 1px solid #000; display: inline-block; padding: 2px 10px; margin-bottom: 3mm;">STRUK PEMINJAMAN</div>
        <div class="bold">NOMOR: {{ $borrow->code }}</div>
        <div>TANGGAL: {{ $borrow->borrow_date->format('d/m/Y H:i') }}</div>
        <div class="divider"></div>
        <div class="bold">PEMINJAM:</div>
        <div class="uppercase">{{ $borrow->requester->name }}</div>
        <div class="bold">PROYEK:</div>
        <div class="uppercase">{{ $borrow->project->name }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">ITEM & QUANTITY</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrow->items as $item)
            <tr>
                <td class="text-center">
                    <span class="item-name uppercase">{{ $item->inventory->name }}</span>
                    <span class="item-qty">{{ $item->quantity }} {{ strtoupper($item->inventory->unit) }}</span>
                    <span style="font-size: 10px;">ID: {{ $item->inventory->code }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="nota-info">
        <div class="divider"></div>
        <div class="bold uppercase">BATAS PENGEMBALIAN:</div>
        <div class="bold" style="font-size: 14px;">{{ $borrow->expected_return_date->format('d/m/Y') }}</div>
        @if($borrow->notes)
        <div style="margin-top: 3mm; font-style: italic;">"{{ $borrow->notes }}"</div>
        @endif
    </div>

    <div class="footer text-center">
        <div class="divider"></div>
        <p class="bold uppercase" style="margin-bottom: 1mm;">PENTING</p>
        <p style="margin: 0; font-size: 10px;">HARAP JAGA BARANG DENGAN BAIK</p>
        <p style="margin: 0; font-size: 10px;">KERUSAKAN ADALAH TANGGUNG JAWAB PEMINJAM</p>
        
        <div class="qr-section">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('borrow.show', $borrow)) }}" class="qr-code">
            <p class="bold" style="margin-top: 2mm; font-size: 10px;">SCAN UNTUK VERIFIKASI DIGITAL</p>
        </div>
        
        <div class="divider"></div>
        <p style="font-size: 10px;">DICETAK PADA: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p class="bold" style="letter-spacing: 2px;">*** TERIMA KASIH ***</p>
    </div>
</body>
</html>

