@php
    $isEdit = $flete->exists;
    $fechaFlete = old('fecha', optional($flete->fecha)->format('Y-m-d') ?: now()->format('Y-m-d'));
    $selectedClienteId = (string) old('cliente_id', $flete->cliente_id);
    $selectedCliente = $clientesOptions->firstWhere('id', $selectedClienteId);
    $clienteSearchValue = old('cliente_nombre_busqueda', $selectedCliente?->nombre ?? '');
    $clientesAutocomplete = $clientesOptions->map(fn ($cliente) => [
        'id' => (string) $cliente->id,
        'name' => $cliente->nombre,
    ])->values();

    $oldItems = old('items');
    if (is_array($oldItems) && count($oldItems) > 0) {
        $itemsData = collect($oldItems)->map(fn ($item) => [
            'fecha' => $item['fecha'] ?? '',
            'descripcion' => $item['descripcion'] ?? '',
            'servicio' => $item['servicio'] ?? '',
            'flete' => $item['flete'] ?? '',
            'total' => $item['total'] ?? '',
        ])->values()->all();
    } elseif ($flete->relationLoaded('items') && $flete->items->isNotEmpty()) {
        $itemsData = $flete->items->map(fn ($item) => [
            'fecha' => optional($item->fecha)->format('Y-m-d') ?? '',
            'descripcion' => $item->descripcion ?? '',
            'servicio' => number_format((float) $item->servicio, 2, '.', ''),
            'flete' => number_format((float) $item->flete, 2, '.', ''),
            'total' => number_format((float) $item->total, 2, '.', ''),
        ])->values()->all();
    } else {
        $itemsData = [[
            'fecha' => $fechaFlete,
            'descripcion' => '',
            'servicio' => '',
            'flete' => '',
            'total' => '',
        ]];
    }

    $totalFletePreview = collect($itemsData)->sum(fn ($item) => (float) ($item['total'] ?: 0));
@endphp

<div class="flete-form space-y-5">
    {{-- Cliente y fecha --}}
    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label for="cliente_search" class="flete-label">Cliente</label>
            <div class="relative" data-autocomplete-root="cliente">
                <input type="hidden" id="cliente_id" name="cliente_id" value="{{ $selectedClienteId }}">
                <input
                    id="cliente_search"
                    name="cliente_nombre_busqueda"
                    type="text"
                    autocomplete="off"
                    spellcheck="false"
                    value="{{ $clienteSearchValue }}"
                    placeholder="Escribe para buscar cliente"
                    class="flete-field"
                    data-autocomplete-input="cliente"
                >
                <div id="cliente_results" class="absolute z-20 mt-1 hidden w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                    <div class="max-h-56 overflow-y-auto" data-autocomplete-list="cliente"></div>
                    <div class="hidden border-t border-slate-100 px-3 py-2 text-sm text-slate-500" data-autocomplete-empty="cliente">
                        No se encontraron clientes. Se creará con este nombre al guardar.
                    </div>
                </div>
            </div>
            @error('cliente_id')
                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
            @enderror
            @error('cliente_nombre_busqueda')
                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="fecha" class="flete-label">Fecha</label>
            <input id="fecha" name="fecha" type="date" value="{{ $fechaFlete }}" class="flete-field">
            @error('fecha')
                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Tabla de productos estilo Excel --}}
    <div>
        <div class="mb-3 flex items-center justify-between gap-3">
            <span class="flete-label mb-0">Productos</span>
            <button type="button" id="add-item-row" class="flete-btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Agregar fila
            </button>
        </div>

        @error('items')
            <p class="mb-2 text-sm text-rose-500">{{ $message }}</p>
        @enderror

        <div class="flete-excel-wrap -mx-1 overflow-x-auto sm:mx-0">
            <table class="flete-excel-table">
                <thead>
                    <tr>
                        <th class="col-fecha">F.</th>
                        <th class="col-desc">Descripción</th>
                        <th class="col-num">Servicio</th>
                        <th class="col-num">Flete</th>
                        <th class="col-num">Total</th>
                        <th class="col-del"></th>
                    </tr>
                </thead>
                <tbody id="items-tbody">
                    @foreach ($itemsData as $index => $item)
                        <tr class="item-row" data-index="{{ $index }}">
                            <td>
                                <input type="date" name="items[{{ $index }}][fecha]" value="{{ $item['fecha'] }}" class="js-item-input flete-cell-input flete-cell-date">
                                @error("items.{$index}.fecha")
                                    <p class="px-1 text-xs text-rose-500">{{ $message }}</p>
                                @enderror
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][descripcion]" value="{{ $item['descripcion'] }}" placeholder="Descripción" class="js-item-input flete-cell-input">
                            </td>
                            <td>
                                <input type="number" min="0" step="0.01" inputmode="decimal" name="items[{{ $index }}][servicio]" value="{{ $item['servicio'] }}" placeholder="0.00" class="js-item-servicio flete-cell-input flete-cell-num">
                            </td>
                            <td>
                                <input type="number" min="0" step="0.01" inputmode="decimal" name="items[{{ $index }}][flete]" value="{{ $item['flete'] }}" placeholder="0.00" class="js-item-flete flete-cell-input flete-cell-num">
                            </td>
                            <td>
                                <input type="number" min="0" step="0.01" inputmode="decimal" name="items[{{ $index }}][total]" value="{{ $item['total'] }}" placeholder="0.00" class="js-item-total flete-cell-input flete-cell-num">
                            </td>
                            <td class="col-del">
                                <button type="button" class="js-remove-row flete-remove-btn" title="Eliminar fila" aria-label="Eliminar fila">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Total general --}}
    <div class="flete-total-bar">
        <span class="text-base font-bold text-slate-700 sm:text-lg">Total general</span>
        <div class="flex items-center gap-1 text-xl font-extrabold text-slate-900 sm:text-2xl">
            <span>S/</span>
            <span id="total-flete-preview">{{ number_format($totalFletePreview, 2, '.', ',') }}</span>
        </div>
    </div>

    <div class="flex flex-col gap-3 pt-1 sm:flex-row sm:items-center">
        <button type="submit" class="flete-btn-primary">
            {{ $isEdit ? 'Actualizar' : 'Guardar' }}
        </button>
        <a href="{{ route('fletes.index') }}" class="flete-btn-cancel">
            Cancelar
        </a>
    </div>
