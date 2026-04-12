{{--
    table.blade.php  (reports partial)
    ───────────────────────────────────
    Variables received:
      $reports      → Collection of formatted aggregated report arrays
      $totalRow     → array with grand totals (null = no data)
      $currentPage  → int
      $lastPage     → int
      $total        → int  (total station count)
      $filters      → array of active filter params (for pagination links)
--}}

@php
    use Carbon\Carbon;

    $fuelTypes = [
        'octane' => ['label' => 'Octane', 'css' => 'fuel-octane'],
        'petrol' => ['label' => 'Petrol', 'css' => 'fuel-petrol'],
        'diesel' => ['label' => 'Diesel', 'css' => 'fuel-diesel'],
        'others' => ['label' => 'Others', 'css' => 'fuel-others'],
    ];

    /**
     * Format a number — "—" for zero/null.
     */
    function formatNumber(float $value): string
    {
        return $value != 0 ? number_format($value, 2, '.', ',') : '—';
    }

    /**
     * Format a difference value.
     * Positive = supply > received (loss), shown in red.
     * Negative = received > supply (surplus).
     */
    function formatDifference(float $value): string
    {
        if ($value == 0) {
            return '—';
        }
        return ($value > 0 ? '' : '') . number_format($value, 2, '.', ',');
    }

    /**
     * CSS class for a difference cell.
     */
    function differenceClass(float $value): string
    {
        if ($value == 0) {
            return 'diff-zero';
        }
        return $value > 0 ? 'diff-positive' : 'diff-negative';
    }

    $filters = $filters ?? [];
    $currentPage = $currentPage ?? 1;
    $lastPage = $lastPage ?? 1;
    $total = $total ?? 0;
@endphp


