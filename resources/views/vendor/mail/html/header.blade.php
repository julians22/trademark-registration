@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Kartini Djohan Trademark Registration')
<img src="{{ asset('img/logo-square.png') }}" class="logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
