import {ref} from 'vue';
import {cloneDeep} from 'lodash';

export default function useStorage(key, defaultData, passThrough = false) {
    let activeData = ref();

    const set = () => {
        if (passThrough) {
            localStorage.setItem(key, JSON.stringify(defaultData));
            return;
        }

        localStorage.setItem(key, JSON.stringify(activeData.value));
    };

    const get = () => {
        return JSON.parse(localStorage.getItem(key));
    };

    const remove = () => {
        localStorage.removeItem(key);
    };

    const clear = () => {
        localStorage.clear();
    };

    if (!passThrough) {
        const storedData = get(key);
        activeData.value = storedData || cloneDeep(defaultData);
    }

    return {activeData, defaultData, get, set, remove, clear};
}
