import { Slot } from "./slot";

/**
 * Manages time slot generation, parsing, and filtering for the time picker.
 *
 * Handles interval-based slot generation, min/max constraints, unavailable time ranges,
 * special slot tagging, and locale-aware formatting.
 *
 * @class SlotsManager
 * @example
 * const manager = new SlotsManager({
 *   interval: 30,
 *   min: '09:00',
 *   max: '17:00',
 *   unavailable: '12:00-13:00',
 *   locale: 'en-US',
 *   timeFormat: '24-hour',
 *   special: { 'peak': '14:00-15:00' }
 * });
 * const slots = manager.generate();
 */
export class SlotsManager {
    /**
     * Creates a new SlotsManager instance.
     *
     * @param {Object} [options={}] - Configuration object
     * @param {number} [options.interval=30] - Minutes between each slot
     * @param {string|null} [options.min=null] - Minimum time as 'H:i' or 'now'. Defaults to 00:00
     * @param {string|null} [options.max=null] - Maximum time as 'H:i' or 'now'. Defaults to 23:59
     * @param {string|null} [options.unavailable=null] - Comma-separated unavailable times/ranges (e.g., '12:00,14:00-15:30')
     * @param {string} [options.locale='auto'] - BCP-47 locale for formatting
     * @param {string} [options.timeFormat='auto'] - Time format: 'auto', '12-hour', or '24-hour'
     * @param {Object} [options.special={}] - Map of custom slot tags. Format: { tagKey: 'H:i,H:i-H:i' }
     */
    constructor({ interval = 30, min = null, max = null, unavailable = null, locale = 'auto', timeFormat = 'auto', special = {} } = {}) {
        /** @type {number} - Minutes between each displayed slot */
        this.interval = interval;

        /** @type {string} - BCP-47 locale for time formatting */
        this.locale = locale;

        /** @type {string} - Time display format preference */
        this.timeFormat = timeFormat;

        const now = SlotsManager.getNowMinutes();

        /** @type {number} - Minimum selectable time in minutes since midnight */
        this.min = min === 'now' ? now : (SlotsManager.parse(min) ?? 0);

        /** @type {number} - Maximum selectable time in minutes since midnight */
        this.max = max === 'now' ? now : (SlotsManager.parse(max) ?? 1439);

        /** @type {Set<number>} - Set of unavailable time values (in minutes) */
        this.unavailable = this.from(unavailable);

        /** @type {Object<string, Set<number>>} - Map of special slot tags to minute sets */
        this.specialSets = Object.entries(special || {}).reduce((acc, [key, value]) => {
            acc[key] = this.from(value);
            return acc;
        }, {});
    }

    /**
     * Parse a time/range string into a Set of minute values.
     *
     * Supports comma-separated times and ranges (e.g., '13:00,17:00-19:00').
     * Ranges are expanded using this.interval and constrained to [this.min, this.max].
     *
     * @param {string|null} [str=null] - Comma-separated times and/or ranges
     * @returns {Set<number>} Set of minute values
     * @example
     * manager.from('09:00,14:00-15:30');  // { 540, 840, 870, 900, 930 }
     * manager.from(null);                 // Set {} (empty)
     */
    from(str = null) {
        const slots = new Set();
        if (str === null) return slots;

        str.split(',').forEach(segment => {
            segment = segment.trim();

            if (segment.includes('-')) {
                const dashIndex = segment.indexOf('-');
                const rawStart = segment.slice(0, dashIndex).trim();
                const rawEnd = segment.slice(dashIndex + 1).trim();
                
                const start = SlotsManager.parse(rawStart.trim());
                const end = SlotsManager.parse(rawEnd.trim());

                if (start !== null && end !== null) {
                    for (let t = start; t <= end; t += this.interval) {
                        if (t >= this.min && t <= this.max) slots.add(t);
                    }
                }
            } else {
                const t = SlotsManager.parse(segment);
                if (t !== null && t >= this.min && t <= this.max) slots.add(t);
            }
        });

        return slots;
    }

    /**
     * Generate the full list of all Slot instances for the configured range.
     *
     * Each slot includes disabled/unavailable status and special tags.
     *
     * @returns {Slot[]} Array of Slot instances
     */
    generate() {
        const slots = [];

        for (let t = this.min; t <= this.max; t += this.interval) {
            const slot = new Slot(t, this.locale, this.timeFormat);
            slot.disabled = this.unavailable.has(t);

            for (const [key, set] of Object.entries(this.specialSets)) {
                if (set.has(t)) slot.keys.push(key);
            }

            slots.push(slot);
        }

        return slots;
    }

    /**
     * Convert a raw time string to an array of Slot instances.
     *
     * Respects the configured interval when expanding ranges.
     *
     * @param {string} str - Comma-separated times/ranges (e.g., '09:00,14:00-15:30')
     * @returns {Slot[]} Array of Slot instances
     * @example
     * manager.slots('09:00,14:00-15:00');  // [Slot(540), Slot(840), Slot(870), ...]
     */
    slots(str) {
        return Array.from(this.from(str)).map(t => new Slot(t, this.locale, this.timeFormat));
    }

