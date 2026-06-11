import { SlotsManager } from "./slot-manager";

/**
 * Represents a single time slot in the time picker.
 *
 * Encapsulates time data in minutes since midnight and provides both
 * canonical (24-hour) and locale-aware formatted representations.
 *
 * @class Slot
 * @example
 * const slot = new Slot(570, 'en-US', 'auto'); // 9:30 AM
 * console.log(slot.value);  // "09:30"
 * console.log(slot.label);  // "9:30 AM"
 */
export class Slot {
    /**
     * Creates a new Slot instance.
     *
     * @param {number} totalMinutes - Minutes since midnight (0–1439). Required.
     * @param {string} [locale='auto'] - BCP-47 locale code or 'auto' to use navigator.language
     * @param {string} [timeFormat='auto'] - Display format. Options: 'auto', '12-hour', '24-hour'
     */
    constructor(totalMinutes, locale = 'auto', timeFormat = 'auto') {
        /** @type {number} - Total minutes since midnight */
        this.totalMinutes = totalMinutes;

        /** @type {number} - Hour component (0–23) */
        this.hours = Math.floor(totalMinutes / 60) % 24;

        /** @type {number} - Minute component (0–59) */
        this.minutes = totalMinutes % 60;

        /** @type {string} - BCP-47 locale for formatting */
        this.locale = locale;

        /** @type {string} - Time format preference */
        this.timeFormat = timeFormat;

        /** @type {boolean} - Whether this slot is disabled/unavailable */
        this.disabled = false;

        /** @type {string[]} - Custom tags/keys marking special slot properties */
        this.keys = [];
    }

    /**
     * Canonical 24-hour value string for backend storage and wire:model.
     *
     * @type {string}
     * @example
     * new Slot(570).value  // "09:30"
     * new Slot(0).value    // "00:00"
     */
    get value() {
        return `${String(this.hours).padStart(2, '0')}:${String(this.minutes).padStart(2, '0')}`;
    }

    /**
     * Human-readable label respecting locale and time format settings.
     * Used for display in the UI (e.g., "9:30 AM", "09:30", "午前9:30").
     *
     * @type {string}
     * @example
     * new Slot(570, 'en-US', '12-hour').label  // "9:30 AM"
     * new Slot(570, 'en-US', '24-hour').label  // "09:30"
     */
    get label() {
        return SlotsManager.format(this.totalMinutes, this.locale, this.timeFormat);
    }
}