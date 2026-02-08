@use('Illuminate\Support\Str')
@use('Filament\Support\Facades\FilamentColor')

<style>
    .fi-audit-timeline .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .fi-audit-timeline .timeline-item:last-child {
        padding-bottom: 0;
    }

    .fi-audit-timeline .timeline-connector {
        position: absolute;
        left: 0.9375rem;
        top: 2.5rem;
        bottom: 0;
        width: 2px;
    }

    .fi-audit-timeline .fi-section-content-ctn,
    .fi-audit-timeline .fi-ta-content,
    .fi-audit-timeline .fi-ta-content-ctn,
    .fi-audit-timeline .fi-ta-content > div {
        overflow-y: visible !important;
        max-height: none !important;
    }

    .fi-audit-timeline .fi-section-content-ctn * {
        scrollbar-gutter: auto;
    }
</style>

<div class="fi-audit-timeline space-y-0">
    @forelse($audits as $audit)
        @php
            $old = $audit->old_values ?? [];
            $new = $audit->new_values ?? [];
            $keys = array_values(array_unique(array_merge(array_keys($old), array_keys($new))));

            $event = $audit->event;
            
            $color = match ($event) {
                'created' => 'success',
                'deleted' => 'danger',
                'restored' => 'warning',
                default => 'primary',
            };
            
            $icon = match ($event) {
                'created' => 'heroicon-o-plus-circle',
                'deleted' => 'heroicon-o-trash',
                'restored' => 'heroicon-o-arrow-path',
                default => 'heroicon-o-pencil-square',
            };
            
            $iconBgClass = match ($event) {
                'created' => 'bg-success-500 dark:bg-success-600',
                'deleted' => 'bg-danger-500 dark:bg-danger-600',
                'restored' => 'bg-warning-500 dark:bg-warning-600',
                default => 'bg-primary-500 dark:bg-primary-600',
            };
            
            $connectorClass = match ($event) {
                'created' => 'bg-success-500 dark:bg-success-600',
                'deleted' => 'bg-danger-500 dark:bg-danger-600',
                'restored' => 'bg-warning-500 dark:bg-warning-600',
                default => 'bg-primary-500 dark:bg-primary-600',
            };
        @endphp

        <div class="timeline-item">

            {{-- Connector Line --}}
            @if (!$loop->last)
                <div class="timeline-connector {{ $connectorClass }}"></div>
            @endif

            <div class="flex gap-4">

                {{-- MARK: Timeline Icon Dot --}}
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $iconBgClass }}">
                        <x-filament::icon :icon="$icon" class="w-4 h-4 text-white" />
                    </div>
                </div>

                {{-- MARK: Card Content --}}
                <div class="flex-1 min-w-0" x-data="{ expanded: true }">

                    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        
                        {{-- MARK: Header --}}
                        <div class="fi-section-header flex items-center justify-between gap-4 border-b border-gray-200 px-4 py-3 dark:border-white/10">
                            
                            <div class="flex items-center gap-3 min-w-0">
                                @if ($audit->user)
                                    {{-- User avatar --}}
                                    <div class="fi-avatar fi-avatar-sm overflow-hidden rounded-full">
                                        @if ($audit->user?->getFilamentAvatarUrl())
                                            <img src="{{ $audit->user->getFilamentAvatarUrl() }}"
                                                alt="{{ $audit->user->name }}" class="h-full w-full object-cover" />
                                        @else
                                            <div
                                                class="fi-avatar-fallback flex h-8 w-8 items-center justify-center text-sm font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                                {{ mb_substr($audit->user->name ?? 'U', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="leading-tight min-w-0">
                                        <div
                                            class="fi-section-header-heading text-sm font-semibold text-gray-950 dark:text-white truncate">
                                            {{ $audit->user->name }}
                                        </div>
                                        <div class="fi-section-description text-xs text-gray-500 dark:text-gray-400">
                                            {{ $audit->ip_address ?? 'IP Unknown' }}
                                        </div>
                                    </div>
                                @else
                                    {{-- System icon --}}
                                    <div class="fi-avatar fi-avatar-sm overflow-hidden rounded-full">
                                        <div
                                            class="fi-avatar-fallback flex h-8 w-8 items-center justify-center bg-gray-100 dark:bg-gray-800">
                                            <x-filament::icon icon="heroicon-m-server"
                                                class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                                        </div>
                                    </div>

                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                        System Bot
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                {{-- MARK: Badge --}}
                                <x-filament::badge :color="$color" size="xs">
                                    {{ Str::upper($audit->event) }}
                                </x-filament::badge>

                                <time datetime="{{ $audit->created_at->toIso8601String() }}"
                                    class="fi-badge-size-xs text-xs font-medium text-gray-500 dark:text-gray-400 tabular-nums">
                                    {{ $audit->created_at->format('M d, Y g:i A') }}
                                </time>

                                {{-- Collapse Toggle --}}
                                <button type="button" @click="expanded = !expanded"
                                    class="flex items-center justify-center w-6 h-6 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-300 dark:hover:bg-gray-800 transition">
                                    <x-filament::icon icon="heroicon-m-chevron-down"
                                        class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': expanded }" />
                                </button>
                            </div>

                        </div>

                        {{-- MARK: Inside Body --}}
                        <div x-show="expanded" x-collapse>
                            <div class="fi-section-content-ctn px-4 py-4">

                                @if (empty($keys))
                                    <div class="py-2 text-center">
                                        <p class="text-xs italic text-gray-400 dark:text-gray-500">
                                            No changes detected.
                                        </p>
                                    </div>

                                @else

                                    {{-- MARK: Table Structure --}}
                                    <div class="fi-ta-content">

                                        {{-- Header --}}
                                        <div
                                            class="fi-ta-header-ctn grid grid-cols-2 gap-6 border-b border-gray-200 pb-2 dark:border-white/10">
                                            <div
                                                class="fi-ta-header-cell text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Original
                                            </div>
                                            <div
                                                class="fi-ta-header-cell text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Changes
                                            </div>
                                        </div>

                                        {{-- Rows --}}
                                        <div class="fi-ta-content divide-y divide-gray-200 dark:divide-white/5">
                                            @foreach ($keys as $key)
                                                <div class="fi-ta-row grid grid-cols-2 gap-6 py-3 transition hover:bg-gray-50 dark:hover:bg-white/5">

                                                    {{-- Old value --}}
                                                    <div class="fi-ta-cell">
                                                        <div class="fi-ta-text-item-label mb-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                            {{ Str::headline($key) }}
                                                        </div>

                                                        @if (array_key_exists($key, $old))
                                                            <x-filament::badge color="gray" size="sm">
                                                                @if (is_array($old[$key]))
                                                                    {{ json_encode($old[$key]) }}
                                                                @elseif(is_null($old[$key]))
                                                                    <span class="italic opacity-60">null</span>
                                                                @elseif($old[$key] === '')
                                                                    <span class="italic opacity-60">empty</span>
                                                                @else
                                                                    {{ $old[$key] }}
                                                                @endif
                                                            </x-filament::badge>
                                                        @else
                                                            <span class="text-gray-300 dark:text-gray-700">—</span>
                                                        @endif
                                                    </div>

                                                    {{-- New value --}}
                                                    <div class="fi-ta-cell relative">
                                                        {{-- Arrow indicator - centered vertically --}}
                                                        <div class="absolute -left-5 top-1/2 -translate-y-1/2 text-gray-300 dark:text-gray-700">
                                                            <x-filament::icon icon="heroicon-m-arrow-right" class="h-4 w-4" />
                                                        </div>

                                                        <div class="fi-ta-text-item-label mb-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                            {{ Str::headline($key) }}
                                                        </div>

                                                        @if (array_key_exists($key, $new))
                                                            <x-filament::badge color="success" size="sm">
                                                                @if (is_array($new[$key]))
                                                                    {{ json_encode($new[$key]) }}
                                                                @elseif(is_null($new[$key]))
                                                                    <span class="italic opacity-60">null</span>
                                                                @elseif($new[$key] === '')
                                                                    <span class="italic opacity-60">empty</span>
                                                                @else
                                                                    {{ $new[$key] }}
                                                                @endif
                                                            </x-filament::badge>
                                                        @else
                                                            <x-filament::badge color="danger" size="sm">
                                                                <span>Deleted</span>
                                                            </x-filament::badge>
                                                        @endif
                                                    </div>

                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    @empty
        {{-- MARK: Empty state --}}
        <div class="fi-ta-empty-state-ctn py-12 px-6">

            <div class="fi-ta-empty-state mx-auto grid max-w-lg justify-items-center text-center">
                <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100/80 p-3 dark:bg-gray-800">
                    <x-filament::icon icon="heroicon-o-clock" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                </div>

                <h4 class="fi-ta-empty-state-heading text-base font-semibold text-gray-950 dark:text-white">
                    No audit history found
                </h4>

                <p class="fi-ta-empty-state-description mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Changes will appear here once you edit this record.
                </p>
            </div>

        </div>
    @endforelse
    
</div>