</div>

<style>
    .flete-form .flete-label {
        display: block;
        margin-bottom: 0.375rem;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #475569;
    }

    .flete-form .flete-field {
        width: 100%;
        border-radius: 0.75rem;
        border: 1px solid #cbd5e1;
        background: #fff;
        padding: 0.875rem 1rem;
        font-size: 1.0625rem;
        line-height: 1.4;
        color: #1e293b;
    }

    .flete-form .flete-field:focus {
        border-color: #0ea5e9;
        outline: none;
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2);
    }

    .flete-form .flete-btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        border-radius: 0.75rem;
        border: 1px solid #bae6fd;
        background: #f0f9ff;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        font-weight: 700;
        color: #0369a1;
    }

    .flete-form .flete-excel-wrap {
        border: 1px solid #94a3b8;
        background: #fff;
    }

    .flete-form .flete-excel-table {
        width: 100%;
        min-width: 36rem;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 1rem;
        color: #334155;
    }

    .flete-form .flete-excel-table th,
    .flete-form .flete-excel-table td {
        border: 1px solid #94a3b8;
        padding: 0;
        margin: 0;
        vertical-align: middle;
    }

    .flete-form .flete-excel-table th {
        background: #e2e8f0;
        padding: 0.5rem 0.25rem;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #475569;
        text-align: center;
        white-space: nowrap;
    }

    .flete-form .flete-excel-table .col-fecha { width: 4.25rem; }
    .flete-form .flete-excel-table .col-desc { width: auto; }
    .flete-form .flete-excel-table .col-num { width: 5.25rem; }
    .flete-form .flete-excel-table .col-del { width: 2.75rem; }

    .flete-form .flete-cell-input {
        display: block;
        width: 100%;
        min-height: 2.875rem;
        border: 0;
        border-radius: 0;
        background: transparent;
        padding: 0.5rem 0.375rem;
        font-size: 1.0625rem;
        line-height: 1.3;
        color: #1e293b;
    }

    .flete-form .flete-cell-input:focus {
        outline: 2px solid #0ea5e9;
        outline-offset: -2px;
        background: #f0f9ff;
    }

    .flete-form .flete-cell-num {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .flete-form .flete-excel-table td:first-child {
        overflow: hidden;
    }

    /* Fecha compacta: texto pequeño, icono de calendario grande */
    .flete-form .flete-cell-date {
        min-height: 2.875rem;
        font-size: 0.625rem;
        line-height: 1.1;
        letter-spacing: -0.03em;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .flete-form .flete-cell-date::-webkit-calendar-picker-indicator {
        width: 1.275rem;
        height: 1.275rem;
        margin: 0;
        padding: 0;
        cursor: pointer;
        opacity: 1;
        transform: scale(1.25);
        transform-origin: center;
    }

    .flete-form .flete-cell-date::-moz-calendar-picker-indicator {
        width: 1.375rem;
        height: 1.375rem;
        cursor: pointer;
    }

    .flete-form .flete-remove-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 2.875rem;
        border: 0;
        background: transparent;
        color: #e11d48;
    }

    .flete-form .flete-remove-btn:active {
        background: #fff1f2;
    }

    .flete-form .flete-total-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border-radius: 0.75rem;
        border: 2px solid #bae6fd;
        background: #f0f9ff;
        padding: 1rem 1.25rem;
    }

    .flete-form .flete-btn-primary {
        display: inline-flex;
        flex: 1;
        align-items: center;
        justify-content: center;
        min-height: 3.25rem;
        border-radius: 0.875rem;
        background: #0284c7;
        padding: 0.875rem 1.25rem;
        font-size: 1.125rem;
        font-weight: 800;
        color: #fff;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
    }

    .flete-form .flete-btn-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 3.25rem;
        border-radius: 0.875rem;
        border: 2px solid #cbd5e1;
        background: #fff;
        padding: 0.875rem 1.5rem;
        font-size: 1.0625rem;
        font-weight: 700;
        color: #475569;
        text-align: center;
    }

    [data-autocomplete-option][data-active="true"] {
        background-color: rgb(240 249 255);
    }
