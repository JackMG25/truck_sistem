<x-app-layout title="Editar flete">
    <x-slot name="header">
        <x-header title="Editar flete" subtitle="Actualiza los datos y productos del flete." />
    </x-slot>

    <section class="space-y-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Flete #{{ $flete->id }}</h2>

            <form action="{{ route('fletes.update', $flete) }}" method="POST" class="mt-5">
                @csrf
                @method('PUT')
                @include('fletes._form')
            </form>
        </div>
    </section>
</x-app-layout>
