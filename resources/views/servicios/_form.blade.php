@php
    $isEdit = $servicio->exists;
    $fechaServicio = old('fecha_servicio', optional($servicio->fecha_servicio)->format('Y-m-d\TH:i') ?: now()->format('Y-m-d\TH:i'));
    $costoTransporte = old('costo_transporte', $servicio->costo_transporte !== null ? number_format((float) $servicio->costo_transporte, 2, '.', '') : '');
    $costoFlete = old('costo_flete', $servicio->costo_flete !== null ? number_format((float) $servicio->costo_flete, 2, '.', '') : '');
    $totalPreview = (float) ($costoTransporte !== '' ? $costoTransporte : 0) + (float) ($costoFlete !== '' ? $costoFlete : 0);
    $selectedClienteId = (string) old('cliente_id', $servicio->cliente_id);
    $selectedAgenciaId = (string) old('agencia_id', $servicio->agencia_id);
    $selectedCliente = $clientesOptions->firstWhere('id', $selectedClienteId);
    $selectedAgencia = $agenciasOptions->firstWhere('id', $selectedAgenciaId);
    $clienteSearchValue = old('cliente_nombre_busqueda', $selectedCliente?->nombre ?? '');
    $agenciaSearchValue = old('agencia_nombre_busqueda', $selectedAgencia?->nombre ?? '');
    $clientesAutocomplete = $clientesOptions->map(fn ($cliente) => [
        'id' => (string) $cliente->id,
        'name' => $cliente->nombre,
    ])->values();
    $agenciasAutocomplete = $agenciasOptions->map(fn ($agencia) => [
        'id' => (string) $agencia->id,
        'name' => $agencia->nombre,
    ])->values();
@endphp

<div class="space-y-2.5">
    <div class="grid gap-2 lg:grid-cols-2">
        <div>
            <label for="cliente_search" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Cliente</label>
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
                    class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200"
                    data-autocomplete-input="cliente"
                >
                <div class="mt-1 text-[11px] text-slate-400">Busca por nombre. Si no existe, se creará al guardar.</div>
                <div id="cliente_results" class="absolute z-20 mt-1 hidden w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                    <div class="max-h-56 overflow-y-auto" data-autocomplete-list="cliente"></div>
                    <div class="hidden border-t border-slate-100 px-3 py-2 text-xs text-slate-500" data-autocomplete-empty="cliente">
                        No se encontraron clientes. Se creará con este nombre al guardar.
                    </div>
                </div>
            </div>
            @error('cliente_id')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="agencia_search" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Agencia</label>
            <div class="relative" data-autocomplete-root="agencia">
                <input type="hidden" id="agencia_id" name="agencia_id" value="{{ $selectedAgenciaId }}">
                <input
                    id="agencia_search"
                    name="agencia_nombre_busqueda"
                    type="text"
                    autocomplete="off"
                    spellcheck="false"
                    value="{{ $agenciaSearchValue }}"
                    placeholder="Escribe para buscar agencia"
                    class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200"
                    data-autocomplete-input="agencia"
                >
                <div class="mt-1 text-[11px] text-slate-400">Busca por nombre. Si no existe, se creará al guardar.</div>
                <div id="agencia_results" class="absolute z-20 mt-1 hidden w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                    <div class="max-h-56 overflow-y-auto" data-autocomplete-list="agencia"></div>
                    <div class="hidden border-t border-slate-100 px-3 py-2 text-xs text-slate-500" data-autocomplete-empty="agencia">
                        No se encontraron agencias. Se creará con este nombre al guardar.
                    </div>
                </div>
            </div>
            @error('agencia_id')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-2 sm:grid-cols-2">
        <div>
            <label for="fecha_servicio" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Fecha servicio</label>
            <input id="fecha_servicio" name="fecha_servicio" type="datetime-local" value="{{ $fechaServicio }}" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200">
            @error('fecha_servicio')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="cantidad_bultos" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Bultos</label>
            <input id="cantidad_bultos" name="cantidad_bultos" type="number" min="1" value="{{ old('cantidad_bultos', $servicio->cantidad_bultos) }}" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200" placeholder="0">
            @error('cantidad_bultos')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="grid gap-2 lg:grid-cols-1">
        <div>
            <label for="descripcion" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="2" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200" placeholder="Detalle breve del servicio">{{ old('descripcion', $servicio->descripcion) }}</textarea>
            @error('descripcion')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="grid gap-2 sm:grid-cols-3">
        <div>
            <label for="costo_transporte" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Transporte</label>
            <input id="costo_transporte" name="costo_transporte" type="number" min="0" step="0.01" value="{{ $costoTransporte }}" class="js-total-input w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200" placeholder="0.00">
            @error('costo_transporte')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="costo_flete" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Flete</label>
            <input id="costo_flete" name="costo_flete" type="number" min="0" step="0.01" value="{{ $costoFlete }}" class="js-total-input w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200" placeholder="0.00">
            @error('costo_flete')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Total</label>
            <div class="flex h-[42px] items-center justify-between rounded-md border border-sky-100 bg-sky-50 px-3 text-sm font-bold text-slate-800">
                <span>S/</span>
                <span id="total-preview">{{ number_format($totalPreview, 2, '.', ',') }}</span>
            </div>
        </div>
    </div>

    <div class="grid gap-2 sm:grid-cols-2">
        <div>
            <span class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Tipo de servicio</span>
            <div class="grid grid-cols-2 gap-2">
                @foreach (['ENVIO' => 'Envío', 'RECOJO' => 'Recojo'] as $value => $label)
                    <label data-radio-card class="flex cursor-pointer items-center justify-center gap-2 rounded-md border px-3 py-2 text-xs font-semibold {{ old('tipo_servicio', $servicio->tipo_servicio) === $value ? 'border-sky-500 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-600' }}">
                        <input type="radio" name="tipo_servicio" value="{{ $value }}" class="h-3.5 w-3.5 border-slate-300 text-sky-600 focus:ring-sky-500" @checked(old('tipo_servicio', $servicio->tipo_servicio) === $value)>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('tipo_servicio')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <span class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Estado servicio</span>
            <div class="grid grid-cols-2 gap-2">
                @foreach (['PENDIENTE' => 'Pendiente', 'ENTREGADO' => 'Entregado'] as $value => $label)
                    <label data-radio-card class="flex cursor-pointer items-center justify-center gap-2 rounded-md border px-3 py-2 text-xs font-semibold {{ old('estado_servicio', $servicio->estado_servicio ?? 'PENDIENTE') === $value ? 'border-sky-500 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-600' }}">
                        <input type="radio" name="estado_servicio" value="{{ $value }}" class="h-3.5 w-3.5 border-slate-300 text-sky-600 focus:ring-sky-500" @checked(old('estado_servicio', $servicio->estado_servicio ?? 'PENDIENTE') === $value)>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('estado_servicio')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    

    <div class="flex items-center gap-2 pt-1">
        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-sm shadow-sky-200">
            {{ $isEdit ? 'Actualizar' : 'Guardar' }}
        </button>
        <a href="{{ route('servicios.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 shadow-sm">
            Cancelar
        </a>
    </div>
