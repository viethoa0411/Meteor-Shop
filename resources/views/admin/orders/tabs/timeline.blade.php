<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Lịch sử đơn hàng</h5>
    </div>
    <div class="card-body">
        <div class="timeline-vertical">
            @foreach($order->timelines ?? [] as $timeline)
                <div class="timeline-item d-flex mb-4">
                    <div class="timeline-marker flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px;">
                            <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                        </div>
                        @if(!$loop->last)
                            <div class="timeline-line bg-light" style="width: 2px; height: 60px; margin: 0 auto;"></div>
                        @endif
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $timeline->title }}</h6>
                                        @if($timeline->description)
                                            <p class="text-muted small mb-0">{{ $timeline->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        {{ $timeline->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                                @if($timeline->old_value && $timeline->new_value)
                                    <div class="small">
                                        <span class="badge bg-secondary">{{ $timeline->old_value }}</span>
                                        <i class="bi bi-arrow-right mx-2"></i>
                                        <span class="badge bg-primary">{{ $timeline->new_value }}</span>
                                    </div>
                                @endif
                                @if($timeline->user)
                                    <div class="small text-muted mt-2">
                                        <i class="bi bi-person"></i> {{ $timeline->user->name }}
                                        <span class="badge bg-light text-dark ms-2">{{ $timeline->user_type }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if(!$order->timelines || $order->timelines->count() === 0)
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clock-history" style="font-size: 3rem;"></i>
                <div class="mt-2">Chưa có lịch sử</div>
            </div>
        @endif
    </div>
</div>

