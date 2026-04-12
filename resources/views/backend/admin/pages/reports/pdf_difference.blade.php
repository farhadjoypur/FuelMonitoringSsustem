<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        * {
            font-family: 'SolaimanLipi', sans-serif;
            box-sizing: border-box;
            font-weight: normal !important;
        }

        body {
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 10px;
            font-weight: normal !important;
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

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead th {
            padding: 6px 5px;
            font-size: 8px;
            font-weight: bold;
            text-align: left;
            border: 1px solid #0a3d6e;
            background-color: #0f4c81;
            color: #ffffff;
            word-wrap: break-word;
        }

        thead th small {
            display: block;
            font-size: 7px;
            font-weight: normal;
            margin-top: 2px;
            color: #ffffff;
        }

        tbody td {
            padding: 5px;
            vertical-align: top;
            border: 1px solid #e2e8f0;
            font-size: 8px;
            color: #1e293b;
            word-wrap: break-word;
        }

        tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .fuel-row {
            padding: 3px 0;
            border-bottom: 1px dashed #e2e8f0;
            min-height: 18px;
        }

        .fuel-row:last-child {
            border-bottom: none;
        }

        .diff-high {
            color: #dc2626;
            font-weight: 700;
        }

        .diff-low {
            color: #c2410c;
            font-weight: 700;
        }

        .diff-normal {
            color: #15803d;
            font-weight: 600;
        }

        .diff-nonzero {
            color: #dc2626;
            font-weight: 700;
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

    <div class="report-title">Difference Report</div>
    <div class="report-meta">
        Generated: {{ now()->format('d M Y, h:i A') }}
        @if (!empty($filters['from_date']))
            &nbsp;|&nbsp; From: {{ $filters['from_date'] }}
        @endif
        @if (!empty($filters['to_date']))
            &nbsp;|&nbsp; To: {{ $filters['to_date'] }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:9%">Date</th>
                <th style="width:13%">Station</th>
                <th style="width:7%">Company</th>
                <th style="width:11%">Tag Officer</th>
                <th style="width:10%">Designation</th>
                <th style="width:9%">Phone</th>
                <th style="width:8%">District</th>
                <th style="width:7%">Upazila</th>
                <th style="width:6%">Fuel</th>
                <th style="width:8%">Diff (L)</th>
                <th style="width:7%">Diff (%)</th>
                <th style="width:8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $i => $row)
                <tr>
                    <td style="text-align:center; color:#94a3b8; font-weight:600;">{{ $i + 1 }}</td>
                    <td>{{ $row['dateFormatted'] }}</td>
                    <td><strong>{{ $row['stationName'] }}</strong></td>
                    <td>{{ $row['companyName'] }}</td>
                    <td>{{ $row['tagOfficerName'] }}</td>
                    <td>{{ $row['officerDesignation'] }}</td>
                    <td>{{ $row['officerPhone'] }}</td>
                    <td>{{ $row['district'] }}</td>
                    <td>{{ $row['thanaUpazila'] }}</td>

                    <td>
                        @foreach ($row['fuelBreakdown'] as $fuel)
                            <div class="fuel-row">{{ $fuel['fuelType'] }}</div>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($row['fuelBreakdown'] as $fuel)
                            <div class="fuel-row {{ $fuel['differenceL'] != '0' ? 'diff-nonzero' : '' }}">
                                {{ $fuel['differenceL'] }}
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($row['fuelBreakdown'] as $fuel)
                            <div class="fuel-row {{ $fuel['differencePercent'] != 0 ? 'diff-nonzero' : '' }}">
                                {{ $fuel['differencePercent'] }}%
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($row['fuelBreakdown'] as $fuel)
                            <div class="fuel-row">
                                <span
                                    class="{{ $fuel['diffStatus'] === 'High' ? 'diff-high' : ($fuel['diffStatus'] === 'Low' ? 'diff-low' : 'diff-normal') }}">
                                    {{ $fuel['diffStatus'] }}
                                </span>
                            </div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Total records: {{ $rows->count() }} &nbsp;|&nbsp; Printed: {{ now()->format('d/m/Y h:i A') }}
    </div>
</body>

</html>
