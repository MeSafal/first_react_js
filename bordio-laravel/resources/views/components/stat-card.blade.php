{{-- Reusable Stat Card Component --}}
{{-- Usage: @include('components.stat-card', ['icon' => 'alert-circle', 'color' => 'danger', 'value' => 5, 'label' => 'Overdue']) --}}

<div class="card border-0 shadow-sm h-100">
    <div class="card-body viso-stat-card">
        <div class="viso-stat-icon bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
            <i class="icon-{{ $icon }}" style="font-size:24px"></i>
        </div>
        <div>
            <div class="h4 fw-bold mb-0 text-dark">{{ $value }}</div>
            <div class="small fw-medium text-muted">{{ $label }}</div>
        </div>
    </div>
</div>
