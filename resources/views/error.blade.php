<x-guest-layout>
    <section class="mx-auto w-full max-w-xl">
        <div class="flex flex-col items-center py-6 dark:text-white">
            <div class="text-center">
                {{ $message }}
            </div>
            <a href="{{route('login')}}">
                Return to login
            </a>
        </div>
    </section>
</x-guest-layout>
