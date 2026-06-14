<x-app-layout title="Nuevo servicio">
    <x-slot name="header">
        <x-header title="Nuevo servicio" subtitle="Registra un servicio y deja que el total se calcule automáticamente." />
    </x-slot>

    <section class="space-y-4">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-lg shadow-sky-100/70">
            <h2 class="text-lg font-bold text-slate-800">Datos del servicio</h2>
            <p class="mt-2 text-sm text-slate-500">Completa los datos principales del traslado o recojo.</p>

            <form action="{{ route('servicios.store') }}" method="POST" class="mt-5">
                @csrf
                @include('servicios._form')
            </form>
        </div>
    </section>
</x-app-layout>