<x-app-layout title="Nuevo flete">
    <x-slot name="header">
        <x-header title="Nuevo flete" subtitle="Registra un flete con sus productos y totales." />
    </x-slot>

    <section class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Datos del flete</h2>

            <form action="{{ route('fletes.store') }}" method="POST" class="mt-5">
                @csrf
                @include('fletes._form')
            </form>
        </div>
    </section>
</x-app-layout>
