<?php

namespace Softpyramid\ForgeStatus\View\Components;

use Illuminate\View\Component;

class DeploymentIndicator extends Component
{
    public function render()
    {
        if (config('forge-status.auth_only') && !auth()->check()) {
            return '';
        }
        
        return view('forge-status::components.deployment-indicator');
    }
}