    /**
     * Convert a raw time string to formatted value strings.
     *
     * @param {string} str - Comma-separated times/ranges
     * @returns {string[]} Array of formatted time strings (e.g., ['09:00', '14:00', '14:30', ...])
     * @example
     * manager.values('09:00,14:00-14:30');  // ['09:00', '14:00', '14:30']
     */
    values(str) {
        return this.slots(str).map(slot => slot.value);
    }

    /**
     * Resolve the scroll-to target time when the dropdown opens.
     *
     * Priority order:
     * 1. Explicitly provided openTo parameter
     * 2. The first currently selected time
     * 3. The slot nearest to the current time (now)
     *
     * @param {string|null} openTo - Explicit open target as 'H:i' or null
     * @param {number|null|undefined} firstSelectedMins - First selected time in minutes
     * @param {Slot[]} slots - Array of available slots
     * @returns {number} Minutes since midnight to scroll to
     * @example
     * manager.resolveOpenTo('10:00', null, slots);  // 600 (10:00)
     * manager.resolveOpenTo(null, 540, slots);      // 540 (first selected)
     */
    resolveOpenTo(openTo, firstSelectedMins, slots) {
        if (!slots.length) return 0;

        if (openTo) {
            const m = SlotsManager.parse(openTo);
            if (m !== null) return m;
        }

        if (firstSelectedMins !== null && firstSelectedMins !== undefined) {
            return firstSelectedMins;
        }

        const nowMins = SlotsManager.getNowMinutes();

        const nearestCallback = (prev, curr) => Math.abs(curr.totalMinutes - nowMins) < Math.abs(prev.totalMinutes - nowMins) ? curr : prev;

        const nearest = slots.reduce(nearestCallback, slots[0]);

        return nearest?.totalMinutes ?? 0;
    }

    // ────────────────────────────────────
    // STATIC UTILITY METHODS
    // ────────────────────────────────────

    /**
     * Parse a time string to total minutes since midnight.
     *
     * Accepts 'H:i' format with optional leading zeros. Returns null for invalid input.
     *
     * @static
     * @param {string} str - Time string (e.g., '9:30', '09:30', '23:59')
     * @returns {number|null} Minutes since midnight (0–1439), or null if invalid
     * @example
     * SlotsManager.parse('09:30');  // 570
     * SlotsManager.parse('23:59');  // 1439
     * SlotsManager.parse('25:00');  // null (invalid)
     */
    static parse(str) {
        if (!str || typeof str !== 'string') return null;
        const parts = str.trim().split(':');
        const h = parseInt(parts[0], 10);
        const m = parseInt(parts[1] ?? '0', 10);
        if (isNaN(h) || isNaN(m) || h < 0 || h > 23 || m < 0 || m > 59) return null;
        return h * 60 + m;
    }

    /**
     * Format minutes into a locale-aware time string.
     *
     * Uses Intl.DateTimeFormat to respect locale and format preferences.
     * Omits minutes from the display when they are 0 (e.g., shows "2 PM" instead of "2:00 PM").
     *
     * @static
     * @param {number} totalMinutes - Minutes since midnight (0–1439)
     * @param {string} [locale='auto'] - BCP-47 locale or 'auto'
     * @param {string} [timeFormat='auto'] - Format preference: 'auto', '12-hour', or '24-hour'
     * @returns {string} Formatted time string respecting locale and format
     * @example
     * SlotsManager.format(570, 'en-US', '12-hour');  // "9:30 AM"
     * SlotsManager.format(570, 'en-US', '24-hour');  // "09:30"
     * SlotsManager.format(900, 'ja-JP', 'auto');     // "15:00"
     */
    static format(totalMinutes, locale = 'auto', timeFormat = 'auto') {
        const h = Math.floor(totalMinutes / 60) % 24;
        const m = totalMinutes % 60;
        const date = new Date(2000, 0, 1, h, m);
        const resolvedLocale = locale === 'auto' ? navigator.language : locale;

        return new Intl.DateTimeFormat(resolvedLocale, {
            hour: 'numeric',
            minute: m === 0 ? undefined : '2-digit',
            hour12: timeFormat === '12-hour' ? true : timeFormat === '24-hour' ? false : SlotsManager.localeUses12Hour(resolvedLocale),
        }).format(date);
    }

    /**
     * Get the current time in minutes since midnight.
     *
     * @static
     * @returns {number} Current time as minutes since midnight
     */
    static getNowMinutes() {
        return new Date().getHours() * 60 + new Date().getMinutes();
    }

    /**
     * Determine if a locale uses 12-hour or 24-hour time format.
     *
     * Uses Intl.DateTimeFormat to detect locale preference. Defaults to 12-hour
     * if detection fails.
     *
     * @static
     * @param {string} locale - BCP-47 locale code
     * @returns {boolean} True if locale uses 12-hour format, false for 24-hour
     * @example
     * SlotsManager.localeUses12Hour('en-US');  // true
     * SlotsManager.localeUses12Hour('de-DE');  // false
     */
    static localeUses12Hour(locale) {
        try {
            return new Intl.DateTimeFormat(locale, { hour: 'numeric' }).resolvedOptions().hour12 ?? true;
        } catch {
            return true;
        }
    }
}