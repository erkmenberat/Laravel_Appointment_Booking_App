<x-layout title="Kundenbereich" mode="wide">
    {{-- Styled placeholder keeps this view consistent until business logic is implemented. --}}
    <div class="space-y-6">
        <div>
            <span class="barber-badge">User Dashboard</span>
            <h1 class="barber-heading mt-3 text-3xl font-semibold">Kundenbereich (Vorbereitung)</h1>
            <p class="mt-2 text-sm text-[#c9bfb3]">
                Diese View wurde optisch angepasst und kann jetzt schrittweise funktional erweitert werden.
            </p>
        </div>

        {{-- Example cards document where future sections can be added. --}}
        <div class="grid gap-4 md:grid-cols-3">
            <div class="barber-panel-muted p-4">
                <h2 class="text-lg font-semibold">Meine Termine</h2>
                <p class="mt-2 text-sm text-[#c9bfb3]">Platz fuer kuenftige Terminliste oder Terminstatus.</p>
            </div>
            <div class="barber-panel-muted p-4">
                <h2 class="text-lg font-semibold">Profil</h2>
                <p class="mt-2 text-sm text-[#c9bfb3]">Platz fuer Kontaktdaten und Aenderungen.</p>
            </div>
            <div class="barber-panel-muted p-4">
                <h2 class="text-lg font-semibold">Hinweise</h2>
                <p class="mt-2 text-sm text-[#c9bfb3]">Platz fuer Erinnerungen oder Salon-Infos.</p>
            </div>
        </div>
    </div>
</x-layout>
