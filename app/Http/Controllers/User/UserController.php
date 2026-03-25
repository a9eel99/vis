<?php

namespace App\Http\Controllers\User;

use App\Application\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index()
    {
        $users = $this->userService->list();
        $roles = Role::orderBy('name')->get();
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        try {
            $user = $this->userService->create($request->validated());

            // Sync individual permissions if provided
            if ($request->has('permissions')) {
                $user->syncPermissions($request->input('permissions', []));
            }

            return redirect()->route('users.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء المستخدم بنجاح.' : 'User created successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء إنشاء المستخدم.' : 'Error creating user.');
        }
    }

    public function edit(string $id)
    {
        $user = $this->userService->find($id);
        $roles = Role::orderBy('name')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, string $id)
    {
        try {
            $user = $this->userService->update($id, $request->validated());

            // Sync individual permissions if provided
            if ($request->has('permissions')) {
                $user->syncPermissions($request->input('permissions', []));
            }

            return redirect()->route('users.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم تحديث المستخدم بنجاح.' : 'User updated successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء تحديث المستخدم.' : 'Error updating user.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->userService->delete($id);

            return redirect()->route('users.index')
                ->with('success', app()->getLocale() === 'ar' ? 'تم حذف المستخدم بنجاح.' : 'User deleted successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'لا يمكن حذف مستخدم لديه فحوصات.' : 'Cannot delete user with inspections.');
        }
    }

    public function toggleActive(string $id)
    {
        try {
            $this->userService->toggleActive($id);

            return back()->with('success', app()->getLocale() === 'ar' ? 'تم تحديث حالة المستخدم.' : 'User status updated.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ.' : 'An error occurred.');
        }
    }
}