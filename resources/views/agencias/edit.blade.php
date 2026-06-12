<x-app-layout title="Editar agencia">
    <x-slot name="header">
        <x-header title="Editar agencia" subtitle="Actualiza la información de contacto y las observaciones de la agencia." />
    </x-slot>

    <section class="space-y-4">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-lg shadow-sky-100/70">
            <h2 class="text-lg font-bold text-slate-800">{{ $agencia->nombre }}</h2>
            <p class="mt-2 text-sm text-slate-500">Modifica los datos necesarios y guarda los cambios.</p>

            <form action="{{ route('agencias.update', $agencia) }}" method="POST" class="mt-5">
                @csrf
                @method('PUT')
                @include('agencias._form')
            </form>
        </div>
    </section>
</x-app-layout>