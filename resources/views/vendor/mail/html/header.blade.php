@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<h1 style="font-color: magenta; font-style:bold;">Thea's Delight POS</h1>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
