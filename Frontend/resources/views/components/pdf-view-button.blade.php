@props(['url', 'label' => 'View PDF', 'class' => 'text-primary hover:underline text-xs'])
<button type="button" {{ $attributes->except('class') }} class="{{ $class }}"
    @click="$dispatch('open-pdf-modal', { url: @js($url), label: @js($label) })">
    {{ $label }}
</button>
