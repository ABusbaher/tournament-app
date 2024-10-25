<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import {reactive, onMounted, ref, toRefs} from 'vue';
import { useVuelidate } from '@vuelidate/core'
import { required, minLength } from '@vuelidate/validators'
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    tournamentId: {
        type: String,
        required: true,
    },
    fixtureId: {
        type: String,
        required: true,
    },
});

const { fixtureId } = toRefs(props);
const { tournamentId } = toRefs(props);

const state = reactive({
    password: ''
});
const rulesFixtureLogin = () => ( {
    password: { required, minLength: minLength(3) },
});

const v$ = useVuelidate(rulesFixtureLogin(), state);
const emit = defineEmits(['passwordSubmitted']);
const errorMsg = ref('');
const showPassword = ref (false);
const toggleShow = () => {
    showPassword.value = !showPassword.value;
};

const submitFixturePassword = () => {
    v$.value.$touch();
    if (v$.value.$invalid) {
        return;
    }
    axios.post(`/api/tournaments/${tournamentId.value}/fixtures/${fixtureId.value}/check-password`, {
        password: state.password,
    }).then(response => {
            emit('passwordSubmitted');
        })
        .catch(error => {
            errorMsg.value = error.response.data.message;
            console.log(error.response.data);
        });
}

onMounted(async() => {
    errorMsg.value = '';
    state.password = '';
});
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <a href="/tournaments">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <h4>
                This page is password protected.
            </h4>
            <h4>
                Please provide valid password in order to check games.
            </h4>

            <div :class="['mt-6', { error: v$.password.$errors.length }]">
                <InputLabel for="password" value="Enter fixture password"/>
                <div class="relative">
                    <TextInput
                        id="password"
                        ref="nameInput"
                        v-model="state.password"
                        :type="showPassword ? 'text' : 'password'"
                        class="mt-1 block w-full"
                        placeholder=""
                    />
                    <button type="button" @click="toggleShow" class="absolute right-2 top-2">
                        <font-awesome-icon v-if="showPassword" :icon="['fas', 'eye-slash']" />
                        <font-awesome-icon v-else :icon="['fas', 'eye']" />
                    </button>
                </div>
                <div class="input-errors mt-2" v-for="error of v$.password.$errors" :key="error.$uid">
                    <InputError :message="error.$message" class="mt-2" />
                </div>
            </div>

            <div v-if="errorMsg" class="mt-2">
                <p class="text-sm text-red-600 dark:text-red-400">
                    {{ errorMsg }}
                </p>
            </div>

            <div class="mt-6 pb-10 flex justify-center">
                <PrimaryButton
                    class="ml-3"
                    @click="submitFixturePassword"
                >
                    Submit password
                </PrimaryButton>
            </div>

        </div>
    </div>
</template>
