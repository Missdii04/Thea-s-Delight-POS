<x-mail::message>
# POS Account Verification

Hi **{{ $user->name }}**,

Thank you for joining our team!
 Please use the following One-Time PIN (OTP) to verify your Point of Sale account:

<x-mail::panel>
# **{{ $pin }}**
</x-mail::panel>

This PIN is valid for **5 minutes** and will expire for security.

Thanks,<br>
Thea's Delight POS 
administrator
</x-mail::message>