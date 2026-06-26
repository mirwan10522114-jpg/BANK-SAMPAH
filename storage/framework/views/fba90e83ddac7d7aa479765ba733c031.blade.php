    <div {{ $attributes->class(["badge"])}}>
        <!-- ICON -->
        @if($icon)
            <x-mary-icon :name="$icon" class="h-4 w-4" />
        @endif

        <!-- VALUE / SLOT -->
        @if($value)
            {{ $value }}
        @else
            {{ $slot }}
        @endif

        <!-- ICON RIGHT -->
        @if($iconRight)
            <x-mary-icon :name="$iconRight" class="h-4 w-4" />
        @endif
    </div>