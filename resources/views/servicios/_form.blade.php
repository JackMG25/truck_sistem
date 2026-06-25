@php
    $isEdit = $servicio->exists;
    $fechaServicio = old('fecha_servicio', optional($servicio->fecha_servicio)->format('Y-m-d\TH:i') ?: now()->format('Y-m-d\TH:i'));
    $costoTransporte = old('costo_transporte', $servicio->costo_transporte !== null ? number_format((float) $servicio->costo_transporte, 2, '.', '') : '0.00');
    $costoFlete = old('costo_flete', $servicio->costo_flete !== null ? number_format((float) $servicio->costo_flete, 2, '.', '') : '0.00');
    $totalPreview = (float) $costoTransporte + (float) $costoFlete;
@endphp

<div class="space-y-2.5">
    <div class="grid gap-2 lg:grid-cols-2">
        <div>
            <div class="mb-1 flex items-center justify-between gap-2">
                <label for="cliente_id" class="block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Cliente</label>
                <button type="button" class="inline-flex items-center rounded-md border border-sky-200 bg-sky-50 px-2 py-1 text-[11px] font-semibold text-sky-700 hover:bg-sky-100" data-open-modal="cliente-modal">+ Nuevo</button>
            </div>
            <select id="cliente_id" name="cliente_id" data-select2="true" data-placeholder="Selecciona un cliente" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200">
                <option value=""></option>
                @foreach ($clientesOptions as $cliente)
                    <option value="{{ $cliente->id }}" @selected((string) old('cliente_id', $servicio->cliente_id) === (string) $cliente->id)>{{ $cliente->nombre }}</option>
                @endforeach
            </select>
            @error('cliente_id')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="mb-1 flex items-center justify-between gap-2">
                <label for="agencia_id" class="block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Agencia</label>
                <button type="button" class="inline-flex items-center rounded-md border border-sky-200 bg-sky-50 px-2 py-1 text-[11px] font-semibold text-sky-700 hover:bg-sky-100" data-open-modal="agencia-modal">+ Nueva</button>
            </div>
            <select id="agencia_id" name="agencia_id" data-select2="true" data-placeholder="Selecciona una agencia" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200">
                <option value=""></option>
                @foreach ($agenciasOptions as $agencia)
                    <option value="{{ $agencia->id }}" @selected((string) old('agencia_id', $servicio->agencia_id) === (string) $agencia->id)>{{ $agencia->nombre }}</option>
                @endforeach
            </select>
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

    <div class="grid gap-2 sm:grid-cols-3">
        <div>
            <label for="costo_transporte" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Transporte</label>
            <input id="costo_transporte" name="costo_transporte" type="number" min="0" step="0.01" value="{{ $costoTransporte }}" class="js-total-input w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200" placeholder="0.00">
            @error('costo_transporte')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="costo_flete" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">Flete</label>
            <input id="costo_flete" name="costo_flete" type="number" min="0" step="0.01" value="{{ $costoFlete }}" class="js-total-input w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200" placeholder="0.00">
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

    <div class="grid gap-2 xl:grid-cols-[1fr_1fr_1.2fr]">
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

        <div>
            <span class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600">pago</span>
            <div class="flex flex-row items-center gap-2">
                @foreach (['PENDIENTE' => 'Pendiente', 'PARCIAL' => 'Parcial', 'PAGADO' => 'Pagado'] as $value => $label)
                    <label data-radio-card class="flex cursor-pointer items-center gap-2 rounded-md border px-2 py-2 text-xs font-semibold {{ old('estado_pago', $servicio->estado_pago ?? 'PENDIENTE') === $value ? 'border-sky-500 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-600' }}">
                        <input type="radio" name="estado_pago" value="{{ $value }}" class="h-3.5 w-3.5 border-slate-300 text-sky-600 focus:ring-sky-500" @checked(old('estado_pago', $servicio->estado_pago ?? 'PENDIENTE') === $value)>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('estado_pago')
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

    <div class="flex items-center gap-2 pt-1">
        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-sm shadow-sky-200">
            {{ $isEdit ? 'Actualizar' : 'Guardar' }}
        </button>
        <a href="{{ route('servicios.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 shadow-sm">
            Cancelar
        </a>
    </div>
</div>

