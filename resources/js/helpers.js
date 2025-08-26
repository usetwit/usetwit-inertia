import {DateTime} from 'luxon';

export const formatDate = (dateString, format, separator) => {
    if (!dateString) {
        return null;
    }

    let date = DateTime.fromFormat(dateString, format);

    if (date.isValid) {
        return date.toFormat(format.replace(/-/g, separator));
    }

    date = DateTime.fromISO(dateString);

    if (date.isValid) {
        return date.toFormat(format.replace(/-/g, separator));
    }

    date = DateTime.fromFormat(dateString, 'yyyy-MM-dd HH:mm:ss');

    if (date.isValid) {
        return date.toFormat(format.replace(/-/g, separator));
    }

    return 'Invalid DateTime';
};

export const applyFilterRegex = (string, global, self = []) => {
    if (Array.isArray(string) || typeof string === 'object' || typeof string === 'boolean') {
        return '';
    }

    if (Array.isArray(global) || typeof global === 'object' || typeof global === 'boolean') {
        global = '';
    }

    self = Array.from(self);
    string = String(string);
    global = String(global);

    const regexParts = [];

    if (global !== '') {
        regexParts.push(escapeRegex(global));
    }

    self.forEach(value => {
        if (Array.isArray(value) || typeof value === 'object' || typeof value === 'boolean') {
            return;
        }

        value = String(value);
        if (value !== '') {
            regexParts.push(escapeRegex(value));
        }
    });

    if (!regexParts.length) {
        return string;
    }

    regexParts.sort((a, b) => b.length - a.length);

    return string.replace(new RegExp(regexParts.join('|'), 'gi'), '<span class="regex-result">$&</span>');
};

const escapeRegex = (str) => str.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');

export function flagEmoji(countryCode) {
    if (typeof countryCode !== 'string') {
        return '';
    }

    return countryCode
        .toUpperCase()
        .split('')
        .map(char => String.fromCodePoint(127397 + char.charCodeAt(0)))
        .join('');
}
