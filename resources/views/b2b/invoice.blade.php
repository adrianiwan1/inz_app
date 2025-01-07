<x-app-layout>
    <form method="POST" action="{{ url('/b2b') }}">
        @csrf
        <div>
            <label>Nazwa firmy</label>
            <input type="text" name="seller_name" required>
        </div>
        <!-- Pola dla sprzedawcy, nabywcy, usługi -->
        <div>
            <button type="submit">Wygeneruj fakturę</button>
        </div>
    </form>
</x-app-layout>
