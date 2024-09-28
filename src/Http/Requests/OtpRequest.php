<?php

namespace BenBjurstrom\Plink\Http\Requests;

use BenBjurstrom\Plink\Actions\AttemptPlink;
use BenBjurstrom\Plink\Exceptions\PlinkAttemptsException;
use BenBjurstrom\Plink\Models\Concerns\Plinkable;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:9'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $code = preg_replace('/[^0-9A-Z]/', '', strtoupper($this->code));
        $this->merge([
            'code' => $code,
        ]);
    }

    /**
     * Send the OTP to the user.
     *
     * @throws ValidationException
     */
    public function authenticate(Plinkable $user): void
    {
        $this->ensureIsNotRateLimited();
        RateLimiter::hit($this->throttleKey(), 300);

        $code = $this->code;
        try {
            (new AttemptPlink)->handle($user, $code);
        } catch (PlinkAttemptsException $e) {
            throw ValidationException::withMessages([
                'code' => $e->getMessage(),
            ]);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::loginUsingId($user->getAuthIdentifier(), $this->remember);
        session()->regenerate();

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'code' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