</div>

<style>
    [data-autocomplete-option][data-active="true"] {
        background-color: rgb(240 249 255);
    }
</style>
<script>
    (function () {
        const totalInputs = document.querySelectorAll('.js-total-input');
        const totalPreview = document.getElementById('total-preview');
        const autocompleteSources = {
            cliente: @json($clientesAutocomplete),
            agencia: @json($agenciasAutocomplete),
        };

        function updateTotal() {
            const values = Array.from(totalInputs).map((input) => Number.parseFloat(input.value || '0') || 0);
            const total = values.reduce((sum, value) => sum + value, 0);
            if (totalPreview) {
                totalPreview.textContent = total.toLocaleString('es-PE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            }
        }

        totalInputs.forEach((input) => input.addEventListener('input', updateTotal));
        updateTotal();

        function syncRadioCards(name) {
            document.querySelectorAll(`input[name="${name}"]`).forEach((input) => {
                const card = input.closest('[data-radio-card]');
                if (!card) return;

                card.classList.toggle('border-sky-500', input.checked);
                card.classList.toggle('bg-sky-50', input.checked);
                card.classList.toggle('text-sky-700', input.checked);
                card.classList.toggle('border-slate-200', !input.checked);
                card.classList.toggle('bg-white', !input.checked);
                card.classList.toggle('text-slate-600', !input.checked);
            });
        }

        ['tipo_servicio', 'estado_servicio'].forEach((name) => {
            document.querySelectorAll(`input[name="${name}"]`).forEach((input) => {
                input.addEventListener('change', function () {
                    syncRadioCards(name);
                });
            });
            syncRadioCards(name);
        });

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

            if (!input || !hiddenInput || !results || !list || !emptyState) {
                return;
            }

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
                const pool = normalizedQuery === ''
                    ? options.slice(0, 8)
                    : options.filter((option) => normalizeText(option.name).includes(normalizedQuery)).slice(0, 8);

                return pool;
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
                    button.className = 'flex w-full items-center justify-between px-3 py-2 text-left text-sm text-slate-700 hover:bg-sky-50';
                    button.dataset.autocompleteOption = key;
                    button.dataset.active = index === 0 ? 'true' : 'false';
                    button.innerHTML = `<span>${option.name}</span><span class="text-[11px] text-slate-400">#${option.id}</span>`;
                    button.addEventListener('mousedown', function (event) {
                        event.preventDefault();
                        setSelection(option);
                    });
                    list.appendChild(button);
                });

                showResults();
            }

            function moveActive(delta) {
                const items = Array.from(list.querySelectorAll('[data-autocomplete-option]'));
                if (!items.length) {
                    return;
                }

                const currentIndex = items.findIndex((item) => item.dataset.active === 'true');
                const nextIndex = currentIndex === -1
                    ? 0
                    : (currentIndex + delta + items.length) % items.length;

                items.forEach((item, index) => {
                    item.dataset.active = index === nextIndex ? 'true' : 'false';
                });

                items[nextIndex].scrollIntoView({ block: 'nearest' });
            }

            function selectActive() {
                const activeItem = list.querySelector('[data-autocomplete-option][data-active="true"]');
                activeItem?.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
            }

            input.addEventListener('focus', function () {
                renderOptions(getMatches(input.value));
            });

            input.addEventListener('input', function () {
                syncHiddenValueFromInput();
                renderOptions(getMatches(input.value));
            });

            input.addEventListener('keydown', function (event) {
                if (results.classList.contains('hidden') && ['ArrowDown', 'ArrowUp'].includes(event.key)) {
                    renderOptions(getMatches(input.value));
                }

                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    moveActive(1);
                }

                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    moveActive(-1);
                }

                if (event.key === 'Enter' && !results.classList.contains('hidden')) {
                    const hasOptions = list.querySelector('[data-autocomplete-option]');
                    if (hasOptions) {
                        event.preventDefault();
                        selectActive();
                    }
                }

                if (event.key === 'Escape') {
                    hideResults();
                }
            });

            input.addEventListener('blur', function () {
                syncHiddenValueFromInput();
                window.setTimeout(hideResults, 120);
            });

            input.closest('form')?.addEventListener('submit', syncHiddenValueFromInput);
        }

        setupAutocomplete('cliente');
        setupAutocomplete('agencia');
    })();
</script>