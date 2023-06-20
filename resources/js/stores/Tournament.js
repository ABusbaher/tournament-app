
import {defineStore} from "pinia";
import {computed, ref} from "vue";

export const useTournamentStore = defineStore('tournament', {
// export const useTournamentStore = defineStore('tournament',() => {
    // const id = ref('');
    // const getId = computed(() => id.value);
    // function setIdFromUrl() {
    //     id.value = window.location.href.split('/')[4];
    // }
    // return { id, getId, setIdFromUrl }
    state: () => ({
        id: 0,
    }),
        getters: {
        getId: (state) => state.id,
    },
    actions: {
        setIdFromUrl() {
            this.id = window.location.href.split('/')[4];
        },
    },
})

