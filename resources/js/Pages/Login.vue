<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import FormLayout from '@/Layouts/FormLayout.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <FormLayout>
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-4xl w-full">
            <div class="flex flex-col lg:flex-row">
                <!-- Left side - Login Form -->
                <div class="lg:w-1/2 p-8 lg:p-8">
                    <div class="max-w-sm mx-auto">
                        <!-- Logo/Avatar -->
                        <div class="flex justify-center mb-8">
                            <img src="@/Images/NFA.svg" class="w-16 h-16">
                        </div>

                        <!-- Welcome Text -->
                        <div class="text-center mb-8">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back!</h1>
                            <p class="text-gray-600 text-sm">Let's get you signed in securely.</p>
                        </div>

                        <!-- Login Form -->
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Username Field -->
                            <div>
                                <label for="username" class="input-label mb-2">
                                    Username
                                </label>
                                <input id="username" v-model="form.username" type="text"
                                    placeholder="Enter Your Username" class="input-field" required />
                            </div>

                            <!-- Password Field -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label for="password" class="input-label">
                                        Password
                                    </label>
                                    <a href="#" class="hyper-link">
                                        Forgot Your Password?
                                    </a>
                                </div>
                                <input id="password" v-model="form.password" type="password"
                                    placeholder="Enter Your Password" class="input-field" required />
                            </div>

                            <!-- Login Button -->
                            <button type="submit" class="btn-primary">
                                Log In
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right side - Image -->
                <div class="lg:w-1/2 relative">
                    <div class="relative h-64 lg:h-full flex items-center justify-center p-8">
                        <img src="@/Images/doc.png" class="w-full h-full">
                    </div>
                </div>
            </div>
        </div>
    </FormLayout>
</template>
