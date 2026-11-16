<x-app-layout title="Perfil">
    <section class="space-y-4">
        <div class="px-1">
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Perfil</h1>
            <p class="mt-1 text-sm text-slate-500">Cambia tu correo, tu clave o cierra la sesión.</p>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
            <h2 class="text-lg font-bold text-slate-900">Sesión</h2>
            <p class="mt-1 text-sm text-slate-500">Sales de la cuenta en este dispositivo.</p>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </section>
</x-app-layout>
