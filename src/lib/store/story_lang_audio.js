import { writable } from 'svelte/store';

export const language = writable('english');
export const isFast = writable(true);