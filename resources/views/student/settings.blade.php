@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Settings</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <h5>Profile Settings</h5>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" value="{{ Auth::user()->name }}">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h5>Notification Settings</h5>
                            <div class="form-group">
                                <label for="meal-notifications">Meal Time Notifications</label>
                                <select class="form-control" id="meal-notifications">
                                    <option value="1">Enabled</option>
                                    <option value="0">Disabled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="menu-updates">Menu Updates</label>
                                <select class="form-control" id="menu-updates">
                                    <option value="1">Enabled</option>
                                    <option value="0">Disabled</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
