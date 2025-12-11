<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thea's Delight - Two-Factor Login</title>
<!-- Load Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;700;800&display=swap');
body {
font-family: 'Figtree', sans-serif;
background-color: #FADDE6;
}
.bg-magenta { background-color: #D54F8D; }
.text-magenta { color: #D54F8D; }
.hover:bg-pink-700:hover { background-color: #C3417E; }

    .input-soft-pink {
        background-color: #FFF0F5;
        border-color: #FFE6F0; 
        border-width: 2px;
    }
    .input-soft-pink:focus {
        background-color: white;
        border-color: #D54F8D;
    }
    .text-header { color: #D54F8D; }
    
    .pin-input {
        text-align: center;
        font-size: 1.5rem;
        letter-spacing: 0.5em;
        font-family: monospace;
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
    }
</style>


</head>
<body class="flex items-center justify-center min-h-screen p-4 sm:p-6 md:p-10">

<!-- Main Container: Responsive Split Layout -->
<div class="w-full max-w-5xl bg-white rounded-xl shadow-2xl overflow-hidden flex flex-wrap lg:flex-nowrap">
    
    <!-- TOP PANEL (Mobile) / LEFT PANEL (Desktop): Branding and Logo -->
    <div class="w-full lg:w-5/12 py-8 px-6 md:p-12 bg-magenta text-white flex flex-col justify-center items-center">
        <img src="{{ asset('storage/logoPOS.png') }}" 
             alt="Thea's Delight Bakery & Cake Logo" 
             style="height: 280px; width: 280px;" class=" mx-auto object-contain mb-4">
        
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-1 text-white text-center">Security Check</h2>
        <p class="text-base sm:text-lg font-bold text-white/80 text-center">Verify Your Identity</p>
    </div>

    <!-- BOTTOM PANEL (Mobile) / RIGHT PANEL (Desktop): OTP Form -->
    <div class="w-full lg:w-7/12 p-6 sm:p-8 md:p-12 flex flex-col justify-center">
        
        <h1 class="text-3xl sm:text-4xl font-extrabold text-header mb-4">
            Two-Factor Login
        </h1>
        
        <!-- Instructions/Information -->
        <div class="mb-6 text-sm text-gray-700 leading-relaxed">
            <p>
                A **6-digit verification PIN** has been sent to your email address: 
                <span class="font-semibold text-magenta">{{ $email }}</span>.
            </p>
            <p class="mt-2">
                Please enter the PIN below to complete your login.
            </p>
        </div>

        <!-- Error Display -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-center" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ $errors->first('pin') ?? 'Invalid PIN or session expired.' }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}">
            @csrf

            <!-- PIN Input Field -->
            <div class="mb-8">
                <label for="pin" class="block text-sm font-medium text-gray-700 mb-2 text-center">Verification PIN</label>
                <input id="pin" 
                        type="text" 
                        name="pin" 
                        value="{{ old('pin') }}"
                        required autofocus 
                        maxlength="6" 
                        placeholder="------"
                        class="input-soft-pink w-full px-4 py-3 rounded-lg focus:ring-2 focus:ring-magenta transition block pin-input">
            </div>

            <!-- Resend PIN & Verify Button -->
            <div class="flex items-center justify-between mt-4">
                <form method="POST" action="{{ route('otp.resend') }}" class="inline-block">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-magenta underline transition duration-150 p-0 m-0 border-none bg-transparent">
                        Resend PIN
                    </button>
                </form>

                <button type="submit" 
                    class="py-3 px-6 rounded-lg shadow-lg text-lg font-bold text-white bg-magenta hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-magenta transition duration-150">
                    Verify
                </button>
            </div>
                </form>
            
            <!-- Cancel / Logout Link -->
            <div class="mt-8 text-center">
                <div style="display: none;">
    <p>Raw route('login'): "{{ route('login') }}"</p>
    <p>Is empty? {{ empty(route('login')) ? 'YES' : 'NO' }}</p>
    <p>Route exists? {{ Route::has('login') ? 'YES' : 'NO' }}</p>
</div>

<!-- Your link -->
<div class="mt-8 text-center">
    <a href="#" 
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
       class="text-sm text-gray-500 hover:text-red-600 underline transition duration-150">
        Cancel and Go to Login
    </a>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
        </form>
</div>
                
                <!-- Hidden form for logout -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

        </form>
    </div>

</div>


</body>
</html>