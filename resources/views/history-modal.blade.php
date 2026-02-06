@use('Illuminate\Support\Str')

<div class="audit-timeline">
    <style>
        .audit-timeline {
            --bg-card: white;
            --border-card: #e5e7eb;
            --shadow-card: 0 1px 2px rgba(0,0,0,0.05);
            --bg-header: rgba(249,250,251,0.5);
            --border-header: #f3f4f6;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --text-muted: #6b7280;
            --text-placeholder: #9ca3af;
            --text-empty: #d1d5db;
            --avatar-from: #f3f4f6;
            --avatar-to: #e5e7eb;
            --avatar-text: #374151;
            --avatar-border: white;
            --timeline: #e5e7eb;

            position: relative;
            margin-inline-start: 1rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .audit-timeline::before {
            content: '';
            position: absolute;
            inset-inline-start: -0.75rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: var(--timeline);
        }

        .dark .audit-timeline {
            --bg-card: #111827;
            --border-card: #374151;
            --shadow-card: 0 1px 3px rgba(0,0,0,0.3);
            --bg-header: rgba(31,41,55,0.5);
            --border-header: #374151;
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --text-muted: #9ca3af;
            --text-placeholder: #9ca3af;
            --text-empty: #4b5563;
            --avatar-from: #374151;
            --avatar-to: #1f2937;
            --avatar-text: #d1d5db;
            --avatar-border: #111827;
            --timeline: #374151;
        }

        .dark .audit-event-created {
            background-color: rgba(16,185,129,0.1) !important;
            color: #10b981 !important;
            border-color: rgba(16,185,129,0.3) !important;
        }

        .dark .audit-event-deleted {
            background-color: rgba(239,68,68,0.1) !important;
            color: #ef4444 !important;
            border-color: rgba(239,68,68,0.3) !important;
        }

        .dark .audit-event-updated {
            background-color: rgba(59,130,246,0.1) !important;
            color: #3b82f6 !important;
            border-color: rgba(59,130,246,0.3) !important;
        }

        .dark .audit-old-value {
            color: #f87171 !important;
            background-color: rgba(239,68,68,0.1) !important;
            border-color: rgba(239,68,68,0.3) !important;
        }

        .dark .audit-new-value {
            color: #34d399 !important;
            background-color: rgba(16,185,129,0.1) !important;
            border-color: rgba(16,185,129,0.3) !important;
        }

        .audit-arrow-icon {
            position: absolute;
            inset-inline-start: -1.25rem;
            top: 0.9rem;
            color: var(--text-placeholder);
            opacity: 0.8;
        }

        [dir="rtl"] .audit-arrow-icon svg {
            transform: scaleX(-1);
        }
    </style>

    @forelse($audits as $audit)
        <div style="position: relative;">
            <div style="position: absolute; inset-inline-start: -1.25rem; top: 0.75rem; width: 1.5rem; height: 1.5rem; border-radius: 9999px; border: 4px solid var(--avatar-border); display: flex; align-items: center; justify-content: center;
                background-color: {{ $audit->event === 'created' ? '#10b981' : ($audit->event === 'deleted' ? '#ef4444' : '#3b82f6') }};">
            </div>

            <div style="background-color: var(--bg-card); border-radius: 0.75rem; border: 1px solid var(--border-card); box-shadow: var(--shadow-card); overflow: hidden; transition: box-shadow 0.2s ease;">

                <div style="padding: 0.75rem 1rem; background-color: var(--bg-header); border-bottom: 1px solid var(--border-header); display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        @if($audit->user)
                            <div style="width: 2rem; height: 2rem; border-radius: 9999px; background: linear-gradient(to bottom right, var(--avatar-from), var(--avatar-to)); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold; color: var(--avatar-text); border: 2px solid var(--avatar-border);">
                                {{ substr($audit->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary); line-height: 1.1;">
                                    {{ $audit->user->name }}
                                </span>
                                <span style="font-size: 0.625rem; color: var(--text-muted); font-weight: 500;">
                                    {{ $audit->ip_address ?? 'IP Unknown' }}
                                </span>
                            </div>
                        @else
                            <div style="width: 2rem; height: 2rem; border-radius: 9999px; background-color: var(--avatar-from); display: flex; align-items: center; justify-content: center;">
                                <x-heroicon-m-server style="width: 1rem; height: 1rem; color: var(--text-muted);"/>
                            </div>
                            <span style="font-size: 0.875rem; font-weight: 500; color: var(--text-secondary);">System Bot</span>
                        @endif
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span class="audit-event-{{ $audit->event }}" style="padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.625rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid;
                            {{ $audit->event === 'created' ? 'background-color: #ecfdf5; color: #065f46; border-color: #d1fae5;' :
                              ($audit->event === 'deleted' ? 'background-color: #fef2f2; color: #991b1b; border-color: #fecaca;' :
                              'background-color: #eff6ff; color: #1e40af; border-color: #bfdbfe;') }}">
                            {{ $audit->event }}
                        </span>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-family: ui-monospace, monospace; font-weight: 500;">
                            {{ $audit->created_at->format('d M H:i') }}
                        </span>
                    </div>
                </div>

                @php
                    $old = $audit->old_values ?? [];
                    $new = $audit->new_values ?? [];
                    $keys = array_unique(array_merge(array_keys($old), array_keys($new)));
                @endphp

                <div style="padding: 1rem; font-size: 0.875rem; color: var(--text-primary);">
                    @if(empty($keys))
                        <div style="text-align: center; color: var(--text-placeholder); font-style: italic; font-size: 0.75rem; padding: 0.5rem 0;">
                            No changes detected explicitly.
                        </div>
                    @else
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-header);">
                            <h4 style="font-size: 0.75rem; font-weight: bold; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">Original</h4>
                            <h4 style="font-size: 0.75rem; font-weight: bold; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">Changes</h4>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            @foreach($keys as $key)
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding: 0.25rem 0.5rem; margin-inline: -0.5rem; border-radius: 0.375rem; transition: background-color 0.15s;">
                                    <div style="display: flex; flex-direction: column;">
                                        <span style="font-size: 0.625rem; text-transform: uppercase; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.125rem;">
                                            {{ Str::headline($key) }}
                                        </span>

                                        @if(array_key_exists($key, $old))
                                            <div class="audit-old-value" style="font-family: ui-monospace, monospace; font-size: 0.75rem; color: #dc2626; background-color: rgba(239,68,68,0.05); padding: 0.125rem 0.375rem; border-radius: 0.25rem; width: fit-content; word-break: break-all; border: 1px solid #fee2e2;">
                                                @if(is_array($old[$key]))
                                                    {{ json_encode($old[$key]) }}
                                                @elseif(is_null($old[$key]))
                                                    <span style="font-style: italic; color: var(--text-placeholder); opacity: 0.8;">null</span>
                                                @elseif($old[$key] === '')
                                                    <span style="font-style: italic; color: var(--text-placeholder); opacity: 0.8;">empty</span>
                                                @else
                                                    {{ $old[$key] }}
                                                @endif
                                            </div>
                                        @else
                                            <span style="color: var(--text-empty); font-size: 0.875rem;">—</span>
                                        @endif
                                    </div>

                                    <div style="position: relative; display: flex; flex-direction: column;">
                                        <div class="audit-arrow-icon">
                                            <x-heroicon-m-arrow-right style="width: 0.75rem; height: 0.75rem;"/>
                                        </div>

                                        <span style="font-size: 0.625rem; text-transform: uppercase; color: var(--text-secondary); font-weight: 600; margin-bottom: 0.125rem;">
                                            {{ Str::headline($key) }}
                                        </span>

                                        @if(array_key_exists($key, $new))
                                            <div class="audit-new-value" style="font-family: ui-monospace, monospace; font-size: 0.75rem; color: #059669; background-color: rgba(16,185,129,0.05); padding: 0.125rem 0.375rem; border-radius: 0.25rem; width: fit-content; word-break: break-all; border: 1px solid #d1fae5;">
                                                @if(is_array($new[$key]))
                                                    {{ json_encode($new[$key]) }}
                                                @elseif(is_null($new[$key]))
                                                    <span style="font-style: italic; color: var(--text-placeholder); opacity: 0.8;">null</span>
                                                @elseif($new[$key] === '')
                                                    <span style="font-style: italic; color: var(--text-placeholder); opacity: 0.8;">empty</span>
                                                @else
                                                    {{ $new[$key] }}
                                                @endif
                                            </div>
                                        @else
                                            <span style="color: var(--text-empty); font-size: 0.875rem;">—</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 0; text-align: center; color: var(--text-muted);">
            <div style="width: 3rem; height: 3rem; border-radius: 9999px; background-color: var(--avatar-from); display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                <x-heroicon-o-clock style="width: 1.5rem; height: 1.5rem; color: var(--text-placeholder);"/>
            </div>
            <p style="font-size: 0.875rem; font-weight: 500; color: var(--text-secondary);">No audit history found.</p>
            <p style="font-size: 0.75rem; color: var(--text-muted);">Changes will appear here once you edit this record.</p>
        </div>
    @endforelse
</div>
