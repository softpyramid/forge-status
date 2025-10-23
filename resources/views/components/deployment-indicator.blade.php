<div id="forge-deployment-indicator" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden transition-all duration-300">
    
    <div class="bg-white rounded-lg shadow-xl border border-gray-200 px-8 py-6 max-w-md w-full mx-4">
        <div class="text-center">
            <!-- Deploying Spinner -->
            <div id="forge-status-deploying" class="hidden mb-4">
                <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <!-- Success Icon -->
            <div id="forge-status-success" class="hidden mb-4">
                <svg class="h-12 w-12 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            
            <!-- Failed Icon -->
            <div id="forge-status-failed" class="hidden mb-4">
                <svg class="h-12 w-12 text-red-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            
            <h3 id="forge-status-text" class="text-lg font-semibold text-gray-900 mb-2">Checking status...</h3>
            <p id="forge-status-detail" class="text-sm text-gray-600 mb-4"></p>
            
            <button onclick="document.getElementById('forge-deployment-indicator').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="h-5 w-5 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    const indicator = document.getElementById('forge-deployment-indicator');
    const statusText = document.getElementById('forge-status-text');
    const statusDetail = document.getElementById('forge-status-detail');
    const deployingIcon = document.getElementById('forge-status-deploying');
    const successIcon = document.getElementById('forge-status-success');
    const failedIcon = document.getElementById('forge-status-failed');
    
    let lastStatus = null;
    let pollInterval = {{ config('forge-status.poll_interval', 5) }} * 1000; // Default 5 seconds
    
    function hideAllIcons() {
        deployingIcon.classList.add('hidden');
        successIcon.classList.add('hidden');
        failedIcon.classList.add('hidden');
    }
    
    function showDeploymentStatus(data) {
        hideAllIcons();
        indicator.classList.remove('hidden');
        
        if (data.status === 'deploying') {
            deployingIcon.classList.remove('hidden');
            statusText.textContent = 'Deploying...';
            statusDetail.textContent = data.branch ? `Branch: ${data.branch}` : data.site_name;
        } else if (data.status === 'success') {
            successIcon.classList.remove('hidden');
            statusText.textContent = 'Deployment Complete!';
            statusDetail.textContent = 'Site is now live';
            
            // Hide after 5 seconds
            setTimeout(() => {
                indicator.classList.add('hidden');
            }, 5000);
        } else if (data.status === 'failed') {
            failedIcon.classList.remove('hidden');
            statusText.textContent = 'Deployment Failed';
            statusDetail.textContent = 'Check Forge logs for details';
            
            // Hide after 10 seconds
            setTimeout(() => {
                indicator.classList.add('hidden');
            }, 10000);
        }
    }
    
    function checkDeploymentStatus() {
        fetch('{{ route('forge-status.check') }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Only update if status changed
            if (data.status !== lastStatus) {
                lastStatus = data.status;
                
                if (data.status === 'deploying') {
                    showDeploymentStatus(data);
                } else if (data.status === 'success' || data.status === 'failed') {
                    showDeploymentStatus(data);
                } else if (data.status === 'idle' && lastStatus === 'deploying') {
                    // Deployment finished, hide indicator
                    indicator.classList.add('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Forge status check failed:', error);
        });
    }
    
    // Check initial status on page load
    checkDeploymentStatus();
    
    // Poll for status updates
    setInterval(checkDeploymentStatus, pollInterval);
})();
</script>
