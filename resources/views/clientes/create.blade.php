<x-app-layout title="Nuevo cliente">
    <x-slot name="header">
        <x-header title="Nuevo cliente" subtitle="Registra un nuevo contacto para asociarlo a futuros servicios." />
    </x-slot>

    <section class="space-y-4">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-lg shadow-sky-100/70">
            <h2 class="text-lg font-bold text-slate-800">Datos del cliente</h2>
            <p class="mt-2 text-sm text-slate-500">Completa los campos básicos para crear el registro.</p>

            <form action="{{ route('clientes.store') }}" method="POST" class="mt-5">
                @csrf
                @include('clientes._form')
            </form>
        </div>
    </section>
</x-app-layout>