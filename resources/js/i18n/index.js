import { ref } from 'vue';
import en from './en';
import si from './si';
import ta from './ta';

const messages = { en, si, ta };
const STORAGE_KEY = 'cp_lang';

export const LOCALES = [
    { code: 'si', label: 'සිංහල' },
    { code: 'ta', label: 'தமிழ்' },
    { code: 'en', label: 'English' },
];

const readSaved = () => {
    try {
        const value = localStorage.getItem(STORAGE_KEY);
        return value && Object.hasOwn(messages, value) ? value : null;
    } catch {
        return null; // storage blocked: the language picker simply shows again
    }
};

const saved = readSaved();
const locale = ref(saved ?? 'en');
const chosen = ref(saved !== null);

const applyDocumentLang = (code) => {
    if (typeof document !== 'undefined') {
        document.documentElement.lang = code;
    }
};

applyDocumentLang(locale.value);

export const setLocale = (code) => {
    if (!Object.hasOwn(messages, code)) return;

    locale.value = code;
    chosen.value = true;
    applyDocumentLang(code);

    try {
        localStorage.setItem(STORAGE_KEY, code);
    } catch {
        // storage blocked: the choice lasts until the page is reloaded
    }
};

export const useI18n = () => {
    // Reads locale.value, so any template that calls t() updates when the language changes.
    const t = (key) => messages[locale.value][key] ?? messages.en[key] ?? key;

    return { t, locale, chosen, setLocale, locales: LOCALES };
};
