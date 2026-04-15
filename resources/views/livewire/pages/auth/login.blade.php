<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-simple')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        if (auth()->user()->role === 'admin') {
            $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
        } else {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
    }
}; ?>

<div class="h-full grid grid-cols-1 lg:grid-cols-2 bg-white dark:bg-secondary-950 overflow-hidden">
    <!-- Left Side: Visuals -->
    <div class="hidden lg:flex flex-col justify-between relative bg-secondary-900 border-r border-secondary-800/50 overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150 mix-blend-overlay z-10 pointer-events-none"></div>
        
        <!-- Animated Blobs -->
        <div class="absolute -top-24 -left-20 w-96 h-96 bg-primary-600/30 rounded-full blur-3xl animate-blob mix-blend-screen"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-3xl animate-blob animation-delay-2000 mix-blend-screen"></div>
        <div class="absolute -bottom-32 -right-20 w-80 h-80 bg-purple-600/30 rounded-full blur-3xl animate-blob animation-delay-4000 mix-blend-screen"></div>
        
        <!-- Content -->
        <div class="relative z-20 px-12 pt-12">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold font-outfit text-white tracking-tight uppercase italic">SMM <span class="text-primary-500">PRO</span></span>
            </div>
        </div>

        <div class="relative z-20 px-12 py-auto flex flex-col justify-center h-full">
            <h1 class="text-5xl font-black text-white leading-tight tracking-tight mb-6 drop-shadow-sm">
                Elevate Your <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-purple-400">Social Presence</span>
            </h1>
            <p class="text-secondary-400 text-lg max-w-md leading-relaxed">
                Join thousands of influencers and businesses using our advanced SMM tools to grow exponentially.
            </p>
            
            <div class="flex items-center gap-2 mt-8">
                 <div class="flex -space-x-3">
                    <img class="w-10 h-10 rounded-full border-2 border-secondary-900" src="https://i.pravatar.cc/100?img=1" alt="User" />
                    <img class="w-10 h-10 rounded-full border-2 border-secondary-900" src="https://i.pravatar.cc/100?img=5" alt="User" />
                    <img class="w-10 h-10 rounded-full border-2 border-secondary-900" src="https://i.pravatar.cc/100?img=8" alt="User" />
                     <div class="w-10 h-10 rounded-full border-2 border-secondary-900 bg-secondary-800 flex items-center justify-center text-xs font-bold text-white">
                        +2k
                    </div>
                </div>
                <div class="ml-2">
                    <div class="flex text-yellow-500">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                         <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-xs text-secondary-400 font-medium mt-1">TrustPilot & Google Reviews</p>
                </div>
            </div>
        </div>

        <div class="relative z-20 px-12 pb-12">
            <div class="flex gap-4 text-xs font-semibold text-secondary-400 uppercase tracking-wider">
                <a href="#" class="hover:text-white transition-colors">Privacy</a>
                <a href="#" class="hover:text-white transition-colors">Terms</a>
                <a href="#" class="hover:text-white transition-colors">Help</a>
            </div>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="flex flex-col justify-center items-center p-4 sm:p-6 lg:px-10 xl:px-12 relative min-h-full bg-white dark:bg-secondary-950 overflow-y-auto" x-data="{ showPassword: false }">
        <div class="w-full max-w-[340px] space-y-2 py-4">
            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-2">
                 <div class="w-8 h-8 mx-auto bg-primary-600 rounded-lg flex items-center justify-center shadow-lg shadow-primary-500/30">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>

            <div class="text-center lg:text-left mb-2">
                <h2 class="text-xl font-bold tracking-tight text-secondary-900 dark:text-white leading-tight">Welcome back</h2>
                <p class="text-[10px] text-secondary-500 dark:text-secondary-400 font-medium">
                    Please enter your details to sign in.
                </p>
            </div>

            <x-auth-session-status class="mb-2" :status="session('status')" />

            <form wire:submit="login" class="space-y-2">
                <!-- Email -->
                <div class="space-y-0.5">
                    <label for="email" class="text-[9px] font-bold uppercase tracking-wider text-secondary-500 dark:text-secondary-400 ml-1">
                        Email or Username
                    </label>
                    <div class="relative group">
                         <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-secondary-400 group-focus-within:text-primary-500 transition-colors duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" /></svg>
                        </div>
                        <input wire:model="form.email" id="email" type="text" required autofocus 
                            class="flex h-8 w-full rounded-lg border-none bg-secondary-50 px-2.5 py-1.5 pl-8 text-xs font-medium ring-1 ring-inset ring-secondary-200 focus:ring-2 focus:ring-primary-500 focus:bg-white placeholder:text-secondary-400 dark:bg-secondary-900 dark:ring-secondary-800 dark:text-white dark:focus:bg-secondary-900 dark:focus:ring-primary-500 transition-all duration-200 shadow-sm" 
                            placeholder="Enter your email or username"
                        >
                    </div>
                    <x-input-error :messages="$errors->get('form.email')" class="mt-0.5 text-[10px] text-red-600 ml-1" />
                </div>

                <!-- Password -->
                <div class="space-y-0.5">
                    <div class="flex items-center justify-between ml-1 mb-0.5">
                        <label for="password" class="text-[9px] font-bold uppercase tracking-wider text-secondary-500 dark:text-secondary-400">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[9px] font-bold text-primary-600 hover:text-primary-500 dark:text-primary-400 underline decoration-transparent hover:decoration-primary-500 transition-all" wire:navigate>
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-secondary-400 group-focus-within:text-primary-500 transition-colors duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <input wire:model="form.password" id="password" :type="showPassword ? 'text' : 'password'" required 
                             class="flex h-8 w-full rounded-lg border-none bg-secondary-50 px-2.5 py-1.5 pl-8 pr-7 text-xs font-medium ring-1 ring-inset ring-secondary-200 focus:ring-2 focus:ring-primary-500 focus:bg-white placeholder:text-secondary-400 dark:bg-secondary-900 dark:ring-secondary-800 dark:text-white dark:focus:bg-secondary-900 dark:focus:ring-primary-500 transition-all duration-200 shadow-sm"
                            placeholder="••••••••"
                        >
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-1.5 flex items-center text-secondary-400 hover:text-primary-600 transition-colors cursor-pointer">
                             <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!showPassword"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="showPassword" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L5.136 5.136m13.728 13.728L13.875 18.825M13.875 18.825l-4.242-4.242" /></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('form.password')" class="mt-0.5 text-[10px] text-red-600 ml-1" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center ml-1 py-1">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="h-3 w-3 rounded border-secondary-300 text-primary-600 focus:ring-primary-600 dark:border-secondary-700 dark:bg-secondary-800 dark:ring-offset-secondary-950 transition-colors cursor-pointer">
                    <label for="remember" class="ml-2 block text-[10px] font-bold uppercase tracking-tight text-secondary-500 dark:text-secondary-400 select-none cursor-pointer">Remember me</label>
                </div>

                <x-button type="submit" variant="primary" class="w-full h-9 text-xs font-bold uppercase tracking-wider shadow shadow-primary-600/30 active:scale-[0.98] transition-all rounded-lg mt-1">
                    Sign In
                </x-button>

                <div class="relative py-3">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-secondary-100 dark:border-secondary-800"></div>
                    </div>
                    <div class="relative flex justify-center text-[10px]">
                        <span class="px-2 bg-white dark:bg-secondary-950 text-secondary-400 uppercase tracking-tight font-bold">New around here?</span>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center w-full h-9 rounded-lg border border-secondary-100 dark:border-secondary-800 text-[10px] font-black tracking-widest uppercase text-secondary-600 dark:text-secondary-400 hover:border-primary-500 hover:text-primary-600 transition-all duration-200" wire:navigate>
                        Create Account
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
         <div class="absolute bottom-4 w-full text-center text-[9px] font-bold text-secondary-400 dark:text-secondary-800 uppercase tracking-widest opacity-60">
            &copy; {{ date('Y') }} SMM PRO. Global Social Solutions.
        </div>
    </div>
</div>
