<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // $request->session()->forget('users');
        // Get users from the session
        $users = $request->session()->get('users', []);

        return Inertia::render(
            'Users/List',
            [
                'users' => $users,
                'success' => Session::get('success'),
                'error' => Session::get('error'),
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'string', 'in:Male,Female'],
            'address' => ['required', 'string', 'max:100'],
            'cover_image' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Max size is 2MB
        ]);

        // Upload cover image if provided
        $imageName = $request->file('cover_image')
            ? time() . '.' . $request->file('cover_image')->getClientOriginalExtension()
            : null;

        if ($imageName) {
            $request->cover_image->storeAs('public/uploads', $imageName);
        }

        // Add user details to the session
        $request->session()->push('users', array_merge($request->except('cover_image'), ['id' => count($request->session()->get('users', [])) + 1, 'cover_image' => $imageName]));

        // Flash success message and redirect
        Session::flash('success', 'User details added successfully.');
        return Redirect::route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $userId)
    {
        // Check if the 'users' session key exists
        if ($request->session()->has('users')) {
            // Get the users from the session
            $users = $request->session()->get('users');
            
            // Find the user with the specified $userId
            $user = collect($users)->firstWhere('id', $userId);

            // Check if the user was found
            if (!$user) {
                // If user not found, set an error flash message and redirect to 'users.index'
                Session::flash('error', 'Invalid user id provided.');
                return Redirect::route('users.index');
            }
        } else {
            // If 'users' session key does not exist, set an error flash message and redirect to 'users.index'
            Session::flash('error', 'Invalid user id provided.');
            return Redirect::route('users.index');
        }

        // Render the 'Users/Edit' Inertia view with the user data
        return Inertia::render(
            'Users/Edit',
            [
                'readOnly' => true,
                'user' => $user
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $userId)
    {
        // Check if the 'users' session key exists
        if ($request->session()->has('users')) {
            // Get the users from the session
            $users = $request->session()->get('users');
            
            // Find the user with the specified $userId
            $user = collect($users)->firstWhere('id', $userId);

            // Check if the user was found
            if (!$user) {
                // If user not found, set an error flash message and redirect to 'users.index'
                Session::flash('error', 'Invalid user id provided.');
                return Redirect::route('users.index');
            }
        } else {
            // If 'users' session key does not exist, set an error flash message and redirect to 'users.index'
            Session::flash('error', 'Invalid user id provided.');
            return Redirect::route('users.index');
        }

        // Render the 'Users/Edit' Inertia view with the user data
        return Inertia::render(
            'Users/Edit',
            [
                'user' => $user
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'string', 'in:Male,Female'],
            'address' => ['required', 'string', 'max:100'],
            // 'cover_image' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Max size is 2MB
        ]);

        // Get users from the session
        $users = $request->session()->get('users');

        // Find the index of the user with the specified $userId
        $userIndex = array_search($userId, array_column($users, 'id'));

        // Check if the user was found
        if ($userIndex !== false) {
            // Check if a new cover image is provided in the request
            if ($request->file('cover_image')) {
                $image = $request->file('cover_image');
                $imageName = time().'.'.$image->getClientOriginalExtension();

                // Move the uploaded file to a public directory
                $request->cover_image->storeAs('public/uploads', $imageName);
            } else {
                $imageName = null;
            }

            // Update user details in the session
            $users[$userIndex]['name'] = $request->name;
            $users[$userIndex]['address'] = $request->address;
            $users[$userIndex]['gender'] = $request->gender;
            // $users[$userIndex]['cover_image'] = $imageName;

            // Update the request data directly in the session
            $request->session()->put('users', $users);

            // Set a success flash message and redirect to 'users.index'
            Session::flash('success', 'User details updated successfully.');
            return Redirect::route('users.index');
        } else {
            // If user not found, set an error flash message and redirect to 'users.index'
            Session::flash('error', 'Invalid user id provided.');
            return Redirect::route('users.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $userId)
    {
        if ($request->session()->has('users')) {
            $users = $request->session()->get('users');
            $userIndex = array_search($userId, array_column($users, 'id'));
            // dd($userIndex);

            if ($userIndex !== false) {
                $fileToDelete = 'uploads/' . $users[$userIndex]['cover_image'];

                unset($users[$userIndex]);

                if (Storage::disk('public')->exists($fileToDelete)) {
                    Storage::disk('public')->delete($fileToDelete);
                }

                $users = array_values($users);

                // Update the request data directly in the session
                $request->session()->put('users', $users);

                return response()->json(['success' => 'User details removed successfully.', 'users' => $users], 200);
            } else {
                return response()->json(['error' => 'Invalid user id provided.', 'users' => $users], 200);
            }
        } else {
            return response()->json(['error' => 'Invalid user id provided.', 'users' => $users], 200);
        }
    }
}
