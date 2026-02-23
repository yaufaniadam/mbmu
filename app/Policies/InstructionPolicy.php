<?php

namespace App\Policies;

use App\Models\Instruction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InstructionPolicy
{
    /**
     * Determine whether the user can view any models.
     * All authenticated users can view instructions
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Admins can view all, others can view if targeted
     */
    public function view(User $user, Instruction $instruction): bool
    {
        // Admins can view all instructions
        if ($user->hasRole(['Superadmin', 'Ketua Kornas', 'Staf Kornas', 'Staf Akuntan Kornas'])) {
            return true;
        }

        // Check if instruction is targeted to this user
        $query = Instruction::query()->where('id', $instruction->id)->forUser($user);
        return $query->exists();
    }

    /**
     * Determine whether the user can create models.
     * Only specific admin roles can create instructions
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Superadmin', 'Ketua Kornas', 'Staf Kornas']);
    }

    /**
     * Determine whether the user can update the model.
     * Only creator or Superadmin can update
     */
    public function update(User $user, Instruction $instruction): bool
    {
        return $user->hasRole('Superadmin') || $instruction->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Only creator or Superadmin can delete
     */
    public function delete(User $user, Instruction $instruction): bool
    {
        return $user->hasRole('Superadmin') || $instruction->created_by === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Instruction $instruction): bool
    {
        return $user->hasRole('Superadmin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Instruction $instruction): bool
    {
        return $user->hasRole('Superadmin');
    }

    /**
     * Determine whether the user can acknowledge the instruction.
     * Non-admin users who are targeted can acknowledge
     */
    public function acknowledge(User $user, Instruction $instruction): bool
    {
        // Check if user hasn't already acknowledged
        if ($instruction->isAcknowledgedBy($user->id)) {
            return false;
        }

        // Check if instruction is targeted to this user
        $query = Instruction::query()->where('id', $instruction->id)->forUser($user);
        return $query->exists();
    }
}
