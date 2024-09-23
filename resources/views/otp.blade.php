<x-guest-layout>
    <section class="mx-auto w-full max-w-xl py-6">
        <div class="text-center dark:text-zinc-100">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="hi-outline hi-lock-closed mb-5 inline-block size-6 opacity-75"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"
                />
            </svg>
            <form class="space-y-6" method="POST" action="{{ $url }}">
                @csrf
                <div>
                    <h2 id="otp-heading" class="mb-2 text-2xl font-bold">
                        One-Time Password
                    </h2>
                    <p
                        id="otp-description"
                        class="mb-8 text-sm text-zinc-600 dark:text-zinc-400"
                    >
                        Enter the 9-digit alpha numeric code sent to your email. The
                        code is case insensitive and dashes will be added
                        automatically.
                    </p>

                    <div class="flex justify-center">
                        <input
                            {{ $code ? 'readonly' : '' }}
                            {{ $code ? 'value="'. $code .'"' : '' }}
                            x-data="{}"
                            id="code"
                            type="text"
                            name="code"
                            required
                            autofocus
                            class="mt-1 block w-72 rounded-xl border-gray-300 p-4 text-center text-2xl uppercase shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            x-mask="***-***-***"
                            placeholder="XXX-XXX-XXX"
                            aria-labelledby="otp-heading"
                            aria-describedby="otp-description {{ $errors->has('form.code') ? 'otp-error' : '' }}"
                            aria-invalid="{{ $errors->has('form.code') ? 'true' : 'false' }}"
                            maxlength="11"
                        />
                        <input type="hidden" name="email" value="{{$email}}">
                    </div>
                    <x-input-error
                        :messages="$errors->get('code')"
                        class="mt-2"
                        id="otp-error"
                    />
                </div>

                <div class="mt-6">
                    <button
                        type="submit"
                        class="inline-flex min-w-32 items-center justify-center gap-2 rounded-lg border border-zinc-800 bg-zinc-800 px-3 py-2 text-sm font-medium leading-5 text-white hover:border-zinc-900 hover:bg-zinc-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500/50 active:border-zinc-700 active:bg-zinc-700 dark:border-zinc-700/50 dark:bg-zinc-700/50 dark:ring-zinc-700/50 dark:hover:border-zinc-700 dark:hover:bg-zinc-700/75 dark:active:border-zinc-700/50 dark:active:bg-zinc-700/50"
                        aria-label="Verify one-time password"
                    >
                        <span>Verify code</span>
                    </button>
                </div>

                <div aria-live="polite" class="sr-only">
                    @if ($errors->has('form.code'))
                        {{ implode(', ', $errors->get('form.code')) }}
                    @endif
                </div>
            </form>

            <div
                class="mt-5 text-sm text-zinc-500 dark:text-zinc-400"
                role="region"
                aria-label="Additional options"
            >
            <span id="resend-prompt">
                Sent to {{ $email }}. Haven't received it?
            </span>
                <a
                    href="{{ route('login') }}"
                    type="button"
                    class="font-medium text-teal-700 underline decoration-teal-500/50 underline-offset-2 hover:text-teal-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:text-teal-300 dark:decoration-teal-400/50 dark:hover:text-teal-100 dark:focus:ring-offset-zinc-800"
                    aria-describedby="resend-prompt"
                >
                    Request a new code
                </a>
            </div>
        </div>
        <!-- END Form -->
    </section>
</x-guest-layout>
