<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thea's Delight - POS System</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;700;800&display=swap');
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #FADDE6;
        }
        .bg-magenta { background-color: #faa0caff; }
        .text-magenta { color: #D54F8D; }
        .hover\:bg-pink-700:hover { background-color: #C3417E; }
        .text-header { color: #D54F8D; }
        
        /* Custom checkmark and bullet styles */
        .custom-bullet { color: #D54F8D; font-weight: bold; }
        .checkmark { color: #D54F8D; }
        
        /* Layout styles */
        .main-content-wrapper { flex-direction: column; }
        
        @media (min-width: 1024px) {
            .main-content-wrapper { flex-direction: row; }
            .logo-panel { width: 41.666667%; order: 2; }
            .content-panel { width: 58.333333%; order: 1; }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 sm:p-6 md:p-10">

    <div class="w-full max-w-5xl bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col lg:flex-row main-content-wrapper">
        
        <!-- LEFT PANEL: Content (Your text content) -->
        <div class="p-6 sm:p-8 md:p-12 content-panel flex flex-col justify-center">
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-header mb-2">Taste the Delight.</h1>
                <p class="text-base sm:text-lg text-gray-600 mb-8">Where Every Bite is a Celebration.</p>
                
                <!-- Features list with custom bullets -->
                <div class="w-full mt-4 pr-6">
                    <ul class="text-gray-700 text-lg space-y-4">
                        <p class="leading-relaxed">
                           — where every transaction leads to a moment of sweetness.               
                            We proudly serve handcrafted cakes made with love, using only the finest ingredients to bring joy in every bite.
                            From classic favorites to custom creations, each dessert is thoughtfully prepared to make your day a little more delightful. </p>
                        <p class="leading-relaxed">
                            At <span style="color:purple; font-style: italic; font-size: 20px ">Thea Delights,</span> every slice is a celebration.
                        </p>
                            </ul>
                </div>
                
                <!-- Divider line -->
                <div class="border-t border-gray-300 my-6"></div>
                
                <!-- Start Selling Button -->
                <div class="mt-6">
                    <a href="{{ route('login') }}" 
                       class="w-full block text-center py-3 px-8 bg-magenta text-white font-bold text-lg 
                              rounded-lg shadow-lg hover:bg-pink-700 transition duration-300">
                        Start Selling Sweet Treats
                    </a>
                </div>
            </div>
        </div>
        
        <!-- RIGHT PANEL: Logo (Magenta background) -->
        <div class="p-8 md:p-12 py-8 bg-magenta text-white flex flex-col justify-center items-center logo-panel">
            <img src="{{ asset('storage/logoPOS.png') }}" 
                 alt="Thea's Delight Bakery & Cake Logo" 
                 class="w-50 h-auto mx-auto object-contain mb-4">
            
            <h2 class="text-3xl sm:text-4xl font-extrabold mb-1 text-white text-center">Thea's Delight</h2>
            <p class="text-base sm:text-lg font-bold text-white/80 text-center">Bakery & Cake POS</p>
            
            <!-- Optional decorative elements -->
            <div class="mt-8 pt-4 border-t border-white/30 w-full max-w-xs text-center">
                <p class="text-white/90 text-sm italic">
                    "Every slice tells a story"
                </p>
            </div>
        </div>
        
    </div>
</body>
</html>