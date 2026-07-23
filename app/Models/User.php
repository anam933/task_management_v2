<?php

namespace App\Models;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\DailyStandupReport;
use App\Models\MeetingMinute;
use App\Models\ProjectCategory;
use Illuminate\Support\Arr;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Department;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'category_id',
        'created_by',
        'reports_to',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function setRoleAttribute($value): void
    {
        $this->attributes['role'] = $this->normalizeRole($value);
    }

    public function getRoleLabelAttribute(): string
    {
        return ucfirst($this->normalizedRole());
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'reports_to');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'reports_to');
    }

    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    public function standupReports()
    {
        return $this->hasMany(DailyStandupReport::class);
    }

    public function scopeEmployees($query)
    {
        return $query->whereRaw('LOWER(role) = ?', ['employee']);
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        return $query->where('category_id', $user->category_id);
    }

    public function scopeCurrentCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->normalizedRole(), array_map([$this, 'normalizeRole'], Arr::wrap($roles)), true);
    }

    public function canAccess(string $permission, mixed $context = null): bool
    {
        $permissions = config('rbac.roles.' . $this->normalizedRole(), []);

        if (in_array('*', $permissions, true)) {
            return true;
        }

        if (! in_array($permission, $permissions, true)) {
            return false;
        }

        if ($permission === 'update-task-status' && $context instanceof Task) {
            return $this->hasRole(['admin', 'manager']) || (int) $context->assigned_to === (int) $this->id;
        }

        if ($permission === 'view-projects' && $context instanceof Project) {
            return $this->hasRole(['admin', 'manager'])
                || (int) $context->project_manager_id === (int) $this->id
                || (int) $context->created_by === (int) $this->id
                || $context->teamMembers()->whereKey($this->id)->exists();
        }

        if ($permission === 'view-tasks' && $context instanceof Task) {
            return $this->hasRole(['admin', 'manager'])
                || (int) $context->assigned_to === (int) $this->id
                || (int) $context->reports_to === (int) $this->id
                || $context->project?->teamMembers()->whereKey($this->id)->exists()
                || (int) $context->project?->project_manager_id === (int) $this->id
                || (int) $context->project?->created_by === (int) $this->id;
        }

        if (in_array($permission, ['view-standup-reports', 'manage-standup-reports'], true) && $context instanceof DailyStandupReport) {
            return $this->hasRole(['admin', 'manager']) || (int) $context->user_id === (int) $this->id;
        }

        if ($permission === 'view-meeting-minutes') {
            if ($this->hasRole('admin')) {
                return true;
            }
            if (!$context instanceof MeetingMinute) {
                return true;
            }
            if ($this->hasRole('manager')) {
                return (int) $context->created_by === (int) $this->id
                    || ($context->project && ((int) $context->project->project_manager_id === (int) $this->id || (int) $context->project->created_by === (int) $this->id));
            }
            if ($this->hasRole('employee')) {
                if (!in_array($context->status, ['Published', 'Completed'])) {
                    return false;
                }
                return $context->project && (
                    (int) $context->project->assigned_to === (int) $this->id
                    || $context->project->teamMembers()->whereKey($this->id)->exists()
                );
            }
            return false;
        }

        if ($permission === 'manage-meeting-minutes') {
            if ($this->hasRole('admin')) {
                return true;
            }
            if ($this->hasRole('manager')) {
                if ($context instanceof MeetingMinute) {
                    return (int) $context->created_by === (int) $this->id;
                }
                return true;
            }
            if ($this->hasRole('employee')) {
                if ($context instanceof MeetingMinute) {
                    return $context->actions()->where('assigned_to', $this->id)->exists();
                }
                return false;
            }
            return false;
        }

        return true;
    }

    public function normalizedRole(?string $role = null): string
    {
        return $this->normalizeRole($role ?? $this->attributes['role'] ?? 'employee');
    }

    private function normalizeRole(mixed $role): string
    {
        return strtolower(trim((string) $role)) ?: 'employee';
    }

    public function department()
        {
            return $this->belongsTo(Department::class);
        }
}