<style>
    .tbl-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: auto;
        max-height: 600px;
        position: relative;
    }

    .tbl-wrap::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }

    .tbl-wrap::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .tbl-wrap::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: .80rem;
        min-width: 900px;
        position: relative;
    }

    thead tr {
        background: #f8fafc;
    }

    thead th {
        padding: 8px 6px;
        text-align: left;
        font-size: .67rem;
        font-weight: 700;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: .55px;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
        position: sticky;
        top: 0;
        background: #f8fafc;
        z-index: 10;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }

    thead th small {
        display: block;
        font-size: .60rem;
        color: #1e293b;
        font-weight: 500;
        letter-spacing: 0;
        text-transform: none;
        margin-top: 2px;
    }

    tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background .1s;
    }

    tbody tr:hover {
        background: #f8faff;
    }

    tbody tr.is-first-fuel-row td {
        border-top: 1.5px solid #e2e8f0;
    }

    tbody td {
        padding: 9px 12px;
        vertical-align: middle;
    }

    .cell-serial {
        font-size: .75rem;
        font-weight: 600;
        color: #94a3b8;
        text-align: center;
    }

    .cell-date .date-range {
        font-size: .79rem;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
    }

    .cell-date .date-single {
        font-size: .79rem;
        font-weight: 600;
        color: #1e293b;
    }

    .cell-date .date-sub {
        font-size: .68rem;
        color: #94a3b8;
        margin-top: 3px;
    }

    .cell-station .station-name {
        font-size: .82rem;
        font-weight: 700;
        color: #1e293b;
    }

    .cell-station .station-sub {
        font-size: .70rem;
        color: #94a3b8;
        margin-top: 2px;
    }

    .cell-company {
        font-size: .80rem;
        font-weight: 600;
        color: #334155;
    }

    .cell-officer {
        font-size: .80rem;
        /* color: #2563eb; */
        font-weight: 600;
    }

    .fuel-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 11px;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    /* .fuel-diesel { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .fuel-petrol { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }
    .fuel-octane { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .fuel-others { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; } */

    .cell-supply {
        color: #15803d;
        font-weight: 600;
    }

    .cell-sales {
        color: #6c6c6c;
        font-weight: 600;
    }

    .cell-closing {
        font-weight: 700;
        color: #1e293b;
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

    .status-label {
        font-size: .75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-available {
        color: #15803d;
    }

    .status-low {
        color: #b45309;
    }

    .status-zero {
        color: #b91c1c;
    }

    .status-highdiff {
        color: #b91c1c;
    }

    .cell-comment {
        font-size: .74rem;
        color: #94a3b8;
        font-style: italic;
    }

    .cell-comment.has-text {
        color: #475569;
        font-style: normal;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 90px;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: .73rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: opacity .15s;
        white-space: nowrap;
    }

    .btn-action:hover {
        opacity: .85;
    }

    .btn-view {
        background: #009433;
        color: #fff;
    }

    .btn-message {
        background: #046694;
        color: #fff;
    }

    .btn-delete {
        background: #fc6061;
        color: #fff;
    }

    /* Grand total row */
    .totals-row td {
        background: #1a5c96;
        color: #fff;
        font-weight: 700;
        font-size: .80rem;
        border-top: 2px solid #134a7c;
    }

    /* Empty state */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 2rem;
        opacity: .22;
        display: block;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: .88rem;
        font-weight: 500;
    }

    .empty-state small {
        font-size: .75rem;
        margin-top: 4px;
        display: block;
    }

    /* Pagination */
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 4px 4px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .pagination-info {
        font-size: .75rem;
        color: #94a3b8;
    }

    .pagination-links {
        display: flex;
        gap: 4px;
    }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: .78rem;
        font-weight: 600;
        color: #475569;
        background: #fff;
        cursor: pointer;
        transition: all .14s;
    }

    .page-btn:hover:not(:disabled):not(.is-active) {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .page-btn.is-active {
        background: #0f4c81;
        color: #fff;
        border-color: #0f4c81;
    }

    .page-btn:disabled {
        opacity: .4;
        cursor: not-allowed;
    }
</style>

@if ($reports->isEmpty())

    {{-- ── Empty / no-filter state ── --}}
    <div class="empty-state">
        <i class="fa-solid fa-filter"></i>
        <p>Apply filters to load reports</p>
        <small>Select date range, station, or any filter then click Apply</small>
    </div>
@else
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        DATE RANGE
                        <small>From → To</small>
                    </th>
                    <th>FILLING STATION</th>
                    <th>COMPANY</th>
                    <th>TAG OFFICER</th>
                    <th>FUEL</th>
                    <th>
                        PREV. STOCK (L)
                        <small>Opening of period</small>
                    </th>
                    <th>
                        SUPPLY FROM DEPOT (L)
                        <small>Total in range</small>
                    </th>
                    <th>
                        RECEIVED AT STATION (L)
                        <small>Total in range</small>
                    </th>
                    <th>
                        DIFFERENCE (L)
                        <small>Supply − Received</small>
                    </th>
                    <th>
                        SALES (L)
                        <small>Total in range</small>
                    </th>
                    <th>
                        CLOSING STOCK (L)
                        <small>Last date only</small>
                    </th>
                    <th>STATUS</th>
                    <th>COMMENT</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @php $serialCounter = ($currentPage - 1) * 10; @endphp
                @foreach ($reports as $serialOffset => $report)
                    @php
                        // Serial number accounts for pagination offset
                        // $serialNumber = ($currentPage - 1) * 10 + $serialOffset + 1;
                        $serialCounter++;
                        $serialNumber = $serialCounter;

                        $fuelCount = count($fuelTypes);
                        $fuelIndex = 0;

                        // Date range display
                        $dateFrom = $report['report_date_from']
                            ? Carbon::parse($report['report_date_from'])->format('d M Y')
                            : '—';
                        $dateTo = $report['report_date_to']
                            ? Carbon::parse($report['report_date_to'])->format('d M Y')
                            : '—';

                        // Single date if from = to
                        $isSingleDate = $report['report_date_from'] === $report['report_date_to'];
                    @endphp

                    @foreach ($fuelTypes as $fuelKey => $fuelMeta)
                        @php
                            $isFirstFuelRow = $fuelIndex === 0;
                            $currentFuelStatus = $report['fuel_statuses'][$fuelKey] ?? [
                                'label' => '—',
                                'css' => 'status-available',
                            ];
                        @endphp

                        <tr class="{{ $isFirstFuelRow ? 'is-first-fuel-row' : '' }}">

                            {{-- ── Grouped cells (first fuel row only) ── --}}
                            @if ($isFirstFuelRow)
                                <td class="cell-serial" rowspan="{{ $fuelCount }}">
                                    {{ $serialNumber }}
                                </td>

                                <td class="cell-date" rowspan="{{ $fuelCount }}">
                                    @if ($isSingleDate)
                                        <div class="date-single">{{ $dateFrom }}</div>
                                        <div class="date-sub">
                                            {{ Carbon::parse($report['report_date_from'])->format('l') }}
                                        </div>
                                    @else
                                        <div class="date-range">{{ $dateFrom }}</div>
                                        <div class="date-sub">→ {{ $dateTo }}</div>
                                    @endif
                                </td>

                                <td class="cell-station" rowspan="{{ $fuelCount }}">
                                    <div class="station-name">{{ $report['station_name'] }}</div>
                                    <div class="station-sub">{{ $report['district'] }}</div>
                                </td>

                                <td class="cell-company" rowspan="{{ $fuelCount }}">
                                    {{ $report['company_name'] }}
                                </td>

                                <td class="cell-officer" rowspan="{{ $fuelCount }}">
                                    {{ $report['tag_officer'] }}
                                </td>
                            @endif

                            {{-- ── Per-fuel data cells ── --}}
                            <td>
                                <span class="fuel-badge {{ $fuelMeta['css'] }}">
                                    {{ $fuelMeta['label'] }}
                                </span>
                            </td>

                            <td>{{ formatNumber($report[$fuelKey . '_prev_stock'] ?? 0) }}</td>

                            <td class="cell-supply">
                                {{ formatNumber($report[$fuelKey . '_supply'] ?? 0) }}
                            </td>

                            <td>{{ formatNumber($report[$fuelKey . '_received'] ?? 0) }}</td>

                            <td class="{{ differenceClass($report[$fuelKey . '_difference'] ?? 0) }}">
                                {{ formatDifference($report[$fuelKey . '_difference'] ?? 0) }}
                            </td>

                            <td class="cell-sales">
                                {{ formatNumber($report[$fuelKey . '_sales'] ?? 0) }}
                            </td>

                            <td class="cell-closing">
                                {{ formatNumber($report[$fuelKey . '_closing_stock'] ?? 0) }}
                            </td>

                            {{-- Per-fuel status --}}
                            <td>
                                <span class="status-label {{ $currentFuelStatus['css'] }}">
                                    {{ $currentFuelStatus['label'] }}
                                </span>
                            </td>

                            {{-- ── Grouped cells continued ── --}}
                            @if ($isFirstFuelRow)
                                <td class="cell-comment {{ $report['comment'] ? 'has-text' : '' }}"
                                    rowspan="{{ $fuelCount }}">
                                    {{ $report['comment'] ?: 'No comments' }}
                                </td>

                                <td rowspan="{{ $fuelCount }}">
                                    <div class="action-buttons">

                                        <button class="btn-action btn-view" type="button"
                                            @click.prevent="">
                                            <i class="fa-solid fa-eye"></i> View
                                        </button>

                                        <button class="btn-action btn-message" type="button"
                                            @click.prevent="">
                                            <i class="fa-solid fa-message"></i> Message
                                        </button>

                                        <button class="btn-action btn-delete" type="button"
                                            @click.prevent="">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>

                                    </div>
                                </td>
                            @endif

                        </tr>

                        @php $fuelIndex++; @endphp
                    @endforeach
                @endforeach

                {{-- ── Grand Totals Row ── --}}
                {{-- ==================== FUEL-WISE TOTAL ROWS ==================== --}}
                {{-- ==================== FUEL-WISE TOTALS + GRAND TOTAL ==================== --}}
                @if (isset($totalRow) && $totalRow)

                    {{-- 1. Fuel-wise Total Rows --}}
                    @foreach ($fuelTypes as $fuelKey => $fuelMeta)
                        <tr class="totals-row fuel-total-row" style="background-color: #f8f9fa; font-weight: 600;">

                            <td colspan="6" class="text-end pe-4">
                                <strong>{{ ucfirst($fuelKey) }} Total:</strong>
                            </td>

                            <td class="">{{ formatNumber($totalRow["{$fuelKey}_prev_stock"] ?? 0) }}</td>
                            <td class="">{{ formatNumber($totalRow["{$fuelKey}_supply"] ?? 0) }}</td>
                            <td class="">{{ formatNumber($totalRow["{$fuelKey}_received"] ?? 0) }}</td>
                            <td class=" {{ ($totalRow["{$fuelKey}_difference"] ?? 0) != 0 ? 'text-warning' : '' }}">
                                {{ formatDifference($totalRow["{$fuelKey}_difference"] ?? 0) }}
                            </td>
                            <td class="">{{ formatNumber($totalRow["{$fuelKey}_sales"] ?? 0) }}</td>
                            <td class="">{{ formatNumber($totalRow["{$fuelKey}_closing_stock"] ?? 0) }}</td>

                            <td colspan="3"></td>
                        </tr>
                    @endforeach

                    {{-- 2. Grand Total Row (All Fuels Combined) --}}
                    @php
                        $grandPrev = 0;
                        $grandSupply = 0;
                        $grandReceived = 0;
                        $grandDiff = 0;
                        $grandSales = 0;
                        $grandClosing = 0;

                        foreach ($fuelTypes as $fuelKey => $fuelMeta) {
                            $grandPrev += (float) ($totalRow["{$fuelKey}_prev_stock"] ?? 0);
                            $grandSupply += (float) ($totalRow["{$fuelKey}_supply"] ?? 0);
                            $grandReceived += (float) ($totalRow["{$fuelKey}_received"] ?? 0);
                            $grandDiff += abs((float) ($totalRow["{$fuelKey}_difference"] ?? 0));
                            $grandSales += (float) ($totalRow["{$fuelKey}_sales"] ?? 0);
                            $grandClosing += (float) ($totalRow["{$fuelKey}_closing_stock"] ?? 0);
                        }
                    @endphp

                    <tr class="grand-total-row" style="background-color: #3766ac; color: white; font-weight: bold;">
                        <td colspan="6" class="text-end fw-bold">
                            GRAND TOTAL (All Fuels)
                        </td>
                        <td class="">{{ formatNumber($grandPrev) }}</td>
                        <td class="">{{ formatNumber($grandSupply) }}</td>
                        <td class="">{{ formatNumber($grandReceived) }}</td>
                        <td class=" {{ $grandDiff > 0 ? 'text-warning' : '' }}">
                            {{ formatDifference($grandDiff) }}
                        </td>
                        <td class="">{{ formatNumber($grandSales) }}</td>
                        <td class="">{{ formatNumber($grandClosing) }}</td>
                        <td colspan="3"></td>
                    </tr>

                @endif

            </tbody>
        </table>
    </div>

    {{-- ── Pagination ── --}}
    @if ($lastPage > 1)
        <div class="pagination-bar">
            <div class="pagination-info">
                Showing page {{ $currentPage }} of {{ $lastPage }}
                ({{ $total }} station{{ $total !== 1 ? 's' : '' }})
            </div>
            <div class="pagination-links">

                {{-- Previous --}}
                <button class="page-btn" @click="goToPage({{ $currentPage - 1 }})"
                    @if ($currentPage <= 1) disabled @endif>
                    <i class="fa-solid fa-chevron-left" style="font-size:.65rem;"></i>
                </button>

                {{-- Page numbers --}}
                @for ($pageNum = max(1, $currentPage - 2); $pageNum <= min($lastPage, $currentPage + 2); $pageNum++)
                    <button class="page-btn {{ $pageNum === $currentPage ? 'is-active' : '' }}"
                        @click="goToPage({{ $pageNum }})">
                        {{ $pageNum }}
                    </button>
                @endfor

                {{-- Next --}}
                <button class="page-btn" @click="goToPage({{ $currentPage + 1 }})"
                    @if ($currentPage >= $lastPage) disabled @endif>
                    <i class="fa-solid fa-chevron-right" style="font-size:.65rem;"></i>
                </button>

            </div>
        </div>
    @endif

@endif
