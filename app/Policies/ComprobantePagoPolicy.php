<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ComprobantePago;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ComprobantePagoPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ComprobantePago');
    }

    public function view(AuthUser $authUser, ComprobantePago $comprobantePago): bool
    {
        return $authUser->can('View:ComprobantePago');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ComprobantePago');
    }

    public function update(AuthUser $authUser, ComprobantePago $comprobantePago): bool
    {
        return $authUser->can('Update:ComprobantePago');
    }

    public function delete(AuthUser $authUser, ComprobantePago $comprobantePago): bool
    {
        return $authUser->can('Delete:ComprobantePago');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ComprobantePago');
    }

    public function restore(AuthUser $authUser, ComprobantePago $comprobantePago): bool
    {
        return $authUser->can('Restore:ComprobantePago');
    }

    public function forceDelete(AuthUser $authUser, ComprobantePago $comprobantePago): bool
    {
        return $authUser->can('ForceDelete:ComprobantePago');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ComprobantePago');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ComprobantePago');
    }

    public function replicate(AuthUser $authUser, ComprobantePago $comprobantePago): bool
    {
        return $authUser->can('Replicate:ComprobantePago');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ComprobantePago');
    }
}
