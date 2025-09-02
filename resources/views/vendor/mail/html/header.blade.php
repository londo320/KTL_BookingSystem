@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'KTL Booking System')
<div style="font-size: 42px; font-weight: 900; letter-spacing: 4px; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">KTL</div>
<div style="font-size: 18px; font-weight: 600; margin-top: 5px;">Booking System</div>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
