<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">Correo</h2>
        <p class="mt-1 text-sm text-slate-500">Este correo es el que usas para entrar.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4 space-y-4">
        @csrf
        @method('patch')

        {{-- El servidor exige el nombre aunque esta pantalla solo pida el correo. --}}
        <input type="hidden" name="name" value="{{ old('name', $user->name) }}">

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700">Correo</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-base text-slate-800 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-sky-200"
            >
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-sky-200">
                Guardar correo
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm font-medium text-emerald-700">Correo guardado.</p>
            @endif
        </div>
    </form>
</section>
