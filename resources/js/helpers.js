import {DateTime} from 'luxon';

export const formatDate = (input, fmt, sep = '-') => {
    if (!input) return null;

    const target = fmt.replace(/-/g, sep);
    const attempts = [
        () => DateTime.fromFormat(input, fmt),
        () => DateTime.fromISO(input),
        () => DateTime.fromSQL(input),
        () => DateTime.fromFormat(input, 'yyyy-MM-dd HH:mm:ss'),
    ];

    for (const parse of attempts) {
        const dt = parse();
        if (dt.isValid) return dt.toFormat(target);
    }

    return null;
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

    string = htmlspecialchars(string);
    const regexParts = [];

    if (global !== '') {
        regexParts.push(escapeRegex(htmlspecialchars(global)));
    }

    self.forEach(value => {
        if (Array.isArray(value) || typeof value === 'object' || typeof value === 'boolean') {
            return;
        }

        value = String(value);

        if (value !== '') {
            regexParts.push(escapeRegex(htmlspecialchars(value)));
        }
    });

    if (!regexParts.length) {
        return string;
    }

    regexParts.sort((a, b) => b.length - a.length);

    return string.replace(new RegExp(regexParts.join('|'), 'gi'), '<span class="regex-result">$&</span>');
};

function htmlspecialchars(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

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
