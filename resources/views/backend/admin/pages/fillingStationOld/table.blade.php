{{--
    partials/table.blade.php
    Rendered both on initial load AND via AJAX (JSON response).
    Variable: $filteredReports  (LengthAwarePaginator)
--}}

@forelse($filteredReports as $station)
    <tr
        data-division="{{ strtolower($station->division ?? '') }}"
        data-status="{{ strtolower($station->status ?? 'active') }}"
        data-station="{{ $station->station_name }}"
        data-company="{{ strtolower($station->company->name ?? '') }}"
    >
        {{-- # --}}
        <td>
            <span class="row-index">
                {{ ($filteredReports->currentPage() - 1) * $filteredReports->perPage() + $loop->iteration }}
            </span>
        </td>

        {{-- Station Name --}}
        <td>
            <span class="station-name">{{ $station->station_name }}</span>
            {{-- <span class="station-sub">{{ $station->depot->depot_name ?? '—' }}</span> --}}
        </td>

        {{-- Code --}}
        <td><span class="badge-code">{{ $station->station_code }}</span></td>

        {{-- Location --}}
        <td>{{ $station->division ?? '—' }}</td>
        <td>{{ $station->district ?? '—' }}</td>
        <td>{{ $station->upazila ?? '—' }}</td>

        {{-- Owner --}}
        <td>
            <span class="station-name" style="font-size:.85rem;">{{ $station->owner_name ?? '—' }}</span>
            <span class="station-sub">{{ $station->owner_phone ?? '' }}</span>
        </td>

        {{-- Company --}}
        <td>
            @isset($station->company->name)
                {{ explode(' ', trim($station->company->name))[0] }}
            @else
                —
            @endisset
        </td>

        {{-- Capacity --}}
        <td>{{ $station->tank_capacity ? number_format($station->tank_capacity) . ' L' : '—' }}</td>

        {{-- Fuel Types --}}
        <td>
            <div class="d-flex flex-wrap gap-1">
                @if ($station->fuel_types)
                    @foreach ($station->fuel_types as $fuel)
                        <span class="badge-fuel">{{ $fuel }}</span>
                    @endforeach
                @else
                    <span class="station-sub">—</span>
                @endif
            </div>
        </td>

        {{-- Status --}}
        <td>
            @php $st = strtolower($station->status ?? 'active'); @endphp
            <span class="badge-status {{ $st === 'active' ? 'badge-active' : 'badge-inactive' }}">
                {{ ucfirst($st) }}
            </span>
        </td>

        {{-- Actions --}}
        <td>
            <div class="actions-cell">
                {{-- Edit --}}
                <button
                    type="button"
                    class="action-btn action-btn-edit"
                    title="Edit"
                    onclick="openEditModal({{ $station->id }})"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L7.5 19.79l-4 1 1-4 12.362-12.303z"/>
                    </svg>
                </button>

                {{-- Delete --}}
                <button
                    type="button"
                    class="action-btn action-btn-delete"
                    title="Delete"
                    onclick="deleteStation({{ $station->id }})"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                    </svg>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="12" class="text-center py-5" style="color:#94a3b8;">
            <span style="font-size:2.2rem;display:block;margin-bottom:8px;opacity:.35;">⛽</span>
            No filling stations found.
        </td>
    </tr>
@endforelse

{{-- Pagination (only rendered when there are pages) --}}
@if ($filteredReports->hasPages())
    <tr>
        <td colspan="12" style="padding:0;">
            <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">
                {{ $filteredReports->links('pagination::bootstrap-5') }}
            </div>
        </td>
    </tr>
@endif