<div id="cliente-modal" class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-900/35 p-3 sm:items-center">
    <div class="w-full max-w-sm rounded-2xl bg-white p-4 shadow-2xl">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h3 class="text-sm font-bold text-slate-800">Nuevo cliente</h3>
            <button type="button" class="rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700" data-close-modal="cliente-modal">✕</button>
        </div>
        <form id="cliente-inline-form" class="space-y-2" data-target-select="#cliente_id" data-endpoint="{{ route('servicios.clientes-inline') }}">
            <input type="text" name="nombre" placeholder="Nombre" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
            <input type="text" name="telefono" placeholder="Teléfono" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
            <input type="text" name="direccion" placeholder="Dirección" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
            <textarea name="observaciones" rows="2" placeholder="Observaciones" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm"></textarea>
            <p class="hidden text-xs text-rose-500" data-inline-error></p>
            <div class="flex gap-2 pt-1">
                <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white">Guardar</button>
                <button type="button" class="inline-flex items-center justify-center rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600" data-close-modal="cliente-modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<div id="agencia-modal" class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-900/35 p-3 sm:items-center">
    <div class="w-full max-w-sm rounded-2xl bg-white p-4 shadow-2xl">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h3 class="text-sm font-bold text-slate-800">Nueva agencia</h3>
            <button type="button" class="rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700" data-close-modal="agencia-modal">✕</button>
        </div>
        <form id="agencia-inline-form" class="space-y-2" data-target-select="#agencia_id" data-endpoint="{{ route('servicios.agencias-inline') }}">
            <input type="text" name="nombre" placeholder="Nombre" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
            <input type="text" name="telefono" placeholder="Teléfono" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
            <input type="text" name="direccion" placeholder="Dirección" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm">
            <textarea name="observaciones" rows="2" placeholder="Observaciones" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm"></textarea>
            <p class="hidden text-xs text-rose-500" data-inline-error></p>
            <div class="flex gap-2 pt-1">
                <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white">Guardar</button>
                <button type="button" class="inline-flex items-center justify-center rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600" data-close-modal="agencia-modal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 42px;
        border-radius: 0.375rem;
        border-color: rgb(226 232 240);
        padding: 6px 10px;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: rgb(15 23 42);
        font-size: 0.875rem;
        line-height: 1.25rem;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 6px;
    }

    .select2-dropdown {
        border-color: rgb(226 232 240);
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .select2-search--dropdown .select2-search__field {
        border-color: rgb(226 232 240);
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    (function () {
        const totalInputs = document.querySelectorAll('.js-total-input');
        const totalPreview = document.getElementById('total-preview');
        const csrfToken = document.querySelector('input[name="_token"]')?.value;

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

        ['tipo_servicio', 'estado_servicio', 'estado_pago'].forEach((name) => {
            document.querySelectorAll(`input[name="${name}"]`).forEach((input) => {
                input.addEventListener('change', function () {
                    syncRadioCards(name);
                });
            });
            syncRadioCards(name);
        });

        if (window.jQuery && window.jQuery.fn.select2) {
            window.jQuery('[data-select2="true"]').each(function () {
                const placeholder = this.getAttribute('data-placeholder') || 'Selecciona una opción';
                window.jQuery(this).select2({
                    width: '100%',
                    placeholder,
                    allowClear: true,
                    dropdownAutoWidth: true,
                });
            });
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            const form = modal.querySelector('form');
            form?.reset();
            const error = modal.querySelector('[data-inline-error]');
            if (error) {
                error.textContent = '';
                error.classList.add('hidden');
            }
        }

        document.querySelectorAll('[data-open-modal]').forEach((button) => {
            button.addEventListener('click', function () {
                openModal(this.getAttribute('data-open-modal'));
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach((button) => {
            button.addEventListener('click', function () {
                closeModal(this.getAttribute('data-close-modal'));
            });
        });

        document.querySelectorAll('#cliente-modal, #agencia-modal').forEach((modal) => {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal(modal.id);
                }
            });
        });

        document.querySelectorAll('#cliente-inline-form, #agencia-inline-form').forEach((form) => {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                const endpoint = form.getAttribute('data-endpoint');
                const targetSelectSelector = form.getAttribute('data-target-select');
                const targetSelect = document.querySelector(targetSelectSelector);
                const errorBox = form.querySelector('[data-inline-error]');
                const submitButton = form.querySelector('button[type="submit"]');

                if (!endpoint || !targetSelect || !csrfToken) {
                    return;
                }

                if (errorBox) {
                    errorBox.textContent = '';
                    errorBox.classList.add('hidden');
                }

                submitButton?.setAttribute('disabled', 'disabled');

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(Object.fromEntries(new FormData(form).entries())),
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        const firstError = result?.errors ? Object.values(result.errors).flat()[0] : 'No se pudo guardar el registro.';
                        throw new Error(firstError);
                    }

                    const option = new Option(result.nombre, result.id, true, true);
                    targetSelect.add(option);

                    if (window.jQuery && window.jQuery.fn.select2) {
                        window.jQuery(targetSelect).trigger('change');
                    } else {
                        targetSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    closeModal(form.closest('[id$="-modal"]').id);
                } catch (error) {
                    if (errorBox) {
                        errorBox.textContent = error.message || 'No se pudo guardar el registro.';
                        errorBox.classList.remove('hidden');
                    }
                } finally {
                    submitButton?.removeAttribute('disabled');
                }
            });
        });
    })();
</script>