// import { writable } from "svelte/store";

// export const name = writable(""); // Global name store
// src/lib/store/nameStore.js

// src/lib/store/nameStore.js

// src/lib/store/nameStore.js
import { writable } from "svelte/store";
import { browser } from '$app/environment';

const storedName = browser ? localStorage.getItem('name') : null;
const initialName = storedName !== null ? storedName : "";

/** @type {import('svelte/store').Writable<string>} */
export const name = writable(initialName);

if (browser) {
  name.subscribe(value => {
    localStorage.setItem('name', value);
  });
}