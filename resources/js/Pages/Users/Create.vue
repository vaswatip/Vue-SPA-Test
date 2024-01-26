<script setup>
    import { ref } from 'vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
  
    const form = useForm({
        name: '',
        gender: '',
        address: '',
        cover_image: ''
    });

    const previewImage = ref(null);
    const handleImageUpload = (event) => {
        form.cover_image =  event.target.files[0];

        if (form.cover_image) {
            previewImage.value = URL.createObjectURL(form.cover_image);
        }
    }

    const submitForm = () => {
        form.post(route("users.store"));
    };
</script>

<template>
    <Head title="Create User" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center text-green-900 mb-5">
                    <h2 class="text-xl font-semibold">Create a user</h2>
                </div>
                <div class="relative overflow-x-auto">
                    <form @submit.prevent="submitForm">
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700" :required="true">
                                <span>Name</span>
                            </label>
                            <input
                                type="text"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                v-model="form.name"
                                autocomplete="off"
                            />
                            <div v-show="form.errors.name">
                                <p class="text-sm text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700" :required="true">
                                <span>Gender</span>
                            </label>
                            <div class="relative">
                                <select
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    v-model="form.gender"
                                >
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg
                                        class="h-5 w-5"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                    </svg>
                                </div>
                            </div>
                            <div v-show="form.errors.gender">
                                <p class="text-sm text-red-600">
                                    {{ form.errors.gender }}
                                </p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700" :required="true">
                                <span>Address</span>
                            </label>
                            <input
                                type="text"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                v-model="form.address"
                                autocomplete="off"
                            />
                            <div v-show="form.errors.address">
                                <p class="text-sm text-red-600">
                                    {{ form.errors.address }}
                                </p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <span>Cover Image</span>
                            </label>
                            <input type="file" accept="image/*" @change="handleImageUpload">
                            <div v-show="form.errors.cover_image">
                                <p class="text-sm text-red-600">
                                    {{ form.errors.cover_image }}
                                </p>
                            </div>
                        </div>
                        <div v-if="previewImage" class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">
                                <span>Image Preview</span>
                            </label>
                            <img :src="previewImage" alt="Image Preview" class="max-w-xs h-auto">
                        </div>
                        <div class="flex items-center gap-4">
                            <Link :href="route('users.index')" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Back
                            </Link>
                            <button
                                class="inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>