<script setup>
    import { ref, defineProps, watch } from 'vue';
    import { Head, Link } from '@inertiajs/vue3';

    const props = defineProps({
        users: Object,
        success: String,
        error: String
    });

    const successMessage = ref(props.success);
    const errorMessage = ref(props.error);
    const sortCriteria = ref('ID-ASC'); // Default sorting criteria
    const sortedUsers = ref(props.users);

    // Watcher to sort the users when sortCriteria changes
    watch(() => sortCriteria.value, (newVal) => {
        sortUsers(newVal);
    });

    // Function to handle user sorting
    const sortUsers = (criteria) => {
        const usersCopy = [...props.users]; // Create a copy of the users array

        // Implement your sorting logic here based on criteria
        if (criteria === 'ID-ASC') {
            usersCopy.sort((a, b) => a.id - b.id);
        } else if (criteria === 'ID-DESC') {
            usersCopy.sort((a, b) => b.id - a.id);
        } else if (criteria === 'Name-ASC') {
            usersCopy.sort((a, b) => a.name.localeCompare(b.name));
        } else if (criteria === 'Name-DESC') {
            usersCopy.sort((a, b) => b.name.localeCompare(a.name));
        }

        sortedUsers.value = usersCopy;
    };

    const deleteItem = async (userId) => {
        if (window.confirm('Are you sure you want to delete this item?')) {
            try {
                const response = await fetch(route('users.destroy', userId), {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                if (response.ok) {
                    const responseData = await response.json();
                    sortedUsers.value = responseData.users;
                    successMessage.value = responseData.success;
                } else {
                    errorMessage.value = ('Failed to delete item: ' + response.statusText);
                }
            } catch (error) {
                errorMessage.value = ('Fetch error: ' +  error);
            }
        }
    };
</script>

<template>
    <Head title="Users" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Display success message -->
                <div v-if="successMessage" id="success" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div class="ms-3 text-sm font-medium">
                        {{ successMessage }}
                    </div>
                </div>
                
                <!-- Display error message -->
                <div v-if="errorMessage" id="error" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div class="ms-3 text-sm font-medium">
                        {{ errorMessage }}
                    </div>
                </div>

                <div class="p-6 flex justify-between items-center text-gray-900">
                    <h2 class="text-xl font-semibold">Users</h2>
                    <Link :href="route('users.create')" class="px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Create User
                    </Link>
                </div>
                
                <div v-show="sortedUsers.length" class="p-6 flex items-center text-gray-900">
                    <h2 class="text-xl font-semibold mr-4">Sort By</h2>
                    <select
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        v-model="sortCriteria"
                    >
                        <option value="ID-ASC">ID - ASC</option>
                        <option value="ID-DESC">ID - DESC</option>
                        <option value="Name-ASC">Name - ASC</option>
                        <option value="Name-DESC">Name - DESC</option>
                    </select>
                </div>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Image
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Address
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Gender
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="sortedUsers.length" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" v-for="(user, index) in sortedUsers" :key="user.id">
                                <td class="px-6 py-4">
                                    {{ (index + 1) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ user.name }}
                                </td>
                                <td class="px-6 py-4">
                                    <img class="h-10 w-10 rounded-full" :src="user.cover_image ? './storage/uploads/' + user.cover_image : null" alt="Image Alt Text">
                                </td>
                                <td class="px-6 py-4">
                                    {{ user.address }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ user.gender }}
                                </td>
                                <td class="px-6 py-4">
                                    <Link :href="route('users.show', [user.id])" class="px-4 py-2 font-semibold text-white bg-yellow-500 rounded hover:bg-yellow-600 focus:outline-none focus:ring focus:ring-yellow-300 mr-2">
                                        View
                                    </Link>
                                    <Link :href="route('users.edit', [user.id])" class="px-4 py-2 font-semibold text-white bg-blue-500 rounded hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-300 mr-2">
                                        Edit
                                    </Link>
                                    <button class="px-4 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600 focus:outline-none focus:ring focus:ring-red-300" @click="deleteItem(user.id)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-else class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4" colspan="6">
                                    No User Details Found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>