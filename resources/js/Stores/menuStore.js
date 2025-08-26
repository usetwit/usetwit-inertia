import {defineStore} from 'pinia';

export const useMenuStore = defineStore('menu', {
    state: () => ({
        isMenuVisible: false,
        isLargeScreen: false,
    }),
});
