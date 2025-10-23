<?php

namespace Softpyramid\ForgeStatus\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeploymentStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $siteName,
        public ?string $branch = null,
        public ?string $commit = null,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('forge-deployments');
    }

    public function broadcastAs(): string
    {
        return 'deployment.started';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => 'deploying',
            'site_name' => $this->siteName,
            'branch' => $this->branch,
            'commit' => $this->commit,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
