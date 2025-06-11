<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notification System Debug</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <h1>🔔 Notification System Debug</h1>
        <p><strong>Current User:</strong> {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>🧪 Test Notifications</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary mb-2" onclick="testMenuNotification()">
                            📅 Test Menu Update Notification
                        </button>
                        <br>
                        <button class="btn btn-secondary mb-2" onclick="testPollNotification()">
                            📊 Test Poll Created Notification
                        </button>
                        <br>
                        <button class="btn btn-success mb-2" onclick="testFeedbackNotification()">
                            💬 Test Feedback Notification
                        </button>
                        <br>
                        <button class="btn btn-info mb-2" onclick="loadFeatureStatus()">
                            🔄 Refresh Feature Status
                        </button>
                        <br>
                        <button class="btn btn-warning mb-2" onclick="clearAllDots()">
                            🗑️ Clear All Dots
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📊 Current Status</h5>
                    </div>
                    <div class="card-body">
                        <div id="statusOutput">
                            <p>Click "Refresh Feature Status" to see current notification counts</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>🔍 Debug Log</h5>
                    </div>
                    <div class="card-body">
                        <div id="debugLog" style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; font-family: monospace; font-size: 12px;">
                            <p>Debug information will appear here...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>🎯 Test Sidebar (Mini)</h5>
                    </div>
                    <div class="card-body">
                        <div class="sidebar-nav">
                            @if(auth()->user()->role === 'kitchen')
                                <a href="#" class="nav-link d-block mb-2" data-feature="kitchen.daily-menu">
                                    <i class="bi bi-journal-text"></i> Menu Planning
                                </a>
                                <a href="#" class="nav-link d-block mb-2" data-feature="kitchen.pre-orders">
                                    <i class="bi bi-calendar-check"></i> Pre-Orders
                                </a>
                                <a href="#" class="nav-link d-block mb-2" data-feature="kitchen.inventory">
                                    <i class="bi bi-box-seam"></i> Inventory
                                </a>
                                <a href="#" class="nav-link d-block mb-2" data-feature="kitchen.feedback">
                                    <i class="bi bi-star"></i> Feedback
                                </a>
                            @elseif(auth()->user()->role === 'student')
                                <a href="#" class="nav-link d-block mb-2" data-feature="student.menu">
                                    <i class="bi bi-journal-text"></i> Menu Planning
                                </a>
                                <a href="#" class="nav-link d-block mb-2" data-feature="student.pre-order">
                                    <i class="bi bi-calendar-check"></i> Pre-Orders
                                </a>
                                <a href="#" class="nav-link d-block mb-2" data-feature="student.feedback">
                                    <i class="bi bi-star"></i> Feedback
                                </a>
                            @elseif(auth()->user()->role === 'cook')
                                <a href="#" class="nav-link d-block mb-2" data-feature="cook.inventory">
                                    <i class="bi bi-box"></i> Inventory
                                </a>
                                <a href="#" class="nav-link d-block mb-2" data-feature="cook.feedback">
                                    <i class="bi bi-star"></i> Feedback
                                </a>
                                <a href="#" class="nav-link d-block mb-2" data-feature="cook.post-assessment">
                                    <i class="bi bi-clipboard-data"></i> Post-Assessment
                                </a>
                                <a href="#" class="nav-link d-block mb-2" data-feature="cook.pre-orders">
                                    <i class="bi bi-calendar-check"></i> Pre-Orders
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification system included in layout -->

    <script>
        function log(message, data = null) {
            const timestamp = new Date().toLocaleTimeString();
            const logElement = document.getElementById('debugLog');
            const logEntry = `[${timestamp}] ${message}`;
            
            if (data) {
                logElement.innerHTML += `<div style="color: #0066cc;">${logEntry}</div>`;
                logElement.innerHTML += `<div style="color: #666; margin-left: 20px;">${JSON.stringify(data, null, 2)}</div>`;
            } else {
                logElement.innerHTML += `<div>${logEntry}</div>`;
            }
            
            logElement.scrollTop = logElement.scrollHeight;
        }

        function testMenuNotification() {
            log('🧪 Testing menu notification...');
            
            fetch('/notifications/test', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    type: 'menu_update',
                    title: 'Test Menu Update',
                    message: 'This is a test menu update notification'
                })
            })
            .then(response => response.json())
            .then(data => {
                log('✅ Menu notification test response:', data);
                setTimeout(loadFeatureStatus, 1000);
            })
            .catch(error => {
                log('❌ Menu notification test error:', error);
            });
        }

        function testPollNotification() {
            log('🧪 Testing poll notification...');
            
            fetch('/notifications/test', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    type: 'poll_created',
                    title: 'Test Poll Created',
                    message: 'This is a test poll notification'
                })
            })
            .then(response => response.json())
            .then(data => {
                log('✅ Poll notification test response:', data);
                setTimeout(loadFeatureStatus, 1000);
            })
            .catch(error => {
                log('❌ Poll notification test error:', error);
            });
        }

        function testFeedbackNotification() {
            log('🧪 Testing feedback notification...');
            
            fetch('/notifications/test', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    type: 'feedback_submitted',
                    title: 'Test Feedback',
                    message: 'This is a test feedback notification'
                })
            })
            .then(response => response.json())
            .then(data => {
                log('✅ Feedback notification test response:', data);
                setTimeout(loadFeatureStatus, 1000);
            })
            .catch(error => {
                log('❌ Feedback notification test error:', error);
            });
        }

        function loadFeatureStatus() {
            log('🔄 Loading feature status...');
            
            fetch('/notifications/feature-status')
                .then(response => response.json())
                .then(data => {
                    log('📊 Feature status response:', data);
                    
                    document.getElementById('statusOutput').innerHTML = `
                        <h6>Feature Notification Counts:</h6>
                        <pre>${JSON.stringify(data.features, null, 2)}</pre>
                        <h6>New Notifications:</h6>
                        <pre>${JSON.stringify(data.new_notifications, null, 2)}</pre>
                    `;
                    
                    // Manually trigger the notification update
                    if (typeof updateFeatureNotifications === 'function') {
                        updateFeatureNotifications(data.features);
                    }
                })
                .catch(error => {
                    log('❌ Feature status error:', error);
                });
        }

        function clearAllDots() {
            log('🗑️ Clearing all notification dots...');
            document.querySelectorAll('.feature-notification-dot').forEach(dot => {
                dot.remove();
                log('🗑️ Removed dot');
            });
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            log('🚀 Debug page loaded');
            log('👤 Current user role: {{ auth()->user()->role }}');
            
            // Load initial status
            setTimeout(loadFeatureStatus, 1000);
        });
    </script>
</body>
</html>
