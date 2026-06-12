<x-app-layout title="Editar cliente">
    <x-slot name="header">
        <x-header title="Editar cliente" subtitle="Actualiza la información de contacto y las observaciones del cliente." />
    </x-slot>

    <section class="space-y-4">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-lg shadow-sky-100/70">
            <h2 class="text-lg font-bold text-slate-800">{{ $cliente->nombre }}</h2>
            <p class="mt-2 text-sm text-slate-500">Modifica los datos necesarios y guarda los cambios.</p>

            <form action="{{ route('clientes.update', $cliente) }}" method="POST" class="mt-5">
                @csrf
                @method('PUT')
                @include('clientes._form')
            </form>
        </div>
    </section>
</x-app-layout>