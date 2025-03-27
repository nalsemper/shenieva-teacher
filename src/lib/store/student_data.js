// src/lib/store/student_data.js
import { writable } from 'svelte/store';

/**
 * @typedef {Object} StudentData
 * @property {number} pk_studentID
 * @property {string} idNo
 * @property {string} studentName
 * @property {string} studentPass
 * @property {'Male' | 'Female'} studentGender
 * @property {number} studentLevel
 * @property {number | null} studentRibbon
 * @property {number | null} studentColtrash
 * @property {number | null} studentProgress
 */

/** @type {import('svelte/store').Writable<StudentData | null>} */
export const studentData = writable(null);