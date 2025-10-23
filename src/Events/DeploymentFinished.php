<?php

namespace Softpyramid\ForgeStatus\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeploymentFinished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $siteName,
        public string $status, // 'success' or 'failed'
        public ?string $branch = null,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('forge-deployments');
    }

    public function broadcastAs(): string
    {
        return 'deployment.finished';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => $this->status,
            'site_name' => $this->siteName,
            'branch' => $this->branch,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
