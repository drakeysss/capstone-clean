@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h2>System Integration Dashboard</h2>
                    <p class="text-muted" style="color: white;">Monitor cross-system connectivity and data flow</p>
                </div>
                <div class="current-time">
                    <i class="bi bi-clock"></i>
                    <span id="currentDateTime"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Integration Status Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card main-card h-100">
                <div class="card-body text-center">
                    <div class="integration-icon mb-3">
                        <i class="bi bi-people-fill display-4 text-primary"></i>
                    </div>
                    <h5>Kitchen Team</h5>
                    <div id="kitchenStatus" class="status-indicator">
                        <span class="badge bg-secondary">Loading...</span>
                    </div>
                    <p class="text-muted mt-2" id="kitchenDetails">Checking connection...</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card main-card h-100">
                <div class="card-body text-center">
                    <div class="integration-icon mb-3">
                        <i class="bi bi-mortarboard-fill display-4 text-success"></i>
                    </div>
                    <h5>Students</h5>
                    <div id="studentStatus" class="status-indicator">
                        <span class="badge bg-secondary">Loading...</span>
                    </div>
                    <p class="text-muted mt-2" id="studentDetails">Checking connection...</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card main-card h-100">
                <div class="card-body text-center">
                    <div class="integration-icon mb-3">
                        <i class="bi bi-bar-chart-fill display-4 text-warning"></i>
                    </div>
                    <h5>Polling System</h5>
                    <div id="pollStatus" class="status-indicator">
                        <span class="badge bg-secondary">Loading...</span>
                    </div>
                    <p class="text-muted mt-2" id="pollDetails">Checking polls...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Data Flow -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Real-time Data Flow</h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshIntegrationData()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Recent Menu Updates</h6>
                            <div id="recentMenuUpdates" class="list-group">
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                    <span class="ms-2">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Active Polls</h6>
                            <div id="activePolls" class="list-group">
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                    <span class="ms-2">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health Metrics -->
    <div class="row">
        <div class="col-12">
            <div class="card main-card">
                <div class="card-header">
                    <h5 class="card-title">System Health Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="metric-card">
                                <h3 id="totalUsers" class="text-primary">-</h3>
                                <p class="text-muted">Total Connected Users</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="metric-card">
                                <h3 id="activePollsCount" class="text-success">-</h3>
                                <p class="text-muted">Active Polls</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="metric-card">
                                <h3 id="menuUpdatesCount" class="text-warning">-</h3>
                                <p class="text-muted">Recent Menu Updates</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="metric-card">
                                <h3 id="syncStatus" class="text-info">✓</h3>
                                <p class="text-muted">Real-time Sync</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadIntegrationData();
    
    // Auto-refresh every 30 seconds
    setInterval(loadIntegrationData, 30000);
    
    // Update current time
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
});

function loadIntegrationData() {
    fetch('/cook/cross-system-data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateIntegrationDisplay(data.data);
            } else {
                showError('Failed to load integration data');
            }
        })
        .catch(error => {
            console.error('Error loading integration data:', error);
            showError('Error loading integration data');
        });
}

function updateIntegrationDisplay(data) {
    // Update status indicators
    updateKitchenStatus(data.connected_users.kitchen_staff, data.kitchen_status);
    updateStudentStatus(data.connected_users.students);
    updatePollStatus(data.active_polls);
    
    // Update metrics
    document.getElementById('totalUsers').textContent = data.connected_users.total_users;
    document.getElementById('activePollsCount').textContent = data.active_polls.length;
    document.getElementById('menuUpdatesCount').textContent = data.recent_menu_updates.length;
    
    // Update recent menu updates
    updateRecentMenuUpdates(data.recent_menu_updates);
    
    // Update active polls
    updateActivePolls(data.active_polls);
}

function updateKitchenStatus(count, status) {
    const statusEl = document.getElementById('kitchenStatus');
    const detailsEl = document.getElementById('kitchenDetails');
    
    if (count > 0) {
        statusEl.innerHTML = '<span class="badge bg-success">Connected</span>';
        detailsEl.textContent = `${count} kitchen staff connected`;
    } else {
        statusEl.innerHTML = '<span class="badge bg-danger">Disconnected</span>';
        detailsEl.textContent = 'No kitchen staff connected';
    }
}

function updateStudentStatus(count) {
    const statusEl = document.getElementById('studentStatus');
    const detailsEl = document.getElementById('studentDetails');
    
    if (count > 0) {
        statusEl.innerHTML = '<span class="badge bg-success">Connected</span>';
        detailsEl.textContent = `${count} students registered`;
    } else {
        statusEl.innerHTML = '<span class="badge bg-warning">No Students</span>';
        detailsEl.textContent = 'No students registered';
    }
}

function updatePollStatus(polls) {
    const statusEl = document.getElementById('pollStatus');
    const detailsEl = document.getElementById('pollDetails');
    
    if (polls.length > 0) {
        statusEl.innerHTML = '<span class="badge bg-success">Active</span>';
        detailsEl.textContent = `${polls.length} active polls`;
    } else {
        statusEl.innerHTML = '<span class="badge bg-secondary">No Polls</span>';
        detailsEl.textContent = 'No active polls';
    }
}

function updateRecentMenuUpdates(updates) {
    const container = document.getElementById('recentMenuUpdates');
    
    if (updates.length === 0) {
        container.innerHTML = '<div class="text-muted text-center py-3">No recent updates</div>';
        return;
    }
    
    let html = '';
    updates.forEach(update => {
        html += `
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${update.name}</h6>
                    <small>${new Date(update.updated_at).toLocaleString()}</small>
                </div>
                <p class="mb-1">${update.day_of_week} - ${update.meal_type} (Week ${update.week_cycle})</p>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function updateActivePolls(polls) {
    const container = document.getElementById('activePolls');
    
    if (polls.length === 0) {
        container.innerHTML = '<div class="text-muted text-center py-3">No active polls</div>';
        return;
    }
    
    let html = '';
    polls.forEach(poll => {
        html += `
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${poll.meal_name}</h6>
                    <span class="badge bg-${poll.status === 'active' ? 'success' : 'info'}">${poll.status}</span>
                </div>
                <p class="mb-1">${poll.poll_date} - ${poll.meal_type}</p>
                <small>${poll.responses_count} responses</small>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function refreshIntegrationData() {
    loadIntegrationData();
    showToast('Integration data refreshed', 'success');
}

function updateCurrentTime() {
    const now = new Date();
    const dateOptions = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    const timeOptions = {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    };

    const dateString = now.toLocaleDateString('en-US', dateOptions);
    const timeString = now.toLocaleTimeString('en-US', timeOptions);

    const element = document.getElementById('currentDateTime');
    if (element) {
        element.innerHTML = `${dateString}<br><small>${timeString}</small>`;
    }
}

function showError(message) {
    console.error(message);
    // You can add toast notification here
}

function showToast(message, type) {
    // Simple toast implementation
    console.log(`${type.toUpperCase()}: ${message}`);
}
</script>

<style>
.integration-icon {
    opacity: 0.8;
}

.status-indicator {
    margin: 10px 0;
}

.metric-card {
    padding: 15px;
    border-radius: 8px;
    background: rgba(0,0,0,0.02);
}

.list-group-item {
    border: 1px solid rgba(0,0,0,0.125);
    margin-bottom: 5px;
    border-radius: 5px;
}
</style>
@endsection
