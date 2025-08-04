<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Verifikacija Email-a" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Hvala na registraciji! Pre nego što počnete, možete li verifikovati svoju email adresu klikom na link
            koji smo vam upravo poslali? Ako niste primili email, rado ćemo vam poslati još jedan.
        </div>

        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400" v-if="verificationLinkSent">
            Novi verifikacioni link je poslat na email adresu koju ste naveli tokom registracije.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Ponovo Pošalji Verifikacioni Email
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    >Odjavi se</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
