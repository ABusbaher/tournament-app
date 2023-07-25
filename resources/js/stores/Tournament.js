import {defineStore} from "pinia";

export const useTournamentStore = defineStore('tournament', {
    state: () => ({
        id: 0,
        types: [
            { value: 'league', label: 'League' },
            { value: 'elimination', label: 'Elimination (Cup)' },
            { value: 'group+elimination', label: 'Group+Elimination' }
        ],
        name: '',
        rounds: '',
        type: ''
    }),
    getters: {
        getId: (state) => state.id,
        getName: (state) => state.name,
        getRounds: (state) => state.rounds,
        getType: (state) => state.type,
    },
    actions: {
        async setIdFromUrl() {
            this.id = window.location.href.split('/')[4];
            try {
                const response = await axios.get(`/api/tournaments/${this.id}`);
                this.name = response.data.name;
                this.rounds = response.data.rounds;
                this.type = response.data.type;
            } catch (error) {
                console.log(error);
            }
        },
        async getByTournamentById(id) {
            try {
                const response = await axios.get(`/api/tournaments/${id}`);
                this.name = response.data.name;
                this.rounds = response.data.rounds;
                this.type = response.data.type;
            } catch (error) {
                console.log(error);
            }
        }
    },
})

