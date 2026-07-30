<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2']) }}
    style="background-color: #0A5BA6;"
    onmouseover="this.style.backgroundColor='#06305E'"
    onmouseout="this.style.backgroundColor='#0A5BA6'">
    {{ $slot }}
</button>
