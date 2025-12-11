<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thea's Delight - Verify PIN</title>
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

<div class="w-full max-w-5xl bg-white rounded-xl shadow-2xl overflow-hidden flex flex-wrap lg:flex-nowrap">
    
    <div class="w-full lg:w-5/12 py-8 px-6 md:p-12 bg-magenta text-white flex flex-col justify-center items-center">
        <img src="{{ asset('storage/logoPOS.png') }}" 
             alt="Thea's Delight Bakery & Cake Logo" 
             class="w-40 h-auto mx-auto object-contain mb-4">
        
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-1 text-white text-center">Registration Verification</h2>
        <p class="text-base sm:text-lg font-bold text-white/80 text-center">Complete Account Setup</p>
    </div>

    <div class="w-full lg:w-7/12 p-6 sm:p-8 md:p-12 flex flex-col justify-center">
        
        <h1 class="text-3xl sm:text-4xl font-extrabold text-header mb-4 text-center lg:text-left">
            Verify PIN
        </h1>
        
        <div class="mb-6 text-sm text-gray-700 leading-relaxed text-center lg:text-left">
            <p>
                A **6-digit verification PIN** has been sent to your email address: 
                <span class="font-semibold text-magenta">{{ $email ?? 'Pending Email Address' }}</span>.
            </p>
            <p class="mt-2">
                Please enter the PIN below to complete your **registration and account activation**.
            </p>
        </div>

        <!-- Error Display -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-center" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ $errors->first('pin') ?? 'Invalid or expired PIN.' }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('register.pin.verify') }}">
            @csrf

            {{-- CRITICAL: NO hidden email field is used. User ID is secured in the session. --}}

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

            <div class="flex flex-col items-center mt-4 space-y-4">
                
                <button type="submit" 
                        class="w-full max-w-xs py-3 px-6 rounded-lg shadow-lg text-lg font-bold text-white bg-magenta hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-magenta transition duration-150">
                    Submit PIN & Activate
                </button>
                
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-magenta underline transition duration-150">
                    Cancel and Go to Login
                </a>
            </div>
        </form>
    </div>

</div>


</body>
</html>