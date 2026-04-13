<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        * {
            font-family: 'SolaimanLipi', sans-serif;
            box-sizing: border-box;
        }

        body {
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 10px;
        }

        .report-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #0f4c81;
        }

        .report-meta {
            text-align: center;
            font-size: 9px;
            color: #64748b;
            margin-bottom: 14px;
        }

        .filter-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            font-size: 9px;
            color: #475569;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead tr {
            background-color: #0f4c81;
        }

        thead th {
            padding: 6px 5px;
            font-size: 8px;
            font-weight: bold;
            text-align: left;
            border: 1px solid #0a3d6e;
            word-wrap: break-word;
            background-color: #0f4c81;
            color: #ffffff;
        }

        thead th small {
            display: block;
            font-size: 7px;
            font-weight: normal;
            margin-top: 2px;
            color: #ffffff;
        }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr.is-first-fuel-row td {
            border-top: 2px solid #cbd5e1;
        }

        tbody td {
            padding: 5px;
            vertical-align: middle;
            border: 1px solid #e2e8f0;
            word-wrap: break-word;
            color: #1e293b;
        }

        tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .cell-serial {
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            font-weight: 600;
        }

        .station-name {
            font-weight: 700;
            font-size: 9px;
            color: #1e293b;
        }

        .station-sub {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 1px;
        }

        .date-range {
            font-weight: 600;
            font-size: 9px;
            color: #1e293b;
        }

        .date-sub {
            font-size: 8px;
            color: #94a3b8;
        }

        .fuel-badge {
            padding: 2px 6px;
            font-size: 8px;
            font-weight: 700;
        }

        .fuel-diesel {
            background-color: #f0fdf4;
            color: #15803d;
        }

        .fuel-petrol {
            background-color: #f5f3ff;
            color: #6d28d9;
        }

        .fuel-octane {
            background-color: #fff7ed;
            color: #c2410c;
        }

        .fuel-others {
            background-color: #f8fafc;
            color: #64748b;
        }

        .cell-supply {
            color: #15803d;
            font-weight: 600;
        }

        .cell-sales {
            color: #6c6c6c;
            font-weight: 600;
        }

        .cell-closing {
            color: #1e293b;
            font-weight: 700;
        }

        .diff-positive {
            color: #dc2626;
            font-weight: 700;
        }

        .diff-negative {
            color: #dc2626;
            font-weight: 700;
        }

        .diff-zero {
            color: #94a3b8;
        }

        .status-available {
            color: #15803d;
            font-weight: 600;
        }

        .status-low {
            color: #b45309;
            font-weight: 600;
        }

        .status-zero {
            color: #b91c1c;
            font-weight: 600;
        }

        .status-highdiff {
            color: #b91c1c;
            font-weight: 600;
        }

        .fuel-total-row td {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 9px;
            border-top: 1px solid #cbd5e1;
        }

        .grand-total-row td {
            background-color: #0f4c81;
            color: #ffffff;
            font-weight: 700;
            font-size: 9px;
            border-top: 2px solid #0a3d6e;
        }

        .footer {
            margin-top: 14px;
            font-size: 8px;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>

<body>

    @php
        use Carbon\Carbon;

        $fuelTypes = [
            'diesel' => ['label' => 'Diesel', 'css' => 'fuel-diesel'],
            'petrol' => ['label' => 'Petrol', 'css' => 'fuel-petrol'],
            'octane' => ['label' => 'Octane', 'css' => 'fuel-octane'],
            'others' => ['label' => 'Others', 'css' => 'fuel-others'],
        ];

        function pdfFormatNumber(float $value): string
        {
            return $value != 0 ? number_format($value, 2, '.', ',') : '0';
        }

        function pdfFormatDiff(float $value): string
        {
            if ($value == 0) {
                return '0';
            }
            return number_format($value, 2, '.', ',');
        }

        function pdfDiffClass(float $value): string
        {
            if ($value == 0) {
                return 'diff-zero';
            }
            return $value > 0 ? 'diff-positive' : 'diff-negative';
        }
    @endphp

    <div class="report-title">Stock &amp; Sales Report</div>
    <div class="report-meta">
        Generated: {{ now()->format('d M Y, h:i A') }}
        @if (!empty($filters['from_date']))
            &nbsp;|&nbsp; From: {{ $filters['from_date'] }}
        @endif
        @if (!empty($filters['to_date']))
            &nbsp;|&nbsp; To: {{ $filters['to_date'] }}
        @endif
    </div>

    @php $activeFilters = array_filter($filters ?? []); @endphp
    @if (count($activeFilters))
        <div class="filter-info">
            <strong>Filters:</strong>
            @foreach ($activeFilters as $key => $val)
                {{ ucfirst(str_replace('_', ' ', $key)) }}: <strong>{{ $val }}</strong> &nbsp;
            @endforeach
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:8%">Date<small>From - To</small></th>
                <th style="width:12%">Filling Station</th>
                <th style="width:9%">Company</th>
                <th style="width:8%">Tag Officer</th>
                <th style="width:5%">Fuel</th>
                <th style="width:7%">Prev. Stock (L)<small>Opening</small></th>
                <th style="width:7%">Supply (L)<small>From Depot</small></th>
                <th style="width:7%">Received At Station (L)<small>At Station</small></th>
                <th style="width:7%">Difference (L)<small>Supply - Received</small></th>
                <th style="width:7%">Sales (L)<small>Total</small></th>
                <th style="width:7%">Closing Stock (L)<small>Last date</small></th>
                <th style="width:6%">Status</th>
                <th style="width:8%">Comment</th>
            </tr>
        </thead>
        <tbody>

            @php $counter = 0; @endphp

            @foreach ($reports as $report)
                @php
                    $counter++;
                    $fuelCount = count($fuelTypes);
                    $fuelIndex = 0;

                    $dateFrom = $report['report_date_from']
                        ? Carbon::parse($report['report_date_from'])->format('d M Y')
                        : '-';
                    $dateTo = $report['report_date_to']
                        ? Carbon::parse($report['report_date_to'])->format('d M Y')
                        : '-';
                    $isSingleDate = $report['report_date_from'] === $report['report_date_to'];
                @endphp

                @foreach ($fuelTypes as $fuelKey => $fuelMeta)
                    @php
                        $isFirst = $fuelIndex === 0;
                        $currentFuelStatus = $report['fuel_statuses'][$fuelKey] ?? [
                            'label' => '-',
                            'css' => 'status-available',
                        ];
                    @endphp

                    <tr class="{{ $isFirst ? 'is-first-fuel-row' : '' }}">

                        @if ($isFirst)
                            <td class="cell-serial" rowspan="{{ $fuelCount }}">{{ $counter }}</td>

                            <td rowspan="{{ $fuelCount }}">
                                <div class="date-range">{{ $dateFrom }}</div>
                                @if (!$isSingleDate)
                                    <div class="date-sub">to {{ $dateTo }}</div>
                                @endif
                            </td>

                            <td rowspan="{{ $fuelCount }}">
                                <div class="station-name">{{ $report['station_name'] }}</div>
                                <div class="station-sub">{{ $report['district'] }}</div>
                            </td>

                            <td rowspan="{{ $fuelCount }}">{{ $report['company_name'] }}</td>
                            <td rowspan="{{ $fuelCount }}">{{ $report['tag_officer'] }}</td>
                        @endif

                        <td>
                            <span class="fuel-badge {{ $fuelMeta['css'] }}">{{ $fuelMeta['label'] }}</span>
                        </td>

                        <td>{{ '-' }}</td>
                        <td class="cell-supply">{{ pdfFormatNumber($report[$fuelKey . '_supply'] ?? 0) }}</td>
                        <td>{{ pdfFormatNumber($report[$fuelKey . '_received'] ?? 0) }}</td>
                        <td class="{{ pdfDiffClass($report[$fuelKey . '_difference'] ?? 0) }}">
                            {{ pdfFormatDiff($report[$fuelKey . '_difference'] ?? 0) }}
                        </td>
                        <td class="cell-sales">{{ pdfFormatNumber($report[$fuelKey . '_sales'] ?? 0) }}</td>
                        <td class="cell-closing">{{ pdfFormatNumber($report[$fuelKey . '_closing_stock'] ?? 0) }}</td>

                        <td>
                            <span class="{{ $currentFuelStatus['css'] }}" style="font-size:8px; font-weight:600;">
                                {{ $currentFuelStatus['label'] }}
                            </span>
                        </td>

                        @if ($isFirst)
                            <td rowspan="{{ $fuelCount }}" style="font-size:8px; color:#475569;">
                                {{ $report['comment'] ?: '-' }}
                            </td>
                        @endif

                    </tr>

                    @php $fuelIndex++; @endphp
                @endforeach
            @endforeach

            {{-- Fuel-wise totals --}}
            @if (isset($totalRow) && $totalRow)
                @foreach ($fuelTypes as $fuelKey => $fuelMeta)
                    <tr class="fuel-total-row">
                        <td colspan="6" style="text-align:right; padding-right:8px;">
                            <strong>{{ ucfirst($fuelKey) }} Total:</strong>
                        </td>
                        {{-- <td>{{ pdfFormatNumber($totalRow["{$fuelKey}_prev_stock"] ?? 0) }}</td> --}}
                        <td>{{ '' }}</td>
                        <td>{{ pdfFormatNumber($totalRow["{$fuelKey}_supply"] ?? 0) }}</td>
                        <td>{{ pdfFormatNumber($totalRow["{$fuelKey}_received"] ?? 0) }}</td>
                        <td>{{ pdfFormatDiff($totalRow["{$fuelKey}_difference"] ?? 0) }}</td>
                        <td>{{ pdfFormatNumber($totalRow["{$fuelKey}_sales"] ?? 0) }}</td>
                        <td>{{ pdfFormatNumber($totalRow["{$fuelKey}_closing_stock"] ?? 0) }}</td>
                        <td colspan="2"></td>
                    </tr>
                @endforeach

                @php
                    $gPrev = $gSupply = $gReceived = $gDiff = $gSales = $gClosing = 0;
                    foreach ($fuelTypes as $fk => $fm) {
                        $gPrev += (float) ($totalRow["{$fk}_prev_stock"] ?? 0);
                        $gSupply += (float) ($totalRow["{$fk}_supply"] ?? 0);
                        $gReceived += (float) ($totalRow["{$fk}_received"] ?? 0);
                        $gDiff += abs((float) ($totalRow["{$fk}_difference"] ?? 0));
                        $gSales += (float) ($totalRow["{$fk}_sales"] ?? 0);
                        $gClosing += (float) ($totalRow["{$fk}_closing_stock"] ?? 0);
                    }
                @endphp

                <tr class="grand-total-row">
                    <td colspan="6" style="text-align:right; padding-right:8px;">
                        GRAND TOTAL (All Fuels)
                    </td>
                    {{-- <td>{{ pdfFormatNumber($gPrev) }}</td> --}}
                    <td>{{ '' }}</td>
                    <td>{{ pdfFormatNumber($gSupply) }}</td>
                    <td>{{ pdfFormatNumber($gReceived) }}</td>
                    <td>{{ pdfFormatDiff($gDiff) }}</td>
                    <td>{{ pdfFormatNumber($gSales) }}</td>
                    <td>{{ pdfFormatNumber($gClosing) }}</td>
                    <td colspan="2"></td>
                </tr>
            @endif

        </tbody>
    </table>

    <div class="footer">
        Total records: {{ $reports->count() }} &nbsp;|&nbsp;
        Printed: {{ now()->format('d/m/Y h:i A') }}
    </div>

</body>

</html>
