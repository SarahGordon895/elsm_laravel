<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\DatabaseNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'gender',
        'date_of_birth',
        'address',
        'city',
        'country',
        'phone_number',
        'department_id',
        'role',
        'status',
        'manager_id',
        'join_date',
        'employment_type',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'join_date' => 'date',
        'status' => 'string',
        'role' => 'string',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function approvedLeaves()
    {
        return $this->hasMany(LeaveApplication::class, 'approved_by');
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function leavePlans()
    {
        return $this->hasMany(LeavePlan::class);
    }

    public function systemNotifications()
    {
        return $this->hasMany(SystemNotification::class);
    }

    public function unreadSystemNotifications()
    {
        return $this->systemNotifications()->unread();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        // Get permissions through roles
        return Permission::whereIn('id', function($query) {
            $query->select('permission_id')
                  ->from('permission_role')
                  ->whereIn('role_id', function($subQuery) {
                      $subQuery->select('role_id')
                              ->from('role_user')
                              ->where('user_id', $this->id);
                  });
        });
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return $this->roles->contains($role);
    }

    public function hasPermission($permission)
    {
        // Check direct permissions
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function giveRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        $this->roles()->syncWithoutDetaching($role);
    }

    public function revokeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        $this->roles()->detach($role);
    }

    public function syncRoles($roles)
    {
        if (is_array($roles)) {
            $roles = Role::whereIn('name', $roles)->get();
        }
        $this->roles()->sync($roles);
    }

    public function isAdmin()
    {
        return $this->hasRole('admin') || $this->hasRole('super_admin') || 
               $this->role === 'admin' || $this->role === 'super_admin';
    }

    public function isHR()
    {
        return $this->hasRole('hr') || $this->role === 'hr';
    }

    public function isManager()
    {
        return $this->hasRole('manager') || $this->subordinates()->count() > 0;
    }

    public function isEmployee()
    {
        return $this->hasRole('employee') || $this->role === 'employee';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeEmployees($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'employee');
        });
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        });
    }

    public function scopeManagers($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'manager');
        });
    }

    public function scopeHR($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'hr');
        });
    }

    public function canApproveLeave($leaveApplication)
    {
        // Admins can approve any leave
        if ($this->isAdmin()) {
            return true;
        }

        // HR can approve any leave
        if ($this->isHR()) {
            return true;
        }

        // Managers can approve their subordinates' leave
        if ($this->isManager()) {
            return $this->subordinates()->where('id', $leaveApplication->user_id)->exists();
        }

        return false;
    }

    public function getSubordinateLeaveApplications()
    {
        if ($this->isAdmin() || $this->isHR()) {
            return LeaveApplication::with(['user', 'leaveType']);
        }

        return LeaveApplication::whereIn('user_id', $this->subordinates()->pluck('id'))
                               ->with(['user', 'leaveType']);
    }

    public function getLeaveBalance($leaveTypeId, $year = null)
    {
        return LeaveBalance::getBalance($this->id, $leaveTypeId, $year);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function routeNotificationForMail($notification = null)
    {
        return $this->email;
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function markNotificationAsRead($notificationId)
    {
        $notification = $this->notifications()->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllNotificationsAsRead()
    {
        $this->unreadNotifications()->update(['read_at' => now()]);
    }
}
