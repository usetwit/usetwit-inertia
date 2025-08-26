import axios from 'axios';
import {ref} from 'vue';
import {toast} from 'vue3-toastify';
import qs from 'qs';

export default function useAxios(url, params = {}, method = 'post') {
    method = method.toLowerCase();
    const data = ref(null);
    const errors = ref({
        fields: [],
        list: '',
        raw: null,
        message: '',
    });
    const status = ref(null);

    const getResponse = async () => {
        try {
            const config = {
                method,
                url,
                withCredentials: true,
                headers: {
                    'Accept': 'application/json',
                },
            };

            if (method === 'get') {
                config.params = params;
                config.paramsSerializer = (params) => qs.stringify(params, {encode: false});
            } else {
                config.data = params;
            }

            const response = await axios(config);

            data.value = response.data;
            status.value = response.status;
            errors.value = {
                fields: [],
                list: '',
                raw: null,
                message: '',
            };

        } catch (error) {
            if (error.response?.data?.errors) {
                const responseErrors = error.response.data.errors;
                errors.value.fields = Object.keys(responseErrors);
                errors.value.list = Object.values(responseErrors).flat().join('\n');
            }

            errors.value.raw = error;
            status.value = error.response?.status || 500;
            errors.value.message = error.message;

            toast.error(`<b>${errors.value.message}</b>\n${errors.value.list}`, {
                dangerouslyHTMLString: true,
            });
        }
    };

    return {data, status, errors, getResponse};
}