</style>

<script>
    (function () {
        const tbody = document.getElementById('items-tbody');
        const addBtn = document.getElementById('add-item-row');
        const totalFletePreview = document.getElementById('total-flete-preview');
        const fechaFleteInput = document.getElementById('fecha');
        const autocompleteSources = {
            cliente: @json($clientesAutocomplete),
        };

        function formatMoney(value) {
            return value.toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function parseNumber(value) {
            return Number.parseFloat(value || '0') || 0;
        }

        function updateTotalFlete() {
            const totalInputs = tbody.querySelectorAll('.js-item-total');
            const total = Array.from(totalInputs).reduce((sum, input) => sum + parseNumber(input.value), 0);
            if (totalFletePreview) {
                totalFletePreview.textContent = formatMoney(total);
            }
        }

        function updateRowTotal(row) {
            const servicioInput = row.querySelector('.js-item-servicio');
            const fleteInput = row.querySelector('.js-item-flete');
            const totalInput = row.querySelector('.js-item-total');

            if (!servicioInput || !fleteInput || !totalInput) return;

            const total = parseNumber(servicioInput.value) + parseNumber(fleteInput.value);
            totalInput.value = total.toFixed(2);
        }

        function reindexRows() {
            tbody.querySelectorAll('.item-row').forEach((row, index) => {
                row.dataset.index = String(index);
                row.querySelectorAll('input').forEach((input) => {
                    const field = input.name.match(/\[(\w+)\]$/)?.[1];
                    if (field) {
                        input.name = `items[${index}][${field}]`;
                    }
                });
            });
        }

        function bindRowEvents(row) {
            const servicioInput = row.querySelector('.js-item-servicio');
            const fleteInput = row.querySelector('.js-item-flete');
            const totalInput = row.querySelector('.js-item-total');
            const removeBtn = row.querySelector('.js-remove-row');

            servicioInput?.addEventListener('input', function () {
                updateRowTotal(row);
                updateTotalFlete();
            });

            fleteInput?.addEventListener('input', function () {
                updateRowTotal(row);
                updateTotalFlete();
            });

            totalInput?.addEventListener('input', updateTotalFlete);

            removeBtn?.addEventListener('click', function () {
                const rows = tbody.querySelectorAll('.item-row');
                if (rows.length <= 1) {
                    row.querySelectorAll('input').forEach((input) => {
                        if (input.type === 'date') {
                            input.value = fechaFleteInput?.value || '';
                        } else {
                            input.value = '';
                        }
                    });
                    updateTotalFlete();
                    return;
                }

                row.remove();
                reindexRows();
                updateTotalFlete();
            });
        }

        function createRow(index) {
            const fecha = fechaFleteInput?.value || '';
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.dataset.index = String(index);
            tr.innerHTML = `
                <td>
                    <input type="date" name="items[${index}][fecha]" value="${fecha}" class="js-item-input flete-cell-input flete-cell-date">
                </td>
                <td>
                    <input type="text" name="items[${index}][descripcion]" placeholder="Descripción" class="js-item-input flete-cell-input">
                </td>
                <td>
                    <input type="number" min="0" step="0.01" inputmode="decimal" name="items[${index}][servicio]" placeholder="0.00" class="js-item-servicio flete-cell-input flete-cell-num">
                </td>
                <td>
                    <input type="number" min="0" step="0.01" inputmode="decimal" name="items[${index}][flete]" placeholder="0.00" class="js-item-flete flete-cell-input flete-cell-num">
                </td>
                <td>
                    <input type="number" min="0" step="0.01" inputmode="decimal" name="items[${index}][total]" placeholder="0.00" class="js-item-total flete-cell-input flete-cell-num">
                </td>
                <td class="col-del">
                    <button type="button" class="js-remove-row flete-remove-btn" title="Eliminar fila" aria-label="Eliminar fila">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </td>
            `;
            return tr;
        }

        addBtn?.addEventListener('click', function () {
            const index = tbody.querySelectorAll('.item-row').length;
            const row = createRow(index);
            tbody.appendChild(row);
            bindRowEvents(row);
        });

        tbody.querySelectorAll('.item-row').forEach(bindRowEvents);
        updateTotalFlete();

        // Evita que la rueda del mouse cambie los montos cuando el input tiene foco
        tbody.addEventListener('wheel', function (event) {
            const target = event.target;
            if (target.matches('.flete-cell-num') && document.activeElement === target) {
                event.preventDefault();
            }
        }, { passive: false });

        function normalizeText(value) {
            return (value || '')
                .toString()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        }

        function setupAutocomplete(key) {
            const input = document.querySelector(`[data-autocomplete-input="${key}"]`);
            const hiddenInput = document.getElementById(`${key}_id`);
            const results = document.getElementById(`${key}_results`);
            const list = document.querySelector(`[data-autocomplete-list="${key}"]`);
            const emptyState = document.querySelector(`[data-autocomplete-empty="${key}"]`);
            const options = autocompleteSources[key];

            if (!input || !hiddenInput || !results || !list || !emptyState) return;

            function hideResults() {
                results.classList.add('hidden');
            }

            function showResults() {
                results.classList.remove('hidden');
            }

            function setSelection(option) {
                if (!option) {
                    hiddenInput.value = '';
                    return;
                }
                hiddenInput.value = option.id;
                input.value = option.name;
                hideResults();
            }

            function getMatches(query) {
                const normalizedQuery = normalizeText(query);
                return normalizedQuery === ''
                    ? options.slice(0, 8)
                    : options.filter((option) => normalizeText(option.name).includes(normalizedQuery)).slice(0, 8);
            }

            function syncHiddenValueFromInput() {
                const exactMatch = options.find((option) => normalizeText(option.name) === normalizeText(input.value));
                hiddenInput.value = exactMatch ? exactMatch.id : '';
            }

            function renderOptions(matches) {
                list.innerHTML = '';
                if (!matches.length) {
                    emptyState.classList.remove('hidden');
                    showResults();
                    return;
                }
                emptyState.classList.add('hidden');
                matches.forEach((option, index) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'flex w-full items-center justify-between px-4 py-3 text-left text-base text-slate-700 hover:bg-sky-50';
                    button.dataset.autocompleteOption = key;
                    button.dataset.active = index === 0 ? 'true' : 'false';
                    button.innerHTML = `<span>${option.name}</span><span class="text-sm text-slate-400">#${option.id}</span>`;
                    button.addEventListener('mousedown', function (event) {
                        event.preventDefault();
                        setSelection(option);
                    });
                    list.appendChild(button);
                });
                showResults();
            }

            input.addEventListener('focus', function () {
                renderOptions(getMatches(input.value));
            });

            input.addEventListener('input', function () {
                syncHiddenValueFromInput();
                renderOptions(getMatches(input.value));
            });

            input.addEventListener('blur', function () {
                syncHiddenValueFromInput();
                window.setTimeout(hideResults, 120);
            });

            input.closest('form')?.addEventListener('submit', syncHiddenValueFromInput);
        }

        setupAutocomplete('cliente');
    })();
</script>
