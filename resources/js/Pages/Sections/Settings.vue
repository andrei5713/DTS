<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <!-- Header at the top -->
        <Header />

        <!-- Sidebar + Main -->
        <div class="flex flex-1 p-6 ">
            <!-- Sidebar (left) -->
            <Sidebar :active-section="activeSection" @section-change="handleSectionChange" />

            <!-- Main Content (right) -->
            <main class="flex-1 ml-6 overflow-auto">

                <!-- Placeholder content -->
                <div class="bg-white p-6 rounded shadow">
                    <h1 class="text-2xl font-bold">Settings</h1>
                    <p>Manage your account and system preferences</p>

                    <div class="flex space-x-4 border-b mb-6">
                        <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
                            'px-4 py-2 text-sm font-medium transition-colors duration-200',
                            activeTab === tab.id ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-blue-600'
                        ]">
                            {{ tab.label }}
                        </button>
                    </div>

                    <div v-if="activeTab === 'profile'">
                        <div class="flex items-center space-x-3">
                            <User class="w-5 h-5" />
                            <h2 class="text-lg font-semibold">Profile Information</h2>
                        </div>
                        <p>Update your personal information and profile settings</p>
                        <!-- <div class="mt-4 mb-4 flex space-x-3">
                            <div class="w-20 h-20 bg-orange-500 rounded-full cursor-pointer"></div>
                            <div class="mt-2">
                                <button class="btn-primary">Change Photo</button>
                                <p class="mt-2">JPG, PNG or GIF. Max size 2MB.</p>
                            </div>
                        </div> -->
                        <form @submit.prevent="updateProfile" class="space-y-6 w-full mt-6">
                            <div class="flex justify-between items-center">
                                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                    <label class="block text-md font-bold mb-2">First Name</label>
                                    <input
                                        class="block w-full border rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                        v-model="form.firstname" type="text" placeholder="Enter your first name">
                                </div>
                                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                    <label class="block text-md font-bold mb-2">Last Name</label>
                                    <input
                                        class="block w-full border rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                        v-model="form.lastname" type="text" placeholder="Enter your last name">
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                    <label class="block text-md font-bold mb-2">Email</label>
                                    <input
                                        class="block w-full border rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                        v-model="form.email" type="text" placeholder="Enter your email">
                                </div>
                                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                    <label class="block text-md font-bold mb-2">Phone</label>
                                    <input
                                        class="block w-full border rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                        v-model="form.number" type="text" placeholder="Enter your phone number">
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                    <label class="block text-md font-bold mb-2">Department</label>
                                    <input
                                        class="block w-full border rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                        v-model="form.department" type="text" placeholder="Enter your department">
                                </div>
                                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                    <label class="block text-md font-bold mb-2">Position</label>
                                    <input
                                        class="block w-full border rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                        v-model="form.position" type="text" placeholder="Enter your position">
                                </div>
                            </div>

                            <div class="flex pl-4">
                                <button class="btn-primary" type="submit">
                                    <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z" />
                                    </svg>
                                    <span>Save Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div v-if="activeTab === 'notification'">
                        <div class="flex items-center space-x-3">
                            <Bell class="w-5 h-5" />
                            <h2 class="text-lg font-semibold">Notification</h2>
                        </div>
                        <p>Where you receive notifications</p>
                        <form @submit.prevent="updateNotif" class="space-y-6 w-full mt-6">

                            <NotificationToggle label="Email Notifications"
                                description="Receive notifications via email" v-model="notifications.email" />

                            <NotificationToggle label="SMS Notifications" description="Receive notifications via SMS"
                                v-model="notifications.sms" />

                            <NotificationToggle label="Push Notifications"
                                description="Receive browser push notifications" v-model="notifications.push" />

                            <NotificationToggle label="Document Update"
                                description="Notify when documents are updated or approved" />

                            <NotificationToggle label="System Alerts"
                                description="Notify about system maintenance and issues" />

                            <NotificationToggle label="Weekly Reports"
                                description="Receive weekly activity summaries" />

                            <div class="flex mt-6">
                                <button class="btn-primary" type="submit">
                                    <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z" />
                                    </svg>
                                    <span>Save Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div v-if="activeTab === 'security'">
                        <div class="flex items-center space-x-3">
                            <ShieldUser class="w-5 h-5" />
                            <h2 class="text-lg font-semibold">Change password</h2>
                        </div>
                        <p>Your password must be at least 6 characters and should include a combination of
                            numbers,
                            letters and special characters.</p>
                        <form @submit.prevent="changePassword" class="space-y-4 w-full mt-6">
                            <div>
                                <label class="block text-md font-bold mb-2">Current password</label>
                                <input v-model="passwordForm.current" type="password"
                                    class="w-full p-2 border rounded" />
                            </div>
                            <div>
                                <label class="block text-md font-bold mb-2">New password</label>
                                <input v-model="passwordForm.new" type="password" class="w-full p-2 border rounded" />
                            </div>
                            <div>
                                <label class="block text-md font-bold mb-2">Re-type new password</label>
                                <input v-model="passwordForm.new" type="password" class="w-full p-2 border rounded" />
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <button type="submit" class="btn-primary">
                                    Change Password
                                </button>
                                <a href="#" class="hyper-link">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';
import NotificationToggle from '@/Components/NotificationToggle.vue';
import { User, Bell, ShieldUser } from 'lucide-vue-next';

const activeSection = ref('settings');

function handleSectionChange(newSection) {
    activeSection.value = newSection;
}

const tabs = [
    { id: 'profile', label: 'Profile' },
    { id: 'notification', label: 'Notification' },
    { id: 'security', label: 'Security' }
]

const activeTab = ref('profile')

const form = ref({
    firstname: '',
    lastname: '',
    email: '',
    number: '',
    department: '',
    position: ''
})

const passwordForm = ref({
    current: '',
    new: ''
})

const notifications = ref({
    email: '',
    sms: '',
    push: ''

})

const updateProfile = () => {
    alert('Profile updated!')
}

const changePassword = () => {
    alert('Password changed!')
}

const updateNotif = () => {
    alert('Notification updated!')
}
</script>