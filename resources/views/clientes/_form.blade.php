@php
    $isEdit = $cliente->exists;
@endphp

<div class="space-y-3">
    <div>
        <label for="nombre" class="mb-1 block text-xs font-semibold text-slate-700">Nombre</label>
        <input
            id="nombre"
            name="nombre"
            type="text"
            value="{{ old('nombre', $cliente->nombre) }}"
            class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200"
            placeholder="Nombre del cliente"
        >
        @error('nombre')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="telefono" class="mb-1 block text-xs font-semibold text-slate-700">Teléfono</label>
            <input
                id="telefono"
                name="telefono"
                type="text"
                value="{{ old('telefono', $cliente->telefono) }}"
                class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200"
                placeholder="999 999 999"
            >
            @error('telefono')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="direccion" class="mb-1 block text-xs font-semibold text-slate-700">Dirección</label>
            <input
                id="direccion"
                name="direccion"
                type="text"
                value="{{ old('direccion', $cliente->direccion) }}"
                class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200"
                placeholder="Dirección de referencia"
            >
            @error('direccion')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="observaciones" class="mb-1 block text-xs font-semibold text-slate-700">Observaciones</label>
        <textarea
            id="observaciones"
            name="observaciones"
            rows="3"
            class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-200"
            placeholder="Notas importantes del cliente"
        >{{ old('observaciones', $cliente->observaciones) }}</textarea>
        @error('observaciones')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-2 pt-2">
        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-sm shadow-sky-200">
            {{ $isEdit ? 'Actualizar' : 'Guardar' }}
        </button>
        <a href="{{ route('clientes.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 shadow-sm">
            Cancelar
        </a>
    </div>
</div>