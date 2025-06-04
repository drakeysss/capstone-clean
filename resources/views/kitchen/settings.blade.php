@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kitchen Settings</h1>
    </div>

    <div class="row">
        <!-- Profile Settings -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Settings</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="John Doe">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="john.doe@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" value="+63 912 345 6789">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" class="form-control" value="Kitchen Manager" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>

            <!-- Password Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="col-xl-6 col-lg-6">
            <!-- Notification Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notification Settings</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Email Notifications</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label">
                                    Low Stock Alerts
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label">
                                    Order Updates
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label">
                                    Menu Changes
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">System Notifications</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label">
                                    Desktop Notifications
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label">
                                    Sound Alerts
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Notification Settings</button>
                    </form>
                </div>
            </div>

            <!-- Inventory Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Inventory Settings</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Low Stock Threshold (%)</label>
                            <input type="number" class="form-control" value="20">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Default Unit of Measurement</label>
                            <select class="form-select">
                                <option>Kilograms (kg)</option>
                                <option>Grams (g)</option>
                                <option>Liters (L)</option>
                                <option>Milliliters (ml)</option>
                                <option>Pieces (pcs)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Auto-order When Low Stock</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                                <label class="form-check-label">Enable automatic ordering</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Inventory Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
