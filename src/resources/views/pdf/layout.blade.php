<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        /* DomPDF NO entiende Tailwind ni flex/grid: se usa CSS clásico y tablas */
        @page { margin: 120px 40px 70px 40px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #222; }
        header { position: fixed; top: -95px; left: 0; right: 0; height: 80px; }
        footer { position: fixed; bottom: -50px; left: 0; right: 0; height: 30px;
                 font-size: 9px; color: #777; text-align: center; border-top: 1px solid #ccc; padding-top: 5px; }
        .company { font-size: 16px; font-weight: bold; color: #1e3a8a; }
        .subcompany { font-size: 11px; font-weight: bold; color: #475569; }
        .muted { color: #666; font-size: 10px; }
        .doc-box { border: 2px solid #1e3a8a; padding: 6px 10px; text-align: center; border-radius: 4px; }
        .doc-box .type { font-size: 10px; text-transform: uppercase; color: #1e3a8a; font-weight: bold; }
        .doc-box .number { font-size: 16px; font-weight: bold; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; }
        .info td { padding: 4px; vertical-align: top; }
        .items { margin-top: 15px; }
        .items th { background: #1e3a8a; color: #fff; padding: 6px; font-size: 10px; text-align: left; }
        .items td { padding: 5px 6px; border-bottom: 1px solid #ddd; }
        .right { text-align: right !important; }
        .totals { width: 45%; margin-left: 55%; margin-top: 10px; }
        .totals td { padding: 4px 6px; }
        .totals .grand td { font-size: 13px; font-weight: bold; border-top: 2px solid #1e3a8a; color: #1e3a8a; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 6px; background: #e2e8f0; font-size: 10px; font-weight: bold; }
        .signatures { margin-top: 50px; }
        .signatures td { width: 50%; text-align: center; padding-top: 35px; }
        .signatures span { border-top: 1px solid #333; padding: 4px 30px 0 30px; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <table>
            <tr>
                <td style="width: 65%;">
                    <div class="company">TURNIX ERP · JyM & CIA S.A.S.</div>
                    <div class="subcompany">Barbería y Perfumería JyM · Distribuidora Cosméticos del Eje</div>
                    <div class="muted">NIT 900.887.654-2 · Cartago, Valle del Cauca · Colombia</div>
                    <div class="muted">Tel. (602) 214-5500 · Cel. 320 665 4433 · contacto@turnixerp.co</div>
                </td>
                <td style="width: 35%;">
                    <div class="doc-box">
                        <div class="type">@yield('doc_type')</div>
                        <div class="number">@yield('doc_number')</div>
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        Documento oficial generado por Turnix ERP el {{ now()->format('d/m/Y H:i') }} — Software de Gestión Empresarial · COTECNOVA
    </footer>

    <main>
        @yield('content')
    </main>
</body>
</html>
