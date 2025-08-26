import globals from 'globals';
import pluginJs from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';

/** @type {import('eslint').Linter.FlatConfig[]} */
export default [
    pluginJs.configs.recommended, ...pluginVue.configs['flat/essential'],
    {
        files: ['**/*.{js,mjs,cjs,vue}'], languageOptions: {
            globals: globals.browser,
        },
        rules: {
            'comma-dangle': ['error', 'always-multiline'],
            semi: ['error', 'always'],
            quotes: ['error', 'single', {avoidEscape: true}],
            'vue/multi-word-component-names': 'off',
            'no-unused-vars': 'off',
            'vue/valid-attribute-name': 'off',
            'vue/no-dupe-keys': 'off',
        },
    }]
