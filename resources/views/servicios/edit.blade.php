<x-app-layout title="Editar servicio">
    <x-slot name="header">
        <x-header title="Editar servicio" subtitle="Actualiza los datos operativos y de cobro del servicio." />
    </x-slot>

    <section class="space-y-4">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-lg shadow-sky-100/70">
            <h2 class="text-lg font-bold text-slate-800">Servicio #{{ $servicio->id }}</h2>
            <p class="mt-2 text-sm text-slate-500">Ajusta cliente, agencia, estados y costos según corresponda.</p>

            <form action="{{ route('servicios.update', $servicio) }}" method="POST" class="mt-5">
                @csrf
                @method('PUT')
                @include('servicios._form')
            </form>
        </div>
    </section>
</x-app-layout>